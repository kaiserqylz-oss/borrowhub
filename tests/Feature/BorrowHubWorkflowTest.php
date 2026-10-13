<?php

use App\Models\BorrowRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function borrowHubItem(User $owner, array $attributes = []): Item
{
    $category = Category::create(['name' => fake()->unique()->word(), 'status' => 'active']);

    return Item::create(array_merge([
        'owner_id' => $owner->id,
        'category_id' => $category->id,
        'name' => 'Cordless drill',
        'description' => 'A useful tool.',
        'condition' => 'good',
        'location' => 'Manila',
        'price_per_day' => 100,
        'status' => 'available',
    ], $attributes));
}

function borrowHubRequest(Item $item, User $borrower, array $attributes = []): BorrowRequest
{
    return BorrowRequest::create(array_merge([
        'item_id' => $item->id,
        'borrower_id' => $borrower->id,
        'start_date' => Carbon::tomorrow()->toDateString(),
        'end_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
        'duration_days' => 3,
        'total_amount' => 300,
        'status' => 'pending',
    ], $attributes));
}

it('matches the create item page instead of treating create as an item id', function () {
    $this->actingAs(User::factory()->create())
        ->get('/items/create')
        ->assertOk();
});

it('shows dashboard items from other owners and excludes inactive or actively borrowed listings', function () {
    $viewer = User::factory()->create();
    $viewer->forceFill(['email_verified_at' => now()])->save();
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $available = borrowHubItem($owner, ['name' => 'Available lantern']);
    borrowHubItem($viewer, ['name' => 'My own listing']);
    borrowHubItem($other, ['name' => 'Inactive listing', 'status' => 'inactive']);
    $active = borrowHubItem($other, ['name' => 'Currently borrowed']);
    $activeRequest = borrowHubRequest($active, User::factory()->create(), ['status' => 'approved']);
    Transaction::create([
        'borrow_request_id' => $activeRequest->id,
        'expected_return_date' => $activeRequest->end_date,
        'return_status' => 'not_returned', 'status' => 'active',
    ]);

    $this->actingAs($viewer)->get(route('items.show', $active))
        ->assertOk()->assertSee('Currently borrowed')->assertDontSee('Request to borrow');

    $this->actingAs($viewer)->get(route('dashboard'))->assertOk()->assertViewHas('items', function ($items) use ($available) {
        return $items->contains('id', $available->id)
            && ! $items->contains('name', 'My own listing')
            && ! $items->contains('name', 'Inactive listing')
            && ! $items->contains('name', 'Currently borrowed');
    });
});

it('requires authentication for item management and borrowing requests', function () {
    $this->get('/items/create')->assertRedirect(route('login'));

    $owner = User::factory()->create();
    $item = borrowHubItem($owner);
    $this->get(route('borrow-requests.create', $item))->assertRedirect(route('login'));
});

it('uses BorrowHub branding on public and authentication pages', function () {
    $this->get(route('home'))->assertOk()->assertSee('BorrowHub')->assertDontSee('Laravel');
    $this->get(route('login'))->assertOk()->assertSee('BorrowHub')->assertDontSee('Laravel');
    $this->get(route('register'))->assertOk()->assertSee('BorrowHub')->assertDontSee('Laravel');
});

it('ships a valid PWA manifest and matching public PNG icons without caching user pages', function () {
    $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true, flags: JSON_THROW_ON_ERROR);

    expect($manifest['name'])->toBe('BorrowHub')
        ->and($manifest['short_name'])->toBe('BorrowHub')
        ->and($manifest['start_url'])->toBe('/dashboard')
        ->and($manifest['scope'])->toBe('/')
        ->and($manifest['display'])->toBe('standalone');

    foreach ($manifest['icons'] as $icon) {
        $path = public_path(ltrim($icon['src'], '/'));
        expect(file_exists($path))->toBeTrue();
        [$width, $height, $type] = getimagesize($path);
        expect($type)->toBe(IMAGETYPE_PNG)
            ->and($width.'x'.$height)->toBe($icon['sizes']);
    }

    $worker = file_get_contents(public_path('service-worker.js'));
    expect($worker)->toContain("if (request.method !== 'GET') return;")
        ->and($worker)->toContain("request.mode === 'navigate'")
        ->and($worker)->not->toContain("'/storage'");
});

it('creates items for the signed-in owner and ignores submitted ownership', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();
    $category = Category::create(['name' => 'Tools', 'status' => 'active']);

    $this->actingAs($owner)->post(route('items.store'), [
        'owner_id' => $attacker->id,
        'category_id' => $category->id,
        'name' => 'Drill',
        'description' => 'Cordless drill',
        'condition' => 'good',
        'location' => 'Manila',
        'price_per_day' => 0,
    ])->assertRedirect(route('items.index'));

    $this->assertDatabaseHas('items', [
        'owner_id' => $owner->id,
        'name' => 'Drill',
        'price_per_day' => 0,
    ]);
});

it('rejects invalid borrowing dates and calculates the amount on the server', function () {
    $owner = User::factory()->create();
    $borrower = User::factory()->create();
    $item = borrowHubItem($owner, ['price_per_day' => 125.50]);

    $this->actingAs($borrower)->post(route('borrow-requests.store', $item), [
        'start_date' => Carbon::yesterday()->toDateString(),
        'end_date' => Carbon::tomorrow()->toDateString(),
        'total_amount' => 0,
    ])->assertSessionHasErrors('start_date');

    $start = Carbon::tomorrow();
    $this->post(route('borrow-requests.store', $item), [
        'start_date' => $start->toDateString(),
        'end_date' => $start->copy()->addDays(2)->toDateString(),
        'total_amount' => 0,
        'duration_days' => 1,
    ])->assertRedirect(route('items.show', $item));

    $this->assertDatabaseHas('borrow_requests', [
        'borrower_id' => $borrower->id,
        'item_id' => $item->id,
        'duration_days' => 3,
        'total_amount' => '376.50',
    ]);
});

it('allows only the owner to approve and creates one consistent transaction', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $borrower = User::factory()->create();
    $item = borrowHubItem($owner);
    $borrowRequest = borrowHubRequest($item, $borrower);

    $this->actingAs($other)
        ->patch(route('owner.borrow-requests.approve', $borrowRequest))
        ->assertForbidden();

    $this->actingAs($owner)
        ->patch(route('owner.borrow-requests.approve', $borrowRequest))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('borrow_requests', ['id' => $borrowRequest->id, 'status' => 'approved']);
    $this->assertDatabaseHas('transactions', [
        'borrow_request_id' => $borrowRequest->id,
        'expected_return_date' => $borrowRequest->end_date->toDateString().' 00:00:00',
        'return_status' => 'not_returned',
        'status' => 'active',
    ]);
    expect(Transaction::where('borrow_request_id', $borrowRequest->id)->count())->toBe(1);
});

it('prevents approval of an overlapping approved reservation', function () {
    $owner = User::factory()->create();
    $borrower = User::factory()->create();
    $item = borrowHubItem($owner);
    $start = Carbon::tomorrow();
    $approved = borrowHubRequest($item, $borrower, [
        'status' => 'approved',
        'start_date' => $start->toDateString(),
        'end_date' => $start->copy()->addDays(2)->toDateString(),
    ]);
    $pending = borrowHubRequest($item, User::factory()->create(), [
        'start_date' => $start->copy()->addDay()->toDateString(),
        'end_date' => $start->copy()->addDays(3)->toDateString(),
    ]);

    $this->actingAs($owner)
        ->patch(route('owner.borrow-requests.approve', $pending))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('borrow_requests', ['id' => $approved->id, 'status' => 'approved']);
    $this->assertDatabaseHas('borrow_requests', ['id' => $pending->id, 'status' => 'pending']);
    $this->assertDatabaseMissing('transactions', ['borrow_request_id' => $pending->id]);
});

it('lets only the owner mark an approved transaction returned', function () {
    $owner = User::factory()->create();
    $borrower = User::factory()->create();
    $item = borrowHubItem($owner);
    $borrowRequest = borrowHubRequest($item, $borrower, ['status' => 'approved']);
    $transaction = Transaction::create([
        'borrow_request_id' => $borrowRequest->id,
        'expected_return_date' => $borrowRequest->end_date,
        'return_status' => 'not_returned',
        'status' => 'active',
    ]);

    $this->actingAs($borrower)
        ->patch(route('owner.borrow-requests.return', $borrowRequest))
        ->assertForbidden();

    $this->actingAs($owner)
        ->patch(route('owner.borrow-requests.return', $borrowRequest))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'return_status' => 'returned',
        'status' => 'completed',
    ]);
    expect($transaction->fresh()->returned_at)->not->toBeNull();
});
it('allows a borrower to rate an owner once after a returned transaction', function () {
    $owner = User::factory()->create();
    $borrower = User::factory()->create();
    $item = borrowHubItem($owner);
    $borrowRequest = borrowHubRequest($item, $borrower, ['status' => 'approved']);
    $transaction = Transaction::create([
        'borrow_request_id' => $borrowRequest->id,
        'expected_return_date' => $borrowRequest->end_date,
        'returned_at' => now(),
        'return_status' => 'returned',
        'status' => 'completed',
    ]);

    $this->actingAs($borrower)->post(route('transactions.rating.store', $transaction), [
        'rating' => 5,
        'comment' => 'Great owner.',
    ])->assertSessionHas('success');

    $this->assertDatabaseHas('ratings', [
        'transaction_id' => $transaction->id,
        'reviewer_id' => $borrower->id,
        'reviewed_user_id' => $owner->id,
        'rating' => 5,
    ]);

    $this->post(route('transactions.rating.store', $transaction), ['rating' => 4])
        ->assertSessionHas('error');
    expect($transaction->ratings()->count())->toBe(1);
});

it('lets a user report another owner item without allowing self reports', function () {
    $owner = User::factory()->create();
    $reporter = User::factory()->create();
    $item = borrowHubItem($owner);

    $this->actingAs($owner)
        ->post(route('items.report.store', $item), ['reason' => 'Test', 'description' => 'Report'])
        ->assertForbidden();

    $this->actingAs($reporter)
        ->post(route('items.report.store', $item), [
            'reason' => 'Safety concern',
            'description' => 'The listing has a safety issue.',
        ])->assertRedirect(route('items.show', $item));

    $this->assertDatabaseHas('reports', [
        'reporter_id' => $reporter->id,
        'reported_user_id' => $owner->id,
        'item_id' => $item->id,
        'status' => 'open',
    ]);
});
it('allows user and participant transaction reports but rejects unrelated transaction reports', function () {
    $reporter = User::factory()->create();
    $reportedUser = User::factory()->create();
    $owner = User::factory()->create();
    $item = borrowHubItem($owner);
    $request = borrowHubRequest($item, $reporter, ['status' => 'approved']);
    $transaction = Transaction::create([
        'borrow_request_id' => $request->id,
        'expected_return_date' => $request->end_date,
        'return_status' => 'not_returned', 'status' => 'active',
    ]);

    $this->actingAs($reporter)->post(route('reports.store'), [
        'target_type' => 'user', 'target_id' => $reportedUser->id,
        'reason' => 'Harassment or abuse', 'description' => 'This member sent an abusive message.',
    ])->assertRedirect(route('reports.index'));
    $this->post(route('reports.store'), [
        'target_type' => 'transaction', 'target_id' => $transaction->id,
        'reason' => 'Damaged or missing item', 'description' => 'The returned item was damaged.',
    ])->assertRedirect(route('reports.index'));

    $unrelated = User::factory()->create();
    $this->actingAs($unrelated)->post(route('reports.store'), [
        'target_type' => 'transaction', 'target_id' => $transaction->id,
        'reason' => 'Other', 'description' => 'I am not part of this transaction.',
    ])->assertForbidden();
    expect(\App\Models\Report::where('reporter_id', $reporter->id)->count())->toBe(2);
});
it('keeps reports private to their reporter and allows admins to manage status', function () {
    $reporter = User::factory()->create();
    $other = User::factory()->create();
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $item = borrowHubItem(User::factory()->create());

    $this->actingAs($reporter)->post(route('reports.store'), [
        'target_type' => 'item', 'target_id' => $item->id,
        'reason' => 'Misleading listing', 'description' => 'The listing description does not match the item.',
    ])->assertRedirect(route('reports.index'));

    $report = \App\Models\Report::firstOrFail();
    $this->actingAs($other)->get(route('reports.index'))->assertOk()->assertViewHas('reports', fn ($reports) => $reports->isEmpty());
    $this->actingAs($reporter)->get(route('reports.index'))->assertOk()->assertViewHas('reports', fn ($reports) => $reports->contains('id', $report->id));
    $this->actingAs($other)->get(route('admin.reports.index'))->assertForbidden();
    $this->actingAs($admin)->patch(route('admin.reports.update', $report), [
        'status' => 'resolved', 'admin_notes' => 'Reviewed and resolved.',
    ])->assertSessionHas('success');
    expect($report->fresh()->status)->toBe('resolved');
    expect($report->fresh()->resolved_at)->not->toBeNull();
});
it('shows genuine reviews and calculates an average from stored ratings', function () {
    $owner = User::factory()->create();
    $borrower = User::factory()->create();
    $item = borrowHubItem($owner);
    $request = borrowHubRequest($item, $borrower, ['status' => 'approved']);
    $transaction = Transaction::create([
        'borrow_request_id' => $request->id, 'expected_return_date' => $request->end_date,
        'returned_at' => now(), 'return_status' => 'returned', 'status' => 'completed',
    ]);
    $transaction->ratings()->create([
        'reviewer_id' => $borrower->id, 'reviewed_user_id' => $owner->id,
        'rating' => 4, 'comment' => 'Helpful and reliable.',
    ]);

    $this->actingAs($borrower)->get(route('reviews.index'))->assertOk()->assertSee('Helpful and reliable.')->assertSee('4.0');
});
it('protects the report review list with the real admin role', function () {
    $member = User::factory()->create();
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();

    $this->actingAs($member)->get(route('admin.reports.index'))->assertForbidden();
    $this->actingAs($admin)->get(route('admin.reports.index'))->assertOk();
});
it('does not approve a pending request after its item is deactivated', function () {
    $owner = User::factory()->create();
    $borrower = User::factory()->create();
    $item = borrowHubItem($owner, ['status' => 'inactive']);
    $borrowRequest = borrowHubRequest($item, $borrower);

    $this->actingAs($owner)
        ->patch(route('owner.borrow-requests.approve', $borrowRequest))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('borrow_requests', ['id' => $borrowRequest->id, 'status' => 'pending']);
    $this->assertDatabaseMissing('transactions', ['borrow_request_id' => $borrowRequest->id]);
});
it('validates malformed browse filters instead of querying bigint columns', function () {
    $this->get('/browse?category_id=not-a-number')->assertSessionHasErrors('category_id');
});
it('stores, replaces, and removes item photos on the public disk', function () {
    Storage::fake('public');
    $owner = User::factory()->create();
    $category = Category::create(['name' => 'Photo Tools', 'status' => 'active']);
    $attributes = [
        'category_id' => $category->id,
        'name' => 'Camera',
        'description' => 'A camera for borrowing.',
        'condition' => 'good',
        'location' => 'Manila',
        'price_per_day' => 0,
    ];
    $png = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/nU8AAAAASUVORK5CYII=';

    $this->actingAs($owner)->post(route('items.store'), $attributes + [
        'image' => UploadedFile::fake()->createWithContent('camera.png', base64_decode($png)),
    ])->assertRedirect(route('items.index'));

    $item = Item::where('name', 'Camera')->firstOrFail();
    Storage::disk('public')->assertExists($item->image_path);
    $oldPath = $item->image_path;

    $this->put(route('items.update', $item), $attributes + [
        'image' => UploadedFile::fake()->createWithContent('replacement.png', base64_decode($png)),
    ])->assertRedirect(route('items.index'));

    $item->refresh();
    expect($item->image_path)->not->toBe($oldPath);
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($item->image_path);
    $replacementPath = $item->image_path;

    $this->put(route('items.update', $item), $attributes + ['remove_image' => 1])
        ->assertRedirect(route('items.index'));
    $item->refresh();
    expect($item->image_path)->toBeNull();
    Storage::disk('public')->assertMissing($item->getOriginal('image_path') ?? $oldPath);
});

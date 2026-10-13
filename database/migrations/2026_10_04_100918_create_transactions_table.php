<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('borrow_request_id')
                ->unique()
                ->constrained('borrow_requests')
                ->cascadeOnDelete();

            $table->timestamp('borrowed_at')->nullable();
            $table->date('expected_return_date');
            $table->timestamp('returned_at')->nullable();

            $table->string('return_status')->default('not_returned');
            $table->string('status')->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
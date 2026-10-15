<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            BorrowHub Rules and Regulations
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 sm:p-8">

                <p class="text-sm text-gray-500">
                    Please read these rules before using BorrowHub.
                </p>

                <div class="mt-6 space-y-6 text-gray-700">

                    <section>
                        <h3 class="text-lg font-bold text-gray-900">
                            1. Honest and Responsible Use
                        </h3>
                        <p class="mt-2">
                            Users must provide accurate information, use
                            respectful language, and act honestly throughout
                            the borrowing process. Fraud, harassment,
                            threats, and intentionally misleading information
                            are prohibited.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-lg font-bold text-gray-900">
                            2. Item Listing Requirements
                        </h3>
                        <p class="mt-2">
                            Item owners must have the right to lend their
                            listed items. Listings should accurately describe
                            the item's condition, rental price, availability,
                            and any known defects or limitations. Owners
                            should use genuine and relevant item photos.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-lg font-bold text-gray-900">
                            3. Borrowing Requests
                        </h3>
                        <p class="mt-2">
                            Borrowers must select realistic borrowing and
                            return dates and review the estimated rental
                            amount before submitting a request. A request is
                            pending until the item owner approves or rejects
                            it. Submitting a request does not guarantee
                            approval or reserve the item.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-lg font-bold text-gray-900">
                            4. Rental Price and Payment
                        </h3>
                        <p class="mt-2">
                            The displayed total is calculated using the
                            listed daily price and the number of calendar
                            days selected, including both the start and end
                            dates. Users must agree on payment method,
                            payment timing, any deposit, and any additional
                            charges before completing the transaction.
                        </p>
                        <p class="mt-2">
                            BorrowHub does not automatically collect,
                            transfer, or refund money unless a supported
                            payment feature is explicitly provided.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-lg font-bold text-gray-900">
                            5. Item Handover and Safe Use
                        </h3>
                        <p class="mt-2">
                            Owners and borrowers should agree on a safe
                            handover location and time. Both parties should
                            inspect the item and record its condition before
                            handover. Borrowers must use the item responsibly
                            and follow any reasonable safety instructions
                            supplied by the owner.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-lg font-bold text-gray-900">
                            6. Returns and Late Items
                        </h3>
                        <p class="mt-2">
                            Borrowers must return the item by the agreed
                            return date and coordinate the return with the
                            owner. If a delay is expected, the borrower must
                            notify the owner as soon as possible. Any late
                            fee or extension must be discussed and agreed
                            upon beforehand and must comply with applicable
                            law.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-lg font-bold text-gray-900">
                            7. Damage, Loss, and Disputes
                        </h3>
                        <p class="mt-2">
                            Borrowers should report damage, loss, accidents,
                            or other problems promptly. Both parties should
                            preserve relevant photos, messages, receipts,
                            and other evidence. Responsibility and any
                            compensation should be determined fairly based
                            on the facts and applicable agreements and law.
                            A report alone does not prove that a user is
                            responsible.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-lg font-bold text-gray-900">
                            8. Cancellations and Refunds
                        </h3>
                        <p class="mt-2">
                            Users should notify the other party promptly if
                            they need to cancel or change an arrangement.
                            Before payment, both parties should clarify
                            cancellation terms. If money has already been
                            paid, any refund should follow the agreed terms
                            and applicable law. BorrowHub does not guarantee
                            or automatically issue refunds.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-lg font-bold text-gray-900">
                            9. Ratings and Reports
                        </h3>
                        <p class="mt-2">
                            Ratings and reviews must be truthful, relevant,
                            and based on genuine experiences. Users may
                            report suspected fraud, unsafe listings,
                            harassment, or violations. Reports may be
                            reviewed by an administrator, but submitting a
                            report does not guarantee a particular outcome.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-lg font-bold text-gray-900">
                            10. Fairness and Legal Compliance
                        </h3>
                        <p class="mt-2">
                            Users must comply with applicable laws of the
                            Philippines. BorrowHub may restrict access or
                            review reported activity in accordance with
                            its procedures. Users should attempt to resolve
                            disagreements respectfully and retain evidence
                            of their arrangements.
                        </p>
                    </section>

                    <section>
                        <h3 class="text-lg font-bold text-gray-900">
                            11. Important Notice
                        </h3>
                        <p class="mt-2">
                            These rules provide general platform guidance
                            and are not a substitute for formal legal advice.
                            Users remain responsible for the agreements they
                            make with one another.
                        </p>
                    </section>

                </div>

                <div class="mt-8 border-t pt-5">
                    <a
                        href="{{ route('borrowing-agreement') }}"
                        class="text-blue-600 underline hover:text-blue-800"
                    >
                        Read the Borrowing Agreement and Transaction Rules
                    </a>

                    <div class="mt-4">
                        <a
                            href="{{ route('items.browse') }}"
                            class="inline-block px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        >
                            Browse Items
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
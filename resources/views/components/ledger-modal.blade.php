@props(['users'])

<div id="ledgerModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center h-full w-full hidden z-50">
    <div class="p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">New Ledger Entry</h3>

            <form action="{{ route('ledger.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                    <input type="number" name="amount" id="amount"  required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" step="any">
                </div>

                <div class="mb-4">
                    <label for="description"
                        class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" id="description"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-1">Transaction Date</label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        value="{{ now()->format('Y-m-d\TH:i') }}">
                    <p class="text-xs text-gray-500 mt-1">Select date and time for this transaction</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <select name="user_id" id="user_select" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a user</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="submit" name="transaction_type" value="lent"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none w-full">
                        Lend
                    </button>
                    <button type="submit" name="transaction_type" value="borrowed"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none w-full">
                        Borrow
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const openModal = document.getElementById('openLedgerModal');
        const modal = document.getElementById('ledgerModal');

        if(openModal && modal) {
            openModal.addEventListener('click', function(){
                // Reset the form and set current date
                if (modal.querySelector('form')) {
                    modal.querySelector('form').reset();
                    document.getElementById('transaction_date').value = getCurrentDateTime();
                }
                modal.classList.remove('hidden');
            });

            window.addEventListener('click', function(event){
                if(event.target === modal){
                    modal.classList.add('hidden');
                }
            });
        }
        
        // Helper function to get current date time in the format needed for datetime-local input
        function getCurrentDateTime() {
            const now = new Date();
            return new Date(now.getTime() - (now.getTimezoneOffset() * 60000))
                .toISOString()
                .slice(0, 16); // Format: YYYY-MM-DDTHH:MM
        }
    })
</script>

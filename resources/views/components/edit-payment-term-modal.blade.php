        <div id="editPaymentTermModal"
            class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full overflow-hidden">
                <div class="bg-blue-100 px-6 py-4">
                    <h3 class="text-lg font-medium text-blue-700">Edit Payment Term</h3>
                </div>
                <form id="editPaymentTermForm" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="editPaymentTermName" class="block text-sm font-medium text-gray-700 mb-2">Term
                            Name</label>
                        <input type="text" name="name" id="editPaymentTermName"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditPaymentTermModal()"
                            class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
<script>
        document.querySelectorAll('.delete-payment-term-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "This payment term will be permanently deleted. This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete this payment term',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(btn.dataset.formId).submit();
                }
            });
        });
    });

    function editPaymentTerm(id, name) {
        document.getElementById('editPaymentTermModal').classList.remove('hidden');
        document.getElementById('editPaymentTermName').value = name;
        document.getElementById('editPaymentTermForm').action = `/payment-terms/${id}`;
    }
    function closeEditPaymentTermModal() {
        document.getElementById('editPaymentTermModal').classList.add('hidden');
    }

    window.addEventListener('click', function (event) {
        if (event.target === document.getElementById('editPaymentTermModal')) {
            closeEditPaymentTermModal();
        }
        if (event.target === document.getElementById('addPaymentTermModal')) {
            document.getElementById('addPaymentTermModal').classList.add('hidden');
        }
    });
</script>
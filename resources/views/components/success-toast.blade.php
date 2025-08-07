@if(session('success'))
    <div id="successToast"
        class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 bg-green-100 border border-green-400 text-green-700 px-6 py-3 rounded shadow-lg flex items-center space-x-2 transition-opacity duration-500">
        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" />
            <path d="M9 12l2 2l4 -4" />
        </svg>
        <span>{{ session('success') }}</span>
        <button onclick="document.getElementById('successToast').remove()"
            class="ml-4 text-green-700 hover:text-green-900 font-bold">&times;</button>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('successToast');
            if (toast) {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 500);
            }
        }, 3500);
    </script>
@endif
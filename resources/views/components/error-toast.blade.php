@if($errors->any())
    <div id="errorToast"
        class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 bg-red-100 border border-red-400 text-red-700 px-6 py-3 rounded shadow-lg flex items-center space-x-2 transition-opacity duration-500">
        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12" y2="16" />
        </svg>
        <span>{{ $errors->first() }}</span>
        <button onclick="document.getElementById('errorToast').remove()"
            class="ml-4 text-red-700 hover:text-red-900 font-bold">&times;</button>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('errorToast');
            if (toast) {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 500);
            }
        }, 3500);
    </script>
@endif
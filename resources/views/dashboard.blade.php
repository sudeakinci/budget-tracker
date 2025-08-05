<x-layout :title="'Dashboard'">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4">Welcome, {{ Auth::user()->name }}!</h2>
        <p>You're now logged in to your account.</p>

        <!-- dashboard content -->
    </div>
</x-layout>
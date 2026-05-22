<x-guest-layout>
<div class="max-w-md mx-auto py-16 px-4 text-center">
    <h1 class="text-2xl font-semibold mb-4">{{ __('This link is protected') }}</h1>
    <form method="POST" action="{{ route('link.unlock', $slug) }}" class="space-y-4 bg-white dark:bg-gray-800 p-6 rounded shadow">
        @csrf
        <input name="password" type="password" required placeholder="{{ __('Enter password') }}"
               class="w-full rounded">
        @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        <button class="w-full bg-blue-600 text-white py-2 rounded">{{ __('Unlock') }}</button>
    </form>
</div>
</x-guest-layout>

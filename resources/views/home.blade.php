<x-guest-layout>
<div class="max-w-3xl mx-auto px-4 py-16">
    <h1 class="text-4xl font-bold mb-2">{{ __('Shorten your URL') }}</h1>
    <p class="text-gray-600 dark:text-gray-300 mb-8">{{ __('Earn money for every valid view of your shortened link.') }}</p>

    @if(session('shortUrl'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-300 rounded p-4 mb-4">
            <strong>{{ __('Your short URL') }}:</strong>
            <a href="{{ session('shortUrl') }}" class="text-blue-600 underline" target="_blank">{{ session('shortUrl') }}</a>
        </div>
    @endif

    <form method="POST" action="{{ route('shorten.guest') }}" class="space-y-4 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">{{ __('Original URL') }}</label>
            <input name="original_url" value="{{ old('original_url') }}" type="url" required
                   class="w-full rounded border-gray-300 dark:bg-gray-700"
                   placeholder="https://example.com/...">
            @error('original_url') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">{{ __('Custom alias (optional)') }}</label>
            <input name="custom_alias" value="{{ old('custom_alias') }}" type="text" pattern="[A-Za-z0-9_-]{3,32}"
                   class="w-full rounded border-gray-300 dark:bg-gray-700"
                   placeholder="my-link">
        </div>
        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded">
            {{ __('Shorten') }}
        </button>
        @guest
            <p class="text-xs text-gray-500 text-center">{{ __('Sign up to earn money from your links.') }}</p>
        @endguest
    </form>
</div>
</x-guest-layout>

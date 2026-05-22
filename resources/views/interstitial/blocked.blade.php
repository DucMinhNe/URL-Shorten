<x-guest-layout>
<div class="max-w-md mx-auto py-16 px-4 text-center">
    <h1 class="text-2xl font-semibold mb-4 text-red-600">{{ __('Link not available') }}</h1>
    <p class="text-gray-600 dark:text-gray-300">{{ __('This link has been disabled or removed.') }}</p>
    <a href="{{ route('home') }}" class="inline-block mt-6 text-blue-600 underline">{{ __('Back to home') }}</a>
</div>
</x-guest-layout>

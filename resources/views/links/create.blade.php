<x-app-layout>
<x-slot name="header"><h2 class="font-semibold text-xl">{{ __('New link') }}</h2></x-slot>
<div class="py-12 max-w-2xl mx-auto px-4">
<form method="POST" action="{{ route('links.store') }}" class="space-y-4 bg-white dark:bg-gray-800 p-6 rounded shadow">
    @csrf
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Original URL') }}</label>
        <input name="original_url" value="{{ old('original_url') }}" type="url" required class="w-full rounded">
        @error('original_url') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Custom alias (optional)') }}</label>
        <input name="custom_alias" value="{{ old('custom_alias') }}" type="text" class="w-full rounded">
        @error('custom_alias') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Password (optional)') }}</label>
        <input name="password" type="text" class="w-full rounded" autocomplete="off">
        @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>
    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">{{ __('Create') }}</button>
</form>
</div>
</x-app-layout>

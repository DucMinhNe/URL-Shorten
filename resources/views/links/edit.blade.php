<x-app-layout>
<x-slot name="header"><h2 class="font-semibold text-xl">{{ __('Edit link') }} /{{ $link->slug }}</h2></x-slot>
<div class="py-12 max-w-2xl mx-auto px-4">
<form method="POST" action="{{ route('links.update', $link) }}" class="space-y-4 bg-white dark:bg-gray-800 p-6 rounded shadow">
    @csrf @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Original URL') }}</label>
        <input name="original_url" value="{{ old('original_url', $link->original_url) }}" type="url" required class="w-full rounded">
        @error('original_url') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="w-full rounded">
            <option value="active" @selected($link->status==='active')>Active</option>
            <option value="disabled" @selected($link->status==='disabled')>Disabled</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('New password') }} (leave blank to keep current)</label>
        <input name="password" type="text" class="w-full rounded" autocomplete="off">
        @if($link->password)<label class="text-sm"><input type="checkbox" name="remove_password" value="1"> {{ __('Remove password') }}</label>@endif
    </div>
    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">{{ __('Save') }}</button>
</form>
</div>
</x-app-layout>

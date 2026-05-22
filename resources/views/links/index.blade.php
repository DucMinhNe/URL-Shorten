<x-app-layout>
<x-slot name="header"><h2 class="font-semibold text-xl">{{ __('My links') }}</h2></x-slot>
<div class="py-12 max-w-7xl mx-auto px-4">
    @if(session('shortUrl'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-300 rounded p-4 mb-4">
            {{ __('Short URL') }}:
            <a href="{{ session('shortUrl') }}" class="text-blue-600 underline" target="_blank">{{ session('shortUrl') }}</a>
        </div>
    @endif
    @if(session('status'))
        <div class="bg-blue-100 dark:bg-blue-900 border border-blue-300 rounded p-3 mb-4 text-sm">{{ session('status') }}</div>
    @endif
    <a href="{{ route('links.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mb-4">+ {{ __('New link') }}</a>

    <div class="bg-white dark:bg-gray-800 rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700"><tr>
            <th class="px-4 py-2 text-left">Short</th>
            <th class="px-4 py-2 text-left">{{ __('Original URL') }}</th>
            <th class="px-4 py-2">Clicks</th>
            <th class="px-4 py-2">{{ __('Valid views') }}</th>
            <th class="px-4 py-2">{{ __('Earned') }}</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2"></th>
        </tr></thead>
        <tbody>
        @forelse($links as $link)
            <tr class="border-t dark:border-gray-700">
                <td class="px-4 py-2"><a href="{{ url('/'.$link->slug) }}" class="text-blue-600 underline" target="_blank">/{{ $link->slug }}</a></td>
                <td class="px-4 py-2 truncate max-w-xs">{{ $link->original_url }}</td>
                <td class="px-4 py-2 text-center">{{ $link->total_clicks }}</td>
                <td class="px-4 py-2 text-center">{{ $link->valid_views }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($link->total_earned) }} đ</td>
                <td class="px-4 py-2 text-center"><span class="px-2 py-0.5 rounded text-xs {{ $link->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">{{ $link->status }}</span></td>
                <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                    <a href="{{ route('links.edit', $link) }}" class="text-blue-600">{{ __('Edit') }}</a>
                    <form method="POST" action="{{ route('links.destroy', $link) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                        <button class="text-red-600">{{ __('Delete') }}</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center py-8 text-gray-500">{{ __('No links yet') }}</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $links->links() }}</div>
</div>
</x-app-layout>

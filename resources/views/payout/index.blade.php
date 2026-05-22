<x-app-layout>
<x-slot name="header"><h2 class="font-semibold text-xl">{{ __('Payout') }}</h2></x-slot>
<div class="py-12 max-w-4xl mx-auto px-4">
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <div class="text-sm text-gray-500">{{ __('Balance') }}</div>
            <div class="text-2xl font-bold">{{ number_format(auth()->user()->balance) }} đ</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <div class="text-sm text-gray-500">{{ __('Total earned') }}</div>
            <div class="text-2xl font-bold">{{ number_format(auth()->user()->total_earned) }} đ</div>
        </div>
    </div>

    @if(session('status')) <div class="bg-green-100 p-3 rounded mb-4">{{ session('status') }}</div> @endif

    <form method="POST" action="{{ route('payout.store') }}" class="space-y-4 bg-white dark:bg-gray-800 p-6 rounded shadow mb-8">
        @csrf
        <h3 class="font-semibold text-lg">{{ __('Request payout') }}</h3>
        <div>
            <label class="block text-sm font-medium">{{ __('Amount (VND)') }}</label>
            <input name="amount" type="number" min="1" value="{{ old('amount') }}" required class="w-full rounded">
            @error('amount') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">{{ __('Method') }}</label>
            <select name="method" class="w-full rounded">
                <option value="momo">Momo</option>
                <option value="zalo">ZaloPay</option>
                <option value="paypal">PayPal</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">{{ __('Account info') }}</label>
            <input name="account_info" type="text" value="{{ old('account_info') }}" required class="w-full rounded" placeholder="0901234567 / email@paypal.com">
            @error('account_info') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">{{ __('Submit request') }}</button>
    </form>

    <h3 class="font-semibold text-lg mb-2">{{ __('History') }}</h3>
    <div class="bg-white dark:bg-gray-800 rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700"><tr>
            <th class="px-4 py-2 text-left">Date</th>
            <th class="px-4 py-2 text-right">{{ __('Amount (VND)') }}</th>
            <th class="px-4 py-2">{{ __('Method') }}</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2 text-left">Note</th>
        </tr></thead>
        <tbody>
        @forelse($requests as $r)
            <tr class="border-t dark:border-gray-700">
                <td class="px-4 py-2">{{ $r->created_at->format('Y-m-d H:i') }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($r->amount) }}</td>
                <td class="px-4 py-2 text-center">{{ $r->method }}</td>
                <td class="px-4 py-2 text-center">
                    <span class="px-2 py-0.5 rounded text-xs {{ ['pending'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-blue-100 text-blue-700','paid'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'][$r->status] }}">{{ $r->status }}</span>
                </td>
                <td class="px-4 py-2 text-xs">{{ $r->admin_note ?? $r->transaction_ref }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center py-8 text-gray-500">{{ __('No payout requests yet') }}</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $requests->links() }}</div>
</div>
</x-app-layout>

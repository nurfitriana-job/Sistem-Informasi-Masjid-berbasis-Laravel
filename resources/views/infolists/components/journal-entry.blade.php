<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="overflow-x-auto">
        <table id="detail-jurnal-table" class="w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg shadow-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Kode Akun') }}
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Nama Akun') }}
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Debit') }}
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Kredit') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @php
                    $debit = 0;
                    $credit = 0;
                @endphp
                @forelse ($getState() as $state)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">
                            {{ $state?->account?->code }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            {{ $state?->account?->name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 text-right">
                            @if ($state->type == 'debit')
                                @php
                                    $debit += $state?->amount;
                                @endphp
                                @money($state?->amount, 'IDR')
                            @else
                                @money(0, 'IDR')
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 text-right">
                            @if ($state->type == 'credit')
                                @php
                                    $credit += $state?->amount;
                                @endphp
                                @money($state?->amount, 'IDR')
                            @else
                                @money(0, 'IDR')
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 text-center">
                            {{ __('No data available') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-800 font-semibold">
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200" colspan="2">Total</td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 text-right">
                        @money($debit, 'IDR')
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 text-right">
                        @money($credit, 'IDR')
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-dynamic-component>

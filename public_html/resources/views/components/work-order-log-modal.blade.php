<div class="space-y-3">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-semibold">
                {{ $record->wo_number }}
            </h3>
            <p class="text-sm text-gray-400">
                {{ $record->customer?->name }} — {{ $record->vehicle?->plate_number }}
            </p>
        </div>
        <span class="text-xs px-2 py-0.5 rounded bg-gray-700/50 text-gray-200">
            Total logs: {{ $logs->count() }}
        </span>
    </div>

    <div class="rounded-lg border border-gray-700/50 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-800/60">
                <tr class="text-left">
                    <th class="px-3 py-2">Time</th>
                    <th class="px-3 py-2">Stage / Status</th>
                    <th class="px-3 py-2">By</th>
                    <th class="px-3 py-2">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-t border-gray-800/60">
                        <td class="px-3 py-2 text-gray-300">
                            {{ optional($log->started_at ?? $log->created_at)->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-0.5 rounded text-xs
                                @class([
                                    // --- WARNA TELAH DIUBAH ---
                                    'bg-blue-600/20 text-blue-300'   => in_array($log->stage, ['Checked-In','QC','Wash']), // Diubah dari sky ke blue (Astra)
                                    'bg-amber-600/20 text-amber-300'=> $log->stage === 'Waiting', // Tetap (Warning)
                                    'bg-green-600/20 text-green-300'=> in_array($log->stage, ['Final','Done']), // Tetap (Success)
                                    'bg-red-600/20 text-red-300'     => $log->stage === 'In-Progress', // Diubah dari indigo ke red (Auto2000)
                                    'bg-gray-600/20 text-gray-300'   => ! in_array($log->stage, ['Checked-In','QC','Wash','Waiting','Final','Done','In-Progress']), // Tetap (Netral)
                                    // --- SELESAI PERUBAHAN WARNA ---
                                ])
                            ">{{ $log->stage }}</span>
                        </td>
                        <td class="px-3 py-2 text-gray-300">
                            {{ $log->user?->name ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-gray-300">
                            {{ $log->remarks ?: '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-6 text-center text-gray-400">
                            No logs yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
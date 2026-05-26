@php
    $pct = max(0, min(100, (int) $getState()));
@endphp

<div class="w-28">
    <div class="h-2 rounded bg-gray-700/50 overflow-hidden">
        <div
            class="h-2 rounded
                    @if($pct >= 100) bg-green-500   @elseif($pct >= 75) bg-red-500     @elseif($pct >= 50) bg-blue-500    @elseif($pct >= 25) bg-amber-500   @else bg-gray-400 @endif"
            style="width: {{ $pct }}%"
        ></div>
    </div>
    <div class="text-xs mt-1 text-gray-300 text-center">{{ $pct }}%</div>
</div>
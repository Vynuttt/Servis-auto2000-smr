<x-filament-panels::page>
<div wire:poll.{{ $pollSeconds }}s class="mb-root">

    {{-- HEADER: Title + Actions --}}
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <div class="text-2xl font-semibold">Monitoring Board</div>

            <div class="flex items-center gap-2">
                @php
                    $createUrl = class_exists(\App\Filament\Resources\WorkOrderResource::class)
                        ? \App\Filament\Resources\WorkOrderResource::getUrl('create')
                        : null;
                @endphp

                @if($createUrl)
                    <x-filament::button
                        tag="a"
                        href="{{ $createUrl }}"
                        color="primary" {{-- Otomatis menjadi Merah --}}
                        icon="heroicon-m-plus"
                    >
                        New Work Order
                    </x-filament::button>
                @endif

                <x-filament::button
                    color="gray"
                    icon="heroicon-m-arrow-path"
                    wire:click="$refresh"
                >
                    Refresh
                </x-filament::button>

                <x-filament::button
                    color="gray"
                    size="xs"
                    icon="{{ $showFilters ? 'heroicon-m-eye-slash' : 'heroicon-m-eye' }}"
                    wire:click="$toggle('showFilters')"
                >
                    {{ $showFilters ? 'Hide controls' : 'Show controls' }}
                </x-filament::button>
            </div>
        </div>

        {{-- FILTERS SECTION --}}
        @if ($showFilters)
        <div class="mb-card p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                {{-- Date --}}
                <div>
                    <label class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 block">Date</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model.live="date" />
                    </x-filament::input.wrapper>
                </div>

                {{-- Start Time --}}
                <div>
                    <label class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 block">Start Time</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="time" step="900" wire:model.live="startTime" />
                    </x-filament::input.wrapper>
                </div>

                {{-- End Time --}}
                <div>
                    <label class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 block">End Time</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="time" step="900" wire:model.live="endTime" />
                    </x-filament::input.wrapper>
                </div>

                {{-- Mechanic ID --}}
                <div>
                    <label class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 block">Mechanic ID (Optional)</label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="number" min="1" placeholder="Filter by Mechanic ID" wire:model.live="mechanicId" />
                    </x-filament::input.wrapper>
                </div>
            </div>
        </div>
        @endif

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach ($this->getKpis() as $key => $value)
                <div class="mb-card p-4">
                    <div class="mb-kpi-title">
                        {{ match($key) {
                            'active' => 'Active WO',
                            'done_today' => 'Done Today',
                            'overdue' => 'Overdue',
                            'util' => 'Utilization',
                            default => ucfirst($key)
                        } }}
                    </div>
                    <div class="mb-kpi-value {{ match($key) {
                        'active' => 'text-gray-900 dark:text-white',
                        'done_today' => 'text-green-600 dark:text-green-400',
                        'overdue' => 'text-red-600 dark:text-red-400',
                        'util' => 'text-red-600 dark:text-red-400', // Diubah dari blue ke red
                        default => 'text-gray-900 dark:text-white'
                    } }}">
                    {{ $value }}{{ $key === 'util' ? '%' : '' }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- LEGEND --}}
        <div class="flex flex-wrap items-center gap-2">
            @foreach ($this->getLegend() as $item)
                <span class="mb-pill">
                    <span class="w-3 h-3 rounded {{ $item['class'] }}"></span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $item['label'] }}</span>
                </span>
            @endforeach
        </div>
    </div>

    {{-- TIMELINE BOARD --}}
    <div class="mt-6 mb-card overflow-hidden">
        <div class="overflow-x-auto mb-scroll">
            {{-- HEADER --}}
            <div class="grid grid-cols-12 mb-time-header sticky top-0 z-30">
                <div class="col-span-2 px-3 py-2 font-medium text-gray-700 dark:text-gray-300">Stall</div>
                <div class="col-span-10 px-2 py-2 relative">
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        @foreach ($this->getTimeTicks() as $tick)
                            <div class="text-[11px]">{{ $tick->format('H:i') }}</div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- STALL ROWS --}}
            @foreach ($this->getStalls() as $stall)
                @php
                    $window = $this->getWindow();
                    $ticksCount = count($this->getTimeTicks());
                    $nowLeft = null;
                    if (now()->between($window['start'], $window['end'])) {
                        $totalMinutes = $window['minutes'] > 0 ? $window['minutes'] : 1;
                        $nowLeft = round(($window['start']->diffInMinutes(now()) / $totalMinutes) * 100, 2);
                    }
                    $items = $this->getActiveWorkOrders()->where('stall_id', $stall->id);
                @endphp

                <div class="grid grid-cols-12 border-b border-gray-200 dark:border-gray-700 last:border-b-0 relative hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors">
                    <div class="col-span-2 px-3 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-slate-600 text-[10px] flex items-center justify-center text-white">
                            {{ \Illuminate\Support\Str::of($stall->name)->substr(0,2)->upper() }}
                        </div>
                        <span>{{ $stall->name }}</span>
                    </div>

                    <div class="col-span-10 px-2 py-3 relative" style="min-height: 70px;">
                        <div class="absolute inset-0 mb-grid pointer-events-none" style="--ticks: {{ $ticksCount }};">
                            @for ($i = 0; $i < $ticksCount; $i++)
                                <div class="mb-grid-col"></div>
                            @endfor
                        </div>

                        @if(!is_null($nowLeft))
                            <div class="mb-now-line bg-red-600" style="left: {{ $nowLeft }}%">
                                <div class="absolute -top-4 -translate-x-1/2 text-[10px] text-red-500">{{ now()->format('H:i') }}</div>
                                <div class="absolute -top-1 -left-1 w-2 h-2 rounded-full bg-red-500"></div>
                            </div>
                            @endif

                        {{-- Work Orders --}}
                        @foreach ($items as $wo)
                            @php
                                $style   = $this->computeBlockStyle($wo);
                                $pending = $this->hasRequest($wo);
                                $editUrl = $this->woEditUrl($wo);

                                $isLate  = $wo->planned_finish && $wo->planned_finish->lt(now()) && $wo->status !== \App\Models\WorkOrder::S_DONE;
                                $dueSoon = $wo->planned_finish && now()->diffInMinutes($wo->planned_finish, false) <= 30 && !$isLate && $wo->status !== \App\Models\WorkOrder::S_DONE;
                            @endphp

                            <a @if($editUrl) href="{{ $editUrl }}" @endif
                               class="mb-wo {{ $this->statusColor($wo->status) }}"
                               style="left: {{ $style['left'] }}%; width: {{ $style['width'] }}%; min-width: 5rem; top: 4px;"
                               title="WO: {{ $wo->wo_number }}&#10;Customer: {{ $wo->customer->name ?? '-' }}&#10;Plate: {{ $wo->vehicle->plate_number ?? '-' }}&#10;{{ $wo->planned_start?->format('H:i') }}–{{ $wo->planned_finish?->format('H:i') }} | {{ $wo->status }}">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold truncate">{{ $wo->wo_number }}</span>
                                    @if($pending)
                                        <span class="bg-amber-300 text-amber-900 rounded px-1.5 py-0.5 text-[10px] font-semibold whitespace-nowrap">
                                            Req: {{ $pending }}
                                        </span>
                                    @endif
                                </div>

                                <div class="opacity-90 text-[11px] truncate">
                                    <span class="font-medium">{{ $wo->vehicle->plate_number ?? '-' }}</span>
                                    • {{ $wo->customer->name ?? '-' }}
                                </div>

                                <div class="opacity-80 text-[10px] flex items-center gap-1">
                                    {{ $wo->planned_start?->format('H:i') }}–{{ $wo->planned_finish?->format('H:i') }} • {{ $wo->status }}
                                    @if($isLate)
                                        <span class="mb-chip mb-chip-late">Late</span>
                                    @elseif($dueSoon)
                                        <span class="mb-chip mb-chip-soon">Due soon</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- BACKLOG --}}
            @php($unassigned = $this->getActiveWorkOrders()->whereNull('stall_id'))
            @if($unassigned->count())
                <div class="grid grid-cols-12 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <div class="col-span-2 px-3 py-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Backlog (No Stall)
                    </div>
                    <div class="col-span-10 px-2 py-3">
                        <div class="flex flex-wrap gap-2">
                            @foreach ($unassigned as $wo)
                                @php($editUrl = $this->woEditUrl($wo))
                                <a @if($editUrl) href="{{ $editUrl }}" @endif
                                   class="rounded-lg px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:shadow-md transition-shadow cursor-pointer"
                                   title="WO: {{ $wo->wo_number }}&#10;{{ $wo->planned_start?->format('H:i') }}–{{ $wo->planned_finish?->format('H:i') }} | {{ $wo->status }}">
                                    <span class="font-semibold">{{ $wo->wo_number }}</span> •
                                    {{ $wo->vehicle->plate_number ?? '-' }} •
                                    <span class="text-gray-500 dark:text-gray-400">{{ $wo->status }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</x-filament-panels::page>
<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold">
                    Clock In/Out Status
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    @if($isOnDuty)
                        <span class="inline-flex items-center gap-1">
                            <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                            You are currently <strong>ON DUTY</strong>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1">
                            <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                            You are currently <strong>OFF DUTY</strong>
                        </span>
                    @endif
                </p>
            </div>

            <div>
                <x-filament::button
                    wire:click="toggleClockIn"
                    :color="$isOnDuty ? 'danger' : 'success'"
                    size="lg"
                >
                    @if($isOnDuty)
                        <x-heroicon-o-clock class="w-5 h-5 mr-2" />
                        Clock Out
                    @else
                        <x-heroicon-o-clock class="w-5 h-5 mr-2" />
                        Clock In
                    @endif
                </x-filament::button>
            </div>
        </div>

        @if($isOnDuty)
            <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <p class="text-sm text-green-800 dark:text-green-200">
                    <strong>Remember:</strong> Don't forget to clock out when you finish your shift.
                </p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
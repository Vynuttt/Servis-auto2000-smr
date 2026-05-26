<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Pending Approval Requests</x-slot>

        <div class="space-y-3">
            @forelse ($items as $it)
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-between border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <img
                            class="w-12 h-12 rounded-full object-cover"
                            src="{{ $it['mechanic']['avatar'] ? asset('storage/' . $it['mechanic']['avatar']) : asset('images/default-avatar.png') }}"
                            alt="avatar"
                        />
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ $it['mechanic']['name'] }}
                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ $it['mechanic']['employee_number'] ?? '—' }})</span>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                WO #{{ $it['work_order']['wo_number'] }}
                                • Request: <span class="font-medium">{{ strtoupper($it['request_type']) }}</span>
                                • Target: <span class="font-medium">{{ $it['requested_status'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-filament::button color="success" wire:click="approve({{ $it['id'] }})">
                            Approve
                        </x-filament::button>

                        <x-filament::button
                            color="danger"
                            x-data
                            x-on:click="
                                const note = prompt('Reject note (required):');
                                if (note !== null) { $wire.reject({{ $it['id'] }}, note) }
                            "
                        >
                            Reject
                        </x-filament::button>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    No pending approvals ✅
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
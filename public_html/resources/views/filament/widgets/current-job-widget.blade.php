<x-filament-widgets::widget class="fi-fo-current-job">
    <x-filament::section>
        
        {{-- Cek apakah ada pekerjaan ($record) --}}
        @if ($record)
            <div class="space-y-4">
                {{-- Judul dan Status --}}
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Pekerjaan Aktif Saat Ini
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            WO: <span class="font-medium text-primary-600">{{ $record->wo_number }}</span>
                        </p>
                    </div>
                    {{-- Tampilkan status pending jika ada --}}
                    @if ($record->pendingRequestedStatus())
                        <div class="rounded-lg border border-yellow-300 bg-yellow-50 p-2 text-right dark:border-yellow-600 dark:bg-gray-800">
                            <p class="text-sm font-medium text-yellow-700 dark:text-yellow-300">
                                <x-heroicon-o-clock class="mr-1 inline-block h-4 w-4" />
                                Menunggu: <strong>{{ $record->pendingRequestedStatus() }}</strong>
                            </p>
                        </div>
                    @else
                         <span class="px-3 py-1 text-sm font-medium rounded-full bg-primary-500/10 text-primary-700 dark:text-primary-400">
                            {{ $record->status }}
                        </span>
                    @endif
                </div>

                {{-- Detail Kendaraan dan Customer --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Plat Nomor</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $record->vehicle?->plate_number ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kendaraan</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $record->vehicle?->model ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $record->vehicle?->customer?->name ?? 'N/A' }}</dd>
                    </div>
                </div>

                {{-- Daftar Layanan --}}
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Layanan yang Dikerjakan:</dt>
                    <dd class="text-gray-900 dark:text-white">
                        <ul class="space-y-2">
                            @forelse ($record->booking?->bookingServices as $bookingService)
                                <li class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                    {{ $bookingService->service?->name ?? 'Layanan tidak ditemukan' }}
                                </li>
                            @empty
                                <li class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                    Tidak ada detail layanan.
                                </li>
                            @endforelse
                        </ul>
                    </dd>
                </div>
            </div>

        @else
            {{-- Tampilan jika tidak ada pekerjaan aktif --}}
            <div class="flex flex-col items-center justify-center p-6 text-center">
                <div class="w-12 h-12 bg-primary-500/10 text-primary-500 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tidak Ada Pekerjaan Aktif</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Mulai pekerjaan baru dari "My Jobs".
                </p>
            </div>
        @endif

    </x-filament::section>
</x-filament-widgets::widget>
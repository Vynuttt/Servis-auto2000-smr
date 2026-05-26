// tailwind.config.cjs
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        // Blade views kamu
        './resources/views/**/*.blade.php',
        // Semua file Filament (resources & vendor)
        './app/Filament/**/*.php',

        './vendor/filament/**/*.blade.php',
        // (opsional) jika kamu punya komponen Blade di folder lain
        './app/View/Components/**/*.php',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        // kamu bisa tambahkan plugin jika perlu, contoh:
        // require('@tailwindcss/forms'),
    ],
    // SAFELIST: supaya kelas warna dinamis dari PHP tidak dihapus saat build
    safelist: [
        // Merah (Primary / Error)
        'bg-red-500',
        'bg-red-600',
        'bg-red-700',
        // Biru (Info / Astra Blue)
        'bg-blue-500',
        // Kuning (Warning)
        'bg-amber-500',
        // Hijau (Success)
        'bg-emerald-600',
        'bg-emerald-700',
        // Netral
        'bg-gray-500',
        'bg-slate-400',
        'bg-slate-500',
    ],
}
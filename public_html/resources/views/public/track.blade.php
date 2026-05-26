@extends('layouts.public')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Lacak Progress Servis</h1>
    
    <div class="bg-slate-800 rounded-lg p-4 mb-4">
        <p class="text-sm text-slate-300">
            Masukkan kode booking atau work order Anda untuk melacak progress servis kendaraan.
        </p>
        <p class="text-xs text-slate-400 mt-2">
            Format: <code class="bg-slate-700 px-1 rounded">BK-20251101-0001</code> atau 
            <code class="bg-slate-700 px-1 rounded">WO-20251101-0001</code>
        </p>
    </div>

    <form id="trackForm" class="space-y-4">
        <div>
            <label for="code" class="block text-sm font-medium mb-2">
                Kode Booking / Work Order
            </label>
            <input 
                type="text" 
                id="code"
                name="code" 
                class="w-full border border-slate-600 rounded-lg p-3 bg-slate-800 text-slate-100 focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                placeholder="Contoh: BK-20251101-0001" 
                required
                pattern="(BK|WO)-\d{8}-\d{4}"
                title="Format harus BK-YYYYMMDD-XXXX atau WO-YYYYMMDD-XXXX"
            >
            <p class="text-xs text-slate-400 mt-1">
                * Kode dapat ditemukan di email konfirmasi atau struk booking Anda
            </p>
        </div>

        <button 
            type="submit"
            class="w-full px-4 py-3 rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium transition-colors"
        >
            🔍 Lacak Sekarang
        </button>
    </form>
</div>

<script>
document.getElementById('trackForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const code = document.getElementById('code').value.trim().toUpperCase();
    
    if (!code) {
        alert('Mohon masukkan kode tracking');
        return;
    }
    
    // Redirect ke route show dengan kode yang dimasukkan
    window.location.href = "{{ route('track.form') }}/" + encodeURIComponent(code);
});
</script>
@endsection
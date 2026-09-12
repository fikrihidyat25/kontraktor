<x-app-layout>
    <x-slot name="title">Edit Tim Proyek</x-slot>

    <div class="page-header">
        <h1>Pilih Tim Proyek (Kontraktor & Konsultan)</h1>
    </div>

    <div class="content-area">
        <div style="max-width:680px;">
            <div style="background:#fff; border:1px solid var(--border); padding:28px;">
                <div style="margin-bottom:20px;">
                    <h3 style="margin-bottom:4px; color:#0F172B;">{{ $proyek->nama_proyek }}</h3>
                    <p style="font-size:13px; color:#6B7280;">Silakan tentukan Kontraktor dan Konsultan Pengawas untuk proyek ini.</p>
                </div>
                <form method="POST" action="{{ route('ppk.proyek.update-tim', $proyek) }}">
                    @csrf
                    <div style="display:grid; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Kontraktor</label>
                            <select name="kontraktor_id" class="form-control" required>
                                <option value="">— Pilih Kontraktor —</option>
                                @foreach($kontraktors as $k)
                                <option value="{{ $k->id }}" {{ (old('kontraktor_id', $proyek->kontraktor_id) == $k->id) ? 'selected' : '' }}>
                                    {{ $k->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('kontraktor_id')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konsultan Pengawas</label>
                            <select name="konsultan_id" class="form-control" required>
                                <option value="">— Pilih Konsultan —</option>
                                @foreach($konsultans as $k)
                                <option value="{{ $k->id }}" {{ (old('konsultan_id', $proyek->konsultan_id) == $k->id) ? 'selected' : '' }}>
                                    {{ $k->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('konsultan_id')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div style="display:flex; gap:10px; margin-top:24px; padding-top:16px; border-top:1px solid var(--border);">
                        <button type="submit" class="btn-primary">Simpan Tim</button>
                        <a href="{{ route('ppk.dashboard') }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="title">Tambah Proyek</x-slot>

    <div class="page-header">
        <h1>Tambah Proyek Konstruksi Baru</h1>
    </div>

    <div class="content-area">
        <div style="max-width:680px;">
            <div style="background:#fff; border:1px solid var(--border); padding:28px;">
                <form method="POST" action="{{ route('admin.proyeks.store') }}">
                    @csrf
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Nama Proyek</label>
                            <input type="text" name="nama_proyek" class="form-control" value="{{ old('nama_proyek') }}" required placeholder="Nama lengkap proyek konstruksi">
                            @error('nama_proyek')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor Kontrak</label>
                            <input type="text" name="nomor_kontrak" class="form-control" value="{{ old('nomor_kontrak') }}" placeholder="Optional">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nilai Kontrak (Rp)</label>
                            <input type="number" name="nilai_kontrak" class="form-control num" value="{{ old('nilai_kontrak') }}" required min="0" step="1000000" placeholder="0">
                            @error('nilai_kontrak')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Lokasi Proyek</label>
                            <textarea name="lokasi" class="form-control" rows="2" required>{{ old('lokasi') }}</textarea>
                            @error('lokasi')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}" required>
                            @error('tanggal_mulai')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}" required>
                            @error('tanggal_selesai')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>

                        <div style="grid-column:1/-1; border-top:1px solid var(--border); padding-top:16px; margin-top:4px;">
                            <span class="section-title">Penugasan Personil</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kontraktor</label>
                            <select name="kontraktor_id" class="form-control" required>
                                <option value="">— Pilih Kontraktor —</option>
                                @foreach($kontraktors as $k)
                                <option value="{{ $k->id }}" {{ old('kontraktor_id')==$k->id?'selected':'' }}>{{ $k->name }}</option>
                                @endforeach
                            </select>
                            @error('kontraktor_id')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konsultan Pengawas</label>
                            <select name="konsultan_id" class="form-control" required>
                                <option value="">— Pilih Konsultan —</option>
                                @foreach($konsultans as $k)
                                <option value="{{ $k->id }}" {{ old('konsultan_id')==$k->id?'selected':'' }}>{{ $k->name }}</option>
                                @endforeach
                            </select>
                            @error('konsultan_id')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">PPK (Klien)</label>
                            <select name="ppk_id" class="form-control" required>
                                <option value="">— Pilih PPK —</option>
                                @foreach($ppks as $p)
                                <option value="{{ $p->id }}" {{ old('ppk_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                            @error('ppk_id')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Deskripsi Proyek (Opsional)</label>
                            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
                        </div>
                    </div>
                    <div style="display:flex; gap:10px; margin-top:8px; padding-top:16px; border-top:1px solid var(--border);">
                        <button type="submit" class="btn-primary">Simpan Proyek</button>
                        <a href="{{ route('admin.proyeks.index') }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

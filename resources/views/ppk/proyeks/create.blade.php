<x-app-layout>
    <x-slot name="title">Tambah Proyek</x-slot>

    <div class="page-header" style="text-align: center;">
        <h1>Tambah Proyek Konstruksi Baru</h1>
    </div>

    <div class="content-area">
        <div style="max-width:680px; margin: 0 auto;">
            <div style="background:#fff; border:1px solid var(--border); padding:28px;">
                <form method="POST" action="{{ route('ppk.proyeks.store') }}">
                    @csrf
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">SKPD</label>
                            <select name="skpd" class="form-control" required>
                                <option value="">— Pilih SKPD —</option>
                                <option value="Dinas Pekerjaan Umum dan Penataan Ruang (PUPR)" {{ old('skpd') == 'Dinas Pekerjaan Umum dan Penataan Ruang (PUPR)' ? 'selected' : '' }}>Dinas Pekerjaan Umum dan Penataan Ruang (PUPR)</option>
                                <option value="Dinas Perumahan Rakyat dan Kawasan Permukiman (Perkim)" {{ old('skpd') == 'Dinas Perumahan Rakyat dan Kawasan Permukiman (Perkim)' ? 'selected' : '' }}>Dinas Perumahan Rakyat dan Kawasan Permukiman (Perkim)</option>
                                <option value="Dinas Bina Marga Cipta Karya dan Tata Ruang (BMCKTR)" {{ old('skpd') == 'Dinas Bina Marga Cipta Karya dan Tata Ruang (BMCKTR)' ? 'selected' : '' }}>Dinas Bina Marga Cipta Karya dan Tata Ruang (BMCKTR)</option>
                                <option value="Dinas Sumber Daya Air dan Bina Konstruksi (SDABK)" {{ old('skpd') == 'Dinas Sumber Daya Air dan Bina Konstruksi (SDABK)' ? 'selected' : '' }}>Dinas Sumber Daya Air dan Bina Konstruksi (SDABK)</option>
                                <option value="Dinas Pendidikan" {{ old('skpd') == 'Dinas Pendidikan' ? 'selected' : '' }}>Dinas Pendidikan</option>
                                <option value="Dinas Kesehatan" {{ old('skpd') == 'Dinas Kesehatan' ? 'selected' : '' }}>Dinas Kesehatan</option>
                                <option value="Dinas Perhubungan" {{ old('skpd') == 'Dinas Perhubungan' ? 'selected' : '' }}>Dinas Perhubungan</option>
                            </select>
                            @error('skpd')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kabupaten/Kota</label>
                            <select name="kab_kota" class="form-control" required>
                                <option value="">— Pilih Kabupaten/Kota —</option>
                                <option value="Kabupaten Agam" {{ old('kab_kota') == 'Kabupaten Agam' ? 'selected' : '' }}>Kabupaten Agam</option>
                                <option value="Kabupaten Dharmasraya" {{ old('kab_kota') == 'Kabupaten Dharmasraya' ? 'selected' : '' }}>Kabupaten Dharmasraya</option>
                                <option value="Kabupaten Kepulauan Mentawai" {{ old('kab_kota') == 'Kabupaten Kepulauan Mentawai' ? 'selected' : '' }}>Kabupaten Kepulauan Mentawai</option>
                                <option value="Kabupaten Lima Puluh Kota" {{ old('kab_kota') == 'Kabupaten Lima Puluh Kota' ? 'selected' : '' }}>Kabupaten Lima Puluh Kota</option>
                                <option value="Kabupaten Padang Pariaman" {{ old('kab_kota') == 'Kabupaten Padang Pariaman' ? 'selected' : '' }}>Kabupaten Padang Pariaman</option>
                                <option value="Kabupaten Pasaman" {{ old('kab_kota') == 'Kabupaten Pasaman' ? 'selected' : '' }}>Kabupaten Pasaman</option>
                                <option value="Kabupaten Pasaman Barat" {{ old('kab_kota') == 'Kabupaten Pasaman Barat' ? 'selected' : '' }}>Kabupaten Pasaman Barat</option>
                                <option value="Kabupaten Pesisir Selatan" {{ old('kab_kota') == 'Kabupaten Pesisir Selatan' ? 'selected' : '' }}>Kabupaten Pesisir Selatan</option>
                                <option value="Kabupaten Sijunjung" {{ old('kab_kota') == 'Kabupaten Sijunjung' ? 'selected' : '' }}>Kabupaten Sijunjung</option>
                                <option value="Kabupaten Solok" {{ old('kab_kota') == 'Kabupaten Solok' ? 'selected' : '' }}>Kabupaten Solok</option>
                                <option value="Kabupaten Solok Selatan" {{ old('kab_kota') == 'Kabupaten Solok Selatan' ? 'selected' : '' }}>Kabupaten Solok Selatan</option>
                                <option value="Kabupaten Tanah Datar" {{ old('kab_kota') == 'Kabupaten Tanah Datar' ? 'selected' : '' }}>Kabupaten Tanah Datar</option>
                                <option value="Kota Bukittinggi" {{ old('kab_kota') == 'Kota Bukittinggi' ? 'selected' : '' }}>Kota Bukittinggi</option>
                                <option value="Kota Padang" {{ old('kab_kota') == 'Kota Padang' ? 'selected' : '' }}>Kota Padang</option>
                                <option value="Kota Padang Panjang" {{ old('kab_kota') == 'Kota Padang Panjang' ? 'selected' : '' }}>Kota Padang Panjang</option>
                                <option value="Kota Pariaman" {{ old('kab_kota') == 'Kota Pariaman' ? 'selected' : '' }}>Kota Pariaman</option>
                                <option value="Kota Payakumbuh" {{ old('kab_kota') == 'Kota Payakumbuh' ? 'selected' : '' }}>Kota Payakumbuh</option>
                                <option value="Kota Sawahlunto" {{ old('kab_kota') == 'Kota Sawahlunto' ? 'selected' : '' }}>Kota Sawahlunto</option>
                                <option value="Kota Solok" {{ old('kab_kota') == 'Kota Solok' ? 'selected' : '' }}>Kota Solok</option>
                            </select>
                            @error('kab_kota')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Nama Proyek</label>
                            <input type="text" name="nama_proyek" class="form-control" value="{{ old('nama_proyek') }}" required placeholder="Nama lengkap proyek konstruksi">
                            @error('nama_proyek')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nilai Kontrak (Rp)</label>
                            <input type="text" name="nilai_kontrak" class="form-control" style="text-align: left;" value="{{ old('nilai_kontrak') }}" required placeholder="0" oninput="formatRupiah(this)">
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
                            <span class="section-title"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kontraktor</label>
                            <select name="kontraktor_id" class="form-control">
                                <option value="">— Pilih Kontraktor —</option>
                                @foreach($kontraktors as $k)
                                <option value="{{ $k->id }}" {{ old('kontraktor_id')==$k->id?'selected':'' }}>
                                    {{ $k->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('kontraktor_id')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konsultan Pengawas</label>
                            <select name="konsultan_id" class="form-control">
                                <option value="">— Pilih Konsultan —</option>
                                @foreach($konsultans as $k)
                                <option value="{{ $k->id }}" {{ old('konsultan_id')==$k->id?'selected':'' }}>
                                    {{ $k->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('konsultan_id')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">PPTK</label>
                            <select name="pptk_id" class="form-control">
                                <option value="">— Pilih PPTK —</option>
                                @foreach($pptks as $p)
                                <option value="{{ $p->id }}" {{ old('pptk_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                            @error('pptk_id')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Deskripsi Proyek (Opsional)</label>
                            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
                        </div>
                    </div>
                    <div style="display:flex; gap:10px; margin-top:8px; padding-top:16px; border-top:1px solid var(--border);">
                        <button type="submit" class="btn-primary">Simpan Proyek</button>
                        <a href="{{ route('ppk.proyeks.index') }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
function formatRupiah(element) {
    let value = element.value.replace(/[^,\d]/g, '').toString();
    let split = value.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    
    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    element.value = rupiah;
}
</script>

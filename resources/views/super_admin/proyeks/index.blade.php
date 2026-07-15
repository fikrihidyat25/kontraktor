<x-app-layout>
    <x-slot name="title">Master Proyek</x-slot>

    <div class="page-header">
        <h1>Master Data Proyek</h1>
    </div>

    <div class="content-area">
        <div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
            <a href="{{ route('admin.proyeks.create') }}" class="btn-primary" style="text-decoration:none;">+ Tambah Proyek Baru</a>
        </div>

        <div style="border:1px solid var(--border); overflow:hidden;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Proyek</th>
                        <th>Lokasi</th>
                        <th class="num">Nilai Kontrak (Rp)</th>
                        <th>Durasi</th>
                        <th>Kontraktor</th>
                        <th>Status</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proyeks as $p)
                    <tr>
                        <td style="color:var(--text-muted);">{{ $loop->iteration }}</td>
                        <td>
                            <div style="font-weight:600; font-size:12px;">{{ $p->nama_proyek }}</div>
                            @if($p->nomor_kontrak)
                            <div style="font-size:10px; color:var(--text-muted);">{{ $p->nomor_kontrak }}</div>
                            @endif
                        </td>
                        <td style="font-size:12px;">{{ $p->lokasi }}</td>
                        <td class="num" style="font-size:12px;">{{ number_format($p->nilai_kontrak, 0, ',', '.') }}</td>
                        <td style="font-size:12px;">
                            {{ $p->tanggal_mulai->format('d/m/Y') }}<br>
                            <span style="color:var(--text-muted);">s/d {{ $p->tanggal_selesai->format('d/m/Y') }}</span>
                        </td>
                        <td style="font-size:12px;">{{ $p->kontraktor->name ?? '-' }}</td>
                        <td><span class="badge {{ $p->status === 'aktif' ? 'badge-approved' : 'badge-draft' }}">{{ ucfirst($p->status) }}</span></td>
                        <td style="text-align:center;">
                            <form method="POST" action="{{ route('admin.proyeks.destroy', $p) }}" onsubmit="return confirm('Hapus proyek {{ $p->nama_proyek }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center; padding:32px; color:var(--text-muted);">Belum ada proyek terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:12px;">{{ $proyeks->links() }}</div>
    </div>
</x-app-layout>

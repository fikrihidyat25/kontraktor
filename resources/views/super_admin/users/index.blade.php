<x-app-layout>
    <x-slot name="title">Manajemen User</x-slot>

    <div class="page-header">
        <h1>Manajemen Pengguna Sistem</h1>
    </div>

    <div class="content-area">
        <div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
            <a href="{{ route('admin.users.create') }}" class="btn-primary" style="text-decoration:none;">+ Tambah Pengguna Baru</a>
        </div>

        <div style="border:1px solid var(--border); overflow:hidden;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Peran / Role</th>
                        <th>Terdaftar</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td style="color:var(--text-muted);">{{ $loop->iteration }}</td>
                        <td style="font-weight:500;">{{ $u->name }}</td>
                        <td style="color:var(--text-muted);">{{ $u->email }}</td>
                        <td><span class="badge badge-submitted">{{ $u->role_label }}</span></td>
                        <td style="color:var(--text-muted);">{{ $u->created_at->format('d/m/Y') }}</td>
                        <td style="text-align:center;">
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus pengguna {{ $u->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center; padding:32px; color:var(--text-muted);">Belum ada pengguna terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:12px;">{{ $users->links() }}</div>
    </div>
</x-app-layout>

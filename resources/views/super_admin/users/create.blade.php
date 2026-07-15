<x-app-layout>
    <x-slot name="title">Tambah Pengguna</x-slot>

    <div class="page-header">
        <h1>Tambah Pengguna Baru</h1>
    </div>

    <div class="content-area">
        <div style="max-width:520px;">
            <div style="background:#fff; border:1px solid var(--border); padding:28px;">
                <span class="section-title">Data Akun</span>
                <form method="POST" action="{{ route('admin.users.store') }}" style="margin-top:20px;">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="PT / Nama Konsultan / Nama PPK">
                        @error('name')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="email@domain.com">
                        @error('email')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Peran / Role</label>
                        <select name="role" class="form-control" required>
                            <option value="">— Pilih Peran —</option>
                            <option value="kontraktor" {{ old('role')=='kontraktor'?'selected':'' }}>Kontraktor</option>
                            <option value="konsultan" {{ old('role')=='konsultan'?'selected':'' }}>Konsultan Pengawas</option>
                            <option value="ppk" {{ old('role')=='ppk'?'selected':'' }}>PPK (Klien)</option>
                        </select>
                        @error('role')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimal 8 karakter">
                        @error('password')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div style="display:flex; gap:10px; margin-top:8px;">
                        <button type="submit" class="btn-primary">Simpan Pengguna</button>
                        <a href="{{ route('admin.users.index') }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

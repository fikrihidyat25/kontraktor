<x-app-layout>
    <x-slot name="title">Tambah Pengguna</x-slot>

    <div class="page-header" style="text-align: center;">
        <h1>Tambah Pengguna Baru</h1>
    </div>

    <div class="content-area" style="display: flex; justify-content: center;">
        <div style="max-width:520px; width: 100%;">
            <div style="background:#fff; border:1px solid var(--border); padding:28px; border-radius: 8px;">
                <span class="section-title">Data Akun</span>
                <form method="POST" action="{{ route('ppk.users.store') }}" style="margin-top:20px;" x-data="{ role: '{{ old('role') }}' }">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Peran / Role</label>
                        <select name="role" x-model="role" class="form-control" required>
                            <option value="">— Pilih Peran —</option>
                            <option value="kontraktor" {{ old('role')=='kontraktor'?'selected':'' }}>Kontraktor</option>
                            <option value="konsultan" {{ old('role')=='konsultan'?'selected':'' }}>Konsultan Pengawas</option>
                            <option value="pptk" {{ old('role')=='pptk'?'selected':'' }}>PPTK</option>
                        </select>
                        @error('role')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required maxlength="255" placeholder="PT / Nama Konsultan / Nama PPK">
                        @error('name')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="form-group" x-show="role === 'kontraktor' || role === 'konsultan'" style="display: none;">
                        <label class="form-label">Nama Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab" class="form-control" value="{{ old('penanggung_jawab') }}">
                        @error('penanggung_jawab')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="email@domain.com">
                        @error('email')<div style="font-size:11px; color:#C62828; margin-top:4px;">{{ $message }}</div>@enderror
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
                        <a href="{{ route('ppk.users.index') }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

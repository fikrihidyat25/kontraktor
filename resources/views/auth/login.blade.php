<x-guest-layout>
    <h2>Otentikasi Pengguna</h2>

    <!-- Session Status -->
    @if(session('status'))
        <div style="background:#E8F5E9; border-left:4px solid #4CAF50; padding:12px; font-size:12px; color:#1B5E20; margin-bottom:16px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">Alamat Email</label>
            <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Kata Sandi</label>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="checkbox-container">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me">Ingat Sesi Saya</label>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ url('/') }}" class="btn-primary" style="background-color: #f1f5f9; color: #475569; text-align: center; text-decoration: none; border: 1px solid #cbd5e1; flex: 1;">Kembali</a>
            <button type="submit" class="btn-primary" style="flex: 1;">Masuk ke Sistem</button>
        </div>


    </form>
</x-guest-layout>

<section>
    <header class="mb-6">
        <h2 class="text-lg font-bold text-[#0F172B]">Informasi Profil</h2>
        <p class="mt-1 text-sm text-gray-500">
            Perbarui informasi profil dan alamat email akun Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6 max-w-xl">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
            <input id="name" name="name" type="text" class="w-full px-4 py-2 bg-white border border-[#E5E7EB] rounded-sm focus:outline-none focus:border-[#1E3A8A] focus:ring-1 focus:ring-[#1E3A8A] transition-colors" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
            <input id="email" name="email" type="email" class="w-full px-4 py-2 bg-white border border-[#E5E7EB] rounded-sm focus:outline-none focus:border-[#1E3A8A] focus:ring-1 focus:ring-[#1E3A8A] transition-colors" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3">
                    <p class="text-sm text-gray-800">
                        Email Anda belum diverifikasi.
                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1E3A8A]">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            Tautan verifikasi baru telah dikirim ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-[#E5E7EB]">
            <button type="submit" class="bg-[#1E3A8A] text-[#FFB800] text-sm font-bold px-6 py-2.5 rounded-sm border border-[#1E3A8A] hover:bg-[#152e70] transition-colors">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 font-bold">
                    ✓ Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>

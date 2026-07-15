<section>
    <header class="mb-6">
        <h2 class="text-lg font-bold text-[#0F172B]">Perbarui Kata Sandi</h2>
        <p class="mt-1 text-sm text-gray-500">
            Pastikan akun Anda menggunakan kata sandi yang panjang, acak, dan aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6 max-w-xl">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi Saat Ini</label>
            <input id="current_password" name="current_password" type="password" class="w-full px-4 py-2 bg-white border border-[#E5E7EB] rounded-sm focus:outline-none focus:border-[#1E3A8A] focus:ring-1 focus:ring-[#1E3A8A] transition-colors" autocomplete="current-password">
            @error('current_password', 'updatePassword') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi Baru</label>
            <input id="password" name="password" type="password" class="w-full px-4 py-2 bg-white border border-[#E5E7EB] rounded-sm focus:outline-none focus:border-[#1E3A8A] focus:ring-1 focus:ring-[#1E3A8A] transition-colors" autocomplete="new-password">
            @error('password', 'updatePassword') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Kata Sandi</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="w-full px-4 py-2 bg-white border border-[#E5E7EB] rounded-sm focus:outline-none focus:border-[#1E3A8A] focus:ring-1 focus:ring-[#1E3A8A] transition-colors" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-[#E5E7EB]">
            <button type="submit" class="bg-[#1E3A8A] text-[#FFB800] text-sm font-bold px-6 py-2.5 rounded-sm border border-[#1E3A8A] hover:bg-[#152e70] transition-colors">
                Perbarui Sandi
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 font-bold">
                    ✓ Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>

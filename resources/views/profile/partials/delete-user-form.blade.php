<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-red-600">Hapus Akun</h2>
        <p class="mt-1 text-sm text-gray-500">
            Sekali akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen.
        </p>
    </header>

    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="bg-red-600 text-white text-sm font-bold px-6 py-2.5 rounded-sm border border-red-600 hover:bg-red-700 transition-colors">
        Hapus Akun
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-gray-900 mb-4">
                Apakah Anda yakin ingin menghapus akun?
            </h2>

            <p class="text-sm text-gray-600 mb-6">
                Sekali akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Silakan masukkan kata sandi Anda untuk mengonfirmasi penghapusan secara permanen.
            </p>

            <div class="mt-6">
                <label for="password_delete" class="sr-only">Kata Sandi</label>
                <input id="password_delete" name="password" type="password" class="w-full md:w-3/4 px-4 py-2 bg-white border border-[#E5E7EB] rounded-sm focus:outline-none focus:border-red-600 focus:ring-1 focus:ring-red-600 transition-colors" placeholder="Masukkan kata sandi" />
                @error('password', 'userDeletion') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="bg-white text-gray-700 text-sm font-bold px-6 py-2.5 rounded-sm border border-gray-300 hover:bg-gray-50 transition-colors" x-on:click="$dispatch('close')">
                    Batal
                </button>

                <button type="submit" class="bg-red-600 text-white text-sm font-bold px-6 py-2.5 rounded-sm border border-red-600 hover:bg-red-700 transition-colors">
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>

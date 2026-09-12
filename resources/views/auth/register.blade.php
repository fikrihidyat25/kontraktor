<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" x-data="{ role: '{{ old('role', 'kontraktor') }}' }">
        @csrf

        <!-- Role -->
        <div>
            <x-input-label for="role" :value="__('Daftar Sebagai (Role)')" />
            <select id="role" name="role" x-model="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required autofocus>
                <option value="kontraktor">Kontraktor</option>
                <option value="konsultan">Konsultan Pengawas</option>
                <option value="ppk">PPK (SKPD)</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Name -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Penanggung Jawab (Kontraktor & Konsultan only) -->
        <div class="mt-4" x-show="role === 'kontraktor' || role === 'konsultan'" style="display: none;">
            <x-input-label for="penanggung_jawab" :value="__('Nama Penanggung Jawab')" />
            <x-text-input id="penanggung_jawab" class="block mt-1 w-full" type="text" name="penanggung_jawab" :value="old('penanggung_jawab')" />
            <x-input-error :messages="$errors->get('penanggung_jawab')" class="mt-2" />
        </div>

        <!-- Kab/Kota (PPK only) -->
        <div class="mt-4" x-show="role === 'ppk'" style="display: none;">
            <x-input-label for="kab_kota" :value="__('Kabupaten / Kota')" />
            <x-text-input id="kab_kota" class="block mt-1 w-full" type="text" name="kab_kota" :value="old('kab_kota')" placeholder="Contoh: Kota Makassar" />
            <x-input-error :messages="$errors->get('kab_kota')" class="mt-2" />
        </div>

        <!-- SKPD (PPK only) -->
        <div class="mt-4" x-show="role === 'ppk'" style="display: none;">
            <x-input-label for="skpd" :value="__('Instansi / SKPD')" />
            <x-text-input id="skpd" class="block mt-1 w-full" type="text" name="skpd" :value="old('skpd')" placeholder="Contoh: Dinas PUPR" />
            <x-input-error :messages="$errors->get('skpd')" class="mt-2" />
        </div>


        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

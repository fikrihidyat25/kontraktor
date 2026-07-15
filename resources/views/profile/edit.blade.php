<x-app-layout>
    <x-slot name="title">Pengaturan Profil</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans">
        <!-- HEADER -->
        <div class="mb-8 border-b border-[#E5E7EB] pb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Pengaturan Profil</h1>
        </div>

        <div class="flex flex-col gap-8">
            <!-- Update Profile Info -->
            <div class="bg-white rounded-sm shadow-sm border border-[#E5E7EB] p-6">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- Update Password -->
            <div class="bg-white rounded-sm shadow-sm border border-[#E5E7EB] p-6">
                @include('profile.partials.update-password-form')
            </div>

            <!-- Delete Account -->
            <div class="bg-white rounded-sm shadow-sm border border-red-200 p-6 border-l-4 border-l-red-600">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>

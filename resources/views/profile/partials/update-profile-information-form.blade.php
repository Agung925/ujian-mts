<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- Form update profil — enctype multipart karena ada upload foto --}}
    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('patch')

        {{-- Bagian Foto Profil (hanya guru dan super_admin) --}}
        @if(in_array($user->role, ['guru', 'super_admin']))
        <div x-data="{ preview: '{{ $user->foto ? Storage::url($user->foto) : '' }}' }">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto Profil</label>
            <div class="flex items-center gap-4">
                {{-- Preview foto / inisial --}}
                <div class="w-16 h-16 rounded-full overflow-hidden bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center shrink-0">
                    <template x-if="preview">
                        <img :src="preview" class="w-16 h-16 object-cover rounded-full" alt="Foto profil">
                    </template>
                    <template x-if="!preview">
                        <span class="text-white font-bold text-xl">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </template>
                </div>
                {{-- Input file --}}
                <div class="flex-1">
                    <input type="file" name="foto" accept="image/jpg,image/jpeg,image/png,image/webp"
                           class="w-full text-sm text-gray-600 dark:text-gray-400 dark:text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700 file:text-xs file:font-semibold hover:file:bg-green-100 cursor-pointer"
                           @change="
                               const file = $event.target.files[0];
                               if (file) preview = URL.createObjectURL(file);
                           ">
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">JPG, PNG, WebP — maks. 2 MB</p>
                    @error('foto')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        @else
        {{-- Siswa: tampilkan foto jika ada, tapi tidak bisa upload --}}
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full overflow-hidden bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center shrink-0">
                @if($user->foto)
                    <img src="{{ Storage::url($user->foto) }}" class="w-16 h-16 object-cover rounded-full" alt="Foto profil">
                @else
                    <span class="text-white font-bold text-xl">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                @endif
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500">Foto profil hanya bisa diubah oleh admin.</p>
        </div>
        @endif

        {{-- Nama --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 dark:text-gray-500">
                    Email belum terverifikasi.
                    <button form="send-verification" class="underline text-green-600 hover:text-green-800">
                        Kirim ulang verifikasi
                    </button>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 text-green-600 text-xs">Link verifikasi telah dikirim.</p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</section>

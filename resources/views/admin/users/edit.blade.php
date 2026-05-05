@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <main class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow border border-gray-200 p-6">

            {{-- Info User --}}
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                {{-- Foto profil atau inisial --}}
                <div class="w-14 h-14 rounded-full overflow-hidden bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center shrink-0">
                    @if($user->foto)
                        <img src="{{ Storage::url($user->foto) }}" class="w-14 h-14 object-cover" alt="Foto {{ $user->name }}">
                    @else
                        <span class="text-white font-bold text-lg">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    @endif
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Edit: {{ $user->name }}</h1>
                    <p class="text-sm text-gray-500">
                        {{ $user->nis ? 'NIS: ' . $user->nis : $user->email }}
                        &mdash;
                        @php
                            $roleLabel = match($user->role) {
                                'super_admin' => 'Super Admin',
                                'guru' => 'Guru',
                                'siswa' => 'Siswa',
                                default => $user->role,
                            };
                        @endphp
                        <span class="font-medium">{{ $roleLabel }}</span>
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}"
                x-data="{ isAktif: {{ $user->is_aktif ? 'true' : 'false' }} }">
                @csrf
                @method('PUT')

                {{-- Nama --}}
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        placeholder="Masukkan nama lengkap" required
                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status Aktif (Toggle) --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Akun</label>
                    <div class="flex items-center gap-3">
                        {{-- Input hidden sebagai fallback nilai false --}}
                        <input type="hidden" name="is_aktif" value="0">
                        <button type="button" @click="isAktif = !isAktif"
                            :class="isAktif ? 'bg-primary-600' : 'bg-gray-300'"
                            class="relative inline-flex w-11 h-6 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1">
                            <span :class="isAktif ? 'translate-x-5' : 'translate-x-1'"
                                class="inline-block w-4 h-4 mt-1 bg-white rounded-full shadow transition-transform duration-200"></span>
                            {{-- Field aktual yang dikirim form --}}
                            <input type="checkbox" name="is_aktif" value="1" x-model="isAktif" class="sr-only">
                        </button>
                        <span x-text="isAktif ? 'Aktif' : 'Non-Aktif'"
                            :class="isAktif ? 'text-green-600 font-medium' : 'text-red-500 font-medium'"
                            class="text-sm"></span>
                    </div>
                </div>

                {{-- Jenis Kelamin --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="L"
                                {{ old('jenis_kelamin', $user->jenis_kelamin) === 'L' ? 'checked' : '' }}
                                class="text-primary-600 focus:ring-primary-500">
                            <span class="text-sm text-gray-700">Laki-laki</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="P"
                                {{ old('jenis_kelamin', $user->jenis_kelamin) === 'P' ? 'checked' : '' }}
                                class="text-primary-600 focus:ring-primary-500">
                            <span class="text-sm text-gray-700">Perempuan</span>
                        </label>
                    </div>
                    @error('jenis_kelamin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- No. Telepon --}}
                <div class="mb-4">
                    <label for="no_telp" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" id="no_telp" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}"
                        placeholder="08xxxxxxxxxx" maxlength="15"
                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('no_telp') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    @error('no_telp')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ganti Password (opsional) --}}
                <div class="border-t border-gray-100 pt-4 mb-6" x-data="{ show: false }">
                    <p class="text-sm text-gray-500 mb-3">Kosongkan jika tidak ingin mengganti password.</p>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" id="password" name="password"
                            placeholder="Minimal 6 karakter (opsional)"
                            class="w-full border rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 rounded-lg transition">
                        Update
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-lg transition">
                        Batal
                    </a>
                </div>
            </form>

            {{-- Section Upload Foto (Super Admin) --}}
            <div class="mt-6 pt-6 border-t border-gray-100" x-data="{ preview: '{{ $user->foto ? Storage::url($user->foto) : '' }}' }">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Upload Foto Profil</h3>
                <div class="flex items-center gap-4 mb-3">
                    {{-- Preview foto terkini --}}
                    <div class="w-14 h-14 rounded-full overflow-hidden bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center shrink-0">
                        <template x-if="preview">
                            <img :src="preview" class="w-14 h-14 object-cover rounded-full" alt="Preview">
                        </template>
                        <template x-if="!preview">
                            <span class="text-white font-bold text-lg">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </template>
                    </div>
                    <p class="text-xs text-gray-400">JPG, PNG, WebP — maks. 2 MB</p>
                </div>
                <form method="POST" action="{{ route('admin.users.upload-foto', $user) }}" enctype="multipart/form-data"
                      class="flex items-center gap-3">
                    @csrf
                    <input type="file" name="foto" accept="image/jpg,image/jpeg,image/png,image/webp" required
                           class="flex-1 text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700 file:text-xs file:font-semibold hover:file:bg-green-100 cursor-pointer"
                           @change="
                               const file = $event.target.files[0];
                               if (file) preview = URL.createObjectURL(file);
                           ">
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition shrink-0">
                        Upload Foto
                    </button>
                </form>
                @if(session('success') && str_contains(session('success'), 'Foto'))
                    <p class="text-green-600 text-xs mt-2">{{ session('success') }}</p>
                @endif
            </div>

        </div>
    </main>
@endsection

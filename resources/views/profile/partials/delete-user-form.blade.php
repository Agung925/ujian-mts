<section class="space-y-4">
    <p class="text-sm text-gray-600">
        Setelah akun dihapus, semua data akan hilang permanen. Pastikan tidak ada data penting sebelum melanjutkan.
    </p>

    {{-- Tombol trigger modal --}}
    <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
        Hapus Akun Ini
    </button>

    {{-- Modal konfirmasi --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h3 class="text-lg font-semibold text-gray-900 mb-2">Yakin ingin menghapus akun?</h3>
            <p class="text-sm text-gray-600 mb-4">Tindakan ini tidak dapat dibatalkan. Masukkan password untuk konfirmasi.</p>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input id="password" name="password" type="password"
                       placeholder="Masukkan password Anda"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                @if($errors->userDeletion->has('password'))
                    <p class="text-red-500 text-xs mt-1">{{ $errors->userDeletion->first('password') }}</p>
                @endif
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" x-on:click="$dispatch('close')"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Batal
                </button>
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Ya, Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>

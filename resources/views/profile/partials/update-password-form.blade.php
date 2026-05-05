<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password Saat Ini <span class="text-red-500">*</span></label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                   class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 {{ $errors->updatePassword->has('current_password') ? 'border-red-400 bg-red-50' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}">
            @if($errors->updatePassword->has('current_password'))
                <p class="text-red-500 text-xs mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password Baru <span class="text-red-500">*</span></label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                   class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 {{ $errors->updatePassword->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}">
            @if($errors->updatePassword->has('password'))
                <p class="text-red-500 text-xs mt-1">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                   class="w-full border rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 {{ $errors->updatePassword->has('password_confirmation') ? 'border-red-400 bg-red-50' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}">
            @if($errors->updatePassword->has('password_confirmation'))
                <p class="text-red-500 text-xs mt-1">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                Perbarui Password
            </button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                   class="text-sm text-green-600">Password berhasil diperbarui.</p>
            @endif
        </div>
    </form>
</section>

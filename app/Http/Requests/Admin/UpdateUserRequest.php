<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /** Hanya super_admin yang boleh akses */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    /** Aturan validasi saat update user */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name'          => 'required|string|max:255',
            'email'         => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'nis'           => ['nullable', 'string', 'max:20', Rule::unique('users', 'nis')->ignore($userId)],
            'password'      => 'nullable|string|min:6',
            'role'          => 'required|in:super_admin,guru,siswa',
            'is_aktif'      => 'boolean',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_telp'       => 'nullable|string|max:15',
        ];
    }

    /** Pesan error kustom */
    public function messages(): array
    {
        return [
            'role.in'          => 'Role harus salah satu dari: super_admin, guru, siswa.',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
        ];
    }
}

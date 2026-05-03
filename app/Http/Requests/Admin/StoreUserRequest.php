<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /** Hanya super_admin yang boleh akses */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    /** Aturan validasi saat tambah user baru */
    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|max:255|unique:users,email',
            'nis'           => 'nullable|string|max:20|unique:users,nis',
            'password'      => 'required|string|min:6',
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

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan form profil user yang sedang login.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Simpan perubahan informasi profil.
     * Guru dan super_admin bisa upload foto profil mereka sendiri.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->only(['name', 'email']);

        // Proses upload foto jika ada file yang dikirim
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            // Simpan foto baru di storage/app/public/fotos
            $data['foto'] = $request->file('foto')->store('fotos', 'public');
        }

        $user->fill($data);

        // Reset verifikasi email jika email berubah
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Hapus akun — HANYA boleh diakses oleh super_admin.
     * Guru dan siswa tidak bisa menghapus akun sendiri.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Pastikan hanya super_admin yang bisa mengakses ini
        if ($request->user()->role !== 'super_admin') {
            abort(403, 'Hanya Super Admin yang dapat menghapus akun.');
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Imports\SiswaImport;
use App\Exports\TemplateSiswaExport;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /** Tampilkan daftar semua user */
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('name', 'ilike', "%{$request->search}%")
                        ->orWhere('email', 'ilike', "%{$request->search}%")
                        ->orWhere('nis', 'ilike', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /** Form tambah user baru */
    public function create()
    {
        return view('admin.users.create');
    }

    /** Simpan user baru */
    public function store(StoreUserRequest $request)
    {
        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'nis'           => $request->nis,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'is_aktif'      => $request->boolean('is_aktif', true),
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_telp'       => $request->no_telp,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /** Tampilkan detail user */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /** Form edit user */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /** Simpan perubahan user */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->only(['name', 'email', 'nis', 'role', 'jenis_kelamin', 'no_telp']);
        $data['is_aktif'] = $request->boolean('is_aktif', true);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /** Hapus user */
    public function destroy(User $user): RedirectResponse
    {
        $user->deleteOrFail();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /** Import data siswa dari file Excel */
    public function importSiswa(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        $import = new SiswaImport;
        Excel::import($import, $request->file('file'));

        // Tampilkan feedback jumlah baris berhasil dan dilewati
        $msg = "Import selesai: {$import->importedCount} siswa berhasil ditambahkan";
        if ($import->skippedCount > 0) {
            $msg .= ", {$import->skippedCount} baris dilewati (NIS duplikat atau data tidak valid)";
        }

        return redirect()->route('admin.users.index')->with('success', $msg);
    }

    /** Download template Excel untuk import siswa */
    public function downloadTemplate()
    {
        return Excel::download(new TemplateSiswaExport, 'template_import_siswa.xlsx');
    }
}

<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya guru dan super_admin yang boleh mengubah soal
        return Auth::check() && in_array(Auth::user()->role, ['guru', 'super_admin']);
    }

    public function rules(): array
    {
        // Ambil tipe soal dari soal yang sedang diedit (tidak bisa diubah)
        $tipeSoal = $this->route('bankSoal')?->tipe_soal;

        $rules = [
            'mata_pelajaran_id'  => 'required|exists:mata_pelajaran,id',
            'pertanyaan'         => 'required|string|min:10',
            'tingkat_kesulitan'  => 'required|in:mudah,sedang,sulit',
            'bobot_nilai'        => 'required|integer|min:1|max:100',
            'gambar'             => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];

        // Validasi pilihan jawaban berdasarkan tipe soal yang ada
        if ($tipeSoal === 'pg') {
            $rules['pilihan']        = 'required|array|min:2|max:5';
            $rules['pilihan.*.teks'] = 'required|string|min:1';
            $rules['jawaban_benar']  = 'required|integer|min:0';
        }

        if ($tipeSoal === 'bs') {
            $rules['jawaban_bs'] = 'required|in:benar,salah';
        }

        if ($tipeSoal === 'essay') {
            $rules['kunci_essay'] = 'nullable|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'mata_pelajaran_id.exists'   => 'Mata pelajaran tidak ditemukan.',
            'pertanyaan.required'        => 'Teks pertanyaan wajib diisi.',
            'pertanyaan.min'             => 'Pertanyaan minimal 10 karakter.',
            'tingkat_kesulitan.required' => 'Tingkat kesulitan wajib dipilih.',
            'bobot_nilai.required'       => 'Bobot nilai wajib diisi.',
            'bobot_nilai.min'            => 'Bobot nilai minimal 1.',
            'bobot_nilai.max'            => 'Bobot nilai maksimal 100.',
            'gambar.image'               => 'File harus berupa gambar.',
            'gambar.max'                 => 'Ukuran gambar maksimal 2MB.',
            'pilihan.required'           => 'Pilihan jawaban wajib diisi.',
            'pilihan.min'                => 'Minimal 2 pilihan jawaban harus diisi.',
            'jawaban_benar.required'     => 'Jawaban yang benar wajib dipilih.',
            'jawaban_bs.required'        => 'Pilih apakah pernyataan ini Benar atau Salah.',
        ];
    }
}

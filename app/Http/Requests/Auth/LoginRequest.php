<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'identifier' bisa berupa NIS atau Email
            'identifier' => ['required', 'string'],
            'password'   => ['required', 'string'],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia
     */
    public function messages(): array
    {
        return [
            'identifier.required' => 'NIS atau Email wajib diisi.',
            'password.required'   => 'Password wajib diisi.',
        ];
    }

    /**
     * Proses autentikasi — deteksi otomatis NIS atau Email
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = $this->input('identifier');
        $password   = $this->input('password');

        // Deteksi apakah input adalah email atau NIS
        // Jika mengandung '@' maka dianggap email, selainnya dianggap NIS
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            // Login menggunakan email (untuk guru & super_admin)
            $credentials = [
                'email'    => $identifier,
                'password' => $password,
                // 'is_aktif' => true,
            ];
        } else {
            // Login menggunakan NIS (untuk siswa)
            $credentials = [
                'nis'      => $identifier,
                'password' => $password,
                // 'is_aktif' => true,
            ];
        }

        // Coba autentikasi
        if (!Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => 'NIS/Email atau password salah.',
            ]);
        }

        // Cek manual apakah akun aktif (menghindari mismatch boolean PostgreSQL)
        if (!Auth::user()->is_aktif) {
            Auth::logout();

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => 'Akun kamu dinonaktifkan. Hubungi administrator.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identifier' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->input('identifier')) . '|' . $this->ip()
        );
    }
}
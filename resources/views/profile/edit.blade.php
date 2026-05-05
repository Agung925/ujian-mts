@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8 space-y-6">

    {{-- Flash Message --}}
    @if(session('status') === 'profile-updated')
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Profil berhasil diperbarui.
        <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">&times;</button>
    </div>
    @endif

    {{-- Kartu Informasi Profil --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-5">Informasi Profil</h2>
        @include('profile.partials.update-profile-information-form')
    </div>

    {{-- Kartu Ganti Password --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-5">Ganti Password</h2>
        @include('profile.partials.update-password-form')
    </div>

    {{-- Kartu Hapus Akun — HANYA untuk super_admin --}}
    @if(Auth::user()->role === 'super_admin')
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-red-200 p-6">
        <h2 class="text-lg font-semibold text-red-700 mb-5">Hapus Akun</h2>
        @include('profile.partials.delete-user-form')
    </div>
    @endif

</div>
@endsection

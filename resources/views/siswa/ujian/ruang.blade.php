<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $sesi->ujian->judul }} — Ujian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Anti-cheat: nonaktifkan seleksi teks pada konten soal */
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-700 min-h-screen"
      x-data="ruangUjian({{ $sesi->id }}, {{ $sesi->sisa_waktu_detik }})"
      @contextmenu.prevent
      @copy.prevent
      @cut.prevent
      @keydown.window="cegahShortcut($event)">

    {{-- ============================================================
         HEADER: Judul + Countdown Timer
    ============================================================ --}}
    <div class="bg-green-600 text-white px-6 py-3 flex items-center justify-between sticky top-0 z-40 shadow">
        <div>
            <p class="font-bold text-sm leading-tight">{{ $sesi->ujian->judul }}</p>
            <p class="text-xs text-green-100">{{ $sesi->ujian->mataPelajaran->nama_mapel ?? '' }}</p>
        </div>
        <div class="text-center">
            <p class="text-xs text-green-200">Sisa Waktu</p>
            <p class="text-2xl font-mono font-bold" :class="sisaDetik <= 300 ? 'text-red-300 animate-pulse' : ''"
               x-text="formatWaktu(sisaDetik)"></p>
        </div>
        <div class="text-right">
            <p class="text-xs text-green-200">Terjawab</p>
            <p class="text-lg font-bold" x-text="terjawab + ' / {{ $soalUrut->count() }}'"></p>
        </div>
    </div>

    {{-- ============================================================
         KONTEN: Soal + Navigasi
    ============================================================ --}}
    <div class="max-w-3xl mx-auto px-4 py-6">

        {{-- Navigasi nomor soal --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 p-4 mb-5">
            <p class="text-xs font-medium text-gray-500 mb-2">Navigasi Soal:</p>
            <div class="flex flex-wrap gap-2">
                @foreach($soalUrut as $i => $soal)
                <button @click="pindahSoal({{ $i }})"
                        :class="soalAktif === {{ $i }}
                            ? 'bg-green-600 text-white'
                            : (jawaban[{{ $soal->id }}] ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200')"
                        class="w-8 h-8 rounded-lg text-xs font-semibold transition">
                    {{ $i + 1 }}
                </button>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-2">
                🟩 Terjawab &nbsp; ⬜ Belum dijawab &nbsp; 🟦 Soal aktif
            </p>
        </div>

        {{-- Soal satu per satu --}}
        @foreach($soalUrut as $i => $soal)
        @php
            $jawabanAda = $sesi->jawabanSiswa->firstWhere('soal_id', $soal->id);
        @endphp
        <div x-show="soalAktif === {{ $i }}" x-cloak
             class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 p-6 mb-4">

            {{-- Nomor & tipe soal --}}
            <div class="flex items-center gap-2 mb-4">
                <span class="text-sm font-bold text-gray-500">Soal {{ $i + 1 }} / {{ $soalUrut->count() }}</span>
                @if($soal->tipe_soal === 'pg')
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">PG</span>
                @elseif($soal->tipe_soal === 'bs')
                    <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">Benar/Salah</span>
                @else
                    <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Essay</span>
                @endif
                <span class="ml-auto text-xs text-gray-400">Bobot: {{ $soal->pivot->bobot_nilai }}</span>
            </div>

            {{-- Teks pertanyaan (no-select agar tidak bisa dicopy) --}}
            <div class="text-gray-800 dark:text-gray-100 text-sm leading-relaxed mb-5 no-select">
                {!! nl2br(e($soal->pertanyaan)) !!}
            </div>

            {{-- Pilihan jawaban: PG & BS --}}
            @if(in_array($soal->tipe_soal, ['pg', 'bs']))
                <div class="space-y-2">
                    @foreach($soal->pilihanJawaban as $pilihan)
                    <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition no-select"
                           :class="jawaban[{{ $soal->id }}] == {{ $pilihan->id }}
                               ? 'border-green-400 bg-green-50'
                               : 'border-gray-200 hover:border-green-300 hover:bg-gray-50 dark:bg-gray-900'">
                        <input type="radio"
                               name="soal_{{ $soal->id }}"
                               value="{{ $pilihan->id }}"
                               {{ $jawabanAda?->pilihan_id == $pilihan->id ? 'checked' : '' }}
                               @change="simpanJawaban({{ $soal->id }}, {{ $pilihan->id }})"
                               x-model="jawaban[{{ $soal->id }}]"
                               class="text-green-600 focus:ring-green-500">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 w-5 shrink-0">{{ $pilihan->label }}.</span>
                        <span class="text-sm text-gray-800 dark:text-gray-100">{{ $pilihan->teks_pilihan }}</span>
                    </label>
                    @endforeach
                </div>

            {{-- Jawaban essay --}}
            @else
                <textarea name="essay_{{ $soal->id }}"
                          rows="4"
                          placeholder="Tulis jawabanmu di sini..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none resize-none"
                          @blur="simpanEssay({{ $soal->id }}, $event.target.value)">{{ $jawabanAda?->jawaban_essay }}</textarea>
            @endif

            {{-- Navigasi prev/next --}}
            <div class="flex justify-between mt-5 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="pindahSoal(soalAktif - 1)"
                        x-show="soalAktif > 0"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-300 flex items-center gap-1">
                    ← Sebelumnya
                </button>
                <span x-show="soalAktif === 0"></span>

                @if($i < $soalUrut->count() - 1)
                <button @click="pindahSoal(soalAktif + 1)"
                        class="text-sm bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg transition">
                    Selanjutnya →
                </button>
                @else
                <button @click="konfirmasiSubmit()"
                        class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg transition">
                    Kumpulkan Ujian ✓
                </button>
                @endif
            </div>
        </div>
        @endforeach

        <div class="text-center mt-4">
            <button @click="konfirmasiSubmit()"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition">
                Kumpulkan Ujian
            </button>
        </div>

    </div>

    {{-- ============================================================
         MODAL: Konfirmasi submit
    ============================================================ --}}
    <div x-show="modalSubmit" x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="text-4xl mb-3">📝</div>
            <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-2">Kumpulkan Ujian?</h3>
            <p class="text-sm text-gray-500 mb-1">
                Soal terjawab: <strong x-text="terjawab"></strong> / {{ $soalUrut->count() }}
            </p>
            <p class="text-xs text-gray-400 mb-5">Jawaban tidak bisa diubah setelah dikumpulkan.</p>
            <form id="form-submit" method="POST" action="{{ route('siswa.ujian.submit', $sesi->id) }}">
                @csrf
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg text-sm mb-2 transition">
                    Ya, Kumpulkan
                </button>
            </form>
            <button @click="modalSubmit = false"
                    class="w-full text-sm text-gray-400 hover:text-gray-600 dark:text-gray-400">
                Kembali ke Soal
            </button>
        </div>
    </div>

    {{-- ============================================================
         MODAL: Peringatan anti-cheat (pindah tab / keluar fullscreen)
    ============================================================ --}}
    <div x-show="modalPeringatan" x-cloak
         class="fixed inset-0 bg-black/80 flex items-center justify-center z-[60] p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center border-4 border-red-400">
            <div class="text-5xl mb-3">⚠️</div>
            <h3 class="font-bold text-red-600 text-lg mb-2">Peringatan!</h3>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2" x-text="pesanPeringatan"></p>
            <p class="text-sm font-semibold text-red-600 mb-1">
                Pelanggaran: <span x-text="jumlahPelanggaran"></span> / 3
            </p>
            <p class="text-xs text-gray-400 mb-5">Jika melanggar 3 kali, ujian akan otomatis dikumpulkan.</p>
            <button @click="tutupPeringatan()"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                Saya Mengerti, Lanjutkan Ujian
            </button>
        </div>
    </div>

    {{-- ============================================================
         MODAL: Auto-submit karena pelanggaran
    ============================================================ --}}
    <div x-show="modalAutoSubmit" x-cloak
         class="fixed inset-0 bg-black/90 flex items-center justify-center z-[70] p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="text-5xl mb-3">🚫</div>
            <h3 class="font-bold text-red-600 text-lg mb-2">Ujian Dikumpulkan Otomatis</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Kamu telah melanggar aturan ujian sebanyak 3 kali. Ujian dikumpulkan secara otomatis.</p>
            <p class="text-xs text-gray-400 animate-pulse">Mengalihkan...</p>
        </div>
    </div>

    <script>
    function ruangUjian(sesiId, sisaDetikAwal) {
        return {
            sesiId,
            soalAktif: 0,
            sisaDetik: sisaDetikAwal,
            modalSubmit: false,
            modalPeringatan: false,
            modalAutoSubmit: false,
            pesanPeringatan: '',
            jumlahPelanggaran: 0,

            // Jawaban dari server (sudah tersimpan sebelumnya)
            jawaban: {
                @foreach($sesi->jawabanSiswa as $j)
                {{ $j->soal_id }}: {{ $j->pilihan_id ?? 'null' }},
                @endforeach
            },

            get terjawab() {
                return Object.values(this.jawaban).filter(v => v !== null && v !== undefined).length;
            },

            init() {
                // 1. Mulai countdown timer
                const interval = setInterval(() => {
                    if (this.sisaDetik <= 0) {
                        clearInterval(interval);
                        document.getElementById('form-submit')?.submit();
                        return;
                    }
                    this.sisaDetik--;
                }, 1000);

                // 2. Anti-cheat: deteksi pindah tab / minimize window
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        this.catatPelanggaran('Kamu berpindah tab atau keluar dari halaman ujian!');
                    }
                });

                // 3. Anti-cheat: deteksi keluar dari fullscreen
                document.addEventListener('fullscreenchange', () => {
                    if (!document.fullscreenElement && this.sisaDetik > 0) {
                        this.catatPelanggaran('Kamu keluar dari mode layar penuh!');
                    }
                });

                // 4. Minta fullscreen saat ujian dimulai
                this.$nextTick(() => {
                    if (document.documentElement.requestFullscreen) {
                        document.documentElement.requestFullscreen().catch(() => {});
                    }
                });
            },

            formatWaktu(detik) {
                const jam = Math.floor(detik / 3600);
                const mnt = Math.floor((detik % 3600) / 60);
                const dtk = detik % 60;
                if (jam > 0) return `${String(jam).padStart(2,'0')}:${String(mnt).padStart(2,'0')}:${String(dtk).padStart(2,'0')}`;
                return `${String(mnt).padStart(2,'0')}:${String(dtk).padStart(2,'0')}`;
            },

            // Cegah keyboard shortcut berbahaya
            cegahShortcut(e) {
                const dilarang = [
                    e.ctrlKey && ['c','u','s','a','p'].includes(e.key.toLowerCase()),
                    e.ctrlKey && e.shiftKey && ['i','j','c'].includes(e.key.toLowerCase()),
                    e.key === 'F12',
                    e.altKey && e.key === 'Tab',
                    e.key === 'PrintScreen',
                ];
                if (dilarang.some(Boolean)) {
                    e.preventDefault();
                }
            },

            pindahSoal(index) {
                const max = {{ $soalUrut->count() - 1 }};
                if (index >= 0 && index <= max) this.soalAktif = index;
            },

            async simpanJawaban(soalId, pilihanId) {
                this.jawaban[soalId] = pilihanId;
                try {
                    await fetch(`/siswa/ujian/${this.sesiId}/jawab`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ soal_id: soalId, pilihan_id: pilihanId }),
                    });
                } catch(e) {}
            },

            async simpanEssay(soalId, teks) {
                try {
                    await fetch(`/siswa/ujian/${this.sesiId}/jawab`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ soal_id: soalId, jawaban_essay: teks }),
                    });
                } catch(e) {}
            },

            // Kirim log pelanggaran ke server
            async catatPelanggaran(pesan) {
                this.pesanPeringatan = pesan;
                this.modalPeringatan = true;

                try {
                    const res = await fetch(`/siswa/ujian/${this.sesiId}/pelanggaran`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ alasan: pesan }),
                    });
                    const data = await res.json();
                    this.jumlahPelanggaran = data.pelanggaran;

                    // Auto-submit jika server bilang harus submit
                    if (data.auto_submit) {
                        this.modalPeringatan = false;
                        this.modalAutoSubmit = true;
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2500);
                    }
                } catch(e) {}
            },

            tutupPeringatan() {
                this.modalPeringatan = false;
                // Coba minta fullscreen lagi setelah tutup peringatan
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen().catch(() => {});
                }
            },

            konfirmasiSubmit() {
                this.modalSubmit = true;
            },
        };
    }
    </script>
</body>
</html>

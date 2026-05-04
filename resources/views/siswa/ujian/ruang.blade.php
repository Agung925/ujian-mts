<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sesi->ujian->judul }} — Ujian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen"
      x-data="ruangUjian({{ $sesi->id }}, {{ $sesi->sisa_waktu_detik }})">

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
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
            <p class="text-xs font-medium text-gray-500 mb-2">Navigasi Soal:</p>
            <div class="flex flex-wrap gap-2">
                @foreach($soalUrut as $i => $soal)
                <button @click="pindahSoal({{ $i }})"
                        :class="soalAktif === {{ $i }}
                            ? 'bg-green-600 text-white'
                            : (jawaban[{{ $soal->id }}] ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200')"
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
             class="bg-white rounded-xl border border-gray-200 p-6 mb-4">

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

            {{-- Teks pertanyaan --}}
            <div class="text-gray-800 text-sm leading-relaxed mb-5">
                {!! nl2br(e($soal->pertanyaan)) !!}
            </div>

            {{-- Pilihan jawaban: PG & BS --}}
            @if(in_array($soal->tipe_soal, ['pg', 'bs']))
                <div class="space-y-2">
                    @foreach($soal->pilihanJawaban as $pilihan)
                    <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition"
                           :class="jawaban[{{ $soal->id }}] == {{ $pilihan->id }}
                               ? 'border-green-400 bg-green-50'
                               : 'border-gray-200 hover:border-green-300 hover:bg-gray-50'">
                        <input type="radio"
                               name="soal_{{ $soal->id }}"
                               value="{{ $pilihan->id }}"
                               {{ $jawabanAda?->pilihan_id == $pilihan->id ? 'checked' : '' }}
                               @change="simpanJawaban({{ $soal->id }}, {{ $pilihan->id }})"
                               x-model="jawaban[{{ $soal->id }}]"
                               class="text-green-600 focus:ring-green-500">
                        <span class="text-sm font-medium text-gray-600 w-5 shrink-0">{{ $pilihan->label }}.</span>
                        <span class="text-sm text-gray-800">{{ $pilihan->teks_pilihan }}</span>
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
            <div class="flex justify-between mt-5 pt-4 border-t border-gray-100">
                <button @click="pindahSoal(soalAktif - 1)"
                        x-show="soalAktif > 0"
                        class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                    ← Sebelumnya
                </button>
                <span x-show="soalAktif === 0"></span>

                @if($i < $soalUrut->count() - 1)
                <button @click="pindahSoal(soalAktif + 1)"
                        class="text-sm bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg transition">
                    Selanjutnya →
                </button>
                @else
                {{-- Tombol kumpul di soal terakhir --}}
                <button @click="konfirmasiSubmit()"
                        class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg transition">
                    Kumpulkan Ujian ✓
                </button>
                @endif
            </div>
        </div>
        @endforeach

        {{-- Tombol kumpulkan selalu tersedia di bawah --}}
        <div class="text-center mt-4">
            <button @click="konfirmasiSubmit()"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition">
                Kumpulkan Ujian
            </button>
        </div>

    </div>

    {{-- Modal konfirmasi submit --}}
    <div x-show="modalSubmit" x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="text-4xl mb-3">📝</div>
            <h3 class="font-bold text-gray-800 mb-2">Kumpulkan Ujian?</h3>
            <p class="text-sm text-gray-500 mb-1">
                Soal terjawab: <strong x-text="terjawab"></strong> / {{ $soalUrut->count() }}
            </p>
            <p class="text-xs text-gray-400 mb-5">Jawaban tidak bisa diubah setelah dikumpulkan.</p>
            <form method="POST" action="{{ route('siswa.ujian.submit', $sesi->id) }}">
                @csrf
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg text-sm mb-2 transition">
                    Ya, Kumpulkan
                </button>
            </form>
            <button @click="modalSubmit = false"
                    class="w-full text-sm text-gray-400 hover:text-gray-600">
                Kembali ke Soal
            </button>
        </div>
    </div>

    <script>
    function ruangUjian(sesiId, sisaDetikAwal) {
        return {
            sesiId,
            soalAktif: 0,
            sisaDetik: sisaDetikAwal,
            modalSubmit: false,
            // Isi jawaban dari data yang sudah ada (dari server)
            jawaban: {
                @foreach($sesi->jawabanSiswa as $j)
                {{ $j->soal_id }}: {{ $j->pilihan_id ?? 'null' }},
                @endforeach
            },

            get terjawab() {
                return Object.values(this.jawaban).filter(v => v !== null && v !== undefined).length;
            },

            init() {
                // Countdown timer
                const interval = setInterval(() => {
                    if (this.sisaDetik <= 0) {
                        clearInterval(interval);
                        // Auto-submit saat waktu habis
                        document.querySelector('form[action*="submit"]')?.submit();
                        return;
                    }
                    this.sisaDetik--;
                }, 1000);
            },

            formatWaktu(detik) {
                const jam  = Math.floor(detik / 3600);
                const mnt  = Math.floor((detik % 3600) / 60);
                const dtk  = detik % 60;
                if (jam > 0) return `${String(jam).padStart(2,'0')}:${String(mnt).padStart(2,'0')}:${String(dtk).padStart(2,'0')}`;
                return `${String(mnt).padStart(2,'0')}:${String(dtk).padStart(2,'0')}`;
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
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content
                                         || '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ soal_id: soalId, pilihan_id: pilihanId }),
                    });
                } catch(e) { /* simpan diam-diam jika gagal */ }
            },

            async simpanEssay(soalId, teks) {
                try {
                    await fetch(`/siswa/ujian/${this.sesiId}/jawab`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ soal_id: soalId, jawaban_essay: teks }),
                    });
                } catch(e) {}
            },

            konfirmasiSubmit() {
                this.modalSubmit = true;
            },
        };
    }
    </script>
</body>
</html>

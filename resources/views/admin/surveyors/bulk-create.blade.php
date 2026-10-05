<x-layouts.app title="Tambah Surveyor Masal (Bulk Create)">

    <div class="pagehead">
        <div>
            <div class="kicker">
                <i class="fa-solid fa-users-medical"></i> MANAJEMEN PETUGAS LAPANGAN
            </div>
            <h1>Tambah Surveyor Banyak Sekaligus (Bulk)</h1>
            <p class="muted">
                Daftarkan banyak akun surveyor sekaligus dengan mengunggah file CSV atau menempelkan (copy-paste) daftar teks baris.
            </p>
        </div>

        <div class="actions">
            <a href="{{ route('admin.surveyors.index') }}" class="btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
            </a>
            <a href="{{ route('admin.surveyors.download-csv-template') }}" class="btn" style="background:#0284c7; color:#fff; border-color:#0284c7;">
                <i class="fa-solid fa-file-csv"></i> Download Template CSV
            </a>
        </div>
    </div>

    <div style="max-width: 840px; margin: 0 auto;">
        <form method="POST" action="{{ route('admin.surveyors.bulk-store') }}" enctype="multipart/form-data">
            @csrf

            {{-- DEFAULT PASSWORD SETTING --}}
            <div class="card" style="margin-bottom: 20px; padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--slate-900); margin-bottom: 6px; display:flex; align-items:center; gap:8px;">
                    <i style="color:#f59e0b;"></i> Kata Sandi Bawaan (Default Password)
                </h3>
                <p class="muted" style="font-size:13px; margin-bottom:12px;">Kata sandi ini akan digunakan untuk semua akun surveyor yang tidak menyertakan kata sandi khusus.</p>

                <div style="max-width:320px;">
                    <input
                        type="text"
                        name="default_password"
                        value="{{ old('default_password', 'password123') }}"
                        placeholder="Contoh: password123"
                        required
                        style="width:100%; font-family:monospace;"
                    >
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom: 24px;">

                {{-- METODE 1: UPLOAD CSV --}}
                <div class="card" style="padding: 24px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--slate-900); margin-bottom: 6px; display:flex; align-items:center; gap:8px;">
                        <i class="fa-solid fa-file-csv text-blue-500" style="color:#2563eb;"></i> Metode 1: Unggah File CSV
                    </h3>
                    <p class="muted" style="font-size:13px; margin-bottom:16px;">
                        Unggah file `.csv` yang berisi daftar nama, email, dan kata sandi (opsional).
                    </p>

                    <div style="margin-bottom: 16px;">
                        <input
                            type="file"
                            name="csv_file"
                            accept=".csv, .txt"
                            style="width:100%; padding:8px; border:1px dashed var(--slate-300); border-radius:6px; background:var(--slate-50);"
                        >
                        @error('csv_file')
                            <span style="color:#ef4444; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <a href="{{ route('admin.surveyors.download-csv-template') }}" class="muted" style="font-size:12px; text-decoration:underline;">
                        <i class="fa-solid fa-download"></i> Unduh contoh format file CSV
                    </a>
                </div>

                {{-- METODE 2: TEKS BARIS (PASTE) --}}
                <div class="card" style="padding: 24px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--slate-900); margin-bottom: 6px; display:flex; align-items:center; gap:8px;">
                        <i class="fa-solid fa-paste text-emerald-500" style="color:#059669;"></i> Metode 2: Teks Baris (Copy-Paste)
                    </h3>
                    <p class="muted" style="font-size:13px; margin-bottom:12px;">
                        Tempelkan daftar baris dipisahkan koma: <code>Nama, Email, Password</code>
                    </p>

                    <div>
                        <textarea
                            name="bulk_text"
                            rows="7"
                            placeholder="Ahmad Subagja, ahmad@cirebon-rtlh.test&#10;Budi Santoso, budi@cirebon-rtlh.test, pass123&#10;Citra Dewi, citra@cirebon-rtlh.test"
                            style="width:100%; font-family:monospace; font-size:13px;"
                        >{{ old('bulk_text') }}</textarea>
                        @error('bulk_text')
                            <span style="color:#ef4444; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- SUBMIT BUTTONS CARD --}}
            <div class="card" style="padding: 20px 24px; display:flex; justify-content:space-between; align-items:center;">
                <div class="muted" style="font-size:13px;">
                    <i class="fa-solid fa-circle-info text-blue-500" style="color:#2563eb;"></i> Email yang sudah terdaftar akan otomatis dilewati agar tidak duplicate.
                </div>

                <div class="actions" style="gap:12px;">
                    <a href="{{ route('admin.surveyors.index') }}" class="btn">
                        Batal
                    </a>
                    <button type="submit" class="btn primary" style="padding: 10px 20px;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Proses & Simpan Semua Surveyor
                    </button>
                </div>
            </div>

        </form>
    </div>

</x-layouts.app>

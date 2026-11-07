# REPOSITORI DOKUMENTASI ARSITEKTUR & SISTEM TANDAIN
## Platform Tanda Tangan Elektronik Kampus Berdaulat
**Jurusan Teknik Informatika dan Komputer — Politeknik Negeri Jakarta (PNJ)**

---

## 1. IKHTISAR REPOSITORI DOKUMENTASI

Selamat datang di repositori dokumentasi rekayasa perangkat lunak dan arsitektur sistem **Tandain** (*Tanda Tangan Elektronik Kampus*). Dokumentasi ini disusun dengan standar akademik dan profesional tinggi (mengadopsi kerangka IEEE Std 830 untuk SRS dan IEEE Std 829 untuk Rencana Pengujian), dirancang untuk memberikan transparansi teknis, kepatuhan tata kelola birokrasi, serta kejelasan operasional bagi seluruh civitas akademika Jurusan TIK PNJ.

Platform Tandain menghadirkan solusi pengesahan dokumen digital tanpa kertas (*paperless*) yang mandiri (*on-premise*), aman secara kriptografis melalui algoritma RSA 2048-bit dan SHA-256, serta bebas dari beban biaya langganan per-dokumen pihak ketiga (*zero third-party cost*).

---

## 2. PETA NAVIGASI DOKUMEN SISTEM

Berikut adalah daftar dokumen teknis lengkap yang menyusun cetak biru sistem Tandain:

| Nama Berkas | Judul & Topik Dokumen | Sasaran Pembaca Utama | Ringkasan Pokok Isi Dokumen |
| :--- | :--- | :--- | :--- |
| [**`00_RINGKASAN_DAN_LATAR_BELAKANG.md`**](./00_RINGKASAN_DAN_LATAR_BELAKANG.md) | **Ringkasan Eksekutif, Latar Belakang & Analisis Strategis** | Pimpinan Jurusan, Dosen Pengampu, Komite Akademik | Menguraikan latar belakang inefisiensi birokrasi tatap muka di PNJ, bahaya pemalsuan tanda tangan basah/scan PNG, visi kedaulatan data kampus, profil pembina (Ir. Iwan Sonjaya, M.T. & Anita Hidayati, S.Kom., M.Kom.), matriks RACI stakeholder, serta studi benchmarking sistem IPB DigiSign dan analisis komparatif kompetitor. |
| [**`01_SRS_SPESIFIKASI_KEBUTUHAN_SISTEM.md`**](./01_SRS_SPESIFIKASI_KEBUTUHAN_SISTEM.md) | **Spesifikasi Kebutuhan Perangkat Lunak (IEEE 830 SRS)** | Tim Pengembang, System Analyst, QA Engineer | Mendefinisikan model *Unified User Entity* (Mahasiswa, Dosen, Tendik) dengan *Dynamic Functional Roles* (Requester & Signer), kebutuhan fungsional (FR) login SSO, master template DomPDF, direct/self/triage routing, sequential signing, passphrase authorization, visual stamping, validitas dokumen vs sertifikat, kebutuhan non-fungsional (NFR), dan batasan tegas in-scope vs out-of-scope. |
| [**`02_ARSITEKTUR_KRIPTOGRAFI_DAN_DATABASE.md`**](./02_ARSITEKTUR_KRIPTOGRAFI_DAN_DATABASE.md) | **Arsitektur Teknologi, Kriptografi & Desain Basis Data** | Arsitek Software, Cryptographer, DBA Backend | Membedah stack teknologi (PHP Native 8.2+ MVC, PostgreSQL 15/16 + PDO_PGSQL + pgcrypto, OpenSSL, Google Identity google/apiclient OIDC, DomPDF, FPDI, PDF.js), pengamanan *Zero-Plaintext Private Key* via AES-256-CBC, solusi paradoks hash sequential signing berbasis *Chained Manifest Sealing*, logika *Dual Verification Engine*, diagram ERD, dan kamus data 7 tabel utama dengan tipe data modern PostgreSQL. |
| [**`03_ALUR_KERJA_DAN_PANDUAN_WEB_UIUX.md`**](./03_ALUR_KERJA_DAN_PANDUAN_WEB_UIUX.md) | **Cetak Biru Alur Kerja & Panduan Desain Antarmuka UI/UX** | Frontend Engineer, UI/UX Designer, Staf Admin TU | Menampilkan diagram Mermaid lengkap untuk 4 alur kerja utama (Pengajuan Dokumen, Triage Desk TU, Sequential Signing, dan Verifikasi Publik QR Code), panduan visual berstandar Google Workspace & IBM Carbon, tipografi (Montserrat & Times New Roman), token warna semantik WCAG 2.1 AA, dan pedoman Form Builder dengan panel cheat sheet. |
| [**`04_RENCANA_PENGUJIAN_DAN_VERIFIKASI_UAT.md`**](./04_RENCANA_PENGUJIAN_DAN_VERIFIKASI_UAT.md) | **Rencana Pengujian Perangkat Lunak & Verifikasi UAT** | Quality Assurance, Penguji Keamanan, Stakeholder | Memuat skenario uji blackbox terstruktur, pengujian brute-force dan SSO whitelist, otorisasi passphrase, pengujian batas kedaluwarsa dokumen, simulasi manipulasi dokumen biner (tamper detection), serta lembar pengesahan Berita Acara UAT resmi. |
| [**`05_STUDI_KASUS_DAN_BENCHMARK_IPB_DISIGN.md`**](./05_STUDI_KASUS_DAN_BENCHMARK_IPB_DISIGN.md) | **Studi Kasus Empiris & Benchmark Sistem IPB DiSign** | Dosen Pengampu, UI/UX Designer, System Analyst | Membedah 8 tangkapan layar empiris sistem produksi IPB DiSign: spesimen tanda tangan/paraf, aturan 1 sertifikat aktif 1 tahun, form pembuatan sertifikat baru & ketentuan passphrase non-reset, form pengajuan berurutan (*sequential signing* toggle), multi-TTD dinamis, pemantauan dokumen diunggah vs ditandatangani, preview canvas tersemat, dan validasi visual stempel dual dosen-mahasiswa berdampingan. |
| [**`AGENTS.md`**](./AGENTS.md) | **Pedoman Rekayasa & Rules Proyek MVC Native** | Seluruh Tim Pengembang & AI Agents | Memuat 7 aturan baku rekayasa perangkat lunak: struktur MVC sederhana (`core/`, `controllers/`, `models/`, `views/`), query PDO standar, larangan komentar kode (*zero comments*), prinsip antislop, strategi commit bertahap, jeda baris visual (*breathing room*), serta pola *if-else single return*. |

---

## 3. PANDUAN MEMBACA BERDASARKAN PERAN PENGGUNA

Untuk mengoptimalkan efisiensi penelaahan dokumen ini, silakan ikuti alur bacaan yang disesuaikan dengan peran Anda:

### 3.1 Untuk Pengembang Perangkat Lunak (*Software Engineers & Developers*)
1. Mulai dengan [**`01_SRS_SPESIFIKASI_KEBUTUHAN_SISTEM.md`**](./01_SRS_SPESIFIKASI_KEBUTUHAN_SISTEM.md) untuk memahami batasan *In-Scope* vs *Out-of-Scope* serta kriteria penerimaan setiap modul.
2. Pelajari secara mendalam [**`02_ARSITEKTUR_KRIPTOGRAFI_DAN_DATABASE.md`**](./02_ARSITEKTUR_KRIPTOGRAFI_DAN_DATABASE.md) sebelum menulis kode backend, khususnya alur siklus hidup memori kunci privat dan skema kamus data relasional.
3. Rujuk [**`03_ALUR_KERJA_DAN_PANDUAN_WEB_UIUX.md`**](./03_ALUR_KERJA_DAN_PANDUAN_WEB_UIUX.md) untuk implementasi token CSS, rasio kontras warna, dan interaksi form builder.

### 3.2 Untuk Dosen Pengampu & Penilai Akademik (*Supervisors & Reviewers*)
1. Baca [**`00_RINGKASAN_DAN_LATAR_BELAKANG.md`**](./00_RINGKASAN_DAN_LATAR_BELAKANG.md) untuk mengevaluasi landasan pemikiran, relevansi kurikulum vokasi PNJ, dan posisi Tandain terhadap preseden kampus lain seperti IPB DigiSign.
2. Telaah Bagian 3 pada [**`02_ARSITEKTUR_KRIPTOGRAFI_DAN_DATABASE.md`**](./02_ARSITEKTUR_KRIPTOGRAFI_DAN_DATABASE.md) untuk meninjau orisinalitas solusi matematis terkait paradoks hash sequential signing.
3. Tinjau [**`04_RENCANA_PENGUJIAN_DAN_VERIFIKASI_UAT.md`**](./04_RENCANA_PENGUJIAN_DAN_VERIFIKASI_UAT.md) pada bagian Berita Acara UAT sebagai bukti pengujian penerimaan pengguna.

### 3.3 Untuk Administrator Sistem & Staf Tata Usaha (*System Administrators & TU Staff*)
1. Pahami wewenang dan alur kerja *Triage Desk* pada Dokumen [**`03_ALUR_KERJA_DAN_PANDUAN_WEB_UIUX.md`**](./03_ALUR_KERJA_DAN_PANDUAN_WEB_UIUX.md) Bab 1.2.
2. Pelajari panduan pembuatan master template persuratan kedinasan pada Bab 3 Dokumen [**`03_ALUR_KERJA_DAN_PANDUAN_WEB_UIUX.md`**](./03_ALUR_KERJA_DAN_PANDUAN_WEB_UIUX.md).
3. Pahami perbedaan status sertifikat aktif dan kedaluwarsa pada Dokumen [**`01_SRS_SPESIFIKASI_KEBUTUHAN_SISTEM.md`**](./01_SRS_SPESIFIKASI_KEBUTUHAN_SISTEM.md) Bab 3.9.

---

## 4. PRINSIP ARSITEKTUR UTAMA TANDAIN

```
+-----------------------------------------------------------------------------+
|                      PILAR ARSITEKTURAL TANDAIN                             |
+-----------------------------------------------------------------------------+
| 1. ZERO-COST SOVEREIGNTY: Tidak ada biaya API atau lisensi pihak ketiga.    |
| 2. ZERO-PLAINTEXT KEY: Kunci privat selalu terenkripsi AES-256-CBC.         |
| 3. SEQUENTIAL CHAINING: Mengatasi paradoks hash melalui rantai manifest.    |
| 4. DUAL VERIFICATION: Pemisahan integritas matematis vs batas administrasi. |
| 5. UNIVERSAL CITIZEN: Mahasiswa, Dosen, Tendik dalam entitas terpadu.       |
+-----------------------------------------------------------------------------+
```

Seluruh kode sumber dan naskah dokumentasi ini dipelihara di bawah supervisi **Jurusan Teknik Informatika dan Komputer Politeknik Negeri Jakarta**. Untuk pertanyaan teknis dan kontribusi pengembangan lebih lanjut, silakan menghubungi tim laboratorium rekayasa perangkat lunak Jurusan TIK PNJ.

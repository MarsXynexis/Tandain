# DOKUMEN 04: RENCANA PENGUJIAN DAN VERIFIKASI USER ACCEPTANCE TESTING (UAT)
## Sistem Tanda Tangan Elektronik Kampus "Tandain"
**Standar Acuan: IEEE Std 829-2008 / Standar Penjaminan Mutu Perangkat Lunak TIK PNJ**

---

## 1. RENCANA PENGUJIAN PERANGKAT LUNAK (SOFTWARE TEST PLAN)

### 1.1 Tujuan Pengujian
Rencana Pengujian (*Test Plan*) ini disusun untuk memvalidasi bahwa seluruh fitur fungsional, integritas kriptografis, dan kehandalan operasional sistem **Tandain** bekerja sesuai dengan Dokumen Spesifikasi Kebutuhan Perangkat Lunak (SRS) serta memenuhi ekspektasi pengguna nyata di Jurusan TIK Politeknik Negeri Jakarta.

### 1.2 Lingkungan Pengujian (Test Environment)
Pengujian dilakukan pada lingkungan yang merefleksikan arsitektur peladen produksi kampus:
* **Peladen Aplikasi:** Ubuntu Server 22.04 LTS / Apache 2.4 / PHP 8.2 (OpenSSL enabled, memory_limit: 256M).
* **Peladen Basis Data:** PostgreSQL 15 / 16 (dengan ekstensi `pgcrypto`, driver `PDO_PGSQL`).
* **Peramban Uji (Desktop):** Google Chrome Versi 120+, Mozilla Firefox Versi 121+, Microsoft Edge Versi 120+.
* **Peramban Uji (Mobile):** Chrome Mobile (Android 13/14) & Safari Mobile (iOS 17).
* **Alat Bantu Forensik PDF:** HxD Hex Editor (untuk manipulasi bita biner PDF), OpenSSL CLI 3.0 (verifikasi hash dan signature manual).

### 1.3 Pendekatan Metodologi Pengujian
1. **Black-box Functional Testing:** Menguji masukan dan luaran setiap fungsi aplikasi tanpa melihat struktur internal kode program, memastikan kriteria penerimaan SRS terpenuhi.
2. **Boundary Value & Negative Testing:** Menguji batas kedaluwarsa dokumen, format masukan ekstrem, dan kombinasi kata sandi salah.
3. **Cryptographic Tamper Simulation (Simulasi Serangan Integritas):** Memanipulasi bita biner dokumen secara sengaja di luar sistem untuk membuktikan keandalan *Dual Verification Engine*.
4. **User Acceptance Testing (UAT):** Pengujian penerimaan langsung oleh pemangku kepentingan utama: Staf Admin TU, Dosen Pengampu, dan Mahasiswa.

---

## 2. MATRIKS PENGUJIAN BLACKBOX & SKENARIO UAT

Berikut adalah matriks komprehensif skenario pengujian fungsional dan keamanan sistem:

| ID Kasus Uji | Kategori Fitur | Skenario Pengujian | Langkah Pengujian | Data Masukan (Input) | Hasil yang Diharapkan (Expected Result) | Status Kelulusan |
| :--- | :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-AUTH-01** | Autentikasi | Login Kredensial Lokal Berhasil | 1. Buka `/login`<br>2. Masukkan email dan sandi terdaftar.<br>3. Klik tombol "Masuk". | Email: `dosen.tik@pnj.ac.id`<br>Password: `SandiBenar#2026` | Sistem memvalidasi hash bcrypt, membuat sesi aman, dan mengarahkan ke dashboard dosen. | **PASS** |
| **TC-AUTH-02** | Autentikasi | Proteksi Brute-Force Login Sandi Salah | 1. Masukkan sandi salah sebanyak 5 kali berturut-turut pada akun yang sama. | Password: `SandiSalah123` (x5) | Pada percobaan ke-5, sistem mengunci sementara percobaan login selama 15 menit via session server (`$_SESSION['login_attempts']`) dengan pesan: *"Terlalu banyak percobaan gagal. Silakan tunggu 15 menit."* | **PASS** |
| **TC-AUTH-03** | Autentikasi (SSO) | Google SSO Akun Terdaftar (Whitelist) | 1. Klik tombol "Masuk dengan Google".<br>2. Pilih akun Google resmi PNJ. | Email OAuth: `mahasiswa@pnj.ac.id` (Sudah terdaftar di tabel `users`). | Sistem memverifikasi subjek token Google, mencocokkan email, dan langsung login ke dashboard mahasiswa. | **PASS** |
| **TC-AUTH-04** | Autentikasi (SSO) | Penolakan Google SSO Akun Non-Whitelist | 1. Klik "Masuk dengan Google".<br>2. Pilih akun email publik/pribadi. | Email OAuth: `orangasing@gmail.com` (Belum ada di tabel `users`). | Sistem menolak sesi, membatalkan login, dan menampilkan notifikasi: *"Email Anda belum terdaftar dalam direktori civitas PNJ TIK."* | **PASS** |
| **TC-TMPL-01** | Master Template | Pembuatan Master Template Surat Dinas | 1. Masuk sebagai Admin TU.<br>2. Buka menu Form Builder Template.<br>3. Klik tag placeholder pada Cheat Sheet.<br>4. Klik "Simpan Template". | Tag: `{{nama_lengkap}}`, `{{nomor_induk}}`, `{{kolom_ttd_1}}`<br>Kode: `SK-AKTIF-2026` | Tag tersisip presisi di area kursor tanpa salah eja. Template tersimpan dengan status `is_active = 1`. | **PASS** |
| **TC-TMPL-02** | Pembuatan Dokumen | Pengisian Form & Render DomPDF Otomatis | 1. Mahasiswa memilih template "Surat Aktif Kuliah".<br>2. Mengisi kolom keperluan.<br>3. Klik "Ajukan Dokumen". | Keperluan: *"Persyaratan Beasiswa Djarum Plus"* | DomPDF berhasil menghasilkan berkas PDF. Seluruh placeholder tergantikan sempurna dengan data mahasiswa, margin A4 presisi. | **PASS** |
| **TC-ROUTE-01** | Disposisi (Triage) | Alur Triage Desk oleh Staf Tata Usaha | 1. Mahasiswa mengajukan surat via rute `TRIAGE_ASSISTED`.<br>2. Staf TU membuka dasbor antrean triage.<br>3. TU menentukan Signer 1 (Koorprodi) & Signer 2 (Kajur).<br>4. TU klik "Disposisikan". | Nomor Surat: `B/124/PL3.TIK/2026`<br>Signer 1: Koorprodi<br>Signer 2: Kajur | Dokumen berpindah status dari `PENDING_TRIAGE` menjadi `IN_PROGRESS`. Signer 1 menerima notifikasi bahwa gilirannya menandatangani. | **PASS** |
| **TC-ROUTE-02** | Disposisi (Triage) | Penolakan Pengajuan Dokumen oleh TU | 1. Staf TU memeriksa permohonan yang tidak lengkap lampirannya.<br>2. TU klik "Tolak Dokumen".<br>3. Memasukkan catatan revisi. | Alasan: *"KRS semester berjalan belum dilampirkan. Mohon lengkapi."* | Status dokumen berubah menjadi `REJECTED`. Mahasiswa menerima notifikasi penolakan beserta isi catatan revisi TU. | **PASS** |
| **TC-ROUTE-03** | Penentuan Signer | Pencarian Penandatangan via Email (Debounce & Tombol Cari) | 1. Ketikkan email dosen pada form ajukan surat.<br>2. Uji timer debounce 500ms tanpa tombol.<br>3. Uji penekanan tombol fisik "Cari" / Enter.<br>4. Verifikasi kemunculan kartu hasil dan klik "Tambah". | Email: `anita.h@pnj.ac.id` | Sistem mengeksekusi panggilan AJAX setelah delay 500ms atau instan via tombol Cari. Menampilkan kartu identitas lengkap (nama, NIP, jabatan, status sertifikat) dan berhasil menambahkannya ke urutan signer tanpa beban render ribuan elemen dropdown. | **PASS** |
| **TC-SIGN-01** | Penandatanganan | Penandatanganan Mandiri (Self-Signing) | 1. Dosen membuat Surat Bebas Lab.<br>2. Memilih rute `SELF_SIGN`.<br>3. Memasukkan passphrase sertifikat.<br>4. Konfirmasi tanda tangan. | Passphrase: `PassphraseDosenRahasia2026!` | Stempel visual tertempel pada koordinat, dokumen langsung berstatus `COMPLETED` dalam satu tahapan. | **PASS** |
| **TC-SIGN-02** | Penandatanganan | Penandatanganan Berantai Multi-Level | 1. Dokumen memerlukan 2 Signer berurutan.<br>2. Signer 2 mencoba membuka dokumen saat Signer 1 belum tanda tangan.<br>3. Signer 1 menandatangani dokumen.<br>4. Signer 2 membuka kembali dokumen. | Rantai: Signer 1 -> Signer 2 | Pada langkah 2, tombol tanda tangan Signer 2 terkunci (*disabled*). Pada langkah 4 (setelah Signer 1 selesai), tombol Signer 2 aktif dan siap diteken. | **PASS** |
| **TC-PASS-01** | Kriptografi | Validasi Otorisasi Passphrase Kunci Benar | 1. Signer membuka modal penandatanganan.<br>2. Memasukkan passphrase sertifikat yang sesuai.<br>3. Klik "Sahkan Tanda Tangan". | Input Passphrase: Valid | Kunci privat berhasil didekripsi di memori via AES-256-CBC, fungsi `openssl_sign` sukses, memori di-*zeroize*, stempel QR terpasang. | **PASS** |
| **TC-PASS-02** | Kriptografi | Penolakan Passphrase Kunci Salah | 1. Signer memasukkan passphrase sembarang/salah.<br>2. Klik "Sahkan Tanda Tangan". | Input Passphrase: `PasswordAsal123` | Dekripsi AES gagal di RAM. Sistem membatalkan proses penandatanganan, dokumen tetap utuh, dan log galat dicatat pada sistem. | **PASS** |
| **TC-EXP-01** | Validitas Waktu | Verifikasi Dokumen Saat Sertifikat Expired (Retroaktif) | 1. Dokumen ditandatangani tanggal 10 Jan 2025 (sertifikat valid s.d. 10 Jan 2026).<br>2. Dilakukan verifikasi pada tanggal 15 Mei 2026 (sertifikat sudah kadaluwarsa). | Tanggal Verifikasi > Valid_Until Sertifikat | Tanda tangan tetap dinyatakan **SAH/VALID** karena saat penandatanganan dieksekusi, sertifikat penandatangan terbukti aktif sah (*non-repudiation*). | **PASS** |
| **TC-EXP-02** | Validitas Waktu | Verifikasi Dokumen Melewati Batas Administrasi | 1. Dokumen Surat Keterangan Aktif memiliki `expires_at = 2026-02-01`.<br>2. Dilakukan verifikasi publik pada `2026-03-01`. | Tanggal Sekarang > `expires_at` | Sistem menampilkan **BADGE KUNING**: *"Dokumen Asli & Sah secara Kriptografi, Namun Masa Berlaku Administratif Telah Berakhir."* | **PASS** |
| **TC-TAMPER-01** | Kriptografi Forensik | Simulasi Manipulasi Bita PDF (Tamper Detection) | 1. Unduh berkas PDF yang telah selesai ditandatangani (`final_sha256_hash` valid).<br>2. Buka berkas dengan Hex Editor.<br>3. Ubah 1 bita teks isi surat (misal mengubah nilai IPK dari 3.80 menjadi 3.99).<br>4. Simpan dan unggah berkas ke portal verifikasi `/verify`. | Berkas PDF yang telah dimodifikasi 1 bita di luar peladen. | Sistem menghitung ulang SHA-256 berkas unggahan -> Hash tidak cocok dengan database -> Sistem menampilkan **BADGE MERAH BAHAYA**: *"PERINGATAN: DOKUMEN TELAH MENGALAMI MANIPULASI ATAU TIDAK SAH!"* | **PASS** |

---

## 3. LEMBAR BERITA ACARA PENGUJIAN PENERIMAAN PENGGUNA (UAT SIGN-OFF FORM)

Pada hari ini, **Rabu tanggal Tiga Puluh bulan September tahun Dua Ribu Dua Puluh Enam**, telah dilaksanakan sesi evaluasi dan verifikasi *User Acceptance Testing* (UAT) untuk implementasi perangkat lunak sistem **Tanda Tangan Elektronik Kampus "Tandain"** bertempat di Ruang Laboratorium Rekayasa Perangkat Lunak, Gedung Jurusan TIK Politeknik Negeri Jakarta.

### 3.1 Pernyataan Hasil Pengujian
Berdasarkan hasil pengujian terhadap seluruh matriks uji fungsional, performa, dan pengujian ketahanan kriptografi:
1. Sistem terbukti mampu menyelesaikan alur birokrasi persuratan akademik secara terintegrasi (Pengajuan, Disposisi TU, dan Penandatanganan Berantai).
2. Mekanisme pengamanan kunci privat RSA 2048-bit terbukti aman (*Zero-Plaintext*) dan terlindungi penuh di balik otorisasi passphrase pengguna.
3. *Dual Verification Engine* berhasil mendeteksi manipulasi bita dokumen sekecil apapun serta mampu membedakan integritas kriptografis dari batas masa berlaku administratif.
4. Antarmuka sistem memenuhi kriteria aksesibilitas kontras warna WCAG 2.1 Level AA serta memiliki performa render PDF di bawah 3 detik.

Dengan ini dinyatakan bahwa sistem **Tandain** dinyatakan:

$$[\ \ \mathbf{X}\ \ ]\ \ \mathbf{DITERIMA\ BAIK\ TANPA\ CATATAN\ KRITIS\ (ACCEPTED)}$$
$$[\ \ \ \ \ ]\ \ \text{Diterima dengan Catatan Minor}$$
$$[\ \ \ \ \ ]\ \ \text{Ditolak / Memerlukan Perbaikan Mayor}$$

---

### 3.2 Lembar Pengesahan Tim Penguji & Stakeholder

Ditetapkan dan disahkan di Depok, 30 September 2026.

| Pihak Pengesah | Nama & Gelar | Tanda Tangan / Persetujuan Digital |
| :--- | :--- | :---: |
| **Dosen Pembimbing Kriptografi & Infrastruktur** | **Ir. Iwan Sonjaya, M.T.**<br>NIP: 196803121994031002 | `[ TERVERIFIKASI TANDAIN ]`<br>*(Signed via RSA-2048 SHA-256)* |
| **Dosen Pembimbing Rekayasa Perangkat Lunak** | **Anita Hidayati, S.Kom., M.Kom.**<br>NIP: 197908242008122001 | `[ TERVERIFIKASI TANDAIN ]`<br>*(Signed via RSA-2048 SHA-256)* |
| **Perwakilan Administrator Tata Usaha (TU)** | **Staf Administrasi Akademik TIK PNJ**<br>NIP/NUP: 198504152010121004 | `[ TERVERIFIKASI TANDAIN ]`<br>*(Signed via RSA-2048 SHA-256)* |
| **Perwakilan Mahasiswa / Ormawa TIK** | **Ketua Himpunan Mahasiswa TIK PNJ**<br>NIM: 2207421035 | `[ TERVERIFIKASI TANDAIN ]`<br>*(Signed via RSA-2048 SHA-256)* |
| **Lead Developer & System Architect** | **Ketua Tim Pengembang Tandain**<br>NIM: 2207421044 | `[ TERVERIFIKASI TANDAIN ]`<br>*(Signed via RSA-2048 SHA-256)* |

# DOKUMEN 01: SPESIFIKASI KEBUTUHAN PERANGKAT LUNAK (SRS)
## Sistem Tanda Tangan Elektronik Kampus "Tandain"
**Standar Acuan: IEEE Std 830-1998 / Kurikulum Jurusan TIK Politeknik Negeri Jakarta**

---

## 1. PENDAHULUAN

### 1.1 Tujuan Dokumen
Dokumen Spesifikasi Kebutuhan Perangkat Lunak (*Software Requirements Specification* — SRS) ini bertujuan untuk mendefinisikan secara menyeluruh kebutuhan fungsional dan non-fungsional dari perangkat lunak **Tandain**. Dokumen ini menjadi acuan tunggal bagi tim arsitek perangkat lunak, pengembang web (*backend* dan *frontend*), penguji perangkat lunak (*software tester/QA*), serta para dosen pengampu (*supervisor*) dalam memastikan sistem dibangun sesuai spesifikasi teknis dan kebutuhan birokrasi kampus Jurusan TIK PNJ.

### 1.2 Ruang Lingkup Produk
Sistem **Tandain** adalah aplikasi web tanda tangan elektronik terdesentralisasi internal kampus yang memfasilitasi siklus hidup dokumen digital: mulai dari pembuatan draf surat baku otomatis, pengunggahan berkas mandiri, disposisi birokrasi (*triage*), penandatanganan berantai multi-tingkat menggunakan kriptografi kunci publik (RSA 2048-bit dan SHA-256), hingga penyediaan portal verifikasi publik nir-login.

### 1.3 Definisi, Akronim, dan Singkatan
* **TTE:** Tanda Tangan Elektronik.
* **PNJ TIK:** Jurusan Teknik Informatika dan Komputer Politeknik Negeri Jakarta.
* **SRS:** *Software Requirements Specification*.
* **RSA:** *Rivest-Shamir-Adleman*, algoritma kriptografi kunci asimetris.
* **SHA-256:** *Secure Hash Algorithm 256-bit*, fungsi *hash* kriptografis searah yang menghasilkan *digest* 32 bita (64 karakter heksadesimal).
* **AES-256-CBC:** *Advanced Encryption Standard* dengan panjang kunci 256-bit dalam mode *Cipher Block Chaining*, digunakan untuk enkripsi simetris kunci privat di peladen.
* **Passphrase:** Kalimat sandi rahasia yang hanya diketahui oleh pemilik kunci untuk mendekripsi kunci privat RSA di memori peladen saat proses penandatanganan.
* **DomPDF:** Pustaka konversi HTML/CSS menjadi berkas biner PDF pada lingkungan PHP.
* **FPDI / FPDF:** Pustaka manipulasi berkas PDF pada PHP yang digunakan untuk menyisipkan stempel visual dan kode QR ke halaman dokumen yang sudah ada.
* **Triage Desk:** Meja disposisi virtual yang dikelola oleh staf Tata Usaha untuk menelaah dokumen pengajuan mahasiswa dan merutekannya ke pejabat berwenang.
* **P2P Signing:** *Peer-to-Peer Signing*, alur penandatanganan horizontal antar sesama mahasiswa (misal: Ketua Himpunan ke Ketua BEM).

### 1.4 Referensi Normatif
1. **IEEE Std 830-1998:** *IEEE Recommended Practice for Software Requirements Specifications*.
2. **Undang-Undang RI No. 11 Tahun 2008:** *Tentang Informasi dan Transaksi Elektronik (UU ITE)* beserta perubahannya pada UU No. 1 Tahun 2024.
3. **Peraturan Pemerintah No. 71 Tahun 2019:** *Penyelenggaraan Sistem dan Transaksi Elektronik (PSTE)*.
4. **W3C WCAG 2.1:** *Web Content Accessibility Guidelines*, Level AA Compliance.

---

## 2. DESKRIPSI KESELURUHAN SISTEM

### 2.1 Perspektif Produk
Sistem **Tandain** diposisikan sebagai aplikasi terpusat (*on-premise*) di lingkungan komputasi Jurusan TIK PNJ. Sistem berinteraksi dengan:
* **Pengguna Internal Kampus:** Mahasiswa, Dosen, Tenaga Kependidikan (Tendik), dan Pimpinan Jurusan.
* **Layanan Google OAuth 2.0:** Untuk autentikasi *Single Sign-On* (SSO) akun kampus terdaftar.
* **Publik / Pihak Eksternal:** Penguji keaslian dokumen (perusahaan mitra, orang tua, lembaga beasiswa) yang mengakses portal verifikasi melalui pemindaian kode QR.

```
                    +------------------------------------------+
                    |          TANDAIN CORE PLATFORM           |
                    +------------------------------------------+
                     |                    |                   |
        +------------+                    |                   +------------+
        v                                 v                                v
+------------------+             +------------------+             +------------------+
|   AKTOR KAMPUS   |             |   TATA USAHA     |             | PUBLIK EKSTERNAL |
| Mahasiswa, Dosen |             | Disposisi Triage |             | Scan QR & Verif  |
| SSO / Kredensial |             | Template Surat   |             | Tanpa Akun/Login |
+------------------+             +------------------+             +------------------+
```

### 2.2 Model Entitas Pengguna Terpadu & Peran Fungsional Dinamis
Berbeda dengan sistem konvensional yang membedakan tabel mahasiswa dan dosen secara kaku, Tandain mengadopsi model **Unified User Entity** dengan **Dynamic Functional Roles**. Setiap akun pengguna (baik mahasiswa, dosen, maupun staf tendik) memiliki hak dasar yang sama untuk bertindak dalam dua peran fungsional:
1. **Requester (Pengaju):** Entitas yang membuat atau mengunggah draf dokumen serta mengajukan alur persetujuan.
2. **Signer (Penandatangan):** Entitas yang memiliki pasangan kunci kriptografi aktif dan menandatangani dokumen sesuai antrean.

> [!IMPORTANT]
> **Prinsip Kesetaraan Hak Akses (Universal Signer & Requester Privilege):**
> Sistem Tandain **TIDAK MEMBATASI** hak tanda tangan hanya untuk dosen atau pejabat struktural kampus. Seluruh civitas akademika—baik Mahasiswa, Dosen, maupun Tendik—memiliki hak penuh dan setara untuk:
> 1. **Menerbitkan Sertifikat TTE Digital Mandiri:** Mahasiswa berhak membangkitkan pasangan kunci RSA 2048-bit dan memiliki sertifikat aktif di sistem.
> 2. **Menentukan Alur Penandatangan (*Set Signers*):** Siapapun yang bertindak sebagai pemohon (termasuk mahasiswa yang mengajukan proposal atau surat kegiatan) berhak menentukan rantai urutan penandatangan (Signer 1..N) baik dosen maupun sesama mahasiswa.
> 3. **Ditunjuk & Menjadi Penandatangan (*Act as Signer*):** Mahasiswa berhak menerima permohonan tanda tangan dan membubuhkan TTE resmi (misal: Ketua Ormawa menandatangani proposal kegiatan, Ketua Kelompok menandatangani laporan proyek, atau Bendahara menyetujui LPJ kepanitiaan).

#### Karakteristik Aktor Berdasarkan Skenario Penggunaan:
* **Mahasiswa:** 
  * Sebagai *Requester:* Mengajukan surat keterangan aktif kuliah, surat survei riset tugas akhir, atau lembar pengesahan skripsi.
  * Sebagai *Signer (P2P):* Ketua Ormawa, Ketua Panitia Dies Natalis, atau Pemimpin Redaksi Majalah Kampus yang menandatangani surat dinas kemahasiswaan sebelum diteruskan ke Pembina ormawa.
* **Dosen:**
  * Sebagai *Signer:* Membubuhkan tanda tangan pada lembar pengesahan nilai, surat bebas tanggungan laboratorium, atau berita acara ujian.
  * Sebagai *Requester:* Mengajukan permohonan surat tugas pengabdian masyarakat atau pengajuan seminar internasional ke Ketua Jurusan.
* **Admin Tata Usaha (TU):**
  * Mengelola *Master Template* surat baku dinas jurusan.
  * Mengoperasikan *Triage Desk* untuk mendisposisikan surat dari mahasiswa yang belum mengetahui urutan penandatangan yang berwenang.
  * Mengelola daftar putih (*whitelist*) email civitas akademika.
* **Pemeriksa Publik (Public Verifier):**
  * Entitas eksternal yang tidak memiliki akun login. Mengakses sistem hanya melalui *endpoint* publik untuk memvalidasi otentisitas dokumen fisik bertanda QR code atau mengunggah berkas PDF.

### 2.3 Batasan Perancangan Umum (General Constraints)
1. **Bahasa Pemrograman & Lingkungan Peladen:** Dibangun menggunakan PHP Native (Versi 8.2+) dengan arsitektur MVC (*Model-View-Controller*) murni sederhana (struktur direktori `core/`, `controllers/`, `models/`, `views/`, dan `index.php` yang patuh pada aturan rekayasa `AGENTS.md`) pada lingkungan peladen Nginx 1.24+ / Apache 2.4+ dengan konfigurasi TLS 1.3 dan ekstensi OpenSSL aktif.
2. **Basis Data:** PostgreSQL 15 / 16 dengan ekstensi `pgcrypto` (penyediaan fungsi `gen_random_uuid()` untuk UUIDv4), driver PHP `PDO_PGSQL`, pemanfaatan tipe data modern (`UUID`, `TIMESTAMPTZ` zona waktu Asia/Jakarta UTC+7, `JSONB` untuk skema formulir template dan jejak audit, `BOOLEAN`, `TEXT`, `VARCHAR`, dan `BYTEA`), serta pengkodean UTF-8 (C.UTF-8 / en_US.UTF-8).
3. **Eksekusi Kriptografi:** Penandatanganan dilakukan *server-side* dengan isolasi memori sementara untuk menjaga kerahasiaan kunci privat.
4. **Kepatuhan Format Berkas:** Dokumen yang beredar dalam sistem wajib berformat **PDF (Portable Document Format)** standar ISO 32000-1.

---

## 3. KEBUTUHAN FUNGSIONAL (FUNCTIONAL REQUIREMENTS)

### 3.1 Modul Autentikasi & Otorisasi Akun

#### [FR-AUTH-001] Login Kredensial Lokal
* **User Story:** Sebagai pengguna terdaftar, saya ingin dapat masuk ke dalam sistem menggunakan kombinasi email/username dan kata sandi lokal, sehingga saya dapat mengakses dasbor operasional saya.
* **Kriteria Penerimaan (Acceptance Criteria):**
  1. *Given* pengguna berada di halaman `/login`, *When* pengguna memasukkan email dan password yang cocok di database, *Then* sistem menginisiasi sesi aman dan mengarahkan pengguna ke halaman dashboard utama.
  2. *Given* pengguna memasukkan password salah sebanyak 5 kali berturut-turut, *When* percobaan ke-5 gagal, *Then* sistem mengunci sementara percobaan login akun tersebut selama 15 menit melalui pelacakan sesi server (`$_SESSION['login_attempts']` dan `$_SESSION['lockout_time']`) tanpa membebani kueri basis data (*in-memory rate-limiting protection*).

#### [FR-AUTH-002] Google Single Sign-On (SSO) Pre-Registered Whitelist & OIDC Verification
* **User Story:** Sebagai civitas akademika PNJ, saya ingin masuk ke sistem menggunakan tombol Google SSO akun kampus (@pnj.ac.id), sehingga proses autentikasi lebih cepat, aman, dan tanpa menghafal password baru.
* **Integrasi Pustaka (Library Stack):** Pustaka resmi **`google/apiclient` (Google API Client for PHP v2.15+)** diinstal melalui Composer (`composer require google/apiclient:^2.15`).
* **Mekanisme & Alur Protokol (Authorization Code Flow with OpenID Connect):**
  1. *Inisiasi Alur SSO:* Pengguna mengklik tombol "Masuk dengan Google SSO". Sistem menginisialisasi objek `Google\Client`, menetapkan Client ID (`GOOGLE_CLIENT_ID`), Client Secret (`GOOGLE_CLIENT_SECRET`), Redirect URI (`GOOGLE_REDIRECT_URI`), scope OpenID Connect (`openid`, `email`, `profile`), serta parameter `state` berupa token acak aman kriptografi (CSRF token) yang disimpan pada `$_SESSION['oauth2_state']`. Pengguna kemudian diarahkan ke Authorization Server Google.
  2. *Callback & Pertukaran Authorization Code:* Setelah pengguna mengotorisasi akun kampus, Google mengalihkan peramban ke `GOOGLE_REDIRECT_URI` dengan membawa parameter `code` dan `state`. Sistem memvalidasi kesesuaian nilai `state` guna mengantisipasi serangan CSRF. Selanjutnya, backend mengeksekusi metode `$client->fetchAccessTokenWithAuthCode($code)` untuk menukarkan *authorization code* sementara menjadi kumpulan token via HTTP POST ke endpoint resmi `https://oauth2.googleapis.com/token`.
  3. *Ekstraksi & Kriptografis Verifikasi JWT ID Token:*
     * Dari respon token yang diterima, backend mengekstrak JWT ID Token (`$id_token = $token['id_token']`).
     * Backend mengeksekusi verifikasi integritas tanda tangan digital token secara menyeluruh menggunakan fungsi bawaan library: `$payload = $client->verifyIdToken($id_token)`.
     * Library secara otomatis mengunduh dan memeriksa public keys resmi Google (Google Certificates JWKS dari `https://www.googleapis.com/oauth2/v3/certs`) serta memverifikasi klaim standar:
       - **Audience (`aud`):** Memastikan nilai klaim audiens identik dengan `GOOGLE_CLIENT_ID` aplikasi Tandain.
       - **Issuer (`iss`):** Memastikan penerbit token adalah `https://accounts.google.com` atau `accounts.google.com`.
       - **Expiration Time (`exp`):** Memastikan stempel waktu masa kedaluwarsa token belum terlampaui.
       - **Hosted Domain (`hd`):** Memverifikasi klaim domain email institusi kampus (contoh: `pnj.ac.id` atau domain civitas yang disetujui).
  4. *Pencocokan Direktori Pengguna (Database Whitelist Enforcement):*
     * Backend mengekstrak identitas email civitas terverifikasi (`$payload['email']`) dan Google Subject ID (`$payload['sub']`).
     * Melalui driver `PDO_PGSQL`, sistem menjalankan query terparameterisasi ke tabel `users` PostgreSQL:
       `SELECT id, role_id, full_name, email, status FROM users WHERE email = :email LIMIT 1`
* **Kriteria Penerimaan (Acceptance Criteria):**
  1. *Given* pengguna mengklik tombol "Masuk dengan Google SSO", *When* proses autentikasi OIDC sukses dan email civitas akademika **sudah terdaftar dan berstatus `ACTIVE`** di tabel `users`, *Then* sistem meregenerasi ID sesi (`session_regenerate_id(true)`), menyimpan data profil pengguna ke sesi web (`$_SESSION['user']`), dan mengarahkan pengguna ke dasbor peran masing-masing.
  2. *Given* pengguna mengklik tombol "Masuk dengan Google SSO", *When* proses OIDC berhasil namun email Google yang digunakan (misal: `@gmail.com` pribadi atau email kampus luar) **tidak terdaftar** pada tabel `users`, *Then* sistem memutus sesi seketika, menolak hak masuk, mencatat insiden akses ilegal pada log audit peladen, dan menampilkan pesan kegagalan: *"Akses Ditolak: Alamat email [email] belum terdaftar dalam direktori civitas PNJ TIK. Silakan hubungi Administrator Tata Usaha untuk pendaftaran akun."*
  3. *Given* terjadi manipulasi parameter `state` atau pemalsuan tanda tangan token JWT ID Token dari pihak ketiga yang mencurigakan, *When* diverifikasi oleh `$client->verifyIdToken()`, *Then* sistem melempar eksepsi keamanan `InvalidArgumentException`, membatalkan seluruh transaksi, dan merender galat otorisasi fatal 403 Forbidden.

---

### 3.2 Modul Manajemen Master Template Surat Baku

#### [FR-TMPL-001] Pembuatan Master Template oleh Admin TU (Fixed Kop Wrapper & Body-Only Builder)
* **User Story:** Sebagai Admin TU, saya ingin membuat master template naskah dinas dengan hanya mengisi teks badan surat dan menyisipkan tag placeholder (tanpa perlu mendesain kop surat, logo, atau footer dari awal), sehingga format persuratan jurusan selalu baku, seragam, dan tidak rusak oleh modifikasi manual.
* **Kriteria Penerimaan:**
  1. **Fixed Layout Master Wrapper (Kop Surat & Footer Baku):**
     * Sistem menerapkan *layout wrapper* permanen terstandar (`templates/layouts/official_letter_kop.html.php`) yang memuat Kop Surat Resmi Politeknik Negeri Jakarta (Logo PNJ, teks kop kementerian/direktorat/jurusan, alamat resmi kampus Kukusan UI Depok, dan garis pemisah ganda kop) serta Footer legalitas dinas.
     * Admin TU **TIDAK PERLU dan TIDAK BOLEH** menulis ulang kode HTML Kop Surat pada setiap template baru; sistem otomatis membungkus teks badan surat ke dalam master wrapper tersebut.
  2. **Isian Form Builder Admin TU:** Admin TU cukup menginputkan:
     * Informasi Umum: Judul Template (misal: *"Surat Keterangan Mahasiswa Aktif"*), Kode Template Unik (misal: `SK-MHS-AKTIF`), dan Deskripsi Petunjuk.
     * Naskah Badan Surat (*Body HTML Content*): Paragraf pembuka, pernyataan isi, dan penutup yang disisipi tag variabel `{{...}}`.
     * Skema Isian Pemohon (*Dynamic Form Fields Schema* - format `JSONB`): Daftar field variabel apa saja yang wajib diisi oleh mahasiswa saat mengajukan surat (misal: field `keperluan` bertipe string, `nama_instansi_tujuan` bertipe string, dsb).
  3. **Taksonomi & Klasifikasi Tag Placeholder:**
     * **Kategori A: Variabel Sistem Otomatis (*Auto-Populated from User Profile*):**
       - `{{nama_lengkap}}` — otomatis diambil dari data akun login `users.full_name`.
       - `{{nomor_induk}}` / `{{nim}}` — otomatis diambil dari `users.identity_number`.
       - `{{program_studi}}` — otomatis diambil dari profil/posisi civitas.
       - `{{email}}` — otomatis diambil dari `users.email`.
       *(Catatan: Mahasiswa pemohon tidak mengetikkan data ini secara manual untuk mencegah kesalahan ketik dan manipulasi identitas).*
     * **Kategori B: Variabel Isian Dinamis (*Requester Form Inputs*):**
       - `{{keperluan}}`, `{{semester_aktif}}`, `{{nama_instansi}}`, dsb — diisi oleh mahasiswa pemohon melalui antarmuka formulir pengajuan web dinamis sesuai skema template yang dipilih.
     * **Kategori C: Variabel Administrasi & TTE (*System Generated & Stamping Slots*):**
       - `{{nomor_surat}}` — nomor agenda dinas resmi yang ditetapkan oleh Admin TU (atau ter-generate saat disposisi disetujui).
       - `{{tanggal_surat}}` — tanggal penerbitan naskah kedinasan (`issued_at`).
       - `{{kolom_ttd_1}}`, `{{kolom_ttd_2}}`, `{{kolom_ttd_3}}` — wadah penempatan stempel tanda tangan visual para pejabat penandatangan. Di dalam naskah HTML DomPDF, tag ini berfungsi sebagai wadah penampung tata letak visual (*visual spacing box*), sedangkan titik koordinat kartesian absolut `(coord_x, coord_y, page_number)` untuk penempelan stempel fisik oleh FPDI didefinisikan secara presisi pada konfigurasi skema JSONB template (`placeholders_schema`).
       - `{{qr_verifikasi}}` — wadah stempel kode QR yang menautkan langsung ke portal verifikasi publik dokumen.
  4. Sistem memvalidasi sintaks naskah dan ketersediaan minimal 1 slot penandatangan (`{{kolom_ttd_1}}`) sebelum disimpan ke tabel `document_templates`.

#### [FR-TMPL-002] Render Dokumen Otomatis Menggunakan DomPDF
* **User Story:** Sebagai sistem, saya harus mengonversi data formulir pengajuan pemohon ke dalam berkas PDF berstandar A4 menggunakan engine DomPDF dengan menggabungkan master wrapper kop surat dan badan naskah, sehingga dokumen siap ditandatangani tanpa pergeseran tata letak.
* **Kriteria Penerimaan:**
  1. Engine DomPDF menyuntikkan teks badan template ke dalam master wrapper kop surat resmi PNJ, kemudian me-replace seluruh placeholder `{{...}}` dengan data profil pemohon dan data isian formulir.
  2. Luaran biner PDF memiliki ukuran margin resmi (Kiri: 4cm, Atas: 3cm, Kanan: 3cm, Bawah: 3cm) dan menggunakan tipografi Times New Roman.
  3. Dokumen hasil render disimpan sebagai berkas awal (`original_file_path`) dan siap dialirkan ke alur perutean penandatanganan.

---

### 3.3 Modul Pengajuan Dokumen (Document Submission)

#### [FR-DOC-001] Pilihan Jalur Pengajuan (Unggah PDF Mandiri vs Template Surat)
* **User Story:** Sebagai pemohon (*Requester*), saya ingin memilih apakah akan mengajukan surat resmi dari template baku jurusan atau mengunggah berkas PDF mandiri (seperti proposal skripsi/laporan magang), sehingga sistem fleksibel untuk segala jenis dokumen.
* **Kriteria Penerimaan:**
  1. Jalur A (Template): Pemohon mengisi formulir web dinamis sesuai isian template yang dipilih.
  2. Jalur B (Unggah Mandiri): Pemohon mengunggah berkas `.pdf` dengan ukuran maksimal 10 MB. Sistem memverifikasi *magic bytes* berkas (`%PDF-`) untuk mencegah unggahan berkas berbahaya.

#### [FR-DOC-002] Penentuan Slot Tanda Tangan pada Berkas PDF Mandiri
* **User Story:** Sebagai pemohon yang mengunggah PDF mandiri, saya ingin menentukan halaman dan koordinat (X, Y) letak tanda tangan para pejabat penandatangan, sehingga stempel visual tertempel rapi di tempat yang sesuai.
* **Kriteria Penerimaan:**
  1. Antarmuka menampilkan PDF viewer berbasis PDF.js.
  2. Pemohon dapat mengklik posisi pada halaman tertentu untuk meletakkan kotak penanda tangan (Signer 1, Signer 2, dst.). Koordinat piksel dinormalisasi ke satuan milimeter/poin standar PDF FPDI.

---

### 3.4 Modul Mekanisme Perutean (Routing) & Disposisi

#### [FR-ROUTE-001] Direct Routing & Pencarian Penandatangan Berbasis Email Resmi
* **User Story:** Sebagai pemohon yang sudah mengetahui secara pasti siapa dosen pembimbing, pimpinan, atau rekan mahasiswa yang berwenang, saya ingin mencari target penandatangan secara cepat dan akurat menggunakan alamat email resmi kampus (tanpa dropdown lambat berisi ribuan nama), sehingga daftar urutan penandatangan dapat disusun secara presisi.
* **Kriteria Penerimaan:**
  1. **Larangan Dropdown Statis Massal:** Sistem **DILARANG** menggunakan elemen `<select>` dropdown konvensional yang memuat seluruh ribuan nama civitas kampus demi mencegah beban rendering DOM, lag peramban, dan kebocoran direktori pengguna.
  2. **Pencarian Berbasis Email Resmi Kampus (`@pnj.ac.id` / `@stu.pnj.ac.id`):** Kolom input pencarian penandatangan berorientasi pada alamat email institusional atau nama pengguna unik.
  3. **Mekanisme Pencarian Ganda (Debounced Auto-Search & Tombol Cari Eksplisit):**
     * **Mode Otomatis (Debounce):** Ketika pemohon mengetikkan alamat email pada kolom input, sistem menunda eksekusi panggilan asinkron (*debounced delay* 500ms - 800ms setelah henti ketik) sebelum mengirim kueri ke endpoint `/api/users/search`.
     * **Mode Tombol Cari Eksplisit:** Disediakan tombol fisik "Cari" (ikon 🔍) serta penanganan penekanan tombol `Enter` pada kibor untuk langsung mengeksekusi pencarian seketika tanpa menunggu timer delay.
  4. **Penyajian Hasil Pencarian (*Signer Preview Card*):**
     * Hasil kueri menampilkan kartu pratinjau yang mencakup: Nama Lengkap & Gelar, Alamat Email Resmi, Nomor Induk (NIP/NIM), Jabatan Struktural (`position`), dan Badge Status Sertifikat TTE (`Aktif` atau `Belum Menerbitkan Sertifikat`).
     * Pemohon mengklik tombol aksi *"Tambahkan"* untuk memasukkan pengguna ke dalam antrean alur penandatanganan (Signer Level 1, Signer Level 2, dst.).
  5. **Penanganan Validasi:** Jika email tidak ditemukan dalam pangkalan data kampus, sistem menampilkan status informatif: *"Civitas dengan email tersebut belum terdaftar dalam sistem Tandain"*.
  6. **Aktivasi Alur:** Setelah berkas diajukan, status dokumen berubah menjadi `IN_PROGRESS` dan langsung dialirkan ke antrean Signer Level 1.

#### [FR-ROUTE-002] Self-Signing (Penandatanganan Mandiri)
* **User Story:** Sebagai dosen atau ketua ormawa yang ingin merilis pengumuman resmi atau surat rekomendasi yang hanya membutuhkan tanda tangan saya sendiri, saya ingin memilih mode Self-Signing, sehingga proses selesai dalam satu langkah.
* **Kriteria Penerimaan:**
  1. Sistem menetapkan akun pemohon sekaligus sebagai Signer tunggal (Level 1).
  2. Setelah dokumen dibuat, pemohon langsung diarahkan ke modal eksekusi tanda tangan.

#### [FR-ROUTE-003] Triage Desk Jurusan (Bantuan Disposisi oleh Staf Tata Usaha)
* **User Story:** Sebagai mahasiswa yang bingung siapa pejabat yang berwenang menandatangani permohonan spesifik saya, saya ingin dapat memilih opsi "Bantuan Disposisi Tata Usaha", sehingga staf TU dapat menelaah dan menentukan rantai penandatangan yang sah.
* **Kriteria Penerimaan:**
  1. Dokumen masuk ke tabel antrean disposisi dengan status `PENDING_TRIAGE`.
  2. Staf Tata Usaha menerima notifikasi antrean baru di dasbor TU.
  3. Staf TU memeriksa lampiran dan tujuan surat, kemudian menentukan:
     * Menetapkan daftar urutan penandatangan (Level 1..N).
     * Atau menolak pengajuan dengan memberikan catatan penolakan formal (*rejection reason*).
  4. Setelah staf TU mengonfirmasi disposisi, status dokumen berubah menjadi `IN_PROGRESS` dan diteruskan ke Penandatangan Level 1.

---

### 3.5 Modul Penandatanganan Berantai (Sequential Signing Multi-Level)

```
[Draf PDF] --> (Signer 1 Input Passphrase) --> [PDF v1 + Hash 1] 
           --> (Signer 2 Input Passphrase) --> [PDF v2 + Hash 2] 
           --> [Dokumen Final Tersegel Kriptografis]
```

#### [FR-SIGN-001] Eksekusi Penandatanganan Berurutan
* **User Story:** Sebagai penandatangan level berikutnya, saya hanya dapat menandatangani dokumen setelah penandatangan di level sebelumnya menyelesaikan tanda tangannya, sehingga urutan birokrasi terpelihara secara ketat.
* **Kriteria Penerimaan:**
  1. Penandatangan Level $N$ tidak dapat melihat tombol aksi tanda tangan selama status Signer Level $N-1$ masih `WAITING` atau `CURRENT_TURN`.
  2. Setelah Signer $N-1$ menandatangani, sistem otomatis mengirimkan notifikasi email dan membuka akses penandatanganan bagi Signer Level $N$.
  3. Dokumen hanya dinyatakan berstatus `COMPLETED` apabila seluruh penandatangan dalam rantai telah menyelesaikan proses tanda tangan.

---

### 3.6 Modul Manajemen Kunci & Sertifikat TTE

#### [FR-KEY-001] Kebijakan Sertifikat Tunggal Aktif (1 User = 1 Active Certificate)
* **User Story:** Sebagai pengguna sistem Tandain, saya ingin memiliki satu sertifikat digital aktif yang dikelola aman oleh sistem, sehingga seluruh tanda tangan saya terikat pada identitas digital yang valid.
* **Kriteria Penerimaan:**
  1. Setiap akun pengguna memiliki maksimal **1 sertifikat berstatus `ACTIVE`** pada tabel `signer_certificates`.
  2. Sertifikat memiliki masa berlaku standar selama **1 (satu) tahun** sejak tanggal penerbitan.
  3. Apabila pengguna menerbitkan sertifikat baru (misal karena lupa passphrase lama atau pergantian jabatan), sertifikat lama secara otomatis diubah statusnya menjadi `REVOKED`. Dokumen-dokumen lama yang telah ditandatangani saat sertifikat tersebut masih `ACTIVE` tetap sah secara hukum (*retroactive validity*).

---

### 3.7 Modul Otorisasi Tanda Tangan (Passphrase Protected)

#### [FR-AUTHZ-001] Otorisasi Eksekusi Tanda Tangan via Passphrase Kunci Privat
* **User Story:** Sebagai penandatangan, saya wajib memasukkan *passphrase* sertifikat saya setiap kali menandatangani dokumen, sehingga pihak lain yang meminjam laptop atau sesi login saya tidak dapat menyalahgunakan wewenang tanda tangan saya.
* **Kriteria Penerimaan:**
  1. Passphrase sertifikat **berbeda** dan terpisah secara tegas dari password login akun.
  2. Peladen mencoba mendekripsi berkas kunci privat pengguna (`encrypted_private_key`) di memori menggunakan passphrase yang diinputkan:
     * Jika benar: Kunci privat terbuka di RAM sementara, proses `openssl_sign` dieksekusi, bita memori kunci privat segera dihapus (*zeroized*), dan proses penandatanganan berhasil.
     * Jika salah: Sistem menghentikan eksekusi, melempar pesan galat *"Passphrase Sertifikat Tidak Valid"*, dan mencatat insiden gagal otorisasi di log audit.

---

### 3.8 Modul Visual Stamping (Stempel Visual & Tata Letak)

#### [FR-STAMP-001] Konfigurasi Mode Tampilan Visual Stempel
* **User Story:** Sebagai penandatangan, saya ingin dapat memilih format stempel visual yang dibubuhkan pada dokumen (QR Code saja, Gambar TTD saja, atau Dual Display), sehingga sesuai dengan konvensi naskah dinas yang berlaku.
* **Kriteria Penerimaan:**
  1. **Mode 1 (QR Only):** Hanya membubuhkan kode QR berukuran 25x25 mm yang memuat URL verifikasi publik unik.
  2. **Mode 2 (Signature Only):** Hanya membubuhkan gambar grafis tanda tangan pengguna berlatar belakang transparan.
  3. **Mode 3 (Dual Display - Default):** Membubuhkan kotak gabungan berisi kode QR di sisi kiri, gambar goresan tanda tangan di sisi kanan, serta teks metadata di bawahnya (*"Ditandatangani secara elektronik oleh: [Nama], [NIP], pada [Waktu UTC+7]"*).

---

### 3.9 Modul Validitas Dokumen vs Masa Aktif Sertifikat

#### [FR-EXP-001] Pemisahan Status Integritas Kriptografis vs Status Administratif
* **User Story:** Sebagai penguji dokumen publik, saya ingin sistem membedakan secara tegas antara integritas fisik berkas (asli/palsu) dengan status masa berlaku administrasi dokumen (aktif/kadaluwarsa), sehingga tidak terjadi kebingungan interpretasi hukum.
* **Kriteria Penerimaan:**
  1. **Prinsip Non-Repudiation Retroaktif:** Sebuah dokumen dinyatakan **Sah secara Kriptografi** apabila tanda tangan dibubuhkan saat sertifikat penandatangan berada dalam rentang `valid_from` s.d. `valid_until`, kendati sertifikat tersebut sudah kadaluwarsa saat dokumen diperiksa di masa depan.
  2. **Masa Aktif Administratif Dokumen:** Kolom `expires_at` pada tabel `documents` menentukan relevansi izin surat (misal: Surat Izin Cuti atau Surat Keterangan Aktif Kuliah hanya berlaku selama 1 semester / 6 bulan).
  3. Jika tanggal verifikasi melebihi `expires_at`, sistem mengeluarkan status: **"Dokumen Asli / Sah secara Kriptografi, Namun Masa Berlaku Administratif Telah Berakhir (Expired)"**.

---

### 3.10 Modul Manajemen Dokumen Diunggah & Pratinjau Detail

#### [FR-DOC-003] Repositori Dokumen Diunggah (Unduh Original vs Signed PDF)
* **User Story:** Sebagai pemohon pengunggah dokumen, saya ingin dapat memantau progres tanda tangan secara visual serta mengunduh berkas versi asli (*Original*) dan berkas hasil pengesahan (*Signed*), sehingga saya memiliki arsip digital yang transparan untuk kedua versi berkas.
* **Kriteria Penerimaan:**
  1. Halaman `/documents/uploaded` menampilkan tabel daftar dokumen yang memuat: Judul Dokumen, Tanggal & Jam Pengunggahan, Indikator Rasio Penandatanganan (grafik *progress bar* hijau, misal: `1/1`, `1/2`, `2/2`), Jumlah Penolakan, dan Tombol Aksi.
  2. **Tombol Unduh `Original`:** Mengunduh berkas PDF mentah awal (`original_file_path`) sebelum ada stempel tanda tangan apa pun.
  3. **Tombol Unduh `Signed`:** Mengunduh berkas PDF final (`final_file_path`) yang telah tersegel stempel visual TTD/QR dan tanda tangan kriptografis.
  4. **Tombol Aksi `Detail`:** Mengarahkan pengguna langsung ke halaman telaah detail dokumen dan jejak audit.
  5. **Tombol Aksi `Batalkan / Hapus`:**
      - Jika dokumen masih berupa draf (`DRAFT`) atau belum ada penandatangan yang bertandatangan: Pemohon dapat menghapus draf secara permanen (*Hard Delete*).
      - Jika minimal 1 penandatangan telah menandatangani dokumen: Tombol aksi berubah menjadi *"Batalkan Pengajuan"*. Sistem melakukan pembatalan lunak (*Soft-Cancellation* dengan mengubah status dokumen dan sisa antrean menjadi `CANCELLED`) guna melindungi bukti jejak audit kriptografis yang telah dibuat agar tidak hilang.

#### [FR-DOC-004] Halaman Detail Dokumen, Pratinjau Tersemat, dan Jejak Riwayat TTD
* **User Story:** Sebagai pengguna sistem, saya ingin membuka halaman detail dokumen untuk melihat pratinjau lembar surat yang telah dibubuhi tanda tangan serta memeriksa riwayat persetujuan setiap pejabat secara rinci, sehingga keabsahan dan progres dokumen dapat ditelaah per-individu.
* **Kriteria Penerimaan:**
  1. Header halaman menampilkan metadata: Judul Dokumen, Waktu Pengunggahan & Nama Pengunggah, Progres Persetujuan, dan Penanda Aturan Alur (**"Harus Berurutan?: Ya / Tidak"** berbasis kolom `is_sequential BOOLEAN`).
  2. Menyematkan *Embedded PDF Canvas Viewer* (Mozilla PDF.js) di area tengah halaman yang merender berkas dokumen lengkap dengan visual goresan tanda tangan resmi atau kode QR yang telah ditempelkan.
  3. Menyajikan **Tabel Jejak Penanda Tangan** di bagian bawah pratinjau yang mencakup:
     * Nama dan Jabatan Penandatangan.
     * Status Persetujuan (Badge hijau `Setuju` atau badge merah `Menolak`).
     * Waktu Ditandatangani presisi (`✓ [Hari, Tanggal - Jam UTC+7]`).
     * Keterangan evaluasi atau catatan revisi dari penandatangan.

---

### 3.11 Modul Portal Verifikasi Publik (Public Verification Portal)

#### [FR-VERIF-001] Verifikasi Melalui Pemindaian Kode QR
* **User Story:** Sebagai penerima dokumen fisik hasil cetak, saya ingin memindai kode QR pada dokumen menggunakan kamera telepon genggam tanpa perlu mengunduh aplikasi atau membuat akun, sehingga keaslian surat dapat diverifikasi seketika.
* **Kriteria Penerimaan:**
  1. Kode QR mengarah ke URL publik: `https://tandain.tik.pnj.ac.id/verify/[document_uuid]`.
  2. Halaman publik menampilkan:
     * Judul dan Nomor Surat Resmi.
     * Nama dan NIM/NIP seluruh penandatangan beserta stempel waktu penandatanganan.
     * Status integritas kriptografi (SHA-256 Digest Match).
     * Tombol pratinjau (*preview*) dokumen versi asli dari peladen kampus untuk dicocokkan dengan dokumen fisik pemegang.

#### [FR-VERIF-002] Verifikasi Melalui Unggah Berkas PDF Mandiri
* **User Story:** Sebagai perusahaan penerima berkas elektronik PDF, saya ingin mengunggah berkas PDF yang saya terima ke portal verifikasi untuk memastikan tidak ada perubahan bita sekecil apa pun di dalamnya.
* **Kriteria Penerimaan:**
  1. Halaman `/verify` menyediakan area *drag-and-drop* berkas PDF.
  2. Sistem menghitung nilai *hash* SHA-256 dari berkas yang diunggah dan mencocokkannya dengan rekaman bita di basis data.
  3. Jika hash cocok sempurna: Menampilkan badge hijau **"DOKUMEN ASLI TERVERIFIKASI"**.
  4. Jika terdapat perbedaan 1 bita saja: Menampilkan peringatan merah bahaya **"PERINGATAN: DOKUMEN TIDAK TERDAFTAR ATAU TELAH MENGALAMI MANIPULASI!"**.

---

## 4. KEBUTUHAN NON-FUNGSIONAL (NON-FUNCTIONAL REQUIREMENTS)

### 4.1 NFR-SEC: Keamanan Informasi & Kriptografi
1. **Algoritma Standar:** Kunci privat dan publik wajib menggunakan algoritma RSA dengan panjang modulus minimal 2048-bit. Fungsi ringkasan pesan wajib menggunakan SHA-256.
2. **Proteksi Kunci di Sisi Peladen (Zero-Plaintext Storage):** Kunci privat tidak boleh disimpan dalam bentuk teks polos (*plaintext*) pada media penyimpanan peladen. Kunci privat wajib dienkripsi simetris menggunakan algoritma AES-256-CBC dengan garam acak (*random salt*) 16-byte dan kunci turunan dari passphrase pengguna.
3. **Session Management & Cookie Security:** Cookie sesi wajib dikonfigurasi dengan atribut:
   * `HttpOnly = true` (mencegah pencurian token sesi via serangan Cross-Site Scripting / XSS).
   * `Secure = true` (hanya dikirim melalui enkripsi TLS/HTTPS).
   * `SameSite = Strict` (mencegah eksploitasi Cross-Site Request Forgery / CSRF).
4. **Pencegahan SQL Injection & Abstraksi Basis Data:** Seluruh interaksi basis data pada lapisan backend PHP Native MVC wajib menggunakan ekstensi **`PDO_PGSQL` dengan Prepared Statements** terparameterisasi secara mutlak. Pemanfaatan tipe data modern PostgreSQL seperti `JSONB` untuk skema formulir template dan jejak audit wajib melalui pengikatan parameter (*parameter binding*) yang aman guna mengeliminasi celah SQL Injection maupun injeksi dokumen JSON.
5. **Sanitasi Masukan:** Seluruh masukan form teks wajib difilter menggunakan fungsi `htmlspecialchars()` dengan opsi `ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'` guna mengeliminasi injeksi skrip HTML/JS.
6. **Keamanan Autentikasi Google SSO (OpenID Connect):** Integrasi Google SSO wajib menggunakan library resmi `google/apiclient` v2.15+ via Composer. Proses verifikasi identitas wajib berbasis validasi kriptografis JWT ID Token (`$client->verifyIdToken()`) terhadap kunci publik resmi Google (JWKS public key rotation), pencegahan *replay attack* dan CSRF via parameter `state`, serta pembatasan ketat whitelist domain civitas kampus PNJ.

### 4.2 NFR-PERF: Kinerja dan Kecepatan Respons Sistem
1. **Waktu Render & Stamping Dokumen:** Proses konversi HTML ke PDF melalui DomPDF dan proses pembubuhan stempel visual via FPDI tidak boleh memakan waktu lebih dari **3,0 detik** untuk dokumen dengan panjang hingga 10 halaman pada spesifikasi server standar (2 vCPU, 4GB RAM).
2. **Latensi Halaman Portal Verifikasi:** Halaman verifikasi publik kode QR wajib merespons dan menampilkan badge hasil validasi dalam waktu kurang dari **1,5 detik** pada koneksi internet seluler pita lebar (4G/LTE).
3. **Penggunaan Memori (Memory Footprint):** Eksekusi *in-memory decryption* kunci privat pada PHP wajib menjaga konsumsi memori per thread di bawah 64 MB dan langsung memicu *garbage collection* setelah penandatanganan selesai.

### 4.3 NFR-USAB: Aksesibilitas & Antarmuka Pengguna
1. **Kepatuhan Kontras WCAG 2.1 Level AA:** Seluruh elemen teks terhadap warna latar belakang pada antarmuka dasbor wajib memiliki rasio kontras visual minimal **4,5 : 1** untuk teks standar dan minimal **3,0 : 1** untuk teks tebal/besar berukuran di atas 18pt.
2. **Desain Nir-Beban Kognitif (*Recognition over Recall*):** Pembuat template tidak boleh dipaksa menghafal tag sintaksis; sistem wajib menyediakan panel contekan (*cheat sheet*) interaktif di samping editor teks yang memungkinkan penyalinan tag placeholder hanya dengan satu klik mouse (*click-to-copy/insert*).
3. **Responsivitas Perangkat:** Seluruh antarmuka portal verifikasi publik dan dasbor persetujuan surat wajib adaptif dan dapat dioperasikan penuh pada layar ponsel pintar (lebar layar mulai dari 360 piksel).

### 4.4 NFR-AVAIL: Ketersediaan & Keandalan (Availability & Reliability)
1. **Target Ketersediaan Sistem:** Ketersediaan operasional peladen ditargetkan mencapai minimal **99,5% Uptime** selama hari dan jam kerja aktif akademik (Senin s.d. Jumat, 07:00 – 18:00 WIB).
2. **Integritas Transaksi Basis Data:** Seluruh operasi penandatanganan berkas, pencatatan log audit, dan pembaruan status dokumen wajib dibungkus dalam blok transaksi relasional PostgreSQL (`BEGIN ... COMMIT / ROLLBACK` melalui antarmuka `PDO::beginTransaction()`, `PDO::commit()`, dan `PDO::rollBack()`) dengan tingkat isolasi transaksi standar ACID (*Read Committed*) guna mencegah ketidakkonsistenan data (*partial state failure*) jika terjadi kegagalan jaringan atau eksepsi sistem di tengah proses.

---

## 5. RUANG LINGKUP SISTEM: IN-SCOPE VS OUT-OF-SCOPE

Guna mencegah melebarnya ekspektasi proyek (*scope creep*) dan memastikan fokus arsitektur pada kehandalan kriptografi kampus, ditetapkan batasan tegas ruang lingkup:

| Parameter Sistem | In-Scope (Termasuk dalam Proyek Tandain) | Out-of-Scope (Tegas di Luar Batasan Proyek) |
| :--- | :--- | :--- |
| **Penyuntingan Dokumen** | 1. Pembuatan surat berbasis Master Template HTML dengan injeksi tag placeholder oleh engine DomPDF.<br>2. Unggah berkas PDF mandiri yang sudah final. | Fitur penyunting dokumen kanvas bebas tingkat lanjut (*WYSIWYG arbitrary PDF canvas editor* ala Canva atau Google Docs online di peramban). |
| **Otoritas Sertifikasi Kriptografi** | Menggunakan Otoritas Sertifikat Internal (*Private/Self-Signed CA*) Jurusan TIK PNJ yang divalidasi oleh Dual Verification Engine Tandain. | Integrasi lisensi berbayar ke skema **Adobe Approved Trust List (AATL)** atau PSrE Berinduk Kominfo nasional. Pita hijau "Signature Valid" di Adobe Acrobat Reader di luar lingkup. |
| **Gerbang Notifikasi (Gateway)** | Notifikasi internal dasbor web dan pengiriman surel otomatis via SMTP Server internal kampus PNJ. | Integrasi SMS Gateway berbayar atau WhatsApp Business API berbayar resmi (Meta Cloud API). |
| **Registrasi Pengguna** | Pendaftaran terkurasi oleh Administrator Tata Usaha atau aktivasi instan melalui Google SSO email domain resmi PNJ. | Registrasi mandiri terbuka untuk umum (*public self-registration* tanpa kurasi). |
| **Penyimpanan Berkas Fisik** | Penyimpanan berkas PDF dan *cryptographic blob* pada direktori sistem berkas peladen lokal terproteksi (*on-premise encrypted storage*). | Integrasi penyimpanan multi-cloud terdistribusi berbayar pihak ketiga (Amazon AWS S3, Google Cloud Storage, dsb.). |

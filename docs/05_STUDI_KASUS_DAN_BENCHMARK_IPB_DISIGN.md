# DOKUMEN 05: STUDI KASUS EMPIRIS & BENCHMARK SISTEM IPB DISIGN
## Analisis Fitur, Antarmuka, dan Validasi Arsitektur untuk Sistem "Tandain"
**Jurusan Teknik Informatika dan Komputer — Politeknik Negeri Jakarta**

---

## 1. PENDAHULUAN & LATAR BELAKANG BENCHMARK

Dokumen ini disusun sebagai **kajian empiris dan analisis perbandingan langsung (*direct benchmark*)** terhadap sistem Tanda Tangan Elektronik perguruan tinggi yang telah beroperasi di lapangan, yaitu **IPB DiSign (IPB University Digital Signature)**. 

Berdasarkan masukan dan rujukan dari dosen peninjau, tangkapan layar nyata antarmuka IPB DiSign digunakan sebagai instrumen validasi kebutuhan (*requirements validation*) untuk membuktikan bahwa keputusan arsitektur yang dirancang pada sistem **Tandain (PNJ TIK)** telah selaras dengan standar dan konvensi persuratan digital kampus di Indonesia.

---

## 2. BEDAH BUKTI EMPIRIS TANGKAPAN LAYAR IPB DISIGN

Terdapat 8 (delapan) tangkapan layar empiris dari sistem produksi IPB DiSign yang memvalidasi keputusan desain arsitektur dan antarmuka sistem Tandain:

---

### 2.1 Tangkapan Layar 1: Manajemen Gambar Tanda Tangan & Paraf Visual

![Manajemen Gambar Tanda Tangan IPB DiSign](assets/ipb_gambar_tanda_tangan.png)

#### A. Temuan Fitur Utama di IPB DiSign:
1. **Dua Jenis Spesimen Visual:** Sistem IPB membedakan antara **Tanda Tangan Penuh** dan **Paraf Singkat**.
2. **Fleksibilitas Input Visual:** Pengguna diberikan dua metode penyediaan gambar tanda tangan:
   * Mengunggah berkas hasil pemindaian (*scan*) format JPG/PNG dengan latar belakang putih bersih atau transparan.
   * Menggambar goresan tanda tangan langsung menggunakan *mouse*, *pen tablet*, atau layar sentuh.
3. **Penyimpanan Spesimen Permanen:** Spesimen tanda tangan yang telah diunggah/digambar disimpan di profil pengguna sehingga tidak perlu dibuat ulang setiap kali menandatangani dokumen baru.

#### B. Analisis Kritis & Keputusan Desain untuk Sistem Tandain PNJ:
* **Penyederhanaan Arsitektur (Pruning Fitur Paraf):**
  * Di IPB DiSign, keberadaan menu "Paraf" merupakan warisan kebiasaan tanda tangan dokumen fisik (kertas puluhan lembar). 
  * Namun dalam arsitektur kriptografi modern **Tandain**, integritas dokumen dijamin penuh secara matematis oleh pasangan kunci RSA 2048-bit dan SHA-256 pada seluruh bita berkas PDF.
  * Memaksa pengguna mengelola dua spesimen terpisah (*tanda tangan* dan *paraf*) terbukti menimbulkan redundansi dan kebingungan pengguna (*cognitive load*).
  * Oleh karena itu, tim Tandain **sengaja menyederhanakan (*pruning*) fitur paraf**: sistem hanya memerlukan **1 (satu) Spesimen Tanda Tangan Resmi Utama** (`signature_image_path`) yang dipadukan dengan QR Code verifikasi. Kolom `initial_image_path` dieliminasi dari basis data Tandain demi menjaga skema tetap ramping (*lean database*).

---

### 2.2 Tangkapan Layar 2: Tata Kelola Sertifikat Digital (Aturan 1 Akun = 1 Sertifikat)

![Sertifikat Digital IPB DiSign](assets/ipb_sertifikat_digital.png)

#### A. Temuan Bukti Otentik (*Smoking Gun Validation*):
Perhatikan teks panduan resmi pada antarmuka IPB DiSign:
> *"Sertifikat digital merupakan identitas Anda di sistem untuk membuat tanda tangan digital. **Anda hanya dapat memiliki satu sertifikat yang aktif dalam satu waktu. Jika Anda membuat sertifikat baru, sertifikat lama akan dinonaktifkan (revoked) secara otomatis.** Anda juga dapat menonaktifkan suatu sertifikat secara manual."*

Teks ini **100% MEMVALIDASI** aturan bisnis yang kita sepakati sebelumnya:
1. **Kebijakan Sertifikat Tunggal Aktif:** Tidak boleh ada lebih dari 1 sertifikat aktif per pengguna dalam satu waktu.
2. **Pencabutan Otomatis (*Auto-Revocation*):** Pembuatan sertifikat baru secara otomatis mencabut wewenang sertifikat lama.
3. **Pencabutan Manual (*Manual Revocation*):** Pengguna memiliki tombol aksi `Revoke` jika merasa kunci privatnya terkompromi/bocor.
4. **Masa Berlaku 1 (Satu) Tahun:**
   * Di tabel terlihat masa aktif: `Selasa, 30 Desember 2025 - Rabu, 30 Desember 2026` (tepat 365 hari).
5. **Struktur Subjek Sertifikat (X.509 Standard):**
   * `Sub: E-faira_ezra@apps.ipb.ac.id, CN=Ezra Faira Azzahra Faisal`
   * `Iss: CN=IPB University, OU=IPB, O=IPB, L=Bogor, S=West Java, C=ID`
   * Memuat atribut: *Serial Number* unik, Waktu Dibuat, dan Badge Status Hijau (`aktif`).

#### B. Penerapan pada Sistem Tandain PNJ:
* **Modul Terkait:** `FR-04` (Manajemen Sertifikat TTE Mandiri) dan Tabel Basis Data `signer_certificates`.
* **Kesesuaian di PostgreSQL:** Kolom `status` (`'ACTIVE'`, `'REVOKED'`, `'EXPIRED'`), `valid_from`, `valid_until`, dan `certificate_serial` pada skema Tandain telah presisi meniru model ini.

---

### 2.3 Tangkapan Layar 3: Unggah Dokumen, Penandatanganan Berurut, dan Multi-TTD

![Unggah Dokumen dan Multi TTD IPB DiSign](assets/ipb_unggah_multi_ttd_urutan.png)

#### A. Temuan Fitur Kunci:
1. **Fitur Pengaturan Urutan TTD (Sequential Signing Toggle):**
   * Terdapat opsi kotak centang (*checkbox*): 
     > ☑️ **"Penandatanganan dokumen harus mengikuti urutan"**
   * Ini membuktikan bahwa sistem TTE kampus **memerlukan toggle fleksibel**: dokumen bisa ditandatangani secara berurutan (Signer 1 lalu Signer 2) ATAU secara paralel (bebas siapa saja duluan yang buka).
2. **Fitur Multi-Penandatangan (Multi-TTD Dinamis):**
   * Antarmuka menyediakan tombol hijau `+` (*Add Signer*) dan tombol merah `-` (*Remove Signer*) untuk menambahkan penandatangan tanpa batasan kaku.
3. **Validasi Model Entitas Terpadu (Unified User & P2P):**
   * Kolom input pencarian penandatangan memuat teks placeholder:
     > 🔍 *"Cari nama mahasiswa atau pegawai..."*
   * **Bukti Sangat Kuat:** Di IPB DiSign, **MAHASISWA DAN PEGAWAI BERADA DALAM SATU DIREKTORI PENCARIAN PENANDATANGAN YANG SAMA!** Hal ini mematahkan asumsi lama bahwa mahasiswa hanya bisa menjadi pemohon; mahasiswa terbukti sah menjadi penandatangan dokumen (misal: pengesahan ormawa, kepanitiaan, atau asisten laboratorium).
4. **Penetapan Nomor Halaman Stempel:**
   * Kolom "No. Halaman" memungkinkan pemohon menentukan di halaman ke berapa stempel penandatangan tersebut harus disematkan.
5. **Akselerator Usability (*Frequently Contacted*):**
---

### 2.4 Tangkapan Layar 4: Repositori Dokumen Diunggah (Unduh Berkas Original & Signed)

![Daftar Dokumen Diunggah IPB DiSign](assets/ipb_daftar_dokumen_diunggah.png)

#### A. Temuan Fitur Utama:
1. **Dukungan Unduh Ganda (Download Original vs Signed):**
   * Di kolom **Aksi**, sistem menyediakan tombol unduh terpisah:
     * 📥 **Tombol `Original` (Biru):** Mengunduh berkas PDF mentah asli sebelum dibubuhi tanda tangan.
     * 📥 **Tombol `Signed` (Biru):** Mengunduh berkas PDF final yang telah tersegel tanda tangan digital kriptografis dan stempel visual.
2. **Indikator Progres Penandatanganan Visual (*Visual Progress Bar*):**
   * Kolom "Penandatanganan" menampilkan grafik batang progres berwarna hijau dengan rasio real-time (misal: `1/1`, `0/0`, `2/2`).
3. **Pencatatan Penolakan (*Rejection Tracking*):**
   * Terdapat kolom khusus "Ditolak" untuk memantau apakah ada penandatangan yang menolak dokumen tersebut beserta jumlahnya.
4. **Tombol Navigasi Cepat:**
   * Tombol **`Detail`** untuk membuka audit trail dan pratinjau dokumen.
   * Tombol merah **`Hapus`** untuk membatalkan/menghapus draf dokumen pengajuan.

#### B. Penerapan pada Sistem Tandain PNJ:
* **Modul Terkait:** `FR-10` (Pengunduhan Dokumen) dan Tabel Basis Data `documents` (kolom `original_file_path` dan `final_file_path`).

---

### 2.5 Tangkapan Layar 5: Halaman Detail Dokumen, Pratinjau Tersemat, dan Jejak Audit

![Detail Dokumen dan Pratinjau IPB DiSign](assets/ipb_detail_dokumen_preview.png)

#### A. Temuan Fitur Utama:
1. **Header Metadata Komprehensif:**
   * Judul Dokumen (misal: *Transkrip Semester 2 J0403251030*).
   * Tanggal & Jam Pengunggahan beserta nama inisiator pengunggah (*Oleh: Ezra Faira Azzahra Faisal*).
   * Batang Progres Penandatanganan (`1/1`).
   * Indikator Aturan Alur: **"Harus Berurutan?: Tidak"** (atau "Ya" jika mode sequential aktif).
2. **Pratinjau Dokumen Tersemat (*Embedded PDF Canvas Viewer*):**
   * Di bagian tengah halaman, sistem menyematkan penampil PDF terintegrasi (berbasis PDF.js) yang menampilkan lembar dokumen lengkap dengan goresan tanda tangan visual yang telah tertempel rapi di lembar surat.
3. **Tabel Jejak Penandatanganan (*Signer Audit Table*):**
   * Di bawah pratinjau, disajikan tabel riwayat penandatangan:
     * **Nama Penandatangan:** (misal: *Dr. Utami Dyah Syafitri S.Si., M.Si.*).
     * **Status Persetujuan:** Badge hijau bertuliskan `setuju`.
     * **Waktu Ditandatangani:** Tercatat stempel waktu presisi dengan ikon centang: `✓ Senin, 28 September 2026 - 14.53.38`.
     * **Keterangan:** Kolom catatan evaluasi atau alasan penolakan.

---

### 2.6 Tangkapan Layar 6: Menu "Dokumen Ditandatangani" (Arsip Wewenang Pengguna)

![Daftar Dokumen Ditandatangani IPB DiSign](assets/ipb_daftar_dokumen_ditandatangani.png)

#### A. Temuan Fitur Utama & Pembuktian Logika Alur:
1. **Dua Sumber Dokumen di Menu Ditandatangani:**
   Tangkapan layar ini membuktikan 100% hipotesis kita bahwa halaman ini menampung **seluruh dokumen yang telah diteken oleh pengguna**, yang berasal dari dua skenario:
   * **Skenario A (Pengajuan Orang Lain):** Perhatikan **Baris No. 7 (*Surat Pernyataan Hak Cipta*)**:
     * Kolom Upload: `Rabu, 11 Februari 2026 | Oleh: FARRAS RAHMAN HAKIM` (Diunggah oleh orang lain).
     * Kolom Ditandatangani: `Rabu, 11 Februari 2026 - 22.53.07`.
     * Kolom Penandatanganan: Menampilkan progres `4/5` (ada 5 penandatangan, 4 sudah meneken termasuk akun Ezra, tersisa 1 orang lagi).
   * **Skenario B (Pengajuan Mandiri / Self-Signed):** Pada Baris 1 s.d. 6 dan 8 s.d. 10:
     * Kolom Upload tertulis: `Oleh: Ezra Faira Azzahra Faisal` (Pemohon sekaligus bertindak sebagai salah satu penandatangan).
2. **Pencatatan Nomor Seri Sertifikat Pengesah:**
   * Terdapat kolom **"Sertifikat"** yang mencantumkan nomor seri sertifikat digital yang digunakan oleh penandatangan saat mengeksekusi tanda tangan (misal: `104C0BA6F5CF110F`). Ini menjadi bukti jejak audit kriptografis yang sangat kuat.
3. **Penyediaan Akses Unduh Cepat:**
   * Tombol biru **`Unduh PDF`** memungkinkan penandatangan mengunduh salinan berkas resmi yang telah ia sahkan secara langsung.

#### B. Penerapan pada Sistem Tandain PNJ:
* **Perbedaan Mendasar Dua Menu di Sidebar Tandain:**
  * **Menu "Dokumen Diunggah":** Khusus untuk dokumen di mana pengguna bertindak sebagai **Pemohon (*Requester*)** $\rightarrow$ Kebutuhannya: memantau progres dan mengunduh berkas *Original* (mentah) vs *Signed* (tersegel).
  * **Menu "Dokumen Ditandatangani":** Khusus untuk dokumen di mana pengguna bertindak sebagai **Penandatangan (*Signer*)** $\rightarrow$ Kebutuhannya: arsip pertanggungjawaban hukum (*non-repudiation archive*) yang mencatat sertifikat mana yang dipakai dan kapan diteken.

---

### 2.7 Tangkapan Layar 7: Bukti Visual Stempel Ganda Dosen & Mahasiswa Berdampingan

![Visual Stempel Dual Dosen dan Mahasiswa Berdampingan](assets/ipb_detail_dokumen_dual_ttd_mahasiswa_dosen.png)

#### A. Temuan Bukti Visual Nyata (*Visual Proof*):
Perhatikan dokumen proposal bisnis pada lembar pratinjau canvas:
1. **Sisi Kiri (Pejabat / Dosen):**
   * Jabatan: *"Menyetujui, Ketua Program Studi"*
   * Format Stempel: Kode QR di kiri berdampingan dengan coretan paraf/tanda tangan di kanan.
   * Nama & NIDN: **Dr. Farida Laila, S.Si., M.Si. (NIDN: 0003117611)**.
2. **Sisi Kanan (Mahasiswa Pengaju):**
   * Jabatan: *"Ketua Kelompok"*
   * Format Stempel: Kode QR di kiri berdampingan dengan coretan tanda tangan di kanan.
   * Nama & NIM: **Siti Fathiyyah Musiyam (NIM: J0412251002)**.

#### B. Kesimpulan Validasi Arsitektur:
Tangkapan layar ini adalah **bukti telak dan tak terbantahkan**:
* Mahasiswa dan Dosen dapat saling membubuhkan stempel digital TTE pada satu lembar dokumen yang sama secara berdampingan.
* Opsi stempel **Dual Display (QR Code + Coretan Tanda Tangan)** adalah konvensi visual resmi yang digunakan dalam proposal dan naskah dinas perguruan tinggi.

---

### 2.8 Tangkapan Layar 8: Pembuatan Sertifikat Baru & Ketentuan Khusus Passphrase

![Pembuatan Sertifikat Baru dan Aturan Passphrase IPB DiSign](assets/ipb_buat_sertifikat_baru_passphrase.png)

#### A. Temuan Bukti Otentik (*Smoking Gun Validation*):
Perhatikan seluruh rincian pada kotak informasi resmi (*callout box*) dan formulir halaman *Buat Sertifikat Baru*:
1. **Passphrase Tidak Dapat Di-Reset (Karakteristik Kriptografi Murni):**
   > *"Passphrase sertifikat digunakan setiap kali Anda akan menandatangani dokumen dengan sertifikat ini. Harap menyimpan passphrase ini dengan baik karena tidak dapat di-reset, tetapi harus dibuat sertifikat baru."*
   * **Validasi Kritis:** Sistem tidak mengizinkan mekanisme *"Lupa Passphrase"* atau reset password biasa. Hal ini membuktikan secara nyata bahwa *private key* dienkripsi secara simetris di peladen menggunakan *passphrase* milik pengguna (*zero-knowledge / zero-plaintext private key*). Jika *passphrase* hilang, kunci privat tidak dapat didekripsi lagi oleh siapapun (termasuk administrator basis data), sehingga satu-satunya solusi pemulihan adalah mencabut (*revoke*) sertifikat lama dan menerbitkan pasangan kunci baru.
2. **Pemisahan Tegas antara Passphrase TTE dan Password Login Akun:**
   > *"Jangan menggunakan passphrase yang sama dengan password login IPB"*
   * **Validasi Kritis:** Sistem secara eksplisit melarang atau memperingatkan pengguna agar tidak menyamakan *passphrase* TTE dengan *password* login/SSO portal kampus. Ini merupakan implementasi nyata dari prinsip pertahanan berlapis (*defense-in-depth* / *two-factor authority*). Jika akun login pengguna tertinggal dalam kondisi aktif di komputer laboratorium atau sesi web dibajak, pelaku tetap **tidak dapat memalsukan tanda tangan digital** karena terhalang oleh *passphrase* kunci privat.
3. **Ketentuan Kompleksitas Passphrase:**
   * Panjang minimal 4 karakter (kombinasi huruf kecil non-kapital dan angka).
   * Disediakan tombol intip (*eye icon show/hide*) untuk meminimalisasi kesalahan ketik saat inisialisasi sertifikat.
4. **Masa Berlaku Default (365 Hari / 1 Tahun):**
   * Field input angka *"Berlaku: [ 365 ] hari dari sekarang"* mengonfirmasi secara gamblang bahwa standar masa hidup sertifikat digital civitas akademika adalah tepat 1 tahun kalender (365 hari).

#### B. Penerapan pada Sistem Tandain PNJ:
* **Modul Terkait:** `FR-04` (Penerbitan Sertifikat Mandiri), `FR-07` (Otorisasi Passphrase Kunci Privat), dan Bab Arsitektur Kriptografi di Dokumen 02.
* **Kesesuaian di PHP Native & PostgreSQL:**
  * Pembangkitan pasangan kunci via `openssl_pkey_new(['private_key_bits' => 2048])`.
  * Enkripsi kunci privat menggunakan fungsi `openssl_pkey_export($res, $privKey, $passphrase, ['cipher' => OPENSSL_CIPHER_AES_256_CBC])`.
  * Peladen Tandain **hanya menyimpan ciphertext kunci privat** di kolom `encrypted_private_key` (tabel `signer_certificates`). Peladen tidak pernah menyimpan *passphrase* pengguna, sehingga konsekuensi *"tidak dapat di-reset, harus buat sertifikat baru jika lupa"* terimplementasi secara presisi dan aman.

---

## 3. MATRIKS PERBANDINGAN: IPB DISIGN VS TANDAIN PNJ

Tabel berikut membandingkan karakteristik sistem IPB DiSign dengan sistem **Tandain** yang kita kembangkan untuk Jurusan TIK PNJ:

| Parameter Sistem | IPB DiSign (IPB University) | Tandain (Jurusan TIK PNJ) | Keunggulan & Justifikasi Tandain |
| :--- | :--- | :--- | :--- |
| **Ketergantungan Eksternal** | Terintegrasi resmi dengan **BSrE BSSN** (*Pemerintah*). | **Mandiri / Sovereign (*Self-Hosted*)** via modul PHP OpenSSL internal kampus. | **Bebas Biaya & Mandiri:** Tandain tidak memerlukan biaya operasional atau pendaftaran sertifikasi PSrE eksternal; ideal untuk persuratan internal kampus vokasi. |
| **Pilihan Alur Pengajuan** | Hanya **Unggah Berkas PDF Mandiri** (Draft dibuat manual di Word lalu diunggah). | **Dua Jalur:**<br>1. Master Template DomPDF (Otomatis)<br>2. Unggah PDF Mandiri. | **Lebih Efisien:** Mahasiswa tidak perlu mengunduh blanko Word, mengisi manual, lalu mengonversi ke PDF; surat dinas rutin terisi otomatis dari data profil. |
| **Penanganan Surat Bingung** | Pemohon wajib tahu pasti siapa dosen/pegawai yang dituju (tidak ada fitur disposisi). | **Meja Disposisi Tata Usaha (*Triage Desk*):** Surat dialihkan ke staf TU untuk ditelaah dan diarahkan. | **Solusi Masalah Nyata:** Mengatasi kebingungan mahasiswa tingkat awal/akhir yang tidak tahu alur birokrasi pejabat penandatangan. |
| **Kebijakan Sertifikat** | 1 sertifikat aktif per user, masa aktif 1 tahun, auto-revoke saat buat baru. | 1 sertifikat aktif per user, masa aktif 1 tahun, auto-revoke saat buat baru. | **Identik:** Mengadopsi *best practice* keamanan digital kampus IPB. |
| **Otorisasi Eksekusi TTD** | Passphrase / PIN otorisasi BSrE. | **Passphrase Kunci Privat Kriptografi** (terpisah mutlak dari password login). | **Keamanan Berlapis:** Melindungi wewenang tanda tangan dari penyalahgunaan saat perangkat tertinggal dalam kondisi login. |
| **Penandatanganan Berurut** | Checkbox *"Mengikuti Urutan"* (Sequential) vs Paralel. | Checkbox *"Mengikuti Urutan"* didukung oleh **Manifest Audit Chaining**. | **Integritas Bita Terjamin:** Menyelesaikan paradoks perubahan hash PDF bertingkat di level PHP Native. |
| **Target Pengguna Penandatangan** | Mahasiswa dan Pegawai/Dosen (*Unified Directory*). | Mahasiswa (Ormawa P2P) dan Dosen/Pejabat (*Dynamic Roles*). | **Identik:** Mendukung legalitas pengesahan dokumen kepanitiaan sesama mahasiswa. |
| **Portal Verifikasi Publik** | Halaman verifikasi kode QR standar. | **Dual Verification Engine:** Memisahkan keabsahan kriptografi (Asli/Palsu) vs masa berlaku surat (Aktif/Kadaluwarsa). | **Kepastian Administrasi:** Memberikan kepastian apakah surat keterangan aktif kuliah mahasiswa masih berlaku atau sudah kadaluwarsa. |

---

## 4. REKOMENDASI IMPLEMENTASI FITUR UNTUK TIM TANDAIN

Berdasarkan analisis visual tangkapan layar IPB DiSign, berikut rekomendasi antarmuka dan basis data yang harus diadaptasi tim pengembang:

### 4.1 Tambahan Komponen Antarmuka Form Pengajuan Dokumen:
1. **Komponen Checkbox Urutan:**
   Tambahkan komponen switch/checkbox pada form pengajuan dokumen:
   ```html
   <div class="form-check form-switch mb-3">
       <input class="form-check-input" type="checkbox" id="is_sequential" name="is_sequential" value="1" checked>
       <label class="form-check-label fw-bold" for="is_sequential">
           Penandatanganan dokumen harus mengikuti urutan (Sequential Signing)
       </label>
       <div class="form-text">Jika dicentang, penandatangan tingkat 2 baru menerima notifikasi setelah penandatangan tingkat 1 selesai.</div>
   </div>
   ```
2. **Komponen Baris Penandatangan Dinamis (*Repeater Row*) Berbasis Pencarian Email:**
   * **Anti-Dropdown Massal:** Menghindari elemen `<select>` dropdown statis berisi ribuan civitas kampus demi performa dan privasi. Sebagai gantinya, gunakan input teks pencarian email resmi (`@pnj.ac.id` / `@stu.pnj.ac.id`) yang didukung *debounced auto-search* (delay henti ketik 500-800ms) dan tombol fisik "Cari" (Enter / klik).
   * Setiap baris/kartu penandatangan yang ditambahkan memuat: Informasi Akun (Nama, Email, NIP/NIM, Jabatan), Input Angka Nomor Halaman Stempel, Pilihan Tipe Stempel (QR Saja, TTD Saja, Dual Display), serta Tombol Hapus baris (`-`).
3. **Manajemen Gambar Tanda Tangan (Penyederhanaan Spesimen Tunggal):**
   * Di menu Pengaturan Profil Akun, sediakan antarmuka terpadu: **Spesimen Tanda Tangan Resmi** dengan dua opsi input (Unggah PNG transparan atau Gambar Manual via Canvas). Fitur tab "Paraf" dari sistem lama tidak perlu diadopsi agar UI bersih dan minim kebingungan.

---

### 5. KESIMPULAN

Tangkapan layar nyata dari **IPB DiSign** membuktikan bahwa:
* Desain sistem **Tandain** yang kita diskusikan **sangat tepat sasaran, akurat, dan mencerminkan sistem enterprise kampus yang sesungguhnya**.
* Aturan **1 Sertifikat Aktif 1 Tahun**, **Checkbox Urutan (Sequential)**, **Multi-TTD Dinamis**, dan **Pencarian Penandatangan Mahasiswa/Pegawai (P2P)** terbukti secara riil diterapkan di perguruan tinggi ternama.
* Dengan penambahan fitur unggulan Tandain seperti **Triage Disposisi TU**, **Template Otomatis DomPDF**, dan **Dual Verification Engine**, sistem Tandain PNJ memiliki keunggulan kompetitif yang lebih solutif bagi mahasiswa dan tata usaha jurusan.

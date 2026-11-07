# DOKUMEN 00: RINGKASAN EKSEKUTIF, LATAR BELAKANG, DAN ANALISIS STRATEGIS SISTEM
## Proyek Tanda Tangan Elektronik Kampus "Tandain"
**Jurusan Teknik Informatika dan Komputer — Politeknik Negeri Jakarta**

---

## 1. Ringkasan Eksekutif Produk (Executive Summary)

### 1.1 Definisi Produk
**Tandain** adalah platform infrastruktur kepercayaan digital (*digital trust platform*) dan Tanda Tangan Elektronik (TTE) internal berbasis web yang dirancang khusus untuk ekosistem civitas akademika Jurusan Teknik Informatika dan Komputer Politeknik Negeri Jakarta (PNJ TIK). Sistem ini mengintegrasikan otomasi administrasi persuratan akademik dan kemahasiswaan dengan mekanisme penjaminan integritas data berbasis kriptografi asimetris (RSA 2048-bit dan SHA-256).

### 1.2 Masalah Pokok yang Diselesaikan
Dalam birokrasi kampus konvensional, proses pengesahan dokumen resmi—seperti Surat Keputusan, Surat Keterangan Mahasiswa Aktif, Lembar Pengesahan Skripsi/Tugas Akhir, Surat Permohonan Magang/PKL, dan Proposal Kegiatan Organisasi Mahasiswa (Ormawa)—kerap terhambat oleh ketergantungan pada tanda tangan basah fisik. Hal ini menimbulkan latensi birokrasi berhari-hari, risiko kehilangan berkas fisik di meja kerja pejabat kampus, serta kerentanan tinggi terhadap pemalsuan tanda tangan melalui manipulasi digital sederhana (*copy-paste* gambar tanda tangan transparan pada berkas PDF).

### 1.3 Nilai Tambah Utama (Value Propositions)
1. **Zero-Cost Sovereign Infrastructure (Tanpa Biaya Pihak Ketiga):**
   Tidak membebankan biaya per-dokumen atau biaya lisensi langganan tahunan kepada kampus, berbeda dengan Penyelenggara Sertifikasi Elektronik (PSrE) komersial. Sistem beroperasi penuh di atas infrastruktur server mandiri Jurusan TIK PNJ.
2. **Kriptografi Asimetris Mandiri (Self-Sovereign Digital Identity):**
   Menggunakan pasangan kunci publik-privat standar industri (RSA 2048-bit) dengan proteksi *envelope encryption* (AES-256-CBC) berbasis *passphrase* rahasia milik pengguna, menjamin asas nir-penyangkalan (*non-repudiation*).
3. **Dual Verification Engine:**
   Pemeriksaan berlapis yang memisahkan pengujian integritas kriptografis matematis (apakah isi bita dokumen telah dimodifikasi) dengan pengujian administratif (apakah masa berlaku dokumen untuk kepentingan akademik masih aktif).
4. **Birokrasi Cerdas (Dynamic Routing & Triage Desk):**
   Mendukung penandatanganan berantai multi-tingkat (*sequential signing*), penandatanganan mandiri (*self-signing*), serta meja disposisi terpusat (*Triage Desk*) oleh staf Tata Usaha untuk mahasiswa yang belum mengetahui alur struktural pejabat penandatangan.

---

## 2. Latar Belakang Permasalahan di Lingkungan Kampus

Lanskap administrasi di lingkungan perguruan tinggi vokasi seperti Politeknik Negeri Jakarta memiliki dinamika tinggi dengan volume sirkulasi dokumen resmi mencapai ratusan berkas per minggu pada masa-masa krusial (awal semester, sidang tugas akhir, dan masa regenerasi kepengurusan ormawa). Berdasarkan observasi dan analisis operasional di Jurusan TIK PNJ, teridentifikasi tiga kendala fundamental:

### 2.1 Hambatan Mobilitas Geografis dan Kehadiran Fisik (Physical Presence Bottleneck)
* Dosen pembimbing, koordinator program studi, ketua jurusan, dan pimpinan lainnya memiliki jadwal tridharma perguruan tinggi yang padat (mengajar, membimbing di laboratorium industri, meneliti, dan menghadiri rapat kedinasan di luar kampus).
* Mahasiswa sering kali harus mengorbankan waktu perkuliahan untuk menunggu kehadiran dosen di ruang kerja jurusan hanya demi membubuhkan tanda tangan pengesahan berkas selama 10 detik.
* Fenomena "dokumen tertumpuk di meja kerja" (*inbox desk stagnation*) menyebabkan proses permohonan surat rekomendasi beasiswa, magang, atau ujian sidang tertunda antara 3 hingga 7 hari kerja.

### 2.2 Kerentanan Pemalsuan dan Kerapuhan Integritas Dokumen (Document Forgery)
* Maraknya praktik penandatanganan dokumen elektronik "semu" (*pseudo-digital signature*) melalui penempelan gambar pindaian tanda tangan (PNG/JPG) ke dokumen Microsoft Word atau PDF.
* Secara hukum dan teknis keamanan informasi, gambar tanda tangan tidak memiliki nilai pembuktian integritas data. Setiap pihak dapat dengan mudah mengekstrak gambar tanda tangan tersebut dan menempelkannya pada surat rekaan lain tanpa izin.
* Tidak ada mekanisme bagi pihak eksternal (perusahaan mitra magang, institusi beasiswa, atau universitas tujuan studi lanjut) untuk memverifikasi keaslian surat selain melakukan konfirmasi manual melalui telepon atau email ke bagian Tata Usaha Jurusan TIK.

### 2.3 Urgensi Kepatuhan Hukum dan Audit Trail Digital
* Sesuai dengan amanat **Undang-Undang Republik Indonesia Nomor 11 Tahun 2008 tentang Informasi dan Transaksi Elektronik (UU ITE)** serta **Peraturan Pemerintah Nomor 71 Tahun 2019 tentang Penyelenggaraan Sistem dan Transaksi Elektronik (PSTE)**, transaksi elektronik wajib menjamin keutuhan (*integrity*), keaslian (*authenticity*), dan nir-penyangkalan (*non-repudiation*).
* Kampus membutuhkan rekaman log audit (*audit trail*) yang tidak dapat diubah (*immutable log*), mencatat secara presisi siapa yang menandatangani, kapan waktu penandatanganan dilakukan (*cryptographic timestamp*), alamat IP pengakses, serta riwayat perubahan status dokumen dari draf hingga berkas final.

---

## 3. Visi & Tujuan Proyek

```
              +-------------------------------------------------------+
              |                    VISI TANDAIN                       |
              |  Mewujudkan Tata Kelola Kampus Bebas Kertas (Paperless|
              |  Campus) Berdaulat yang Menjamin Integritas Dokumen   |
              |  secara Kriptografis, Cepat, dan Nol Biaya Operasional|
              +-------------------------------------------------------+
                                          |
        +---------------------------------+---------------------------------+
        |                                 |                                 |
        v                                 v                                 v
+------------------+             +------------------+             +------------------+
|    EFISIENSI     |             |    INTEGRITAS    |             |    KEDAULATAN    |
| Pangkas waktu    |             | RSA 2048 + SHA256|             | On-premise, zero |
| birokrasi dari   |             | Menjamin dokumen |             | vendor lock-in,  |
| hari ke menit    |             | anti-manipulasi  |             | tanpa biaya PSrE |
+------------------+             +------------------+             +------------------+
```

### 3.1 Visi Strategis
Menjadikan Jurusan TIK Politeknik Negeri Jakarta sebagai pelopor transformasi tata kelola dokumen digital mandiri di lingkungan politeknik se-Indonesia, dengan menghadirkan sistem tanda tangan elektronik yang berdaulat secara teknologi, mudah digunakan oleh civitas akademika, dan memiliki derajat keamanan informasi berstandar internasional.

### 3.2 Tujuan Proyek (SMART Objectives)
1. **Efisiensi Waktu Pengesahan Dokumen:** Menurunkan siklus hidup persetujuan dokumen administrasi (dari pengajuan hingga selesai ditandatangani) dari rata-rata **4 hari kerja** menjadi **kurang dari 1 jam**, asalkan penandatangan berada dalam jaringan internet.
2. **Eliminasi Pengeluaran Pihak Ketiga (Zero Third-Party Cost):** Menghemat anggaran belanja operasional jurusan dengan membangun sistem TTE mandiri tanpa kewajiban membayar biaya transaksi per dokumen ke vendor PSrE komersial (yang umumnya berkisar antara Rp2.500 hingga Rp7.500 per dokumen).
3. **Pemberdayaan Dokumen Baku Otomatis:** Menyediakan modul pembuatan surat baku otomatis berbasis templat web yang langsung mengonversi data formulir menjadi berkas PDF standar kedinasan PNJ tanpa memerlukan perangkat lunak pengolah kata pihak ketiga.
4. **Verifikasi Publik Nir-Hambatan (Zero Barrier Verification):** Memungkinkan verifikasi instan bagi masyarakat luas, orang tua mahasiswa, dan instansi mitra industri cukup dengan memindai kode QR dinamis pada dokumen fisik atau mengunggah berkas PDF ke portal verifikasi terbuka tanpa keharusan membuat akun (*no-login verification*).

---

## 4. Profil Tim Proyek & Pemangku Kepentingan (Stakeholders)

Pengembangan sistem **Tandain** diinisiasi sebagai proyek rekayasa perangkat lunak strategis di bawah naungan Jurusan Teknik Informatika dan Komputer, Politeknik Negeri Jakarta.

### 4.1 Pembina dan Pengarah Akademik
Sistem ini dibimbing dan diarahkan oleh dosen senior yang memiliki keahlian mendalam pada bidang rekayasa sistem informasi, tata kelola perangkat lunak, dan keamanan siber:

1. **Ir. Iwan Sonjaya, M.T.**
   * *Peran:* Dosen Pengampu & Pengarah Teknis Arsitektur & Kriptografi.
   * *Fokus Supervisi:* Desain fondasi algoritma asimetris, skema pengamanan kunci privat (*envelope encryption*), penanganan paradoks hash pada penandatanganan berantai (*manifest audit chain*), dan kepatuhan standar kriptografi OpenSSL.
2. **Anita Hidayati, S.Kom., M.Kom.**
   * *Peran:* Dosen Pengampu & Pengarah Rekayasa Perangkat Lunak & Tata Kelola.
   * *Fokus Supervisi:* Metodologi analisis kebutuhan sistem (IEEE 830), perancangan proses bisnis birokrasi kampus, pengujian penerimaan pengguna (*User Acceptance Testing* / UAT), dan evaluasi aksesibilitas antarmuka pengguna (WCAG 2.1 AA).

### 4.2 Struktur Tim Pengembang (Development Team)
Tim pelaksana proyek terdiri dari mahasiswa Jurusan TIK PNJ dengan pembagian peran terstruktur:

| Peran Tim | Tanggung Jawab Utama |
| :--- | :--- |
| **Project Manager & Systems Analyst** | Manajemen jadwal proyek, perumusan spesifikasi kebutuhan (SRS), koordinasi pengujian fungsional, dan penyelarasan proses birokrasi kampus. |
| **Lead Cryptographer & Backend Engineer** | Implementasi enkripsi RSA 2048-bit, hashing SHA-256, mekanisme de-kapsulasi passphrase in-memory, serta perancangan skema basis data PostgreSQL 15/16 (ekstensi pgcrypto, tipe data UUID, JSONB, dan TIMESTAMPTZ). |
| **Document Processing Engineer** | Penanganan engine rendering dokumen (DomPDF), pemetaan posisi stempel visual (FPDI/FPDF), manipulasi bita PDF, dan generator QR Code dinamis. |
| **Frontend & UI/UX Specialist** | Pengembangan antarmuka berbasis Web responsif, implementasi viewer PDF.js, integrasi design system Google Workspace & IBM Carbon, serta pemenuhan standar kontras WCAG 2.1 AA. |
| **Quality Assurance & Security Auditor** | Pelaksanaan uji penetrasi black-box, pengujian batas masa berlaku dokumen, simulasi manipulasi dokumen (*tamper test*), dan koordinasi UAT bersama dosen serta staf TU. |

### 4.3 Matriks RACI Stakeholder Kampus

Untuk memastikan kelancaran adopsi sistem, disusun matriks peran RACI (*Responsible, Accountable, Consulted, Informed*):

| Aktivitas / Fitur | Mahasiswa | Dosen | Admin Tata Usaha | Ketua Jurusan / Pimpinan | Verifikator Publik |
| :--- | :---: | :---: | :---: | :---: | :---: |
| Pengajuan Dokumen Mandiri | **R** | C | I | I | - |
| Pengajuan Dokumen via Template | **R** | - | I | I | - |
| Pengelolaan Master Template Surat | - | C | **R** / **A** | C | - |
| Disposisi Dokumen (Triage Desk) | - | - | **R** / **A** | I | - |
| Penandatanganan Akademik (Dosen) | - | **R** / **A** | I | I | - |
| Penandatanganan P2P (Ormawa) | **R** / **A** | - | I | - | - |
| Pengesahan Pejabat Struktural | - | - | I | **R** / **A** | - |
| Verifikasi QR Code / Cek Hash Dokumen | I | I | I | I | **R** / **A** |

*Keterangan:*
* **R (Responsible):** Pihak yang mengeksekusi aktivitas.
* **A (Accountable):** Pihak yang bertanggung jawab penuh dan memiliki otoritas akhir.
* **C (Consulted):** Pihak yang dimintai masukan atau konsultasi substansi.
* **I (Informed):** Pihak yang menerima pemberitahuan status perkembangan.

---

## 5. Korelasi Riset, Benchmarking, dan Analisis Komparatif

### 5.1 Studi Preseden: Sistem IPB DigiSign
Dalam tahapan perancangan sistem Tandain, tim melakukan *benchmarking* terhadap implementasi tanda tangan digital kampus terkemuka di Indonesia, salah satunya **IPB DigiSign** yang dikembangkan oleh Institut Pertanian Bogor.

* **Temuan Positif IPB DigiSign:**
  1. Penggunaan akun tunggal (*Single Sign-On* / SSO) kampus terbukti meningkatkan tingkat adopsi civitas akademika secara eksponensial.
  2. Format verifikasi publik berbasis kode QR yang mengarah langsung ke peladen resmi kampus memberikan rasa aman instan bagi institusi penerima surat.
  3. Mengurangi volume pengadaan kertas fisik (*green campus initiative*) hingga puluhan rim per semester.
* **Celah yang Disempurnakan oleh Tandain:**
  1. *Masalah Disposisi:* Pada sistem konvensional, mahasiswa sering keliru memilih pejabat penandatangan saat mengajukan surat yang sifatnya antar-divisi. Tandain menghadirkan fitur **Triage Desk** di mana staf TU dapat meninjau permohonan dan merutekan ulang alur penandatanganan tanpa membatalkan pengajuan berkas.
  2. *Dukungan Tanda Tangan Mahasiswa (P2P Ormawa):* Sebagian besar sistem kampus hanya memberikan hak tanda tangan kepada pejabat struktural dan dosen. Tandain menerapkan model *Unified User Entity*, memungkinkan ketua organisasi mahasiswa (BEM, HMD, Kepanitiaan) menandatangani surat secara berantai dengan jaminan kriptografi yang setara.
  3. *Inovasi Template Dinamis DomPDF:* Mahasiswa tidak perlu membuat dokumen di Microsoft Word secara manual. Tandain menyediakan form web yang menginjeksikan data langsung ke template baku jurusan secara otomatis.

### 5.2 Analisis Komparatif Sistem Tanda Tangan Elektronik

Berikut adalah matriks komparasi analitis antara sistem **Tandain** dengan alternatif solusi yang ada di pasar:

| Parameter Evaluasi | Tandain (PNJ TIK) | PSrE Komersial Berbayar (PrivyID / Peruri) | Sistem Sisfo Konvensional (Scan PNG Basah) | Adobe Acrobat Digital ID (Lokal) |
| :--- | :--- | :--- | :--- | :--- |
| **Model Biaya Operasional** | **Nol Rupiah (Zero-Cost)**, berjalan di atas server kampus. | Biaya per transaksi dokumen (Rp2.500 - Rp7.500/sign). Membebani anggaran. | Gratis, namun tidak aman. | Memerlukan lisensi Adobe Acrobat Pro berbayar per pengguna. |
| **Kedaulatan Data (Data Sovereignty)** | Dokumen internal tersimpan 100% di server lokal PNJ TIK. | Berkas diunggah ke *cloud* pihak ketiga (potensi kerahasiaan bocor). | Tersimpan lokal, tetapi berkas mudah disunting siapa saja. | Tersimpan lokal di perangkat masing-masing pengguna. |
| **Jaminan Anti-Manipulasi (Kriptografi)** | **Kuat (RSA 2048-bit + SHA-256 Digest)** dengan Audit Trail chaining. | Sangat Kuat (Sertifikat Induk Root CA Kominfo). | **Nol.** Gambar tanda tangan dapat disalin/ditempel (*forgery*). | Kuat, namun bergantung pada sertifikat perangkat individu. |
| **Template Surat Baku Otomatis** | **Terintegrasi Penuh (DomPDF Engine)** dengan placeholder teks. | Tidak ada. Pengguna wajib mengunggah berkas PDF jadi. | Tidak ada atau terbatas pada cetak tabel HTML polos. | Tidak ada. Pengguna mengolah berkas di aplikasi terpisah. |
| **Alur Birokrasi Kampus (Triage TU)** | **Ada.** Staf TU dapat melakukan triage & disposisi fleksibel. | Tidak ada. Pengaju harus menentukan sendiri email tujuan. | Tidak ada alur terstruktur terverifikasi. | Tidak ada alur *workflow* terdistribusi. |
| **Kemudahan Verifikasi Publik** | **Sangat Tinggi.** Cukup scan QR code lewat kamera HP apa saja tanpa login. | Tinggi, namun beberapa penyedia mewajibkan aplikasi khusus. | Rendah. Verifikasi hanya dapat dilakukan dengan kontak manual ke TU. | Memerlukan perangkat komputer dengan Adobe Reader terkonfigurasi. |
| **Status Validitas Dokumen** | Memisahkan status Kriptografi (Asli/Palsu) vs Administratif (Aktif/Expired). | Validasi fokus pada validitas sertifikat saat pemeriksaan. | Tidak ada status validitas sistematis. | Bergantung pada trust store sistem operasi lokal. |

### 5.3 Kesimpulan Strategis
Berdasarkan analisis di atas, pengembangan sistem **Tandain** merupakan langkah strategis yang sangat tepat guna bagi Jurusan TIK PNJ. Tandain mengisi jurang pemisah antara metode konvensional yang rapuh dari sisi keamanan informasi dengan layanan komersial pihak ketiga yang menimbulkan beban anggaran berkepanjangan bagi institusi pendidikan vokasi negeri.

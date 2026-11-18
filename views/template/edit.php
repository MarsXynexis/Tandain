<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$templateData = $data['template'] ?? ($template ?? []);
$schemaList = $data['schemaFields'] ?? ($schemaFields ?? []);

$templateId = $templateData['id'] ?? '';
$titleValue = $templateData['title'] ?? '';
$codeValue = $templateData['template_code'] ?? '';
$descriptionValue = $templateData['description'] ?? '';
$htmlValue = $templateData['html_content'] ?? '';
$rawActive = $templateData['is_active'] ?? false;
$isActiveValue = ($rawActive === true || $rawActive === 1 || $rawActive === '1' || $rawActive === 't' || $rawActive === 'true');

$initialSchemaJson = json_encode(array_values(is_array($schemaList) ? $schemaList : []), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <div class="min-w-0 w-full flex flex-col gap-6">

      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex flex-wrap items-center gap-3">
            <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Ubah Template Surat</h1>
            <?php if ($codeValue !== ''): ?>
              <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-surface-container border border-outline-variant font-code-tabular text-xs font-semibold text-primary">
                <?= htmlspecialchars($codeValue, ENT_QUOTES, 'UTF-8'); ?>
              </span>
            <?php endif; ?>
          </div>
          <p class="font-body-md text-body-md text-on-surface-variant mt-1">Perbarui konfigurasi kolom isian dan format lembar kerja naskah dinas resmi PNJ</p>
        </div>

        <div>
          <a href="/Tandain/template" class="btn-md inline-flex items-center gap-1.5 border border-outline-variant text-primary hover:bg-surface-container-low transition-colors">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali</span>
          </a>
        </div>
      </div>

      <?php require 'views/layouts/flash.php'; ?>

      <?php $notice = ['type' => 'error', 'id' => 'builder-client-error']; ?>
      <?php require 'views/layouts/notice.php'; ?>

      <form id="template-builder-form" method="POST" action="/Tandain/template/update/<?= urlencode($templateId); ?>" class="flex flex-col gap-6" novalidate>

        <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm flex flex-col gap-5">

          <div class="flex items-center justify-between pb-3 border-b border-surface-container">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-secondary text-[22px]">description</span>
              <div>
                <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Informasi Template</h2>
                <p class="font-body-sm text-xs text-on-surface-variant">Informasi umum dan status keaktifan template naskah dinas</p>
              </div>
            </div>
          </div>

          <div class="flex flex-col gap-1.5">
            <label for="template-title" class="font-label-md text-label-md font-semibold text-primary">
              Judul Template Surat <span class="text-[#b91c1c]">*</span>
            </label>
            <input id="template-title" name="title" type="text" maxlength="200" required value="<?= htmlspecialchars($titleValue, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Contoh: Surat Keterangan Mahasiswa Aktif" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant/60 font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="flex flex-col gap-1.5">
              <label for="template-code" class="font-label-md text-label-md font-semibold text-primary">
                Kode Template <span class="text-[#b91c1c]">*</span>
              </label>
              <input id="template-code" name="template_code" type="text" maxlength="50" required value="<?= htmlspecialchars($codeValue, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Contoh: SK-AKTIF-MHS" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant/60 font-code-tabular text-code-tabular uppercase focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
              <p class="font-body-sm text-[11px] text-on-surface-variant">Huruf kapital, angka, tanda hubung (-), atau garis bawah (_).</p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label for="template-is-active" class="font-label-md text-label-md font-semibold text-primary">
                Status Template
              </label>
              <select id="template-is-active" name="is_active" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
                <option value="1" <?= $isActiveValue ? 'selected' : ''; ?>>Aktif (Tersedia untuk Pengajuan)</option>
                <option value="0" <?= !$isActiveValue ? 'selected' : ''; ?>>Nonaktif (Disembunyikan)</option>
              </select>
            </div>

          </div>

          <div class="flex flex-col gap-1.5">
            <label for="template-description" class="font-label-md text-label-md font-semibold text-primary">
              Deskripsi Ringkas
            </label>
            <textarea id="template-description" name="description" rows="2" placeholder="Penjelasan singkat peruntukan template surat bagi pemohon..." class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant/60 font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all leading-relaxed"><?= htmlspecialchars($descriptionValue, ENT_QUOTES, 'UTF-8'); ?></textarea>
          </div>

        </section>

        <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm flex flex-col gap-4">

          <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-surface-container">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-secondary text-[22px]">tune</span>
              <div>
                <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Kolom Isian Formulir</h2>
                <p class="font-body-sm text-xs text-on-surface-variant">Definisikan data tambahan yang wajib diisi pemohon saat mengajukan template ini</p>
              </div>
            </div>

            <button id="btn-add-field" type="button" class="btn-md inline-flex items-center gap-1.5 bg-brand hover:bg-brand-hover text-white font-semibold transition-colors shadow-sm">
              <span class="material-symbols-outlined text-[18px]">add</span>
              <span>Tambah Kolom Isian</span>
            </button>
          </div>

          <div id="schema-empty-state" class="rounded-lg border border-dashed border-outline-variant bg-surface-container-low px-4 py-6 text-center font-body-sm text-body-sm text-on-surface-variant">
            Belum ada kolom isian tambahan. Klik tombol "Tambah Kolom Isian" jika pemohon perlu mengisi data khusus saat mengajukan surat (seperti keperluan atau instansi tujuan).
          </div>

          <div id="schema-fields-container" class="flex flex-col gap-3"></div>

        </section>

        <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm flex flex-col gap-6">

          <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-surface-container">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-secondary text-[22px]">draw</span>
              <div>
                <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Editor Naskah Surat</h2>
                <p class="font-body-sm text-xs text-on-surface-variant">Susun isi surat pada lembar kerja A4. Kop dan footer resmi diterapkan otomatis oleh sistem.</p>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <button id="btn-test-pdf" type="button" class="btn-md inline-flex items-center gap-1.5 border border-[#004B87] text-[#004B87] hover:bg-[#004B87] hover:text-white font-semibold transition-colors">
                <span class="material-symbols-outlined text-[18px]" id="btn-test-pdf-icon">picture_as_pdf</span>
                <span id="btn-test-pdf-label">Pratinjau PDF</span>
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

            <div class="xl:col-span-8 flex flex-col gap-3 min-w-0">

              <style>
                [data-cmd].is-active {
                  background-color: #cbd5e1 !important;
                  border-color: #64748b !important;
                  color: #0f172a !important;
                  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.16) !important;
                }

                [data-cmd].is-active:hover {
                  background-color: #94a3b8 !important;
                }
              </style>

              <div class="lg:sticky lg:top-20 z-20 bg-surface-container-lowest border border-outline-variant rounded-xl p-2.5 shadow-sm flex items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-1">
                  <button type="button" data-cmd="bold" title="Tebal (Ctrl+B)" class="p-1.5 rounded-lg border border-outline-variant bg-surface-container-low hover:bg-surface-container text-primary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">format_bold</span>
                  </button>

                  <button type="button" data-cmd="italic" title="Miring (Ctrl+I)" class="p-1.5 rounded-lg border border-outline-variant bg-surface-container-low hover:bg-surface-container text-primary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">format_italic</span>
                  </button>

                  <button type="button" data-cmd="underline" title="Garis Bawah (Ctrl+U)" class="p-1.5 rounded-lg border border-outline-variant bg-surface-container-low hover:bg-surface-container text-primary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">format_underlined</span>
                  </button>

                  <div class="h-5 w-px bg-outline-variant mx-1"></div>

                  <button type="button" data-cmd="justifyLeft" title="Rata Kiri" class="p-1.5 rounded-lg border border-outline-variant bg-surface-container-low hover:bg-surface-container text-primary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">format_align_left</span>
                  </button>

                  <button type="button" data-cmd="justifyCenter" title="Rata Tengah" class="p-1.5 rounded-lg border border-outline-variant bg-surface-container-low hover:bg-surface-container text-primary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">format_align_center</span>
                  </button>

                  <button type="button" data-cmd="justifyRight" title="Rata Kanan" class="p-1.5 rounded-lg border border-outline-variant bg-surface-container-low hover:bg-surface-container text-primary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">format_align_right</span>
                  </button>

                  <button type="button" data-cmd="justifyFull" title="Rata Kiri-Kanan (Justify)" class="p-1.5 rounded-lg border border-outline-variant bg-surface-container-low hover:bg-surface-container text-primary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">format_align_justify</span>
                  </button>

                  <div class="h-5 w-px bg-outline-variant mx-1"></div>

                  <button type="button" data-cmd="insertUnorderedList" title="Daftar Poin" class="p-1.5 rounded-lg border border-outline-variant bg-surface-container-low hover:bg-surface-container text-primary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">format_list_bulleted</span>
                  </button>

                  <button type="button" data-cmd="insertOrderedList" title="Daftar Nomor" class="p-1.5 rounded-lg border border-outline-variant bg-surface-container-low hover:bg-surface-container text-primary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">format_list_numbered</span>
                  </button>
                </div>

                <div class="hidden sm:flex items-center text-xs text-on-surface-variant font-medium">
                  Lembar A4 Resmi PNJ
                </div>
              </div>

              <div class="w-full overflow-x-auto rounded-2xl bg-surface-container border border-outline-variant p-4 sm:p-6 flex justify-center shadow-inner">

                <article class="pnj-a4-sheet shadow-2xl bg-white" id="pnj-a4-visual-sheet" style="max-width: 210mm;">

                  <header class="pnj-letter-kop select-none" contenteditable="false">
                    <img class="pnj-letter-kop-logo" src="/Tandain/assets/img/logo-pnj-kop.png?v=3" alt="Logo Politeknik Negeri Jakarta" width="118" height="118">

                    <div class="pnj-letter-kop-text">
                      <div class="pnj-kop-ministry">KEMENTERIAN PENDIDIKAN TINGGI, SAINS,<br>DAN TEKNOLOGI</div>
                      <div class="pnj-kop-institution">POLITEKNIK NEGERI JAKARTA</div>
                      <div class="pnj-kop-meta">Jalan Prof. DR. G.A. Siwabessy, Kampus UI, Depok 16425</div>
                      <div class="pnj-kop-meta">Telepon (021) 7270036, Hunting, Fax (021) 7270034</div>
                      <div class="pnj-kop-meta">Laman: <u>http://www.pnj.ac.id</u>, Pos-el: <u>humas@pnj.ac.id</u></div>
                    </div>
                  </header>

                  <div class="pnj-letter-body focus:outline-none min-h-[560px] cursor-text p-2 rounded-lg transition-colors focus:bg-blue-50/20" id="pnj-letter-body-editable" contenteditable="true" spellcheck="false"><?= $htmlValue; ?></div>

                  <footer class="pnj-letter-footer select-none" contenteditable="false">
                    <div>Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik</div>
                    <div>yang diterbitkan oleh Platform Tandain, Politeknik Negeri Jakarta (PNJ).</div>
                  </footer>

                </article>

              </div>

              <textarea id="html-content-hidden" name="html_content" class="hidden"><?= htmlspecialchars($htmlValue, ENT_QUOTES, 'UTF-8'); ?></textarea>

              <p class="font-body-sm text-xs text-on-surface-variant flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px] text-[#15803d]">check_circle</span>
                <span>Naskah dinas wajib memuat minimal satu slot tanda tangan seperti <code class="font-code-tabular text-[#15803d] font-semibold">{{kolom_ttd_1}}</code>.</span>
              </p>

            </div>

            <div class="xl:col-span-4 flex flex-col gap-4 xl:sticky xl:top-20 xl:self-start">

              <div class="bg-surface-container-low/70 border border-outline-variant rounded-xl p-4 flex flex-col gap-3.5 shadow-sm">
                <div>
                  <h3 class="font-label-md text-sm font-bold text-primary">Variabel Dokumen</h3>
                  <p class="font-body-sm text-xs text-on-surface-variant">Klik tag untuk menyisipkan ke posisi kursor naskah</p>
                </div>

                <div class="flex flex-col gap-1.5">
                  <span class="text-xs font-semibold text-primary">Data Pemohon</span>
                  <div class="flex flex-wrap gap-1.5">
                    <button type="button" data-insert-tag="{{nama_lengkap}}" title="Nama lengkap pemohon dari akun login" class="btn-md inline-flex border border-outline-variant bg-white hover:border-[#bfdbfe] hover:bg-[#eff6ff] hover:text-[#004B87] text-primary font-code-tabular font-medium transition-colors shadow-sm">{{nama_lengkap}}</button>
                    <button type="button" data-insert-tag="{{nomor_induk}}" title="NIM atau NIP pemohon" class="btn-md inline-flex border border-outline-variant bg-white hover:border-[#bfdbfe] hover:bg-[#eff6ff] hover:text-[#004B87] text-primary font-code-tabular font-medium transition-colors shadow-sm">{{nomor_induk}}</button>
                    <button type="button" data-insert-tag="{{program_studi}}" title="Program studi pemohon" class="btn-md inline-flex border border-outline-variant bg-white hover:border-[#bfdbfe] hover:bg-[#eff6ff] hover:text-[#004B87] text-primary font-code-tabular font-medium transition-colors shadow-sm">{{program_studi}}</button>
                    <button type="button" data-insert-tag="{{email}}" title="Email resmi kampus" class="btn-md inline-flex border border-outline-variant bg-white hover:border-[#bfdbfe] hover:bg-[#eff6ff] hover:text-[#004B87] text-primary font-code-tabular font-medium transition-colors shadow-sm">{{email}}</button>
                    <button type="button" data-insert-tag="{{jabatan}}" title="Jabatan atau status civitas" class="btn-md inline-flex border border-outline-variant bg-white hover:border-[#bfdbfe] hover:bg-[#eff6ff] hover:text-[#004B87] text-primary font-code-tabular font-medium transition-colors shadow-sm">{{jabatan}}</button>
                  </div>
                </div>

                <div class="flex flex-col gap-1.5 pt-2.5 border-t border-outline-variant/60">
                  <span class="text-xs font-semibold text-primary">Kedinasan &amp; Pengesahan</span>
                  <div class="flex flex-wrap gap-1.5">
                    <button type="button" data-insert-tag="{{nomor_surat}}" title="Nomor naskah dinas resmi" class="btn-md inline-flex border border-outline-variant bg-white hover:border-[#bfdbfe] hover:bg-[#eff6ff] hover:text-[#004B87] text-primary font-code-tabular font-medium transition-colors shadow-sm">{{nomor_surat}}</button>
                    <button type="button" data-insert-tag="{{tanggal_surat}}" title="Tanggal naskah dinas" class="btn-md inline-flex border border-outline-variant bg-white hover:border-[#bfdbfe] hover:bg-[#eff6ff] hover:text-[#004B87] text-primary font-code-tabular font-medium transition-colors shadow-sm">{{tanggal_surat}}</button>
                    <button type="button" data-insert-tag="{{kolom_ttd_1}}" title="Slot tanda tangan pejabat 1" class="btn-md inline-flex border border-[#bbf7d0] bg-[#f0fdf4] hover:bg-[#dcfce7] text-[#15803d] font-code-tabular font-semibold transition-colors shadow-sm">{{kolom_ttd_1}}</button>
                    <button type="button" data-insert-tag="{{kolom_ttd_2}}" title="Slot tanda tangan pejabat 2" class="btn-md inline-flex border border-[#bbf7d0] bg-[#f0fdf4] hover:bg-[#dcfce7] text-[#15803d] font-code-tabular font-semibold transition-colors shadow-sm">{{kolom_ttd_2}}</button>
                    <button type="button" data-insert-tag="{{qr_verifikasi}}" title="QR Code verifikasi" class="btn-md inline-flex border border-[#bbf7d0] bg-[#f0fdf4] hover:bg-[#dcfce7] text-[#15803d] font-code-tabular font-semibold transition-colors shadow-sm">{{qr_verifikasi}}</button>
                  </div>
                </div>

                <div class="flex flex-col gap-1.5 pt-2.5 border-t border-outline-variant/60">
                  <span class="text-xs font-semibold text-primary">Isian Tambahan (Formulir)</span>
                  <div id="cheat-sheet-dynamic" class="flex flex-wrap items-center gap-1.5">
                    <span class="text-xs text-on-surface-variant/80 italic">Belum ada kolom isian formulir.</span>
                  </div>
                </div>
              </div>

              <div class="bg-surface-container-low/70 border border-outline-variant rounded-xl p-4 flex flex-col gap-2.5 shadow-sm">
                <div>
                  <h3 class="font-label-md text-sm font-bold text-primary">Struktur Surat</h3>
                  <p class="font-body-sm text-xs text-on-surface-variant">Sisipkan blok komponen naskah dinas baku</p>
                </div>

                <div class="flex flex-col gap-1.5">
                  <button type="button" data-snippet-key="title" class="btn-md inline-flex items-center justify-start gap-2 border border-outline-variant bg-white hover:bg-surface-container text-primary font-semibold transition-colors text-left shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-secondary">title</span>
                    <span>Judul &amp; Nomor Surat</span>
                  </button>

                  <button type="button" data-snippet-key="applicant_table" class="btn-md inline-flex items-center justify-start gap-2 border border-outline-variant bg-white hover:bg-surface-container text-primary font-semibold transition-colors text-left shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-secondary">table_rows</span>
                    <span>Tabel Data Pemohon</span>
                  </button>

                  <button type="button" data-snippet-key="ttd_single" class="btn-md inline-flex items-center justify-start gap-2 border border-[#bbf7d0] bg-[#f0fdf4] hover:bg-[#dcfce7] text-[#15803d] font-semibold transition-colors text-left shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-[#15803d]">draw</span>
                    <span>Tanda Tangan 1 Pejabat (Kanan)</span>
                  </button>

                  <button type="button" data-snippet-key="ttd_double" class="btn-md inline-flex items-center justify-start gap-2 border border-[#bbf7d0] bg-[#f0fdf4] hover:bg-[#dcfce7] text-[#15803d] font-semibold transition-colors text-left shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-[#15803d]">group</span>
                    <span>Tanda Tangan 2 Pejabat (Kiri &amp; Kanan)</span>
                  </button>

                  <button type="button" data-snippet-key="tembusan" class="btn-md inline-flex items-center justify-start gap-2 border border-outline-variant bg-white hover:bg-surface-container text-primary font-semibold transition-colors text-left shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-secondary">format_list_numbered</span>
                    <span>Blok Tembusan</span>
                  </button>
                </div>
              </div>

              <div class="bg-surface-container-low/70 border border-outline-variant rounded-xl p-4 flex flex-col gap-2.5 shadow-sm">
                <div>
                  <h3 class="font-label-md text-sm font-bold text-primary">Format Baku</h3>
                  <p class="font-body-sm text-xs text-on-surface-variant">Muat contoh struktur naskah yang sudah teruji</p>
                </div>

                <div class="flex flex-col gap-1.5">
                  <button type="button" data-load-preset="sk_mhs" class="btn-md inline-flex items-center justify-start gap-2 border border-outline-variant bg-white hover:bg-secondary hover:text-white text-primary font-semibold transition-all text-left shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">school</span>
                    <span>Surat Keterangan Mahasiswa</span>
                  </button>

                  <button type="button" data-load-preset="pengumuman" class="btn-md inline-flex items-center justify-start gap-2 border border-outline-variant bg-white hover:bg-secondary hover:text-white text-primary font-semibold transition-all text-left shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">campaign</span>
                    <span>Pengumuman Resmi PNJ</span>
                  </button>

                  <button type="button" data-load-preset="rekomendasi_2ttd" class="btn-md inline-flex items-center justify-start gap-2 border border-outline-variant bg-white hover:bg-secondary hover:text-white text-primary font-semibold transition-all text-left shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">history_edu</span>
                    <span>Surat Rekomendasi (2 Pejabat)</span>
                  </button>
                </div>
              </div>

            </div>

          </div>

        </section>

        <div class="flex flex-wrap items-center justify-end gap-3 pt-2 pb-6">
          <a href="/Tandain/template" class="btn-md inline-flex border border-outline-variant text-on-surface-variant hover:text-primary hover:bg-surface-container-low font-semibold transition-colors">
            Batal
          </a>

          <button type="submit" class="btn-lg inline-flex items-center gap-2 bg-brand hover:bg-brand-hover text-white font-semibold transition-all shadow-sm active:scale-[0.98]">
            <span class="material-symbols-outlined text-[18px]">save</span>
            <span>Simpan Perubahan</span>
          </button>
        </div>

      </form>

    </div>

    <div id="pdf-export-offscreen" style="position: fixed; left: -10000px; top: 0; width: 210mm; z-index: -1; pointer-events: none; background: #ffffff;">
      <?php
      $letterSheetId = 'pnj-a4-export-sheet';
      $letterBodyId = 'pnj-letter-export-body';
      $letterBodyHtml = '';
      require 'views/templates/official_letter_frame.php';
      ?>
    </div>

    <div id="pdf-test-modal" class="fixed inset-0 z-50 bg-black/50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="pdf-modal-title">
      <div class="bg-surface-container-lowest border border-outline-variant rounded-xl max-w-4xl w-full p-5 shadow-xl flex flex-col gap-4">

        <div class="flex items-center justify-between pb-3 border-b border-surface-container">
          <div class="flex items-center gap-2 text-primary">
            <span class="material-symbols-outlined text-[22px] text-secondary">picture_as_pdf</span>
            <h3 id="pdf-modal-title" class="font-headline-sm text-headline-sm font-semibold">Pratinjau Dokumen PDF (A4)</h3>
          </div>

          <button id="btn-close-pdf-modal" type="button" aria-label="Tutup modal pratinjau PDF" class="p-1 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-low transition-colors">
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>

        <iframe id="pdf-test-iframe" src="about:blank" title="Pratinjau Dokumen PDF" class="w-full h-[70vh] rounded-lg border border-outline-variant bg-surface-container-low"></iframe>

        <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-surface-container">
          <span id="pdf-test-meta" class="font-code-tabular text-xs text-on-surface-variant"></span>

          <div class="flex items-center gap-3">
            <button id="btn-cancel-pdf-modal" type="button" class="btn-md inline-flex border border-outline-variant text-on-surface-variant hover:text-primary font-medium transition-colors">
              Tutup
            </button>

            <a id="btn-download-test-pdf" href="#" download="pratinjau-template.pdf" class="btn-md inline-flex items-center gap-1.5 bg-brand hover:bg-brand-hover text-white font-semibold transition-colors shadow-sm">
              <span class="material-symbols-outlined text-[18px]">download</span>
              <span>Unduh Salinan PDF</span>
            </a>
          </div>
        </div>

      </div>
    </div>

    <template id="schema-row-template">
      <div data-schema-row class="p-4 rounded-xl border border-outline-variant bg-surface-container-low/60 flex flex-col gap-3">

        <div class="flex items-center justify-between gap-2 pb-2 border-b border-outline-variant/60">
          <div class="flex items-center gap-2">
            <span data-row-badge class="inline-flex items-center px-2 py-0.5 rounded bg-surface-container text-primary font-code-tabular text-xs font-bold">Field #1</span>
            <button type="button" data-row-tag-preview class="btn-md hidden font-code-tabular text-[#15803d] bg-[#f0fdf4] border border-[#bbf7d0] font-semibold hover:bg-[#dcfce7] transition-all cursor-pointer" title="Klik untuk menyisipkan tag ke naskah surat"></button>
          </div>

          <button type="button" data-remove-field class="btn-md inline-flex items-center gap-1 text-[#b91c1c] hover:bg-red-50 font-semibold transition-colors">
            <span class="material-symbols-outlined text-[16px]">delete</span>
            <span>Hapus</span>
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

          <div class="flex flex-col gap-1 sm:col-span-2">
            <label class="font-label-sm text-xs font-semibold text-primary">Label Tampilan <span class="text-[#b91c1c]">*</span></label>
            <input type="text" name="field_label[]" data-input="label" required placeholder="Contoh: Keperluan Pengajuan" class="px-3 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-sm text-body-sm focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
          </div>

          <div class="flex flex-col gap-1">
            <div class="flex items-center justify-between">
              <label class="font-label-sm text-xs font-semibold text-primary">Kunci Tag Naskah <span class="text-[#b91c1c]">*</span></label>
              <button type="button" data-toggle-custom-name class="font-label-sm text-[11px] text-secondary hover:underline inline-flex items-center gap-0.5" title="Klik untuk kustomisasi kunci tag manual">
                <span class="material-symbols-outlined text-[13px]">edit</span>
                <span data-toggle-label>Ubah</span>
              </button>
            </div>
            <div class="relative flex items-center">
              <span class="absolute left-2.5 text-on-surface-variant font-code-tabular text-xs font-bold select-none pointer-events-none">{{</span>
              <input type="text" name="field_name[]" data-input="name" required readonly placeholder="otomatis" class="w-full pl-6 pr-6 py-2 bg-surface-container/60 border border-outline-variant rounded-lg text-[#15803d] font-code-tabular text-xs font-bold focus:outline-none transition-all cursor-default select-all" title="Otomatis dari label tampilan. Klik 'Ubah' jika ingin kustom.">
              <span class="absolute right-2.5 text-on-surface-variant font-code-tabular text-xs font-bold select-none pointer-events-none">}}</span>
            </div>
          </div>

          <div class="flex flex-col gap-1">
            <label class="font-label-sm text-xs font-semibold text-primary">Tipe Input</label>
            <select name="field_type[]" data-input="type" class="px-3 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-sm text-body-sm focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
              <option value="text">Teks Singkat (Text)</option>
              <option value="textarea">Teks Panjang (Textarea)</option>
              <option value="number">Angka (Number)</option>
              <option value="date">Tanggal (Date)</option>
              <option value="select">Pilihan Dropdown (Select)</option>
            </select>
          </div>

          <div class="flex flex-col gap-1 sm:col-span-2">
            <label class="font-label-sm text-xs font-semibold text-primary">Kewajiban Isi</label>
            <select name="field_required[]" data-input="required" class="px-3 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-sm text-body-sm focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
              <option value="1">Wajib Diisi</option>
              <option value="0">Opsional</option>
            </select>
          </div>

          <div class="flex flex-col gap-1 sm:col-span-2">
            <label class="font-label-sm text-xs font-semibold text-primary">Contoh Isian (Placeholder)</label>
            <input type="text" name="field_placeholder[]" data-input="placeholder" placeholder="Contoh: Persyaratan beasiswa unggulan" class="px-3 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-sm text-body-sm focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
          </div>

          <div data-options-wrap class="hidden flex-col gap-1 sm:col-span-2 lg:col-span-4">
            <label class="font-label-sm text-xs font-semibold text-primary">Opsi Pilihan (Pisahkan dengan koma) <span class="text-[#b91c1c]">*</span></label>
            <input type="text" name="field_options[]" data-input="options" placeholder="Contoh: Reguler, Karyawan, Transfer" class="px-3 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-sm text-body-sm focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
          </div>

        </div>

      </div>
    </template>

  </main>

  <script src="/Tandain/assets/js/html2canvas.min.js"></script>
  <script src="/Tandain/assets/js/jspdf.umd.min.js"></script>
  <script src="/Tandain/assets/js/pnj-letter-renderer.js"></script>

  <script>
    (function () {
      const INITIAL_SCHEMA_FIELDS = <?= $initialSchemaJson; ?>;

      const DEFAULT_INITIAL_BODY = '<div class="pnj-letter-heading" style="text-align: center; margin: 0 0 16px 0;">\n  <p style="margin: 0; font-size: 11pt; font-weight: 700;"><u>SURAT KETERANGAN</u></p>\n  <p style="margin: 2px 0 0 0; font-size: 10.5pt;">Nomor {{nomor_surat}}</p>\n</div>\n<p style="text-align: justify; margin: 0 0 10px 0;">Yang bertanda tangan di bawah ini menerangkan dengan sesungguhnya bahwa:</p>\n<table style="width: 100%; border-collapse: collapse; margin: 10px 0 14px 0;">\n  <tr>\n    <td style="width: 160px; padding: 2px 0; vertical-align: top;">Nama Lengkap</td>\n    <td style="width: 16px; padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top; font-weight: 700;">{{nama_lengkap}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">NIM / NIP</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{nomor_induk}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">Program Studi</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{program_studi}}</td>\n  </tr>\n</table>\n<p style="text-align: justify; margin: 0 0 10px 0;">Adalah benar mahasiswa terdaftar aktif di Politeknik Negeri Jakarta pada semester berjalan.</p>\n<p style="text-align: justify; margin: 0 0 20px 0;">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>\n<table style="width: 100%; border-collapse: collapse; margin-top: 24px; page-break-inside: avoid;">\n  <tr>\n    <td style="width: 50%;"></td>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-left: 10px;">\n      <p style="margin: 0 0 2px 0;">Depok, {{tanggal_surat}}</p>\n      <p style="margin: 0 0 4px 0;">Ketua Jurusan Teknik Informatika dan Komputer,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_1}}</div>\n    </td>\n  </tr>\n</table>\n<p style="margin: 16px 0 0 0;"><br></p>\n';

      const PRESETS = {
        sk_mhs: '<div class="pnj-letter-heading" style="text-align: center; margin: 0 0 16px 0;">\n  <p style="margin: 0; font-size: 11pt; font-weight: 700;"><u>SURAT KETERANGAN</u></p>\n  <p style="margin: 2px 0 0 0; font-size: 10.5pt;">Nomor {{nomor_surat}}</p>\n</div>\n<p style="text-align: justify; margin: 0 0 10px 0;">Yang bertanda tangan di bawah ini menerangkan dengan sesungguhnya bahwa:</p>\n<table style="width: 100%; border-collapse: collapse; margin: 10px 0 14px 0;">\n  <tr>\n    <td style="width: 160px; padding: 2px 0; vertical-align: top;">Nama Lengkap</td>\n    <td style="width: 16px; padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top; font-weight: 700;">{{nama_lengkap}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">NIM / NIP</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{nomor_induk}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">Program Studi</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{program_studi}}</td>\n  </tr>\n</table>\n<p style="text-align: justify; margin: 0 0 10px 0;">Adalah benar mahasiswa terdaftar aktif pada jurusan kami untuk tahun akademik berjalan dan berkelakuan baik dalam mengikuti seluruh kegiatan akademik di Politeknik Negeri Jakarta.</p>\n<p style="text-align: justify; margin: 0 0 20px 0;">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>\n<table style="width: 100%; border-collapse: collapse; margin-top: 24px; page-break-inside: avoid;">\n  <tr>\n    <td style="width: 50%;"></td>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-left: 10px;">\n      <p style="margin: 0 0 2px 0;">Depok, {{tanggal_surat}}</p>\n      <p style="margin: 0 0 4px 0;">Ketua Jurusan Teknik Informatika dan Komputer,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_1}}</div>\n    </td>\n  </tr>\n</table>\n<p style="margin: 16px 0 0 0;"><br></p>\n',
        pengumuman: '<div class="pnj-letter-heading" style="text-align: center; margin: 0 0 16px 0;">\n  <p style="margin: 0; font-size: 11pt; font-weight: 700; letter-spacing: 0.5px;">PENGUMUMAN</p>\n  <p style="margin: 2px 0 0 0; font-size: 10.5pt;">Nomor 1053/DST/PL3.A/B/DV.01.00/2026</p>\n  <p style="margin: 12px 0 0 0; font-size: 10.5pt;">tentang</p>\n  <p style="margin: 2px 0 0 0; font-size: 10.5pt; font-weight: 700;">Perubahan Mekanisme Pengajuan Surat Keterangan Mahasiswa</p>\n</div>\n<p style="text-align: justify; margin: 0 0 12px 0;">Diberitahukan kepada seluruh mahasiswa Politeknik Negeri Jakarta bahwa terdapat perubahan mekanisme pengajuan surat keterangan mahasiswa yang semula diajukan melalui laman surat-akademik.pnj.ac.id, untuk sementara waktu diajukan melalui link sebagai berikut :</p>\n<p style="text-align: center; margin: 12px 0; font-weight: 700;"><a href="https://s.pnj.ac.id/SuratKeteranganOnline" style="color: #000000; text-decoration: underline;">https://s.pnj.ac.id/SuratKeteranganOnline</a></p>\n<div style="text-align: center; margin: 14px 0;">{{qr_verifikasi}}</div>\n<p style="text-align: justify; margin: 0 0 12px 0;">Sehubungan dengan perubahan tersebut, seluruh mahasiswa yang membutuhkan surat keterangan diharapkan untuk mengajukan permohonan melalui tautan yang telah disediakan dengan mengisi data dan melengkapi persyaratan sesuai dengan ketentuan yang berlaku.</p>\n<p style="text-align: justify; margin: 0 0 12px 0;">Dengan adanya perubahan mekanisme ini, diharapkan proses pengajuan dan pelayanan surat keterangan mahasiswa dapat berjalan lebih tertib, efektif, dan mudah diakses.</p>\n<p style="text-align: justify; margin: 0 0 20px 0;">Demikian pengumuman ini disampaikan untuk menjadi perhatian seluruh mahasiswa. Atas perhatian dan kerja samanya, diucapkan terima kasih.</p>\n<table style="width: 100%; border-collapse: collapse; margin-top: 20px; page-break-inside: avoid;">\n  <tr>\n    <td style="width: 50%;"></td>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-left: 10px;">\n      <p style="margin: 0 0 2px 0;">2 November 2026</p>\n      <p style="margin: 0 0 4px 0;">Wakil Direktur Bidang Akademik,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_1}}</div>\n    </td>\n  </tr>\n</table>\n<div class="pnj-tembusan" style="margin-top: 18px; font-size: 10pt; line-height: 1.35; text-align: left;">\n  <p style="margin: 0 0 2px 0;">Tembusan :</p>\n  <ol style="margin: 2px 0 0 0; padding-left: 20px;">\n    <li>Direktur;</li>\n    <li>Para Wakil Direktur;</li>\n    <li>Para Ketua Jurusan;</li>\n    <li>Koordinator PSDKU Kab. Demak;</li>\n    <li>Koordinator PSDKU Kota Pekalongan;</li>\n    <li>Kabag. Akademik dan Kemahasiswaan.</li>\n  </ol>\n</div>\n<p style="margin: 16px 0 0 0;"><br></p>\n',
        rekomendasi_2ttd: '<div class="pnj-letter-heading" style="text-align: center; margin: 0 0 16px 0;">\n  <p style="margin: 0; font-size: 11pt; font-weight: 700;"><u>SURAT PENGANTAR PRAKTEK KERJA LAPANGAN</u></p>\n  <p style="margin: 2px 0 0 0; font-size: 10.5pt;">Nomor {{nomor_surat}}</p>\n</div>\n<p style="text-align: justify; margin: 0 0 10px 0;">Pimpinan Jurusan Teknik Informatika dan Komputer Politeknik Negeri Jakarta dengan ini menerangkan bahwa mahasiswa berikut:</p>\n<table style="width: 100%; border-collapse: collapse; margin: 10px 0 14px 0;">\n  <tr>\n    <td style="width: 160px; padding: 2px 0; vertical-align: top;">Nama Lengkap</td>\n    <td style="width: 16px; padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top; font-weight: 700;">{{nama_lengkap}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">NIM / NIP</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{nomor_induk}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">Program Studi</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{program_studi}}</td>\n  </tr>\n</table>\n<p style="text-align: justify; margin: 0 0 10px 0;">Diberikan rekomendasi dan pengantar resmi untuk melaksanakan kegiatan Praktek Kerja Lapangan (PKL) / Magang Industri pada instansi mitra sesuai ketentuan kurikulum pendidikan vokasi Politeknik Negeri Jakarta.</p>\n<p style="text-align: justify; margin: 0 0 20px 0;">Demikian surat pengantar ini diberikan untuk dapat dipergunakan sebagaimana mestinya.</p>\n<table style="width: 100%; border-collapse: collapse; margin-top: 24px; page-break-inside: avoid;">\n  <tr>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-right: 15px;">\n      <p style="margin: 0 0 2px 0;">Mengetahui,</p>\n      <p style="margin: 0 0 4px 0;">Koordinator Program Studi,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_1}}</div>\n    </td>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-left: 15px;">\n      <p style="margin: 0 0 2px 0;">Depok, {{tanggal_surat}}</p>\n      <p style="margin: 0 0 4px 0;">Ketua Jurusan TIK,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_2}}</div>\n    </td>\n  </tr>\n</table>\n<p style="margin: 16px 0 0 0;"><br></p>\n'
      };

      const SNIPPETS = {
        title: '<div class="pnj-letter-heading" style="text-align: center; margin: 0 0 16px 0;">\n  <p style="margin: 0; font-size: 11pt; font-weight: 700;"><u>SURAT KETERANGAN</u></p>\n  <p style="margin: 2px 0 0 0; font-size: 10.5pt;">Nomor {{nomor_surat}}</p>\n</div>\n',
        paragraph: '<p style="text-align: justify; margin: 0 0 12px 0;">Yang bertanda tangan di bawah ini menerangkan dengan sesungguhnya bahwa mahasiswa Politeknik Negeri Jakarta berikut terdaftar aktif pada semester berjalan.</p>\n',
        applicant_table: '<table style="width: 100%; border-collapse: collapse; margin: 10px 0 14px 0;">\n  <tr>\n    <td style="width: 160px; padding: 2px 0; vertical-align: top;">Nama Lengkap</td>\n    <td style="width: 16px; padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top; font-weight: 700;">{{nama_lengkap}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">NIM / NIP</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{nomor_induk}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">Program Studi</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{program_studi}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">Email</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{email}}</td>\n  </tr>\n</table>\n',
        ttd_single: '<table style="width: 100%; border-collapse: collapse; margin-top: 24px; page-break-inside: avoid;">\n  <tr>\n    <td style="width: 50%;"></td>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-left: 10px;">\n      <p style="margin: 0 0 2px 0;">Depok, {{tanggal_surat}}</p>\n      <p style="margin: 0 0 4px 0;">Ketua Jurusan Teknik Informatika dan Komputer,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_1}}</div>\n    </td>\n  </tr>\n</table>\n<p style="margin: 16px 0 0 0;"><br></p>\n',
        ttd_double: '<table style="width: 100%; border-collapse: collapse; margin-top: 24px; page-break-inside: avoid;">\n  <tr>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-right: 15px;">\n      <p style="margin: 0 0 2px 0;">Mengetahui,</p>\n      <p style="margin: 0 0 4px 0;">Koordinator Program Studi,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_1}}</div>\n    </td>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-left: 15px;">\n      <p style="margin: 0 0 2px 0;">Depok, {{tanggal_surat}}</p>\n      <p style="margin: 0 0 4px 0;">Ketua Jurusan TIK,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_2}}</div>\n    </td>\n  </tr>\n</table>\n<p style="margin: 16px 0 0 0;"><br></p>\n',
        tembusan: '<div class="pnj-tembusan" style="margin-top: 18px; font-size: 10pt; line-height: 1.35; text-align: left;">\n  <p style="margin: 0 0 2px 0;">Tembusan :</p>\n  <ol style="margin: 2px 0 0 0; padding-left: 20px;">\n    <li>Wakil Direktur Bidang Akademik;</li>\n    <li>Ketua Jurusan terkait;</li>\n    <li>Arsip.</li>\n  </ol>\n</div>\n<p style="margin: 16px 0 0 0;"><br></p>\n'
      };

      const builderForm = document.getElementById('template-builder-form');
      const clientErrorBox = document.getElementById('builder-client-error');
      const titleInput = document.getElementById('template-title');
      const codeInput = document.getElementById('template-code');
      const editableEl = document.getElementById('pnj-letter-body-editable');
      const hiddenHtmlInput = document.getElementById('html-content-hidden');
      const schemaContainer = document.getElementById('schema-fields-container');
      const schemaEmptyState = document.getElementById('schema-empty-state');
      const schemaTemplate = document.getElementById('schema-row-template');
      const addFieldBtn = document.getElementById('btn-add-field');
      const dynamicCheatSheet = document.getElementById('cheat-sheet-dynamic');
      const exportSheetEl = document.getElementById('pnj-a4-export-sheet');
      const testPdfBtn = document.getElementById('btn-test-pdf');
      const testPdfLabel = document.getElementById('btn-test-pdf-label');
      const pdfModal = document.getElementById('pdf-test-modal');
      const pdfIframe = document.getElementById('pdf-test-iframe');
      const pdfMeta = document.getElementById('pdf-test-meta');
      const pdfDownloadLink = document.getElementById('btn-download-test-pdf');
      const closePdfModalBtn = document.getElementById('btn-close-pdf-modal');
      const cancelPdfModalBtn = document.getElementById('btn-cancel-pdf-modal');

      let activePdfBlobUrl = null;

      function syncHiddenInput() {
        if (editableEl && hiddenHtmlInput) {
          hiddenHtmlInput.value = editableEl.innerHTML.trim();
        }
      }

      function ensureTrailingParagraph() {
        if (!editableEl) {
          return;
        }

        const lastChild = editableEl.lastElementChild;

        if (!lastChild || lastChild.tagName !== 'P') {
          const p = document.createElement('p');
          p.style.margin = '16px 0 0 0';
          p.innerHTML = '<br>';
          editableEl.appendChild(p);
        }
      }

      function getActiveAlignment() {
        if (document.queryCommandState) {
          if (document.queryCommandState('justifyCenter')) {
            return 'justifyCenter';
          }

          if (document.queryCommandState('justifyRight')) {
            return 'justifyRight';
          }

          if (document.queryCommandState('justifyFull')) {
            return 'justifyFull';
          }

          if (document.queryCommandState('justifyLeft')) {
            return 'justifyLeft';
          }
        }

        const sel = window.getSelection();

        if (!sel || sel.rangeCount === 0 || !sel.anchorNode) {
          return 'justifyLeft';
        }

        let node = sel.anchorNode;

        if (node.nodeType === 3) {
          node = node.parentNode;
        }

        if (!editableEl || !editableEl.contains(node)) {
          return null;
        }

        let current = node;

        while (current && current !== editableEl) {
          if (current.nodeType === 1) {
            const inlineAlign = current.style ? current.style.textAlign : '';

            if (inlineAlign === 'center') {
              return 'justifyCenter';
            }

            if (inlineAlign === 'right' || inlineAlign === 'end') {
              return 'justifyRight';
            }

            if (inlineAlign === 'justify') {
              return 'justifyFull';
            }

            if (inlineAlign === 'left' || inlineAlign === 'start') {
              return 'justifyLeft';
            }

            const computedAlign = window.getComputedStyle(current).textAlign;

            if (computedAlign === 'center') {
              return 'justifyCenter';
            }

            if (computedAlign === 'right' || computedAlign === 'end') {
              return 'justifyRight';
            }

            if (computedAlign === 'justify') {
              return 'justifyFull';
            }

            if (computedAlign === 'left' || computedAlign === 'start') {
              return 'justifyLeft';
            }
          }

          current = current.parentNode;
        }

        return 'justifyLeft';
      }

      function updateToolbarActiveStates() {
        if (!editableEl) {
          return;
        }

        const sel = window.getSelection();
        let isInsideEditor = false;

        if (sel && sel.rangeCount > 0 && sel.anchorNode) {
          let checkNode = sel.anchorNode;

          if (checkNode.nodeType === 3) {
            checkNode = checkNode.parentNode;
          }

          if (checkNode && (checkNode === editableEl || editableEl.contains(checkNode))) {
            isInsideEditor = true;
          }
        }

        const activeAlign = isInsideEditor ? getActiveAlignment() : null;

        document.querySelectorAll('[data-cmd]').forEach(function (button) {
          const cmd = button.getAttribute('data-cmd');
          let isActive = false;

          if (isInsideEditor && cmd) {
            if (cmd === 'justifyLeft' || cmd === 'justifyCenter' || cmd === 'justifyRight' || cmd === 'justifyFull') {
              if (cmd === activeAlign) {
                isActive = true;
              }
            } else {
              try {
                if (document.queryCommandState && document.queryCommandState(cmd)) {
                  isActive = true;
                }
              } catch (e) {
                isActive = false;
              }
            }
          }

          if (isActive) {
            button.classList.add('is-active');
            button.setAttribute('aria-pressed', 'true');
          } else {
            button.classList.remove('is-active');
            button.setAttribute('aria-pressed', 'false');
          }
        });
      }

      function showClientError(message) {
        if (clientErrorBox) {
          showNotice(clientErrorBox, message);
          clientErrorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }

      function clearClientError() {
        if (clientErrorBox) {
          hideNotice(clientErrorBox);
        }
      }

      const STOP_WORDS = new Set([
        'dan', 'atau', 'yang', 'di', 'ke', 'dari', 'untuk', 'pada',
        'saat', 'dengan', 'ini', 'oleh', 'bagi', 'tentang', 'sebagai'
      ]);

      const PHRASE_MAP = {
        'nama lengkap': 'nama',
        'keperluan pengajuan': 'keperluan',
        'keperluan surat': 'keperluan',
        'perihal pengajuan': 'perihal',
        'perihal surat': 'perihal',
        'tujuan pengajuan': 'tujuan',
        'tujuan magang': 'tujuan_magang',
        'tujuan surat': 'tujuan',
        'alamat lengkap': 'alamat',
        'alamat domisili': 'alamat',
        'alasan permohonan': 'alasan',
        'alasan pengajuan': 'alasan',
        'nomor telepon': 'telepon',
        'no telepon': 'telepon',
        'nomor hp': 'no_hp',
        'no hp': 'no_hp',
        'nomor whatsapp': 'no_wa',
        'no wa': 'no_wa',
        'nama instansi': 'instansi',
        'nama perusahaan': 'perusahaan'
      };

      function simplifyLabelToSnakeCase(rawLabel, trimEdges) {
        let text = String(rawLabel || '').toLowerCase().trim();

        if (text === '') {
          return '';
        }

        const phrases = Object.keys(PHRASE_MAP);

        for (let i = 0; i < phrases.length; i++) {
          const phrase = phrases[i];

          if (text === phrase) {
            return PHRASE_MAP[phrase];
          }
        }

        for (let i = 0; i < phrases.length; i++) {
          const phrase = phrases[i];
          const pattern = new RegExp('\\b' + phrase + '\\b', 'g');
          text = text.replace(pattern, PHRASE_MAP[phrase]);
        }

        const rawWords = text.replace(/[^a-z0-9]+/g, ' ').trim().split(/\s+/);
        const filtered = rawWords.filter(function (word) {
          return word && !STOP_WORDS.has(word);
        });

        const activeWords = filtered.length > 0 ? filtered : rawWords;
        const slicedWords = activeWords.slice(0, 3);
        let result = slicedWords.join('_');

        if (trimEdges) {
          result = result.replace(/_+/g, '_').replace(/^_+|_+$/g, '');
        }

        return result;
      }

      function slugifySnakeCase(rawText, trimEdges) {
        const text = String(rawText || '').replace(/([a-z0-9])([A-Z])/g, '$1_$2').toLowerCase();
        let normalized = text.replace(/[^a-z0-9_]+/g, '_');

        if (trimEdges) {
          normalized = normalized.replace(/_+/g, '_').replace(/^_+|_+$/g, '');
        }

        return normalized;
      }

      function normalizeTemplateCode(rawCode) {
        return String(rawCode || '').toUpperCase().replace(/\s+/g, '-').replace(/[^A-Z0-9_-]/g, '');
      }

      function readSchemaRows() {
        const rows = Array.prototype.slice.call(schemaContainer.querySelectorAll('[data-schema-row]'));
        const items = [];

        rows.forEach(function (row) {
          const labelInput = row.querySelector('[data-input="label"]');
          const nameInput = row.querySelector('[data-input="name"]');
          const typeInput = row.querySelector('[data-input="type"]');
          const requiredInput = row.querySelector('[data-input="required"]');
          const placeholderInput = row.querySelector('[data-input="placeholder"]');
          const optionsInput = row.querySelector('[data-input="options"]');
          const rawOptions = optionsInput ? optionsInput.value : '';
          const parsedOptions = rawOptions.split(',').map(function (opt) {
            return opt.trim();
          }).filter(function (opt) {
            return opt !== '';
          });

          items.push({
            rowEl: row,
            labelEl: labelInput,
            nameEl: nameInput,
            optionsEl: optionsInput,
            label: labelInput ? labelInput.value.trim() : '',
            name: slugifySnakeCase(nameInput ? nameInput.value : '', true),
            type: typeInput ? typeInput.value : 'text',
            required: requiredInput ? requiredInput.value === '1' : true,
            placeholder: placeholderInput ? placeholderInput.value.trim() : '',
            options: parsedOptions
          });
        });

        return items;
      }

      function updateRowNumbersAndPreviewBadges() {
        const rows = Array.prototype.slice.call(schemaContainer.querySelectorAll('[data-schema-row]'));

        rows.forEach(function (row, index) {
          const badgeEl = row.querySelector('[data-row-badge]');
          const nameInput = row.querySelector('[data-input="name"]');
          const tagPreviewEl = row.querySelector('[data-row-tag-preview]');
          const cleanName = slugifySnakeCase(nameInput ? nameInput.value : '', true);

          if (badgeEl) {
            badgeEl.textContent = 'Field #' + (index + 1);
          }

          if (tagPreviewEl) {
            if (cleanName !== '') {
              tagPreviewEl.textContent = '{{' + cleanName + '}}';
              tagPreviewEl.classList.remove('hidden');
              tagPreviewEl.classList.add('inline-flex');
            } else {
              tagPreviewEl.textContent = '';
              tagPreviewEl.classList.add('hidden');
              tagPreviewEl.classList.remove('inline-flex');
            }
          }
        });

        if (schemaEmptyState) {
          schemaEmptyState.classList.toggle('hidden', rows.length > 0);
        }
      }

      function renderDynamicCheatSheet() {
        const items = readSchemaRows().filter(function (item) {
          return item.name !== '';
        });

        dynamicCheatSheet.innerHTML = '';

        if (items.length === 0) {
          const emptyHint = document.createElement('span');
          emptyHint.className = 'text-xs text-on-surface-variant/80 italic';
          emptyHint.textContent = 'Belum ada kolom isian formulir.';
          dynamicCheatSheet.appendChild(emptyHint);
        } else {
          items.forEach(function (item) {
            const tagText = '{{' + item.name + '}}';
            const chipBtn = document.createElement('button');

            chipBtn.type = 'button';
            chipBtn.dataset.insertTag = tagText;
            chipBtn.title = item.label !== '' ? item.label : item.name;
            chipBtn.className = 'px-2.5 py-1 rounded-md border border-[#bfdbfe] bg-[#eff6ff] hover:bg-[#dbeafe] text-[#004B87] font-code-tabular text-xs font-semibold transition-colors shadow-sm';
            chipBtn.textContent = tagText;

            chipBtn.addEventListener('mousedown', function (e) {
              e.preventDefault();
            });

            chipBtn.addEventListener('click', function () {
              insertAtCursor(tagText);
            });

            dynamicCheatSheet.appendChild(chipBtn);
          });
        }
      }

      let savedSelectionRange = null;

      function saveCurrentSelection() {
        const sel = window.getSelection();

        if (sel && sel.rangeCount > 0) {
          const range = sel.getRangeAt(0);

          if (editableEl && editableEl.contains(range.commonAncestorContainer)) {
            savedSelectionRange = range.cloneRange();
          }
        }
      }

      function insertAtCursor(text) {
        if (!editableEl) return;

        let range = null;
        const sel = window.getSelection();

        if (savedSelectionRange && editableEl.contains(savedSelectionRange.commonAncestorContainer)) {
          range = savedSelectionRange.cloneRange();
        } else if (sel && sel.rangeCount > 0) {
          const candidateRange = sel.getRangeAt(0);

          if (editableEl.contains(candidateRange.commonAncestorContainer)) {
            range = candidateRange.cloneRange();
          }
        }

        if (!range) {
          ensureTrailingParagraph();
          const lastChild = editableEl.lastElementChild;
          range = document.createRange();

          if (lastChild) {
            range.selectNodeContents(lastChild);
            range.collapse(false);
          } else {
            range.selectNodeContents(editableEl);
            range.collapse(false);
          }
        }

        editableEl.focus();

        if (sel) {
          sel.removeAllRanges();
          sel.addRange(range);
        }

        range.deleteContents();

        if (text.indexOf('<') !== -1) {
          const temp = document.createElement('div');
          temp.innerHTML = text;
          const frag = document.createDocumentFragment();
          let node;
          let lastNode;

          while ((node = temp.firstChild)) {
            lastNode = frag.appendChild(node);
          }

          range.insertNode(frag);

          if (lastNode) {
            range = range.cloneRange();
            range.setStartAfter(lastNode);
            range.collapse(true);
            sel.removeAllRanges();
            sel.addRange(range);
          }
        } else {
          const textNode = document.createTextNode(text);
          range.insertNode(textNode);
          range.setStartAfter(textNode);
          range.collapse(true);
          sel.removeAllRanges();
          sel.addRange(range);
        }

        savedSelectionRange = range.cloneRange();

        ensureTrailingParagraph();
        syncHiddenInput();
      }

      function attachRowEvents(row) {
        const labelInput = row.querySelector('[data-input="label"]');
        const nameInput = row.querySelector('[data-input="name"]');
        const toggleBtn = row.querySelector('[data-toggle-custom-name]');
        const toggleLabel = row.querySelector('[data-toggle-label]');
        const tagPreviewBtn = row.querySelector('[data-row-tag-preview]');
        const typeInput = row.querySelector('[data-input="type"]');
        const optionsWrap = row.querySelector('[data-options-wrap]');
        const removeBtn = row.querySelector('[data-remove-field]');

        if (tagPreviewBtn) {
          tagPreviewBtn.addEventListener('click', function () {
            const currentTag = tagPreviewBtn.textContent.trim();

            if (currentTag !== '') {
              insertAtCursor(currentTag);
            }
          });
        }

        if (toggleBtn && nameInput) {
          toggleBtn.addEventListener('click', function () {
            const isManual = nameInput.dataset.manual === 'true';

            if (isManual) {
              nameInput.dataset.manual = 'false';
              nameInput.readOnly = true;
              nameInput.classList.add('bg-surface-container/60', 'cursor-default');
              nameInput.classList.remove('bg-surface-container-lowest', 'cursor-text');

              if (toggleLabel) {
                toggleLabel.textContent = 'Ubah';
              }

              nameInput.value = simplifyLabelToSnakeCase(labelInput ? labelInput.value : '', true);
            } else {
              nameInput.dataset.manual = 'true';
              nameInput.readOnly = false;
              nameInput.classList.remove('bg-surface-container/60', 'cursor-default');
              nameInput.classList.add('bg-surface-container-lowest', 'cursor-text');

              if (toggleLabel) {
                toggleLabel.textContent = 'Otomatis';
              }

              nameInput.focus();
              nameInput.select();
            }

            updateRowNumbersAndPreviewBadges();
            renderDynamicCheatSheet();
          });
        }

        if (labelInput && nameInput) {
          labelInput.addEventListener('input', function () {
            if (nameInput.dataset.manual !== 'true') {
              nameInput.value = simplifyLabelToSnakeCase(labelInput.value, false);
              updateRowNumbersAndPreviewBadges();
              renderDynamicCheatSheet();
            }
          });

          labelInput.addEventListener('blur', function () {
            if (nameInput.dataset.manual !== 'true') {
              nameInput.value = simplifyLabelToSnakeCase(labelInput.value, true);
              updateRowNumbersAndPreviewBadges();
              renderDynamicCheatSheet();
            }
          });
        }

        if (nameInput) {
          nameInput.addEventListener('input', function () {
            nameInput.dataset.manual = 'true';

            if (toggleLabel) {
              toggleLabel.textContent = 'Otomatis';
            }

            updateRowNumbersAndPreviewBadges();
            renderDynamicCheatSheet();
          });

          nameInput.addEventListener('blur', function () {
            nameInput.value = slugifySnakeCase(nameInput.value, true);
            updateRowNumbersAndPreviewBadges();
            renderDynamicCheatSheet();
          });
        }

        if (typeInput && optionsWrap) {
          function checkType() {
            const isSelect = typeInput.value === 'select';
            optionsWrap.classList.toggle('hidden', !isSelect);
            optionsWrap.classList.toggle('flex', isSelect);
          }

          typeInput.addEventListener('change', checkType);
          checkType();
        }

        if (removeBtn) {
          removeBtn.addEventListener('click', function () {
            row.remove();
            updateRowNumbersAndPreviewBadges();
            renderDynamicCheatSheet();
          });
        }
      }

      function addSchemaRow(initialData) {
        if (!schemaTemplate || !schemaContainer) return;

        const fragment = schemaTemplate.content.cloneNode(true);
        const row = fragment.querySelector('[data-schema-row]');

        if (!row) return;

        if (initialData) {
          const labelInput = row.querySelector('[data-input="label"]');
          const nameInput = row.querySelector('[data-input="name"]');
          const typeInput = row.querySelector('[data-input="type"]');
          const requiredInput = row.querySelector('[data-input="required"]');
          const placeholderInput = row.querySelector('[data-input="placeholder"]');
          const optionsInput = row.querySelector('[data-input="options"]');

          if (labelInput) labelInput.value = initialData.label || '';
          if (nameInput) {
            nameInput.value = initialData.name || '';
            if (initialData.name) {
              nameInput.dataset.manual = 'true';
            }
          }
          if (typeInput && initialData.type) typeInput.value = initialData.type;
          if (requiredInput) requiredInput.value = initialData.required === false ? '0' : '1';
          if (placeholderInput) placeholderInput.value = initialData.placeholder || '';
          if (optionsInput) {
            if (Array.isArray(initialData.options)) {
              optionsInput.value = initialData.options.join(', ');
            } else if (typeof initialData.options === 'string') {
              optionsInput.value = initialData.options;
            }
          }
        }

        attachRowEvents(row);
        schemaContainer.appendChild(row);

        updateRowNumbersAndPreviewBadges();
        renderDynamicCheatSheet();
      }

      if (addFieldBtn) {
        addFieldBtn.addEventListener('click', function () {
          addSchemaRow();
        });
      }

      if (codeInput) {
        codeInput.addEventListener('input', function () {
          codeInput.value = normalizeTemplateCode(codeInput.value);
        });

        codeInput.addEventListener('blur', function () {
          codeInput.value = normalizeTemplateCode(codeInput.value);
        });
      }

      if (editableEl) {
        if (editableEl.innerHTML.trim() === '') {
          editableEl.innerHTML = DEFAULT_INITIAL_BODY;
        }

        ensureTrailingParagraph();
        syncHiddenInput();
        updateToolbarActiveStates();

        editableEl.addEventListener('input', function () {
          syncHiddenInput();
          updateToolbarActiveStates();
        });

        editableEl.addEventListener('blur', function () {
          syncHiddenInput();
        });

        editableEl.addEventListener('keyup', function () {
          saveCurrentSelection();
          updateToolbarActiveStates();
        });

        editableEl.addEventListener('mouseup', function () {
          saveCurrentSelection();
          updateToolbarActiveStates();
        });

        editableEl.addEventListener('focus', function () {
          saveCurrentSelection();
          updateToolbarActiveStates();
        });

        document.addEventListener('selectionchange', function () {
          saveCurrentSelection();
          updateToolbarActiveStates();
        });

        editableEl.addEventListener('click', function (e) {
          if (e.target === editableEl) {
            ensureTrailingParagraph();
            const lastP = editableEl.lastElementChild;

            if (lastP) {
              const range = document.createRange();
              range.selectNodeContents(lastP);
              range.collapse(false);
              const sel = window.getSelection();

              if (sel) {
                sel.removeAllRanges();
                sel.addRange(range);
              }
            }
          }
        });

        editableEl.addEventListener('keydown', function (e) {
          const sel = window.getSelection();

          if (!sel || sel.rangeCount === 0) {
            return;
          }

          const range = sel.getRangeAt(0);
          let cell = range.commonAncestorContainer;

          while (cell && cell !== editableEl && cell.nodeName !== 'TD' && cell.nodeName !== 'TH') {
            cell = cell.parentNode;
          }

          if (cell && (cell.nodeName === 'TD' || cell.nodeName === 'TH')) {
            const table = cell.closest('table');
            const row = cell.parentElement;
            const isLastRow = row && table && row === table.rows[table.rows.length - 1];
            const isLastCell = row && cell === row.cells[row.cells.length - 1];

            let block = range.commonAncestorContainer;

            while (block && block.parentNode !== cell) {
              block = block.parentNode;
            }

            if (e.key === 'Enter') {
              const isBlockEmpty = !block || block.textContent.trim() === '' || block.innerHTML === '<br>';

              if (isBlockEmpty && isLastRow && isLastCell) {
                e.preventDefault();

                if (block && cell.children.length > 1) {
                  block.remove();
                }

                ensureTrailingParagraph();
                let targetP = table.nextElementSibling;

                if (!targetP || targetP.tagName !== 'P') {
                  targetP = document.createElement('p');
                  targetP.style.margin = '16px 0 0 0';
                  targetP.innerHTML = '<br>';
                  table.parentNode.insertBefore(targetP, table.nextSibling);
                }

                const newRange = document.createRange();
                newRange.selectNodeContents(targetP);
                newRange.collapse(false);
                sel.removeAllRanges();
                sel.addRange(newRange);

                syncHiddenInput();
                return;
              }
            }

            if (e.key === 'ArrowDown' && isLastRow && isLastCell) {
              if (block === cell.lastElementChild) {
                ensureTrailingParagraph();
                const targetP = table.nextElementSibling;

                if (targetP) {
                  e.preventDefault();
                  const newRange = document.createRange();
                  newRange.selectNodeContents(targetP);
                  newRange.collapse(false);
                  sel.removeAllRanges();
                  sel.addRange(newRange);
                  return;
                }
              }
            }

            if (e.key === 'Backspace') {
              const isBlockEmpty = block && (block.textContent.trim() === '' || block.innerHTML === '<br>');

              if (isBlockEmpty && block.previousElementSibling) {
                e.preventDefault();
                const prev = block.previousElementSibling;
                block.remove();

                const newRange = document.createRange();
                newRange.selectNodeContents(prev);
                newRange.collapse(false);
                sel.removeAllRanges();
                sel.addRange(newRange);

                syncHiddenInput();
                return;
              }

              if (range.collapsed && range.startOffset === 0) {
                if (cell.cellIndex > 0) {
                  e.preventDefault();
                  const prevCell = row.cells[cell.cellIndex - 1];
                  const newRange = document.createRange();
                  newRange.selectNodeContents(prevCell);
                  newRange.collapse(false);
                  sel.removeAllRanges();
                  sel.addRange(newRange);
                  return;
                } else if (cell.cellIndex === 0) {
                  const isRowEmpty = row.textContent.trim() === '';

                  if (isRowEmpty) {
                    e.preventDefault();
                    const prevRow = row.previousElementSibling;
                    row.remove();

                    if (table.rows.length === 0) {
                      const fallbackP = document.createElement('p');
                      fallbackP.innerHTML = '<br>';
                      fallbackP.style.margin = '0 0 12px 0';
                      table.parentNode.insertBefore(fallbackP, table.nextSibling);
                      table.remove();

                      const newRange = document.createRange();
                      newRange.selectNodeContents(fallbackP);
                      newRange.collapse(false);
                      sel.removeAllRanges();
                      sel.addRange(newRange);
                    } else if (prevRow) {
                      const targetCell = prevRow.cells[prevRow.cells.length - 1];
                      const newRange = document.createRange();
                      newRange.selectNodeContents(targetCell);
                      newRange.collapse(false);
                      sel.removeAllRanges();
                      sel.addRange(newRange);
                    }

                    syncHiddenInput();
                    return;
                  } else if (row.previousElementSibling) {
                    e.preventDefault();
                    const prevRow = row.previousElementSibling;
                    const targetCell = prevRow.cells[prevRow.cells.length - 1];
                    const newRange = document.createRange();
                    newRange.selectNodeContents(targetCell);
                    newRange.collapse(false);
                    sel.removeAllRanges();
                    sel.addRange(newRange);
                    return;
                  } else if (table.previousElementSibling) {
                    e.preventDefault();
                    const prev = table.previousElementSibling;
                    const newRange = document.createRange();
                    newRange.selectNodeContents(prev);
                    newRange.collapse(false);
                    sel.removeAllRanges();
                    sel.addRange(newRange);
                    return;
                  }
                }
              }
            }
          } else {
            if (e.key === 'Backspace') {
              let block = range.commonAncestorContainer;

              while (block && block.parentNode !== editableEl) {
                block = block.parentNode;
              }

              if (block && block.tagName === 'P' && block.previousElementSibling && block.previousElementSibling.tagName === 'TABLE') {
                const isBlockEmpty = block.textContent.trim() === '' || block.innerHTML === '<br>';

                if (isBlockEmpty && range.startOffset === 0 && range.collapsed) {
                  const table = block.previousElementSibling;
                  const lastCell = table.querySelector('tr:last-child td:last-child');

                  if (lastCell) {
                    e.preventDefault();
                    const targetEl = lastCell.lastElementChild || lastCell;
                    const newRange = document.createRange();
                    newRange.selectNodeContents(targetEl);
                    newRange.collapse(false);
                    sel.removeAllRanges();
                    sel.addRange(newRange);
                    return;
                  }
                }
              }
            }
          }
        });
      }

      document.querySelectorAll('[data-cmd]').forEach(function (button) {
        button.addEventListener('mousedown', function (e) {
          e.preventDefault();
        });

        button.addEventListener('click', function (e) {
          e.preventDefault();
          const cmd = button.getAttribute('data-cmd');

          if (cmd && editableEl) {
            editableEl.focus();

            if (cmd === 'justifyLeft') {
              const sel = window.getSelection();

              if (sel && sel.rangeCount > 0) {
                const range = sel.getRangeAt(0);
                let cell = range.commonAncestorContainer;

                while (cell && cell !== editableEl && cell.nodeName !== 'TD' && cell.nodeName !== 'TH') {
                  cell = cell.parentNode;
                }

                if (cell && (cell.nodeName === 'TD' || cell.nodeName === 'TH')) {
                  const table = cell.closest('table');
                  let block = range.commonAncestorContainer;

                  while (block && block.parentNode !== cell) {
                    block = block.parentNode;
                  }

                  const isSignatureCore = block && (
                    block.innerHTML.indexOf('kolom_ttd') !== -1 ||
                    block.textContent.indexOf('NIP') !== -1 ||
                    block.textContent.indexOf('Ketua') !== -1 ||
                    block.textContent.indexOf('Direktur') !== -1 ||
                    block.textContent.indexOf('Koordinator') !== -1 ||
                    block.textContent.indexOf('{{tanggal_surat}}') !== -1
                  );

                  if (block && !isSignatureCore && block.previousElementSibling) {
                    const blockHtml = block.innerHTML;
                    block.remove();

                    ensureTrailingParagraph();
                    let targetP = table.nextElementSibling;

                    if (!targetP || targetP.tagName !== 'P') {
                      targetP = document.createElement('p');
                      targetP.style.margin = '16px 0 0 0';
                      table.parentNode.insertBefore(targetP, table.nextSibling);
                    }

                    targetP.innerHTML = blockHtml;
                    targetP.style.textAlign = 'left';

                    const newRange = document.createRange();
                    newRange.selectNodeContents(targetP);
                    newRange.collapse(false);
                    sel.removeAllRanges();
                    sel.addRange(newRange);

                    syncHiddenInput();
                    updateToolbarActiveStates();
                    return;
                  }
                }
              }
            }

            document.execCommand(cmd, false, null);
            syncHiddenInput();
            updateToolbarActiveStates();
            setTimeout(updateToolbarActiveStates, 10);
          }
        });
      });

      document.querySelectorAll('[data-snippet-key]').forEach(function (button) {
        button.addEventListener('mousedown', function (e) {
          e.preventDefault();
        });

        button.addEventListener('click', function (e) {
          e.preventDefault();
          const key = button.getAttribute('data-snippet-key');
          const snippet = SNIPPETS[key];

          if (snippet) {
            insertAtCursor(snippet);
            updateToolbarActiveStates();
          }
        });
      });

      document.querySelectorAll('[data-insert-tag]').forEach(function (button) {
        button.addEventListener('mousedown', function (e) {
          e.preventDefault();
        });

        button.addEventListener('click', function (e) {
          e.preventDefault();
          const tag = button.getAttribute('data-insert-tag');

          if (tag) {
            insertAtCursor(tag);
            updateToolbarActiveStates();
          }
        });
      });

      document.querySelectorAll('[data-load-preset]').forEach(function (button) {
        button.addEventListener('click', function (e) {
          e.preventDefault();
          const presetKey = button.getAttribute('data-load-preset');
          const presetHtml = PRESETS[presetKey];

          if (presetHtml && editableEl) {
            editableEl.innerHTML = presetHtml;
            ensureTrailingParagraph();
            syncHiddenInput();
            updateToolbarActiveStates();
            editableEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        });
      });

      if (builderForm) {
        builderForm.addEventListener('submit', function (event) {
          clearClientError();
          syncHiddenInput();

          const title = titleInput ? titleInput.value.trim() : '';
          const code = codeInput ? codeInput.value.trim() : '';
          const html = hiddenHtmlInput ? hiddenHtmlInput.value.trim() : '';

          if (title === '') {
            event.preventDefault();
            showClientError('Judul template surat wajib diisi.');
            return;
          }

          if (code === '') {
            event.preventDefault();
            showClientError('Kode template surat wajib diisi.');
            return;
          }

          if (!/^[A-Z0-9_-]+$/.test(code)) {
            event.preventDefault();
            showClientError('Kode template hanya boleh memuat huruf kapital, angka, tanda hubung (-), dan garis bawah (_).');
            return;
          }

          if (html === '') {
            event.preventDefault();
            showClientError('Konten naskah badan surat tidak boleh kosong.');
            return;
          }

          const hasSignerSlot = /\{\{\s*kolom_ttd_\d+\s*\}\}/i.test(html);
          if (!hasSignerSlot) {
            event.preventDefault();
            showClientError('Badan surat wajib memuat minimal satu slot tanda tangan seperti {{kolom_ttd_1}}.');
            return;
          }

          const schemaItems = readSchemaRows();
          const seenNames = {};

          for (let i = 0; i < schemaItems.length; i++) {
            const item = schemaItems[i];

            if (item.label === '') {
              event.preventDefault();
              showClientError('Label tampilan pada Field #' + (i + 1) + ' wajib diisi.');
              if (item.labelEl) item.labelEl.focus();
              return;
            }

            if (item.name === '') {
              event.preventDefault();
              showClientError('Kunci tag pada Field #' + (i + 1) + ' wajib diisi.');
              if (item.nameEl) item.nameEl.focus();
              return;
            }

            if (!/^[a-z0-9_]+$/.test(item.name)) {
              event.preventDefault();
              showClientError('Kunci tag pada Field #' + (i + 1) + ' hanya boleh memuat huruf kecil, angka, dan garis bawah.');
              if (item.nameEl) item.nameEl.focus();
              return;
            }

            if (seenNames[item.name]) {
              event.preventDefault();
              showClientError('Kunci tag "{{' + item.name + '}}" ganda pada Field #' + (i + 1) + '. Setiap kunci tag harus unik.');
              if (item.nameEl) item.nameEl.focus();
              return;
            }

            seenNames[item.name] = true;

            if (item.type === 'select' && item.options.length === 0) {
              event.preventDefault();
              showClientError('Field #' + (i + 1) + ' bertipe Pilihan Dropdown wajib memiliki opsi (pisahkan dengan koma).');
              if (item.optionsEl) item.optionsEl.focus();
              return;
            }
          }
        });
      }

      function buildSampleValues() {
        const values = {
          nama_lengkap: 'Farhan Hariri',
          nomor_induk: '2507411050',
          program_studi: 'Teknik Informatika (TI-2C)',
          email: 'farhan.hariri.tik25@mhsw.pnj.ac.id',
          jabatan: 'Mahasiswa Teknik Informatika',
          nomor_surat: '042/PL3/TIK/PNJ/2026',
          tanggal_surat: window.PnjLetterRenderer ? window.PnjLetterRenderer.formatIndonesianDate() : '15 November 2026'
        };

        const schemaItems = readSchemaRows();

        schemaItems.forEach(function (field) {
          if (field.name !== '') {
            let sample = '';
            if (field.placeholder !== '') {
              sample = field.placeholder.replace(new RegExp('^contoh\\s*:\\s*', 'i'), '').trim();
            } else if (field.type === 'select' && field.options.length > 0) {
              sample = field.options[0];
            } else if (field.type === 'date') {
              sample = window.PnjLetterRenderer ? window.PnjLetterRenderer.formatIndonesianDate() : '15 November 2026';
            } else if (field.type === 'number') {
              sample = '4';
            } else {
              sample = '[' + (field.label || field.name) + ']';
            }
            values[field.name] = sample;
          }
        });

        return values;
      }

      function closePdfModal() {
        if (pdfModal) {
          pdfModal.classList.add('hidden');
          pdfModal.classList.remove('flex');
        }

        if (pdfIframe) {
          pdfIframe.src = 'about:blank';
        }

        if (activePdfBlobUrl) {
          URL.revokeObjectURL(activePdfBlobUrl);
          activePdfBlobUrl = null;
        }
      }

      if (closePdfModalBtn) closePdfModalBtn.addEventListener('click', closePdfModal);
      if (cancelPdfModalBtn) cancelPdfModalBtn.addEventListener('click', closePdfModal);

      if (testPdfBtn) {
        testPdfBtn.addEventListener('click', function () {
          if (!window.PnjLetterRenderer || !exportSheetEl || !editableEl) return;

          syncHiddenInput();
          const rawHtml = editableEl.innerHTML;
          const sampleValues = buildSampleValues();

          window.PnjLetterRenderer.renderIntoSheet(exportSheetEl, rawHtml, sampleValues, { isPreview: true });

          const prevLabel = testPdfLabel ? testPdfLabel.textContent : '';
          if (testPdfBtn) testPdfBtn.disabled = true;
          if (testPdfLabel) testPdfLabel.textContent = 'Membuat PDF...';

          setTimeout(function () {
            window.PnjLetterRenderer.generatePdfBlob(exportSheetEl).then(function (pdfBlob) {
              if (testPdfBtn) testPdfBtn.disabled = false;
              if (testPdfLabel) testPdfLabel.textContent = prevLabel;

              if (activePdfBlobUrl) {
                URL.revokeObjectURL(activePdfBlobUrl);
                activePdfBlobUrl = null;
              }

              activePdfBlobUrl = URL.createObjectURL(pdfBlob);

              if (pdfIframe) pdfIframe.src = activePdfBlobUrl;
              if (pdfDownloadLink) pdfDownloadLink.href = activePdfBlobUrl;
              if (pdfMeta) {
                const kbSize = (pdfBlob.size / 1024).toFixed(1);
                pdfMeta.textContent = 'Ukuran berkas PDF: ' + kbSize + ' KB • Standar ISO 32000-1 (A4)';
              }

              if (pdfModal) {
                pdfModal.classList.remove('hidden');
                pdfModal.classList.add('flex');
              }
            }).catch(function (error) {
              if (testPdfBtn) testPdfBtn.disabled = false;
              if (testPdfLabel) testPdfLabel.textContent = prevLabel;
              showClientError('Gagal merender berkas PDF: ' + (error && error.message ? error.message : error));
            });
          }, 120);
        });
      }

      if (Array.isArray(INITIAL_SCHEMA_FIELDS) && INITIAL_SCHEMA_FIELDS.length > 0) {
        INITIAL_SCHEMA_FIELDS.forEach(function (field) {
          addSchemaRow(field);
        });
      } else {
        updateRowNumbersAndPreviewBadges();
        renderDynamicCheatSheet();
      }
    })();
  </script>

<?php require_once 'views/layouts/footer.php'; ?>

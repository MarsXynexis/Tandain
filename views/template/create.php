<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$oldData = $data['old'] ?? ($old ?? null);
$schemaList = $data['schemaFields'] ?? ($schemaFields ?? []);

$titleValue = $oldData['title'] ?? '';
$codeValue = $oldData['template_code'] ?? '';
$descriptionValue = $oldData['description'] ?? '';
$htmlValue = $oldData['html_content'] ?? '';

if (is_array($oldData)) {
    $isActiveValue = !empty($oldData['is_active']);
} else {
    $isActiveValue = true;
}

$initialSchemaJson = json_encode(array_values(is_array($schemaList) ? $schemaList : []), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <div class="min-w-0 w-full flex flex-col gap-6">

      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Buat Template Surat</h1>
          <p class="font-body-md text-body-md text-on-surface-variant mt-1">Susun template naskah dinas resmi Politeknik Negeri Jakarta dengan isian dinamis dan pengesahan digital</p>
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

      <form id="template-builder-form" method="POST" action="/Tandain/template/store" class="flex flex-col gap-6" novalidate>

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

                  <div class="pnj-letter-body focus:outline-none min-h-[560px] cursor-text p-2 rounded-lg transition-colors focus:bg-blue-50/20" id="pnj-letter-body-editable" contenteditable="true" spellcheck="false"><?= $htmlValue ?: ''; ?></div>

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
                    <button type="button" data-insert-tag="{{jabatan}}" title="Jabatan struktural (Kajur/Kaprodi) atau peran civitas" class="btn-md inline-flex border border-outline-variant bg-white hover:border-[#bfdbfe] hover:bg-[#eff6ff] hover:text-[#004B87] text-primary font-code-tabular font-medium transition-colors shadow-sm">{{jabatan}}</button>
                  </div>
                </div>

                <div class="flex flex-col gap-1.5 pt-2.5 border-t border-outline-variant/60">
                  <span class="text-xs font-semibold text-primary">Kedinasan &amp; Pengesahan</span>
                  <div class="flex flex-wrap gap-1.5">
                    <button type="button" data-insert-tag="{{nomor_surat}}" title="Nomor naskah dinas resmi" class="btn-md inline-flex border border-outline-variant bg-white hover:border-[#bfdbfe] hover:bg-[#eff6ff] hover:text-[#004B87] text-primary font-code-tabular font-medium transition-colors shadow-sm">{{nomor_surat}}</button>
                    <button type="button" data-insert-tag="{{tanggal_surat}}" title="Tanggal naskah dinas" class="btn-md inline-flex border border-outline-variant bg-white hover:border-[#bfdbfe] hover:bg-[#eff6ff] hover:text-[#004B87] text-primary font-code-tabular font-medium transition-colors shadow-sm">{{tanggal_surat}}</button>
                    <button type="button" data-insert-tag="{{kolom_ttd_1}}" title="Slot tanda tangan pejabat 1" class="btn-md inline-flex border border-[#bbf7d0] bg-[#f0fdf4] hover:bg-[#dcfce7] text-[#15803d] font-code-tabular font-semibold transition-colors shadow-sm">{{kolom_ttd_1}}</button>
                    <button type="button" data-insert-tag="{{kolom_ttd_2}}" title="Slot tanda tangan pejabat 2" class="btn-md inline-flex border border-[#bbf7d0] bg-[#f0fdf4] hover:bg-[#dcfce7] text-[#15803d] font-code-tabular font-semibold transition-colors shadow-sm">{{kolom_ttd_2}}</button>
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
            <span>Simpan Template</span>
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
  <script src="/Tandain/assets/js/template-builder.js"></script>

  <script>
    setupTemplateBuilder({
      initialSchemaFields: <?= $initialSchemaJson; ?>,
      loadDefaultBody: <?= !is_array($oldData) ? 'true' : 'false'; ?>
    });
  </script>

<?php require_once 'views/layouts/footer.php'; ?>

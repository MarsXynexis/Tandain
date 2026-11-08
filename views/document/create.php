<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 min-w-0 w-full max-w-none p-5 lg:p-6">

    <div class="mb-5">
      <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight font-bold">Ajukan Dokumen Baru</h1>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1" id="page-subtitle">Unggah berkas PDF dan tentukan alur pihak penandatangan</p>
    </div>

    <form class="space-y-4" method="POST" action="/Tandain/document/create" enctype="multipart/form-data">

      <input id="submission-mode" name="submission_mode" type="hidden" value="upload">

      <section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-4 shadow-sm">

        <div class="flex items-center gap-2 mb-3 pb-2 border-b border-surface-container">
          <span class="material-symbols-outlined text-secondary text-[22px]">description</span>
          <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Berkas &amp; Informasi Dokumen</h2>
        </div>

        <div class="flex items-center gap-2 mb-6 p-1 bg-surface-container rounded-lg max-w-md">
          <button class="flex-1 flex items-center justify-center gap-2 py-2 px-4 rounded-lg bg-[#004B87] hover:bg-[#003d6d] text-white font-label-md text-label-md font-semibold shadow-sm transition" id="tab-upload-btn" type="button">
            <span class="material-symbols-outlined text-[18px]">upload_file</span>
            <span>Unggah Berkas PDF</span>
          </button>

          <button class="flex-1 flex items-center justify-center gap-2 py-2 px-4 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-lowest font-label-md text-label-md font-medium transition" id="tab-template-btn" type="button">
            <span class="material-symbols-outlined text-[18px]">description</span>
            <span>Gunakan Template Surat</span>
          </button>
        </div>

        <div class="space-y-4" id="tab-upload-content">

          <div class="border-2 border-dashed border-outline-variant hover:border-secondary rounded-lg p-6 text-center bg-surface hover:bg-surface-container-low transition-colors cursor-pointer group" id="drop-zone" onclick="document.getElementById('file-upload').click();">
            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-surface-container flex items-center justify-center text-primary-container group-hover:bg-secondary-fixed transition-colors">
              <span class="material-symbols-outlined text-[32px] text-error" style="font-variation-settings: 'FILL' 1;">picture_as_pdf</span>
            </div>

            <p class="font-label-lg text-label-lg text-primary font-semibold" id="drop-zone-text">Tarik dan lepas berkas PDF ke sini, atau klik Telusuri File</p>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 mb-4" id="drop-zone-subtext">Maksimal ukuran berkas 2 MB (Format PDF)</p>

            <input class="hidden" id="file-upload" name="document_file" type="file" accept="application/pdf">

            <button class="inline-flex items-center gap-2 px-4 py-2 border border-outline rounded-lg text-primary-container font-label-md text-label-md bg-white hover:bg-surface-container transition-all" onclick="event.stopPropagation(); document.getElementById('file-upload').click();" type="button">
              <span class="material-symbols-outlined text-[18px]">folder_open</span>
              <span>Telusuri File</span>
            </button>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="doc-title">
              Judul Dokumen <span class="text-error">*</span>
            </label>
            <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition" id="doc-title" name="title" placeholder="Contoh: Surat Rekomendasi Magang MBKM" type="text">
          </div>

          <div>
            <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="doc-expires-at">
              Batas Kedaluwarsa Dokumen <span class="font-normal text-on-surface-variant">(Opsional)</span>
            </label>
            <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition cursor-pointer" id="doc-expires-at" name="expires_at" type="date">
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1.5">Batas waktu legalitas administratif berkas. Kosongkan jika dokumen berlaku selamanya.</p>
          </div>

        </div>

        <div class="space-y-4 hidden" id="tab-template-content">

          <div class="p-3.5 rounded-lg bg-surface-container-low border border-outline-variant flex items-start gap-3">
            <span class="material-symbols-outlined text-blue-600 text-[20px] mt-0.5 shrink-0">info</span>
            <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">Data identitas pemohon (Nama Lengkap, NIM/NIP, dan Program Studi) terisi otomatis dari profil akun Anda.</p>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="template-select">
              Pilih Jenis Template Surat Resmi <span class="text-error">*</span>
            </label>
            <div class="relative">
              <select class="w-full pr-10 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition appearance-none cursor-pointer px-3.5" id="template-select" name="template_code">
                <option value="SK-MAGANG-MBKM" selected>Surat Rekomendasi Magang MBKM (SK-MAGANG-MBKM)</option>
                <option value="SK-AKTIF">Surat Keterangan Mahasiswa Aktif (SK-AKTIF)</option>
                <option value="SK-BEBAS-TANGGUNGAN">Surat Pengantar Bebas Tanggungan (SK-BEBAS-TANGGUNGAN)</option>
              </select>
              <span class="material-symbols-outlined absolute right-3 top-2.5 text-[20px] text-on-surface-variant pointer-events-none">expand_more</span>
            </div>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="template-agency">
              Instansi / Perusahaan Tujuan <span class="text-error">*</span>
            </label>
            <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition" id="template-agency" name="destination_agency" placeholder="PT Telkom Indonesia Tbk" type="text" value="PT Telkom Indonesia Tbk">
          </div>

          <div>
            <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="template-period">
              Periode Pelaksanaan Magang <span class="text-error">*</span>
            </label>
            <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition" id="template-period" name="internship_period" placeholder="Februari 2026 s.d. Juli 2026" type="text" value="Februari 2026 s.d. Juli 2026">
          </div>

          <div>
            <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="template-expires-at">
              Batas Kedaluwarsa Dokumen <span class="font-normal text-on-surface-variant">(Opsional)</span>
            </label>
            <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition cursor-pointer" id="template-expires-at" name="expires_at" type="date" disabled>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1.5">Batas waktu legalitas administratif surat resmi. Kosongkan jika dokumen berlaku selamanya.</p>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="template-notes">
              Catatan untuk Tata Usaha (Opsional)
            </label>
            <textarea class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition leading-relaxed" id="template-notes" name="tu_notes" placeholder="Catatan pengantar mahasiswa ke staf TU jika ada pesan khusus" rows="2"></textarea>
          </div>

        </div>

      </section>

      <section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-4 shadow-sm" id="section-signers">

        <div class="flex flex-col gap-3 border-b border-surface-container mb-3 pb-3">

          <div class="flex flex-col gap-3 w-full">

            <div>
              <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-[22px]">route</span>
                <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Alur Pihak Penandatangan</h2>
              </div>

              <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Tentukan pihak berwenang dan urutan penandatanganan dokumen elektronik</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">

              <div class="flex items-start gap-2.5 order-2">
                <input class="mt-1 w-4 h-4 rounded border-outline-variant text-blue-600 focus:ring-blue-600 cursor-pointer" id="tu-triage-check" name="is_triage" type="checkbox" value="1">
                <label class="cursor-pointer" for="tu-triage-check">
                  <span class="font-label-md text-label-md text-primary block leading-tight font-semibold">Saya tidak tahu alur pejabat penandatangan</span>
                  <span class="text-xs text-on-surface-variant font-normal block leading-relaxed mt-0.5">Admin TU akan menelaah berkas dan menetapkan penandatangan resmi (membutuhkan waktu verifikasi tambahan).</span>
                </label>
              </div>

              <div class="flex items-start gap-3 transition-opacity duration-200 order-1" id="sequential-toggle-container">
                <label class="relative inline-flex items-center cursor-pointer">
                  <input checked class="sr-only peer" id="sequential-check" name="is_sequential" type="checkbox" value="1">
                  <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#004B87]"></div>
                </label>

                <div>
                  <span class="font-label-md text-label-md text-primary block leading-tight font-semibold">Penandatanganan dokumen harus mengikuti urutan</span>
                  <span class="text-xs text-on-surface-variant font-normal block leading-relaxed">Jika aktif, berkas akan diteruskan bertahap dari penandatangan pertama ke berikutnya</span>
                </div>
              </div>

            </div>

          </div>

        </div>

        <div class="space-y-4 transition-opacity duration-200" id="signers-flow-container">

          <div class="flex flex-wrap items-center gap-2">
            <button class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-green-700 hover:bg-green-800 text-white font-label-md text-label-md font-semibold shadow-sm transition" type="button">
              <span class="material-symbols-outlined text-[18px] mr-1">add</span>
              <span>Tambah Penandatangan</span>
            </button>

            <button class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-outline-variant bg-white hover:bg-surface-container-low text-primary-container font-label-md text-label-md font-semibold transition" type="button">
              <span>Tambahkan Saya Sendiri</span>
            </button>
          </div>

          <div class="space-y-2">

            <div class="flex items-center justify-between p-3 rounded-lg border border-outline-variant bg-surface-container-lowest hover:border-blue-500 transition gap-4">
              <div class="flex items-center gap-4 flex-1 min-w-0">
                <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0">1</div>

                <div class="flex-1 min-w-0">
                  <p class="font-label-lg text-label-lg font-bold text-primary truncate">Anita Hidayati, S.Kom., M.Kom.</p>
                  <p class="font-body-sm text-body-sm text-on-surface-variant truncate mt-0.5">anita.h@pnj.ac.id • NIP 197908102008122002</p>
                </div>
              </div>

              <button class="p-1.5 rounded-lg text-error hover:bg-error-container/40 transition shrink-0" title="Hapus Baris" type="button">
                <span class="material-symbols-outlined text-[20px]">delete</span>
              </button>
            </div>

            <div class="flex items-center justify-between p-3 rounded-lg border border-outline-variant bg-surface-container-lowest hover:border-blue-500 transition gap-4">
              <div class="flex items-center gap-4 flex-1 min-w-0">
                <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0">2</div>

                <div class="flex-1 min-w-0">
                  <p class="font-label-lg text-label-lg font-bold text-primary truncate">Dr. Bambang W., M.T.</p>
                  <p class="font-body-sm text-body-sm text-on-surface-variant truncate mt-0.5">bambang.w@pnj.ac.id • NIP 197405121999031001</p>
                </div>
              </div>

              <button class="p-1.5 rounded-lg text-error hover:bg-error-container/40 transition shrink-0" title="Hapus Baris" type="button">
                <span class="material-symbols-outlined text-[20px]">delete</span>
              </button>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between p-3 rounded-lg border border-dashed border-outline bg-surface gap-4">
              <div class="flex items-center gap-4 flex-1 w-full">
                <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0">3</div>

                <div class="flex-1 flex gap-2">
                  <div class="relative flex-1">
                    <input class="w-full pl-9 pr-3 py-2 rounded-lg border border-outline-variant bg-white font-body-sm text-body-sm text-primary focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none" placeholder="Cari NIP/NIM atau email resmi PNJ (@pnj.ac.id)" type="text">
                    <span class="material-symbols-outlined absolute left-2.5 top-2.5 text-[18px] text-on-surface-variant">person_search</span>
                  </div>

                  <button class="px-3.5 py-2 bg-[#004B87] hover:bg-[#003d6d] text-white rounded-lg font-label-md text-label-md flex items-center gap-1 transition shadow-sm shrink-0" type="button">
                    <span class="material-symbols-outlined text-[16px]">search</span>
                    <span>Cari</span>
                  </button>
                </div>
              </div>

              <button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition shrink-0 self-end md:self-center" title="Batalkan" type="button">
                <span class="material-symbols-outlined text-[20px]">close</span>
              </button>
            </div>

          </div>

        </div>

      </section>

      <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm hidden" id="section-template-approval">

        <div class="flex items-center justify-between pb-4 mb-5 border-b border-surface-container">
          <div class="flex items-center gap-2.5">
            <span class="material-symbols-outlined text-secondary text-[22px]">approval_delegation</span>
            <div>
              <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Tujuan Pengesahan Dokumen</h2>
              <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Pejabat berwenang dan validasi penomoran surat resmi</p>
            </div>
          </div>
        </div>

        <div class="p-4 rounded-xl border border-outline-variant bg-surface-container-low flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div>
              <div class="flex flex-wrap items-center gap-2">
                <p class="font-label-lg text-label-lg font-bold text-primary">Dr. Bambang W., M.T.</p>
              </div>
              <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">NIP 197405121999031001 • bambang.w@pnj.ac.id</p>
            </div>
          </div>
        </div>

        <div class="mt-4 flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm">
          <span class="material-symbols-outlined text-[18px] text-blue-600 shrink-0">info</span>
          <p>Nomor agenda surat resmi diterbitkan otomatis oleh sistem Tata Usaha.</p>
        </div>

      </section>

      <div class="flex justify-between items-center pt-4 border-t border-outline-variant">
        <a class="border border-outline-variant text-on-surface-variant hover:bg-surface-container hover:text-on-surface px-6 py-2.5 rounded-lg font-label-lg text-label-lg font-medium transition inline-block" href="/Tandain/dashboard">
          Batal
        </a>

        <button class="bg-[#004B87] hover:bg-[#003d6d] text-white px-8 py-2.5 rounded-lg font-label-lg text-label-lg font-semibold shadow-sm flex items-center gap-2 transition active:scale-[0.99]" type="submit">
          <span id="submit-btn-text">Lanjut ke Atur Posisi TTE</span>
          <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
        </button>
      </div>

    </form>

  </main>

  <script>
    const fileUpload = document.getElementById('file-upload');
    const dropZoneText = document.getElementById('drop-zone-text');
    const dropZoneSubtext = document.getElementById('drop-zone-subtext');

    if (fileUpload && dropZoneText) {
      fileUpload.addEventListener('change', function () {
        if (this.files && this.files[0]) {
          const file = this.files[0];
          dropZoneText.textContent = file.name;
          dropZoneSubtext.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB • Berkas PDF Siap Diunggah';
        }
      });
    }

    const triageCheck = document.getElementById('tu-triage-check');
    const signerSection = document.getElementById('signers-flow-container');
    const sequentialCheck = document.getElementById('sequential-check');
    const sequentialContainer = document.getElementById('sequential-toggle-container');

    function updateTriageState() {
      if (triageCheck) {
        if (triageCheck.checked) {
          if (signerSection) {
            signerSection.classList.add('opacity-40', 'pointer-events-none');
          }

          if (sequentialContainer) {
            sequentialContainer.classList.add('opacity-40', 'pointer-events-none');
          }

          if (sequentialCheck) {
            sequentialCheck.disabled = true;
          }
        } else {
          if (signerSection) {
            signerSection.classList.remove('opacity-40', 'pointer-events-none');
          }

          if (sequentialContainer) {
            sequentialContainer.classList.remove('opacity-40', 'pointer-events-none');
          }

          if (sequentialCheck) {
            sequentialCheck.disabled = false;
          }
        }
      }
    }

    if (triageCheck) {
      triageCheck.addEventListener('change', updateTriageState);
      updateTriageState();
    }

    const tabUploadBtn = document.getElementById('tab-upload-btn');
    const tabTemplateBtn = document.getElementById('tab-template-btn');
    const tabUploadContent = document.getElementById('tab-upload-content');
    const tabTemplateContent = document.getElementById('tab-template-content');
    const sectionSigners = document.getElementById('section-signers');
    const sectionTemplateApproval = document.getElementById('section-template-approval');
    const submitBtnText = document.getElementById('submit-btn-text');
    const pageSubtitle = document.getElementById('page-subtitle');
    const submissionMode = document.getElementById('submission-mode');

    const activeBtnClass = ['bg-[#004B87]', 'hover:bg-[#003d6d]', 'text-white', 'font-semibold', 'shadow-sm'];
    const inactiveBtnClass = ['text-on-surface-variant', 'hover:text-on-surface', 'hover:bg-surface-container-lowest', 'font-medium'];

    const docExpires = document.getElementById('doc-expires-at');
    const tplExpires = document.getElementById('template-expires-at');
    const today = new Date().toISOString().split('T')[0];

    if (docExpires) {
      docExpires.min = today;
    }

    if (tplExpires) {
      tplExpires.min = today;
    }

    function switchTab(mode) {
      if (mode === 'template') {
        tabUploadBtn.classList.remove(...activeBtnClass);
        tabUploadBtn.classList.add(...inactiveBtnClass);

        tabTemplateBtn.classList.remove(...inactiveBtnClass);
        tabTemplateBtn.classList.add(...activeBtnClass);

        tabUploadContent.classList.add('hidden');
        tabTemplateContent.classList.remove('hidden');

        sectionSigners.classList.add('hidden');
        sectionTemplateApproval.classList.remove('hidden');

        if (docExpires && tplExpires) {
          docExpires.disabled = true;
          tplExpires.disabled = false;
        }

        submitBtnText.textContent = 'Pratinjau & Ajukan Dokumen';
        pageSubtitle.textContent = 'Lengkapi data isian formulir surat dinas resmi jurusan';
        submissionMode.value = 'template';
      } else {
        tabTemplateBtn.classList.remove(...activeBtnClass);
        tabTemplateBtn.classList.add(...inactiveBtnClass);

        tabUploadBtn.classList.remove(...inactiveBtnClass);
        tabUploadBtn.classList.add(...activeBtnClass);

        tabTemplateContent.classList.add('hidden');
        tabUploadContent.classList.remove('hidden');

        sectionTemplateApproval.classList.add('hidden');
        sectionSigners.classList.remove('hidden');

        if (docExpires && tplExpires) {
          docExpires.disabled = false;
          tplExpires.disabled = true;
        }

        submitBtnText.textContent = 'Lanjut ke Atur Posisi TTE';
        pageSubtitle.textContent = 'Unggah berkas PDF dan tentukan alur pihak penandatangan';
        submissionMode.value = 'upload';
      }
    }

    if (tabUploadBtn && tabTemplateBtn) {
      tabUploadBtn.addEventListener('click', function () {
        switchTab('upload');
      });

      tabTemplateBtn.addEventListener('click', function () {
        switchTab('template');
      });

      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('mode') === 'template' || window.location.hash === '#template') {
        switchTab('template');
      }
    }
  </script>

<?php require_once 'views/layouts/footer.php'; ?>
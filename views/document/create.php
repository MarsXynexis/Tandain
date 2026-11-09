<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 min-w-0 w-full max-w-none p-5 lg:p-6">

    <div class="mb-5">
      <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight font-bold">Ajukan Dokumen Baru</h1>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1" id="page-subtitle">Unggah berkas PDF dan tentukan alur pihak penandatangan</p>
    </div>

    <form class="space-y-4" id="document-form" method="POST" action="/Tandain/document/create" enctype="multipart/form-data" novalidate>

      <?php if (!empty($error)): ?>
        <div class="p-3 rounded-lg bg-red-50 border border-red-300 text-red-700 text-sm">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <div class="hidden p-3 rounded-lg bg-red-50 border border-red-300 text-red-700 text-sm" id="client-error" role="alert"></div>

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

        <!-- ================= TAB UNGGAH PDF ================= -->
        <div class="space-y-4" id="tab-upload-content">

          <div class="border-2 border-dashed border-outline-variant hover:border-secondary rounded-lg p-6 text-center bg-surface hover:bg-surface-container-low transition-colors cursor-pointer group" id="drop-zone">
            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-surface-container flex items-center justify-center text-primary-container group-hover:bg-secondary-fixed transition-colors">
              <span class="material-symbols-outlined text-[32px] text-error" style="font-variation-settings: 'FILL' 1;">picture_as_pdf</span>
            </div>

            <p class="font-label-lg text-label-lg text-primary font-semibold" id="drop-zone-text">Tarik dan lepas berkas PDF ke sini, atau klik Telusuri File</p>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 mb-4" id="drop-zone-subtext">Maksimal ukuran berkas 2 MB (Format PDF)</p>

            <input class="hidden" id="file-upload" name="document_file" type="file" accept="application/pdf">

            <button class="inline-flex items-center gap-2 px-4 py-2 border border-outline rounded-lg text-primary-container font-label-md text-label-md bg-white hover:bg-surface-container transition-all" id="browse-btn" type="button">
              <span class="material-symbols-outlined text-[18px]">folder_open</span>
              <span>Telusuri File</span>
            </button>
          </div>

          <div>
            <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="doc-title">
              Judul Dokumen <span class="text-error">*</span>
            </label>
            <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition" id="doc-title" name="title" maxlength="255" placeholder="Contoh: Surat Rekomendasi Magang MBKM" type="text">
          </div>

          <div>
            <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="doc-expires-at">
              Batas Kedaluwarsa Dokumen <span class="font-normal text-on-surface-variant">(Opsional)</span>
            </label>
            <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition cursor-pointer" id="doc-expires-at" name="expires_at" type="date">
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1.5">Batas waktu legalitas administratif berkas. Kosongkan jika dokumen berlaku selamanya.</p>
          </div>

        </div>

        <!-- ================= TAB TEMPLATE ================= -->
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

      <!-- ================= ALUR PENANDATANGAN ================= -->
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
            <button class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-green-700 hover:bg-green-800 text-white font-label-md text-label-md font-semibold shadow-sm transition" id="add-signer-btn" type="button">
              <span class="material-symbols-outlined text-[18px] mr-1">add</span>
              <span>Tambah Penandatangan</span>
            </button>

            <button class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-outline-variant bg-white hover:bg-surface-container-low text-primary-container font-label-md text-label-md font-semibold transition" id="add-self-btn" type="button">
              <span>Tambahkan Saya Sendiri</span>
            </button>
          </div>

          <div class="space-y-2">

            <!-- Daftar penandatangan (diisi lewat JavaScript) -->
            <div class="space-y-2" id="signer-list"></div>

            <p class="text-sm text-on-surface-variant py-2" id="signer-empty">Belum ada penandatangan. Klik "Tambah Penandatangan" untuk mencari.</p>

            <!-- Baris pencarian -->
            <div class="hidden relative" id="signer-search-row">
              <div class="flex flex-col md:flex-row items-center justify-between p-3 rounded-lg border border-dashed border-outline bg-surface gap-4">
                <div class="flex items-center gap-4 flex-1 w-full">
                  <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0" id="search-row-number">1</div>

                  <div class="flex-1 flex gap-2">
                    <div class="relative flex-1">
                      <input class="w-full pl-9 pr-3 py-2 rounded-lg border border-outline-variant bg-white font-body-sm text-body-sm text-primary focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none" id="signer-search-input" autocomplete="off" placeholder="Cari nama, NIP/NIM, atau email resmi PNJ (@pnj.ac.id)" type="text">
                      <span class="material-symbols-outlined absolute left-2.5 top-2.5 text-[18px] text-on-surface-variant">person_search</span>
                    </div>

                    <button class="px-3.5 py-2 bg-[#004B87] hover:bg-[#003d6d] text-white rounded-lg font-label-md text-label-md flex items-center gap-1 transition shadow-sm shrink-0" id="signer-search-btn" type="button">
                      <span class="material-symbols-outlined text-[16px]">search</span>
                      <span>Cari</span>
                    </button>
                  </div>
                </div>

                <button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition shrink-0 self-end md:self-center" id="signer-search-close" title="Batalkan" type="button">
                  <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
              </div>

              <!-- Hasil pencarian -->
              <ul class="hidden mt-1 rounded-lg border border-outline-variant bg-white shadow-md divide-y divide-surface-container max-h-64 overflow-auto" id="signer-results"></ul>
            </div>

          </div>

        </div>

      </section>

      <!-- ================= TUJUAN PENGESAHAN (MODE TEMPLATE) ================= -->
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
    // ------------------------------------------------------------------
    // Data dari PHP
    // $candidates : daftar calon penandatangan (dikirim dari controller)
    // $user       : user yang sedang login (dari session)
    // ------------------------------------------------------------------
    const CANDIDATES = <?= json_encode($candidates ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const ME = <?= json_encode([
      'id'       => $user['id'] ?? '',
      'name'     => $user['full_name'] ?? ($user['name'] ?? ''),
      'identity' => $user['identity_number'] ?? '',
      'email'    => $user['email'] ?? '',
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    const MAX_SIZE = 2 * 1024 * 1024;

    const form = document.getElementById('document-form');
    const clientError = document.getElementById('client-error');

    function showError(msg) {
      clientError.textContent = msg;
      clientError.classList.remove('hidden');
      clientError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function clearError() {
      clientError.textContent = '';
      clientError.classList.add('hidden');
    }

    // ------------------------------------------------------------------
    // Upload file + drag & drop
    // ------------------------------------------------------------------
    const fileUpload = document.getElementById('file-upload');
    const dropZone = document.getElementById('drop-zone');
    const dropZoneText = document.getElementById('drop-zone-text');
    const dropZoneSubtext = document.getElementById('drop-zone-subtext');
    const browseBtn = document.getElementById('browse-btn');

    const defaultDropText = dropZoneText.textContent;
    const defaultDropSubtext = dropZoneSubtext.textContent;

    function resetFile() {
      fileUpload.value = '';
      dropZoneText.textContent = defaultDropText;
      dropZoneSubtext.textContent = defaultDropSubtext;
    }

    function handleFile() {
      clearError();
      const file = fileUpload.files && fileUpload.files[0];
      if (!file) return;

      const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
      if (!isPdf) {
        resetFile();
        showError('Berkas harus berformat PDF.');
        return;
      }
      if (file.size > MAX_SIZE) {
        resetFile();
        showError('Ukuran berkas maksimal 2 MB.');
        return;
      }

      dropZoneText.textContent = file.name;
      dropZoneSubtext.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB • Berkas PDF Siap Diunggah';

      // Isi judul otomatis dari nama file kalau masih kosong
      const titleInput = document.getElementById('doc-title');
      if (titleInput && titleInput.value.trim() === '') {
        titleInput.value = file.name.replace(/\.pdf$/i, '').replace(/[_-]+/g, ' ');
      }
    }

    fileUpload.addEventListener('change', handleFile);
    dropZone.addEventListener('click', () => fileUpload.click());
    browseBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      fileUpload.click();
    });

    ['dragenter', 'dragover'].forEach((evt) => {
      dropZone.addEventListener(evt, (e) => {
        e.preventDefault();
        dropZone.classList.add('border-secondary', 'bg-surface-container-low');
      });
    });
    ['dragleave', 'drop'].forEach((evt) => {
      dropZone.addEventListener(evt, (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-secondary', 'bg-surface-container-low');
      });
    });
    dropZone.addEventListener('drop', (e) => {
      if (e.dataTransfer && e.dataTransfer.files.length > 0) {
        fileUpload.files = e.dataTransfer.files;
        handleFile();
      }
    });

    // ------------------------------------------------------------------
    // Alur penandatangan
    // ------------------------------------------------------------------
    const signerList = document.getElementById('signer-list');
    const signerEmpty = document.getElementById('signer-empty');
    const searchRow = document.getElementById('signer-search-row');
    const searchInput = document.getElementById('signer-search-input');
    const searchBtn = document.getElementById('signer-search-btn');
    const searchClose = document.getElementById('signer-search-close');
    const searchResults = document.getElementById('signer-results');
    const searchRowNumber = document.getElementById('search-row-number');
    const addSignerBtn = document.getElementById('add-signer-btn');
    const addSelfBtn = document.getElementById('add-self-btn');

    function signerIds() {
      return Array.from(signerList.querySelectorAll('input[name="signers[]"]')).map((i) => i.value);
    }

    function renumberSigners() {
      const rows = signerList.querySelectorAll('[data-signer-row]');
      rows.forEach((row, i) => {
        row.querySelector('[data-number]').textContent = i + 1;
      });
      searchRowNumber.textContent = rows.length + 1;
      signerEmpty.classList.toggle('hidden', rows.length > 0 || !searchRow.classList.contains('hidden'));
    }

    function addSigner(person) {
      if (!person.id) {
        showError('Data penandatangan tidak valid.');
        return;
      }
      if (signerIds().includes(person.id)) {
        showError('Penandatangan tersebut sudah ada dalam daftar.');
        return;
      }
      clearError();

      const row = document.createElement('div');
      row.dataset.signerRow = '1';
      row.className = 'flex items-center justify-between p-3 rounded-lg border border-outline-variant bg-surface-container-lowest hover:border-blue-500 transition gap-4';

      const left = document.createElement('div');
      left.className = 'flex items-center gap-4 flex-1 min-w-0';

      const badge = document.createElement('div');
      badge.dataset.number = '1';
      badge.className = 'w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0';

      const info = document.createElement('div');
      info.className = 'flex-1 min-w-0';

      const name = document.createElement('p');
      name.className = 'font-label-lg text-label-lg font-bold text-primary truncate';
      name.textContent = person.name;

      const detail = document.createElement('p');
      detail.className = 'font-body-sm text-body-sm text-on-surface-variant truncate mt-0.5';
      detail.textContent = [person.email, person.identity ? 'NIP/NIM ' + person.identity : ''].filter(Boolean).join(' • ');

      info.append(name, detail);
      left.append(badge, info);

      const hidden = document.createElement('input');
      hidden.type = 'hidden';
      hidden.name = 'signers[]';
      hidden.value = person.id;

      const del = document.createElement('button');
      del.type = 'button';
      del.title = 'Hapus Baris';
      del.className = 'p-1.5 rounded-lg text-error hover:bg-error-container/40 transition shrink-0';
      del.innerHTML = '<span class="material-symbols-outlined text-[20px]">delete</span>';
      del.addEventListener('click', () => {
        row.remove();
        renumberSigners();
      });

      row.append(left, hidden, del);
      signerList.appendChild(row);
      renumberSigners();
    }

    function openSearch() {
      searchRow.classList.remove('hidden');
      signerEmpty.classList.add('hidden');
      renumberSigners();
      searchInput.value = '';
      searchResults.classList.add('hidden');
      searchInput.focus();
    }

    function closeSearch() {
      searchRow.classList.add('hidden');
      searchResults.classList.add('hidden');
      searchResults.innerHTML = '';
      renumberSigners();
    }

    function runSearch() {
      const q = searchInput.value.trim().toLowerCase();
      searchResults.innerHTML = '';

      if (q.length < 2) {
        showResultMessage('Ketik minimal 2 karakter.');
        return;
      }

      const taken = signerIds();
      const found = CANDIDATES.filter((c) => {
        if (taken.includes(c.id)) return false;
        const hay = [c.full_name, c.identity_number, c.email].join(' ').toLowerCase();
        return hay.includes(q);
      }).slice(0, 8);

      if (found.length === 0) {
        showResultMessage('Tidak ditemukan. Pastikan NIP/NIM atau email benar.');
        return;
      }

      found.forEach((c) => {
        const li = document.createElement('li');
        li.className = 'px-3 py-2 cursor-pointer hover:bg-surface-container-low';

        const n = document.createElement('p');
        n.className = 'text-sm font-semibold text-primary';
        n.textContent = c.full_name;

        const d = document.createElement('p');
        d.className = 'text-xs text-on-surface-variant';
        d.textContent = [c.email, c.identity_number, c.position].filter(Boolean).join(' • ');

        li.append(n, d);
        li.addEventListener('click', () => {
          addSigner({ id: c.id, name: c.full_name, identity: c.identity_number, email: c.email });
          closeSearch();
        });
        searchResults.appendChild(li);
      });
      searchResults.classList.remove('hidden');
    }

    function showResultMessage(text) {
      const li = document.createElement('li');
      li.className = 'px-3 py-2 text-sm text-on-surface-variant';
      li.textContent = text;
      searchResults.appendChild(li);
      searchResults.classList.remove('hidden');
    }

    addSignerBtn.addEventListener('click', openSearch);
    addSelfBtn.addEventListener('click', () => addSigner(ME));
    searchBtn.addEventListener('click', runSearch);
    searchClose.addEventListener('click', closeSearch);
    searchInput.addEventListener('input', runSearch);
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault(); // jangan submit form
        runSearch();
      }
    });

    // ------------------------------------------------------------------
    // Mode triage (Admin TU yang menentukan penandatangan)
    // ------------------------------------------------------------------
    const triageCheck = document.getElementById('tu-triage-check');
    const signerSection = document.getElementById('signers-flow-container');
    const sequentialCheck = document.getElementById('sequential-check');
    const sequentialContainer = document.getElementById('sequential-toggle-container');

    function updateTriageState() {
      const on = triageCheck.checked;
      signerSection.classList.toggle('opacity-40', on);
      signerSection.classList.toggle('pointer-events-none', on);
      sequentialContainer.classList.toggle('opacity-40', on);
      sequentialContainer.classList.toggle('pointer-events-none', on);
      sequentialCheck.disabled = on;
      if (on) closeSearch();
    }

    triageCheck.addEventListener('change', updateTriageState);
    updateTriageState();

    // ------------------------------------------------------------------
    // Tab Unggah / Template
    // ------------------------------------------------------------------
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

    // Tanggal lokal (bukan UTC) supaya tidak bergeser sehari di zona WIB
    const now = new Date();
    const today = [
      now.getFullYear(),
      String(now.getMonth() + 1).padStart(2, '0'),
      String(now.getDate()).padStart(2, '0'),
    ].join('-');
    docExpires.min = today;
    tplExpires.min = today;

    function switchTab(mode) {
      clearError();
      const isTemplate = mode === 'template';

      tabUploadBtn.classList.remove(...(isTemplate ? activeBtnClass : inactiveBtnClass));
      tabUploadBtn.classList.add(...(isTemplate ? inactiveBtnClass : activeBtnClass));
      tabTemplateBtn.classList.remove(...(isTemplate ? inactiveBtnClass : activeBtnClass));
      tabTemplateBtn.classList.add(...(isTemplate ? activeBtnClass : inactiveBtnClass));

      tabUploadContent.classList.toggle('hidden', isTemplate);
      tabTemplateContent.classList.toggle('hidden', !isTemplate);
      sectionSigners.classList.toggle('hidden', isTemplate);
      sectionTemplateApproval.classList.toggle('hidden', !isTemplate);

      docExpires.disabled = isTemplate;
      tplExpires.disabled = !isTemplate;

      submitBtnText.textContent = isTemplate ? 'Pratinjau & Ajukan Dokumen' : 'Lanjut ke Atur Posisi TTE';
      pageSubtitle.textContent = isTemplate
        ? 'Lengkapi data isian formulir surat dinas resmi jurusan'
        : 'Unggah berkas PDF dan tentukan alur pihak penandatangan';
      submissionMode.value = isTemplate ? 'template' : 'upload';
    }

    tabUploadBtn.addEventListener('click', () => switchTab('upload'));
    tabTemplateBtn.addEventListener('click', () => switchTab('template'));

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('mode') === 'template' || window.location.hash === '#template') {
      switchTab('template');
    }

    // ------------------------------------------------------------------
    // Validasi sebelum submit
    // ------------------------------------------------------------------
    form.addEventListener('submit', (e) => {
      clearError();

      if (submissionMode.value === 'template') {
        if (!document.getElementById('template-agency').value.trim()) {
          e.preventDefault();
          return showError('Instansi / perusahaan tujuan wajib diisi.');
        }
        if (!document.getElementById('template-period').value.trim()) {
          e.preventDefault();
          return showError('Periode pelaksanaan wajib diisi.');
        }
        return;
      }

      const file = fileUpload.files && fileUpload.files[0];
      if (!file) {
        e.preventDefault();
        return showError('Berkas PDF wajib diunggah.');
      }
      if (file.size > MAX_SIZE) {
        e.preventDefault();
        return showError('Ukuran berkas maksimal 2 MB.');
      }
      if (!document.getElementById('doc-title').value.trim()) {
        e.preventDefault();
        return showError('Judul dokumen wajib diisi.');
      }
      if (!triageCheck.checked && signerIds().length === 0) {
        e.preventDefault();
        return showError('Pilih minimal satu penandatangan, atau centang "Saya tidak tahu alur pejabat penandatangan".');
      }
    });

    renumberSigners();
  </script>

<?php require_once 'views/layouts/footer.php'; ?>
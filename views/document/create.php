<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$templateList = [];

foreach ($templates ?? [] as $tpl) {
    $decodedSchema = is_string($tpl['placeholders_schema'] ?? null)
        ? json_decode($tpl['placeholders_schema'], true)
        : ($tpl['placeholders_schema'] ?? []);

    if (!is_array($decodedSchema)) {
        $decodedSchema = [];
    }

    $templateList[] = [
        'id' => $tpl['id'] ?? '',
        'template_code' => $tpl['template_code'] ?? '',
        'title' => $tpl['title'] ?? '',
        'description' => $tpl['description'] ?? '',
        'html_content' => $tpl['html_content'] ?? '',
        'placeholders_schema' => $decodedSchema
    ];
}

$requesterName = $user['full_name'] ?? ($user['name'] ?? '-');
$requesterIdentity = $user['identity_number'] ?? '-';
$requesterPosition = formatUserAffiliation(
    $user['position'] ?? null,
    $user['study_program'] ?? null,
    $user['role_display_name'] ?? '-'
);
$requesterEmail = $user['email'] ?? '-';
?>

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <div>
      <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight font-bold">Ajukan Dokumen Baru</h1>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1" id="page-subtitle">Unggah berkas PDF dan tentukan alur pihak penandatangan</p>
    </div>

    <form class="" id="document-form" method="POST" action="/Tandain/document/create" enctype="multipart/form-data" novalidate>

      <?php require 'views/layouts/flash.php'; ?>

      <?php $notice = ['type' => 'error', 'id' => 'client-error']; ?>
      <?php require 'views/layouts/notice.php'; ?>

      <input id="submission-mode" name="submission_mode" type="hidden" value="upload">

      <section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-4 lg:p-5 shadow-sm">

        <div class="flex items-center gap-2 mb-3 pb-2 border-b border-surface-container">
          <span class="material-symbols-outlined text-secondary text-[22px]">description</span>
          <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Berkas &amp; Informasi Dokumen</h2>
        </div>

        <div class="flex items-center gap-2 mb-6 p-1 bg-surface-container rounded-lg max-w-md">
          <button class="btn-md flex-1 flex items-center justify-center gap-2 bg-white text-primary font-semibold shadow-sm transition" id="tab-upload-btn" type="button">
            <span class="material-symbols-outlined text-[18px]">upload_file</span>
            <span>Unggah Berkas PDF</span>
          </button>

          <button class="btn-md flex-1 flex items-center justify-center gap-2 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-lowest font-medium transition" id="tab-template-btn" type="button">
            <span class="material-symbols-outlined text-[18px]">description</span>
            <span>Gunakan Template Surat</span>
          </button>
        </div>

        <div class="space-y-4" id="tab-upload-content">

          <div class="border-2 border-dashed border-outline-variant hover:border-secondary rounded-lg p-6 text-center bg-surface hover:bg-surface-container-low transition-colors cursor-pointer group" id="drop-zone">
            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-surface-container flex items-center justify-center text-primary-container group-hover:bg-secondary-fixed transition-colors">
              <span class="material-symbols-outlined text-[32px] text-error" style="font-variation-settings: 'FILL' 1;">picture_as_pdf</span>
            </div>

            <p class="font-label-lg text-label-lg text-primary font-semibold" id="drop-zone-text">Tarik dan lepas berkas PDF ke sini, atau klik Telusuri File</p>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 mb-4" id="drop-zone-subtext">Maksimal ukuran berkas 2 MB (Format PDF)</p>

            <input class="hidden" id="file-upload" name="document_file" type="file" accept="application/pdf">

            <button class="btn-md inline-flex items-center gap-2 border border-outline text-primary-container bg-white hover:bg-surface-container transition-all" id="browse-btn" type="button">
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

        <div class="hidden" id="tab-template-content">

          <?php if (count($templateList) === 0): ?>
            <div class="p-6 rounded-lg border border-outline-variant bg-surface-container-low text-center">
              <span class="material-symbols-outlined text-[32px] text-on-surface-variant mb-2">article</span>
              <p class="font-label-lg text-label-lg text-primary font-semibold">Belum Ada Template Surat Aktif</p>
              <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Hubungi Administrator Tata Usaha atau gunakan tab Unggah Berkas PDF.</p>
            </div>
          <?php else: ?>
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

              <div class="xl:col-span-5 space-y-5">

                <div>
                  <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="template-select">
                    Pilih Jenis Template Surat Resmi <span class="text-error">*</span>
                  </label>

                  <div class="relative">
                    <select class="w-full pl-3.5 pr-10 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition appearance-none cursor-pointer" id="template-select" name="template_id" disabled>
                      <?php foreach ($templateList as $index => $tpl): ?>
                        <option value="<?= htmlspecialchars($tpl['id']) ?>" <?= $index === 0 ? 'selected' : '' ?>>
                          <?= htmlspecialchars($tpl['title']) ?> (<?= htmlspecialchars($tpl['template_code']) ?>)
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-2.5 text-[20px] text-on-surface-variant pointer-events-none">expand_more</span>
                  </div>

                  <div class="mt-1.5 flex items-start gap-1.5" id="template-description-box">
                    <span class="material-symbols-outlined text-[16px] text-on-surface-variant shrink-0 mt-0.5">info</span>
                    <p class="font-body-sm text-xs text-on-surface-variant leading-relaxed" id="template-description-text"></p>
                    <span class="hidden" id="template-code-label"></span>
                  </div>
                </div>

                <div class="p-3 rounded-lg bg-surface-container-low border border-outline-variant">
                  <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-label-md text-sm font-semibold text-primary"><?= htmlspecialchars($requesterName) ?></span>
                    <?php if ($requesterIdentity !== ''): ?>
                      <span class="font-code-tabular text-[11px] text-on-surface-variant bg-surface-container-lowest px-1.5 py-0.5 rounded border border-outline-variant font-medium"><?= htmlspecialchars($requesterIdentity) ?></span>
                    <?php endif; ?>
                  </div>
                  <p class="font-body-sm text-xs text-on-surface-variant mt-0.5">
                    <?= htmlspecialchars($requesterPosition !== '' ? $requesterPosition : '-') ?> &bull; <span class="text-outline"><?= htmlspecialchars($requesterEmail) ?></span>
                  </p>
                </div>

                <div class="pt-2 border-t border-outline-variant/60">
                  <div class="flex items-center gap-1.5 mb-1">
                    <span class="material-symbols-outlined text-[#004B87] text-[18px]">edit_note</span>
                    <h3 class="font-label-md text-xs font-bold text-primary uppercase tracking-wider">Isian Naskah Surat</h3>
                  </div>
                  <p class="font-body-sm text-xs text-on-surface-variant">Lengkapi data berikut untuk dicetak pada lembar naskah dinas resmi.</p>
                </div>

                <div class="space-y-4" id="dynamic-template-fields"></div>

                <div class="pt-3 border-t border-outline-variant/60 space-y-4">
                  <div>
                    <label class="block font-label-md text-label-md text-primary font-semibold mb-1.5" for="template-doc-title">
                      Judul Dokumen <span class="font-normal text-xs text-on-surface-variant">(Opsional)</span>
                    </label>
                    <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition" id="template-doc-title" name="title" maxlength="255" placeholder="Otomatis sesuai template dan nama pemohon" type="text" disabled>
                    <p class="font-body-sm text-xs text-on-surface-variant mt-1">Kosongkan untuk memakai judul otomatis dari nama template dan pemohon.</p>
                  </div>

                  <div>
                    <label class="block font-label-md text-label-md text-primary font-semibold mb-1.5" for="template-expires-at">
                      Batas Kedaluwarsa Dokumen <span class="font-normal text-xs text-on-surface-variant">(Opsional)</span>
                    </label>
                    <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition cursor-pointer" id="template-expires-at" name="expires_at" type="date" disabled>
                    <p class="font-body-sm text-xs text-on-surface-variant mt-1">Batas waktu legalitas administratif surat resmi. Kosongkan jika dokumen berlaku selamanya.</p>
                  </div>
                </div>

              </div>

              <div class="xl:col-span-7 xl:sticky xl:top-20">

                <div class="border border-outline-variant rounded-xl bg-surface-container p-4">

                  <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                    <div>
                      <h3 class="font-label-lg text-label-lg font-semibold text-primary">Live Pratinjau Surat Resmi</h3>
                      <p class="font-body-sm text-body-sm text-on-surface-variant">Kop dan kaki surat Politeknik Negeri Jakarta diterapkan otomatis</p>
                    </div>

                    <span class="px-2.5 py-1 rounded-full bg-surface-container-lowest border border-outline-variant font-code-tabular text-[11px] font-semibold text-on-surface-variant">A4 • 210 x 297 mm</span>
                  </div>

                  <div class="w-full flex justify-center items-start overflow-hidden rounded-lg bg-surface-container-high/60 p-2" id="template-preview-viewport">
                    <div class="relative shrink-0 overflow-hidden shadow-md border border-outline-variant bg-white" id="template-preview-stage">
                      <div class="origin-top-left bg-white" id="template-preview-scale-box" style="position: absolute; top: 0; left: 0; width: 210mm;">
                        <?php
                        $letterSheetId = 'template-preview-sheet';
                        $letterBodyId = 'template-preview-body';
                        $letterBodyHtml = '';
                        require 'views/templates/official_letter_frame.php';
                        ?>
                      </div>
                    </div>
                  </div>

                </div>

              </div>

            </div>
          <?php endif; ?>

        </div>

      </section>

      <section class=" mt-4 bg-surface-container-lowest border border-outline-variant rounded-lg p-4 lg:p-5 shadow-sm" id="section-signers">

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
            <button class="btn-md inline-flex items-center justify-center bg-brand hover:bg-brand-hover text-white font-semibold shadow-sm transition" id="add-signer-btn" type="button">
              <span class="material-symbols-outlined text-[18px] mr-1">add</span>
              <span>Tambah Penandatangan</span>
            </button>

            <?php if (($user['role_name'] ?? '') !== 'admin_tu'): ?>
              <button class="btn-md inline-flex items-center justify-center border border-outline-variant bg-white hover:bg-surface-container-low text-primary-container font-semibold transition" id="add-self-btn" type="button">
                <span>Tambahkan Saya Sendiri</span>
              </button>
            <?php endif; ?>
          </div>

          <div class="space-y-2">

            <p class="text-xs text-on-surface-variant" id="signer-order-hint" hidden>Tarik pegangan di kiri untuk mengubah urutan.</p>
            <p class="sr-only" role="status" id="signer-announcement"></p>

            <div class="space-y-2" id="signer-list"></div>

            <p class="text-sm text-on-surface-variant py-2" id="signer-empty">Belum ada penandatangan. Klik "Tambah Penandatangan" untuk mencari.</p>

            <?php require 'views/layouts/signer_picker.php'; ?>

          </div>

        </div>

      </section>

      <div class="flex flex-wrap items-center justify-end gap-3 pt-4 border-t border-outline-variant">
        <a class="btn-md inline-flex border border-outline-variant text-on-surface-variant hover:bg-surface-container hover:text-on-surface font-medium transition" href="/Tandain/dashboard">
          Batal
        </a>

        <button class="btn-lg bg-brand hover:bg-brand-hover text-white font-semibold shadow-sm flex items-center gap-2 transition active:scale-[0.99]" id="submit-doc-btn" type="submit">
          <span id="submit-btn-text">Lanjut ke Atur Posisi TTE</span>
          <span class="material-symbols-outlined text-[18px]" id="submit-btn-icon">arrow_forward</span>
        </button>
      </div>

    </form>

  </main>

  <?php require 'views/layouts/signer_badges.php'; ?>

  <?php require 'views/layouts/signer_order_control.php'; ?>
  <?php require 'views/layouts/signer_row.php'; ?>

  <script src="/Tandain/assets/js/html2canvas.min.js"></script>
  <script src="/Tandain/assets/js/jspdf.umd.min.js"></script>
  <script src="/Tandain/assets/js/pnj-letter-renderer.js"></script>
  <script src="/Tandain/assets/js/signer-picker.js"></script>
  <script src="/Tandain/assets/js/signer-order.js"></script>

  <script>
    const TEMPLATES = <?= json_encode($templateList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const NEXT_DOC_SEQ = <?= json_encode($next_doc_seq ?? '001', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const ME = <?= json_encode([
      'id' => $user['id'] ?? '',
      'name' => $requesterName,
      'identity' => $user['identity_number'] ?? '',
      'email' => $user['email'] ?? '',
      'position' => $user['position'] ?? '',
      'study_program' => $user['study_program'] ?? '',
      'role_display_name' => $user['role_display_name'] ?? '',
      'canSign' => $can_sign
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    const MAX_SIZE = 2 * 1024 * 1024;

    const form = document.getElementById('document-form');
    const clientError = document.getElementById('client-error');

    const fileUpload = document.getElementById('file-upload');
    const dropZone = document.getElementById('drop-zone');
    const dropZoneText = document.getElementById('drop-zone-text');
    const dropZoneSubtext = document.getElementById('drop-zone-subtext');
    const browseBtn = document.getElementById('browse-btn');
    const docTitle = document.getElementById('doc-title');
    const docExpires = document.getElementById('doc-expires-at');

    const defaultDropText = dropZoneText.textContent;
    const defaultDropSubtext = dropZoneSubtext.textContent;

    const signerList = document.getElementById('signer-list');
    const signerAnnouncement = document.getElementById('signer-announcement');
    const addSignerBtn = document.getElementById('add-signer-btn');
    const addSelfBtn = document.getElementById('add-self-btn');


    const triageCheck = document.getElementById('tu-triage-check');
    const signerSection = document.getElementById('signers-flow-container');
    const sequentialCheck = document.getElementById('sequential-check');
    const sequentialContainer = document.getElementById('sequential-toggle-container');

    const tabUploadBtn = document.getElementById('tab-upload-btn');
    const tabTemplateBtn = document.getElementById('tab-template-btn');
    const tabUploadContent = document.getElementById('tab-upload-content');
    const tabTemplateContent = document.getElementById('tab-template-content');
    const submitBtn = document.getElementById('submit-doc-btn');
    const submitBtnText = document.getElementById('submit-btn-text');
    const submitBtnIcon = document.getElementById('submit-btn-icon');
    const pageSubtitle = document.getElementById('page-subtitle');
    const submissionMode = document.getElementById('submission-mode');

    const templateSelect = document.getElementById('template-select');
    const templateCodeLabel = document.getElementById('template-code-label');
    const templateDescriptionText = document.getElementById('template-description-text');
    const dynamicFieldsContainer = document.getElementById('dynamic-template-fields');
    const templateDocTitle = document.getElementById('template-doc-title');
    const tplExpires = document.getElementById('template-expires-at');
    const previewViewport = document.getElementById('template-preview-viewport');
    const previewStage = document.getElementById('template-preview-stage');
    const previewScaleBox = document.getElementById('template-preview-scale-box');
    const previewSheet = document.getElementById('template-preview-sheet');

    const activeBtnClass = ['bg-white', 'text-primary', 'font-semibold', 'shadow-sm'];
    const inactiveBtnClass = ['text-on-surface-variant', 'hover:text-on-surface', 'hover:bg-surface-container-lowest', 'font-medium'];

    let userTouchedRouting = false;
    let isSubmitting = false;

    function showError(msg) {
      showNotice(clientError, msg);
      clientError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function clearError() {
      hideNotice(clientError);
    }

    function resetFile() {
      fileUpload.value = '';
      dropZoneText.textContent = defaultDropText;
      dropZoneSubtext.textContent = defaultDropSubtext;
    }

    function handleFile() {
      clearError();

      const file = fileUpload.files && fileUpload.files[0];

      if (file) {
        const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');

        if (!isPdf) {
          resetFile();
          showError('Berkas harus berformat PDF.');
        } else if (file.size > MAX_SIZE) {
          resetFile();
          showError('Ukuran berkas maksimal 2 MB.');
        } else {
          dropZoneText.textContent = file.name;
          dropZoneSubtext.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB • Berkas PDF Siap Diunggah';

          if (docTitle && docTitle.value.trim() === '') {
            docTitle.value = file.name.replace(/\.pdf$/i, '').replace(/[_-]+/g, ' ');
          }
        }
      }
    }

    function signerIds() {
      return Array.from(signerList.querySelectorAll('input[name="signers[]"]')).map((i) => i.value);
    }

    function renumberSigners() {
      updateSignerList(signerList, sequentialCheck.checked && !triageCheck.checked);
    }

    function addSigner(person) {
      if (!person.id) {
        showError('Data penandatangan tidak valid.');
      } else if (signerIds().includes(person.id)) {
        showError('Penandatangan tersebut sudah ada dalam daftar.');
      } else {
        clearError();
        userTouchedRouting = true;

        signerList.append(createSignerRow(person, 'signers[]'));
        renumberSigners();
      }
    }

    function updateTriageState() {
      const on = triageCheck.checked;

      signerSection.classList.toggle('opacity-40', on);
      signerSection.inert = on;
      sequentialContainer.classList.toggle('opacity-40', on);
      sequentialContainer.classList.toggle('pointer-events-none', on);
      sequentialCheck.disabled = on;

      if (on) {
        closeSearch();
      }

      renumberSigners();
    }

    function getSelectedTemplate() {
      let selected = null;

      if (templateSelect && TEMPLATES.length > 0) {
        const targetId = templateSelect.value;
        selected = TEMPLATES.find((tpl) => tpl.id === targetId) || TEMPLATES[0];
      }

      return selected;
    }

    function collectTemplateFieldValues() {
      const values = {};

      if (dynamicFieldsContainer) {
        const inputs = dynamicFieldsContainer.querySelectorAll('[data-template-field]');

        inputs.forEach((input) => {
          const key = input.dataset.templateField;

          if (key) {
            values[key] = input.value.trim();
          }
        });
      }

      return values;
    }

    function buildAutoDocumentNumber(selectedTemplate) {
      const rawCode = selectedTemplate && selectedTemplate.template_code
        ? String(selectedTemplate.template_code).trim().toUpperCase()
        : 'SURAT';
      const currentYear = new Date().getFullYear();

      return NEXT_DOC_SEQ + '/PL3/' + rawCode + '/PNJ/' + currentYear;
    }

    function buildTemplateValuesMap(selectedTemplate, forPreview) {
      const map = {
        nomor_surat: buildAutoDocumentNumber(selectedTemplate),
        nama_lengkap: ME.name,
        nomor_induk: ME.identity,
        program_studi: ME.study_program || '',
        study_program: ME.study_program || '',
        prodi: ME.study_program || '',
        jabatan: ME.position || ME.role_display_name || '',
        position: ME.position || '',
        email: ME.email
      };

      const fieldValues = collectTemplateFieldValues();
      const schema = selectedTemplate && Array.isArray(selectedTemplate.placeholders_schema)
        ? selectedTemplate.placeholders_schema
        : [];

      schema.forEach((field) => {
        const key = field.name || '';
        const rawValue = fieldValues[key] || '';

        if (key !== '') {
          if (field.type === 'date' && rawValue !== '' && window.PnjLetterRenderer) {
            map[key] = window.PnjLetterRenderer.formatIndonesianDate(rawValue);
          } else if (rawValue !== '' || !(key in map)) {
            map[key] = rawValue;
          }
        }
      });

      if (!forPreview && window.PnjLetterRenderer) {
        map.tanggal_surat = window.PnjLetterRenderer.formatIndonesianDate();
      }

      return map;
    }

    function updatePreviewScale() {
      if (previewViewport && previewStage && previewScaleBox && previewSheet && !tabTemplateContent.classList.contains('hidden')) {
        const viewportStyles = window.getComputedStyle(previewViewport);
        const padX = (parseFloat(viewportStyles.paddingLeft) || 0) + (parseFloat(viewportStyles.paddingRight) || 0);
        const padY = (parseFloat(viewportStyles.paddingTop) || 0) + (parseFloat(viewportStyles.paddingBottom) || 0);
        const availableWidth = Math.max(0, previewViewport.clientWidth - padX - 2);
        const naturalWidth = previewSheet.offsetWidth || 794;
        const naturalHeight = previewSheet.offsetHeight || 1123;
        const rawScale = availableWidth > 0 ? Math.min(1, availableWidth / naturalWidth) : 1;
        const scale = Math.floor(rawScale * 10000) / 10000;
        const scaledWidth = Math.floor(naturalWidth * scale);
        const scaledHeight = Math.ceil(naturalHeight * scale);

        previewScaleBox.style.transform = 'scale(' + scale + ')';
        previewScaleBox.style.transformOrigin = 'top left';
        previewStage.style.width = (scaledWidth + 2) + 'px';
        previewStage.style.height = (scaledHeight + 2) + 'px';
        previewViewport.style.height = (scaledHeight + 2 + padY) + 'px';
      }
    }

    function renderTemplatePreview() {
      const selected = getSelectedTemplate();

      if (selected && previewSheet && window.PnjLetterRenderer) {
        const valuesMap = buildTemplateValuesMap(selected, true);

        window.PnjLetterRenderer.renderIntoSheet(previewSheet, selected.html_content, valuesMap, { isPreview: true });
        updatePreviewScale();
        window.requestAnimationFrame(updatePreviewScale);
      }
    }

    function createDynamicFieldInput(field) {
      const fieldId = 'tpl-field-' + field.name;
      const baseInputClass = 'w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition';
      let element = null;

      if (field.type === 'textarea') {
        element = document.createElement('textarea');
        element.rows = 3;
        element.className = baseInputClass + ' leading-relaxed';
      } else if (field.type === 'select') {
        const selectWrapper = document.createElement('div');
        selectWrapper.className = 'relative';

        const selectEl = document.createElement('select');
        selectEl.className = baseInputClass + ' pr-10 appearance-none cursor-pointer';
        selectEl.id = fieldId;
        selectEl.dataset.templateField = field.name;
        selectEl.dataset.fieldLabel = field.label || field.name;

        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = 'Pilih ' + (field.label || field.name);
        selectEl.appendChild(defaultOpt);

        const optionsList = Array.isArray(field.options) ? field.options : [];
        optionsList.forEach((optText) => {
          const opt = document.createElement('option');
          opt.value = optText;
          opt.textContent = optText;
          selectEl.appendChild(opt);
        });

        const icon = document.createElement('span');
        icon.className = 'material-symbols-outlined absolute right-3 top-2.5 text-[20px] text-on-surface-variant pointer-events-none';
        icon.textContent = 'expand_more';

        if (field.required) {
          selectEl.required = true;
          selectEl.setAttribute('aria-required', 'true');
        }

        selectEl.addEventListener('change', renderTemplatePreview);

        selectWrapper.append(selectEl, icon);
        element = selectWrapper;
      } else {
        element = document.createElement('input');
        element.className = baseInputClass;

        if (field.type === 'number') {
          element.type = 'number';
          element.step = 'any';
        } else if (field.type === 'date') {
          element.type = 'date';
          element.className = baseInputClass + ' cursor-pointer';
        } else {
          element.type = 'text';
        }
      }

      if (field.type !== 'select' && element) {
        element.id = fieldId;
        element.dataset.templateField = field.name;
        element.dataset.fieldLabel = field.label || field.name;

        if (field.placeholder) {
          element.placeholder = field.placeholder;
        }

        if (field.required) {
          element.required = true;
          element.setAttribute('aria-required', 'true');
        }

        element.addEventListener('input', renderTemplatePreview);
        element.addEventListener('change', renderTemplatePreview);
      }

      return element;
    }

    function renderDynamicTemplateFields() {
      const selected = getSelectedTemplate();

      if (selected && dynamicFieldsContainer) {
        if (templateCodeLabel) {
          templateCodeLabel.textContent = selected.template_code;
        }

        if (templateDescriptionText) {
          templateDescriptionText.textContent = selected.description || 'Isi kolom di bawah ini sesuai keperluan surat resmi Anda.';
        }

        if (templateDocTitle) {
          const autoLabel = ME.name ? selected.title + ' - ' + ME.name : selected.title;
          templateDocTitle.placeholder = 'Otomatis: ' + autoLabel;
        }

        dynamicFieldsContainer.innerHTML = '';

        const schema = Array.isArray(selected.placeholders_schema) ? selected.placeholders_schema : [];

        if (schema.length === 0) {
          const emptyNote = document.createElement('div');
          emptyNote.className = 'p-3 rounded-lg bg-surface-container-low border border-outline-variant text-sm text-on-surface-variant';
          emptyNote.textContent = 'Template ini hanya menggunakan data identitas akun pemohon.';
          dynamicFieldsContainer.appendChild(emptyNote);
        } else {
          schema.forEach((field) => {
            const wrapper = document.createElement('div');

            const label = document.createElement('label');
            label.className = 'block font-label-md text-label-md text-primary font-semibold mb-2';
            label.htmlFor = 'tpl-field-' + field.name;
            label.textContent = (field.label || field.name) + ' ';

            if (field.required) {
              const star = document.createElement('span');
              star.className = 'text-error';
              star.textContent = '*';
              label.appendChild(star);
            }

            const inputNode = createDynamicFieldInput(field);

            wrapper.append(label);

            if (inputNode) {
              wrapper.appendChild(inputNode);
            }

            dynamicFieldsContainer.appendChild(wrapper);
          });
        }

        syncTemplateInputsDisabledState();
        renderTemplatePreview();
      }
    }

    function syncTemplateInputsDisabledState() {
      const isTemplate = submissionMode.value === 'template';

      if (fileUpload) {
        fileUpload.disabled = isTemplate;
      }

      if (docTitle) {
        docTitle.disabled = isTemplate;
      }

      if (docExpires) {
        docExpires.disabled = isTemplate;
      }

      if (templateSelect) {
        templateSelect.disabled = !isTemplate;
      }

      if (templateDocTitle) {
        templateDocTitle.disabled = !isTemplate;
      }

      if (tplExpires) {
        tplExpires.disabled = !isTemplate;
      }

      if (dynamicFieldsContainer) {
        dynamicFieldsContainer.querySelectorAll('[data-template-field]').forEach((el) => {
          el.disabled = !isTemplate;
        });
      }
    }

    function setSubmittingState(active) {
      isSubmitting = active;

      if (submitBtn) {
        submitBtn.disabled = active;
        submitBtn.classList.toggle('opacity-70', active);
        submitBtn.classList.toggle('cursor-not-allowed', active);
      }

      if (submitBtnIcon) {
        if (active) {
          submitBtnIcon.textContent = 'progress_activity';
          submitBtnIcon.classList.add('animate-spin');
        } else {
          submitBtnIcon.textContent = 'arrow_forward';
          submitBtnIcon.classList.remove('animate-spin');
        }
      }

      if (submitBtnText) {
        if (active) {
          submitBtnText.textContent = 'Membuat PDF & Mengirim...';
        } else if (submissionMode.value === 'template') {
          submitBtnText.textContent = 'Buat & Ajukan Dokumen';
        } else {
          submitBtnText.textContent = 'Lanjut ke Atur Posisi TTE';
        }
      }
    }

    function switchTab(mode) {
      clearError();

      const isTemplate = mode === 'template';

      tabUploadBtn.classList.remove(...(isTemplate ? activeBtnClass : inactiveBtnClass));
      tabUploadBtn.classList.add(...(isTemplate ? inactiveBtnClass : activeBtnClass));
      tabTemplateBtn.classList.remove(...(isTemplate ? inactiveBtnClass : activeBtnClass));
      tabTemplateBtn.classList.add(...(isTemplate ? activeBtnClass : inactiveBtnClass));

      tabUploadContent.classList.toggle('hidden', isTemplate);
      tabTemplateContent.classList.toggle('hidden', !isTemplate);

      submissionMode.value = isTemplate ? 'template' : 'upload';

      if (!userTouchedRouting) {
        if (isTemplate && signerIds().length === 0) {
          triageCheck.checked = true;
          updateTriageState();
        } else if (!isTemplate) {
          triageCheck.checked = false;
          updateTriageState();
        }
      }

      syncTemplateInputsDisabledState();

      submitBtnText.textContent = isTemplate ? 'Buat & Ajukan Dokumen' : 'Lanjut ke Atur Posisi TTE';
      pageSubtitle.textContent = isTemplate
        ? 'Pilih template resmi, lengkapi data surat, dan tinjau pratinjau A4 sebelum diajukan'
        : 'Unggah berkas PDF dan tentukan alur pihak penandatangan';

      if (isTemplate) {
        renderTemplatePreview();
      }
    }

    async function submitTemplateForm() {
      const selected = getSelectedTemplate();

      if (!selected) {
        showError('Template surat resmi belum tersedia.');
      } else {
        const schema = Array.isArray(selected.placeholders_schema) ? selected.placeholders_schema : [];
        const fieldValues = collectTemplateFieldValues();
        let validationError = '';
        let firstInvalidInput = null;

        schema.forEach((field) => {
          if (validationError === '' && field.required) {
            const val = fieldValues[field.name] || '';

            if (val === '') {
              validationError = 'Isian "' + (field.label || field.name) + '" wajib diisi.';
              firstInvalidInput = document.getElementById('tpl-field-' + field.name);
            }
          }
        });

        if (validationError !== '') {
          showError(validationError);

          if (firstInvalidInput) {
            firstInvalidInput.focus();
          }
        } else if (!triageCheck.checked && signerIds().length === 0) {
          showError('Pilih minimal satu penandatangan, atau centang "Saya tidak tahu alur pejabat penandatangan".');
        } else if (!previewSheet || !window.PnjLetterRenderer) {
          showError('Komponen pembuat surat PDF belum siap.');
        } else {
          setSubmittingState(true);

          try {
            const exportSheet = previewSheet.cloneNode(true);
            const exportValues = buildTemplateValuesMap(selected, false);

            window.PnjLetterRenderer.renderIntoSheet(exportSheet, selected.html_content, exportValues, { isPreview: false });

            const pdfBlob = await window.PnjLetterRenderer.generatePdfBlob(exportSheet);

            if (!pdfBlob || pdfBlob.size === 0) {
              throw new Error('Gagal menghasilkan berkas PDF dari template.');
            }

            if (pdfBlob.size > MAX_SIZE) {
              throw new Error('Ukuran PDF hasil template melebihi batas maksimal 2 MB.');
            }

            const formData = new FormData();
            const safeFilename = (selected.template_code || 'surat').toLowerCase().replace(/[^a-z0-9_-]/g, '-') + '.pdf';
            const pdfFile = new File([pdfBlob], safeFilename, { type: 'application/pdf' });

            formData.append('submission_mode', 'template');
            formData.append('template_id', selected.id);
            formData.append('document_number', exportValues.nomor_surat || '');
            formData.append('template_fields_json', JSON.stringify(fieldValues));
            formData.append('title', templateDocTitle ? templateDocTitle.value.trim() : '');
            formData.append('expires_at', tplExpires ? tplExpires.value : '');
            formData.append('document_file', pdfFile, safeFilename);

            if (triageCheck.checked) {
              formData.append('is_triage', '1');
            } else {
              if (sequentialCheck.checked) {
                formData.append('is_sequential', '1');
              }

              signerIds().forEach((id) => {
                formData.append('signers[]', id);
              });
            }

            const response = await fetch('/Tandain/document/create', {
              method: 'POST',
              body: formData,
              headers: {
                'X-Requested-With': 'XMLHttpRequest'
              }
            });

            const contentType = response.headers.get('content-type') || '';

            if (contentType.indexOf('application/json') !== -1) {
              const payload = await response.json();

              setSubmittingState(false);
              showError(payload.error || 'Gagal mengajukan dokumen.');
            } else if (!response.ok) {
              setSubmittingState(false);
              showError('Terjadi kesalahan saat mengirim dokumen (HTTP ' + response.status + ').');
            } else if (response.url && response.url.indexOf('/document/create') !== -1) {
              setSubmittingState(false);
              showError('Pengajuan dokumen tidak dapat diproses. Periksa kembali isian Anda.');
            } else {
              window.location.href = response.url || '/Tandain/document';
            }
          } catch (err) {
            setSubmittingState(false);
            showError(err && err.message ? err.message : 'Gagal membuat atau mengirim berkas PDF.');
          }
        }
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

    addSignerBtn.addEventListener('click', () => {
      userTouchedRouting = true;
    });

    const closeSearch = setupSignerPicker(addSignerBtn, {
      signerIds,
      onSelect: (person) => addSigner({
        id: person.id,
        name: person.full_name,
        identity: person.identity_number,
        email: person.email,
        position: person.position || '',
        study_program: person.study_program || '',
        role_display_name: person.role_display_name || ''
      }),
      onVisibilityChange: renumberSigners
    });

    addSelfBtn?.addEventListener('click', () => {
      if (ME.canSign) {
        addSigner(ME);
      } else {
        showError('Akun Anda harus aktif dan memiliki sertifikat TTE yang masih berlaku.');
      }
    });

    setupSignerOrder(signerList, (row, announce) => {
      userTouchedRouting = true;
      renumberSigners();

      if (announce) {
        const name = row.querySelector('[data-signer-name]').textContent;
        const position = row.querySelector('[data-number]').textContent;
        signerAnnouncement.textContent = name + ' dipindah ke urutan ' + position + '.';
      }
    });

    signerList.addEventListener('click', (event) => {
      const button = event.target.closest('[data-remove-signer]');

      if (button) {
        userTouchedRouting = true;
        button.closest('[data-signer-row]').remove();
        renumberSigners();
      }
    });

    triageCheck.addEventListener('change', () => {
      userTouchedRouting = true;
      updateTriageState();
    });

    sequentialCheck.addEventListener('change', () => {
      userTouchedRouting = true;
      renumberSigners();
    });

    tabUploadBtn.addEventListener('click', () => switchTab('upload'));
    tabTemplateBtn.addEventListener('click', () => switchTab('template'));

    if (templateSelect) {
      templateSelect.addEventListener('change', renderDynamicTemplateFields);
    }

    window.addEventListener('resize', updatePreviewScale);

    if (window.ResizeObserver && previewViewport) {
      const resizeObserver = new ResizeObserver(updatePreviewScale);
      resizeObserver.observe(previewViewport);
    }

    const now = new Date();
    const today = [
      now.getFullYear(),
      String(now.getMonth() + 1).padStart(2, '0'),
      String(now.getDate()).padStart(2, '0')
    ].join('-');

    if (docExpires) {
      docExpires.min = today;
    }

    if (tplExpires) {
      tplExpires.min = today;
    }

    form.addEventListener('submit', (e) => {
      clearError();

      if (isSubmitting) {
        e.preventDefault();
      } else if (submissionMode.value === 'template') {
        e.preventDefault();
        submitTemplateForm();
      } else {
        const file = fileUpload.files && fileUpload.files[0];

        if (!file) {
          e.preventDefault();
          showError('Berkas PDF wajib diunggah.');
        } else if (file.size > MAX_SIZE) {
          e.preventDefault();
          showError('Ukuran berkas maksimal 2 MB.');
        } else if (!docTitle.value.trim()) {
          e.preventDefault();
          showError('Judul dokumen wajib diisi.');
        } else if (!triageCheck.checked && signerIds().length === 0) {
          e.preventDefault();
          showError('Pilih minimal satu penandatangan, atau centang "Saya tidak tahu alur pejabat penandatangan".');
        }
      }
    });

    renderDynamicTemplateFields();
    updateTriageState();

    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.get('mode') === 'template' || window.location.hash === '#template') {
      const requestedCode = urlParams.get('template') || '';

      if (requestedCode !== '' && templateSelect) {
        const matched = TEMPLATES.find((tpl) => tpl.template_code.toUpperCase() === requestedCode.toUpperCase());

        if (matched) {
          templateSelect.value = matched.id;
          renderDynamicTemplateFields();
        }
      }

      switchTab('template');
    } else {
      syncTemplateInputsDisabledState();
    }
  </script>

<?php require_once 'views/layouts/footer.php'; ?>

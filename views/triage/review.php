<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$document = $data['document'];
$canDispose = !empty($data['can_dispose']);
$currentStatus = $document['status'];

if ($currentStatus === 'PENDING_TRIAGE') {
    $pageTitle = 'Pemeriksaan & Disposisi Dokumen';
    $pageSubtitle = 'Verifikasi berkas permohonan dan tentukan alur pihak penandatangan';
    $backUrl = '/Tandain/triage';
} elseif ($currentStatus === 'IN_PROGRESS') {
    $pageTitle = 'Rincian Dokumen';
    $pageSubtitle = 'Dokumen telah didisposisikan dan sedang menunggu tanda tangan';
    $backUrl = '/Tandain/triage?status=dispatched';
} elseif ($currentStatus === 'COMPLETED') {
    $pageTitle = 'Rincian Dokumen';
    $pageSubtitle = 'Dokumen telah selesai ditandatangani dan tersimpan dalam arsip';
    $backUrl = '/Tandain/archive?status=COMPLETED';
} elseif ($currentStatus === 'REJECTED') {
    $pageTitle = 'Rincian Dokumen';
    $pageSubtitle = 'Pengajuan dokumen ditolak pada meja disposisi Tata Usaha';
    $backUrl = '/Tandain/archive?status=REJECTED';
} elseif ($currentStatus === 'CANCELLED') {
    $pageTitle = 'Rincian Dokumen';
    $pageSubtitle = 'Dokumen tidak lagi berada dalam alur tanda tangan';
    $backUrl = '/Tandain/archive?status=CANCELLED';
} else {
    $pageTitle = 'Rincian Dokumen';
    $pageSubtitle = 'Dokumen masih berstatus draf dan belum diajukan';
    $backUrl = '/Tandain/dashboard';
}

$isCompletedSigned = ($currentStatus === 'COMPLETED' && !empty($document['final_file_path']));
$filePath = $isCompletedSigned ? $document['final_file_path'] : ($document['original_file_path'] ?? '');
$fileExists = !empty($filePath) && file_exists($filePath);
$fileName = !empty($filePath) ? basename($filePath) : 'Berkas tidak tersedia';
$fileDescription = $isCompletedSigned ? 'Berkas surat resmi bertanda tangan elektronik' : 'Berkas permohonan surat digital';
?>

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
      <div>
        <div class="flex flex-wrap items-center gap-3">
          <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight font-bold"><?= $pageTitle; ?></h1>
          <?php renderStatusBadge($currentStatus); ?>
        </div>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1"><?= $pageSubtitle; ?></p>
      </div>

      <a class="btn-md inline-flex items-center gap-1.5 border border-outline-variant text-primary hover:bg-surface-container-low transition-colors shrink-0" href="<?= $backUrl; ?>">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        <span>Kembali</span>
      </a>
    </div>

    <?php require_once 'views/layouts/flash.php'; ?>

    <?php if ($currentStatus === 'REJECTED' && !empty($document['rejection_reason'])): ?>
      <?php $notice = ['type' => 'error', 'message' => 'Alasan penolakan: ' . $document['rejection_reason']]; ?>
      <?php require 'views/layouts/notice.php'; ?>
    <?php endif; ?>

    <?php if (!$canDispose): ?>
      <div class="flex items-start gap-3 rounded-xl border border-outline-variant bg-surface-container-low px-4 py-3 text-on-surface-variant font-body-sm text-body-sm">
        <span class="material-symbols-outlined text-[20px]">lock</span>
        <span>Halaman ini hanya menampilkan rincian. Disposisi tidak dapat diubah karena dokumen sudah keluar dari antrean.</span>
      </div>
    <?php endif; ?>

    <?php if ($canDispose): ?>
      <form class="flex flex-col gap-6" method="POST" action="/Tandain/triage/processDisposition" id="disposition-form" novalidate>
        <input type="hidden" name="document_id" value="<?= htmlspecialchars($document['id'], ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>

      <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 pb-3 border-b border-surface-container">
          <span class="material-symbols-outlined text-secondary text-[22px]">description</span>
          <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Berkas &amp; Informasi Dokumen</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <p class="block font-label-md text-label-md text-primary font-semibold mb-1">Judul Dokumen</p>
            <p class="font-body-md text-body-md text-on-surface font-medium"><?= htmlspecialchars($document['title'], ENT_QUOTES, 'UTF-8'); ?></p>
          </div>

          <div>
            <p class="block font-label-md text-label-md text-primary font-semibold mb-1">Pemohon</p>
            <p class="font-body-md text-body-md text-on-surface font-medium"><?= htmlspecialchars($document['requester_name'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="font-code-tabular text-body-sm text-on-surface-variant"><?= htmlspecialchars($document['requester_nim'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php $requesterAffiliation = formatUserAffiliation($document['requester_position'] ?? null, $document['requester_study_program'] ?? null, $document['requester_role_display_name'] ?? ''); ?>
            <?php if ($requesterAffiliation !== ''): ?>
              <p class="font-body-sm text-xs text-on-surface-variant mt-0.5"><?= htmlspecialchars($requesterAffiliation, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
          </div>

          <div>
            <p class="block font-label-md text-label-md text-primary font-semibold mb-1">Tanggal Diajukan</p>
            <p class="font-body-md text-body-md text-on-surface font-medium"><?= formatTanggalIndo($document['created_at'], 'datetime'); ?></p>
          </div>
        </div>

        <?php if (!empty($document['triage_admin_name'])): ?>
          <div class="mt-4 pt-4 border-t border-surface-container">
            <p class="font-body-sm text-body-sm text-on-surface-variant">Didisposisikan oleh <span class="font-semibold text-primary"><?= htmlspecialchars($document['triage_admin_name'], ENT_QUOTES, 'UTF-8'); ?></span></p>
          </div>
        <?php endif; ?>

        <div class="mt-6 pt-5 border-t border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">
              <span class="material-symbols-outlined text-[24px]">picture_as_pdf</span>
            </div>

            <div class="min-w-0">
              <p class="font-label-md text-label-md font-semibold text-primary truncate"><?= htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'); ?></p>
              <p class="font-body-sm text-xs text-on-surface-variant"><?= htmlspecialchars($fileDescription, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
          </div>

          <?php if ($fileExists): ?>
            <a href="/Tandain/triage/file/<?= rawurlencode($document['id']); ?>" target="_blank" rel="noopener" class="btn-md inline-flex items-center gap-2 border border-outline text-primary bg-white hover:bg-surface-container transition-all shrink-0">
              <span class="material-symbols-outlined text-[18px]">visibility</span>
              <span>Lihat Berkas</span>
            </a>
          <?php else: ?>
            <span class="inline-flex items-center gap-2 px-4 py-2 border border-outline-variant rounded-lg text-on-surface-variant font-label-md text-label-md bg-surface-container-low shrink-0">
              <span class="material-symbols-outlined text-[18px]">info</span>
              <span>Berkas tidak tersedia</span>
            </span>
          <?php endif; ?>
        </div>
      </section>

      <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-surface-container">
          <span class="material-symbols-outlined text-secondary text-[22px]">tag</span>
          <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Nomor &amp; Legalitas Surat Dinas</h2>
        </div>

        <?php if ($canDispose): ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="document_number">
                Nomor Naskah Kedinasan Resmi PNJ <span class="font-normal text-on-surface-variant">(Opsional)</span>
              </label>
              <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition" id="document_number" name="document_number" type="text" value="<?= htmlspecialchars($document['document_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Otomatis terisi untuk template / kosongkan jika berkas unggahan">
            </div>

            <div>
              <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="expires_at">
                Batas Kedaluwarsa Dokumen <span class="font-normal text-on-surface-variant">(Opsional)</span>
              </label>
              <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition cursor-pointer" id="expires_at" name="expires_at" type="date" min="<?= date('Y-m-d'); ?>" value="<?= htmlspecialchars(!empty($document['expires_at']) ? date('Y-m-d', strtotime($document['expires_at'])) : '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <p class="block font-label-md text-label-md text-primary font-semibold mb-1">Nomor Naskah Kedinasan</p>
              <p class="font-body-md text-body-md text-on-surface font-medium"><?= !empty($document['document_number']) ? htmlspecialchars($document['document_number'], ENT_QUOTES, 'UTF-8') : 'Belum ditetapkan'; ?></p>
            </div>

            <div>
              <p class="block font-label-md text-label-md text-primary font-semibold mb-1">Batas Kedaluwarsa</p>
              <p class="font-body-md text-body-md text-on-surface font-medium"><?= !empty($document['expires_at']) ? formatTanggalIndo($document['expires_at']) : 'Tanpa batas kedaluwarsa'; ?></p>
            </div>
          </div>
        <?php endif; ?>
      </section>

      <section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-4 lg:p-5 shadow-sm" id="section-signers">

        <div class="flex flex-col gap-3 border-b border-surface-container mb-3 pb-3">
          <div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-secondary text-[22px]">route</span>
              <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Alur Pihak Penandatangan</h2>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Tentukan pihak berwenang dan urutan penandatanganan dokumen elektronik</p>
          </div>

          <?php if ($canDispose): ?>
            <div class="flex items-start gap-3" id="sequential-toggle-container">
              <label class="relative inline-flex items-center cursor-pointer select-none">
                <input <?= in_array($document['is_sequential'] ?? true, [true, 1, '1', 't', 'true'], true) ? 'checked' : ''; ?> class="sr-only peer" id="sequential-check" name="is_sequential" type="checkbox" value="1" aria-labelledby="sequential-label">
                <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#004B87]"></div>
              </label>

              <div>
                <span class="font-label-md text-label-md text-primary block leading-tight font-semibold" id="sequential-label">Penandatanganan dokumen harus mengikuti urutan</span>
                <span class="text-xs text-on-surface-variant font-normal block leading-relaxed">Jika aktif, berkas akan diteruskan bertahap dari penandatangan pertama ke berikutnya</span>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <div class="space-y-4 transition-opacity duration-200" id="signers-flow-container">

          <?php if ($canDispose): ?>
            <div class="flex flex-wrap items-center gap-2">
              <button class="btn-md inline-flex items-center justify-center bg-brand hover:bg-brand-hover text-white font-semibold shadow-sm transition" id="btn-show-add-signer" type="button">
                <span class="material-symbols-outlined text-[18px] mr-1">add</span>
                <span>Tambah Penandatangan</span>
              </button>
            </div>
          <?php endif; ?>

          <div class="space-y-2">

            <?php if ($canDispose): ?>
              <p class="text-xs text-on-surface-variant" id="signer-order-hint" hidden>Tarik pegangan di kiri untuk mengubah urutan.</p>
              <p class="sr-only" role="status" id="signer-announcement"></p>

              <div class="space-y-2" id="signer-list"></div>

              <p class="text-sm text-on-surface-variant py-2" id="signer-empty" <?= !empty($data['current_signers']) ? 'hidden' : ''; ?>>Belum ada penandatangan. Klik "Tambah Penandatangan" untuk mencari.</p>

              <?php require 'views/layouts/signer_picker.php'; ?>

              <?php $notice = ['type' => 'error', 'id' => 'disposition-error']; ?>
              <?php require 'views/layouts/notice.php'; ?>
            <?php elseif (empty($data['current_signers'])): ?>
              <p class="text-sm text-on-surface-variant py-2">Belum ada penandatangan.</p>
            <?php else: ?>
              <?php foreach ($data['current_signers'] as $index => $signer): ?>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border border-outline-variant bg-surface-container-lowest gap-4">
                  <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0"><?= $index + 1; ?></div>

                    <div class="flex-1 min-w-0">
                      <p class="font-label-lg text-label-lg font-bold text-primary truncate"><?= htmlspecialchars($signer['signer_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                      <p class="font-body-sm text-body-sm text-on-surface-variant truncate mt-0.5">
                        <?= htmlspecialchars($signer['signer_email'], ENT_QUOTES, 'UTF-8'); ?> &bull; <span class="font-code-tabular"><?= htmlspecialchars($signer['signer_nim_nip'], ENT_QUOTES, 'UTF-8'); ?></span>
                      </p>
                      <p class="text-[11px] text-on-surface-variant mt-0.5"><?= htmlspecialchars(formatUserAffiliation($signer['signer_position'] ?? null, $signer['signer_study_program'] ?? null, $signer['signer_role_display_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                  </div>

                  <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap shrink-0">
                    <span class="inline-flex items-center rounded-full bg-surface-container px-2.5 py-1 text-[11px] font-semibold text-on-surface-variant border border-outline-variant">
                      <?= ($signer['stamp_type'] ?? 'DUAL_DISPLAY') === 'SIGNATURE_ONLY' ? 'Tanda Tangan Saja' : ((($signer['stamp_type'] ?? '') === 'QR_ONLY') ? 'QR Code Saja' : 'Tanda Tangan & QR'); ?>
                    </span>

                    <span class="text-[11px] text-on-surface-variant">Hal. <?= htmlspecialchars((string)($signer['page_number'] ?? 1), ENT_QUOTES, 'UTF-8'); ?></span>

                    <?php
                    $signerStatus = $signer['status'];

                    if (in_array($currentStatus, ['REJECTED', 'CANCELLED'], true) && in_array($signerStatus, ['WAITING', 'CURRENT_TURN'], true)) {
                        $signerStatus = 'CANCELLED';
                    }

                    renderStatusBadge($signerStatus);
                    ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>

          </div>

        </div>

      </section>

    <?php if ($canDispose): ?>

        <div class="flex flex-wrap items-center justify-end gap-3 pt-5 border-t border-outline-variant">
          <button class="btn-md sm:mr-auto border border-red-300 text-red-600 hover:bg-red-50 font-medium transition inline-flex items-center gap-2" onclick="openRejectModal()" type="button">
            <span class="material-symbols-outlined text-[18px]">block</span>
            <span>Tolak Pengajuan</span>
          </button>

          <div class="flex flex-wrap items-center justify-end gap-3">
            <a class="btn-md inline-flex border border-outline-variant text-on-surface-variant hover:bg-surface-container hover:text-on-surface font-medium transition" href="/Tandain/triage">
              Batal
            </a>

            <button class="btn-lg bg-brand hover:bg-brand-hover text-white font-semibold shadow-sm flex items-center gap-2 transition active:scale-[0.99]" type="submit">
              <span>Lanjut ke Atur Posisi TTE</span>
              <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </button>
          </div>
        </div>

      </form>

      <?php require 'views/layouts/signer_badges.php'; ?>
      <?php require 'views/layouts/signer_order_control.php'; ?>
      <?php require 'views/layouts/signer_row.php'; ?>

      <div class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4 hidden" id="reject-modal">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl max-w-lg w-full p-6 shadow-xl flex flex-col gap-4">
          <div class="flex items-center justify-between border-b border-surface-container pb-3">
            <div class="flex items-center gap-2 text-red-600">
              <span class="material-symbols-outlined text-[22px]">block</span>
              <h3 class="font-headline-sm text-headline-sm font-semibold">Tolak Pengajuan Dokumen</h3>
            </div>

            <button class="text-on-surface-variant hover:text-primary" onclick="closeRejectModal()" type="button">
              <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
          </div>

          <form action="/Tandain/triage/reject" class="flex flex-col gap-4" method="POST">
            <input name="document_id" type="hidden" value="<?= htmlspecialchars($document['id'], ENT_QUOTES, 'UTF-8'); ?>">

            <div>
              <label class="block font-label-md text-label-md text-primary font-semibold mb-1" for="modal_rejection_reason">
                Alasan Penolakan <span class="text-error">*</span>
              </label>
              <textarea class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-primary font-body-sm text-body-sm focus:border-red-500 outline-none transition" id="modal_rejection_reason" name="rejection_reason" placeholder="Tuliskan alasan formal penolakan berkas mahasiswa..." required rows="3"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
              <button class="btn-md inline-flex border border-outline-variant text-on-surface-variant hover:bg-surface-container font-medium transition" onclick="closeRejectModal()" type="button">
                Batal
              </button>

              <button class="btn-md inline-flex bg-red-600 hover:bg-red-700 text-white font-semibold transition" type="submit">
                Tolak Berkas
              </button>
            </div>
          </form>
        </div>
      </div>

    <?php endif; ?>

  </main>

<?php if ($canDispose): ?>
  <?php
  $currentSigners = [];

  foreach ($data['current_signers'] as $signer) {
      $currentSigners[] = [
          'id' => $signer['signer_id'],
          'name' => $signer['signer_name'],
          'email' => $signer['signer_email'],
          'identity' => $signer['signer_nim_nip'],
          'position' => $signer['signer_position'] ?? '',
          'study_program' => $signer['signer_study_program'] ?? '',
          'role_display_name' => $signer['signer_role_display_name'] ?? '',
      ];
  }
  ?>

  <script src="/Tandain/assets/js/signer-picker.js"></script>
  <script src="/Tandain/assets/js/signer-order.js"></script>
  <script>
    const currentSigners = <?= json_encode($currentSigners, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE); ?>;
    const signersList = document.getElementById('signer-list');
    const signerAnnouncement = document.getElementById('signer-announcement');
    const btnShowAddSigner = document.getElementById('btn-show-add-signer');
    const sequentialCheck = document.getElementById('sequential-check');
    const dispositionForm = document.getElementById('disposition-form');
    const dispositionError = document.getElementById('disposition-error');

    function renumberSigners() {
      updateSignerList(signersList, sequentialCheck.checked);
    }

    function showDispositionError(message) {
      showNotice(dispositionError, message);
      dispositionError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function hideDispositionError() {
      hideNotice(dispositionError);
    }

    currentSigners.forEach((person) => signersList.append(createSignerRow(person, 'signer_ids[]')));

    setupSignerPicker(btnShowAddSigner, {
      signerIds: () => Array.from(signersList.querySelectorAll('input[name="signer_ids[]"]'), (input) => input.value),
      onSelect: (user) => {
        const person = {
          id: user.id,
          name: user.full_name,
          email: user.email,
          identity: user.identity_number,
          position: user.position || '',
          study_program: user.study_program || '',
          role_display_name: user.role_display_name || ''
        };
        signersList.append(createSignerRow(person, 'signer_ids[]'));
        hideDispositionError();
      },
      onVisibilityChange: renumberSigners
    });

    setupSignerOrder(signersList, (row, announce) => {
      renumberSigners();

      if (announce) {
        const name = row.querySelector('[data-signer-name]').textContent;
        const position = row.querySelector('[data-number]').textContent;
        signerAnnouncement.textContent = name + ' dipindah ke urutan ' + position + '.';
      }
    });

    signersList.addEventListener('click', (event) => {
      const button = event.target.closest('[data-remove-signer]');

      if (button) {
        button.closest('[data-signer-row]').remove();
        renumberSigners();
      }
    });

    sequentialCheck.addEventListener('change', renumberSigners);

    if (dispositionForm) {
      dispositionForm.addEventListener('submit', function (event) {
        hideDispositionError();

        const signerCount = signersList.children.length;
        const expiresAt = document.getElementById('expires_at').value;
        const today = '<?= date('Y-m-d'); ?>';

        if (signerCount === 0) {
          event.preventDefault();
          showDispositionError('Tetapkan minimal satu penandatangan sebelum menyimpan disposisi.');
          btnShowAddSigner.focus();
        } else if (expiresAt !== '' && expiresAt < today) {
          event.preventDefault();
          showDispositionError('Batas kedaluwarsa dokumen tidak boleh berada di masa lalu.');
          document.getElementById('expires_at').focus();
        }
      });
    }

    function openRejectModal() {
      const modal = document.getElementById('reject-modal');

      if (modal) {
        modal.classList.remove('hidden');
      }
    }

    function closeRejectModal() {
      const modal = document.getElementById('reject-modal');

      if (modal) {
        modal.classList.add('hidden');
      }
    }

    renumberSigners();
  </script>
<?php endif; ?>

<?php require_once 'views/layouts/footer.php'; ?>

<?php
$isLoggedIn = !empty($data['user']['id']);
$formWidth = $isLoggedIn ? 'max-w-5xl' : 'max-w-3xl';
$status = $data['status'] ?? 'upload';
$document = $data['document'] ?? null;
$hasResult = in_array($status, ['success', 'expired'], true) && !empty($document);
$signers = $hasResult && is_array($document['signers'] ?? null) ? $document['signers'] : [];
$filePath = $hasResult ? ($document['final_file_path'] ?? '') : '';
$fileUrl = ($hasResult && $filePath !== '' && !empty($document['id'])) ? '/Tandain/verification/file/' . rawurlencode($document['id']) : '';
$fileHash = $data['file_hash'] ?? '';
$hasFileHash = preg_match('/\A[0-9a-f]{64}\z/i', $fileHash) === 1;
$hashLabel = $hasResult ? 'Hash SHA-256 (Dokumen Final Bertanda Tangan)' : 'Hash SHA-256 (Berkas Diunggah)';
$signerCertificates = array_filter($signers, static fn ($signer) => !empty($signer['certificate_serial']));

require_once 'views/layouts/header.php';
?>

<?php if ($isLoggedIn): ?>
  <?php require_once 'views/layouts/sidebar.php'; ?>
  <?php require_once 'views/layouts/navbar.php'; ?>
<?php else: ?>
  <div class="flex min-h-screen min-w-0 flex-1 flex-col">
    <header class="sticky top-0 z-30 border-b border-outline-variant bg-surface-container-lowest">
      <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-6 lg:px-8">
        <a href="/Tandain/verification" class="flex items-center gap-2.5">
          <img src="/Tandain/assets/img/app-icon.png" alt="" class="size-10 shrink-0 object-contain">
          <span class="font-headline-sm text-headline-sm font-bold text-primary">Tandain</span>
        </a>

        <a href="/Tandain/login" class="btn-md inline-flex gap-2 border border-outline-variant font-semibold text-primary hover:bg-surface-container-low">
          <span class="material-symbols-outlined text-[18px]" aria-hidden="true">login</span>
          Masuk
        </a>
      </div>
    </header>
<?php endif; ?>

<main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6 <?= $isLoggedIn ? '' : 'mx-auto max-w-6xl'; ?>">

  <?php if ($hasResult): ?>

    <?php if ($status === 'expired' || !empty($data['is_expired'])): ?>

      <section class="bg-[#fffbeb] border border-[#fde68a] rounded-xl p-5 shadow-sm">
        <div class="flex items-center gap-4">
          <div class="flex-shrink-0 h-11 w-11 rounded-full bg-surface-container-lowest shadow-sm border border-[#fef3c7] flex items-center justify-center text-[#d97706]">
            <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;">warning</span>
          </div>
          <div class="flex-1">
            <h1 class="font-headline-lg text-headline-lg font-bold text-[#78350f]">Dokumen Kedaluwarsa</h1>
            <p class="font-body-md text-body-md text-[#92400e] mt-0.5 leading-relaxed">Dokumen terdaftar di Tandain dan telah melewati masa berlaku</p>
          </div>
        </div>
      </section>

    <?php else: ?>

      <section class="bg-emerald-50 border border-emerald-200/90 rounded-xl p-5 shadow-sm">
        <div class="flex items-center gap-4">
          <div class="flex-shrink-0 h-11 w-11 rounded-full bg-surface-container-lowest shadow-sm border border-emerald-100 flex items-center justify-center text-emerald-600">
            <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
          </div>
          <div class="flex-1">
            <h1 class="font-headline-lg text-headline-lg font-bold text-emerald-950">Dokumen Valid</h1>
            <p class="font-body-md text-body-md text-emerald-900/90 mt-0.5 leading-relaxed">Dokumen terdaftar di Tandain dan masih berlaku</p>
          </div>
        </div>
      </section>

    <?php endif; ?>

    <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl shadow-sm overflow-hidden">
      <div class="grid grid-cols-1 md:grid-cols-12 divide-y md:divide-y-0 md:divide-x divide-outline-variant/50">

        <div class="md:col-span-7 p-6 space-y-5 flex flex-col justify-between">
          <div class="flex items-center justify-between border-b border-outline-variant/40 pb-3">
            <div class="flex items-center gap-2.5">
              <div class="p-1.5 rounded-lg bg-surface-container-low text-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">description</span>
              </div>
              <h2 class="font-headline-md text-headline-sm font-bold text-primary tracking-tight">Informasi Dokumen</h2>
            </div>
          </div>

          <div class="space-y-3.5 flex-1">
            <div class="flex items-start justify-between py-1 border-b border-outline-variant/30">
              <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Judul Dokumen</span>
              <span class="font-headline-sm text-[14px] font-bold text-primary text-right max-w-[60%]"><?= htmlspecialchars($document['title'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>

            <div class="flex items-start justify-between py-1 border-b border-outline-variant/30">
              <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Nomor Dokumen</span>
              <span class="font-body-md text-[13px] font-medium text-slate-700 text-right font-code-tabular"><?= htmlspecialchars(($document['document_number'] ?? '') ?: '-', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>

            <div class="flex items-start justify-between py-1 border-b border-outline-variant/30">
              <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Pemohon</span>
              <div class="text-right">
                <span class="font-headline-sm text-[13px] font-bold text-primary block"><?= htmlspecialchars($document['requester_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="font-code-tabular text-[11px] text-slate-500 block"><?= htmlspecialchars(($document['requester_nim'] ?? '') ?: '-', ENT_QUOTES, 'UTF-8'); ?></span>
                <?php $requesterAffiliation = formatUserAffiliation($document['requester_position'] ?? null, $document['requester_study_program'] ?? null, $document['requester_role_display_name'] ?? ''); ?>
                <?php if ($requesterAffiliation !== ''): ?>
                  <span class="font-body-sm text-[11px] text-on-surface-variant block"><?= htmlspecialchars($requesterAffiliation, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
              </div>
            </div>

            <?php $approvedAt = !empty($document['completed_at']) ? $document['completed_at'] : (!empty($document['issued_at']) ? $document['issued_at'] : ($document['created_at'] ?? '')); ?>
            <div class="flex items-start justify-between py-1 border-b border-outline-variant/30">
              <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Tanggal Disahkan</span>
              <span class="font-body-md text-[13px] font-medium text-slate-700 text-right"><?= !empty($approvedAt) ? formatTanggalIndo($approvedAt, 'datetime') : '-'; ?></span>
            </div>

            <div class="flex items-start justify-between py-1">
              <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Masa Berlaku</span>
              <span class="font-body-md text-[13px] font-medium text-slate-800 text-right"><?= !empty($document['expires_at']) ? formatTanggalIndo($document['expires_at']) : 'Selamanya'; ?></span>
            </div>
          </div>
        </div>

        <div class="md:col-span-5 p-6 bg-surface-container-low/40 flex flex-col justify-between space-y-4">
          <div class="border-b border-outline-variant/40 pb-3">
            <div class="flex items-center gap-2.5">
              <div class="flex items-center justify-center rounded-lg bg-surface-container-low p-1.5 text-primary">
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">groups</span>
              </div>
              <h2 class="font-headline-sm text-headline-sm font-bold text-primary">Daftar Penandatangan</h2>
            </div>
          </div>

          <div class="space-y-3 flex-1 flex flex-col justify-center">

            <?php if (empty($signers)): ?>
              <div class="p-6 text-center text-on-surface-variant text-xs bg-surface-container-lowest rounded-xl border border-outline-variant/50">
                Belum ada penandatangan yang ditugaskan pada dokumen ini.
              </div>
            <?php else: ?>
              <?php foreach ($signers as $signer): ?>
                <?php $isSigned = $signer['status'] === 'SIGNED'; ?>
                <div class="bg-surface-container-lowest border border-outline-variant/50 rounded-xl p-3.5 shadow-sm space-y-2">
                  <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                      <div class="h-7 w-7 rounded-full bg-primary-container text-surface-container-lowest font-headline-sm text-[12px] font-bold flex items-center justify-center flex-shrink-0">
                        <?= htmlspecialchars((string)($signer['signing_order'] ?? 1), ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                      <div class="min-w-0">
                        <h3 class="font-headline-sm text-[13px] font-bold text-primary truncate"><?= htmlspecialchars($signer['full_name'] ?? 'Penandatangan', ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="font-body-sm text-[11px] text-on-surface-variant truncate"><?= htmlspecialchars(formatUserAffiliation($signer['position'] ?? null, $signer['study_program'] ?? null, $signer['role_display_name'] ?? 'Civitas Akademika'), ENT_QUOTES, 'UTF-8'); ?></p>
                      </div>
                    </div>
                    <?php renderStatusBadge($signer['status']); ?>
                  </div>

                  <div class="flex items-center justify-between text-[10.5px] text-slate-500 pt-1.5 border-t border-slate-100">
                    <?php if ($isSigned && !empty($signer['signed_at'])): ?>
                      <span class="font-code-tabular font-medium text-slate-600"><?= formatTanggalIndo($signer['signed_at'], 'datetime'); ?></span>
                    <?php else: ?>
                      <span class="font-body-sm text-[11px] text-slate-400">Belum dibubuhkan</span>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>

          </div>
        </div>

      </div>
    </div>

    <div class="pt-1">
      <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
        <?php if ($fileUrl !== ''): ?>
          <a href="<?= htmlspecialchars($fileUrl . '?download=1', ENT_QUOTES, 'UTF-8'); ?>" class="btn-md flex-1 w-full bg-brand hover:bg-brand-hover active:scale-[0.99] text-white flex items-center justify-center gap-2.5 shadow-sm transition-all duration-150">
            <span class="material-symbols-outlined text-[20px]">download</span>
            <span>Unduh Salinan PDF</span>
          </a>
        <?php endif; ?>

        <a href="/Tandain/verification" class="btn-md w-full sm:w-auto bg-surface-container-lowest hover:bg-surface-container-low text-primary border border-outline-variant/60 flex items-center justify-center gap-2 shadow-sm transition-all duration-150">
          <span class="material-symbols-outlined text-[20px]">document_scanner</span>
          <span>Verifikasi Dokumen Lain</span>
        </a>
      </div>
    </div>

  <?php elseif ($status === 'error'): ?>

    <div class="mx-auto w-full space-y-6 <?= $formWidth; ?>">
      <div class="space-y-2">
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Verifikasi Dokumen</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Validasi keaslian berkas dan tanda tangan elektronik resmi</p>
      </div>

      <div class="space-y-6 rounded-xl border border-outline-variant bg-surface-container-lowest p-4 shadow-sm sm:p-6">
        <div class="rounded-xl border border-red-200 bg-red-50/70 p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
          <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0 shadow-sm">
            <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">cancel</span>
          </div>
          <div class="flex-1 space-y-1">
            <h2 class="text-lg font-bold text-red-800 font-headline-sm">Dokumen Tidak Terdaftar</h2>
            <p class="text-sm text-red-700 font-body-sm leading-relaxed">Berkas tidak cocok dengan dokumen final bertanda tangan yang terdaftar di Tandain, atau proses tanda tangan belum selesai.</p>
          </div>
        </div>

        <div class="bg-surface-container-low border border-outline-variant/50 rounded-xl p-6 space-y-4">
          <h3 class="text-sm font-semibold text-primary font-headline-sm inline-flex items-center gap-2 pb-3 border-b border-outline-variant/40 w-full">
            <span class="material-symbols-outlined text-[20px] text-on-surface-variant">description</span>
            <span>Rincian Berkas Pemeriksaan</span>
          </h3>
          <div class="grid grid-cols-1 gap-4 text-left font-body-sm sm:grid-cols-2">
            <div class="space-y-1">
              <span class="text-xs text-on-surface-variant font-medium">Nama Berkas</span>
              <p class="text-sm font-semibold text-primary break-all"><?= htmlspecialchars($data['file_name'] ?: 'berkas_pemeriksaan.pdf', ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div class="space-y-1">
              <span class="text-xs text-on-surface-variant font-medium">Ukuran Berkas</span>
              <p class="text-sm font-semibold text-primary"><?= ($data['file_size'] > 0) ? number_format($data['file_size'] / (1024 * 1024), 2) . ' MB' : '-'; ?></p>
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-3 pt-2">
          <a href="/Tandain/verification" class="btn-md flex w-full bg-brand text-white transition-colors hover:bg-brand-hover">
            Unggah Ulang Berkas
          </a>
        </div>
      </div>
    </div>

  <?php else: ?>

    <div class="mx-auto w-full space-y-6 <?= $formWidth; ?>">
      <div class="space-y-2">
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Verifikasi Dokumen</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Validasi keaslian berkas dan tanda tangan elektronik resmi</p>
      </div>

      <div class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4 shadow-sm sm:p-6">
        <form method="POST" action="/Tandain/verification/check" enctype="multipart/form-data" class="space-y-8" id="verification-form" novalidate>
          <div class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-outline-variant bg-surface-container-low px-4 py-8 text-center transition-colors hover:border-secondary sm:px-8" id="drop-zone">
            <div class="w-14 h-14 rounded-xl bg-surface-container-lowest border border-outline-variant/50 flex items-center justify-center text-[#004B87] shadow-sm mb-4">
              <span class="material-symbols-outlined text-[32px]">picture_as_pdf</span>
            </div>

            <h2 id="drop-zone-text" class="mb-1.5 max-w-full break-words font-headline-sm text-headline-sm font-semibold text-primary">Tarik PDF ke sini atau pilih file</h2>
            <p id="drop-zone-subtext" class="font-body-sm text-body-sm text-on-surface-variant mb-6">Format PDF (Maks. 5 MB)</p>

            <input type="file" name="document_file" accept="application/pdf,.pdf" class="hidden" id="file-upload" aria-describedby="verification-error" <?= !empty($data['upload_error']) ? 'aria-invalid="true"' : ''; ?> required>
            <button type="button" id="browse-btn" class="btn-md inline-flex gap-2 border border-outline-variant bg-surface-container-lowest text-primary transition-colors hover:bg-surface-container-low">
              <span class="material-symbols-outlined text-[18px]" aria-hidden="true">folder_open</span>
              <span>Pilih File</span>
            </button>
          </div>

          <?php $notice = ['type' => 'error', 'id' => 'verification-error', 'message' => $data['upload_error'] ?? '']; ?>
          <?php require 'views/layouts/notice.php'; ?>

          <div class="flex flex-col gap-3 pt-1">
            <button type="submit" id="submit-btn" disabled class="btn-lg flex w-full cursor-not-allowed bg-brand text-white opacity-50 transition-colors">
              Periksa Dokumen
            </button>
          </div>
        </form>
      </div>
    </div>

  <?php endif; ?>

  <?php if ($hasFileHash || $signerCertificates): ?>
    <details class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4 sm:p-5 <?= $hasResult ? '' : 'mx-auto w-full ' . $formWidth; ?>">
      <summary class="cursor-pointer rounded font-medium text-primary focus-visible:outline-secondary">Rincian Teknis &amp; Kriptografi</summary>
      <dl class="mt-4 space-y-3 text-sm">
        <?php if ($hasFileHash): ?>
          <div>
            <dt class="text-on-surface-variant"><?= htmlspecialchars($hashLabel, ENT_QUOTES, 'UTF-8'); ?></dt>
            <dd class="break-all font-mono"><?= htmlspecialchars($fileHash, ENT_QUOTES, 'UTF-8'); ?></dd>
          </div>
        <?php endif; ?>

        <?php foreach ($signerCertificates as $signer): ?>
          <div>
            <dt class="text-on-surface-variant">Nomor seri sertifikat · <?= htmlspecialchars($signer['full_name'] ?? 'Penandatangan', ENT_QUOTES, 'UTF-8'); ?></dt>
            <dd class="break-all font-mono"><?= htmlspecialchars($signer['certificate_serial'], ENT_QUOTES, 'UTF-8'); ?></dd>
          </div>
        <?php endforeach; ?>
      </dl>
    </details>
  <?php endif; ?>

  <?php if ($fileUrl !== ''): ?>
    <section class="space-y-4">
      <div class="flex flex-wrap items-center justify-between gap-3 border-b border-outline-variant pb-3">
        <h2 class="font-headline-md text-headline-md font-bold text-primary">Pratinjau Dokumen</h2>
        <a href="<?= htmlspecialchars($fileUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener" class="btn-sm inline-flex shrink-0 gap-1.5 border border-outline-variant bg-surface-container-lowest text-primary hover:bg-surface-container-low">
          <span class="material-symbols-outlined text-[16px]" aria-hidden="true">open_in_new</span>
          Buka PDF
        </a>
      </div>

      <iframe src="<?= htmlspecialchars($fileUrl, ENT_QUOTES, 'UTF-8'); ?>" class="h-[min(65vh,640px)] w-full rounded-xl border border-outline-variant bg-white" title="Pratinjau Dokumen"></iframe>
    </section>
  <?php endif; ?>

</main>

<?php if (!$isLoggedIn): ?>
    <footer class="border-t border-outline-variant bg-surface-container-lowest px-6 py-5 text-center text-sm text-on-surface-variant">
      &copy; 2026 Politeknik Negeri Jakarta
    </footer>
  </div>
<?php endif; ?>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('drop-zone');
    const fileUpload = document.getElementById('file-upload');
    const browseBtn = document.getElementById('browse-btn');
    const dropZoneText = document.getElementById('drop-zone-text');
    const dropZoneSubtext = document.getElementById('drop-zone-subtext');
    const submitBtn = document.getElementById('submit-btn');
    const form = document.getElementById('verification-form');
    const uploadError = document.getElementById('verification-error');

    if (!dropZone || !fileUpload) return;

    const defaultText = dropZoneText.textContent;
    const defaultSubtext = dropZoneSubtext.textContent;
    const MAX_SIZE = 5 * 1024 * 1024;

    function enableSubmit() {
      if (!submitBtn) return;
      submitBtn.removeAttribute('disabled');
      submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
      submitBtn.classList.add('hover:bg-brand-hover', 'active:scale-[0.99]', 'cursor-pointer');
    }

    function disableSubmit() {
      if (!submitBtn) return;
      submitBtn.setAttribute('disabled', 'disabled');
      submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
      submitBtn.classList.remove('hover:bg-brand-hover', 'active:scale-[0.99]', 'cursor-pointer');
    }

    function handleFile() {
      hideNotice(uploadError);
      fileUpload.removeAttribute('aria-invalid');

      const file = fileUpload.files && fileUpload.files[0];

      if (!file) {
        resetDropZone();
      } else if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        resetDropZone();
        showUploadError('Berkas harus berformat PDF.');
      } else if (file.size > MAX_SIZE) {
        resetDropZone();
        showUploadError('Ukuran berkas maksimal 5 MB.');
      } else {
        dropZoneText.textContent = file.name;
        dropZoneSubtext.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
        dropZone.classList.add('border-emerald-500', 'bg-emerald-50/40');
        enableSubmit();
      }
    }

    function showUploadError(message) {
      fileUpload.setAttribute('aria-invalid', 'true');
      showNotice(uploadError, message);
    }

    function resetDropZone() {
      fileUpload.value = '';
      dropZoneText.textContent = defaultText;
      dropZoneSubtext.textContent = defaultSubtext;
      dropZone.classList.remove('border-emerald-500', 'bg-emerald-50/40');
      disableSubmit();
    }

    form.addEventListener('submit', (e) => {
      if (!fileUpload.files.length) {
        e.preventDefault();
        showUploadError('Pilih berkas PDF terlebih dahulu.');
      }
    });

    dropZone.addEventListener('click', () => {
      fileUpload.click();
    });

    browseBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      fileUpload.click();
    });

    fileUpload.addEventListener('change', handleFile);

    ['dragenter', 'dragover'].forEach((eventName) => {
      dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('border-secondary', 'bg-surface-container');
      });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
      dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('border-secondary', 'bg-surface-container');
      });
    });

    dropZone.addEventListener('drop', (e) => {
      if (e.dataTransfer && e.dataTransfer.files.length > 0) {
        fileUpload.files = e.dataTransfer.files;
        handleFile();
      }
    });
  });
</script>

<?php if ($isLoggedIn): ?>
  <?php require_once 'views/layouts/footer.php'; ?>
<?php else: ?>
</body>

</html>
<?php endif; ?>

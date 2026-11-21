<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
if ($document['status'] === 'COMPLETED') {
    $progressClass = 'bg-[#15803d]';
} elseif ($document['status'] === 'IN_PROGRESS') {
    $progressClass = 'bg-brand';
} elseif ($document['status'] === 'REJECTED') {
    $progressClass = 'bg-[#b91c1c]';
} elseif ($document['status'] === 'PENDING_TRIAGE') {
    $progressClass = 'bg-brand';
} else {
    $progressClass = 'bg-outline-variant';
}

$previewUrl = '/Tandain/document/file/' . rawurlencode($document['id']);
?>

<main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">
  <div class="mx-auto w-full">

    <div class="mb-6">
      <a href="/Tandain/document" class="inline-flex items-center gap-1 text-secondary font-label-md text-label-md hover:underline">
        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span>
        <span>Kembali ke daftar</span>
      </a>
      <h1 class="mt-2 font-headline-lg text-headline-lg font-bold tracking-tight text-primary"><?= htmlspecialchars($document['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
      <p class="mt-1 flex flex-wrap items-center gap-2 font-body-md text-body-md text-on-surface-variant">
        Diajukan pada <?= formatTanggalIndo($document['created_at'], 'datetime'); ?>
        <?php renderStatusBadge($document['status']); ?>
      </p>
    </div>

    <div class="space-y-3 [&:has([data-notice]:not([hidden]))]:mb-6">
      <?php require 'views/layouts/flash.php'; ?>

      <?php if ($document['status'] === 'REJECTED' && !empty($document['rejection_reason'])): ?>
        <?php $notice = ['type' => 'error', 'message' => 'Alasan penolakan: ' . $document['rejection_reason']]; ?>
        <?php require 'views/layouts/notice.php'; ?>
      <?php endif; ?>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.7fr)_minmax(280px,0.8fr)]">

      <div class="space-y-6">
        <section class="overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest shadow-sm">
          <div class="border-b border-outline-variant/70 p-5">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[18px] text-primary" aria-hidden="true">description</span>
              <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Tinjau Dokumen</h2>
            </div>
            <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Pratinjau dokumen yang diajukan.</p>
          </div>

          <div class="bg-surface-container-low p-4">
            <iframe
              title="Pratinjau dokumen"
              src="<?= htmlspecialchars($previewUrl, ENT_QUOTES, 'UTF-8'); ?>"
              class="h-[720px] w-full rounded-lg border border-outline-variant bg-white"></iframe>
          </div>
        </section>
      </div>

      <aside class="space-y-6">
        <section class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm">
          <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Pemohon</h2>
          <p class="mt-2 font-body-md text-body-md font-semibold text-primary"><?= htmlspecialchars($user['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>

          <?php if (!empty($user['position'])): ?>
            <p class="font-body-sm text-body-sm text-on-surface-variant"><?= htmlspecialchars($user['position'], ENT_QUOTES, 'UTF-8'); ?></p>
          <?php endif; ?>

          <?php if (!empty($user['email'])): ?>
            <p class="font-body-sm text-body-sm text-on-surface-variant"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
          <?php endif; ?>

          <div class="mt-4 space-y-3 border-t border-outline-variant/70 pt-4 text-sm">
            <?php if (!empty($document['document_number'])): ?>
              <div>
                <p class="font-label-md text-label-md text-on-surface-variant">Nomor Naskah</p>
                <p class="mt-1 font-code-tabular text-body-sm font-medium text-primary"><?= htmlspecialchars($document['document_number'], ENT_QUOTES, 'UTF-8'); ?></p>
              </div>
            <?php endif; ?>

            <div>
              <div class="mb-1.5 flex items-center justify-between">
                <span class="font-label-sm text-[11px] font-semibold text-on-surface-variant"><?= (int) $signedCount; ?>/<?= (int) $totalSigners; ?> tanda tangan</span>
                <span class="font-code-tabular text-[11px] font-bold text-on-surface-variant"><?= (int) $percent; ?>%</span>
              </div>

              <div class="h-2 w-full overflow-hidden rounded-full bg-surface-container">
                <div class="h-2 rounded-full <?= $progressClass; ?>" style="width: <?= (int) $percent; ?>%"></div>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-2 mt-6">
            <a
              href="<?= htmlspecialchars($previewUrl . '?version=original&download=1', ENT_QUOTES, 'UTF-8'); ?>"
              class="btn-md inline-flex flex-1 items-center justify-center gap-2 border border-outline-variant bg-white font-semibold text-primary transition-colors hover:bg-surface-container-low">
              <span class="material-symbols-outlined text-[18px]" aria-hidden="true">download</span>
              <span>Unduh PDF Asli</span>
            </a>

            <?php if (($document['status'] ?? '') === 'COMPLETED' && !empty($document['final_file_path'])): ?>
              <a
                href="<?= htmlspecialchars($previewUrl . '?version=signed&download=1', ENT_QUOTES, 'UTF-8'); ?>"
                class="btn-md inline-flex flex-1 items-center justify-center gap-2 bg-[#15803d] font-semibold text-white shadow-sm transition-colors hover:bg-[#166534]">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">download</span>
                <span>Unduh PDF Bertanda Tangan</span>
              </a>
            <?php endif; ?>
          </div>
        </section>

        <section class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm">
          <h3 class="font-headline-sm text-headline-sm font-semibold text-primary">Penandatangan</h3>

          <?php if (count($signers) === 0): ?>
            <p class="py-6 text-center font-body-md text-body-md text-on-surface-variant">Belum ada penanda tangan untuk dokumen ini.</p>
          <?php else: ?>
            <div class="mt-4 space-y-3">
              <?php foreach ($signers as $signer): ?>
                <?php $signedTimestamp = !empty($signer['signed_at']) ? strtotime($signer['signed_at']) : false; ?>
                <div class="flex flex-wrap items-start justify-between gap-3 rounded-lg border border-outline-variant bg-surface-container-low px-3 py-2.5">
                  <div class="min-w-0 flex-1">
                    <p class="whitespace-normal break-words font-body-md text-body-md font-medium text-primary"><?= htmlspecialchars($signer['full_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="font-body-sm text-body-sm text-on-surface-variant"><?= htmlspecialchars($signer['position'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="font-body-sm text-body-sm text-on-surface-variant break-all"><?= htmlspecialchars($signer['email'], ENT_QUOTES, 'UTF-8'); ?></p>

                    <?php if ($signer['status'] === 'SIGNED' && $signedTimestamp !== false): ?>
                      <p class="mt-1 inline-flex items-center gap-1 font-body-sm text-body-sm text-on-surface-variant">
                        <span class="material-symbols-outlined text-[14px]" aria-hidden="true">schedule</span>
                        <span>Ditandatangani <?= formatTanggalIndo($signedTimestamp, 'datetime'); ?></span>
                      </p>
                    <?php endif; ?>

                    <?php if (!empty($signer['rejection_reason'])): ?>
                      <p class="mt-1 font-body-sm text-body-sm text-error">Alasan: <?= htmlspecialchars($signer['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                  </div>

                  <div class="shrink-0">
                    <?php renderStatusBadge($signer['status']); ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>
      </aside>

    </div>
  </div>
</main>

<?php require_once 'views/layouts/footer.php'; ?>

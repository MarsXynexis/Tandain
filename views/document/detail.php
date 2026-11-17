<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
date_default_timezone_set('Asia/Jakarta');

$bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

$time = strtotime($document['created_at']);
$tanggal = date('d', $time) . ' ' . $bulan[date('n', $time) - 1] . ' ' . date('Y', $time);

if ($document['status'] === 'COMPLETED') {
  $label = 'Selesai';
  $badge = 'bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]';
} elseif ($document['status'] === 'IN_PROGRESS') {
  $label = 'Diproses';
  $badge = 'bg-[#dbeafe] text-[#1d4ed8] border border-[#bfdbfe]';
} elseif ($document['status'] === 'REJECTED') {
  $label = 'Ditolak';
  $badge = 'bg-[#FFDAD6] text-[#BA1A1A] border border-[#FFC5C5]';
} elseif ($document['status'] === 'PENDING_TRIAGE') {
  $label = 'Menunggu Disposisi';
  $badge = 'bg-amber-100 text-amber-700 border border-amber-200';
} else {
  $label = ($document['status'] === 'DRAFT') ? 'Draft' : 'Dibatalkan';
  $badge = 'bg-surface-container text-on-surface-variant border border-outline-variant';
}

$previewFile = $document['original_file_path'];

if (!empty($document['final_file_path'])) {
  $previewFile = $document['final_file_path'];
}
?>

<main class="flex-1 w-full p-8">
  <div class="mx-auto w-full">
    <div class="mb-6">
      <a href="/Tandain/document" class="inline-flex items-center gap-1 text-secondary font-label-md text-label-md hover:underline">
        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span>
        <span>Kembali ke daftar</span>
      </a>
      <h1 class="mt-2 font-headline-lg text-headline-lg font-bold tracking-tight text-primary"><?= htmlspecialchars($document['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
      <p class="mt-1 font-body-md text-body-md text-on-surface-variant">
        Diajukan pada <?= htmlspecialchars($tanggal, ENT_QUOTES, 'UTF-8'); ?>
        <span class="ml-2 inline-flex items-center rounded-full px-2.5 py-1 text-label-sm font-label-sm <?= $badge; ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span>
      </p>
    </div>

    <?php if ($document['status'] === 'REJECTED' && !empty($document['rejection_reason'])): ?>
      <div class="mb-6 rounded-xl border border-[#FFC5C5] bg-error-container p-4 font-body-md text-body-md text-on-error-container">
        <strong>Dokumen ditolak.</strong> <?= htmlspecialchars($document['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

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
              src="/Tandain/<?= htmlspecialchars(ltrim($previewFile, '/'), ENT_QUOTES, 'UTF-8'); ?>"
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
            <div>
              <p class="font-label-md text-label-md text-on-surface-variant">Judul Dokumen</p>
              <p class="mt-1 font-body-md text-body-md font-medium text-primary"><?= htmlspecialchars($document['title'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <div>
              <p class="font-label-md text-label-md text-on-surface-variant">Tanggal Diajukan</p>
              <p class="mt-1 font-body-md text-body-md text-on-surface"><?= htmlspecialchars($tanggal, ENT_QUOTES, 'UTF-8'); ?> • <?= date('H:i', $time); ?> WIB</p>
            </div>

            <div>
              <p class="font-label-md text-label-md text-on-surface-variant">Status</p>
              <span class="mt-1 inline-flex items-center rounded-full px-2.5 py-1 text-label-sm font-label-sm <?= $badge; ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>

            <div>
              <div class="mb-1.5 flex items-center justify-between">
                <span class="font-label-sm text-[11px] font-semibold text-on-surface-variant"><?= (int) $signedCount; ?>/<?= (int) $totalSigners; ?> tanda tangan</span>
                <span class="font-code-tabular text-[11px] font-bold text-on-surface-variant"><?= (int) $percent; ?>%</span>
              </div>
              <div class="h-2 w-full overflow-hidden rounded-full bg-surface-container">
                <div class="h-2 rounded-full bg-secondary-container" style="width: <?= (int) $percent; ?>%"></div>
              </div>
            </div>
          </div>

          <div class="flex justify-between mt-6 gap-2">
            <?php if (!empty($document['original_file_path'])): ?>
              <a
                href="/Tandain/<?= htmlspecialchars(ltrim($document['original_file_path'], '/'), ENT_QUOTES, 'UTF-8'); ?>"
                download
                class="inline-flex items-center justify-center gap-2 rounded-md border border-outline-variant bg-white px-4 py-2.5 font-label-md text-label-md font-semibold text-primary transition-colors hover:bg-surface-container-low">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">download</span>
                Original
              </a>
            <?php endif; ?>
            <?php if (($document['status'] ?? '') === 'COMPLETED' && !empty($document['final_file_path'])): ?>
              <a
                href="/Tandain/<?= htmlspecialchars(ltrim($document['final_file_path'], '/'), ENT_QUOTES, 'UTF-8'); ?>"
                download
                class="inline-flex items-center justify-center gap-2 rounded-md bg-[#15803d] px-4 py-2.5 font-label-md text-label-md font-semibold text-white shadow-sm transition-colors hover:bg-[#166534]">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">download</span>
                Signed
              </a>
            <?php else: ?>
              <div class="inline-flex items-center justify-center gap-2 rounded-md bg-[#7b8f82] px-4 py-2.5 font-label-md text-label-md font-semibold text-white shadow-sm cursor-not-allowed" aria-disabled="true">
                <span class=" material-symbols-outlined text-[18px]" aria-hidden="true">download</span>
                Signed
              </div>
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
              <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-outline-variant bg-surface-container-low px-3 py-2.5">
                <div class="min-w-0">
                  <p class="truncate font-body-md text-body-md font-medium text-primary"><?= htmlspecialchars($signer['full_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <p class="font-body-sm text-body-sm text-on-surface-variant"><?= htmlspecialchars($signer['position'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                  <p class="font-body-sm text-body-sm text-on-surface-variant"><?= htmlspecialchars($signer['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <?php if ($signer['status'] === 'SIGNED' && $signedTimestamp !== false): ?>
                    <p class="mt-1 inline-flex items-center gap-1 font-body-sm text-body-sm text-on-surface-variant">
                      <span class="material-symbols-outlined text-[14px]" aria-hidden="true">schedule</span>
                      Ditandatangani <?= date('d M Y, H:i', $signedTimestamp); ?> WIB
                    </p>
                  <?php endif; ?>
                  <?php if (!empty($signer['rejection_reason'])): ?>
                    <p class="mt-1 font-body-sm text-body-sm text-error">Alasan: <?= htmlspecialchars($signer['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <?php endif; ?>
                </div>

                <?php
                $signerStatus = [
                  'WAITING' => ['Menunggu giliran', 'hourglass_top', 'bg-[#fef3c7] text-[#92400e]'],
                  'CURRENT_TURN' => ['Dalam peninjauan', 'sync', 'bg-[#dbeafe] text-[#1d4ed8]'],
                  'SIGNED' => ['Signed', 'check_circle', 'bg-[#dcfce7] text-[#15803d]'],
                  'REJECTED' => ['Ditolak', 'cancel', 'bg-[#fee2e2] text-[#b91c1c]'],
                  'CANCELLED' => ['Dibatalkan', 'cancel', 'bg-surface-container text-on-surface-variant'],
                ][$signer['status']] ?? null;
                ?>
                <?php if ($signerStatus !== null): ?>
                  <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-label-sm font-label-sm <?= $signerStatus[2]; ?>">
                    <span class="material-symbols-outlined text-[14px]" aria-hidden="true"><?= $signerStatus[1]; ?></span>
                    <span><?= htmlspecialchars($signerStatus[0], ENT_QUOTES, 'UTF-8'); ?></span>
                  </span>
                <?php endif; ?>
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
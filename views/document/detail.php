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

  <main class="flex-1 p-8 w-full flex flex-col gap-6">

    <div>
      <a href="/Tandain/document" class="inline-flex items-center gap-1 text-secondary font-label-md text-label-md hover:underline">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        <span>Kembali ke daftar</span>
      </a>
      <h1 class="text-2xl font-bold text-primary tracking-tight font-headline-lg mt-2"><?= htmlspecialchars($document['title']); ?></h1>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1">
        Diajukan pada <?= $tanggal; ?>
        <span class="ml-2 inline-flex items-center px-2.5 py-1 rounded-full text-label-sm font-label-sm <?= $badge; ?>"><?= $label; ?></span>
      </p>
    </div>

    <?php if ($document['status'] === 'REJECTED' && !empty($document['rejection_reason'])): ?>
      <div class="bg-error-container text-on-error-container rounded-xl p-4 font-body-md text-body-md">
        <strong>Dokumen ditolak.</strong> <?= htmlspecialchars($document['rejection_reason']); ?>
      </div>
    <?php endif; ?>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 flex flex-col gap-4">
      <div>
        <h2 class="font-headline-sm text-headline-sm text-primary">Status Dokumen</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Urutan penanda tangan dan status masing-masing.</p>
      </div>

      <div class="flex flex-col">
        <?php if (count($signers) === 0): ?>
          <p class="py-6 text-center text-on-surface-variant font-body-md text-body-md">Belum ada penanda tangan untuk dokumen ini.</p>
        <?php endif; ?>

        <?php foreach ($signers as $signer): ?>
          <?php
          if ($signer['status'] === 'SIGNED') {
              $signerLabel = 'Sudah tanda tangan';
              $signerBadge = 'bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]';
          } elseif ($signer['status'] === 'CURRENT_TURN') {
              $signerLabel = 'Giliran tanda tangan';
              $signerBadge = 'bg-[#dbeafe] text-[#1d4ed8] border border-[#bfdbfe]';
          } elseif ($signer['status'] === 'REJECTED') {
              $signerLabel = 'Menolak';
              $signerBadge = 'bg-[#FFDAD6] text-[#BA1A1A] border border-[#FFC5C5]';
          } elseif ($signer['status'] === 'CANCELLED') {
              $signerLabel = 'Dibatalkan';
              $signerBadge = 'bg-surface-container text-on-surface-variant border border-outline-variant';
          } else {
              $signerLabel = 'Menunggu';
              $signerBadge = 'bg-amber-100 text-amber-700 border border-amber-200';
          }
          ?>
          <div class="flex items-center justify-between gap-3 py-3 border-b border-surface-container">
            <div>
              <p class="font-semibold text-primary font-body-md text-body-md"><?= htmlspecialchars($signer['full_name']); ?></p>
              <p class="font-body-sm text-body-sm text-on-surface-variant"><?= htmlspecialchars($signer['position'] ?? ''); ?></p>
              <p class="font-body-sm text-body-sm text-on-surface-variant"><?= htmlspecialchars($signer['email']); ?></p>
              <?php if (!empty($signer['rejection_reason'])): ?>
                <p class="font-body-sm text-body-sm text-error mt-1">Alasan: <?= htmlspecialchars($signer['rejection_reason']); ?></p>
              <?php endif; ?>
            </div>
            <div class="text-right">
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-label-sm font-label-sm <?= $signerBadge; ?>"><?= $signerLabel; ?></span>
              <?php if (!empty($signer['signed_at'])): ?>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1"><?= date('d', strtotime($signer['signed_at'])) . ' ' . $bulan[date('n', strtotime($signer['signed_at'])) - 1] . ' ' . date('Y', strtotime($signer['signed_at'])); ?></p>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div>
        <div class="flex justify-between items-center mb-1.5">
          <span class="font-label-sm text-[11px] font-semibold text-on-surface-variant"><?= $signedCount; ?>/<?= $totalSigners; ?> tanda tangan</span>
          <span class="font-code-tabular text-[11px] font-bold text-on-surface-variant"><?= $percent; ?>%</span>
        </div>
        <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
          <div class="bg-secondary-container h-2 rounded-full" style="width: <?= $percent; ?>%"></div>
        </div>
      </div>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 flex flex-col gap-4">
      <div>
        <h2 class="font-headline-sm text-headline-sm text-primary">Tinjau Dokumen</h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Pratinjau dokumen yang diajukan.</p>
      </div>

      <iframe title="Pratinjau dokumen" src="/Tandain/<?= htmlspecialchars($previewFile); ?>" class="w-full h-[560px] border border-outline-variant rounded-xl bg-surface-container-low"></iframe>

      <div class="flex items-center gap-2 flex-wrap">
        <a href="/Tandain/<?= htmlspecialchars($document['original_file_path']); ?>" download class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-[12px] font-medium transition-colors">
          <span class="material-symbols-outlined text-[15px]">download</span>
          <span>Original</span>
        </a>
        <?php if ($document['status'] === 'COMPLETED' && !empty($document['final_file_path'])): ?>
          <a href="/Tandain/<?= htmlspecialchars($document['final_file_path']); ?>" download class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-[#15803d] text-white hover:bg-[#003865] font-label-md text-[12px] font-medium shadow-sm transition-all">
            <span class="material-symbols-outlined text-[15px]">download</span>
            <span>Signed</span>
          </a>
        <?php endif; ?>
      </div>
    </div>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

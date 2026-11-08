<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
date_default_timezone_set('Asia/Jakarta');

$bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

$activeTab = 'font-semibold bg-surface-container-lowest text-primary shadow-sm';
$inactiveTab = 'font-medium text-on-surface-variant hover:text-primary';

$first = 0;

if ($total > 0) {
    $first = ($page - 1) * $perPage + 1;
}

$last = min($total, $page * $perPage);

$searchUrl = urlencode($search);
$statusUrl = '';

if ($filter !== 'all') {
    $statusUrl = '&status=' . urlencode($filter);
}
?>

  <main class="flex-1 p-8 w-full flex flex-col gap-6">

    <div class="mb-6">
      <h1 class="text-2xl font-bold text-primary tracking-tight font-headline-lg">Dokumen Diunggah</h1>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1">Daftar berkas yang telah Anda ajukan untuk proses penandatanganan elektronik</p>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-none overflow-hidden">

      <div class="p-5 border-b border-surface-container-high flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-surface-container-lowest">
        <form method="get" action="/Tandain/document" class="relative flex-1 max-w-md">
          <?php if ($filter !== 'all'): ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($filter); ?>">
          <?php endif; ?>
          <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-on-surface-variant">
            <span class="material-symbols-outlined text-[20px]">search</span>
          </span>
          <input class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all" name="q" value="<?= htmlspecialchars($search); ?>" placeholder="Cari judul atau nomor dokumen..." type="text">
        </form>

        <div class="flex items-center gap-3 flex-wrap">
          <div class="flex items-center gap-1 p-1 bg-surface-container rounded-lg">
            <a href="/Tandain/document?q=<?= $searchUrl; ?>" class="px-3.5 py-1.5 rounded-md font-label-md text-label-md transition-colors <?= $filter === 'all' ? $activeTab : $inactiveTab; ?>">
              Semua (<?= $counts['total_all']; ?>)
            </a>
            <a href="/Tandain/document?status=completed&q=<?= $searchUrl; ?>" class="px-3.5 py-1.5 rounded-md font-label-md text-label-md transition-colors <?= $filter === 'completed' ? $activeTab : $inactiveTab; ?>">
              Selesai (<?= $counts['total_completed']; ?>)
            </a>
            <a href="/Tandain/document?status=in_progress&q=<?= $searchUrl; ?>" class="px-3.5 py-1.5 rounded-md font-label-md text-label-md transition-colors <?= $filter === 'in_progress' ? $activeTab : $inactiveTab; ?>">
              Sedang Diproses (<?= $counts['total_in_progress']; ?>)
            </a>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant h-11">
              <th class="py-3 px-5 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider w-14 text-center">No</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Judul Dokumen</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Tanggal Pengajuan</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider min-w-[190px]">Progres TTE</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-center">Status</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-right min-w-[260px]">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-surface-container font-body-md text-body-md">

            <?php if (count($documents) === 0): ?>
              <tr>
                <td colspan="6" class="py-12 px-6 text-center text-on-surface-variant">
                  Belum ada dokumen yang cocok. Ubah kata kunci atau pilih filter lain.
                </td>
              </tr>
            <?php endif; ?>

            <?php foreach ($documents as $i => $doc): ?>
              <?php
              $time = strtotime($doc['created_at']);
              $tanggal = date('d', $time) . ' ' . $bulan[date('n', $time) - 1] . ' ' . date('Y', $time);
              $jam = date('H:i', $time) . ' WIB';

              $signed = (int) $doc['signed_count'];
              $totalSigner = (int) $doc['total_signers'];
              $percent = 0;

              if ($totalSigner > 0) {
                  $percent = (int) (($signed / $totalSigner) * 100);
              }

              if ($totalSigner > 0 && $signed === $totalSigner) {
                  $progressText = $signed . '/' . $totalSigner . ' Selesai';
              } else {
                  $progressText = $signed . '/' . $totalSigner . ' Signer';
              }

              if ($doc['status'] === 'COMPLETED') {
                  $label = 'Selesai';
                  $icon = 'check';
                  $badge = 'bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]';
                  $textColor = 'text-[#15803d]';
                  $barColor = 'bg-[#15803d]';
              } elseif ($doc['status'] === 'IN_PROGRESS') {
                  $label = 'Diproses';
                  $icon = 'sync';
                  $badge = 'bg-[#dbeafe] text-[#1d4ed8] border border-[#bfdbfe]';
                  $textColor = 'text-[#1d4ed8]';
                  $barColor = 'bg-secondary-container';
              } elseif ($doc['status'] === 'REJECTED') {
                  $label = 'Ditolak';
                  $icon = 'cancel';
                  $badge = 'bg-[#FFDAD6] text-[#BA1A1A] border border-[#FFC5C5]';
                  $textColor = 'text-[#BA1A1A]';
                  $barColor = 'bg-amber-400';
              } elseif ($doc['status'] === 'PENDING_TRIAGE') {
                  $label = 'Menunggu Disposisi';
                  $icon = 'hourglass_top';
                  $badge = 'bg-amber-100 text-amber-700 border border-amber-200';
                  $textColor = 'text-amber-700';
                  $barColor = 'bg-amber-400';
              } else {
                  $label = ($doc['status'] === 'DRAFT') ? 'Draft' : 'Dibatalkan';
                  $icon = 'block';
                  $badge = 'bg-surface-container text-on-surface-variant border border-outline-variant';
                  $textColor = 'text-on-surface-variant';
                  $barColor = 'bg-outline';
              }

              $detailUrl = '/Tandain/document/detail?id=' . urlencode($doc['id']);
              ?>
            <tr class="hover:bg-surface-container-low/50 transition-colors group">
              <td class="py-4 px-5 text-center font-code-tabular text-code-tabular text-on-surface-variant"><?= $first + $i; ?></td>
              <td class="py-4 px-6">
                <a href="<?= $detailUrl; ?>" class="block font-headline-sm text-[15px] font-semibold text-primary hover:text-secondary transition-colors leading-tight">
                  <?= htmlspecialchars($doc['title']); ?>
                </a>
              </td>
              <td class="py-4 px-6 font-code-tabular text-code-tabular text-on-surface-variant whitespace-nowrap">
                <div><?= $tanggal; ?></div>
                <div class="text-[11px] text-outline"><?= $jam; ?></div>
              </td>
              <td class="py-4 px-6">
                <div class="w-full max-w-[160px]">
                  <div class="flex justify-between items-center mb-1.5">
                    <span class="font-label-sm text-[11px] font-semibold <?= $textColor; ?>"><?= $progressText; ?></span>
                    <span class="font-code-tabular text-[11px] font-bold <?= $textColor; ?>"><?= $percent; ?>%</span>
                  </div>
                  <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                    <div class="<?= $barColor; ?> h-2 rounded-full" style="width: <?= $percent; ?>%"></div>
                  </div>
                </div>
              </td>
              <td class="py-4 px-6 text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm <?= $badge; ?>">
                  <span class="material-symbols-outlined text-[14px]"><?= $icon; ?></span>
                  <span><?= $label; ?></span>
                </span>
              </td>
              <td class="py-4 px-6 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <a href="<?= $detailUrl; ?>" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-[12px] font-medium transition-colors">
                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                    <span>Detail</span>
                  </a>
                  <a href="/Tandain/<?= htmlspecialchars($doc['original_file_path']); ?>" download class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-[12px] font-medium transition-colors">
                    <span class="material-symbols-outlined text-[15px]">download</span>
                    <span>Original</span>
                  </a>
                  <?php if ($doc['status'] === 'COMPLETED' && !empty($doc['final_file_path'])): ?>
                    <a href="/Tandain/<?= htmlspecialchars($doc['final_file_path']); ?>" download class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#15803d] text-white hover:bg-[#003865] font-label-md text-[12px] font-medium shadow-sm transition-all">
                      <span class="material-symbols-outlined text-[15px]">download</span>
                      <span>Signed</span>
                    </a>
                  <?php else: ?>
                    <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-surface-container text-outline cursor-not-allowed font-label-md text-[12px] font-medium" disabled title="Dokumen belum selesai ditandatangani semua pihak">
                      <span class="material-symbols-outlined text-[15px]">download</span>
                      <span>Signed</span>
                    </button>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>

          </tbody>
        </table>
      </div>

      <div class="p-4 border-t border-surface-container-high bg-surface-container-lowest flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="font-body-md text-body-md text-on-surface-variant">
          Menampilkan <span class="font-semibold text-primary"><?= $first; ?>-<?= $last; ?></span> dari <span class="font-semibold text-primary"><?= $total; ?></span> dokumen
        </div>

        <div class="flex items-center gap-2">
          <?php if ($page > 1): ?>
            <a href="/Tandain/document?page=<?= $page - 1; ?>&q=<?= $searchUrl; ?><?= $statusUrl; ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-primary hover:bg-surface-container-low font-label-md text-label-md font-semibold transition-colors">
              <span class="material-symbols-outlined text-[18px]">chevron_left</span>
              <span>Sebelumnya</span>
            </a>
          <?php else: ?>
            <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-outline font-label-md text-label-md opacity-50 pointer-events-none" disabled>
              <span class="material-symbols-outlined text-[18px]">chevron_left</span>
              <span>Sebelumnya</span>
            </button>
          <?php endif; ?>

          <div class="flex items-center gap-1">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <?php if ($p === $page): ?>
                <span class="w-8 h-8 rounded-lg bg-primary-container text-on-primary font-label-md text-label-md font-bold flex items-center justify-center"><?= $p; ?></span>
              <?php else: ?>
                <a href="/Tandain/document?page=<?= $p; ?>&q=<?= $searchUrl; ?><?= $statusUrl; ?>" class="w-8 h-8 rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md flex items-center justify-center transition-colors"><?= $p; ?></a>
              <?php endif; ?>
            <?php endfor; ?>
          </div>

          <?php if ($page < $totalPages): ?>
            <a href="/Tandain/document?page=<?= $page + 1; ?>&q=<?= $searchUrl; ?><?= $statusUrl; ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-primary hover:bg-surface-container-low font-label-md text-label-md font-semibold transition-colors">
              <span>Selanjutnya</span>
              <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </a>
          <?php else: ?>
            <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-outline font-label-md text-label-md opacity-50 pointer-events-none" disabled>
              <span>Selanjutnya</span>
              <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </button>
          <?php endif; ?>
        </div>
      </div>

    </div>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

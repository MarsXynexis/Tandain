<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
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

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <div>
      <h1 class="text-headline-lg font-bold text-primary tracking-tight font-headline-lg">Dokumen Diunggah</h1>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1">Daftar berkas yang telah Anda ajukan untuk proses penandatanganan elektronik</p>
    </div>

    <?php require 'views/layouts/flash.php'; ?>

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

        <div class="min-w-0 max-w-full">
          <div class="flex max-w-full items-center gap-1 overflow-x-auto rounded-xl bg-surface-container p-1">
            <a href="/Tandain/document?q=<?= $searchUrl; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $filter === 'all' ? $activeTab : $inactiveTab; ?>">
              Semua (<?= $counts['total_all']; ?>)
            </a>
            <a href="/Tandain/document?status=completed&q=<?= $searchUrl; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $filter === 'completed' ? $activeTab : $inactiveTab; ?>">
              Selesai (<?= $counts['total_completed']; ?>)
            </a>
            <a href="/Tandain/document?status=in_progress&q=<?= $searchUrl; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $filter === 'in_progress' ? $activeTab : $inactiveTab; ?>">
              Sedang Diproses (<?= $counts['total_in_progress']; ?>)
            </a>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="data-table min-w-[740px]">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant h-11">
              <th scope="col" class="w-14 text-center">No</th>
              <th scope="col">Judul Dokumen</th>
              <th scope="col">Tanggal Pengajuan</th>
              <th scope="col" class="min-w-[190px]">Progres TTE</th>
              <th scope="col" class="text-center">Status</th>
              <th scope="col" class="text-right">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-surface-container">

            <?php if (count($documents) === 0): ?>
              <tr>
                <td colspan="6" class="table-empty text-center text-on-surface-variant">
                  Belum ada dokumen yang cocok. Ubah kata kunci atau pilih filter lain.
                </td>
              </tr>
            <?php endif; ?>

            <?php foreach ($documents as $i => $doc): ?>
              <?php
              $signed = (int) $doc['signed_count'];
              $totalSigner = (int) $doc['total_signers'];
              $percent = 0;

              if ($totalSigner > 0) {
                  $percent = (int) (($signed / $totalSigner) * 100);
              }

              $progressText = $signed . '/' . $totalSigner . ' tanda tangan';

              if ($doc['status'] === 'COMPLETED') {
                  $textColor = 'text-[#15803d]';
                  $barColor = 'bg-[#15803d]';
              } elseif ($doc['status'] === 'IN_PROGRESS') {
                  $textColor = 'text-[#1d4ed8]';
                  $barColor = 'bg-brand';
              } elseif ($doc['status'] === 'REJECTED') {
                  $textColor = 'text-[#BA1A1A]';
                  $barColor = 'bg-amber-400';
              } elseif ($doc['status'] === 'PENDING_TRIAGE') {
                  $textColor = 'text-amber-700';
                  $barColor = 'bg-amber-400';
              } else {
                  $textColor = 'text-on-surface-variant';
                  $barColor = 'bg-outline';
              }

              $detailUrl = '/Tandain/document/detail?id=' . urlencode($doc['id']);
              ?>
            <tr class="hover:bg-surface-container-low/50 transition-colors group">
              <td class="text-center font-code-tabular text-code-tabular text-on-surface-variant"><?= $first + $i; ?></td>
              <td>
                <a href="<?= $detailUrl; ?>" class="block table-title text-primary hover:text-secondary transition-colors leading-tight">
                  <?= htmlspecialchars($doc['title']); ?>
                </a>
              </td>
              <td class="table-date text-on-surface-variant">
                <div><?= formatTanggalIndo($doc['created_at']); ?></div>
                <div class="table-meta"><?= formatTanggalIndo($doc['created_at'], 'time'); ?></div>
              </td>
              <td>
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
              <td class="text-center">
                <?php renderStatusBadge($doc['status']); ?>
              </td>
              <td class="text-right whitespace-nowrap">
                <?php if ($doc['status'] === 'DRAFT' && $totalSigner > 0): ?>
                  <a href="/Tandain/document/position/<?= rawurlencode($doc['id']); ?>" class="btn-sm inline-flex gap-1.5 bg-brand text-white transition-colors hover:bg-brand-hover">
                    <span class="material-symbols-outlined text-[15px]" aria-hidden="true">draw</span>
                    <span>Atur Posisi TTE</span>
                  </a>
                <?php else: ?>
                  <a href="<?= $detailUrl; ?>" class="btn-sm inline-flex gap-1.5 border border-outline-variant font-medium text-on-surface transition-colors hover:bg-surface-container-low">
                    <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                    <span>Lihat Detail</span>
                  </a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>

          </tbody>
        </table>
      </div>

      <?php if ($total > 0): ?>
        <div class="table-footer flex flex-col items-start justify-between gap-3 border-t border-surface-container-high bg-white sm:flex-row sm:items-center">
          <p class="text-on-surface-variant">
            <?php if ($totalPages > 1): ?>
              Menampilkan <strong class="text-primary"><?= $first; ?>-<?= $last; ?></strong> dari
            <?php endif; ?>
            <strong class="text-primary"><?= $total; ?></strong> dokumen
          </p>

          <?php if ($totalPages > 1): ?>
            <nav class="flex min-w-0 max-w-full items-center gap-2" aria-label="Halaman dokumen">
              <?php if ($page > 1): ?>
                <a href="/Tandain/document?page=<?= $page - 1; ?>&q=<?= $searchUrl; ?><?= $statusUrl; ?>" aria-label="Halaman sebelumnya" class="btn-sm inline-flex shrink-0 items-center gap-1 border border-outline-variant bg-white font-semibold text-primary transition-colors hover:bg-surface-container-low">
                  <span class="material-symbols-outlined text-[18px]" aria-hidden="true">chevron_left</span>
                  <span class="hidden sm:inline">Sebelumnya</span>
                </a>
              <?php else: ?>
                <button type="button" class="btn-sm inline-flex shrink-0 items-center gap-1 border border-outline-variant bg-white text-outline opacity-50" aria-label="Halaman sebelumnya" disabled>
                  <span class="material-symbols-outlined text-[18px]" aria-hidden="true">chevron_left</span>
                  <span class="hidden sm:inline">Sebelumnya</span>
                </button>
              <?php endif; ?>

              <div class="flex min-w-0 items-center gap-1 overflow-x-auto p-1">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                  <?php if ($p === $page): ?>
                    <span aria-current="page" class="btn-sm btn-icon flex shrink-0 bg-primary-container font-semibold text-on-primary"><?= $p; ?></span>
                  <?php else: ?>
                    <a href="/Tandain/document?page=<?= $p; ?>&q=<?= $searchUrl; ?><?= $statusUrl; ?>" class="btn-sm btn-icon flex shrink-0 text-on-surface-variant transition-colors hover:bg-surface-container-low"><?= $p; ?></a>
                  <?php endif; ?>
                <?php endfor; ?>
              </div>

              <?php if ($page < $totalPages): ?>
                <a href="/Tandain/document?page=<?= $page + 1; ?>&q=<?= $searchUrl; ?><?= $statusUrl; ?>" aria-label="Halaman berikutnya" class="btn-sm inline-flex shrink-0 items-center gap-1 border border-outline-variant bg-white font-semibold text-primary transition-colors hover:bg-surface-container-low">
                  <span class="hidden sm:inline">Selanjutnya</span>
                  <span class="material-symbols-outlined text-[18px]" aria-hidden="true">chevron_right</span>
                </a>
              <?php else: ?>
                <button type="button" class="btn-sm inline-flex shrink-0 items-center gap-1 border border-outline-variant bg-white text-outline opacity-50" aria-label="Halaman berikutnya" disabled>
                  <span class="hidden sm:inline">Selanjutnya</span>
                  <span class="material-symbols-outlined text-[18px]" aria-hidden="true">chevron_right</span>
                </button>
              <?php endif; ?>
            </nav>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    </div>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

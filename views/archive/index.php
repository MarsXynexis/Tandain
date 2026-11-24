<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$statusQuery = '';

if (!empty($data['search'])) {
    $statusQuery = '&search=' . urlencode($data['search']);
}

if (!empty($data['search'])) {
    $emptyMessage = 'Tidak ada dokumen arsip yang cocok dengan kata kunci pencarian.';
} elseif ($data['current_status'] === 'COMPLETED') {
    $emptyMessage = 'Belum ada dokumen yang selesai.';
} elseif ($data['current_status'] === 'REJECTED') {
    $emptyMessage = 'Belum ada dokumen yang ditolak.';
} elseif ($data['current_status'] === 'CANCELLED') {
    $emptyMessage = 'Belum ada dokumen yang dibatalkan.';
} else {
    $emptyMessage = 'Belum ada dokumen dalam arsip.';
}
?>

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Arsip Dokumen</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Dokumen yang telah selesai, ditolak, atau dibatalkan beserta riwayat disposisinya</p>
      </div>
    </div>

    <section class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-none overflow-hidden flex flex-col">

      <div class="p-5 border-b border-outline-variant flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-surface-container-lowest">

        <form method="GET" action="/Tandain/archive" class="flex items-center gap-2 flex-1 max-w-lg">
          <input type="hidden" name="status" value="<?= htmlspecialchars($data['current_status'], ENT_QUOTES, 'UTF-8'); ?>">

          <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-on-surface-variant">
              <span class="material-symbols-outlined text-[20px]">search</span>
            </span>
            <input name="search" value="<?= htmlspecialchars($data['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all" placeholder="Cari judul, nomor surat, nama, atau NIM..." type="text">
          </div>

          <button type="submit" class="btn-md inline-flex items-center gap-1.5 bg-surface-container-low hover:bg-surface-container text-primary border border-outline-variant font-semibold transition-colors shrink-0">
            <span class="material-symbols-outlined text-[18px]">search</span>
            <span>Cari</span>
          </button>

          <?php if (!empty($data['search'])): ?>
            <a href="/Tandain/archive?status=<?= urlencode($data['current_status']); ?>" class="px-2.5 py-2 text-on-surface-variant hover:text-primary font-label-md text-label-md transition-colors shrink-0">
              Reset
            </a>
          <?php endif; ?>
        </form>

        <div class="flex items-center gap-1 p-1 bg-surface-container rounded-lg self-start lg:self-auto max-w-full overflow-x-auto">
          <a href="/Tandain/archive?status=COMPLETED<?= $statusQuery; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $data['current_status'] === 'COMPLETED' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Selesai (<?= htmlspecialchars((string)($data['stats']['completed_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>

          <a href="/Tandain/archive?status=REJECTED<?= $statusQuery; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $data['current_status'] === 'REJECTED' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Ditolak (<?= htmlspecialchars((string)($data['stats']['rejected_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>

          <a href="/Tandain/archive?status=CANCELLED<?= $statusQuery; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $data['current_status'] === 'CANCELLED' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Dibatalkan (<?= htmlspecialchars((string)($data['stats']['cancelled_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>

          <a href="/Tandain/archive?status=all<?= $statusQuery; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $data['current_status'] === 'all' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Semua (<?= htmlspecialchars((string)($data['stats']['total_archived'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>
        </div>

      </div>

      <div class="overflow-x-auto">
        <table class="data-table min-w-[760px]">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant h-11">
              <th scope="col" class="w-14 text-center">No</th>
              <th scope="col">Judul Dokumen</th>
              <th scope="col">Pemohon</th>
              <th scope="col">Tanggal Arsip</th>
              <th scope="col" class="text-center">Status</th>
              <th scope="col" class="text-right min-w-[150px]">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-surface-container">
            <?php if (empty($data['documents'])): ?>
              <tr>
                <td colspan="6" class="table-empty text-center text-on-surface-variant">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <div class="w-12 h-12 rounded-full bg-surface-container text-on-surface-variant flex items-center justify-center mb-1">
                      <span class="material-symbols-outlined text-[26px]">folder_open</span>
                    </div>
                    <p class="font-headline-sm text-sm font-semibold text-primary">Arsip Belum Tersedia</p>
                    <p class="font-body-sm text-xs text-on-surface-variant"><?= htmlspecialchars($emptyMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; ?>
              <?php foreach ($data['documents'] as $doc): ?>
                <?php $archiveDate = !empty($doc['completed_at']) ? $doc['completed_at'] : $doc['updated_at']; ?>
                <tr class="hover:bg-surface-container-low/50 transition-colors group">
                  <td class="text-center font-code-tabular text-code-tabular text-on-surface-variant"><?= $no++; ?></td>

                  <td>
                    <a href="/Tandain/triage/review/<?= $doc['id']; ?>" class="table-title text-primary hover:text-secondary transition-colors leading-tight inline-block">
                      <?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <?php if (!empty($doc['document_number'])): ?>
                      <div class="table-meta font-code-tabular mt-0.5">
                        <?= htmlspecialchars($doc['document_number'], ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    <?php endif; ?>

                    <?php if ($doc['status'] === 'REJECTED' && !empty($doc['rejection_reason'])): ?>
                      <div class="text-[11px] text-[#b91c1c] mt-1 max-w-[320px] truncate" title="<?= htmlspecialchars($doc['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?>">
                        Alasan: <?= htmlspecialchars($doc['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    <?php elseif ($doc['status'] === 'CANCELLED' && !empty($doc['rejection_reason'])): ?>
                      <div class="text-[11px] text-on-surface-variant mt-1 max-w-[320px] truncate" title="<?= htmlspecialchars($doc['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?>">
                        Alasan: <?= htmlspecialchars($doc['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    <?php endif; ?>
                  </td>

                  <td>
                    <div class="text-sm font-medium text-primary">
                      <?= htmlspecialchars($doc['requester_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <div class="table-meta font-code-tabular mt-0.5">
                      NIM/NIP: <?= htmlspecialchars($doc['requester_nim'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                  </td>

                  <td class="table-date text-on-surface-variant">
                    <div><?= formatTanggalIndo($archiveDate); ?></div>
                    <div class="table-meta"><?= formatTanggalIndo($archiveDate, 'time'); ?></div>
                  </td>

                  <td class="text-center whitespace-nowrap">
                    <?php renderStatusBadge($doc['status']); ?>
                  </td>

                  <td class="text-right whitespace-nowrap">
                    <a href="/Tandain/triage/review/<?= $doc['id']; ?>" class="btn-sm inline-flex items-center gap-1.5 border border-outline-variant text-on-surface hover:bg-surface-container-low transition-colors">
                      <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                      <span>Lihat Detail</span>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="table-footer bg-surface-container-low border-t border-outline-variant flex items-center justify-between">
        <span class="text-xs text-on-surface-variant">
          Menampilkan <strong class="text-primary font-semibold"><?= count($data['documents']); ?></strong> dokumen arsip
        </span>
      </div>

    </section>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

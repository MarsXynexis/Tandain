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

  <main class="flex-1 w-full p-8 flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Arsip Dokumen</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Dokumen yang telah selesai, ditolak, atau dibatalkan beserta riwayat disposisinya</p>
      </div>
    </div>

    <section class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-none overflow-hidden flex flex-col">

      <div class="p-5 border-b border-outline-variant flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-surface-container-lowest">

        <form method="GET" action="/Tandain/archive" class="relative flex-1 max-w-md">
          <input type="hidden" name="status" value="<?= htmlspecialchars($data['current_status'], ENT_QUOTES, 'UTF-8'); ?>">
          <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-on-surface-variant">
            <span class="material-symbols-outlined text-[20px]">search</span>
          </span>
          <input name="search" value="<?= htmlspecialchars($data['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all" placeholder="Cari judul, nomor surat, nama, atau NIM..." type="text">
        </form>

        <div class="flex items-center gap-1 p-1 bg-surface-container rounded-lg self-start lg:self-auto overflow-x-auto">
          <a href="/Tandain/archive?status=COMPLETED<?= $statusQuery; ?>" class="px-3.5 py-1.5 rounded-md font-label-md text-label-md whitespace-nowrap transition-colors <?= $data['current_status'] === 'COMPLETED' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Selesai (<?= htmlspecialchars((string)($data['stats']['completed_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>

          <a href="/Tandain/archive?status=REJECTED<?= $statusQuery; ?>" class="px-3.5 py-1.5 rounded-md font-label-md text-label-md whitespace-nowrap transition-colors <?= $data['current_status'] === 'REJECTED' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Ditolak (<?= htmlspecialchars((string)($data['stats']['rejected_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>

          <a href="/Tandain/archive?status=CANCELLED<?= $statusQuery; ?>" class="px-3.5 py-1.5 rounded-md font-label-md text-label-md whitespace-nowrap transition-colors <?= $data['current_status'] === 'CANCELLED' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Dibatalkan (<?= htmlspecialchars((string)($data['stats']['cancelled_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>

          <a href="/Tandain/archive?status=all<?= $statusQuery; ?>" class="px-3.5 py-1.5 rounded-md font-label-md text-label-md whitespace-nowrap transition-colors <?= $data['current_status'] === 'all' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Semua (<?= htmlspecialchars((string)($data['stats']['total_archived'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>
        </div>

      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant h-11">
              <th class="py-3 px-5 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider w-14 text-center">No</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Judul Dokumen</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Pemohon</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Tanggal Arsip</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-center">Status</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-right min-w-[150px]">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-surface-container font-body-md text-body-md">
            <?php if (empty($data['documents'])): ?>
              <tr>
                <td colspan="6" class="py-8 text-center text-on-surface-variant">
                  <?= htmlspecialchars($emptyMessage, ENT_QUOTES, 'UTF-8'); ?>
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; ?>
              <?php foreach ($data['documents'] as $doc): ?>
                <?php $archiveDate = !empty($doc['completed_at']) ? $doc['completed_at'] : $doc['updated_at']; ?>
                <tr class="hover:bg-surface-container-low/50 transition-colors group">
                  <td class="py-4 px-5 text-center font-code-tabular text-code-tabular text-on-surface-variant"><?= $no++; ?></td>

                  <td class="py-4 px-6">
                    <a href="/Tandain/triage/review/<?= $doc['id']; ?>" class="font-headline-sm text-[15px] font-semibold text-primary hover:text-secondary transition-colors leading-tight inline-block">
                      <?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <?php if (!empty($doc['document_number'])): ?>
                      <div class="font-code-tabular text-[11px] text-outline mt-0.5">
                        <?= htmlspecialchars($doc['document_number'], ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    <?php endif; ?>

                    <?php if ($doc['status'] === 'REJECTED' && !empty($doc['rejection_reason'])): ?>
                      <div class="text-[11px] text-[#b91c1c] mt-1 max-w-[320px] truncate" title="<?= htmlspecialchars($doc['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?>">
                        Alasan: <?= htmlspecialchars($doc['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    <?php endif; ?>
                  </td>

                  <td class="py-4 px-6">
                    <div class="font-headline-sm text-[15px] font-semibold text-primary leading-tight">
                      <?= htmlspecialchars($doc['requester_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <div class="font-code-tabular text-[11px] text-outline mt-0.5">
                      NIM/NIP: <?= htmlspecialchars($doc['requester_nim'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                  </td>

                  <td class="py-4 px-6 font-code-tabular text-code-tabular text-on-surface-variant whitespace-nowrap">
                    <div><?= date('d M Y', strtotime($archiveDate)); ?></div>
                    <div class="text-[11px] text-outline"><?= date('H:i', strtotime($archiveDate)); ?> WIB</div>
                  </td>

                  <td class="py-4 px-6 text-center whitespace-nowrap">
                    <?php if ($doc['status'] === 'COMPLETED'): ?>
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]">
                        <span class="material-symbols-outlined text-[14px]">check</span>
                        <span>Dokumen Selesai</span>
                      </span>
                    <?php elseif ($doc['status'] === 'REJECTED'): ?>
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-[#fee2e2] text-[#b91c1c] border border-[#fecaca]">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                        <span>Ditolak</span>
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-surface-container text-on-surface-variant border border-outline-variant">
                        <span class="material-symbols-outlined text-[14px]">block</span>
                        <span>Dibatalkan</span>
                      </span>
                    <?php endif; ?>
                  </td>

                  <td class="py-4 px-6 text-right whitespace-nowrap">
                    <a href="/Tandain/triage/review/<?= $doc['id']; ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-outline-variant font-label-md text-label-md text-on-surface hover:bg-surface-container-low transition-colors">
                      <span class="material-symbols-outlined text-[16px]">visibility</span>
                      <span>Lihat Rincian</span>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="p-4 bg-surface-container-low border-t border-outline-variant flex items-center justify-between">
        <span class="font-body-sm text-body-sm text-on-surface-variant">
          Menampilkan <strong class="text-primary font-semibold"><?= count($data['documents']); ?></strong> dokumen arsip
        </span>
      </div>

    </section>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

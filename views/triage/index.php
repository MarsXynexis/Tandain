<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$statusQuery = '';

if (!empty($data['search'])) {
    $statusQuery = '&search=' . urlencode($data['search']);
}

if (!empty($data['search'])) {
    $emptyMessage = 'Tidak ada dokumen yang cocok dengan kata kunci pencarian.';
} elseif ($data['current_status'] === 'pending') {
    $emptyMessage = 'Tidak ada dokumen yang menunggu disposisi.';
} elseif ($data['current_status'] === 'dispatched') {
    $emptyMessage = 'Tidak ada dokumen yang sedang diproses.';
} elseif ($data['current_status'] === 'rejected') {
    $emptyMessage = 'Tidak ada pengajuan dokumen yang ditolak.';
} else {
    $emptyMessage = 'Belum ada dokumen dalam alur disposisi.';
}
?>

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Disposisi Dokumen</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Daftar permohonan surat yang memerlukan penetapan alur penandatanganan</p>
      </div>
    </div>

    <?php require_once 'views/layouts/flash.php'; ?>

    <section class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-none overflow-hidden flex flex-col">

      <div class="p-5 border-b border-surface-container-high flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-surface-container-lowest">

        <form method="GET" action="/Tandain/triage" class="flex items-center gap-2 flex-1 max-w-lg">
          <input type="hidden" name="status" value="<?= htmlspecialchars($data['current_status'], ENT_QUOTES, 'UTF-8'); ?>">

          <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-on-surface-variant">
              <span class="material-symbols-outlined text-[20px]">search</span>
            </span>
            <input name="search" value="<?= htmlspecialchars($data['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all" placeholder="Cari judul dokumen, nama pemohon, atau NIM..." type="text">
          </div>

          <button type="submit" class="btn-md inline-flex items-center gap-1.5 bg-surface-container-low hover:bg-surface-container text-primary border border-outline-variant font-semibold transition-colors shrink-0">
            <span class="material-symbols-outlined text-[18px]">search</span>
            <span>Cari</span>
          </button>

          <?php if (!empty($data['search'])): ?>
            <a href="/Tandain/triage?status=<?= urlencode($data['current_status']); ?>" class="px-2.5 py-2 text-on-surface-variant hover:text-primary font-label-md text-label-md transition-colors shrink-0">
              Reset
            </a>
          <?php endif; ?>
        </form>

        <div class="flex items-center gap-1 p-1 bg-surface-container rounded-lg self-start lg:self-auto max-w-full overflow-x-auto">
          <a href="/Tandain/triage?status=pending<?= $statusQuery; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $data['current_status'] === 'pending' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Menunggu (<?= htmlspecialchars((string)($data['stats']['pending_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>

          <a href="/Tandain/triage?status=dispatched<?= $statusQuery; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $data['current_status'] === 'dispatched' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Diproses (<?= htmlspecialchars((string)($data['stats']['dispatched_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>

          <a href="/Tandain/triage?status=rejected<?= $statusQuery; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $data['current_status'] === 'rejected' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Ditolak (<?= htmlspecialchars((string)($data['stats']['rejected_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>

          <a href="/Tandain/triage?status=all<?= $statusQuery; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $data['current_status'] === 'all' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Semua (<?= htmlspecialchars((string)($data['stats']['total_triage'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>
        </div>

      </div>

      <div class="overflow-x-auto">
        <table class="data-table min-w-[760px]">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant h-11">
              <th scope="col">Judul Dokumen</th>
              <th scope="col">Pemohon</th>
              <th scope="col">Tanggal Masuk</th>
              <th scope="col" class="text-center">Status</th>
              <th scope="col" class="text-right min-w-[180px]">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-surface-container">
            <?php if (empty($data['documents'])): ?>
              <tr>
                <td colspan="5" class="table-empty text-center text-on-surface-variant">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <div class="w-12 h-12 rounded-full <?= ($data['current_status'] === 'pending' && empty($data['search'])) ? 'bg-[#f0fdf4] text-[#15803d]' : 'bg-surface-container text-on-surface-variant'; ?> flex items-center justify-center mb-1">
                      <span class="material-symbols-outlined text-[26px]"><?= ($data['current_status'] === 'pending' && empty($data['search'])) ? 'task_alt' : 'folder_open'; ?></span>
                    </div>
                    <p class="font-headline-sm text-sm font-semibold text-primary"><?= ($data['current_status'] === 'pending' && empty($data['search'])) ? 'Antrean Disposisi Bersih' : 'Tidak Ada Dokumen'; ?></p>
                    <p class="font-body-sm text-xs text-on-surface-variant"><?= htmlspecialchars($emptyMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($data['documents'] as $doc): ?>
                <tr class="hover:bg-surface-container-low/50 transition-colors group">
                  <td>
                    <a href="/Tandain/triage/review/<?= $doc['id']; ?>" class="table-title text-primary hover:text-secondary transition-colors leading-tight inline-block">
                      <?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <?php if (!empty($doc['document_number'])): ?>
                      <div class="table-meta font-code-tabular mt-0.5">
                        <?= htmlspecialchars($doc['document_number'], ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    <?php endif; ?>
                  </td>

                  <td>
                    <div class="text-sm font-medium text-primary">
                      <?= htmlspecialchars($doc['requester_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <div class="table-meta font-code-tabular mt-0.5">
                      <?= htmlspecialchars($doc['requester_nim'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <?php $requesterAffiliation = formatUserAffiliation($doc['requester_position'] ?? null, $doc['requester_study_program'] ?? null, ''); ?>
                    <?php if ($requesterAffiliation !== ''): ?>
                      <div class="table-meta mt-0.5">
                        <?= htmlspecialchars($requesterAffiliation, ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    <?php endif; ?>
                  </td>

                  <td class="table-date text-on-surface-variant">
                    <div><?= formatTanggalIndo($doc['created_at']); ?></div>
                    <div class="table-meta"><?= formatTanggalIndo($doc['created_at'], 'time'); ?></div>
                  </td>

                  <td class="text-center whitespace-nowrap">
                    <?php renderStatusBadge($doc['status']); ?>
                  </td>

                  <td class="text-right whitespace-nowrap">
                    <?php if ($doc['status'] === 'PENDING_TRIAGE'): ?>
                      <a href="/Tandain/triage/review/<?= $doc['id']; ?>" class="btn-sm inline-flex items-center gap-1.5 bg-brand text-white font-semibold hover:bg-brand-hover transition-all shadow-sm active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                        <span>Lihat Detail</span>
                      </a>
                    <?php else: ?>
                      <a href="/Tandain/triage/review/<?= $doc['id']; ?>" class="btn-sm inline-flex items-center gap-1.5 border border-outline-variant text-on-surface hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                        <span>Lihat Detail</span>
                      </a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="table-footer bg-surface-container-low border-t border-outline-variant flex items-center justify-between">
        <span class="text-xs text-on-surface-variant">
          Menampilkan <strong class="text-primary font-semibold"><?= count($data['documents']); ?></strong> pengajuan dokumen
        </span>
      </div>

    </section>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

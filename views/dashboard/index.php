<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php $isAdmin = !empty($data['is_admin']); ?>

<main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

  <h1 class="font-headline-lg text-headline-lg font-bold tracking-tight text-primary">Beranda</h1>

  <?php if ($isAdmin): ?>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

      <div class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Total Dokumen</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['doc_stats']['total_documents'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-soft text-brand">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">folder_open</span>
        </div>
      </div>

      <a class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-colors hover:border-secondary/50" href="/Tandain/triage?status=pending">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Menunggu Disposisi</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['doc_stats']['total_pending_triage'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#fef3c7] text-[#b45309]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">hourglass_top</span>
        </div>
      </a>

      <div class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Sedang Diproses</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['doc_stats']['total_in_progress'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#dbeafe] text-[#1d4ed8]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">sync</span>
        </div>
      </div>

      <a class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-colors hover:border-secondary/50" href="/Tandain/archive?status=COMPLETED">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Dokumen Selesai</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['doc_stats']['total_completed'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#dcfce7] text-[#15803d]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">verified</span>
        </div>
      </a>

    </section>

  <?php else: ?>

    <nav aria-label="Aksi dokumen" class="grid grid-cols-1 gap-4 md:grid-cols-3 xl:gap-6">
      <a href="/Tandain/document/create" class="block rounded-xl border border-outline-variant bg-surface-container-lowest p-4 shadow-sm transition-colors hover:border-brand focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
        <div class="flex h-24 items-center justify-center rounded-lg bg-brand-soft text-brand md:h-32">
          <span class="material-symbols-outlined text-[44px] md:text-[52px]" aria-hidden="true">upload_file</span>
        </div>

        <h2 class="mt-3 font-headline-sm text-headline-sm font-semibold text-primary">Ajukan Dokumen</h2>
        <p class="mt-1 text-body-sm text-on-surface-variant">Unggah PDF atau gunakan template surat.</p>
      </a>

      <a href="/Tandain/inbox" class="block rounded-xl border border-outline-variant bg-surface-container-lowest p-4 shadow-sm transition-colors hover:border-brand focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
        <div class="flex h-24 items-center justify-center rounded-lg bg-blue-100 text-blue-700 md:h-32">
          <span class="material-symbols-outlined text-[44px] md:text-[52px]" aria-hidden="true">draw</span>
        </div>

        <h2 class="mt-3 font-headline-sm text-headline-sm font-semibold text-primary">Tinjau dan Tandatangani</h2>
        <p class="mt-1 text-body-sm text-on-surface-variant">Periksa dokumen yang menunggu tanda tangan Anda.</p>
      </a>

      <a href="/Tandain/document" class="block rounded-xl border border-outline-variant bg-surface-container-lowest p-4 shadow-sm transition-colors hover:border-brand focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
        <div class="flex h-24 items-center justify-center rounded-lg bg-green-100 text-green-700 md:h-32">
          <span class="material-symbols-outlined text-[44px] md:text-[52px]" aria-hidden="true">folder_shared</span>
        </div>

        <h2 class="mt-3 font-headline-sm text-headline-sm font-semibold text-primary">Daftar Dokumen</h2>
        <p class="mt-1 text-body-sm text-on-surface-variant">Pantau status dokumen yang Anda ajukan.</p>
      </a>
    </nav>

  <?php endif; ?>

  <section class="rounded-xl bg-surface-container-lowest border border-outline-variant shadow-sm overflow-hidden flex flex-col">

    <div class="p-6 border-b border-outline-variant flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface-container-lowest">
      <h2 class="font-headline-sm text-headline-sm font-bold text-primary">Aktivitas Dokumen Terbaru</h2>

      <div>
        <a href="<?= $isAdmin ? '/Tandain/archive?status=all' : '/Tandain/document'; ?>" class="btn-md inline-flex items-center gap-1.5 bg-surface-container-low hover:bg-surface-container text-primary font-semibold transition-colors">
          <span><?= $isAdmin ? 'Lihat Arsip Dokumen' : 'Lihat Dokumen Saya'; ?></span>
          <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
        </a>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="data-table min-w-[740px]">
        <thead>
          <tr class="bg-surface-container-low border-b border-outline-variant text-[13px] font-label-md uppercase tracking-wider text-on-surface-variant">
            <th scope="col">Judul Dokumen</th>
            <th scope="col">Pemohon</th>
            <th scope="col">Tanggal Diajukan</th>
            <th scope="col" class="text-center">Status</th>
            <th scope="col" class="text-right">Aksi</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-outline-variant/60">
          <?php if (empty($data['recent_documents'])): ?>
            <tr>
              <td colspan="5" class="table-empty text-center text-on-surface-variant">
                Belum ada dokumen yang terdaftar dalam sistem.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($data['recent_documents'] as $doc): ?>
              <tr class="hover:bg-surface-container-low/60 transition-colors group">
                <td>
                  <div class="table-title text-primary">
                    <?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8'); ?>
                  </div>
                  <?php if (!empty($doc['document_number'])): ?>
                    <div class="table-meta font-code-tabular mt-0.5">
                      <?= htmlspecialchars($doc['document_number'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                  <?php endif; ?>
                </td>

                <td>
                  <p class="text-sm font-medium text-primary">
                    <?= htmlspecialchars($doc['requester_name'], ENT_QUOTES, 'UTF-8'); ?>
                  </p>
                  <p class="table-meta font-code-tabular">
                    <?= htmlspecialchars($doc['requester_nim'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                  </p>
                </td>

                <td class="table-date text-on-surface-variant">
                  <div><?= formatTanggalIndo($doc['created_at']); ?></div>
                  <div class="table-meta"><?= formatTanggalIndo($doc['created_at'], 'time'); ?></div>
                </td>

                <td class="text-center whitespace-nowrap">
                  <?php $badgeStatus = $doc['status'] === 'IN_PROGRESS' && ($doc['signer_status'] ?? '') === 'CURRENT_TURN' ? 'CURRENT_TURN' : $doc['status']; ?>
                  <?php renderStatusBadge($badgeStatus); ?>
                </td>

                <td class="text-right whitespace-nowrap">
                  <?php if ($isAdmin && $doc['status'] === 'PENDING_TRIAGE'): ?>
                    <a href="/Tandain/triage/review/<?= $doc['id']; ?>" class="btn-sm inline-flex w-full items-center justify-center gap-1.5 bg-brand text-white font-semibold hover:bg-brand-hover transition-all shadow-sm active:scale-[0.98]">
                      <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                      <span>Lihat Detail</span>
                    </a>
                  <?php elseif ($isAdmin): ?>
                    <a href="/Tandain/triage/review/<?= $doc['id']; ?>" class="btn-sm inline-flex w-full items-center justify-center gap-1.5 border border-outline-variant text-on-surface hover:bg-surface-container-low transition-colors">
                      <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                      <span>Lihat Detail</span>
                    </a>
                  <?php else: ?>
                    <?php if (($doc['signer_status'] ?? null) === 'CURRENT_TURN'): ?>
                      <a href="/Tandain/inbox/detail?document_id=<?= rawurlencode($doc['id']); ?>" class="btn-sm inline-flex w-full items-center justify-center gap-1.5 bg-brand text-white font-semibold hover:bg-brand-hover transition-all shadow-sm active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">edit</span>
                        <span>Review & TTD</span>
                      </a>
                    <?php elseif (($doc['status'] ?? null) === 'DRAFT'): ?>
                      <a href="/Tandain/document/position/<?= rawurlencode($doc['id']); ?>" class="btn-sm inline-flex w-full items-center justify-center gap-1.5 border border-brand text-brand font-bold hover:bg-brand-hover hover:text-white transition-all shadow-sm active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">draw</span>
                        <span>Atur Posisi TTE</span>
                      </a>
                    <?php elseif (($doc['requester_id'] ?? '') === ($data['user']['id'] ?? '')): ?>
                      <a href="/Tandain/document/detail?id=<?= rawurlencode($doc['id']); ?>" class="btn-sm inline-flex w-full items-center justify-center gap-1.5 border border-outline-variant text-on-surface hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                        <span>Lihat Detail</span>
                      </a>
                    <?php else: ?>
                      <a href="/Tandain/inbox/detail?document_id=<?= rawurlencode($doc['id']); ?>" class="btn-sm inline-flex w-full items-center justify-center gap-1.5 border border-outline-variant text-on-surface hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                        <span>Lihat Detail</span>
                      </a>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </section>

</main>

<?php require_once 'views/layouts/footer.php'; ?>

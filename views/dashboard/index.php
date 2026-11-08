<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php $isAdmin = !empty($data['is_admin']); ?>

  <main class="flex-1 p-8 w-full flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Beranda</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Platform Tanda Tangan &amp; Segel Elektronik Politeknik Negeri Jakarta</p>
      </div>

      <?php if ($isAdmin): ?>
        <div class="flex items-center gap-3">
          <a href="/Tandain/triage" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-outline-variant text-primary font-label-md text-label-md font-semibold hover:bg-surface-container-low transition-colors">
            <span class="material-symbols-outlined text-[18px]">move_to_inbox</span>
            <span>Antrean Disposisi</span>
          </a>

          <a href="/Tandain/archive" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#004B87] text-white font-label-md text-label-md font-semibold hover:bg-[#003865] transition-all shadow-sm active:scale-[0.98]">
            <span class="material-symbols-outlined text-[18px]">folder_open</span>
            <span>Arsip Dokumen</span>
          </a>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($isAdmin): ?>

      <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

        <div class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm">
          <div class="min-w-0">
            <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Total Dokumen</p>
            <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['doc_stats']['total_documents'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Berkas terdaftar</p>
          </div>
          <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#e8e7ff] text-[#5145d8]">
            <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">folder_open</span>
          </div>
        </div>

        <a class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md" href="/Tandain/triage?status=pending">
          <div class="min-w-0">
            <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Menunggu Disposisi</p>
            <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['doc_stats']['total_pending_triage'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Perlu ditelaah TU</p>
          </div>
          <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#fef3c7] text-[#b45309]">
            <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">hourglass_top</span>
          </div>
        </a>

        <div class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm">
          <div class="min-w-0">
            <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Sedang Diproses</p>
            <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['doc_stats']['total_in_progress'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Dalam alur TTE</p>
          </div>
          <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#dbeafe] text-[#1d4ed8]">
            <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">sync</span>
          </div>
        </div>

        <a class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md" href="/Tandain/archive?status=COMPLETED">
          <div class="min-w-0">
            <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Dokumen Selesai</p>
            <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['doc_stats']['total_completed'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Tersimpan di arsip</p>
          </div>
          <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#dcfce7] text-[#15803d]">
            <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">verified</span>
          </div>
        </a>

      </section>

    <?php else: ?>

      <section class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <a class="group rounded-xl border border-outline-variant bg-surface-container-lowest p-4 transition-all hover:-translate-y-0.5 hover:border-secondary hover:shadow-md" href="/Tandain/document/create">
          <div class="flex h-32 items-center justify-center rounded-lg bg-[#e8e7ff] text-[#5145d8] transition-colors group-hover:bg-[#dedcff]">
            <span class="material-symbols-outlined text-[64px]" aria-hidden="true">upload_file</span>
          </div>
          <div class="mt-3">
            <h2 class="font-label-lg text-label-lg font-semibold text-primary">Kirim Dokumen Baru</h2>
            <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Unggah berkas atau pilih template untuk meminta tanda tangan.</p>
          </div>
        </a>

        <a class="group rounded-xl border border-outline-variant bg-surface-container-lowest p-4 transition-all hover:-translate-y-0.5 hover:border-secondary hover:shadow-md" href="/Tandain/inbox">
          <div class="flex h-32 items-center justify-center rounded-lg bg-[#dceafe] text-[#1d4ed8] transition-colors group-hover:bg-[#cfe0fc]">
            <span class="material-symbols-outlined text-[64px]" aria-hidden="true">draw</span>
          </div>
          <div class="mt-3">
            <h2 class="font-label-lg text-label-lg font-semibold text-primary">Tandatangani Surat</h2>
            <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Periksa dan beri tanda tangan pada surat yang Anda terima.</p>
          </div>
        </a>

        <a class="group rounded-xl border border-outline-variant bg-surface-container-lowest p-4 transition-all hover:-translate-y-0.5 hover:border-secondary hover:shadow-md" href="/Tandain/document">
          <div class="flex h-32 items-center justify-center rounded-lg bg-[#dcfce7] text-[#15803d] transition-colors group-hover:bg-[#c8f7d8]">
            <span class="material-symbols-outlined text-[64px]" aria-hidden="true">folder_shared</span>
          </div>
          <div class="mt-3">
            <h2 class="font-label-lg text-label-lg font-semibold text-primary">Riwayat &amp; Status Surat</h2>
            <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Pantau proses persetujuan dan unduh surat yang sudah selesai.</p>
          </div>
        </a>

      </section>

    <?php endif; ?>

    <section class="rounded-xl bg-surface-container-lowest border border-outline-variant shadow-sm overflow-hidden flex flex-col">

      <div class="p-6 border-b border-outline-variant flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface-container-lowest">
        <div class="flex flex-col gap-1">
          <h2 class="font-headline-sm text-headline-sm font-bold text-primary">Aktivitas Dokumen Terbaru</h2>
          <p class="font-body-sm text-body-sm text-on-surface-variant">Daftar verifikasi dan status pengesahan berkas digital di sistem Tandain.</p>
        </div>

        <div>
          <a href="<?= $isAdmin ? '/Tandain/archive?status=all' : '/Tandain/document'; ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-surface-container-low hover:bg-surface-container text-primary font-label-md text-label-md font-semibold transition-colors">
            <span><?= $isAdmin ? 'Lihat Arsip Dokumen' : 'Lihat Dokumen Saya'; ?></span>
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
          </a>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant text-[13px] font-label-md uppercase tracking-wider text-on-surface-variant">
              <th class="py-3.5 px-6 font-semibold">Judul Dokumen</th>
              <th class="py-3.5 px-6 font-semibold">Pemohon</th>
              <th class="py-3.5 px-6 font-semibold text-right">Tanggal Diajukan</th>
              <th class="py-3.5 px-6 font-semibold text-center">Status</th>
              <th class="py-3.5 px-6 font-semibold text-right">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-outline-variant/60 font-body-sm text-body-sm">
            <?php if (empty($data['recent_documents'])): ?>
              <tr>
                <td colspan="5" class="py-8 text-center text-on-surface-variant">
                  Belum ada dokumen yang terdaftar dalam sistem.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($data['recent_documents'] as $doc): ?>
                <tr class="hover:bg-surface-container-low/60 transition-colors group">
                  <td class="py-4 px-6">
                    <div class="font-label-lg text-label-lg font-semibold text-primary">
                      <?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <?php if (!empty($doc['document_number'])): ?>
                      <div class="font-code-tabular text-[12px] text-outline mt-0.5">
                        <?= htmlspecialchars($doc['document_number'], ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    <?php endif; ?>
                  </td>

                  <td class="py-4 px-6">
                    <p class="font-label-md text-label-md font-semibold text-primary">
                      <?= htmlspecialchars($doc['requester_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <p class="font-code-tabular text-[12px] text-outline">
                      <?= htmlspecialchars($doc['requester_nim'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                  </td>

                  <td class="py-4 px-6 text-right font-code-tabular text-on-surface-variant whitespace-nowrap">
                    <div><?= date('d M Y', strtotime($doc['created_at'])); ?></div>
                    <div class="text-[12px] text-outline"><?= date('H:i', strtotime($doc['created_at'])); ?> WIB</div>
                  </td>

                  <td class="py-4 px-6 text-center whitespace-nowrap">
                    <?php if ($doc['status'] === 'COMPLETED'): ?>
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-label-sm font-semibold bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]">
                        <span class="material-symbols-outlined text-[14px]">check</span>
                        <span>Dokumen Selesai</span>
                      </span>
                    <?php elseif ($doc['status'] === 'PENDING_TRIAGE'): ?>
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-label-sm font-semibold bg-[#fef3c7] text-[#b45309] border border-[#fde68a]">
                        <span class="material-symbols-outlined text-[14px]">hourglass_top</span>
                        <span>Menunggu Disposisi</span>
                      </span>
                    <?php elseif ($doc['status'] === 'IN_PROGRESS'): ?>
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-label-sm font-semibold bg-[#dbeafe] text-[#1d4ed8] border border-[#bfdbfe]">
                        <span class="material-symbols-outlined text-[14px]">sync</span>
                        <span>Sedang Diproses</span>
                      </span>
                    <?php elseif ($doc['status'] === 'REJECTED'): ?>
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-label-sm font-semibold bg-[#fee2e2] text-[#b91c1c] border border-[#fecaca]">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                        <span>Ditolak</span>
                      </span>
                    <?php elseif ($doc['status'] === 'CANCELLED'): ?>
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-label-sm font-semibold bg-surface-container text-on-surface-variant border border-outline-variant">
                        <span class="material-symbols-outlined text-[14px]">block</span>
                        <span>Dibatalkan</span>
                      </span>
                    <?php elseif ($doc['status'] === 'DRAFT'): ?>
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-label-sm font-semibold bg-surface-container text-on-surface-variant border border-outline-variant">
                        <span>Draf</span>
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-label-sm font-semibold bg-surface-container text-on-surface-variant border border-outline-variant">
                        <span>Status Tidak Dikenal</span>
                      </span>
                    <?php endif; ?>
                  </td>

                  <td class="py-4 px-6 text-right whitespace-nowrap">
                    <?php if ($isAdmin && $doc['status'] === 'PENDING_TRIAGE'): ?>
                      <a href="/Tandain/triage/review/<?= $doc['id']; ?>" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-[#004B87] text-white font-label-md text-label-md font-semibold hover:bg-[#003865] transition-all shadow-sm active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[16px]">assignment_turned_in</span>
                        <span>Telaah &amp; Disposisi</span>
                      </a>
                    <?php elseif ($isAdmin): ?>
                      <a href="/Tandain/triage/review/<?= $doc['id']; ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-outline-variant font-label-md text-label-md text-on-surface hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        <span>Lihat Rincian</span>
                      </a>
                    <?php else: ?>
                      <a href="/Tandain/document" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-outline-variant font-label-md text-label-md text-on-surface hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        <span>Lihat Dokumen</span>
                      </a>
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

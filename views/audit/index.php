<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$actionLabels = [
    'CREATED' => 'Dokumen Diajukan',
    'DOCUMENT_CREATED' => 'Dokumen Diajukan',
    'DOCUMENT_CREATED_FROM_TEMPLATE' => 'Dibuat dari Template',
    'POSITIONS_SET' => 'Posisi TTE Ditetapkan',
    'DOCUMENT_SIGNED' => 'Dokumen Ditandatangani',
    'TRIAGE_ASSIGNED' => 'Disposisi Tata Usaha',
    'REJECTED' => 'Pengajuan Ditolak',
    'DOCUMENT_CANCELLED' => 'Dokumen Dibatalkan',
    'CANCELLED' => 'Dokumen Dibatalkan',
];

$badgeClasses = [
    'CREATED' => 'bg-[#e8e7ff] text-[#5145d8] border-[#c7c5ff]',
    'DOCUMENT_CREATED' => 'bg-[#e8e7ff] text-[#5145d8] border-[#c7c5ff]',
    'DOCUMENT_CREATED_FROM_TEMPLATE' => 'bg-[#ede9fe] text-[#6d28d9] border-[#ddd6fe]',
    'POSITIONS_SET' => 'bg-[#e0f2fe] text-[#0369a1] border-[#bae6fd]',
    'DOCUMENT_SIGNED' => 'bg-[#dcfce7] text-[#15803d] border-[#bbf7d0]',
    'TRIAGE_ASSIGNED' => 'bg-[#dceafe] text-[#1d4ed8] border-[#bfdbfe]',
    'REJECTED' => 'bg-[#fee2e2] text-[#b91c1c] border-[#fecaca]',
    'DOCUMENT_CANCELLED' => 'bg-surface-container text-on-surface-variant border-outline-variant',
    'CANCELLED' => 'bg-surface-container text-on-surface-variant border-outline-variant',
];
?>

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Log Audit Dokumen</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Rekaman riwayat peristiwa dokumen dan transaksi tanda tangan elektronik</p>
      </div>
    </div>

    <section class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-none overflow-hidden flex flex-col">

      <div class="p-5 border-b border-outline-variant bg-surface-container-lowest">
        <form method="GET" action="/Tandain/audit" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

          <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-on-surface-variant">
              <span class="material-symbols-outlined text-[20px]">search</span>
            </span>
            <input name="search" value="<?= htmlspecialchars($data['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all" placeholder="Cari dokumen, nomor surat, civitas, atau catatan..." type="text">
          </div>

          <div class="flex items-center gap-3 flex-wrap">

            <select name="action" class="px-3.5 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-md text-body-md focus:outline-none focus:border-secondary transition-colors">
              <option value="">Semua Aksi Peristiwa</option>
              <?php foreach ($data['actions'] as $act): ?>
                <?php $actValue = $act['action']; ?>
                <option value="<?= htmlspecialchars($actValue, ENT_QUOTES, 'UTF-8'); ?>" <?= ($data['selected_action'] === $actValue) ? 'selected' : ''; ?>>
                  <?= htmlspecialchars($actionLabels[$actValue] ?? ucwords(strtolower(str_replace('_', ' ', $actValue))), ENT_QUOTES, 'UTF-8'); ?>
                </option>
              <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-md inline-flex items-center gap-1.5 bg-surface-container-low hover:bg-surface-container text-primary border border-outline-variant font-semibold transition-colors">
              <span class="material-symbols-outlined text-[18px]">filter_list</span>
              <span>Filter</span>
            </button>

            <?php if (!empty($data['search']) || !empty($data['selected_action'])): ?>
              <a href="/Tandain/audit" class="px-3 py-2 text-on-surface-variant hover:text-primary font-label-md text-label-md transition-colors">
                Reset
              </a>
            <?php endif; ?>

          </div>

        </form>
      </div>

      <div class="overflow-x-auto">
        <table class="data-table min-w-[880px]">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant h-10 text-[11px] font-bold uppercase tracking-wider text-outline">
              <th scope="col" class="whitespace-nowrap">Waktu Kejadian</th>
              <th scope="col" class="whitespace-nowrap">Aksi Peristiwa</th>
              <th scope="col" class="min-w-[200px]">Dokumen Terkait</th>
              <th scope="col" class="min-w-[170px]">Aktor / Civitas</th>
              <th scope="col">Catatan &amp; Detail</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-surface-container">
            <?php if (empty($data['logs'])): ?>
              <tr>
                <td colspan="5" class="table-empty text-center text-on-surface-variant">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <div class="w-12 h-12 rounded-full bg-surface-container text-on-surface-variant flex items-center justify-center mb-1">
                      <span class="material-symbols-outlined text-[26px]">history</span>
                    </div>
                    <p class="font-headline-sm text-sm font-semibold text-primary">Log Audit Bersih</p>
                    <p class="font-body-sm text-xs text-on-surface-variant">Tidak ada rekaman log audit yang cocok dengan filter atau pencarian.</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($data['logs'] as $log): ?>
                <?php
                $actValue = $log['action'];
                $badgeClass = $badgeClasses[$actValue] ?? 'bg-surface-container text-on-surface-variant border-outline-variant';
                $docId = $log['document_id'] ?? '';
                ?>
                <tr class="hover:bg-surface-container-low/60 transition-colors group">
                  <td class="table-date text-on-surface-variant">
                    <div class="font-medium text-primary"><?= formatTanggalIndo($log['created_at']); ?></div>
                    <div class="table-meta"><?= formatTanggalIndo($log['created_at'], 'time-seconds'); ?></div>
                  </td>

                  <td class="whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border <?= $badgeClass; ?>">
                      <?= htmlspecialchars($actionLabels[$actValue] ?? ucwords(strtolower(str_replace('_', ' ', $actValue))), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </td>

                  <td class="min-w-[200px]">
                    <?php if (!empty($docId)): ?>
                      <a href="/Tandain/triage/review/<?= urlencode($docId); ?>" class="table-title text-primary hover:text-secondary transition-colors leading-snug line-clamp-1 inline-flex items-center gap-1 group-hover:text-secondary" title="Lihat detail dokumen">
                        <span><?= htmlspecialchars($log['document_title'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="material-symbols-outlined text-[13px] opacity-0 group-hover:opacity-100 transition-opacity">open_in_new</span>
                      </a>
                    <?php else: ?>
                      <span class="table-title text-primary"><?= htmlspecialchars($log['document_title'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>

                    <?php if (!empty($log['document_number'])): ?>
                      <span class="table-meta font-code-tabular block mt-0.5">
                        <?= htmlspecialchars($log['document_number'], ENT_QUOTES, 'UTF-8'); ?>
                      </span>
                    <?php endif; ?>
                  </td>

                  <td class="min-w-[170px]">
                    <?php if (!empty($log['user_full_name'])): ?>
                      <div class="font-medium text-primary leading-tight">
                        <?= htmlspecialchars($log['user_full_name'], ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                      <div class="table-meta font-code-tabular">
                        <?= htmlspecialchars($log['user_identity_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        <?php if (!empty($log['user_role_display'])): ?>
                          <span class="text-on-surface-variant font-normal">&bull; <?= htmlspecialchars($log['user_role_display'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                      </div>
                    <?php else: ?>
                      <span class="text-on-surface-variant italic text-[11px]">Sistem Otomatis / Publik</span>
                    <?php endif; ?>
                  </td>

                  <td class="text-on-surface-variant text-[12px] leading-relaxed">
                    <?= htmlspecialchars($log['notes'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="table-footer bg-surface-container-low border-t border-outline-variant flex items-center justify-between">
        <span class="text-xs text-on-surface-variant">
          Menampilkan <strong class="text-primary font-semibold"><?= count($data['logs']); ?></strong> peristiwa log audit
          <?php if (count($data['logs']) >= 100): ?>
            (menampilkan 100 peristiwa terbaru)
          <?php endif; ?>
        </span>
      </div>

    </section>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

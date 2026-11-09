<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$actionLabels = [
    'DOCUMENT_CREATED' => 'Dokumen Diajukan',
    'DOCUMENT_SIGNED' => 'Dokumen Ditandatangani',
    'TRIAGE_ASSIGNED' => 'Disposisi Tata Usaha',
    'REJECTED' => 'Pengajuan Ditolak',
    'DOCUMENT_CANCELLED' => 'Dokumen Dibatalkan',
    'CANCELLED' => 'Dokumen Dibatalkan',
];

$badgeClasses = [
    'DOCUMENT_CREATED' => 'bg-[#e8e7ff] text-[#5145d8] border-[#c7c5ff]',
    'DOCUMENT_SIGNED' => 'bg-[#dcfce7] text-[#15803d] border-[#bbf7d0]',
    'TRIAGE_ASSIGNED' => 'bg-[#dceafe] text-[#1d4ed8] border-[#bfdbfe]',
    'REJECTED' => 'bg-[#fee2e2] text-[#b91c1c] border-[#fecaca]',
    'DOCUMENT_CANCELLED' => 'bg-surface-container text-on-surface-variant border-outline-variant',
    'CANCELLED' => 'bg-surface-container text-on-surface-variant border-outline-variant',
];
?>

  <main class="flex-1 w-full p-8 flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Log Audit Dokumen</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Rekaman riwayat peristiwa dokumen dan transaksi tanda tangan elektronik</p>
      </div>
    </div>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

      <a class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md" href="/Tandain/audit">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Total Peristiwa</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['stats']['total_logs'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Aktivitas dokumen</p>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#e8e7ff] text-[#5145d8]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">history</span>
        </div>
      </a>

      <a class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md" href="/Tandain/audit?action=DOCUMENT_SIGNED">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Tanda Tangan Sah</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['stats']['total_signed'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Tanda tangan berhasil</p>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#dcfce7] text-[#15803d]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">draw</span>
        </div>
      </a>

      <a class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md" href="/Tandain/audit?action=TRIAGE_ASSIGNED">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Disposisi Tata Usaha</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['stats']['total_triage'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Alur penandatangan</p>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#dceafe] text-[#1d4ed8]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">move_to_inbox</span>
        </div>
      </a>

      <div class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Tolak &amp; Pembatalan</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['stats']['total_rejected_or_cancelled'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Tidak dilanjutkan</p>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#fee2e2] text-[#b91c1c]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">cancel</span>
        </div>
      </div>

    </section>

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

            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-surface-container-low hover:bg-surface-container text-primary border border-outline-variant rounded-lg font-label-md text-label-md font-semibold transition-colors">
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
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant h-11 text-[13px] font-semibold uppercase tracking-wide text-on-surface-variant">
              <th class="py-3 px-5 text-center w-14">No</th>
              <th class="py-3 px-6 whitespace-nowrap">Waktu &amp; Tanggal</th>
              <th class="py-3 px-6">Dokumen Terkait</th>
              <th class="py-3 px-6">Aktor / Civitas</th>
              <th class="py-3 px-6 text-center">Aksi Peristiwa</th>
              <th class="py-3 px-6">Catatan &amp; Detail</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-outline-variant/60 font-body-sm text-body-sm">
            <?php if (empty($data['logs'])): ?>
              <tr>
                <td colspan="6" class="py-8 text-center text-on-surface-variant">
                  Tidak ada rekaman log audit yang ditemukan.
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; ?>
              <?php foreach ($data['logs'] as $log): ?>
                <tr class="hover:bg-surface-container-low/50 transition-colors group">
                  <td class="py-4 px-5 text-center font-code-tabular text-on-surface-variant"><?= $no++; ?></td>

                  <td class="py-4 px-6 whitespace-nowrap font-code-tabular text-on-surface-variant">
                    <div><?= date('d M Y', strtotime($log['created_at'])); ?></div>
                    <div class="text-[11px] text-outline"><?= date('H:i:s', strtotime($log['created_at'])); ?> WIB</div>
                  </td>

                  <td class="py-4 px-6">
                    <div class="font-label-md text-label-md font-semibold text-primary">
                      <?= htmlspecialchars($log['document_title'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <?php if (!empty($log['document_number'])): ?>
                      <div class="font-code-tabular text-[12px] text-outline mt-0.5">
                        <?= htmlspecialchars($log['document_number'], ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    <?php endif; ?>
                  </td>

                  <td class="py-4 px-6">
                    <?php if (!empty($log['user_full_name'])): ?>
                      <div class="font-label-md text-label-md font-medium text-primary">
                        <?= htmlspecialchars($log['user_full_name'], ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                      <div class="font-code-tabular text-[12px] text-outline">
                        <?= htmlspecialchars($log['user_identity_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                      <?php if (!empty($log['user_role_display'])): ?>
                        <div class="text-[11px] text-on-surface-variant mt-0.5">
                          <?= htmlspecialchars($log['user_role_display'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="text-on-surface-variant italic">Sistem Otomatis / Publik</span>
                    <?php endif; ?>
                  </td>

                  <td class="py-4 px-6 text-center whitespace-nowrap">
                    <?php $actValue = $log['action']; ?>
                    <?php $badgeClass = $badgeClasses[$actValue] ?? 'bg-surface-container text-on-surface-variant border-outline-variant'; ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border <?= $badgeClass; ?>">
                      <?= htmlspecialchars($actionLabels[$actValue] ?? ucwords(strtolower(str_replace('_', ' ', $actValue))), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </td>

                  <td class="py-4 px-6 text-on-surface-variant">
                    <?= htmlspecialchars($log['notes'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="p-4 bg-surface-container-low border-t border-outline-variant flex items-center justify-between">
        <span class="font-body-sm text-body-sm text-on-surface-variant">
          Menampilkan <strong class="text-primary font-semibold"><?= count($data['logs']); ?></strong> peristiwa log audit
        </span>
      </div>

    </section>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$templatesList = $data['templates'] ?? ($templates ?? []);
$statsData = $data['stats'] ?? ($stats ?? []);
$searchTerm = $data['search'] ?? ($search ?? '');
$currentStatus = $data['current_status'] ?? ($current_status ?? 'all');

$activeFlash = $data['flash'] ?? ($flash ?? ($_SESSION['flash'] ?? null));

if (isset($_SESSION['flash'])) {
    unset($_SESSION['flash']);
}

$searchQuery = '';

if (!empty($searchTerm)) {
    $searchQuery = '&search=' . urlencode($searchTerm);
}

if (!empty($searchTerm)) {
    $emptyMessage = 'Tidak ada template surat yang cocok dengan kata kunci pencarian.';
} elseif ($currentStatus === 'active') {
    $emptyMessage = 'Tidak ada template surat yang berstatus aktif.';
} elseif ($currentStatus === 'inactive') {
    $emptyMessage = 'Tidak ada template surat yang berstatus nonaktif.';
} else {
    $emptyMessage = 'Belum ada template surat yang terdaftar.';
}
?>

  <main class="flex-1 min-w-0 w-full max-w-full [contain:inline-size] overflow-x-hidden p-6 xl:p-8 flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Manajemen Template Surat</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Kelola naskah dinas resmi PNJ dan skema isian dinamis pemohon</p>
      </div>

      <div class="flex items-center gap-3">
        <a href="/Tandain/template/create" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#004B87] text-white font-label-md text-label-md font-semibold hover:bg-[#003865] transition-all shadow-sm active:scale-[0.98]">
          <span class="material-symbols-outlined text-[18px]">add</span>
          <span>Buat Template Baru</span>
        </a>
      </div>
    </div>

    <?php if (!empty($activeFlash) && is_array($activeFlash)): ?>
      <?php
      if (($activeFlash['type'] ?? '') === 'success') {
          $flashClass = 'border-[#bbf7d0] bg-[#f0fdf4] text-[#15803d]';
          $flashIcon = 'check_circle';
      } elseif (($activeFlash['type'] ?? '') === 'error') {
          $flashClass = 'border-[#fecaca] bg-[#fef2f2] text-[#b91c1c]';
          $flashIcon = 'error';
      } else {
          $flashClass = 'border-[#bfdbfe] bg-[#eff6ff] text-[#1d4ed8]';
          $flashIcon = 'info';
      }
      ?>

      <div class="flex items-center gap-3 rounded-xl border px-4 py-3 font-body-sm text-body-sm <?= $flashClass; ?>" role="status">
        <span class="material-symbols-outlined text-[20px]" aria-hidden="true"><?= $flashIcon; ?></span>
        <span><?= htmlspecialchars($activeFlash['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
    <?php endif; ?>

    <section class="grid grid-cols-1 sm:grid-cols-3 gap-5">

      <a href="/Tandain/template?status=all<?= $searchQuery; ?>" class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Total Template</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($statsData['total_templates'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Naskah surat terdaftar</p>
        </div>

        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#e8e7ff] text-[#5145d8]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">description</span>
        </div>
      </a>

      <a href="/Tandain/template?status=active<?= $searchQuery; ?>" class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Template Aktif</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($statsData['active_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Tersedia untuk pengajuan</p>
        </div>

        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#dcfce7] text-[#15803d]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">check_circle</span>
        </div>
      </a>

      <a href="/Tandain/template?status=inactive<?= $searchQuery; ?>" class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Template Nonaktif</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($statsData['inactive_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Diarsipkan sementara</p>
        </div>

        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#fef3c7] text-[#b45309]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">pause_circle</span>
        </div>
      </a>

    </section>

    <section class="min-w-0 w-full bg-surface-container-lowest border border-outline-variant rounded-xl shadow-none overflow-hidden flex flex-col">

      <div class="p-5 border-b border-surface-container-high flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-surface-container-lowest">

        <form method="GET" action="/Tandain/template" class="flex items-center gap-2 min-w-0 flex-1 max-w-lg">
          <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus, ENT_QUOTES, 'UTF-8'); ?>">

          <div class="relative min-w-0 flex-1">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-on-surface-variant">
              <span class="material-symbols-outlined text-[20px]">search</span>
            </span>
            <input name="search" value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8'); ?>" class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all" placeholder="Cari kode template, judul, atau deskripsi..." type="text">
          </div>

          <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-surface-container-low hover:bg-surface-container text-primary border border-outline-variant rounded-lg font-label-md text-label-md font-semibold transition-colors shrink-0">
            <span class="material-symbols-outlined text-[18px]">search</span>
            <span>Cari</span>
          </button>

          <?php if (!empty($searchTerm) || $currentStatus !== 'all'): ?>
            <a href="/Tandain/template" class="px-2.5 py-2 text-on-surface-variant hover:text-primary font-label-md text-label-md transition-colors shrink-0">
              Reset
            </a>
          <?php endif; ?>
        </form>

        <div class="flex items-center gap-1 p-1 bg-surface-container rounded-lg self-start lg:self-auto max-w-full overflow-x-auto shrink-0">
          <a href="/Tandain/template?status=all<?= $searchQuery; ?>" class="px-3.5 py-1.5 rounded-md font-label-md text-label-md whitespace-nowrap transition-colors <?= $currentStatus === 'all' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Semua (<?= htmlspecialchars((string)($statsData['total_templates'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>

          <a href="/Tandain/template?status=active<?= $searchQuery; ?>" class="px-3.5 py-1.5 rounded-md font-label-md text-label-md whitespace-nowrap transition-colors <?= $currentStatus === 'active' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Aktif (<?= htmlspecialchars((string)($statsData['active_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>

          <a href="/Tandain/template?status=inactive<?= $searchQuery; ?>" class="px-3.5 py-1.5 rounded-md font-label-md text-label-md whitespace-nowrap transition-colors <?= $currentStatus === 'inactive' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Nonaktif (<?= htmlspecialchars((string)($statsData['inactive_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>
        </div>

      </div>

      <div class="w-full overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant h-11">
              <th class="py-3 px-2.5 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider w-10 text-center">No</th>
              <th class="py-3 px-3 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap">Kode Template</th>
              <th class="py-3 px-3 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Judul &amp; Deskripsi</th>
              <th class="py-3 px-2.5 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-center whitespace-nowrap">Field Dinamis</th>
              <th class="py-3 px-2.5 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider whitespace-nowrap">Diperbarui</th>
              <th class="py-3 px-2.5 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-center">Status</th>
              <th class="py-3 px-3 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-right whitespace-nowrap">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-surface-container font-body-md text-body-md">
            <?php if (empty($templatesList)): ?>
              <tr>
                <td colspan="7" class="py-10 px-6 text-center text-on-surface-variant">
                  <p><?= htmlspecialchars($emptyMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; ?>
              <?php foreach ($templatesList as $tpl): ?>
                <?php
                $schemaRaw = $tpl['placeholders_schema'] ?? [];

                if (is_string($schemaRaw)) {
                    $decodedSchema = json_decode($schemaRaw, true);
                    $schemaItems = is_array($decodedSchema) ? $decodedSchema : [];
                } elseif (is_array($schemaRaw)) {
                    $schemaItems = $schemaRaw;
                } else {
                    $schemaItems = [];
                }

                $fieldCount = count($schemaItems);
                $isActive = ($tpl['is_active'] === true || $tpl['is_active'] === 1 || $tpl['is_active'] === '1' || $tpl['is_active'] === 't' || $tpl['is_active'] === 'true');
                $updatedTimestamp = !empty($tpl['updated_at']) ? strtotime($tpl['updated_at']) : time();
                ?>

                <tr class="hover:bg-surface-container-low/50 transition-colors group">
                  <td class="py-3.5 px-2.5 text-center font-code-tabular text-code-tabular text-on-surface-variant"><?= $no++; ?></td>

                  <td class="py-3.5 px-3 whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-surface-container border border-outline-variant font-code-tabular text-[11px] font-semibold text-primary">
                      <?= htmlspecialchars($tpl['template_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </td>

                  <td class="py-3.5 px-3 min-w-[140px] max-w-xs">
                    <a href="/Tandain/template/edit/<?= urlencode($tpl['id']); ?>" class="font-headline-sm text-[15px] font-semibold text-primary hover:text-secondary transition-colors leading-tight inline-block">
                      <?= htmlspecialchars($tpl['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    </a>

                    <?php if (!empty($tpl['description'])): ?>
                      <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 line-clamp-2">
                        <?= htmlspecialchars($tpl['description'], ENT_QUOTES, 'UTF-8'); ?>
                      </p>
                    <?php endif; ?>
                  </td>

                  <td class="py-3.5 px-2.5 text-center whitespace-nowrap">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-surface-container-low border border-outline-variant/80 font-code-tabular text-xs font-semibold text-primary">
                      <span class="material-symbols-outlined text-[14px] text-secondary">tune</span>
                      <span><?= $fieldCount; ?> field</span>
                    </span>
                  </td>

                  <td class="py-3.5 px-2.5 font-code-tabular text-xs text-on-surface-variant whitespace-nowrap">
                    <div><?= date('d M Y', $updatedTimestamp); ?></div>
                    <div class="text-[11px] text-outline"><?= date('H:i', $updatedTimestamp); ?> WIB</div>
                  </td>

                  <td class="py-3.5 px-2.5 text-center whitespace-nowrap">
                    <?php if ($isActive): ?>
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-label-sm font-label-sm bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]">
                        <span class="material-symbols-outlined text-[13px]">check_circle</span>
                        <span>Aktif</span>
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-label-sm font-label-sm bg-surface-container text-on-surface-variant border border-outline-variant">
                        <span class="material-symbols-outlined text-[13px]">pause_circle</span>
                        <span>Nonaktif</span>
                      </span>
                    <?php endif; ?>
                  </td>

                  <td class="py-3.5 px-3 text-right whitespace-nowrap">
                    <div class="inline-flex items-center justify-end gap-1.5">

                      <a href="/Tandain/template/edit/<?= urlencode($tpl['id']); ?>" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-outline-variant font-label-md text-xs font-semibold text-primary hover:bg-surface-container-low transition-colors" title="Ubah Template">
                        <span class="material-symbols-outlined text-[15px]">edit</span>
                        <span>Ubah</span>
                      </a>

                      <form method="POST" action="/Tandain/template/toggle/<?= urlencode($tpl['id']); ?>" class="inline">
                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-outline-variant <?= $isActive ? 'text-[#b45309] hover:bg-amber-50' : 'text-[#15803d] hover:bg-green-50'; ?> transition-colors" title="<?= $isActive ? 'Nonaktifkan Template' : 'Aktifkan Template'; ?>" aria-label="<?= $isActive ? 'Nonaktifkan Template' : 'Aktifkan Template'; ?>">
                          <span class="material-symbols-outlined text-[17px]"><?= $isActive ? 'pause_circle' : 'play_circle'; ?></span>
                          <span class="sr-only"><?= $isActive ? 'Nonaktifkan' : 'Aktifkan'; ?></span>
                        </button>
                      </form>

                      <form method="POST" action="/Tandain/template/delete/<?= urlencode($tpl['id']); ?>" class="inline" onsubmit="return confirm('Hapus template surat ini secara permanen?');">
                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-outline-variant text-[#b91c1c] hover:bg-red-50 transition-colors" title="Hapus Template" aria-label="Hapus Template">
                          <span class="material-symbols-outlined text-[17px]">delete</span>
                          <span class="sr-only">Hapus</span>
                        </button>
                      </form>

                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="p-4 bg-surface-container-low border-t border-outline-variant flex items-center justify-between">
        <span class="font-body-sm text-body-sm text-on-surface-variant">
          Menampilkan <strong class="text-primary font-semibold"><?= count($templatesList); ?></strong> template surat
          <?php if (!empty($searchTerm) || $currentStatus !== 'all'): ?>
            sesuai filter
          <?php endif; ?>
        </span>
      </div>

    </section>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

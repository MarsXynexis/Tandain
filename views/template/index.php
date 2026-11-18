<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$templatesList = $data['templates'] ?? ($templates ?? []);
$statsData = $data['stats'] ?? ($stats ?? []);
$searchTerm = $data['search'] ?? ($search ?? '');
$currentStatus = $data['current_status'] ?? ($current_status ?? 'all');

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

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Manajemen Template Surat</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Kelola naskah dinas resmi PNJ dan skema isian dinamis pemohon</p>
      </div>

      <div class="flex items-center gap-3">
        <a href="/Tandain/template/create" class="btn-md inline-flex items-center gap-2 bg-brand text-white font-semibold hover:bg-brand-hover transition-all shadow-sm active:scale-[0.98]">
          <span class="material-symbols-outlined text-[18px]">add</span>
          <span>Buat Template Baru</span>
        </a>
      </div>
    </div>

    <?php require 'views/layouts/flash.php'; ?>

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

          <button type="submit" class="btn-md inline-flex items-center gap-1.5 bg-surface-container-low hover:bg-surface-container text-primary border border-outline-variant font-semibold transition-colors shrink-0">
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
          <a href="/Tandain/template?status=all<?= $searchQuery; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $currentStatus === 'all' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Semua (<?= htmlspecialchars((string)($statsData['total_templates'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>

          <a href="/Tandain/template?status=active<?= $searchQuery; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $currentStatus === 'active' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Aktif (<?= htmlspecialchars((string)($statsData['active_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>

          <a href="/Tandain/template?status=inactive<?= $searchQuery; ?>" class="btn-sm inline-flex shrink-0 whitespace-nowrap transition-colors <?= $currentStatus === 'inactive' ? 'font-semibold bg-surface-container-lowest text-primary shadow-sm' : 'font-medium text-on-surface-variant hover:text-primary'; ?>">
            Nonaktif (<?= htmlspecialchars((string)($statsData['inactive_count'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>)
          </a>
        </div>

      </div>

      <div class="w-full overflow-x-auto">
        <table class="data-table min-w-[760px]">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant h-11">
              <th scope="col">Template Naskah Dinas</th>
              <th scope="col" class="whitespace-nowrap">Terakhir Diperbarui</th>
              <th scope="col" class="text-center whitespace-nowrap">Status</th>
              <th scope="col" class="text-right whitespace-nowrap">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-surface-container">
            <?php if (empty($templatesList)): ?>
              <tr>
                <td colspan="4" class="table-empty text-center text-on-surface-variant">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <div class="w-12 h-12 rounded-full bg-surface-container text-on-surface-variant flex items-center justify-center mb-1">
                      <span class="material-symbols-outlined text-[26px]">description</span>
                    </div>
                    <p class="font-headline-sm text-sm font-semibold text-primary">Template Tidak Ditemukan</p>
                    <p class="font-body-sm text-xs text-on-surface-variant"><?= htmlspecialchars($emptyMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
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
                  <td class="min-w-[280px]">
                    <div class="flex items-start gap-3">
                      <div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-secondary shrink-0 mt-0.5">
                        <span class="material-symbols-outlined text-[20px]">description</span>
                      </div>
                      <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                          <a href="/Tandain/template/edit/<?= urlencode($tpl['id']); ?>" class="table-title text-primary hover:text-secondary transition-colors leading-snug">
                            <?= htmlspecialchars($tpl['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                          </a>
                          <span class="font-code-tabular text-[11px] font-medium text-outline bg-surface-container px-1.5 py-0.5 rounded border border-outline-variant/60">
                            <?= htmlspecialchars($tpl['template_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                          </span>
                        </div>

                        <?php if (!empty($tpl['description'])): ?>
                          <p class="font-body-sm text-xs text-on-surface-variant mt-1 line-clamp-1 leading-relaxed">
                            <?= htmlspecialchars($tpl['description'], ENT_QUOTES, 'UTF-8'); ?>
                          </p>
                        <?php endif; ?>

                        <div class="flex items-center gap-2 mt-1.5 text-[11px] text-outline">
                          <?php if ($fieldCount > 0): ?>
                            <span class="inline-flex items-center gap-1 font-medium text-on-surface-variant">
                              <span class="material-symbols-outlined text-[13px] text-secondary">tune</span>
                              <span><?= $fieldCount; ?> kolom isian pemohon</span>
                            </span>
                          <?php else: ?>
                            <span class="text-outline">Naskah statis (tanpa isian pemohon)</span>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </td>

                  <td class="table-date text-on-surface-variant">
                    <div class="font-medium text-primary"><?= formatTanggalIndo($updatedTimestamp); ?></div>
                    <div class="table-meta"><?= formatTanggalIndo($updatedTimestamp, 'time'); ?></div>
                  </td>

                  <td class="text-center whitespace-nowrap">
                    <?php renderStatusBadge($isActive ? 'ACTIVE' : 'INACTIVE'); ?>
                  </td>

                  <td class="text-right whitespace-nowrap">
                    <div class="inline-flex items-center justify-end gap-1.5">

                      <a href="/Tandain/template/edit/<?= urlencode($tpl['id']); ?>" class="btn-sm inline-flex items-center gap-1 border border-outline-variant font-semibold text-primary hover:bg-surface-container-low transition-colors" title="Ubah Template">
                        <span class="material-symbols-outlined text-[15px]">edit</span>
                        <span>Ubah</span>
                      </a>

                      <form method="POST" action="/Tandain/template/toggle/<?= urlencode($tpl['id']); ?>" class="inline">
                        <button type="submit" class="btn-sm btn-icon inline-flex items-center justify-center border border-outline-variant <?= $isActive ? 'text-[#b45309] hover:bg-amber-50' : 'text-[#15803d] hover:bg-green-50'; ?> transition-colors" title="<?= $isActive ? 'Nonaktifkan Template' : 'Aktifkan Template'; ?>" aria-label="<?= $isActive ? 'Nonaktifkan Template' : 'Aktifkan Template'; ?>">
                          <span class="material-symbols-outlined text-[17px]"><?= $isActive ? 'pause_circle' : 'play_circle'; ?></span>
                          <span class="sr-only"><?= $isActive ? 'Nonaktifkan' : 'Aktifkan'; ?></span>
                        </button>
                      </form>

                      <form method="POST" action="/Tandain/template/delete/<?= urlencode($tpl['id']); ?>" class="inline" data-confirm-form>
                        <button type="submit" class="btn-sm btn-icon inline-flex items-center justify-center border border-outline-variant text-[#b91c1c] hover:bg-red-50 transition-colors" title="Hapus Template" aria-label="Hapus Template">
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

      <div class="table-footer bg-surface-container-low border-t border-outline-variant flex items-center justify-between">
        <span class="text-xs text-on-surface-variant">
          Menampilkan <strong class="text-primary font-semibold"><?= count($templatesList); ?></strong> template surat
          <?php if (!empty($searchTerm) || $currentStatus !== 'all'): ?>
            sesuai filter
          <?php endif; ?>
        </span>
      </div>

    </section>

  </main>

<?php $confirmation = ['title' => 'Hapus Template Surat', 'message' => 'Template yang dihapus tidak dapat dipulihkan.', 'action' => 'Hapus Template']; ?>
<?php require 'views/layouts/confirm.php'; ?>

<?php require_once 'views/layouts/footer.php'; ?>

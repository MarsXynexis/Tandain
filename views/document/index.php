<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$docCounts = ['all' => 0, 'completed' => 0, 'in_progress' => 0, 'draft' => 0, 'rejected' => 0];
$docRows = [];

foreach ($documents as $docItem) {
  if ($docItem['status'] === 'COMPLETED') {
    $filterStatus = 'completed';
  } elseif (in_array($docItem['status'], ['PENDING_TRIAGE', 'IN_PROGRESS'], true)) {
    $filterStatus = 'in_progress';
  } elseif ($docItem['status'] === 'DRAFT') {
    $filterStatus = 'draft';
  } elseif ($docItem['status'] === 'REJECTED') {
    $filterStatus = 'rejected';
  } else {
    $filterStatus = 'other';
  }

  $docItem['filter_status'] = $filterStatus;
  $docRows[] = $docItem;
  $docCounts['all']++;

  if (isset($docCounts[$filterStatus])) {
    $docCounts[$filterStatus]++;
  }
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
        <label class="relative flex-1 max-w-md">
          <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-on-surface-variant">
            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">search</span>
          </span>
          <input id="doc-search" class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all" placeholder="Cari judul atau nomor dokumen..." type="search">
        </label>

        <div class="min-w-0 max-w-full">
          <div class="flex max-w-full items-center gap-1 overflow-x-auto rounded-xl bg-surface-container p-1" role="group" aria-label="Filter status dokumen">
            <button class="btn-sm inline-flex doc-filter shrink-0 whitespace-nowrap transition-colors font-semibold bg-surface-container-lowest text-primary shadow-sm" type="button" data-filter="all" aria-pressed="true">Semua (<?= $docCounts['all']; ?>)</button>
            <button class="btn-sm inline-flex doc-filter shrink-0 whitespace-nowrap transition-colors font-medium text-on-surface-variant hover:text-primary" type="button" data-filter="completed" aria-pressed="false">Selesai (<?= $docCounts['completed']; ?>)</button>
            <button class="btn-sm inline-flex doc-filter shrink-0 whitespace-nowrap transition-colors font-medium text-on-surface-variant hover:text-primary" type="button" data-filter="in_progress" aria-pressed="false">Sedang Diproses (<?= $docCounts['in_progress']; ?>)</button>
            <button class="btn-sm inline-flex doc-filter shrink-0 whitespace-nowrap transition-colors font-medium text-on-surface-variant hover:text-primary" type="button" data-filter="draft" aria-pressed="false">Draft (<?= $docCounts['draft']; ?>)</button>
            <button class="btn-sm inline-flex doc-filter shrink-0 whitespace-nowrap transition-colors font-medium text-on-surface-variant hover:text-primary" type="button" data-filter="rejected" aria-pressed="false">Ditolak (<?= $docCounts['rejected']; ?>)</button>
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

          <tbody id="doc-rows" class="divide-y divide-surface-container">
            <?php $number = 1; ?>
            <?php foreach ($docRows as $doc): ?>
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
            <tr class="doc-row hover:bg-surface-container-low/50 transition-colors group" data-status="<?= $doc['filter_status']; ?>" data-search="<?= htmlspecialchars(strtolower($doc['title'] . ' ' . ($doc['document_number'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
              <td class="doc-row-number text-center font-code-tabular text-code-tabular text-on-surface-variant"><?= $number; ?></td>
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
                <?php if ($doc['status'] === 'DRAFT'): ?>
                  <div class="inline-flex w-full items-center justify-end gap-1.5">
                    <?php if ($totalSigner > 0): ?>
                      <a href="/Tandain/document/position/<?= rawurlencode($doc['id']); ?>" class="btn-sm inline-flex flex-1 items-center justify-center gap-1.5 bg-brand text-white transition-colors hover:bg-brand-hover">
                        <span class="material-symbols-outlined text-[15px]" aria-hidden="true">draw</span>
                        <span>Atur Posisi TTE</span>
                      </a>
                    <?php else: ?>
                      <a href="<?= $detailUrl; ?>" class="btn-sm inline-flex w-full items-center justify-center gap-1.5 border border-outline-variant font-medium text-on-surface transition-colors hover:bg-surface-container-low">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                        <span>Lihat Detail</span>
                      </a>
                    <?php endif; ?>
                  </div>
                <?php else: ?>
                  <a href="<?= $detailUrl; ?>" class="btn-sm inline-flex w-full items-center justify-center gap-1.5 border border-outline-variant font-medium text-on-surface transition-colors hover:bg-surface-container-low">
                    <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                    <span>Lihat Detail</span>
                  </a>
                <?php endif; ?>
              </td>
            </tr>
            <?php $number += 1; ?>
            <?php endforeach; ?>
            <tr id="doc-empty" class="hidden">
              <td colspan="6" class="table-empty text-center text-on-surface-variant">
                Belum ada dokumen yang cocok. Ubah kata kunci atau pilih filter lain.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <footer id="doc-footer" class="table-footer flex flex-col items-start justify-between gap-3 border-t border-surface-container-high bg-white sm:flex-row sm:items-center">
        <p id="doc-count" class="text-on-surface-variant">Menampilkan <strong id="doc-visible-count" class="text-primary">0</strong> dari <strong id="doc-filter-count" class="text-primary"><?= $docCounts['all']; ?></strong> dokumen</p>
        <nav id="doc-pagination" class="flex min-w-0 max-w-full items-center gap-2" aria-label="Halaman dokumen">
          <button id="doc-previous" class="btn-sm inline-flex shrink-0 items-center gap-1 border border-outline-variant bg-white font-semibold text-primary transition-colors hover:bg-surface-container-low" type="button" aria-label="Halaman sebelumnya">
            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">chevron_left</span>
            <span class="hidden sm:inline">Sebelumnya</span>
          </button>
          <div id="doc-page-numbers" class="flex min-w-0 items-center gap-1 overflow-x-auto p-1" aria-label="Nomor halaman"></div>
          <button id="doc-next" class="btn-sm inline-flex shrink-0 items-center gap-1 border border-outline-variant bg-white font-semibold text-primary transition-colors hover:bg-surface-container-low" type="button" aria-label="Halaman berikutnya">
            <span class="hidden sm:inline">Selanjutnya</span>
            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">chevron_right</span>
          </button>
        </nav>
      </footer>

    </div>

  </main>

<script>
  (() => {
    const searchInput = document.getElementById('doc-search');
    const filterButtons = document.querySelectorAll('.doc-filter');
    const rows = Array.from(document.querySelectorAll('.doc-row'));
    const emptyRow = document.getElementById('doc-empty');
    const docFooter = document.getElementById('doc-footer');
    const pagination = document.getElementById('doc-pagination');
    const previousButton = document.getElementById('doc-previous');
    const nextButton = document.getElementById('doc-next');
    const pageNumbers = document.getElementById('doc-page-numbers');
    const pageSize = 8;
    let activeFilter = 'all';
    let currentPage = 1;

    const createPageButton = (page) => {
      const button = document.createElement('button');
      const isActive = page === currentPage;
      button.type = 'button';
      button.textContent = String(page);
      button.setAttribute('aria-label', `Halaman ${page}`);
      button.className = isActive
        ? 'btn-sm btn-icon flex shrink-0 bg-primary-container font-semibold text-on-primary'
        : 'btn-sm btn-icon flex shrink-0 font-semibold text-on-surface-variant hover:bg-surface-container-low';
      if (isActive) button.setAttribute('aria-current', 'page');
      button.addEventListener('click', () => {
        currentPage = page;
        renderRows();
      });
      pageNumbers.appendChild(button);
    };

    const renderRows = () => {
      const query = searchInput.value.trim().toLocaleLowerCase();
      const matchedRows = rows.filter((row) => {
        const matchesFilter = activeFilter === 'all' || row.dataset.status === activeFilter;
        const matchesSearch = row.dataset.search.includes(query);
        return matchesFilter && matchesSearch;
      });

      const totalPages = Math.ceil(matchedRows.length / pageSize);
      currentPage = Math.min(currentPage, Math.max(totalPages, 1));
      const startIndex = (currentPage - 1) * pageSize;
      const pageRows = matchedRows.slice(startIndex, startIndex + pageSize);
      const pageRowSet = new Set(pageRows);

      rows.forEach((row) => row.classList.toggle('hidden', !pageRowSet.has(row)));
      pageRows.forEach((row, index) => {
        row.querySelector('.doc-row-number').textContent = String(startIndex + index + 1);
      });
      emptyRow.classList.toggle('hidden', matchedRows.length > 0);

      const endIndex = Math.min(startIndex + pageRows.length, matchedRows.length);
      document.getElementById('doc-visible-count').textContent = matchedRows.length
        ? `${startIndex + 1}-${endIndex}`
        : '0';
      document.getElementById('doc-filter-count').textContent = String(matchedRows.length);

      pageNumbers.replaceChildren();
      if (totalPages > 1) {
        const firstPage = Math.max(1, Math.min(currentPage - 2, totalPages - 4));
        const lastPage = Math.min(totalPages, firstPage + 4);
        for (let page = firstPage; page <= lastPage; page += 1) {
          createPageButton(page);
        }
      }

      previousButton.disabled = currentPage <= 1;
      nextButton.disabled = currentPage >= totalPages;
      previousButton.classList.toggle('opacity-40', previousButton.disabled);
      previousButton.classList.toggle('cursor-not-allowed', previousButton.disabled);
      nextButton.classList.toggle('opacity-40', nextButton.disabled);
      nextButton.classList.toggle('cursor-not-allowed', nextButton.disabled);
      pagination.classList.toggle('hidden', totalPages <= 1);
      docFooter.classList.toggle('hidden', matchedRows.length === 0);
    };

    previousButton.addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage -= 1;
        renderRows();
      }
    });
    nextButton.addEventListener('click', () => {
      currentPage += 1;
      renderRows();
    });

    searchInput.addEventListener('input', () => {
      currentPage = 1;
      renderRows();
    });
    filterButtons.forEach((button) => {
      button.addEventListener('click', () => {
        activeFilter = button.dataset.filter;
        currentPage = 1;
        filterButtons.forEach((filterButton) => {
          const isActive = filterButton === button;
          filterButton.classList.toggle('bg-surface-container-lowest', isActive);
          filterButton.classList.toggle('text-primary', isActive);
          filterButton.classList.toggle('shadow-sm', isActive);
          filterButton.classList.toggle('font-semibold', isActive);
          filterButton.classList.toggle('font-medium', !isActive);
          filterButton.classList.toggle('text-on-surface-variant', !isActive);
          filterButton.classList.toggle('hover:text-primary', !isActive);
          filterButton.setAttribute('aria-pressed', String(isActive));
        });
        renderRows();
      });
    });

    renderRows();
  })();
</script>

<?php require_once 'views/layouts/footer.php'; ?>
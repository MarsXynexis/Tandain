<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$inboxCounts = ['all' => 0, 'signed' => 0, 'waiting' => 0, 'rejected' => 0];
$inboxRows = [];

foreach ($data['documents'] as $inboxDocument) {
  if ($inboxDocument['signer_status'] !== 'WAITING') {
    if ($inboxDocument['document_status'] === 'REJECTED' || $inboxDocument['signer_status'] === 'REJECTED') {
      $filterStatus = 'rejected';
    } elseif ($inboxDocument['signer_status'] === 'CURRENT_TURN') {
      $filterStatus = 'waiting';
    } else {
      $filterStatus = 'signed';
    }

    $inboxDocument['filter_status'] = $filterStatus;
    $inboxRows[] = $inboxDocument;
    $inboxCounts['all']++;
    $inboxCounts[$filterStatus]++;
  }
}
?>

<main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">
  <section>
    <h1 class="font-headline-lg text-headline-lg font-bold tracking-tight text-primary">Persetujuan Dokumen</h1>
    <p class="font-body-md text-body-md text-on-surface-variant mt-1">Daftar dokumen yang memerlukan persetujuan dan pembubuhan tanda tangan elektronik Anda</p>
  </section>

  <?php require 'views/layouts/flash.php'; ?>

  <section class="flex flex-col overflow-hidden rounded-lg border border-outline-variant/40 bg-surface-container-lowest shadow-sm">
    <div class="flex flex-col items-stretch gap-3 border-b border-outline-variant/30 p-5 md:flex-row md:items-center">
      <label class="relative min-w-0 flex-1">
        <span class="material-symbols-outlined pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[16px] text-on-surface-variant" aria-hidden="true">search</span>
        <input id="inbox-search" class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all" placeholder="Cari judul atau nomor dokumen..." type="search">
      </label>

      <div class="flex min-w-0 max-w-full items-center gap-1 overflow-x-auto rounded-xl bg-surface-container-high p-1 md:max-w-[65%]" role="group" aria-label="Filter status dokumen">
        <button class="btn-sm inline-flex inbox-filter whitespace-nowrap bg-white font-semibold text-primary shadow-sm" type="button" data-filter="all" aria-pressed="true">Semua (<?= $inboxCounts['all']; ?>)</button>
        <button class="btn-sm inline-flex inbox-filter whitespace-nowrap font-medium text-on-surface-variant transition-colors hover:text-primary" type="button" data-filter="signed" aria-pressed="false">Ditandatangani (<?= $inboxCounts['signed']; ?>)</button>
        <button class="btn-sm inline-flex inbox-filter whitespace-nowrap font-medium text-on-surface-variant transition-colors hover:text-primary" type="button" data-filter="rejected" aria-pressed="false">Ditolak (<?= $inboxCounts['rejected']; ?>)</button>
        <button class="btn-sm inline-flex inbox-filter whitespace-nowrap font-medium text-on-surface-variant transition-colors hover:text-primary" type="button" data-filter="waiting" aria-pressed="false">Pending (<?= $inboxCounts['waiting']; ?>)</button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="data-table min-w-[920px]">
        <thead>
          <tr class="border-b border-outline-variant/40">
            <th scope="col" class="w-20 text-center">No</th>
            <th scope="col" class="w-[30%]">Judul Dokumen</th>
            <th scope="col" class="w-[280px]">Pemohon</th>
            <th scope="col" class="w-[180px]">Tanggal Diajukan</th>
            <th scope="col" class="w-[320px] text-center">Status</th>
            <th scope="col" class="w-[200px] text-right">Aksi</th>
          </tr>
        </thead>
        <tbody id="inbox-rows">
          <?php $data['number'] = 1; ?>
          <?php foreach ($inboxRows as $document): ?>
            <?php $identityLabel = ($document['requester_role_name'] ?? '') === 'pegawai' ? 'NIP' : (($document['requester_role_name'] ?? '') === 'mahasiswa' ? 'NIM' : 'NIM/NIP'); ?>
            <?php $rowFilterStatus = $document['filter_status']; ?>
            <tr class="inbox-row border-b border-outline-variant/20 transition-colors hover:bg-surface-container-low/40" data-status="<?= $rowFilterStatus; ?>" data-search="<?= htmlspecialchars(strtolower($document['document_title'] . ' ' . $document['requester_name']), ENT_QUOTES, 'UTF-8'); ?>">
              <td class="inbox-row-number text-center font-body-md text-body-md text-on-surface-variant"><?= htmlspecialchars($data['number'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td>
                <a href="<?= '/Tandain/inbox/detail?document_id=' . urlencode($document['document_id']); ?>" class="block table-title text-primary hover:text-secondary transition-colors leading-tight">
                  <?= htmlspecialchars($document['document_title'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
              </td>
              <td>
                <span class="text-sm font-medium text-primary"><?= htmlspecialchars($document['requester_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="table-meta block"><?= $identityLabel; ?>: <?= htmlspecialchars($document['requester_nim_nip'] ?? 'Tidak tersedia', ENT_QUOTES, 'UTF-8'); ?></span>
              </td>
              <td class="table-date">
                <p class="font-medium text-on-surface"><?= formatTanggalIndo($document['submitted_at']); ?></p>
                <p class="table-meta"><?= formatTanggalIndo($document['submitted_at'], 'time'); ?></p>
              </td>
              <td class="text-center">
                <?php renderStatusBadge($rowFilterStatus === 'rejected' ? 'REJECTED' : $document['signer_status']); ?>

                <?php if ($document['signer_status'] === 'SIGNED' && $document['document_status'] === 'IN_PROGRESS'): ?>
                  <p class="table-meta mt-1 text-[9px]">Menunggu penandatangan berikutnya</p>
                <?php endif; ?>
              </td>
              <td class="whitespace-nowrap">
                <div class="flex items-center justify-end">
                  <?php if ($rowFilterStatus === 'waiting'): ?>
                    <a class="btn-sm inline-flex w-full items-center justify-center gap-2 bg-brand font-semibold text-white shadow-sm transition-colors hover:bg-brand-hover" href="/Tandain/inbox/detail?document_id=<?= urlencode($document['document_id']); ?>">
                      <span class="material-symbols-outlined text-[16px]" aria-hidden="true">edit</span>
                      <span>Review & TTD</span>
                    </a>
                  <?php else: ?>
                    <a class="btn-sm inline-flex w-full items-center justify-center gap-2 border border-slate-300 bg-white font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-50 hover:text-slate-900" href="/Tandain/inbox/detail?document_id=<?= urlencode($document['document_id']); ?>">
                      <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                      <span>Lihat Detail</span>
                    </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php $data['number'] += 1; ?>
          <?php endforeach; ?>
          <tr id="inbox-empty" class="hidden">
            <td class="table-empty text-center font-body-md text-body-md text-on-surface-variant" colspan="6">Tidak ada dokumen yang sesuai.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <footer class="table-footer flex flex-col gap-3 border-t border-outline-variant/30 bg-white sm:flex-row sm:items-center sm:justify-between">
      <p id="inbox-count" class="text-xs text-on-surface-variant">Menampilkan <span id="inbox-visible-count" class="font-semibold text-primary">0</span> dari <span id="inbox-filter-count" class="font-semibold text-primary"><?= $inboxCounts['all']; ?></span> permohonan</p>
      <nav id="inbox-pagination" class="flex min-w-0 max-w-full items-center gap-2" aria-label="Halaman permohonan">
        <button id="inbox-previous" class="btn-sm inline-flex shrink-0 items-center gap-1 border border-outline-variant/40 text-on-surface-variant" type="button" aria-label="Halaman sebelumnya">
          <span class="material-symbols-outlined text-[14px]" aria-hidden="true">chevron_left</span>
          <span class="hidden sm:inline">Sebelumnya</span>
        </button>
        <div id="inbox-page-numbers" class="flex min-w-0 items-center gap-1 overflow-x-auto p-1" aria-label="Nomor halaman"></div>
        <button id="inbox-next" class="btn-sm inline-flex shrink-0 items-center gap-1 border border-outline-variant/40 text-primary hover:bg-surface-container-low" type="button" aria-label="Halaman berikutnya">
          <span class="hidden sm:inline">Selanjutnya</span>
          <span class="material-symbols-outlined text-[14px]" aria-hidden="true">chevron_right</span>
        </button>
      </nav>
    </footer>
  </section>
</main>

<script>
  (() => {
    const searchInput = document.getElementById('inbox-search');
    const filterButtons = document.querySelectorAll('.inbox-filter');
    const rows = Array.from(document.querySelectorAll('.inbox-row'));
    const emptyRow = document.getElementById('inbox-empty');
    const pagination = document.getElementById('inbox-pagination');
    const previousButton = document.getElementById('inbox-previous');
    const nextButton = document.getElementById('inbox-next');
    const pageNumbers = document.getElementById('inbox-page-numbers');
    const pageSize = 10;
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
        row.querySelector('.inbox-row-number').textContent = String(startIndex + index + 1);
      });
      emptyRow.classList.toggle('hidden', matchedRows.length > 0);

      const endIndex = Math.min(startIndex + pageRows.length, matchedRows.length);
      document.getElementById('inbox-visible-count').textContent = matchedRows.length
        ? `${startIndex + 1}-${endIndex}`
        : '0';
      document.getElementById('inbox-filter-count').textContent = String(matchedRows.length);

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
          filterButton.classList.toggle('bg-white', isActive);
          filterButton.classList.toggle('text-primary', isActive);
          filterButton.classList.toggle('shadow-sm', isActive);
          filterButton.classList.toggle('font-semibold', isActive);
          filterButton.classList.toggle('font-medium', !isActive);
          filterButton.classList.toggle('text-on-surface-variant', !isActive);
          filterButton.setAttribute('aria-pressed', String(isActive));
        });
        renderRows();
      });
    });

    renderRows();
  })();
</script>

<?php require_once 'views/layouts/footer.php'; ?>

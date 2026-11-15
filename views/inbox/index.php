<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$inboxCounts = ['all' => 0, 'signed' => 0, 'waiting' => 0, 'rejected' => 0];
foreach ($data['documents'] as $inboxDocument) {
  if ($inboxDocument['signer_status'] === 'WAITING') {
    continue;
  }

  $inboxCounts['all']++;
  $filterStatus = $inboxDocument['signer_status'] === 'CURRENT_TURN'
    ? 'waiting'
    : ($inboxDocument['signer_status'] === 'REJECTED' ? 'rejected' : 'signed');
  $inboxCounts[$filterStatus]++;
}
?>

<main class="flex-1 w-full p-8 flex flex-col gap-6">
  <section>
    <h1 class="font-headline-lg text-headline-lg font-bold tracking-tight text-primary">Persetujuan Dokumen</h1>
    <p class="font-body-md text-body-md text-on-surface-variant mt-1">Daftar dokumen yang memerlukan persetujuan dan pembubuhan tanda tangan elektronik Anda.</p>
  </section>

  <section class="flex flex-col overflow-hidden rounded-lg border border-outline-variant/40 bg-surface-container-lowest shadow-sm">
    <div class="flex flex-col items-stretch gap-3 border-b border-outline-variant/30 p-5 md:flex-row md:items-center">
      <label class="relative min-w-0 flex-1">
        <span class="material-symbols-outlined pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[16px] text-on-surface-variant" aria-hidden="true">search</span>
        <input id="inbox-search" class="h-10 w-full rounded border border-outline-variant/50 bg-surface-container-low/40 pl-10 pr-4 font-body-md text-body-md text-primary placeholder:text-on-surface-variant/60 focus:border-secondary focus:outline-none focus:ring-1 focus:ring-secondary" placeholder="Cari judul atau nomor dokumen..." type="search">
      </label>

      <div class="flex shrink-0 items-center gap-1 overflow-x-auto rounded bg-surface-container-high p-1" role="group" aria-label="Filter status dokumen">
        <button class="inbox-filter whitespace-nowrap rounded-md bg-white px-3.5 py-1.5 font-label-md text-label-md font-semibold text-primary shadow-sm" type="button" data-filter="all" aria-pressed="true">Semua (<?= $inboxCounts['all']; ?>)</button>
        <button class="inbox-filter whitespace-nowrap rounded-md px-3.5 py-1.5 font-label-md text-label-md font-medium text-on-surface-variant transition-colors hover:text-primary" type="button" data-filter="signed" aria-pressed="false">Ditandatangani (<?= $inboxCounts['signed']; ?>)</button>
        <button class="inbox-filter whitespace-nowrap rounded-md px-3.5 py-1.5 font-label-md text-label-md font-medium text-on-surface-variant transition-colors hover:text-primary" type="button" data-filter="rejected" aria-pressed="false">Ditolak (<?= $inboxCounts['rejected']; ?>)</button>
        <button class="inbox-filter whitespace-nowrap rounded-md px-3.5 py-1.5 font-label-md text-label-md font-medium text-on-surface-variant transition-colors hover:text-primary" type="button" data-filter="waiting" aria-pressed="false">Pending (<?= $inboxCounts['waiting']; ?>)</button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full min-w-[920px] border-collapse text-left">
        <thead>
          <tr class="border-b border-outline-variant/40 bg-surface-container-low/60 text-[11px] font-bold uppercase tracking-[0.05em] text-on-surface-variant">
            <th class="w-20 px-6 py-3 text-center">No</th>
            <th class="w-[30%] px-6 py-3">Judul Dokumen</th>
            <th class="w-[280px] px-6 py-3">Pemohon</th>
            <th class="w-[180px] px-6 py-3">Tanggal Diajukan</th>
            <th class="w-[320px] px-6 py-3 text-center">Status</th>
            <th class="w-[200px] px-6 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody id="inbox-rows">
          <?php $data['number'] = 1; ?>
          <?php foreach ($data['documents'] as $document): ?>
            <?php if ($document['signer_status'] === 'WAITING') continue; ?>
            <?php $submittedTimestamp = strtotime($document['submitted_at']); ?>
            <?php $identityLabel = ($document['requester_role_name'] ?? '') === 'pegawai' ? 'NIP' : (($document['requester_role_name'] ?? '') === 'mahasiswa' ? 'NIM' : 'NIM/NIP'); ?>
            <?php $rowFilterStatus = $document['signer_status'] === 'CURRENT_TURN' ? 'waiting' : ($document['signer_status'] === 'REJECTED' ? 'rejected' : 'signed'); ?>
            <tr class="inbox-row border-b border-outline-variant/20 transition-colors hover:bg-surface-container-low/40" data-status="<?= $rowFilterStatus; ?>" data-search="<?= htmlspecialchars(strtolower($document['document_title'] . ' ' . $document['requester_name']), ENT_QUOTES, 'UTF-8'); ?>">
              <td class="inbox-row-number px-6 py-5 text-center font-body-md text-body-md text-on-surface-variant"><?= htmlspecialchars($data['number'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td class="px-6 py-5">
                <a href="<?= '/Tandain/inbox/detail?document_id=' . urlencode($document['document_id']); ?>" class="block font-headline-sm text-[15px] font-semibold text-primary hover:text-secondary transition-colors leading-tight">
                  <?= htmlspecialchars($document['document_title'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
              </td>
              <td class="px-6 py-5">
                <span class="block font-body-md text-body-md font-medium text-primary"><?= htmlspecialchars($document['requester_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="block font-body-sm text-body-sm text-on-surface-variant"><?= $identityLabel; ?>: <?= htmlspecialchars($document['requester_nim_nip'] ?? 'Tidak tersedia', ENT_QUOTES, 'UTF-8'); ?></span>
              </td>
              <td class="whitespace-nowrap px-6 py-5">
                <p class="font-body-sm text-body-sm font-medium text-on-surface"><?= $submittedTimestamp !== false ? date('d M Y', $submittedTimestamp) : '-'; ?></p>
                <p class="font-body-sm text-body-sm text-on-surface-variant"><?= $submittedTimestamp !== false ? date('H:i', $submittedTimestamp) . ' WIB' : '-'; ?></p>
              </td>
              <td class="px-6 py-5 text-center">
                <?php if ($document['signer_status'] === 'CURRENT_TURN'): ?>
                  <?php
                  $statusLabel = 'Menunggu Tanda Tangan Anda';
                  $statusIcon = 'edit';
                  $statusBadge = 'bg-[#fffbeb] text-[#b45309]';
                  ?>
                <?php elseif ($document['signer_status'] === 'SIGNED' && $document['document_status'] === 'IN_PROGRESS'): ?>
                  <?php
                  $statusLabel = 'Menunggu Penandatangan Selanjutnya';
                  $statusIcon = 'hourglass_top';
                  $statusBadge = 'bg-[#eff6ff] text-[#1d4ed8]';
                  ?>
                <?php elseif ($document['signer_status'] === 'SIGNED'): ?>
                  <?php
                  $statusLabel = 'Sudah Ditandatangani';
                  $statusIcon = 'check';
                  $statusBadge = 'bg-[#dcfce7] text-[#047857]';
                  ?>
                <?php elseif ($document['signer_status'] === 'REJECTED'): ?>
                  <?php
                  $statusLabel = 'Ditolak';
                  $statusIcon = 'close';
                  $statusBadge = 'bg-[#fef2f2] text-[#b91c1c]';
                  ?>
                <?php else: ?>
                  <?php
                  $statusLabel = 'Dibatalkan';
                  $statusIcon = 'block';
                  $statusBadge = 'bg-surface-container text-on-surface-variant';
                  ?>
                <?php endif; ?>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm <?= $statusBadge; ?>">
                  <span class="material-symbols-outlined text-[14px]" aria-hidden="true"><?= $statusIcon; ?></span>
                  <span><?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                </span>
              </td>
              <td class="whitespace-nowrap px-6 py-5">
                <div class="flex items-center justify-end gap-1.5">
                  <?php if ($document['signer_status'] === 'CURRENT_TURN'): ?>
                    <a class="w-full inline-flex justify-between rounded bg-[#004b87] px-7 py-2 font-label-md text-label-md font-semibold text-white shadow-sm transition-colors hover:bg-[#003865]" href="/Tandain/inbox/detail?document_id=<?= urlencode($document['document_id']); ?>">
                      <span class="material-symbols-outlined text-[14px]" aria-hidden="true">edit_document</span>
                      Review &amp; TTE
                    </a>
                  <?php else: ?>
                    <a class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-sm border border-outline-variant bg-white px-2.5 py-1.5 font-body-sm text-body-sm font-medium text-on-surface transition-colors hover:bg-surface-container-low" href="/Tandain/inbox/detail?document_id=<?= urlencode($document['document_id']); ?>">
                      <span class="material-symbols-outlined text-[14px]" aria-hidden="true">visibility</span>
                      Detail
                    </a>
                    <?php if ($document['signer_status'] === 'SIGNED'): ?>
                      <?php if ($document['document_status'] === 'COMPLETED'): ?>
                        <a class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-sm bg-[#047857] px-2.5 py-1.5 font-body-sm text-body-sm font-medium text-white shadow-sm transition-colors hover:bg-[#065f46]" href="/Tandain/inbox/detail?document_id=<?= urlencode($document['document_id']); ?>&amp;download=signed">
                          <span class="material-symbols-outlined text-[13px]" aria-hidden="true">download</span>
                          Signed
                        </a>
                      <?php else: ?>
                        <div class="inline-flex flex-1 cursor-not-allowed items-center justify-center gap-1.5 rounded-sm bg-[#7b8f82] px-2.5 py-1.5 font-body-sm text-body-sm font-medium text-white shadow-sm" aria-disabled="true">
                          <span class="material-symbols-outlined text-[13px]" aria-hidden="true">download</span>
                          Signed
                        </div>
                      <?php endif; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php $data['number'] += 1; ?>
          <?php endforeach; ?>
          <tr id="inbox-empty" class="hidden">
            <td class="px-6 py-10 text-center font-body-md text-body-md text-on-surface-variant" colspan="6">Tidak ada dokumen yang sesuai.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <footer class="flex flex-col gap-3 border-t border-outline-variant/30 bg-white px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
      <p id="inbox-count" class="font-body-sm text-body-sm text-on-surface-variant">Menampilkan <span id="inbox-visible-count" class="font-semibold text-primary">0</span> dari <span id="inbox-filter-count" class="font-semibold text-primary"><?= $inboxCounts['all']; ?></span> permohonan</p>
      <nav id="inbox-pagination" class="flex items-center justify-end gap-2" aria-label="Pagination">
        <button id="inbox-previous" class="inline-flex items-center gap-1 rounded border border-outline-variant/40 px-3 py-1.5 font-body-sm text-body-sm text-on-surface-variant" type="button" aria-label="Halaman sebelumnya">
          <span class="material-symbols-outlined text-[14px]" aria-hidden="true">chevron_left</span>
          Sebelumnya
        </button>
        <div id="inbox-page-numbers" class="flex items-center gap-1" aria-label="Nomor halaman"></div>
        <button id="inbox-next" class="inline-flex items-center gap-1 rounded border border-outline-variant/40 px-3 py-1.5 font-body-sm text-body-sm text-primary hover:bg-surface-container-low" type="button" aria-label="Halaman berikutnya">
          Selanjutnya
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
        ? 'flex h-7 w-7 items-center justify-center rounded bg-on-surface font-label-md text-label-md font-semibold text-surface'
        : 'flex h-7 w-7 items-center justify-center rounded font-label-md text-label-md font-semibold text-on-surface-variant hover:bg-surface-container-low';
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
<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$inboxDocuments = [
    [
        'number' => '1',
        'title' => 'Lembar Pengesahan Skripsi',
        'applicant' => 'Uda Uda',
        'date' => '25 Feb 2026',
        'time' => '08:30 WIB',
        'status' => 'waiting',
    ],
    [
        'number' => '2',
        'title' => 'Berita Acara Sidang Tugas Akhir',
        'applicant' => 'Theo Town',
        'date' => '24 Feb 2026',
        'time' => '14:15 WIB',
        'status' => 'waiting',
    ],
    [
        'number' => '3',
        'title' => 'Surat Rekomendasi Magang Industri',
        'applicant' => 'Nayla Skeleton',
        'date' => '24 Feb 2026',
        'time' => '11:00 WIB',
        'status' => 'signed',
    ],
    [
        'number' => '5',
        'title' => 'Pengajuan Cuti Akademik',
        'applicant' => 'Son',
        'date' => '22 Feb 2026',
        'time' => '10:20 WIB',
        'status' => 'signed',
    ],
];
?>

  <main class="flex-1 w-full p-8 flex flex-col gap-6">
    <section>
      <h1 class="font-headline-lg text-2xl font-bold tracking-tight text-primary">Persetujuan Dokumen</h1>
      <p class="font-body-md text-body-md text-on-surface-variant">Daftar dokumen yang memerlukan persetujuan dan pembubuhan tanda tangan elektronik Anda.</p>
    </section>

    <section class="flex flex-col overflow-hidden rounded-lg border border-outline-variant/40 bg-surface-container-lowest shadow-sm">
      <div class="flex flex-col items-stretch gap-3 border-b border-outline-variant/30 p-5 md:flex-row md:items-center">
        <label class="relative min-w-0 flex-1">
          <span class="material-symbols-outlined pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[16px] text-on-surface-variant" aria-hidden="true">search</span>
          <input id="inbox-search" class="h-10 w-full rounded border border-outline-variant/50 bg-surface-container-low/40 pl-10 pr-4 font-body-md text-body-md text-primary placeholder:text-on-surface-variant/60 focus:border-secondary focus:outline-none focus:ring-1 focus:ring-secondary" placeholder="Cari judul atau nomor dokumen..." type="search">
        </label>

        <div class="flex shrink-0 items-center gap-1 overflow-x-auto rounded bg-surface-container-high p-1" role="group" aria-label="Filter status dokumen">
          <button class="inbox-filter whitespace-nowrap rounded-md bg-white px-3.5 py-1.5 font-label-md text-label-md font-semibold text-primary shadow-sm" type="button" data-filter="all" aria-pressed="true">Semua (18)</button>
          <button class="inbox-filter whitespace-nowrap rounded-md px-3.5 py-1.5 font-label-md text-label-md font-medium text-on-surface-variant transition-colors hover:text-primary" type="button" data-filter="signed" aria-pressed="false">Selesai (14)</button>
          <button class="inbox-filter whitespace-nowrap rounded-md px-3.5 py-1.5 font-label-md text-label-md font-medium text-on-surface-variant transition-colors hover:text-primary" type="button" data-filter="waiting" aria-pressed="false">Sedang Diproses (4)</button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[920px] border-collapse text-left">
          <thead>
            <tr class="border-b border-outline-variant/40 bg-surface-container-low/60 text-[11px] font-bold uppercase tracking-[0.05em] text-on-surface-variant">
              <th class="w-20 px-6 py-3 text-center">No</th>
              <th class="px-6 py-3">Judul Dokumen</th>
              <th class="w-[135px] px-6 py-3">Pemohon</th>
              <th class="w-[180px] px-6 py-3">Tanggal Diajukan</th>
              <th class="w-[320px] px-6 py-3 text-center">Status</th>
              <th class="w-[200px] px-6 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody id="inbox-rows">
            <?php foreach ($inboxDocuments as $document): ?>
              <?php $isWaiting = $document['status'] === 'waiting'; ?>
              <tr class="inbox-row border-b border-outline-variant/20 transition-colors hover:bg-surface-container-low/40" data-status="<?= htmlspecialchars($document['status'], ENT_QUOTES, 'UTF-8'); ?>" data-search="<?= htmlspecialchars(strtolower($document['number'] . ' ' . $document['title'] . ' ' . $document['applicant']), ENT_QUOTES, 'UTF-8'); ?>">
                <td class="px-6 py-5 text-center font-body-md text-body-md text-on-surface-variant"><?= htmlspecialchars($document['number'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="px-6 py-5">
                  <span class="block max-w-[240px] font-headline-sm text-[15px] font-semibold leading-[1.4] text-primary"><?= htmlspecialchars($document['title'], ENT_QUOTES, 'UTF-8'); ?></span>
                </td>
                <td class="px-6 py-5 font-body-md text-body-md font-medium text-primary"><?= htmlspecialchars($document['applicant'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="whitespace-nowrap px-6 py-5">
                  <p class="font-body-sm text-body-sm font-medium text-on-surface"><?= htmlspecialchars($document['date'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <p class="font-body-sm text-body-sm text-on-surface-variant"><?= htmlspecialchars($document['time'], ENT_QUOTES, 'UTF-8'); ?></p>
                </td>
                <td class="px-6 py-5 text-center">
                  <?php if ($isWaiting): ?>
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-[#fde68a] bg-[#fffbeb] px-2.5 py-1 text-[12px] font-semibold text-[#b45309]">
                      <span class="material-symbols-outlined text-[12px]" aria-hidden="true">hourglass_top</span>
                      Menunggu Tanda Tangan Anda
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-[#a7f3d0] bg-[#ecfdf5] px-2.5 py-1 text-[12px] font-semibold text-[#047857]">
                      <span class="material-symbols-outlined text-[13px]" aria-hidden="true">check</span>
                      Sudah Ditandatangani
                    </span>
                  <?php endif; ?>
                </td>
                <td class="whitespace-nowrap px-6 py-5">
                  <div class="flex items-center justify-end gap-1.5">
                    <?php if ($isWaiting): ?>
                      <a class="inline-flex items-center gap-1.5 rounded bg-[#004b87] px-3.5 py-2 font-label-md text-label-md font-semibold text-white shadow-sm transition-colors hover:bg-[#003865]" href="#">
                        <span class="material-symbols-outlined text-[14px]" aria-hidden="true">edit_document</span>
                        Review &amp; TTE
                      </a>
                    <?php else: ?>
                      <a class="inline-flex items-center gap-1.5 rounded-sm border border-outline-variant bg-white px-2.5 py-1.5 font-body-sm text-body-sm font-medium text-on-surface transition-colors hover:bg-surface-container-low" href="#">
                        <span class="material-symbols-outlined text-[14px]" aria-hidden="true">visibility</span>
                        Detail
                      </a>
                      <a class="inline-flex items-center gap-1.5 rounded-sm bg-[#047857] px-2.5 py-1.5 font-body-sm text-body-sm font-medium text-white shadow-sm transition-colors hover:bg-[#065f46]" href="#">
                        <span class="material-symbols-outlined text-[13px]" aria-hidden="true">download</span>
                        Signed
                      </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            <tr id="inbox-empty" class="hidden">
              <td class="px-6 py-10 text-center font-body-md text-body-md text-on-surface-variant" colspan="6">Tidak ada dokumen yang sesuai.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <footer class="flex flex-col gap-3 border-t border-outline-variant/30 bg-white px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
        <p id="inbox-count" class="font-body-sm text-body-sm text-on-surface-variant">Menampilkan <span class="font-semibold text-primary">1-4</span> dari <span class="font-semibold text-primary">4</span> permohonan</p>
        <nav class="flex items-center justify-end gap-2" aria-label="Pagination">
          <button class="inline-flex items-center gap-1 rounded border border-outline-variant/40 px-3 py-1.5 font-body-sm text-body-sm text-on-surface-variant opacity-40" type="button" disabled>
            <span class="material-symbols-outlined text-[14px]" aria-hidden="true">chevron_left</span>
            Sebelumnya
          </button>
          <span class="flex h-7 w-7 items-center justify-center rounded bg-on-surface font-label-md text-label-md font-semibold text-surface" aria-current="page">1</span>
          <button class="flex h-7 w-7 items-center justify-center rounded font-label-md text-label-md font-semibold text-on-surface-variant hover:bg-surface-container-low" type="button">2</button>
          <button class="flex h-7 w-7 items-center justify-center rounded font-label-md text-label-md font-semibold text-on-surface-variant hover:bg-surface-container-low" type="button">3</button>
          <button class="flex h-7 w-7 items-center justify-center rounded font-label-md text-label-md font-semibold text-on-surface-variant hover:bg-surface-container-low" type="button">4</button>
          <button class="inline-flex items-center gap-1 rounded border border-outline-variant/40 px-3 py-1.5 font-body-sm text-body-sm text-primary hover:bg-surface-container-low" type="button">
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
      const count = document.getElementById('inbox-count');
      let activeFilter = 'all';

      const renderRows = () => {
        const query = searchInput.value.trim().toLocaleLowerCase();
        let visibleCount = 0;

        rows.forEach((row) => {
          const matchesFilter = activeFilter === 'all' || row.dataset.status === activeFilter;
          const matchesSearch = row.dataset.search.includes(query);
          const isVisible = matchesFilter && matchesSearch;
          row.classList.toggle('hidden', !isVisible);
          if (isVisible) visibleCount += 1;
        });

        emptyRow.classList.toggle('hidden', visibleCount > 0);
        count.innerHTML = `Menampilkan <span class="font-semibold text-primary">${visibleCount ? `1-${visibleCount}` : '0'}</span> dari <span class="font-semibold text-primary">${visibleCount}</span> permohonan`;
      };

      searchInput.addEventListener('input', renderRows);
      filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
          activeFilter = button.dataset.filter;
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
    })();
  </script>

<?php require_once 'views/layouts/footer.php'; ?>

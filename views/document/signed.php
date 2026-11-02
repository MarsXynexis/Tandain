<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 p-8 w-full flex flex-col gap-6">

    <div class="flex flex-col gap-1">
      <h1 class="text-2xl font-bold text-primary font-headline-lg tracking-tight">Dokumen Ditandatangani</h1>
      <p class="font-body-md text-body-md text-on-surface-variant">Arsip dokumen digital yang telah resmi dibubuhi tanda tangan dan segel elektronik</p>
    </div>

    <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/40 shadow-sm flex flex-col overflow-hidden">

      <div class="p-5 border-b border-outline-variant/30 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-surface-container-lowest">
        <div class="relative w-full lg:w-96">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-on-surface-variant">
            <span class="material-symbols-outlined text-lg">search</span>
          </div>

          <input class="w-full h-10 pl-10 pr-4 text-sm bg-surface-container-low/40 rounded-lg border border-outline-variant/50 focus:border-secondary focus:ring-1 focus:ring-secondary text-primary placeholder-on-surface-variant/60 font-body-md" placeholder="Cari judul atau nomor dokumen..." type="text">
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low/60 border-b border-outline-variant/40 text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
              <th class="py-3.5 px-6 w-14 text-center">No</th>
              <th class="py-3.5 px-6">Judul Dokumen</th>
              <th class="py-3.5 px-6 w-[22%]">Nama Pemohon</th>
              <th class="py-3.5 px-6 w-[20%]">Waktu Ditandatangani</th>
              <th class="py-3.5 px-6 text-right w-[180px]">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-outline-variant/20 font-body-md text-body-md text-on-surface">
            <tr class="hover:bg-surface-container-low/40 transition-colors">
              <td class="py-4 px-6 text-center font-label-md text-on-surface-variant">1</td>
              <td class="py-4 px-6">
                <span class="font-semibold text-primary font-headline-sm text-[15px] leading-snug">Lembar Pengesahan Skripsi</span>
              </td>
              <td class="py-4 px-6">
                <span class="font-medium text-primary text-sm">Farhan Maulana</span>
              </td>
              <td class="py-4 px-6">
                <div class="flex flex-col">
                  <span class="text-xs font-medium text-on-surface">24 Feb 2026</span>
                  <span class="text-xs text-on-surface-variant mt-0.5">09:45 WIB</span>
                </div>
              </td>
              <td class="py-4 px-6 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button class="inline-flex items-center gap-1.5 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-semibold border border-outline-variant/50 text-primary hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-sm">visibility</span>
                    <span>Detail</span>
                  </button>

                  <button class="inline-flex items-center gap-1.5 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#004B87] hover:bg-[#003865] text-white shadow-xs transition-colors">
                    <span class="material-symbols-outlined text-sm">verified_user</span>
                    <span>Unduh PDF</span>
                  </button>
                </div>
              </td>
            </tr>

            <tr class="hover:bg-surface-container-low/40 transition-colors">
              <td class="py-4 px-6 text-center font-label-md text-on-surface-variant">2</td>
              <td class="py-4 px-6">
                <span class="font-semibold text-primary font-headline-sm text-[15px] leading-snug">Berita Acara Sidang Tugas Akhir</span>
              </td>
              <td class="py-4 px-6">
                <span class="font-medium text-primary text-sm">Rizky Ananda</span>
              </td>
              <td class="py-4 px-6">
                <div class="flex flex-col">
                  <span class="text-xs font-medium text-on-surface">24 Feb 2026</span>
                  <span class="text-xs text-on-surface-variant mt-0.5">08:30 WIB</span>
                </div>
              </td>
              <td class="py-4 px-6 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-outline-variant/50 text-primary hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-sm">visibility</span>
                    <span>Detail</span>
                  </button>

                  <button class="inline-flex items-center gap-1.5 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#004B87] hover:bg-[#003865] text-white shadow-xs transition-colors">
                    <span class="material-symbols-outlined text-sm">verified_user</span>
                    <span>Unduh PDF</span>
                  </button>
                </div>
              </td>
            </tr>

            <tr class="hover:bg-surface-container-low/40 transition-colors">
              <td class="py-4 px-6 text-center font-label-md text-on-surface-variant">3</td>
              <td class="py-4 px-6">
                <span class="font-semibold text-primary font-headline-sm text-[15px] leading-snug">Surat Keterangan Bebas Kompen</span>
              </td>
              <td class="py-4 px-6">
                <span class="font-medium text-primary text-sm">Nabila Safitri</span>
              </td>
              <td class="py-4 px-6">
                <div class="flex flex-col">
                  <span class="text-xs font-medium text-on-surface">22 Feb 2026</span>
                  <span class="text-xs text-on-surface-variant mt-0.5">13:10 WIB</span>
                </div>
              </td>
              <td class="py-4 px-6 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-outline-variant/50 text-primary hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-sm">visibility</span>
                    <span>Detail</span>
                  </button>

                  <button class="inline-flex items-center gap-1.5 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#004B87] hover:bg-[#003865] text-white shadow-xs transition-colors">
                    <span class="material-symbols-outlined text-sm">verified_user</span>
                    <span>Unduh PDF</span>
                  </button>
                </div>
              </td>
            </tr>

            <tr class="hover:bg-surface-container-low/40 transition-colors">
              <td class="py-4 px-6 text-center font-label-md text-on-surface-variant">4</td>
              <td class="py-4 px-6">
                <span class="font-semibold text-primary font-headline-sm text-[15px] leading-snug">Formulir Persetujuan Seminar Proposal</span>
              </td>
              <td class="py-4 px-6">
                <span class="font-medium text-primary text-sm">Dimas Aditya</span>
              </td>
              <td class="py-4 px-6">
                <div class="flex flex-col">
                  <span class="text-xs font-medium text-on-surface">21 Feb 2026</span>
                  <span class="text-xs text-on-surface-variant mt-0.5">15:20 WIB</span>
                </div>
              </td>
              <td class="py-4 px-6 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-outline-variant/50 text-primary hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-sm">visibility</span>
                    <span>Detail</span>
                  </button>

                  <button class="inline-flex items-center gap-1.5 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#004B87] hover:bg-[#003865] text-white shadow-xs transition-colors">
                    <span class="material-symbols-outlined text-sm">verified_user</span>
                    <span>Unduh PDF</span>
                  </button>
                </div>
              </td>
            </tr>

            <tr class="hover:bg-surface-container-low/40 transition-colors">
              <td class="py-4 px-6 text-center font-label-md text-on-surface-variant">5</td>
              <td class="py-4 px-6">
                <span class="font-semibold text-primary font-headline-sm text-[15px] leading-snug">Surat Rekomendasi Magang MBKM</span>
              </td>
              <td class="py-4 px-6">
                <span class="font-medium text-primary text-sm">Alya Putri</span>
              </td>
              <td class="py-4 px-6">
                <div class="flex flex-col">
                  <span class="text-xs font-medium text-on-surface">20 Feb 2026</span>
                  <span class="text-xs text-on-surface-variant mt-0.5">10:15 WIB</span>
                </div>
              </td>
              <td class="py-4 px-6 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-outline-variant/50 text-primary hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-sm">visibility</span>
                    <span>Detail</span>
                  </button>

                  <button class="inline-flex items-center gap-1.5 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#004B87] hover:bg-[#003865] text-white shadow-xs transition-colors">
                    <span class="material-symbols-outlined text-sm">verified_user</span>
                    <span>Unduh PDF</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="p-4 px-6 border-t border-outline-variant/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-surface-container-lowest">
        <p class="font-body-sm text-body-sm text-on-surface-variant">
          Menampilkan <span class="font-semibold text-primary">1-5</span> dari <span class="font-semibold text-primary">32</span> dokumen
        </p>

        <div class="flex items-center gap-2">
          <button class="px-3 py-1.5 rounded-lg border border-outline-variant/40 text-xs font-medium text-on-surface-variant hover:bg-surface-container-low disabled:opacity-40 disabled:pointer-events-none transition-colors inline-flex items-center gap-1" disabled>
            <span class="material-symbols-outlined text-sm">chevron_left</span>
            <span>Sebelumnya</span>
          </button>

          <div class="flex items-center gap-1">
            <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-semibold bg-on-surface text-surface">1</span>
            <button class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-semibold text-on-surface-variant hover:bg-surface-container-low transition-colors">2</button>
            <button class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-semibold text-on-surface-variant hover:bg-surface-container-low transition-colors">3</button>
          </div>

          <button class="px-3 py-1.5 rounded-lg border border-outline-variant/40 text-xs font-medium text-primary hover:bg-surface-container-low transition-colors inline-flex items-center gap-1">
            <span>Selanjutnya</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
          </button>
        </div>
      </div>

    </section>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>


<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 p-8 w-full flex flex-col gap-6">

    <div class="mb-6">
      <h1 class="text-2xl font-bold text-primary tracking-tight font-headline-lg">Dokumen Diunggah</h1>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1">Daftar berkas yang telah Anda ajukan untuk proses penandatanganan elektronik</p>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-none overflow-hidden">

      <div class="p-5 border-b border-surface-container-high flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-surface-container-lowest">
        <div class="relative flex-1 max-w-md">
          <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-on-surface-variant">
            <span class="material-symbols-outlined text-[20px]">search</span>
          </span>
          <input class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all" placeholder="Cari judul atau nomor dokumen..." type="text">
        </div>

        <div class="flex items-center gap-3 flex-wrap">
          <div class="flex items-center gap-1 p-1 bg-surface-container rounded-lg">
            <button class="px-3.5 py-1.5 rounded-md font-label-md text-label-md font-semibold bg-surface-container-lowest text-primary shadow-sm transition-colors">
              Semua (18)
            </button>
            <button class="px-3.5 py-1.5 rounded-md font-label-md text-label-md font-medium text-on-surface-variant hover:text-primary transition-colors">
              Selesai (14)
            </button>
            <button class="px-3.5 py-1.5 rounded-md font-label-md text-label-md font-medium text-on-surface-variant hover:text-primary transition-colors">
              Sedang Diproses (4)
            </button>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant h-11">
              <th class="py-3 px-5 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider w-14 text-center">No</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Judul Dokumen</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Tanggal Pengajuan</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider min-w-[190px]">Progres TTE</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-center">Status</th>
              <th class="py-3 px-6 font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider text-right min-w-[260px]">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-surface-container font-body-md text-body-md">
            <tr class="hover:bg-surface-container-low/50 transition-colors group">
              <td class="py-4 px-5 text-center font-code-tabular text-code-tabular text-on-surface-variant">1</td>
              <td class="py-4 px-6">
                <div class="font-headline-sm text-[15px] font-semibold text-primary hover:text-secondary cursor-pointer transition-colors leading-tight">
                  Lembar Pengesahan Skripsi
                </div>
              </td>
              <td class="py-4 px-6 font-code-tabular text-code-tabular text-on-surface-variant whitespace-nowrap">
                <div>24 Feb 2026</div>
                <div class="text-[11px] text-outline">09:30 WIB</div>
              </td>
              <td class="py-4 px-6">
                <div class="w-full max-w-[160px]">
                  <div class="flex justify-between items-center mb-1.5">
                    <span class="font-label-sm text-[11px] font-semibold text-[#15803d]">2/2 Selesai</span>
                    <span class="font-code-tabular text-[11px] text-[#15803d] font-bold">100%</span>
                  </div>
                  <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                    <div class="bg-[#15803d] h-2 rounded-full w-full"></div>
                  </div>
                </div>
              </td>
              <td class="py-4 px-6 text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]">
                  <span class="material-symbols-outlined text-[14px]">check</span>
                  <span>Selesai</span>
                </span>
              </td>
              <td class="py-4 px-6 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-[12px] font-medium transition-colors">
                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                    <span>Detail</span>
                  </button>
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-[12px] font-medium transition-colors">
                    <span class="material-symbols-outlined text-[15px]">download</span>
                    <span>Original</span>
                  </button>
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#15803d] text-white hover:bg-[#003865] font-label-md text-[12px] font-medium shadow-sm transition-all">
                    <span class="material-symbols-outlined text-[15px]">download</span>
                    <span>Signed</span>
                  </button>
                </div>
              </td>
            </tr>

            <tr class="hover:bg-surface-container-low/50 transition-colors group">
              <td class="py-4 px-5 text-center font-code-tabular text-code-tabular text-on-surface-variant">2</td>
              <td class="py-4 px-6">
                <div class="font-headline-sm text-[15px] font-semibold text-primary hover:text-secondary cursor-pointer transition-colors leading-tight">
                  Surat Keterangan Aktif Kuliah
                </div>
              </td>
              <td class="py-4 px-6 font-code-tabular text-code-tabular text-on-surface-variant whitespace-nowrap">
                <div>23 Feb 2026</div>
                <div class="text-[11px] text-outline">14:15 WIB</div>
              </td>
              <td class="py-4 px-6">
                <div class="w-full max-w-[160px]">
                  <div class="flex justify-between items-center mb-1.5">
                    <span class="font-label-sm text-[11px] font-semibold text-[#1d4ed8]">1/2 Signer</span>
                    <span class="font-code-tabular text-[11px] text-[#1d4ed8] font-bold">50%</span>
                  </div>
                  <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                    <div class="bg-secondary-container h-2 rounded-full w-1/2"></div>
                  </div>
                </div>
              </td>
              <td class="py-4 px-6 text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-[#dbeafe] text-[#1d4ed8] border border-[#bfdbfe]">
                  <span class="material-symbols-outlined text-[14px]">sync</span>
                  <span>Diproses</span>
                </span>
              </td>
              <td class="py-4 px-6 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-[12px] font-medium transition-colors">
                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                    <span>Detail</span>
                  </button>
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-[12px] font-medium transition-colors">
                    <span class="material-symbols-outlined text-[15px]">download</span>
                    <span>Original</span>
                  </button>
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-surface-container text-outline cursor-not-allowed font-label-md text-[12px] font-medium" disabled title="Dokumen belum selesai ditandatangani semua pihak">
                    <span class="material-symbols-outlined text-[15px]">download</span>
                    <span>Signed</span>
                  </button>
                </div>
              </td>
            </tr>

            <tr class="hover:bg-surface-container-low/50 transition-colors group">
              <td class="py-4 px-5 text-center font-code-tabular text-code-tabular text-on-surface-variant">3</td>
              <td class="py-4 px-6">
                <div class="font-headline-sm text-[15px] font-semibold text-primary hover:text-secondary cursor-pointer transition-colors leading-tight">
                  Permohonan Izin Riset Magang Industri
                </div>
              </td>
              <td class="py-4 px-6 font-code-tabular text-code-tabular text-on-surface-variant whitespace-nowrap">
                <div>21 Feb 2026</div>
                <div class="text-[11px] text-outline">10:00 WIB</div>
              </td>
              <td class="py-4 px-6">
                <div class="w-full max-w-[160px]">
                  <div class="flex justify-between items-center mb-1.5">
                    <span class="font-label-sm text-[11px] font-semibold text-[#15803d]">1/1 Selesai</span>
                    <span class="font-code-tabular text-[11px] text-[#15803d] font-bold">100%</span>
                  </div>
                  <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                    <div class="bg-[#15803d] h-2 rounded-full w-full"></div>
                  </div>
                </div>
              </td>
              <td class="py-4 px-6 text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]">
                  <span class="material-symbols-outlined text-[14px]">check</span>
                  <span>Selesai</span>
                </span>
              </td>
              <td class="py-4 px-6 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-[12px] font-medium transition-colors">
                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                    <span>Detail</span>
                  </button>
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-[12px] font-medium transition-colors">
                    <span class="material-symbols-outlined text-[15px]">download</span>
                    <span>Original</span>
                  </button>
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#15803d] text-white hover:bg-[#003865] font-label-md text-[12px] font-medium shadow-sm transition-all">
                    <span class="material-symbols-outlined text-[15px]">download</span>
                    <span>Signed</span>
                  </button>
                </div>
              </td>
            </tr>

            <tr class="hover:bg-surface-container-low/50 transition-colors group">
              <td class="py-4 px-5 text-center font-code-tabular text-code-tabular text-on-surface-variant">4</td>
              <td class="py-4 px-6">
                <div class="font-headline-sm text-[15px] font-semibold text-primary hover:text-secondary cursor-pointer transition-colors leading-tight">
                  Proposal Pengabdian Masyarakat
                </div>
              </td>
              <td class="py-4 px-6 font-code-tabular text-code-tabular text-on-surface-variant whitespace-nowrap">
                <div>20 Feb 2026</div>
                <div class="text-[11px] text-outline">11:20 WIB</div>
              </td>
              <td class="py-4 px-6">
                <div class="w-full max-w-[160px]">
                  <div class="flex justify-between items-center mb-1.5">
                    <span class="font-label-sm text-[11px] font-semibold text-[#15803d]">3/3 Selesai</span>
                    <span class="font-code-tabular text-[11px] text-[#15803d] font-bold">100%</span>
                  </div>
                  <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                    <div class="bg-[#15803d] h-2 rounded-full w-full"></div>
                  </div>
                </div>
              </td>
              <td class="py-4 px-6 text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]">
                  <span class="material-symbols-outlined text-[14px]">check</span>
                  <span>Selesai</span>
                </span>
              </td>
              <td class="py-4 px-6 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-[12px] font-medium transition-colors">
                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                    <span>Detail</span>
                  </button>
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-[12px] font-medium transition-colors">
                    <span class="material-symbols-outlined text-[15px]">download</span>
                    <span>Original</span>
                  </button>
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#15803d] text-white hover:bg-[#003865] font-label-md text-[12px] font-medium shadow-sm transition-all">
                    <span class="material-symbols-outlined text-[15px]">download</span>
                    <span>Signed</span>
                  </button>
                </div>
              </td>
            </tr>

            <tr class="hover:bg-surface-container-low/50 transition-colors group">
              <td class="py-4 px-5 text-center font-code-tabular text-code-tabular text-on-surface-variant">5</td>
              <td class="py-4 px-6">
                <div class="font-headline-sm text-[15px] font-semibold text-primary hover:text-secondary cursor-pointer transition-colors leading-tight">
                  Surat Rekomendasi Beasiswa Prestasi
                </div>
              </td>
              <td class="py-4 px-6 font-code-tabular text-code-tabular text-on-surface-variant whitespace-nowrap">
                <div>19 Feb 2026</div>
                <div class="text-[11px] text-outline">16:45 WIB</div>
              </td>
              <td class="py-4 px-6">
                <div class="w-full max-w-[160px]">
                  <div class="flex justify-between items-center mb-1.5">
                    <span class="font-label-sm text-[11px] font-semibold text-[#BA1A1A]">0/1 Signer</span>
                    <span class="font-code-tabular text-[11px] text-[#BA1A1A] font-bold">0%</span>
                  </div>
                  <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                    <div class="bg-amber-400 h-2 rounded-full w-0"></div>
                  </div>
                </div>
              </td>
              <td class="py-4 px-6 text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-[#FFDAD6] text-[#BA1A1A] border border-[#FFC5C5]">
                  <span class="material-symbols-outlined text-[14px]">cancel</span>
                  <span>Ditolak</span>
                </span>
              </td>
              <td class="py-4 px-6 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-[12px] font-medium transition-colors">
                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                    <span>Detail</span>
                  </button>
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border border-outline-variant bg-surface-container-lowest text-on-surface hover:bg-surface-container-low font-label-md text-[12px] font-medium transition-colors">
                    <span class="material-symbols-outlined text-[15px]">download</span>
                    <span>Original</span>
                  </button>
                  <button class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-surface-container text-outline cursor-not-allowed font-label-md text-[12px] font-medium" disabled title="Dokumen belum selesai ditandatangani semua pihak">
                    <span class="material-symbols-outlined text-[15px]">download</span>
                    <span>Signed</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="p-4 border-t border-surface-container-high bg-surface-container-lowest flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="font-body-md text-body-md text-on-surface-variant">
          Menampilkan <span class="font-semibold text-primary">1-5</span> dari <span class="font-semibold text-primary">18</span> dokumen
        </div>

        <div class="flex items-center gap-2">
          <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-outline hover:text-primary hover:bg-surface-container-low font-label-md text-label-md transition-colors disabled:opacity-50 disabled:pointer-events-none" disabled>
            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            <span>Sebelumnya</span>
          </button>

          <div class="flex items-center gap-1">
            <span class="w-8 h-8 rounded-lg bg-primary-container text-on-primary font-label-md text-label-md font-bold flex items-center justify-center">1</span>
            <button class="w-8 h-8 rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md flex items-center justify-center transition-colors">2</button>
          </div>

          <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-primary hover:bg-surface-container-low font-label-md text-label-md font-semibold transition-colors">
            <span>Selanjutnya</span>
            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
          </button>
        </div>
      </div>

    </div>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>


<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 p-8 w-full flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Beranda</h1>
      </div>
    </div>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">

      <a class="group rounded-xl border border-outline-variant bg-surface-container-lowest p-4 transition-all hover:-translate-y-0.5 hover:border-secondary hover:shadow-md" href="/Tandain/document/create">
        <div class="flex h-32 items-center justify-center rounded-lg bg-[#e8e7ff] text-[#5145d8] transition-colors group-hover:bg-[#dedcff]">
          <span class="material-symbols-outlined text-[64px]" aria-hidden="true">upload_file</span>
        </div>
        <div class="mt-3">
          <h2 class="font-label-lg text-label-lg font-semibold text-primary">Ajukan Dokumen</h2>
          <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Unggah draf dokumen baru untuk pengesahan</p>
        </div>
      </a>

      <a class="group rounded-xl border border-outline-variant bg-surface-container-lowest p-4 transition-all hover:-translate-y-0.5 hover:border-secondary hover:shadow-md" href="/Tandain/document/signed">
        <div class="flex h-32 items-center justify-center rounded-lg bg-[#dceafe] text-[#1d4ed8] transition-colors group-hover:bg-[#cfe0fc]">
          <span class="material-symbols-outlined text-[64px]" aria-hidden="true">draw</span>
        </div>
        <div class="mt-3">
          <h2 class="font-label-lg text-label-lg font-semibold text-primary">Review &amp; Tandatangani</h2>
          <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Periksa dan bubuhkan TTE digital</p>
        </div>
      </a>

      <a class="group rounded-xl border border-outline-variant bg-surface-container-lowest p-4 transition-all hover:-translate-y-0.5 hover:border-secondary hover:shadow-md" href="/Tandain/document">
        <div class="flex h-32 items-center justify-center rounded-lg bg-[#dcfce7] text-[#15803d] transition-colors group-hover:bg-[#c8f7d8]">
          <span class="material-symbols-outlined text-[64px]" aria-hidden="true">folder_shared</span>
        </div>
        <div class="mt-3">
          <h2 class="font-label-lg text-label-lg font-semibold text-primary">Daftar Dokumen</h2>
          <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Telusuri seluruh arsip &amp; berkas Anda</p>
        </div>
      </a>

    </section>

    <section class="rounded-xl bg-surface-container-lowest border border-outline-variant shadow-sm overflow-hidden flex flex-col">

      <div class="p-6 border-b border-outline-variant flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface-container-lowest">
        <div class="flex flex-col gap-1">
          <h2 class="font-headline-sm text-headline-sm font-bold text-primary">Aktivitas Dokumen Terbaru</h2>
          <p class="font-body-sm text-body-sm text-on-surface-variant">Daftar verifikasi dan status pengesahan berkas digital Anda.</p>
        </div>

        <div class="flex items-center gap-1 p-1 bg-surface-container-low rounded-lg">
          <button class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-surface-container-lowest text-primary font-semibold shadow-sm text-label-md transition-all">
            <span>Perlu Tanda Tangan Anda</span>
            <span class="px-2 py-0.2 text-[11px] font-bold bg-[#fef3c7] text-[#b45309] border border-[#fde68a] rounded-full">3</span>
          </button>

          <button class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-lowest/60 font-medium text-label-md transition-colors">
            <span>Pengajuan Saya</span>
          </button>

          <button class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-lowest/60 font-medium text-label-md transition-colors">
            <span>Semua</span>
          </button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant text-[13px] font-label-md uppercase tracking-wider text-on-surface-variant">
              <th class="py-3.5 px-6 font-semibold">Judul Dokumen</th>
              <th class="py-3.5 px-6 font-semibold">Pemohon</th>
              <th class="py-3.5 px-6 font-semibold text-right">Tanggal Diajukan</th>
              <th class="py-3.5 px-6 font-semibold text-center">Status</th>
              <th class="py-3.5 px-6 font-semibold text-right">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-outline-variant/60 font-body-sm text-body-sm">
            <tr class="hover:bg-surface-container-low/60 transition-colors group">
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <div>
                    <a class="font-label-lg text-label-lg font-semibold text-primary hover:text-secondary transition-colors block" href="#">Lembar Pengesahan Skripsi</a>
                  </div>
                </div>
              </td>

              <td class="py-4 px-6">
                <div>
                  <p class="font-label-md text-label-md font-semibold text-primary">Farhan Maulana</p>
                  <p class="font-code-tabular text-[12px] text-outline">2207411032</p>
                </div>
              </td>

              <td class="py-4 px-6 text-right font-code-tabular text-on-surface-variant">
                <div>24 Feb 2026</div>
                <div class="text-[12px] text-outline">09:30 WIB</div>
              </td>

              <td class="py-4 px-6 text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-label-sm font-semibold bg-[#fef3c7] text-[#b45309] border border-[#fde68a]">
                  <span class="material-symbols-outlined text-[14px]">hourglass_top</span>
                  Menunggu Giliran Anda
                </span>
              </td>

              <td class="py-4 px-6 text-right">
                <button class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-[#004B87] text-on-primary font-label-md text-label-md font-semibold hover:opacity-90 transition-all shadow-sm active:scale-[0.98]">
                  <span class="material-symbols-outlined text-[16px]">edit_note</span>
                  <span>Review & TTE</span>
                </button>
              </td>
            </tr>

            <tr class="hover:bg-surface-container-low/60 transition-colors group">
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <div>
                    <a class="font-label-lg text-label-lg font-semibold text-primary hover:text-secondary transition-colors block" href="#">Surat Keterangan Aktif Kuliah</a>
                  </div>
                </div>
              </td>

              <td class="py-4 px-6">
                <div>
                  <p class="font-label-md text-label-md font-semibold text-primary">Siti Nurhaliza</p>
                  <p class="font-code-tabular text-[12px] text-outline">2307312015</p>
                </div>
              </td>

              <td class="py-4 px-6 text-right font-code-tabular text-on-surface-variant">
                <div>23 Feb 2026</div>
                <div class="text-[12px] text-outline">14:15 WIB</div>
              </td>

              <td class="py-4 px-6 text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-label-sm font-semibold bg-[#dbeafe] text-[#1d4ed8] border border-[#bfdbfe]">
                  <span class="material-symbols-outlined text-[14px]">sync</span>
                  Sedang Diproses
                </span>
              </td>

              <td class="py-4 px-6 text-right">
                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-outline-variant font-label-md text-label-md text-on-surface hover:bg-surface-container-low transition-colors">
                  <span class="material-symbols-outlined text-[16px]">visibility</span>
                  <span>Lihat Detail</span>
                </button>
              </td>
            </tr>

            <tr class="hover:bg-surface-container-low/60 transition-colors group">
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <div>
                    <a class="font-label-lg text-label-lg font-semibold text-primary hover:text-secondary transition-colors block" href="#">Proposal Pengabdian Masyarakat</a>
                  </div>
                </div>
              </td>

              <td class="py-4 px-6">
                <div>
                  <p class="font-label-md text-label-md font-semibold text-primary">Agus Setiawan, M.Kom.</p>
                  <p class="font-code-tabular text-[12px] text-outline">198403152010121002</p>
                </div>
              </td>

              <td class="py-4 px-6 text-right font-code-tabular text-on-surface-variant">
                <div>20 Feb 2026</div>
                <div class="text-[12px] text-outline">11:20 WIB</div>
              </td>

              <td class="py-4 px-6 text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-label-sm font-semibold bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]">
                  <span class="material-symbols-outlined text-[14px]">check</span>
                  Selesai Ditandatangani
                </span>
              </td>

              <td class="py-4 px-6 text-right">
                <div class="inline-flex items-center gap-1.5">
                  <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-outline-variant font-label-md text-label-md text-on-surface hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                    <span>Lihat Detail</span>
                  </button>
                </div>
              </td>
            </tr>

            <tr class="hover:bg-surface-container-low/60 transition-colors group">
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <div>
                    <a class="font-label-lg text-label-lg font-semibold text-primary hover:text-secondary transition-colors block" href="#">Berita Acara Sidang Tugas Akhir</a>
                  </div>
                </div>
              </td>

              <td class="py-4 px-6">
                <div>
                  <p class="font-label-md text-label-md font-semibold text-primary">Rizky Ananda</p>
                  <p class="font-code-tabular text-[12px] text-outline">2207411005</p>
                </div>
              </td>

              <td class="py-4 px-6 text-right font-code-tabular text-on-surface-variant">
                <div>24 Feb 2026</div>
                <div class="text-[12px] text-outline">08:10 WIB</div>
              </td>

              <td class="py-4 px-6 text-center">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-label-sm font-semibold bg-[#fef3c7] text-[#b45309] border border-[#fde68a]">
                  <span class="material-symbols-outlined text-[14px]">hourglass_top</span>
                  Menunggu Giliran Anda
                </span>
              </td>

              <td class="py-4 px-6 text-right">
                <button class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-[#004B87] text-on-primary font-label-md text-label-md font-semibold hover:opacity-90 transition-all shadow-sm active:scale-[0.98]">
                  <span class="material-symbols-outlined text-[16px]">edit_note</span>
                  <span>Review & TTE</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="p-4 bg-surface-container-low border-t border-outline-variant flex items-center justify-between">
        <span class="font-body-sm text-body-sm text-on-surface-variant">
          Menampilkan <strong class="text-primary font-semibold">4</strong> dari <strong class="text-primary font-semibold">58</strong> permohonan aktif
        </span>

        <div class="flex items-center gap-2">
          <button class="px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface-variant font-label-md text-label-md hover:text-primary transition-colors disabled:opacity-50" disabled>
            Sebelumnya
          </button>

          <span class="px-3 py-1 font-code-tabular text-[12px] font-semibold text-primary">Halaman 1 dari 15</span>

          <button class="px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface-variant font-label-md text-label-md hover:text-primary transition-colors">
            Selanjutnya
          </button>
        </div>
      </div>

    </section>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 w-full p-8 flex flex-col gap-8">
    <section class="flex flex-col gap-1">
      <h1 class="font-headline-lg text-headline-lg font-bold tracking-tight text-primary">Sertifikat Digital</h1>
      <p class="font-body-md text-body-md text-on-surface-variant">Kelola kredensial kriptografis dan passphrase Anda untuk penandatanganan elektronik.</p>
    </section>

    <section class="flex items-center gap-6 border border-[#cbd5e1] bg-[#dbeafe] p-6 text-on-surface-variant">
      <span class="material-symbols-outlined shrink-0 text-[32px] text-[#004b87]" aria-hidden="true">info</span>
      <p class="font-body-md text-body-md font-semibold">
        Sertifikat digital adalah identitas resmi elektronik Anda di aplikasi Tandain. Sertifikat digital bertindak sebagai stempel dan tanda tangan resmi berbasis kode unik. Fitur ini memastikan identitas Penandatangan valid serta mengunci dokumen secara digital agar keaslian dan keabsahan surat resmi kampus terjamin.
      </p>
    </section>

    <section class="flex flex-col overflow-hidden rounded-lg border border-outline-variant bg-surface-container-lowest shadow-sm">
      <div class="flex flex-col gap-4 border-b border-outline-variant p-6 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex flex-col gap-1">
          <h2 class="flex items-center gap-2 font-headline-sm text-headline-sm font-bold text-primary">
            <span class="material-symbols-outlined text-[20px] text-secondary" aria-hidden="true">document_scanner</span>
            Sertifikat Digital Saya
          </h2>
          <p class="font-body-sm text-body-sm text-on-surface-variant">Daftar seluruh sertifikat digital Anda.</p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] border-collapse text-left">
          <thead>
            <tr class="border-b border-outline-variant bg-surface-container-low text-[13px] font-semibold uppercase tracking-wide text-on-surface-variant">
              <th class="px-5 py-3.5 text-center">Serial Number</th>
              <th class="px-5 py-3.5">Pemohon</th>
              <th class="px-5 py-3.5 text-center">Tanggal Berlaku</th>
              <th class="px-5 py-3.5 text-center">Status</th>
              <th class="px-5 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/60">
            <tr class="text-sm text-on-surface">
              <td class="whitespace-nowrap px-5 py-4 font-label-md text-label-md font-semibold text-primary">CERT-PNJ-TIK-2026-0002</td>
              <td class="px-5 py-4">
                <p class="font-label-md text-label-md font-semibold text-primary">Dr. Bambang W., M.T.</p>
                <p class="font-body-sm text-body-sm text-outline">(NIP: 198003152005011002)</p>
              </td>
              <td class="whitespace-nowrap px-5 py-4 text-right font-body-sm text-body-sm text-on-surface-variant">
                <p>Dibuat: 24 Februari 2026</p>
                <p>Berakhir: 24 Februari 2027</p>
              </td>
              <td class="px-5 py-4 text-center">
                <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-[#bbf7d0] bg-[#dcfce7] px-2.5 py-1 text-[11px] font-bold text-[#15803d]">
                  <span class="material-symbols-outlined text-[14px]" aria-hidden="true">check</span>
                  Sedang Digunakan
                </span>
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center justify-end gap-3">
                  <button type="button" class="inline-flex items-center gap-1.5 rounded border border-outline-variant px-3 py-1.5 font-label-md text-label-md font-semibold text-on-surface transition-colors hover:bg-surface-container-low">
                    <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                    History
                  </button>
                  <button type="button" class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-[#ba1a1a] px-3 py-1.5 font-label-md text-label-md font-semibold text-white shadow-sm transition-colors hover:bg-[#93000a]">
                    <span class="material-symbols-outlined text-[16px]" aria-hidden="true">disabled_by_default</span>
                    Cabut Sertifikat
                  </button>
                </div>
              </td>
            </tr>
            <tr class="text-sm text-on-surface">
              <td class="whitespace-nowrap px-5 py-4 font-label-md text-label-md font-semibold text-primary">CERT-PNJ-TIK-2026-0001</td>
              <td class="px-5 py-4">
                <p class="font-label-md text-label-md font-semibold text-primary">Dr. Bambang W., M.T.</p>
                <p class="font-body-sm text-body-sm text-outline">(NIP: 198003152005011002)</p>
              </td>
              <td class="whitespace-nowrap px-5 py-4 text-right font-body-sm text-body-sm text-on-surface-variant">
                <p>Dibuat: 1 Februari 2026</p>
                <p>Berakhir: 24 Februari 2026</p>
              </td>
              <td class="px-5 py-4 text-center">
                <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-[#f1c5c1] bg-[#fff1f0] px-2.5 py-1 text-[11px] font-semibold text-error">
                  <span class="material-symbols-outlined text-[14px]" aria-hidden="true">cancel</span>
                  Tidak Digunakan
                </span>
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center justify-end gap-3">
                  <button type="button" class="inline-flex items-center gap-1.5 rounded border border-outline-variant px-3 py-1.5 font-label-md text-label-md font-semibold text-on-surface transition-colors hover:bg-surface-container-low">
                    <span class="material-symbols-outlined text-[16px]" aria-hidden="true">visibility</span>
                    History
                  </button>
                  <button type="button" disabled class="inline-flex cursor-not-allowed items-center gap-1.5 whitespace-nowrap rounded bg-[#ba1a1a]/50 px-3 py-1.5 font-label-md text-label-md font-semibold text-white">
                    <span class="material-symbols-outlined text-[16px]" aria-hidden="true">disabled_by_default</span>
                    Cabut Sertifikat
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <footer class="flex flex-col gap-4 border-t border-outline-variant bg-surface-container-low p-4 lg:flex-row lg:items-center lg:justify-between">
        <p class="font-body-sm text-body-sm text-on-surface-variant">
          Menampilkan <strong class="text-primary">4</strong> dari <strong class="text-primary">58</strong> permohonan aktif
        </p>
        <div class="flex flex-wrap items-center justify-center gap-2">
          <button type="button" disabled class="rounded border border-outline-variant bg-white px-3 py-1.5 font-label-md text-label-md font-semibold text-on-surface-variant opacity-50">Sebelumnya</button>
          <span class="px-3 py-1 font-label-md text-label-md font-semibold text-primary">Halaman 1 dari 15</span>
          <button type="button" class="rounded border border-outline-variant bg-white px-3 py-1.5 font-label-md text-label-md font-semibold text-on-surface transition-colors hover:bg-surface-container-lowest">Selanjutnya</button>
        </div>
        <a href="/Tandain/certificate/create" class="inline-flex items-center justify-center gap-1.5 rounded bg-[#004b87] px-3.5 py-2 font-label-md text-label-md font-semibold text-white shadow-sm transition-colors hover:bg-[#003865]">
          <span class="material-symbols-outlined text-[16px]" aria-hidden="true">note_add</span>
          Buat Sertifikat Baru
        </a>
      </footer>
    </section>
  </main>

<?php require_once 'views/layouts/footer.php'; ?>
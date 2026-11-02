<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 p-8 w-full flex flex-col gap-6">

    <div class="mb-8">
      <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight font-bold">Ajukan Dokumen Baru</h1>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1">Unggah berkas PDF dan tentukan alur pihak penandatangan</p>
    </div>

    <form class="space-y-6" method="POST" action="/Tandain/document/create" enctype="multipart/form-data">

      <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">

        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-surface-container">
          <span class="material-symbols-outlined text-secondary text-[22px]">description</span>
          <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Berkas & Informasi Dokumen</h2>
        </div>

        <div class="border-2 border-dashed border-outline-variant hover:border-secondary rounded-xl p-8 text-center bg-surface hover:bg-surface-container-low transition-colors cursor-pointer mb-6 group">
          <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-surface-container flex items-center justify-center text-primary-container group-hover:bg-secondary-fixed transition-colors">
            <span class="material-symbols-outlined text-[32px] text-error" style="font-variation-settings: 'FILL' 1;">picture_as_pdf</span>
          </div>

          <p class="font-label-lg text-label-lg text-primary font-semibold">Tarik dan lepas berkas PDF ke sini, atau klik Telusuri File</p>
          <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 mb-4">Maksimal ukuran berkas 10 MB (Format PDF murni)</p>

          <button class="inline-flex items-center gap-2 px-4 py-2 border border-outline rounded-lg text-primary-container font-label-md text-label-md bg-white hover:bg-surface-container transition-all" type="button">
            <span class="material-symbols-outlined text-[18px]">folder_open</span>
            <span>Telusuri File</span>
          </button>
        </div>

        <div>
          <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="doc-title">
            Judul Dokumen <span class="text-error">*</span>
          </label>
          <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition" id="doc-title" name="title" placeholder="Contoh: Surat Rekomendasi Magang MBKM" type="text" required>
        </div>

      </section>

      <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 mb-5 border-b border-surface-container">
          <div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-secondary text-[22px]">route</span>
              <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Alur Pihak Penandatangan</h2>
            </div>

            <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Tentukan pihak berwenang dan urutan penandatanganan dokumen elektronik</p>
          </div>

          <div class="flex items-start md:items-center gap-3 rounded-lg">
            <label class="relative inline-flex items-center cursor-pointer">
              <input checked class="sr-only peer" type="checkbox" name="is_sequential" value="1">
              <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </label>

            <div>
              <span class="font-label-md text-label-md text-primary block leading-tight font-semibold">Penandatanganan dokumen harus mengikuti urutan</span>
              <span class="text-xs text-on-surface-variant font-normal block leading-relaxed">Jika aktif, berkas akan diteruskan bertahap dari penandatangan pertama ke berikutnya</span>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-5">
          <button class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-label-md text-label-md font-semibold shadow-sm transition" type="button">
            <span class="material-symbols-outlined text-[18px] mr-1">add</span>
            <span>Tambah Penandatangan</span>
          </button>

          <button class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-outline-variant bg-white hover:bg-surface-container-low text-primary-container font-label-md text-label-md font-semibold transition" type="button">
            <span>Tambahkan Saya Sendiri</span>
          </button>
        </div>

        <div class="space-y-3">

          <div class="flex items-center justify-between p-4 rounded-xl border border-outline-variant bg-surface-container-lowest hover:border-blue-500 transition gap-4">
            <div class="flex items-center gap-4 flex-1 min-w-0">
              <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0">1</div>

              <div class="flex-1 min-w-0">
                <p class="font-label-lg text-label-lg font-bold text-primary truncate">Anita Hidayati, S.Kom., M.Kom.</p>
                <p class="font-body-sm text-body-sm text-on-surface-variant truncate mt-0.5">anita.h@pnj.ac.id • NIP 197908102008122002</p>
              </div>
            </div>

            <button class="p-1.5 rounded-lg text-error hover:bg-error-container/40 transition shrink-0" title="Hapus Baris" type="button">
              <span class="material-symbols-outlined text-[20px]">delete</span>
            </button>
          </div>

          <div class="flex items-center justify-between p-4 rounded-xl border border-outline-variant bg-surface-container-lowest hover:border-blue-500 transition gap-4">
            <div class="flex items-center gap-4 flex-1 min-w-0">
              <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0">2</div>

              <div class="flex-1 min-w-0">
                <p class="font-label-lg text-label-lg font-bold text-primary truncate">Dr. Bambang W., M.T.</p>
                <p class="font-body-sm text-body-sm text-on-surface-variant truncate mt-0.5">bambang.w@pnj.ac.id • NIP 197405121999031001</p>
              </div>
            </div>

            <button class="p-1.5 rounded-lg text-error hover:bg-error-container/40 transition shrink-0" title="Hapus Baris" type="button">
              <span class="material-symbols-outlined text-[20px]">delete</span>
            </button>
          </div>

          <div class="flex flex-col md:flex-row items-center justify-between p-4 rounded-xl border border-dashed border-outline bg-surface gap-4">
            <div class="flex items-center gap-4 flex-1 w-full">
              <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0">3</div>

              <div class="flex-1 flex gap-2">
                <div class="relative flex-1">
                  <input class="w-full pl-9 pr-3 py-2 rounded-lg border border-outline-variant bg-white font-body-sm text-body-sm text-primary focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none" placeholder="Cari NIP/NIM atau email resmi PNJ (@pnj.ac.id)" type="text">
                  <span class="material-symbols-outlined absolute left-2.5 top-2.5 text-[18px] text-on-surface-variant">person_search</span>
                </div>

                <button class="px-3.5 py-2 bg-blue-600 text-white rounded-lg font-label-md text-label-md flex items-center gap-1 hover:bg-blue-700 transition shadow-sm shrink-0" type="button">
                  <span class="material-symbols-outlined text-[16px]">search</span>
                  <span>Cari</span>
                </button>
              </div>
            </div>

            <button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition shrink-0 self-end md:self-center" title="Batalkan" type="button">
              <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
          </div>

        </div>

      </section>

      <div class="flex justify-between items-center pt-5 border-t border-outline-variant">
        <a href="/Tandain/dashboard" class="border border-outline-variant text-on-surface-variant hover:bg-surface-container hover:text-on-surface px-6 py-2.5 rounded-lg font-label-lg text-label-lg font-medium transition inline-block">
          Batal
        </a>

        <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-lg font-label-lg text-label-lg font-semibold shadow-sm flex items-center gap-2 transition active:scale-[0.99]" type="submit">
          <span>Lanjut ke Atur Posisi TTE</span>
          <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
        </button>
      </div>

    </form>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>


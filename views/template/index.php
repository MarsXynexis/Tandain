<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 w-full p-8 flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex flex-wrap items-center gap-3">
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Template Surat</h1>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container text-on-surface-variant border border-outline-variant">Belum Diaktifkan</span>
      </div>
    </div>

    <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-12 shadow-sm flex flex-col items-center justify-center text-center">

      <div class="w-16 h-16 rounded-2xl bg-surface-container-low flex items-center justify-center text-secondary mb-4">
        <span class="material-symbols-outlined text-[36px]">article</span>
      </div>

      <h2 class="font-headline-sm text-headline-sm font-bold text-primary">Modul Template Naskah Dinas</h2>

      <p class="font-body-md text-body-md text-on-surface-variant max-w-md mt-2">
        Pengelolaan template surat belum tersedia. Pengajuan dokumen saat ini dilakukan melalui unggah berkas manual.
      </p>

      <a href="/Tandain/dashboard" class="mt-6 inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-outline-variant text-primary font-label-md text-label-md font-semibold hover:bg-surface-container-low transition-colors">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        <span>Kembali ke Beranda</span>
      </a>

    </section>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

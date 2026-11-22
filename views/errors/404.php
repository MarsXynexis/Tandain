<?php
$backUrl = isset($_SESSION['user']) ? '/Tandain/dashboard' : '/Tandain/login';
$backLabel = isset($_SESSION['user']) ? 'Kembali ke Beranda' : 'Kembali ke Halaman Masuk';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Halaman Tidak Ditemukan - Tandain</title>
  <link rel="icon" type="image/png" href="/Tandain/assets/img/app-icon.png">

  <link href="/Tandain/assets/css/fonts.css" rel="stylesheet">
  <link href="/Tandain/assets/css/material-symbols.css" rel="stylesheet">
  <link href="/Tandain/assets/css/ui.css" rel="stylesheet">

<?php require_once 'views/layouts/tailwind_tokens.php'; ?>
</head>

<body class="bg-background text-on-surface font-body-md antialiased min-h-screen flex items-center justify-center p-6">

  <main class="w-full max-w-md rounded-2xl border border-outline-variant bg-surface-container-lowest p-8 text-center shadow-sm">

    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-container text-primary">
      <span class="material-symbols-outlined text-[30px]" aria-hidden="true">search_off</span>
    </div>

    <p class="font-code-tabular text-label-md font-bold uppercase tracking-wider text-on-surface-variant">404</p>
    <h1 class="mt-1 font-headline-md text-headline-md font-bold text-primary">Halaman Tidak Ditemukan</h1>
    <p class="mt-2 font-body-md text-body-md text-on-surface-variant">Alamat yang Anda buka tidak tersedia atau sudah dipindahkan.</p>

    <div class="mt-6">
      <a href="<?= $backUrl; ?>" class="btn-md inline-flex items-center justify-center gap-2 bg-brand font-semibold text-white transition-colors hover:bg-brand-hover">
        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span>
        <span><?= $backLabel; ?></span>
      </a>
    </div>

  </main>

</body>
</html>

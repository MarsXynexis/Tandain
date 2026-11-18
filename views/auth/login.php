<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= htmlspecialchars($data['title'] ?? 'Login - Tandain', ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="icon" type="image/png" href="/Tandain/assets/img/app-icon.png">

  <link href="/Tandain/assets/css/fonts.css" rel="stylesheet">
  <link href="/Tandain/assets/css/material-symbols.css" rel="stylesheet">
  <link href="/Tandain/assets/css/notices.css" rel="stylesheet">
  <link href="/Tandain/assets/css/ui.css" rel="stylesheet">
  <script src="/Tandain/assets/js/notices.js"></script>

<?php require_once __DIR__ . '/../layouts/tailwind_tokens.php'; ?>

  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #eaf0f6;
    }
  </style>
</head>

<body class="relative min-h-screen flex items-center justify-center overflow-x-hidden p-4 md:p-8 text-slate-800 antialiased selection:bg-secondary selection:text-on-secondary">
  <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
    <div class="absolute inset-0 opacity-[0.28]">
      <img src="/Tandain/assets/img/login-background.png" alt="" class="absolute left-[-32.98%] top-[-18.91%] h-[141.86%] w-[132.98%] max-w-none">
    </div>
  </div>

  <main class="relative z-10 flex min-h-[580px] w-full max-w-[900px] flex-col overflow-hidden rounded-[32px] bg-white shadow-2xl md:flex-row">

    <section class="relative flex w-full flex-col items-center justify-between bg-[#003865] p-6 text-white sm:p-9 md:min-h-[580px] md:w-[46.67%] md:max-w-[420px]">

      <div class="flex w-full items-center gap-4">
        <img src="/Tandain/assets/img/app-icon.png" alt="Logo Tandain" class="h-12 w-12 shrink-0 rounded-xl object-contain drop-shadow-md">

        <div class="min-w-0">
          <p class="text-2xl font-bold leading-tight tracking-tight text-white">Tandain eSign</p>
          <p class="text-xs font-normal text-slate-300">
            Digital Signature Platform <span class="text-slate-400 mx-0.5">•</span> Powered By <span class="font-semibold text-white">Tandain</span>
          </p>
        </div>
      </div>

      <div class="w-full text-center md:text-left">
        <p class="text-xs font-normal tracking-wide text-slate-300">
          &copy; 2026 <span class="text-white font-semibold">Tandain</span>
        </p>
      </div>

    </section>

    <section class="flex w-full min-w-0 flex-col justify-center bg-white p-6 sm:p-8 md:w-[53.33%] md:p-10 lg:p-12">
      <div class="mb-6">
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary">Selamat datang</h1>
        <p class="mt-1 text-sm leading-relaxed text-slate-500">Silahkan masuk menggunakan akun resmi PNJ Anda.</p>
      </div>

      <div class="space-y-3 [&:has([data-notice]:not([hidden]))]:mb-4">
        <?php if (!empty($data['error'])): ?>
          <?php $notice = ['type' => 'error', 'message' => $data['error']]; ?>
          <?php require 'views/layouts/notice.php'; ?>
        <?php endif; ?>

        <?php require 'views/layouts/flash.php'; ?>
      </div>

      <form action="/Tandain/login" class="space-y-3.5" method="POST">

        <div class="space-y-1.5">
          <label class="block text-xs font-bold text-slate-700" for="username">Username</label>
          <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
              <span class="material-symbols-outlined text-[20px]" aria-hidden="true">person</span>
            </div>
            <input class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-[45px] pr-4 text-sm text-slate-900 placeholder:text-slate-400 transition duration-150 focus:border-[#004B87] focus:outline-none focus:ring-2 focus:ring-[#004B87]/20" id="username" name="username" placeholder="Masukkan username" required="" type="text" value="<?= htmlspecialchars($data['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          </div>
        </div>

        <div class="space-y-1.5">
          <label class="block text-xs font-bold text-slate-700" for="password">Password</label>
          <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
              <span class="material-symbols-outlined text-[20px]" aria-hidden="true">lock</span>
            </div>
            <input class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-[45px] pr-12 text-sm text-slate-900 placeholder:text-slate-400 transition duration-150 focus:border-[#004B87] focus:outline-none focus:ring-2 focus:ring-[#004B87]/20" id="password" name="password" placeholder="Masukkan password" required="" type="password">
            <button aria-label="Tampilkan password" aria-pressed="false" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#004B87]/20" id="togglePassword" type="button">
              <span class="material-symbols-outlined text-[20px]" id="eyeIcon" aria-hidden="true">visibility</span>
            </button>
          </div>
        </div>

        <div class="pt-1.5">
          <button class="btn-lg flex w-full bg-brand text-white shadow-md shadow-brand/20 transition hover:bg-brand-hover" type="submit">
            <span>Masuk</span>
          </button>
        </div>

      </form>

      <p class="mt-5 border-t border-slate-200 pt-4 text-center text-xs text-slate-500">
        <a href="/Tandain/verification" aria-label="Klik di sini untuk verifikasi dokumen" class="rounded-sm font-semibold text-brand underline underline-offset-2 hover:text-brand-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">Klik di sini</a>
        untuk verifikasi dokumen.
      </p>
    </section>

  </main>

  <script>
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (toggleBtn && passwordInput) {
      toggleBtn.addEventListener('click', function () {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        toggleBtn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
        toggleBtn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
        eyeIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
      });
    }
  </script>

</body>
</html>

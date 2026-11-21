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
      background-color: #ebf1f8;
    }
  </style>
</head>

<body class="relative min-h-screen flex items-center justify-center overflow-x-hidden p-4 md:p-8 text-slate-800 antialiased selection:bg-secondary selection:text-on-secondary">
  <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
    <svg class="h-full w-full" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice">
      <path d="M 0,0 L 640,0 C 560,260 760,540 520,900 L 0,900 Z" fill="#e1eaf5"/>
      <path d="M 1040,900 C 1130,720 1290,620 1440,560 L 1440,900 Z" fill="#dbe5f2"/>
      <circle cx="1310" cy="110" r="220" fill="none" stroke="#d5e1ef" stroke-width="1.5"/>
      <circle cx="1310" cy="110" r="168" fill="none" stroke="#d5e1ef" stroke-width="1" stroke-dasharray="6 8"/>
      <circle cx="120" cy="790" r="190" fill="none" stroke="#d3dfee" stroke-width="1.5"/>
    </svg>
  </div>

  <main class="relative z-10 flex min-h-[580px] w-full max-w-[920px] flex-col overflow-hidden rounded-[28px] border border-slate-200/90 bg-white shadow-[0_20px_50px_-14px_rgba(0,56,101,0.14)] md:flex-row">

    <div class="flex items-center gap-3.5 bg-[#003865] px-6 py-5 text-white md:hidden">
      <img src="/Tandain/assets/img/app-icon.png" alt="Logo Tandain" class="h-10 w-10 shrink-0 rounded-xl object-contain">
      <div class="min-w-0">
        <p class="text-lg font-bold leading-tight text-white">Tandain eSign</p>
        <p class="text-xs text-slate-200">Digital Signature Platform</p>
      </div>
    </div>

    <section class="hidden md:flex md:flex-col md:w-[47.5%] md:max-w-[436px] md:shrink-0 md:p-3.5 md:pr-0">
      <div class="relative flex-1 min-h-[552px] w-full overflow-hidden rounded-[20px] bg-[#dfebf7]">
        <img src="/Tandain/assets/img/tandain-login-art.jpg" alt="Ilustrasi tanda tangan digital Tandain eSign" class="absolute inset-0 h-full w-full object-cover object-center select-none">

        <div class="absolute inset-x-0 top-0 z-10 p-6 pr-16">
          <div class="flex items-center gap-3.5">
            <img src="/Tandain/assets/img/app-icon.png" alt="Logo Tandain" class="h-11 w-11 shrink-0 rounded-xl object-contain shadow-sm">
            <div class="min-w-0">
              <p class="text-xl font-bold leading-tight tracking-tight text-[#003865]">Tandain eSign</p>
              <p class="text-xs font-medium text-slate-700">
                Digital Signature Platform <span class="mx-0.5 text-slate-500">·</span> Powered By <span class="font-semibold text-[#003865]">Tandain</span>
              </p>
            </div>
          </div>
        </div>

        <svg class="pointer-events-none absolute -bottom-px -right-px -top-px z-20 h-[calc(100%+2px)] w-[76px]" viewBox="0 0 100 580" preserveAspectRatio="none" aria-hidden="true">
          <path d="M 64,0 C 4,132 94,272 34,410 C -4,498 46,554 74,580 L 100,580 L 100,0 Z" fill="#ffffff"/>
        </svg>
      </div>
    </section>

    <section class="flex w-full min-w-0 flex-1 flex-col justify-center bg-white p-6 sm:p-8 md:py-10 md:pl-6 md:pr-10 lg:pr-12">
      <div class="mb-6">
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary">Selamat datang</h1>
        <p class="mt-1 text-sm leading-relaxed text-slate-600">Silakan masuk menggunakan akun resmi PNJ Anda.</p>
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
            <button aria-label="Tampilkan password" aria-pressed="false" class="absolute inset-y-0 right-0 flex items-center rounded-r-xl pr-3.5 text-slate-500 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#004B87]/20" id="togglePassword" type="button">
              <span class="material-symbols-outlined text-[20px]" id="eyeIcon" aria-hidden="true">visibility</span>
            </button>
          </div>
        </div>

        <div class="pt-1.5">
          <button class="btn-lg flex w-full bg-brand text-white transition hover:bg-brand-hover active:bg-[#002b4d]" type="submit">
            <span>Masuk</span>
          </button>
        </div>

      </form>

      <div class="relative my-4 flex items-center justify-center">
        <div class="border-t border-slate-200 w-full"></div>
        <span class="bg-white px-3 text-xs text-slate-500 absolute font-normal">atau</span>
      </div>

      <div>
        <a class="btn-lg flex w-full gap-3 border border-slate-300 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 active:bg-slate-100" href="/Tandain/auth/google">
          <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
            <path d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.15z" fill="#4285F4"></path>
            <path d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z" fill="#34A853"></path>
            <path d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z" fill="#FBBC05"></path>
            <path d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.93 6.72-4.93z" fill="#EA4335"></path>
          </svg>
          <span>Login dengan Google Workspace</span>
        </a>
      </div>

      <div class="mt-4 text-center">
        <p class="mx-auto max-w-[395px] text-[11px] leading-[16.5px] text-slate-500">
          Gunakan email Google yang telah terdaftar pada sistem Tandain.
        </p>
      </div>

      <p class="mt-5 border-t border-slate-200 pt-4 text-center text-xs text-slate-600">
        <a href="/Tandain/verification" aria-label="Klik di sini untuk verifikasi dokumen" class="rounded-sm font-semibold text-brand underline underline-offset-2 hover:text-brand-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">Klik di sini</a>
        untuk verifikasi dokumen.
      </p>

      <p class="mt-2.5 text-center text-[11px] text-slate-500">
        &copy; 2026 Tandain
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

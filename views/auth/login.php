<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= $data['title'] ?? 'Login - TandaIN eSign | Politeknik Negeri Jakarta'; ?></title>

  <link href="/Tandain/assets/css/fonts.css" rel="stylesheet">
  <link href="/Tandain/assets/css/material-symbols.css" rel="stylesheet">

  <script src="/Tandain/assets/js/tailwind.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          },
          colors: {
            navy: {
              900: '#1b204a',
              950: '#141738',
            },
            sso: {
              purple: '#4f42dc',
              hover: '#4336c9',
            },
            google: {
              blue: '#1a73e8',
              hover: '#1565c0',
            }
          },
          borderRadius: {
            '4xl': '2rem',
          }
        }
      }
    }
  </script>
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #eaf0f6;
    }
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      display: inline-block;
      vertical-align: middle;
      line-height: 1;
    }
  </style>
</head>

<body class="relative min-h-screen flex items-center justify-center overflow-x-hidden p-4 md:p-8 text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">
  <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
    <div class="absolute inset-0 opacity-[0.28]">
      <img src="/Tandain/assets/img/login-background.png" alt="" class="absolute left-[-32.98%] top-[-18.91%] h-[141.86%] w-[132.98%] max-w-none">
    </div>
  </div>

  <main class="relative z-10 flex min-h-[580px] w-full max-w-[900px] flex-col overflow-hidden rounded-[32px] bg-white shadow-2xl md:flex-row">

    <section class="relative flex w-full flex-col items-center justify-between bg-[#242953] p-6 text-white sm:p-9 md:min-h-[580px] md:w-[46.67%] md:max-w-[420px]">

      <div class="flex w-full items-center gap-4">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-white/20 bg-white/10 p-px backdrop-blur-sm">
          <span class="material-symbols-outlined text-[24px] text-[#E3DFFF]" aria-hidden="true">draw</span>
        </div>

        <div class="min-w-0">
          <h1 class="text-2xl font-bold leading-tight tracking-tight text-white">Tandain eSign</h1>
          <p class="text-xs font-normal text-slate-300">
            Digital Signature Platform <span class="text-slate-400 mx-0.5">•</span> Powered By <span class="font-semibold text-white">Tandain</span>
          </p>
        </div>
      </div>

      <div class="relative my-8 aspect-[240/236.4] w-full max-w-[240px]  md:my-6">
        <img src="/Tandain/assets/img/logo-pnj.png" alt="Logo Politeknik Negeri Jakarta" class="absolute left-[-21.04%] top-[-5.3%] h-[109.59%] w-[142.08%] max-w-none opacity-[0.84]">
      </div>

      <div class="w-full text-center md:text-left">
        <p class="text-xs font-normal tracking-wide text-slate-400">
          &copy; 2026 <span class="text-slate-200 font-semibold">Tandain</span>
        </p>
      </div>

    </section>

    <section class="flex w-full min-w-0 flex-col justify-center bg-white p-6 sm:p-8 md:w-[53.33%] md:p-10 lg:p-12">
      <div class="mb-7">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Selamat datang</h2>
        <p class="mt-1 text-sm leading-relaxed text-slate-500">Silahkan masuk menggunakan akun resmi PNJ Anda.</p>
      </div>

      <?php if (!empty($data['error'])) : ?>
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl flex items-center gap-2">
          <span class="material-symbols-outlined shrink-0 text-[16px] text-red-500" aria-hidden="true">warning</span>
          <span><?= htmlspecialchars($data['error']); ?></span>
        </div>
      <?php endif; ?>

      <?php require_once 'views/layouts/flash.php'; ?>

      <form action="/Tandain/login" class="space-y-2" method="POST">

        <div class="space-y-1.5">
          <label class="block text-xs font-bold text-slate-700" for="username">Username</label>
          <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
              <span class="material-symbols-outlined text-[20px]" aria-hidden="true">person_outline</span>
            </div>
            <input class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-[45px] pr-4 text-sm text-slate-900 placeholder:text-slate-400 transition duration-150 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500" id="username" name="username" placeholder="Masukkan username" required="" type="text" value="<?= htmlspecialchars($data['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          </div>
        </div>

        <div class="space-y-1.5">
          <label class="block text-xs font-bold text-slate-700" for="password">Password</label>
          <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
              <span class="material-symbols-outlined text-[20px]" aria-hidden="true">lock</span>
            </div>
            <input class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-[45px] pr-12 text-sm text-slate-900 placeholder:text-slate-400 transition duration-150 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500" id="password" name="password" placeholder="Masukkan password" required="" type="password">
            <button aria-label="Tampilkan password" aria-pressed="false" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" id="togglePassword" type="button">
              <span class="material-symbols-outlined text-[20px]" id="eyeIcon" aria-hidden="true">visibility</span>
            </button>
          </div>
        </div>

        <div class="flex justify-center py-1">
          <span class="text-center text-[11px] font-bold leading-[16.5px] text-slate-800 underline">Lupa Password?</span>
        </div>

        <div class="pt-2">
          <button class="w-full h-12 bg-[#483fd4] hover:bg-[#3d34bf] active:bg-[#342ca8] text-white font-medium text-sm rounded-xl transition duration-150 flex items-center justify-center gap-2.5 shadow-md shadow-indigo-600/20" type="submit">
            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">verified_user</span>
            <span>Masuk</span>
          </button>
        </div>

      </form>

      <div class="relative my-5 flex items-center justify-center">
        <div class="border-t border-slate-200 w-full"></div>
        <span class="bg-white px-3 text-xs text-slate-400 absolute font-normal">atau</span>
      </div>

      <div>
        <button class="w-full h-12 bg-[#1a73e8] hover:bg-[#1565c0] active:bg-[#0d47a1] text-white font-medium text-sm rounded-xl transition duration-150 flex items-center justify-center gap-3 shadow-md shadow-blue-500/20" type="button">
          <div class="w-7 h-7 bg-white rounded-md flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" viewBox="0 0 24 24">
              <path d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.15z" fill="#4285F4"></path>
              <path d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z" fill="#34A853"></path>
              <path d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z" fill="#FBBC05"></path>
              <path d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.93 6.72-4.93z" fill="#EA4335"></path>
            </svg>
          </div>
          <span>Login dengan Google Workspace</span>
        </button>
      </div>

      <div class="pt-[15px] text-center">
        <p class="mx-auto max-w-[395px] text-[11px] leading-[16.5px] text-slate-500">
          <a class="font-bold underline text-slate-800 hover:text-indigo-600 transition duration-150" href="#">Klik Disini</a>
          untuk melihat panduan penggunaan akun SSO Google Workspace
        </p>
      </div>
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

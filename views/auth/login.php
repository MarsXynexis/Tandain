<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= $data['title'] ?? 'Login - TandaIN eSign | Politeknik Negeri Jakarta'; ?></title>

  <link href="/Tandain/assets/css/fonts.css" rel="stylesheet">

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
  </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 md:p-8 text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

  <main class="w-full max-w-[960px] bg-white rounded-4xl shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[580px]">

    <section class="w-full md:w-[46%] bg-[#242953] text-white p-8 md:p-10 flex flex-col justify-between relative">

      <div class="flex items-center gap-3">
        <img src="/Tandain/assets/img/logo.png" alt="Logo Tandain" class="w-11 h-11 rounded-xl object-cover shrink-0 shadow-md">

        <div>
          <h1 class="text-2xl font-bold tracking-tight text-white leading-tight">eSign</h1>
          <p class="text-xs text-slate-300 font-normal">
            Digital Signature Platform <span class="text-slate-400 mx-0.5">•</span> Powered By <span class="font-semibold text-white">Tandain</span>
          </p>
        </div>
      </div>

      <div class="my-6 flex justify-center items-center">
        <div class="rounded-2xl shadow-2xl overflow-hidden max-w-[320px] w-full border border-white/10 bg-white">
          <img src="/Tandain/assets/img/login-chat.png" alt="Pratinjau Informasi" class="w-full h-auto object-contain block">
        </div>
      </div>

      <div class="text-center md:text-left">
        <p class="text-xs text-slate-400 tracking-wide font-normal">
          &copy; 2026 <span class="text-slate-200 font-semibold">Tandain</span>
        </p>
      </div>

    </section>

    <section class="w-full md:w-[54%] bg-white p-8 sm:p-10 md:p-12 flex flex-col justify-center">
      <div class="mb-7">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Selamat datang</h2>
        <p class="text-sm text-slate-500 mt-1 leading-relaxed">Silahkan masuk menggunakan akun resmi PNJ Anda.</p>
      </div>

      <?php if (!empty($data['error'])) : ?>
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl flex items-center gap-2">
          <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
          <span><?= htmlspecialchars($data['error']); ?></span>
        </div>
      <?php endif; ?>

      <form action="/Tandain/login" class="space-y-4" method="POST">

        <div class="space-y-1.5">
          <label class="block text-xs font-bold text-slate-700" for="username">Username (NIM / NIP / Email)</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path>
              </svg>
            </div>
            <input class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm placeholder:text-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150" id="username" name="username" placeholder="Masukkan NIM, NIP, atau email" required="" type="text" value="<?= htmlspecialchars($data['username'] ?? ''); ?>">
          </div>
        </div>

        <div class="space-y-1.5">
          <label class="block text-xs font-bold text-slate-700" for="password">Password</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <svg class="w-5 h-5 stroke-[1.75]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <rect height="11" rx="2" ry="2" width="18" x="3" y="11"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke-linecap="round" stroke-linejoin="round"></path>
              </svg>
            </div>
            <input class="w-full pl-11 pr-11 py-2.5 bg-white border border-slate-300 rounded-xl text-sm placeholder:text-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150" id="password" name="password" placeholder="Masukkan password" required="" type="password">
            <button aria-label="Toggle password visibility" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" id="togglePassword" type="button">
              <svg class="w-5 h-5" fill="none" id="eyeIcon" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="space-y-1.5">
          <label class="block text-xs font-bold text-slate-700" for="captcha">Kode Keamanan (Captcha)</label>
          <div class="flex items-center gap-3">
            <img src="/Tandain/captcha" id="captcha-img" alt="Captcha" class="h-10 rounded-xl border border-slate-300 cursor-pointer shrink-0" title="Klik untuk memperbarui captcha">
            <input class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm placeholder:text-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150" id="captcha" name="captcha" placeholder="Ketik kode captcha" required="" type="text" autocomplete="off">
          </div>
        </div>

        <div class="pt-2">
          <button class="w-full h-12 bg-[#483fd4] hover:bg-[#3d34bf] active:bg-[#342ca8] text-white font-medium text-sm rounded-xl transition duration-150 flex items-center justify-center gap-2.5 shadow-md shadow-indigo-600/20" type="submit">
            <svg class="w-5 h-5 stroke-[1.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="m9 12 2 2 4-4" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <span>Login dengan SSO</span>
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

      <div class="mt-4 text-center">
        <p class="text-[11px] text-slate-500 leading-normal">
          <a class="font-bold underline text-slate-800 hover:text-indigo-600 transition duration-150" href="#">Klik Disini</a>
          untuk melihat panduan penggunaan akun SSO Google Workspace
        </p>

        <p class="text-xs text-slate-500 mt-3">
          Belum memiliki akun?
          <a href="/Tandain/register" class="font-bold text-indigo-600 hover:text-indigo-800 transition duration-150">Daftar akun</a>
        </p>
      </div>
    </section>

  </main>

  <script>
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const captchaImg = document.getElementById('captcha-img');

    if (toggleBtn && passwordInput) {
      toggleBtn.addEventListener('click', function () {
        const isPassword = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
      });
    }

    if (captchaImg) {
      captchaImg.addEventListener('click', function () {
        this.src = '/Tandain/captcha?' + Date.now();
      });
    }
  </script>

</body>
</html>

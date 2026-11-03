<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= $data['title'] ?? 'Daftar Akun - Tandain'; ?></title>

  <link href="/Tandain/assets/css/fonts.css" rel="stylesheet">

  <script src="/Tandain/assets/js/tailwind.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
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

  <main class="w-full max-w-[560px] bg-white rounded-3xl shadow-2xl overflow-hidden p-8 sm:p-10">

    <div class="flex items-center gap-3 mb-6">
      <img src="/Tandain/assets/img/logo.png" alt="Logo Tandain" class="w-10 h-10 rounded-xl object-cover shrink-0 shadow-sm">

      <div>
        <h1 class="text-xl font-bold tracking-tight text-slate-900 leading-tight">Tandain</h1>
        <p class="text-xs text-slate-500 font-normal">Platform Tanda Tangan & Segel Elektronik PNJ</p>
      </div>
    </div>

    <div class="mb-6">
      <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Akun Baru</h2>
      <p class="text-sm text-slate-500 mt-1 leading-relaxed">Lengkapi identitas diri Anda untuk membuat akun resmi Tandain.</p>
    </div>

    <?php if (!empty($data['error'])) : ?>
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span><?= htmlspecialchars($data['error']); ?></span>
      </div>
    <?php endif; ?>

    <form action="/Tandain/register" class="space-y-4" method="POST">

      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700" for="full_name">Nama Lengkap</label>
        <input class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm placeholder:text-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150" id="full_name" name="full_name" placeholder="Masukkan nama lengkap beserta gelar" required="" type="text">
      </div>

      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700" for="identity_number">NIP / NIM</label>
        <input class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm placeholder:text-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150" id="identity_number" name="identity_number" placeholder="Masukkan NIP atau NIM resmi" required="" type="text">
      </div>

      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700" for="email">Alamat Email</label>
        <input class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm placeholder:text-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150" id="email" name="email" placeholder="nama@pnj.ac.id" required="" type="email">
      </div>

      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700" for="password">Password</label>
        <input class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm placeholder:text-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150" id="password" name="password" placeholder="Minimal 8 karakter" required="" type="password">
        <?php if (!empty($data['validate'])) : ?>
          <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($data['validate']); ?></p>
        <?php endif; ?>
      </div>

      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700" for="repassword">Konfirmasi Password</label>
        <input class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm placeholder:text-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150" id="repassword" name="repassword" placeholder="Ulangi password di atas" required="" type="password">
      </div>

      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700" for="captcha">Kode Keamanan (Captcha)</label>
        <div class="flex items-center gap-3">
          <img src="/Tandain/captcha" id="captcha-img" alt="Captcha" class="h-10 rounded-xl border border-slate-300 cursor-pointer shrink-0" title="Klik untuk memperbarui captcha">
          <input class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm placeholder:text-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150" id="captcha" name="captcha" placeholder="Ketik kode captcha" required="" type="text" autocomplete="off">
        </div>
      </div>

      <div class="pt-2">
        <button class="w-full h-11 bg-[#483fd4] hover:bg-[#3d34bf] active:bg-[#342ca8] text-white font-semibold text-sm rounded-xl transition duration-150 flex items-center justify-center gap-2 shadow-md shadow-indigo-600/20" type="submit">
          Daftar Akun
        </button>
      </div>

    </form>

    <div class="mt-6 text-center">
      <p class="text-xs text-slate-500">
        Sudah memiliki akun?
        <a href="/Tandain/login" class="font-bold text-indigo-600 hover:text-indigo-800 transition duration-150">Masuk di sini</a>
      </p>
    </div>

  </main>

  <script>
    const captchaImg = document.getElementById('captcha-img');

    if (captchaImg) {
      captchaImg.addEventListener('click', function () {
        this.src = '/Tandain/captcha?' + Date.now();
      });
    }
  </script>

</body>
</html>

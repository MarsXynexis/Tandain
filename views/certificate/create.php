<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 w-full max-w-[1400px] p-8 flex flex-col gap-8">
    <section class="flex flex-col gap-1">
      <h1 class="font-headline-lg text-headline-lg font-bold tracking-tight text-primary">Sertifikat Digital</h1>
      <p class="font-body-md text-body-md text-on-surface-variant">Kelola kredensial kriptografis dan passphrase Anda untuk penandatanganan elektronik.</p>
    </section>

    <section class="flex flex-col gap-5 rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm md:p-7">
      <header class="flex flex-col gap-1 border-b border-surface-container pb-5">
        <h2 class="flex items-center gap-2 font-headline-sm text-headline-sm font-bold text-primary">
          <span class="material-symbols-outlined text-[20px] text-secondary" aria-hidden="true">document_scanner</span>
          Buat Sertifikat Baru
        </h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Terbitkan sertifikat digital baru untuk menandatangani dokumen secara elektronik.</p>
      </header>

      <div class="flex items-start gap-4 rounded-xl border border-blue-200/80 bg-blue-50/70 p-5 text-on-surface-variant shadow-sm">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-[#004B87]">
          <span class="material-symbols-outlined text-[24px]" aria-hidden="true">lock</span>
        </div>

        <div class="flex-1 min-w-0 pt-0.5">
          <p class="font-body-sm text-body-sm leading-relaxed text-on-surface-variant">
            Passphrase ini digunakan setiap kali Anda menandatangani dokumen. Simpan passphrase dengan aman karena tidak dapat di-reset; jika lupa, Anda harus membuat sertifikat baru.
          </p>

          <div class="mt-2 text-xs text-on-surface-variant">
            <p class="font-semibold mb-1">Ketentuan passphrase:</p>
            <ul class="list-disc pl-5 space-y-0.5">
              <li>Minimal 4 karakter</li>
              <li>Kombinasi huruf kecil dan angka</li>
              <li>Berbeda dengan password login akun Anda</li>
            </ul>
          </div>
        </div>
      </div>

      <form class="flex flex-col gap-4" method="POST" action="/Tandain/certificate/create">
        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-semibold text-on-surface-variant" for="passphrase">Passphrase (minimal 8 karakter unik)</label>
          <input class="h-10 w-full rounded-lg border border-outline-variant bg-white px-3 text-sm text-primary placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-1 focus:ring-secondary" id="passphrase" name="passphrase" placeholder="Minimal 8 karakter" type="password" minlength="8" autocomplete="new-password" required>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-semibold text-on-surface-variant" for="passphrase-confirmation">Konfirmasi Passphrase</label>
          <input class="h-10 w-full rounded-lg border border-outline-variant bg-white px-3 text-sm text-primary placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-1 focus:ring-secondary" id="passphrase-confirmation" name="passphrase_confirmation" placeholder="Ulangi passphrase" type="password" minlength="8" autocomplete="new-password" required>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-semibold text-on-surface-variant" for="account-password">Password Akun Anda</label>
          <div class="flex h-10 items-center gap-3 rounded-lg border border-outline-variant bg-white px-3 focus-within:border-secondary focus-within:ring-1 focus-within:ring-secondary">
            <span class="material-symbols-outlined text-[16px] text-outline" aria-hidden="true">lock</span>
            <input class="h-full min-w-0 flex-1 border-0 bg-transparent text-sm text-primary placeholder:text-on-surface-variant/50 focus:outline-none" id="account-password" name="account_password" placeholder="Masukkan Password Anda" type="password" autocomplete="current-password" required>
          </div>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
          <button class="inline-flex items-center gap-1.5 rounded-lg bg-[#004B87] hover:bg-[#003d6d] px-4 py-2 font-label-md text-label-md font-semibold text-white shadow-sm transition-colors focus:outline-none" type="submit">
            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">note_add</span>
            Buat Sertifikat Baru
          </button>
          <a class="inline-flex items-center rounded-lg border border-outline-variant bg-white px-4 py-2 font-label-md text-label-md font-semibold text-on-surface transition-colors hover:bg-surface-container-low" href="/Tandain/certificate">
            Batal
          </a>
        </div>
      </form>
    </section>
  </main>

<?php require_once 'views/layouts/footer.php'; ?>
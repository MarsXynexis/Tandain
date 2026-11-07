<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 w-full max-w-[1400px] p-8 flex flex-col gap-8">
    <section class="flex flex-col gap-1">
      <h1 class="font-headline-lg text-headline-lg font-bold tracking-tight text-primary">Sertifikat Digital</h1>
      <p class="font-body-md text-body-md text-on-surface-variant">Kelola kredensial kriptografis dan passphrase Anda untuk penandatanganan elektronik.</p>
    </section>

    <section class="flex flex-col gap-5 rounded-lg border border-[#cbd5e1] bg-surface-container-lowest p-5 md:p-7">
      <header class="flex flex-col gap-1 border-b border-[#f1f5f9] pb-5">
        <h2 class="flex items-center gap-2 font-headline-sm text-headline-sm font-bold text-primary">
          <span class="material-symbols-outlined text-[20px] text-secondary" aria-hidden="true">document_scanner</span>
          Buat Sertifikat Baru
        </h2>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Terbitkan sertifikat digital baru untuk menandatangani dokumen secara elektronik.</p>
      </header>

      <div class="border border-[#cbd5e1] bg-[#dbeafe] px-3 py-2 font-body-sm text-body-sm text-on-surface-variant">
        <p>Passphrase ini digunakan setiap kali Anda menandatangani dokumen. Simpan passphrase dengan aman karena tidak dapat di-reset; jika lupa, Anda harus membuat sertifikat baru.</p>
        <p class="mt-1">Ketentuan passphrase:</p>
        <ul class="list-disc pl-5">
          <li>Minimal 4 karakter</li>
          <li>Kombinasi huruf kecil dan angka</li>
          <li>Berbeda dengan password login akun Anda</li>
        </ul>
      </div>

      <form class="flex flex-col gap-4" method="POST" action="/Tandain/certificate/create">
        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-semibold text-on-surface-variant" for="passphrase">Passphrase (minimal 8 karakter unik)</label>
          <input class="h-10 w-full rounded-sm border border-[#cbd5e1] bg-white px-3 text-sm text-primary placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-1 focus:ring-secondary" id="passphrase" name="passphrase" placeholder="Minimal 8 karakter" type="password" minlength="8" autocomplete="new-password" required>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-semibold text-on-surface-variant" for="passphrase-confirmation">Konfirmasi Passphrase</label>
          <input class="h-10 w-full rounded-sm border border-[#cbd5e1] bg-white px-3 text-sm text-primary placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-1 focus:ring-secondary" id="passphrase-confirmation" name="passphrase_confirmation" placeholder="Ulangi passphrase" type="password" minlength="8" autocomplete="new-password" required>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-semibold text-on-surface-variant" for="account-password">Password Akun Anda</label>
          <div class="flex h-10 items-center gap-3 rounded-sm border border-[#cbd5e1] bg-white px-3 focus-within:border-secondary focus-within:ring-1 focus-within:ring-secondary">
            <span class="material-symbols-outlined text-[16px] text-outline" aria-hidden="true">lock</span>
            <input class="h-full min-w-0 flex-1 border-0 bg-transparent text-sm text-primary placeholder:text-on-surface-variant/50 focus:outline-none" id="account-password" name="account_password" placeholder="Masukkan Password Anda" type="password" autocomplete="current-password" required>
          </div>
        </div>

        <div class="flex flex-wrap gap-3">
          <button class="inline-flex items-center gap-1.5 rounded bg-[#004b87] px-3.5 py-1.5 font-label-md text-label-md font-semibold text-white shadow-sm transition-colors hover:bg-[#003865] focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2" type="submit">
            <span class="material-symbols-outlined text-[14px]" aria-hidden="true">note_add</span>
            Buat Sertifikat Baru
          </button>
          <a class="inline-flex items-center rounded border border-outline-variant bg-white px-3.5 py-1.5 font-label-md text-label-md font-semibold text-on-surface transition-colors hover:bg-surface-container-low" href="/Tandain/certificate">
            Batal
          </a>
        </div>
      </form>
    </section>
  </main>

<?php require_once 'views/layouts/footer.php'; ?>
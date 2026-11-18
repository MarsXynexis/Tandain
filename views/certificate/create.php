<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <section class="flex flex-col gap-1">
      <h1 class="font-headline-lg text-headline-lg font-bold tracking-tight text-primary">Buat Sertifikat Baru</h1>
      <p class="font-body-md text-body-md text-on-surface-variant">Terbitkan sertifikat digital untuk menandatangani dokumen secara elektronik</p>
    </section>

    <?php require_once 'views/layouts/flash.php'; ?>

    <section class="flex w-full flex-col gap-5 rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm md:p-7">
      <div class="flex items-start gap-4 rounded-xl border border-blue-200/80 bg-blue-50/70 p-5 text-on-surface-variant shadow-sm">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-[#004B87]">
          <span class="material-symbols-outlined text-[24px]" aria-hidden="true">lock</span>
        </div>

        <div class="flex-1 min-w-0 pt-0.5">
          <p class="font-body-sm text-body-sm leading-relaxed text-on-surface-variant">
            Passphrase ini digunakan setiap kali Anda menandatangani dokumen. Simpan passphrase dengan aman karena tidak dapat di-reset; jika lupa, Anda dapat menerbitkan sertifikat baru.
          </p>

          <div class="mt-2 text-xs text-on-surface-variant">
            <p class="font-semibold mb-1">Ketentuan passphrase:</p>
            <ul class="list-disc pl-5 space-y-0.5">
              <li>Minimal 4 karakter (dapat berupa PIN angka maupun kombinasi huruf)</li>
              <li>Menerbitkan sertifikat baru otomatis mencabut sertifikat aktif sebelumnya</li>
            </ul>
          </div>
        </div>
      </div>

      <form class="grid grid-cols-1 gap-4 xl:grid-cols-2 xl:gap-x-6" method="POST" action="/Tandain/certificate/store">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-semibold text-on-surface-variant" for="passphrase">Passphrase</label>

          <div class="relative">
            <input class="h-10 w-full rounded-lg border border-outline-variant bg-white pl-3 pr-10 text-sm text-primary placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-1 focus:ring-secondary" id="passphrase" name="passphrase" placeholder="Minimal 4 karakter" type="password" minlength="4" autocomplete="new-password" required>
            <button type="button" data-toggle-password="passphrase" class="absolute inset-y-0 right-0 flex items-center px-3 text-on-surface-variant hover:text-primary transition" aria-label="Tampilkan passphrase">
              <span class="material-symbols-outlined text-[18px]">visibility</span>
            </button>
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-label-md text-label-md font-semibold text-on-surface-variant" for="passphrase-confirmation">Konfirmasi Passphrase</label>

          <div class="relative">
            <input class="h-10 w-full rounded-lg border border-outline-variant bg-white pl-3 pr-10 text-sm text-primary placeholder:text-on-surface-variant/50 focus:border-secondary focus:outline-none focus:ring-1 focus:ring-secondary" id="passphrase-confirmation" name="passphrase_confirmation" placeholder="Ulangi passphrase" type="password" minlength="4" autocomplete="new-password" required>
            <button type="button" data-toggle-password="passphrase-confirmation" class="absolute inset-y-0 right-0 flex items-center px-3 text-on-surface-variant hover:text-primary transition" aria-label="Tampilkan konfirmasi passphrase">
              <span class="material-symbols-outlined text-[18px]">visibility</span>
            </button>
          </div>
        </div>

        <div class="col-span-full flex flex-wrap items-center justify-end gap-3 pt-2">
          <a class="btn-md inline-flex items-center border border-outline-variant bg-white font-semibold text-on-surface transition-colors hover:bg-surface-container-low" href="/Tandain/certificate">
            Batal
          </a>

          <button class="btn-lg inline-flex items-center gap-1.5 bg-brand hover:bg-brand-hover font-semibold text-white shadow-sm transition-colors" type="submit">
            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">add</span>
            <span>Buat Sertifikat Baru</span>
          </button>
        </div>
      </form>
    </section>

  </main>

  <script>
    document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const targetId = btn.dataset.togglePassword;
        const input = document.getElementById(targetId);
        const icon = btn.querySelector('.material-symbols-outlined');

        if (input && icon) {
          const isPassword = input.type === 'password';
          input.type = isPassword ? 'text' : 'password';
          icon.textContent = isPassword ? 'visibility_off' : 'visibility';
        }
      });
    });
  </script>

<?php require_once 'views/layouts/footer.php'; ?>

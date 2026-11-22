<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <div class="flex w-full max-w-2xl flex-col justify-between gap-4 md:flex-row md:items-center">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Tambah Akun Civitas</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Daftarkan akun civitas agar dapat masuk dan menggunakan layanan TTE</p>
      </div>

      <div>
        <a class="btn-md inline-flex items-center gap-1.5 border border-outline-variant text-primary hover:bg-surface-container-low transition-colors" href="/Tandain/accounts">
          <span class="material-symbols-outlined text-[18px]">arrow_back</span>
          <span>Kembali</span>
        </a>
      </div>
    </div>

    <?php require_once 'views/layouts/flash.php'; ?>

    <?php if (!empty($data['error'])): ?>
      <?php $notice = ['type' => 'error', 'message' => $data['error']]; ?>
      <?php require 'views/layouts/notice.php'; ?>
    <?php endif; ?>

    <section class="max-w-2xl bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm flex flex-col gap-6">

      <form method="POST" action="/Tandain/accounts/create" class="flex flex-col gap-5" autocomplete="off">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

        <div class="flex flex-col gap-1.5">
          <label for="full_name" class="font-label-md text-label-md font-semibold text-primary">Nama Lengkap &amp; Gelar <span class="text-[#b91c1c]">*</span></label>
          <input type="text" id="full_name" name="full_name" required maxlength="150" value="<?= htmlspecialchars($data['form']['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Contoh: Dr. Bambang W., M.T. atau Farhan Maulana" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant/60 font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
        </div>

        <div class="flex flex-col gap-1.5">
          <label for="identity_number" class="font-label-md text-label-md font-semibold text-primary">Nomor Induk (NIM / NIP) <span class="text-[#b91c1c]">*</span></label>
          <input type="text" id="identity_number" name="identity_number" required maxlength="50" value="<?= htmlspecialchars($data['form']['identity_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Contoh: 198003152005011002 atau 2207411032" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant/60 font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
        </div>

        <div class="flex flex-col gap-1.5">
          <label for="email" class="font-label-md text-label-md font-semibold text-primary">Alamat Email <span class="text-[#b91c1c]">*</span></label>
          <input type="email" id="email" name="email" required maxlength="150" value="<?= htmlspecialchars($data['form']['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="nama@email.com" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant/60 font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
          <p class="font-body-sm text-xs text-on-surface-variant">Masukkan alamat email aktif yang digunakan untuk masuk ke sistem.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

          <div class="flex flex-col gap-1.5">
            <label for="position" class="font-label-md text-label-md font-semibold text-primary">Jabatan Struktural <span class="font-normal text-on-surface-variant">(Opsional)</span></label>
            <input type="text" id="position" name="position" maxlength="100" value="<?= htmlspecialchars($data['form']['position'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Contoh: Ketua Jurusan TIK" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant/60 font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
            <p class="font-body-sm text-xs text-on-surface-variant">Diisi untuk pejabat seperti Kajur atau Kaprodi.</p>
          </div>

          <div class="flex flex-col gap-1.5">
            <label for="study_program" class="font-label-md text-label-md font-semibold text-primary">Program Studi <span class="font-normal text-on-surface-variant">(Opsional)</span></label>
            <input type="text" id="study_program" name="study_program" maxlength="100" value="<?= htmlspecialchars($data['form']['study_program'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Contoh: D4 Teknik Informatika" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant/60 font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
            <p class="font-body-sm text-xs text-on-surface-variant">Diisi untuk dosen pengajar atau mahasiswa.</p>
          </div>

        </div>

        <div class="flex flex-col gap-1.5">
          <label for="role_id" class="font-label-md text-label-md font-semibold text-primary">Peran Akun Sistem <span class="text-[#b91c1c]">*</span></label>
          <select id="role_id" name="role_id" required class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
            <option value="">Pilih Peran Akun</option>
            <?php foreach ($data['roles'] as $roleItem): ?>
              <option value="<?= htmlspecialchars($roleItem['id'], ENT_QUOTES, 'UTF-8'); ?>" <?= (($data['form']['role_id'] ?? '') === $roleItem['id']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($roleItem['display_name'], ENT_QUOTES, 'UTF-8'); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

          <div class="flex flex-col gap-1.5">
            <label for="password" class="font-label-md text-label-md font-semibold text-primary">Kata Sandi Awal <span class="text-[#b91c1c]">*</span></label>
            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant/60 font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
          </div>

          <div class="flex flex-col gap-1.5">
            <label for="password_confirmation" class="font-label-md text-label-md font-semibold text-primary">Konfirmasi Kata Sandi <span class="text-[#b91c1c]">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password" placeholder="Ulangi kata sandi" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant/60 font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
          </div>

        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-outline-variant">
          <a href="/Tandain/accounts" class="btn-md inline-flex border border-outline-variant text-on-surface-variant hover:text-primary transition-colors">
            Batal
          </a>

          <button type="submit" class="btn-lg inline-flex items-center gap-2 bg-brand text-white font-semibold hover:bg-brand-hover transition-all shadow-sm active:scale-[0.98]">
            <span class="material-symbols-outlined text-[18px]">check</span>
            <span>Simpan Akun</span>
          </button>
        </div>

      </form>

    </section>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

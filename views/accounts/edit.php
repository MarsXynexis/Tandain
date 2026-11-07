<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php $isSelf = ($data['target_user']['id'] === $data['user']['id']); ?>

  <main class="flex-1 w-full p-8 flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Ubah Akun Civitas</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Perbarui identitas civitas akademika, peran akses, dan status akun</p>
      </div>

      <div>
        <a class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-outline-variant font-label-md text-label-md text-primary hover:bg-surface-container-low transition-colors" href="/Tandain/accounts">
          <span class="material-symbols-outlined text-[18px]">arrow_back</span>
          <span>Kembali</span>
        </a>
      </div>
    </div>

    <?php require_once 'views/layouts/flash.php'; ?>

    <?php if (!empty($data['error'])): ?>
      <div class="p-4 rounded-xl border border-red-200 bg-red-50 text-[#b91c1c] font-body-sm text-body-sm flex items-center gap-3" role="alert">
        <span class="material-symbols-outlined text-[20px]">error</span>
        <span><?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
    <?php endif; ?>

    <section class="max-w-2xl bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm flex flex-col gap-6">

      <div class="flex items-center gap-2 pb-4 border-b border-outline-variant">
        <span class="material-symbols-outlined text-secondary text-[22px]">badge</span>
        <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Informasi Akun</h2>
      </div>

      <form method="POST" action="/Tandain/accounts/edit/<?= $data['target_user']['id']; ?>" class="flex flex-col gap-5">

        <div class="flex flex-col gap-1.5">
          <label for="full_name" class="font-label-md text-label-md font-semibold text-primary">Nama Lengkap &amp; Gelar <span class="text-[#b91c1c]">*</span></label>
          <input type="text" id="full_name" name="full_name" required value="<?= htmlspecialchars($data['form']['full_name'], ENT_QUOTES, 'UTF-8'); ?>" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
        </div>

        <div class="flex flex-col gap-1.5">
          <label for="identity_number" class="font-label-md text-label-md font-semibold text-primary">Nomor Induk (NIM / NIP) <span class="text-[#b91c1c]">*</span></label>
          <input type="text" id="identity_number" name="identity_number" required value="<?= htmlspecialchars($data['form']['identity_number'], ENT_QUOTES, 'UTF-8'); ?>" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
        </div>

        <div class="flex flex-col gap-1.5">
          <label for="email" class="font-label-md text-label-md font-semibold text-primary">Alamat Email Resmi Kampus <span class="text-[#b91c1c]">*</span></label>
          <input type="email" id="email" name="email" required value="<?= htmlspecialchars($data['form']['email'], ENT_QUOTES, 'UTF-8'); ?>" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
          <p class="font-body-sm text-xs text-on-surface-variant">Gunakan domain kampus (@pnj.ac.id atau @stu.pnj.ac.id).</p>
        </div>

        <div class="flex flex-col gap-1.5">
          <label for="position" class="font-label-md text-label-md font-semibold text-primary">Jabatan Struktural / Unit Kerja</label>
          <input type="text" id="position" name="position" value="<?= htmlspecialchars($data['form']['position'], ENT_QUOTES, 'UTF-8'); ?>" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

          <div class="flex flex-col gap-1.5">
            <label for="role_id" class="font-label-md text-label-md font-semibold text-primary">Peran Akun Sistem <span class="text-[#b91c1c]">*</span></label>
            <select id="role_id" name="role_id" required <?= $isSelf ? 'disabled' : ''; ?> class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all disabled:bg-surface-container disabled:text-on-surface-variant">
              <?php foreach ($data['roles'] as $roleItem): ?>
                <option value="<?= htmlspecialchars($roleItem['id'], ENT_QUOTES, 'UTF-8'); ?>" <?= ($data['form']['role_id'] === $roleItem['id']) ? 'selected' : ''; ?>>
                  <?= htmlspecialchars($roleItem['display_name'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
              <?php endforeach; ?>
            </select>

            <?php if ($isSelf): ?>
              <input type="hidden" name="role_id" value="<?= htmlspecialchars($data['form']['role_id'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php endif; ?>
          </div>

          <div class="flex flex-col gap-1.5">
            <label for="status" class="font-label-md text-label-md font-semibold text-primary">Status Akun <span class="text-[#b91c1c]">*</span></label>
            <select id="status" name="status" required <?= $isSelf ? 'disabled' : ''; ?> class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all disabled:bg-surface-container disabled:text-on-surface-variant">
              <option value="ACTIVE" <?= ($data['form']['status'] === 'ACTIVE') ? 'selected' : ''; ?>>Aktif</option>
              <option value="SUSPENDED" <?= ($data['form']['status'] === 'SUSPENDED') ? 'selected' : ''; ?>>Ditangguhkan</option>
            </select>

            <?php if ($isSelf): ?>
              <input type="hidden" name="status" value="<?= htmlspecialchars($data['form']['status'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php endif; ?>
          </div>

        </div>

        <?php if ($isSelf): ?>
          <div class="flex items-start gap-2.5 rounded-lg border border-outline-variant bg-surface-container-low px-4 py-3 text-on-surface-variant font-body-sm text-body-sm">
            <span class="material-symbols-outlined text-[18px]">info</span>
            <span>Peran dan status akun Anda sendiri tidak dapat diubah dari halaman ini.</span>
          </div>
        <?php endif; ?>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-outline-variant">
          <a href="/Tandain/accounts" class="px-4 py-2.5 rounded-lg border border-outline-variant text-on-surface-variant hover:text-primary font-label-md text-label-md transition-colors">
            Batal
          </a>

          <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#004B87] text-white font-label-md text-label-md font-semibold hover:bg-[#003865] transition-all shadow-sm active:scale-[0.98]">
            <span class="material-symbols-outlined text-[18px]">save</span>
            <span>Simpan Perubahan</span>
          </button>
        </div>

      </form>

    </section>

    <section class="max-w-2xl bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm flex flex-col gap-5">

      <div class="flex items-center gap-2 pb-4 border-b border-outline-variant">
        <span class="material-symbols-outlined text-secondary text-[22px]">lock_reset</span>
        <div>
          <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Atur Ulang Kata Sandi</h2>
          <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Kata sandi baru langsung berlaku untuk akun ini</p>
        </div>
      </div>

      <form method="POST" action="/Tandain/accounts/resetPassword/<?= $data['target_user']['id']; ?>" class="flex flex-col gap-5" autocomplete="off">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

          <div class="flex flex-col gap-1.5">
            <label for="new_password" class="font-label-md text-label-md font-semibold text-primary">Kata Sandi Baru <span class="text-[#b91c1c]">*</span></label>
            <input type="password" id="new_password" name="new_password" required minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant/60 font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
          </div>

          <div class="flex flex-col gap-1.5">
            <label for="new_password_confirmation" class="font-label-md text-label-md font-semibold text-primary">Konfirmasi Kata Sandi Baru <span class="text-[#b91c1c]">*</span></label>
            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required minlength="8" autocomplete="new-password" placeholder="Ulangi kata sandi baru" class="px-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant/60 font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all">
          </div>

        </div>

        <div class="flex items-center justify-end pt-4 border-t border-outline-variant">
          <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-amber-300 text-amber-700 hover:bg-amber-50 font-label-md text-label-md font-semibold transition-colors">
            <span class="material-symbols-outlined text-[18px]">lock_reset</span>
            <span>Perbarui Kata Sandi</span>
          </button>
        </div>

      </form>

    </section>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

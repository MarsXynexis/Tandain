<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Manajemen Akun Civitas</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Daftar akun civitas akademika, peran akses, dan status sertifikat digital</p>
      </div>

      <div class="flex items-center gap-3">
        <a class="btn-md inline-flex items-center gap-2 bg-brand text-white font-semibold hover:bg-brand-hover transition-all shadow-sm active:scale-[0.98]" href="/Tandain/accounts/create">
          <span class="material-symbols-outlined text-[18px]">person_add</span>
          <span>Tambah Akun Baru</span>
        </a>
      </div>
    </div>

    <?php require_once 'views/layouts/flash.php'; ?>

    <section class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-none overflow-hidden flex flex-col">

      <div class="p-5 border-b border-outline-variant bg-surface-container-lowest">
        <form method="GET" action="/Tandain/accounts" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

          <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-on-surface-variant">
              <span class="material-symbols-outlined text-[20px]">search</span>
            </span>
            <input name="search" value="<?= htmlspecialchars($data['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary placeholder:text-on-surface-variant font-body-md text-body-md focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20 transition-all" placeholder="Cari nama, email, atau NIM/NIP..." type="text">
          </div>

          <div class="flex items-center gap-3 flex-wrap">

            <select name="role" class="px-3.5 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-md text-body-md focus:outline-none focus:border-secondary transition-colors">
              <option value="">Semua Peran</option>
              <?php foreach ($data['roles'] as $roleItem): ?>
                <option value="<?= htmlspecialchars($roleItem['name'], ENT_QUOTES, 'UTF-8'); ?>" <?= ($data['selected_role'] === $roleItem['name']) ? 'selected' : ''; ?>>
                  <?= htmlspecialchars($roleItem['display_name'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
              <?php endforeach; ?>
            </select>

            <select name="status" class="px-3.5 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-primary font-body-md text-body-md focus:outline-none focus:border-secondary transition-colors">
              <option value="">Semua Status</option>
              <option value="ACTIVE" <?= ($data['selected_status'] === 'ACTIVE') ? 'selected' : ''; ?>>Aktif</option>
              <option value="SUSPENDED" <?= ($data['selected_status'] === 'SUSPENDED') ? 'selected' : ''; ?>>Ditangguhkan</option>
            </select>

            <button type="submit" class="btn-md inline-flex items-center gap-1.5 bg-surface-container-low hover:bg-surface-container text-primary border border-outline-variant font-semibold transition-colors">
              <span class="material-symbols-outlined text-[18px]">filter_list</span>
              <span>Filter</span>
            </button>

            <?php if (!empty($data['search']) || !empty($data['selected_role']) || !empty($data['selected_status'])): ?>
              <a href="/Tandain/accounts" class="px-3 py-2 text-on-surface-variant hover:text-primary font-label-md text-label-md transition-colors">
                Reset
              </a>
            <?php endif; ?>

          </div>

        </form>
      </div>

      <div class="overflow-x-auto">
        <table class="data-table min-w-[1000px]">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant h-11 text-[13px] font-semibold uppercase tracking-wide text-on-surface-variant">
              <th scope="col">Nama Civitas</th>
              <th scope="col">Nomor Induk</th>
              <th scope="col">Alamat Email</th>
              <th scope="col">Jabatan / Program Studi</th>
              <th scope="col">Peran</th>
              <th scope="col" class="text-center">Sertifikat TTE</th>
              <th scope="col" class="text-center">Status Akun</th>
              <th scope="col" class="text-right min-w-[200px]">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-outline-variant/60">
            <?php if (empty($data['users'])): ?>
              <tr>
                <td colspan="8" class="table-empty text-center text-on-surface-variant">
                  <div class="flex flex-col items-center justify-center gap-2 max-w-sm mx-auto">
                    <div class="w-12 h-12 rounded-full bg-surface-container text-on-surface-variant flex items-center justify-center mb-1">
                      <span class="material-symbols-outlined text-[26px]">manage_accounts</span>
                    </div>
                    <p class="font-headline-sm text-sm font-semibold text-primary">Akun Tidak Ditemukan</p>
                    <p class="font-body-sm text-xs text-on-surface-variant">Tidak ada akun civitas yang cocok dengan kriteria pencarian atau filter.</p>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($data['users'] as $u): ?>
                <tr class="hover:bg-surface-container-low/50 transition-colors group">
                  <td>
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center font-bold text-primary text-xs uppercase shrink-0">
                        <?= htmlspecialchars(mb_substr($u['full_name'], 0, 2), ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                      <span class="table-title text-primary">
                        <?= htmlspecialchars($u['full_name'], ENT_QUOTES, 'UTF-8'); ?>
                      </span>
                    </div>
                  </td>

                  <td class="font-code-tabular text-on-surface-variant whitespace-nowrap">
                    <?= htmlspecialchars($u['identity_number'], ENT_QUOTES, 'UTF-8'); ?>
                  </td>

                  <td class="text-on-surface-variant whitespace-nowrap">
                    <?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8'); ?>
                  </td>

                  <td class="text-on-surface-variant">
                    <?= htmlspecialchars(formatUserAffiliation($u['position'] ?? null, $u['study_program'] ?? null, '-'), ENT_QUOTES, 'UTF-8'); ?>
                  </td>

                  <td class="whitespace-nowrap">
                    <span class="font-label-md text-label-md font-medium text-primary">
                      <?= htmlspecialchars($u['role_display_name'] ?? $u['role_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </td>

                  <td class="text-center whitespace-nowrap">
                    <?php renderStatusBadge(!empty($u['has_active_cert']) ? 'CERT_ACTIVE' : 'CERT_INACTIVE'); ?>
                  </td>

                  <td class="text-center whitespace-nowrap">
                    <?php renderStatusBadge($u['status']); ?>
                  </td>

                  <td class="text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">

                      <a href="/Tandain/accounts/edit/<?= $u['id']; ?>" class="btn-sm inline-flex items-center gap-1 border border-outline-variant text-primary hover:bg-surface-container-low transition-colors" title="Ubah Akun">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                        <span>Ubah</span>
                      </a>

                      <?php if ($u['id'] === $data['user']['id']): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-outline-variant font-label-md text-label-md text-on-surface-variant">
                          <span class="material-symbols-outlined text-[16px]">lock</span>
                          <span>Akun Anda</span>
                        </span>
                      <?php else: ?>
                        <form method="POST" action="/Tandain/accounts/toggle/<?= $u['id']; ?>" class="inline" data-confirm-form>
                          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                          <button type="submit" class="btn-sm inline-flex items-center gap-1 border border-outline-variant <?= ($u['status'] === 'ACTIVE') ? 'text-[#b91c1c] hover:bg-red-50' : 'text-[#15803d] hover:bg-green-50'; ?> transition-colors" title="Ubah Status Akun">
                            <span class="material-symbols-outlined text-[16px]"><?= ($u['status'] === 'ACTIVE') ? 'block' : 'check_circle'; ?></span>
                            <span><?= ($u['status'] === 'ACTIVE') ? 'Tangguhkan' : 'Aktifkan'; ?></span>
                          </button>
                        </form>
                      <?php endif; ?>

                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="table-footer bg-surface-container-low border-t border-outline-variant flex items-center justify-between">
        <span class="text-xs text-on-surface-variant">
          Menampilkan <strong class="text-primary font-semibold"><?= count($data['users']); ?></strong> akun
          <?php if (!empty($data['search']) || !empty($data['selected_role']) || !empty($data['selected_status'])): ?>
            sesuai filter
          <?php endif; ?>
        </span>
      </div>

    </section>

  </main>

<?php $confirmation = ['title' => 'Ubah Status Akun', 'message' => 'Ubah status akun yang dipilih?', 'action' => 'Ubah Status']; ?>
<?php require 'views/layouts/confirm.php'; ?>

<?php require_once 'views/layouts/footer.php'; ?>

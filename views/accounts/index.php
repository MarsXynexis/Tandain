<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 w-full p-8 flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Manajemen Akun Civitas</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Daftar akun civitas akademika, peran akses, dan status sertifikat digital</p>
      </div>

      <div class="flex items-center gap-3">
        <a class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#004B87] text-white font-label-md text-label-md font-semibold hover:bg-[#003865] transition-all shadow-sm active:scale-[0.98]" href="/Tandain/accounts/create">
          <span class="material-symbols-outlined text-[18px]">person_add</span>
          <span>Tambah Akun Baru</span>
        </a>
      </div>
    </div>

    <?php require_once 'views/layouts/flash.php'; ?>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

      <a class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md" href="/Tandain/accounts">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Total Akun</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['stats']['total_users'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Civitas terdaftar</p>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#e8e7ff] text-[#5145d8]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">groups</span>
        </div>
      </a>

      <a class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md" href="/Tandain/accounts?role=pegawai">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Dosen &amp; Tendik</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['stats']['total_pegawai'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Pendidik dan tendik</p>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#dceafe] text-[#1d4ed8]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">badge</span>
        </div>
      </a>

      <a class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md" href="/Tandain/accounts?role=mahasiswa">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Mahasiswa</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['stats']['total_mahasiswa'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Akun mahasiswa</p>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#ede9fe] text-[#6d28d9]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">school</span>
        </div>
      </a>

      <a class="flex items-start justify-between gap-4 rounded-2xl border border-outline-variant/70 bg-surface-container-lowest p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:border-secondary/50 hover:shadow-md" href="/Tandain/accounts?status=ACTIVE">
        <div class="min-w-0">
          <p class="font-body-md text-[13px] font-semibold text-on-surface-variant">Akun Aktif</p>
          <p class="mt-2 font-headline-xl text-[32px] font-bold leading-none tracking-tight text-primary"><?= htmlspecialchars((string)($data['stats']['total_active'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="mt-2 font-body-sm text-[12px] leading-5 text-on-surface-variant">Akses aktif</p>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#dcfce7] text-[#15803d]">
          <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">verified_user</span>
        </div>
      </a>

    </section>

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

            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-surface-container-low hover:bg-surface-container text-primary border border-outline-variant rounded-lg font-label-md text-label-md font-semibold transition-colors">
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
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant h-11 text-[13px] font-semibold uppercase tracking-wide text-on-surface-variant">
              <th scope="col" class="py-3 px-5 text-center w-14">No</th>
              <th scope="col" class="py-3 px-6">Nama Civitas</th>
              <th scope="col" class="py-3 px-6">Nomor Induk</th>
              <th scope="col" class="py-3 px-6">Email Resmi</th>
              <th scope="col" class="py-3 px-6">Jabatan / Unit</th>
              <th scope="col" class="py-3 px-6">Peran</th>
              <th scope="col" class="py-3 px-6 text-center">Sertifikat TTE</th>
              <th scope="col" class="py-3 px-6 text-center">Status Akun</th>
              <th scope="col" class="py-3 px-6 text-right min-w-[200px]">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-outline-variant/60 font-body-sm text-body-sm">
            <?php if (empty($data['users'])): ?>
              <tr>
                <td colspan="9" class="py-8 text-center text-on-surface-variant">
                  Tidak ada akun civitas yang cocok dengan kriteria pencarian.
                </td>
              </tr>
            <?php else: ?>
              <?php $no = 1; ?>
              <?php foreach ($data['users'] as $u): ?>
                <tr class="hover:bg-surface-container-low/50 transition-colors group">
                  <td class="py-4 px-5 text-center font-code-tabular text-on-surface-variant"><?= $no++; ?></td>

                  <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center font-bold text-primary text-xs uppercase shrink-0">
                        <?= htmlspecialchars(mb_substr($u['full_name'], 0, 2), ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                      <span class="font-label-md text-label-md font-semibold text-primary">
                        <?= htmlspecialchars($u['full_name'], ENT_QUOTES, 'UTF-8'); ?>
                      </span>
                    </div>
                  </td>

                  <td class="py-4 px-6 font-code-tabular text-on-surface-variant whitespace-nowrap">
                    <?= htmlspecialchars($u['identity_number'], ENT_QUOTES, 'UTF-8'); ?>
                  </td>

                  <td class="py-4 px-6 text-on-surface-variant whitespace-nowrap">
                    <?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8'); ?>
                  </td>

                  <td class="py-4 px-6 text-on-surface-variant">
                    <?= htmlspecialchars($u['position'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                  </td>

                  <td class="py-4 px-6 whitespace-nowrap">
                    <span class="font-label-md text-label-md font-medium text-primary">
                      <?= htmlspecialchars($u['role_display_name'] ?? $u['role_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </td>

                  <td class="py-4 px-6 text-center whitespace-nowrap">
                    <?php if (!empty($u['has_active_cert'])): ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]">
                        <span class="material-symbols-outlined text-[14px]">verified</span>
                        <span>Aktif</span>
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-surface-container text-on-surface-variant border border-outline-variant">
                        <span>Belum Terbit</span>
                      </span>
                    <?php endif; ?>
                  </td>

                  <td class="py-4 px-6 text-center whitespace-nowrap">
                    <?php if ($u['status'] === 'ACTIVE'): ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]">
                        <span>Aktif</span>
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#fee2e2] text-[#b91c1c] border border-[#fecaca]">
                        <span>Ditangguhkan</span>
                      </span>
                    <?php endif; ?>
                  </td>

                  <td class="py-4 px-6 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">

                      <a href="/Tandain/accounts/edit/<?= $u['id']; ?>" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-outline-variant font-label-md text-label-md text-primary hover:bg-surface-container-low transition-colors" title="Ubah Akun">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                        <span>Ubah</span>
                      </a>

                      <?php if ($u['id'] === $data['user']['id']): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-outline-variant font-label-md text-label-md text-on-surface-variant">
                          <span class="material-symbols-outlined text-[16px]">lock</span>
                          <span>Akun Anda</span>
                        </span>
                      <?php else: ?>
                        <form method="POST" action="/Tandain/accounts/toggle/<?= $u['id']; ?>" class="inline" onsubmit="return confirm('Ubah status akun ini?');">
                          <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-outline-variant font-label-md text-label-md <?= ($u['status'] === 'ACTIVE') ? 'text-[#b91c1c] hover:bg-red-50' : 'text-[#15803d] hover:bg-green-50'; ?> transition-colors" title="Ubah Status Akun">
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

      <div class="p-4 bg-surface-container-low border-t border-outline-variant flex items-center justify-between">
        <span class="font-body-sm text-body-sm text-on-surface-variant">
          Menampilkan <strong class="text-primary font-semibold"><?= count($data['users']); ?></strong> akun
          <?php if (!empty($data['search']) || !empty($data['selected_role']) || !empty($data['selected_status'])): ?>
            sesuai filter
          <?php endif; ?>
        </span>
      </div>

    </section>

  </main>

<?php require_once 'views/layouts/footer.php'; ?>

<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>
<?php require_once 'views/layouts/flash.php'; ?>

  <main class="flex-1 w-full p-8 flex flex-col gap-8">
    <section class="flex flex-col gap-1">
      <h1 class="font-headline-lg text-headline-lg font-bold tracking-tight text-primary">Sertifikat Digital</h1>
      <p class="font-body-md text-body-md text-on-surface-variant">Kelola kredensial kriptografis dan passphrase Anda untuk penandatanganan elektronik.</p>
    </section>

    <section class="flex items-start gap-4 rounded-xl border border-blue-200/80 bg-blue-50/70 p-5 text-on-surface-variant shadow-sm">
      <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-[#004B87]">
        <span class="material-symbols-outlined text-[24px]" aria-hidden="true">info</span>
      </div>

      <div class="flex-1 min-w-0 pt-0.5">
        <p class="font-body-sm text-body-sm leading-relaxed text-on-surface-variant">
          Sertifikat digital adalah identitas resmi elektronik Anda di aplikasi Tandain. Sertifikat digital bertindak sebagai stempel dan tanda tangan resmi berbasis kode unik. Fitur ini memastikan identitas Penandatangan valid serta mengunci dokumen secara digital agar keaslian dan keabsahan surat resmi kampus terjamin.
        </p>
      </div>
    </section>

    <section class="flex flex-col overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest shadow-sm">
      <div class="flex flex-col gap-4 border-b border-outline-variant p-6 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex flex-col gap-1">
          <h2 class="flex items-center gap-2 font-headline-sm text-headline-sm font-bold text-primary">
            <span class="material-symbols-outlined text-[20px] text-secondary" aria-hidden="true">document_scanner</span>
            Sertifikat Digital Saya
          </h2>
          <p class="font-body-sm text-body-sm text-on-surface-variant">Daftar seluruh sertifikat digital Anda.</p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] border-collapse text-left">
          <thead>
            <tr class="border-b border-outline-variant bg-surface-container-low text-[13px] font-semibold uppercase tracking-wide text-on-surface-variant">
              <th class="px-5 py-3.5 text-center">Serial Number</th>
              <th class="px-5 py-3.5">Pemohon</th>
              <th class="px-5 py-3.5 text-center">Tanggal Berlaku</th>
              <th class="px-5 py-3.5 text-center">Status</th>
              <th class="px-5 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/60">
            <?php $certificates = $data['certificates'] ?? []; ?>
            <?php foreach ($certificates as $certificate): ?>
              <?php
              $status = $certificate['status'] ?? '';
              $statusLabels = [
                  'ACTIVE' => ['Aktif', 'border-[#bbf7d0] bg-[#dcfce7] text-[#15803d]', 'check'],
                  'REVOKED' => ['Dicabut', 'border-[#f1c5c1] bg-[#fff1f0] text-error', 'cancel'],
                  'EXPIRED' => ['Kedaluwarsa', 'border-outline-variant bg-surface-container-low text-on-surface-variant', 'schedule'],
              ];
              [$statusLabel, $statusClasses, $statusIcon] = $statusLabels[$status] ?? [
                  htmlspecialchars($status !== '' ? $status : 'Tidak diketahui', ENT_QUOTES, 'UTF-8'),
                  'border-outline-variant bg-surface-container-low text-on-surface-variant',
                  'help',
              ];
              ?>
              <tr class="text-sm text-on-surface">
                <td class="whitespace-nowrap px-5 py-4 font-label-md text-label-md font-semibold text-primary">
                  <?= htmlspecialchars($certificate['certificate_serial'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                </td>
                <td class="px-5 py-4">
                  <p class="font-label-md text-label-md font-semibold text-primary">
                    <?= htmlspecialchars($data['user']['full_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                  </p>
                  <p class="font-body-sm text-body-sm text-outline">
                    (NIP/NIM: <?= htmlspecialchars($certificate['identity_number'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>)
                  </p>
                </td>
                <td class="whitespace-nowrap px-5 py-4  font-body-sm text-body-sm text-on-surface-variant">
                  <p>Dibuat: <?= !empty($certificate['valid_from']) ? htmlspecialchars(date('d F Y', strtotime($certificate['valid_from'])), ENT_QUOTES, 'UTF-8') : '-'; ?></p>
                  <?php if ($status === 'ACTIVE'): ?>
                    <p>Berakhir: <?= !empty($certificate['valid_until']) ? htmlspecialchars(date('d F Y', strtotime($certificate['valid_until'])), ENT_QUOTES, 'UTF-8') : '-'; ?></p>
                  <?php endif; ?>
                  <?php if ($status === 'REVOKED' && !empty($certificate['revoked_at'])): ?>
                    <p>Dicabut: <?= htmlspecialchars(date('d F Y', strtotime($certificate['revoked_at'])), ENT_QUOTES, 'UTF-8'); ?></p>
                  <?php endif; ?>
                </td>
                <td class="px-5 py-4 text-center">
                  <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border px-2.5 py-1 text-[11px] font-bold <?= $statusClasses; ?>">
                    <span class="material-symbols-outlined text-[14px]" aria-hidden="true"><?= $statusIcon; ?></span>
                    <?= $statusLabel; ?>
                  </span>
                </td>
                <td class="px-5 py-4">
                  <div class="flex items-center justify-end">
                    <?php if ($status === 'ACTIVE'): ?>
                      <form method="POST" action="/Tandain/certificate/revoke" onsubmit="return confirm('Yakin ingin mencabut sertifikat ini? Sertifikat yang dicabut tidak dapat digunakan untuk menandatangani dokumen.');">
                        <input type="hidden" name="certificate_id" value="<?= htmlspecialchars($certificate['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-[#ba1a1a] px-3 py-1.5 font-label-md text-label-md font-semibold text-white shadow-sm transition-colors hover:bg-[#93000a]">
                          <span class="material-symbols-outlined text-[16px]" aria-hidden="true">disabled_by_default</span>
                          Cabut Sertifikat
                        </button>
                      </form>
                    <?php elseif ($status === 'REVOKED'): ?>
                      <div class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-[#af6f6f] px-3 py-1.5 font-label-md text-label-md font-semibold text-white shadow-sm transition-colors cursor-not-allowed" aria-disabled="true">
                          <span class="material-symbols-outlined text-[16px]" aria-hidden="true">disabled_by_default</span>
                          Cabut Sertifikat
                    </div>
                    <?php else: ?>
                      <span class="text-xs text-on-surface-variant">-</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($certificates)): ?>
              <tr>
                <td class="px-5 py-8 text-center text-sm text-on-surface-variant" colspan="5">
                  Anda belum memiliki sertifikat digital.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <footer class="flex flex-col gap-4 border-t border-outline-variant bg-surface-container-low p-4 lg:flex-row lg:items-center lg:justify-between">
        <p class="font-body-sm text-body-sm text-on-surface-variant">
          Menampilkan <strong class="text-primary"><?= count($certificates); ?></strong> sertifikat
        </p>
        <a href="/Tandain/certificate/create" class="inline-flex items-center justify-center gap-1.5 rounded bg-[#004b87] px-3.5 py-2 font-label-md text-label-md font-semibold text-white shadow-sm transition-colors hover:bg-[#003865]">
          <span class="material-symbols-outlined text-[16px]" aria-hidden="true">note_add</span>
          Buat Sertifikat Baru
        </a>
      </footer>
    </section>
  </main>

<?php require_once 'views/layouts/footer.php'; ?>
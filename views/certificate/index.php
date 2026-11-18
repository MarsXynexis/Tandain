<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php $certificates = $data['certificates'] ?? []; ?>

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <header class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold tracking-tight text-primary">Sertifikat Digital</h1>
        <p class="mt-1 text-body-md text-on-surface-variant">Sertifikat aktif diperlukan untuk menandatangani dokumen elektronik</p>
      </div>

      <a href="/Tandain/certificate/create" class="btn-md inline-flex gap-1.5 bg-brand text-white transition-colors hover:bg-brand-hover">
        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">add</span>
        <span>Buat Sertifikat Baru</span>
      </a>
    </header>

    <?php require_once 'views/layouts/flash.php'; ?>

    <section class="overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest shadow-sm">
      <div class="overflow-x-auto">
        <table class="data-table min-w-[620px]" aria-label="Daftar sertifikat digital">
          <thead>
            <tr class="border-b border-outline-variant">
              <th scope="col">Nomor Seri</th>
              <th scope="col">Masa Berlaku</th>
              <th scope="col" class="text-center">Status</th>
              <th scope="col" class="text-right">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-outline-variant/60">
            <?php foreach ($certificates as $certificate): ?>
              <?php
              $status = $certificate['status'] ?? '';
              ?>
              <tr class="text-sm text-on-surface">
                <td class="font-code-tabular whitespace-nowrap text-sm font-medium text-primary">
                  <?= htmlspecialchars($certificate['certificate_serial'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                </td>

                <td class="table-date text-on-surface-variant">
                  <p>Dibuat: <?= !empty($certificate['valid_from']) ? htmlspecialchars(formatTanggalIndo($certificate['valid_from']), ENT_QUOTES, 'UTF-8') : '-'; ?></p>

                  <?php if ($status === 'ACTIVE'): ?>
                    <p>Berakhir: <?= !empty($certificate['valid_until']) ? htmlspecialchars(formatTanggalIndo($certificate['valid_until']), ENT_QUOTES, 'UTF-8') : '-'; ?></p>
                  <?php elseif ($status === 'REVOKED' && !empty($certificate['revoked_at'])): ?>
                    <p>Dicabut: <?= htmlspecialchars(formatTanggalIndo($certificate['revoked_at']), ENT_QUOTES, 'UTF-8'); ?></p>
                  <?php endif; ?>
                </td>

                <td class="text-center">
                  <?php renderStatusBadge($status); ?>
                </td>

                <td>
                  <div class="flex items-center justify-end">
                    <?php if ($status === 'ACTIVE'): ?>
                      <button
                        type="button"
                        data-revoke-id="<?= htmlspecialchars($certificate['id'], ENT_QUOTES, 'UTF-8'); ?>"
                        data-revoke-serial="<?= htmlspecialchars($certificate['certificate_serial'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        class="btn-sm open-revoke-btn inline-flex items-center gap-1.5 whitespace-nowrap border border-red-300 bg-white font-semibold text-red-600 transition-colors hover:bg-red-50">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">block</span>
                        <span>Cabut Sertifikat</span>
                      </button>
                    <?php else: ?>
                      <span class="text-xs text-on-surface-variant">-</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>

            <?php if (empty($certificates)): ?>
              <tr>
                <td class="table-empty text-center text-sm text-on-surface-variant" colspan="4">
                  Anda belum memiliki sertifikat digital.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($certificates): ?>
        <footer class="table-footer border-t border-outline-variant bg-surface-container-low text-on-surface-variant">
          <strong class="text-primary"><?= count($certificates); ?></strong> sertifikat
        </footer>
      <?php endif; ?>
    </section>

    <div id="revoke-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
      <div class="w-full max-w-md rounded-xl border border-outline-variant bg-surface-container-lowest p-6 shadow-xl flex flex-col gap-4">
        <div class="flex items-center justify-between border-b border-surface-container pb-3">
          <div class="flex items-center gap-2 text-red-600">
            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">block</span>
            <h3 class="font-headline-sm text-headline-sm font-semibold">Cabut Sertifikat Digital</h3>
          </div>

          <button type="button" id="close-revoke-modal" class="text-on-surface-variant hover:text-primary transition">
            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">close</span>
          </button>
        </div>

        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
          Sertifikat <strong id="revoke-serial-label" class="font-code-tabular text-primary"></strong> yang dicabut tidak dapat digunakan lagi untuk menandatangani dokumen.
        </p>

        <form method="POST" action="/Tandain/certificate/revoke" class="flex items-center justify-end gap-3 pt-2">
          <input type="hidden" name="certificate_id" id="revoke-certificate-id" value="">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

          <button type="button" id="cancel-revoke-btn" class="btn-md inline-flex border border-outline-variant font-medium text-on-surface-variant transition hover:bg-surface-container">
            Batal
          </button>

          <button type="submit" class="btn-md inline-flex bg-red-600 font-semibold text-white transition hover:bg-red-700">
            Cabut Sertifikat
          </button>
        </form>
      </div>
    </div>

  </main>

  <script>
    (() => {
      const modal = document.getElementById('revoke-modal');
      const certIdInput = document.getElementById('revoke-certificate-id');
      const serialLabel = document.getElementById('revoke-serial-label');
      const closeBtn = document.getElementById('close-revoke-modal');
      const cancelBtn = document.getElementById('cancel-revoke-btn');

      const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        certIdInput.value = '';
      };

      document.querySelectorAll('.open-revoke-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
          certIdInput.value = btn.dataset.revokeId || '';
          serialLabel.textContent = btn.dataset.revokeSerial || '';
          modal.classList.remove('hidden');
          modal.classList.add('flex');
        });
      });

      closeBtn.addEventListener('click', closeModal);
      cancelBtn.addEventListener('click', closeModal);

      modal.addEventListener('click', (event) => {
        if (event.target === modal) {
          closeModal();
        }
      });
    })();
  </script>

<?php require_once 'views/layouts/footer.php'; ?>

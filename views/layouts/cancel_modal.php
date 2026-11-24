<?php
$modalId = $cancelModal['id'] ?? 'cancel-document-modal';
$modalTitleId = $modalId . '-title';
$modalAction = $cancelModal['action'] ?? '';
$modalTitle = $cancelModal['title'] ?? 'Batalkan Pengajuan Dokumen';
$modalMessage = $cancelModal['message'] ?? 'Pengajuan akan dihentikan dan dipindahkan ke status Dibatalkan. Sertakan alasan pembatalan.';
$modalPlaceholder = $cancelModal['placeholder'] ?? 'Jelaskan alasan pembatalan pengajuan...';
$modalCsrfToken = $cancelModal['csrf_token'] ?? csrfToken();
$modalDocumentId = $cancelModal['document_id'] ?? '';
?>

<dialog id="<?= htmlspecialchars($modalId, ENT_QUOTES, 'UTF-8'); ?>" aria-labelledby="<?= htmlspecialchars($modalTitleId, ENT_QUOTES, 'UTF-8'); ?>" style="position: fixed; top: 50%; left: 50%; margin: 0; transform: translate(-50%, -50%);" class="max-h-[calc(100%-2rem)] w-[min(100%-2rem,32rem)] overflow-y-auto rounded-2xl border border-outline-variant bg-surface-container-lowest p-0 text-on-surface shadow-2xl backdrop:bg-black/50">
  <form method="POST" action="<?= htmlspecialchars($modalAction, ENT_QUOTES, 'UTF-8'); ?>" class="p-6">
    <?php if ($modalDocumentId !== ''): ?>
      <input type="hidden" name="document_id" value="<?= htmlspecialchars($modalDocumentId, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($modalCsrfToken, ENT_QUOTES, 'UTF-8'); ?>">

    <div class="flex items-start gap-3">
      <span class="material-symbols-outlined mt-0.5 text-[24px] text-[#b91c1c]" aria-hidden="true">cancel</span>
      <div>
        <h2 id="<?= htmlspecialchars($modalTitleId, ENT_QUOTES, 'UTF-8'); ?>" class="font-headline-sm text-headline-sm font-semibold text-primary"><?= htmlspecialchars($modalTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant"><?= htmlspecialchars($modalMessage, ENT_QUOTES, 'UTF-8'); ?></p>
      </div>
    </div>

    <label class="mt-5 block">
      <span class="mb-1.5 block font-label-md text-label-md text-primary">Alasan Pembatalan</span>
      <textarea
        name="cancellation_reason"
        required
        minlength="3"
        maxlength="1000"
        rows="4"
        class="w-full rounded-lg border border-outline-variant bg-surface-container-low px-3 py-2.5 font-body-md text-body-md text-primary placeholder:text-on-surface-variant/70 focus:border-secondary focus:outline-none focus:ring-1 focus:ring-secondary"
        placeholder="<?= htmlspecialchars($modalPlaceholder, ENT_QUOTES, 'UTF-8'); ?>"></textarea>
    </label>

    <div class="mt-6 flex flex-wrap justify-end gap-3">
      <button type="button" data-modal-close class="btn-md inline-flex border border-outline-variant font-semibold text-primary hover:bg-surface-container-low">Kembali</button>
      <button type="submit" class="btn-md inline-flex items-center gap-2 bg-[#b91c1c] font-semibold text-white hover:bg-[#991b1b]">
        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">cancel</span>
        <span>Batalkan Pengajuan</span>
      </button>
    </div>
  </form>
</dialog>

<script>
  document.addEventListener('click', (event) => {
    if (event.target instanceof Element) {
      const trigger = event.target.closest('[data-modal-open]');

      if (trigger) {
        const modal = document.getElementById(trigger.dataset.modalOpen);

        if (modal instanceof HTMLDialogElement) {
          modal.showModal();
        }
      }
    }
  });

  document.querySelectorAll('dialog').forEach((modal) => {
    modal.addEventListener('click', (event) => {
      if (event.target === modal) {
        modal.close();
      }
    });

    modal.querySelectorAll('[data-modal-close]').forEach((button) => {
      button.addEventListener('click', () => modal.close());
    });
  });
</script>

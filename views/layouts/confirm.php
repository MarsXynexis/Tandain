<dialog id="action-confirmation" aria-labelledby="confirmation-title" aria-describedby="confirmation-message" class="m-auto w-[calc(100%-2rem)] max-w-md rounded-xl border border-outline-variant bg-surface-container-lowest p-6 text-on-surface shadow-xl backdrop:bg-black/40">
  <h2 id="confirmation-title" class="font-headline-sm text-headline-sm font-semibold text-primary"><?= htmlspecialchars($confirmation['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
  <p id="confirmation-message" class="mt-2 text-sm text-on-surface-variant"><?= htmlspecialchars($confirmation['message'], ENT_QUOTES, 'UTF-8'); ?></p>

  <form method="dialog" class="mt-6 flex justify-end gap-3">
    <button type="submit" value="cancel" autofocus class="btn-md inline-flex border border-outline-variant font-medium hover:bg-surface-container-low">Batal</button>
    <button type="submit" value="confirm" class="btn-md inline-flex bg-red-700 font-semibold text-white hover:bg-red-800"><?= htmlspecialchars($confirmation['action'], ENT_QUOTES, 'UTF-8'); ?></button>
  </form>
</dialog>

<script>
  (() => {
    const dialog = document.getElementById('action-confirmation');
    let pendingForm;
    let pendingSubmitter;
    let confirmedForm;

    document.addEventListener('submit', (event) => {
      if (event.target.matches('[data-confirm-form]') && event.target !== confirmedForm) {
        event.preventDefault();

        if (!dialog.open) {
          pendingForm = event.target;
          pendingSubmitter = event.submitter;
          dialog.returnValue = '';
          dialog.showModal();
        }
      }
    });

    dialog.addEventListener('close', () => {
      if (dialog.returnValue === 'confirm' && pendingForm) {
        confirmedForm = pendingForm;
        pendingForm.requestSubmit(pendingSubmitter || undefined);
        confirmedForm = null;
      }

      pendingForm = null;
      pendingSubmitter = null;
    });
  })();
</script>

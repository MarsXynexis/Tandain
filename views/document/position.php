<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

  <div>
    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight font-bold">Atur Posisi TTE</h1>
    <p class="font-body-md text-body-md text-on-surface-variant mt-1">Klik nama penandatangan untuk memunculkan kotak TTE, lalu geser ke posisi yang diinginkan pada dokumen</p>
  </div>

  <div class="hidden space-y-3 [&:has([data-notice]:not([hidden]))]:block">
    <?php require 'views/layouts/flash.php'; ?>

    <?php $notice = ['type' => 'error', 'id' => 'client-error']; ?>
    <?php require 'views/layouts/notice.php'; ?>
  </div>

  <div class="flex flex-col lg:flex-row gap-4 items-start">

    <div id="pdf-scroll-container" class="flex-1 min-w-0 w-full max-w-none lg:sticky lg:top-20 lg:h-[calc(100dvh-6rem)] lg:overflow-y-auto">
      <div class="text-sm text-on-surface-variant py-6 text-center" id="pdf-loading">Memuat dokumen...</div>
      <div id="pdf-container" class="space-y-4 overflow-x-auto pb-4"></div>
    </div>

    <div class="w-full lg:w-72 shrink-0 space-y-2 lg:sticky lg:top-20">

      <h2 class="font-headline-sm text-headline-sm text-primary font-semibold mb-1">Penandatangan</h2>

      <?php foreach ($signers as $i => $s): ?>
        <?php $signerAffiliation = formatUserAffiliation($s['position'] ?? null, $s['study_program'] ?? null, $s['role_display_name'] ?? ''); ?>
        <div class="p-3 rounded-lg border border-outline-variant bg-surface-container-lowest" data-signer-card="<?= htmlspecialchars($s['id']) ?>">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0"><?= $i + 1 ?></div>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-bold text-primary truncate"><?= htmlspecialchars($s['name']) ?></p>
              <?php if ($signerAffiliation !== ''): ?>
                <p class="text-[11px] text-on-surface-variant truncate"><?= htmlspecialchars($signerAffiliation, ENT_QUOTES, 'UTF-8') ?></p>
              <?php endif; ?>
              <p class="text-xs text-on-surface-variant" data-status>Belum ditempatkan</p>
            </div>
          </div>
          <div class="flex gap-2 mt-3">
            <button type="button"
              class="btn-md inline-flex add-box flex-1 bg-brand hover:bg-brand-hover text-white font-semibold transition"
              data-signer="<?= htmlspecialchars($s['id']) ?>"
              data-label="<?= $i + 1 ?>. <?= htmlspecialchars($s['name']) ?>">
              Tempatkan kotak
            </button>
            <button type="button"
              class="btn-md remove-box hidden border border-outline-variant text-error font-semibold hover:bg-error-container/40 transition"
              data-signer="<?= htmlspecialchars($s['id']) ?>">
              Hapus
            </button>
          </div>
        </div>
      <?php endforeach; ?>

      <p class="text-xs text-on-surface-variant pt-1">Kotak muncul di halaman yang sedang terlihat dan bisa digeser ke halaman mana pun.</p>
      <div class="flex flex-wrap items-center justify-between gap-3 pt-4 mt-4 border-t border-outline-variant">
        <form method="POST" action="/Tandain/document/delete/<?= htmlspecialchars($documentId) ?>" class="inline" data-confirm-form>
          <button type="submit" class="btn-md inline-flex items-center gap-1 border border-outline-variant text-[#b91c1c] hover:bg-red-50 transition-colors" title="Hapus Draf">
            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">delete</span>
            <span>Hapus</span>
          </button>
        </form>

        <form id="positions-form" method="POST" action="/Tandain/document/savePositions/<?= htmlspecialchars($documentId) ?>">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="positions" id="positions">
          <button class="btn-md bg-brand hover:bg-brand-hover text-white font-semibold shadow-sm flex items-center gap-2 transition active:scale-[0.99]" type="submit">
            <span>Simpan &amp; Ajukan</span>
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
          </button>
        </form>
      </div>
    </div>



  </div>



</main>

<script src="/Tandain/assets/js/pdf.min.js"></script>
<script src="/Tandain/assets/js/signature-positioner.js"></script>
<script>
  setupSignaturePositioner({
    pdfUrl: <?= json_encode($pdfUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
    signerIds: <?= json_encode(array_column($signers, 'id'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>
  });
</script>
<?php $confirmation = ['title' => 'Hapus Draf Dokumen', 'message' => 'Hapus draf dokumen ini secara permanen?', 'action' => 'Hapus Draf']; ?>
<?php require 'views/layouts/confirm.php'; ?>
<?php require_once 'views/layouts/footer.php'; ?>
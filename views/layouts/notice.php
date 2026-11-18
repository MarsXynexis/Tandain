<?php
$noticeIcons = [
    'success' => 'check_circle',
    'error' => 'error',
    'warning' => 'warning',
    'info' => 'info',
];

$noticeType = $notice['type'] ?? 'info';
$noticeType = isset($noticeIcons[$noticeType]) ? $noticeType : 'info';
$noticeMessage = $notice['message'] ?? '';
?>

<div data-notice data-type="<?= $noticeType; ?>" class="items-start gap-3 rounded-xl border px-4 py-3 font-body-sm text-sm"
    <?php if (isset($notice['id'])): ?>id="<?= htmlspecialchars($notice['id'], ENT_QUOTES, 'UTF-8'); ?>"<?php endif; ?>
    <?= $noticeMessage === '' ? 'hidden' : ''; ?>>
  <?php foreach ($noticeIcons as $type => $icon): ?>
    <span class="material-symbols-outlined shrink-0 text-[20px]" data-notice-icon="<?= $type; ?>" aria-hidden="true"><?= $icon; ?></span>
  <?php endforeach; ?>

  <div data-notice-message class="min-w-0 flex-1 break-words" role="<?= $noticeType === 'error' ? 'alert' : 'status'; ?>"><?= htmlspecialchars($noticeMessage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></div>

  <button type="button" data-notice-dismiss aria-label="Tutup pemberitahuan" class="-my-1 -mr-1 inline-flex size-7 shrink-0 items-center justify-center rounded-md hover:bg-black/5 focus-visible:outline focus-visible:outline-2">
    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">close</span>
  </button>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
  <?php
  $flash = $_SESSION['flash'];

  unset($_SESSION['flash']);

  if ($flash['type'] === 'success') {
      $flashClass = 'border-[#bbf7d0] bg-[#f0fdf4] text-[#15803d]';
      $flashIcon = 'check_circle';
  } elseif ($flash['type'] === 'error') {
      $flashClass = 'border-[#fecaca] bg-[#fef2f2] text-[#b91c1c]';
      $flashIcon = 'error';
  } else {
      $flashClass = 'border-[#bfdbfe] bg-[#eff6ff] text-[#1d4ed8]';
      $flashIcon = 'info';
  }
  ?>

  <div class="flex items-center gap-3 rounded-xl border px-4 py-3 font-body-sm text-body-sm <?= $flashClass; ?>" role="status">
    <span class="material-symbols-outlined text-[20px]" aria-hidden="true"><?= $flashIcon; ?></span>
    <span><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?></span>
  </div>
<?php endif; ?>

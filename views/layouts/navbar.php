<div class="flex-1 ml-[280px] flex flex-col min-h-screen bg-background">

  <header class="h-16 bg-surface-container-lowest border-b border-outline-variant sticky top-0 z-30 flex items-center px-8 justify-end">
    <div class="flex items-center gap-4">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center text-primary text-xs font-bold border border-outline-variant">
          <span class="material-symbols-outlined text-[18px] text-on-surface-variant">person</span>
        </div>

        <span class="font-label-md text-label-md font-semibold text-primary leading-tight">
          <?= htmlspecialchars($data['user']['full_name'] ?? 'Pengguna'); ?>
        </span>
      </div>
    </div>
  </header>


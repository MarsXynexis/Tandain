<template id="signer-row-template">
  <div data-signer-row>
    <div class="flex items-center justify-between gap-2 rounded-lg border border-outline-variant bg-surface-container-lowest p-3 transition-colors hover:border-brand sm:gap-3">
      <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3" data-signer-info>
        <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0" data-number></div>

        <div class="flex-1 min-w-0">
          <p class="font-label-lg text-label-lg font-bold text-primary truncate" data-signer-name></p>
          <p class="font-body-sm text-body-sm text-on-surface-variant truncate mt-0.5" data-signer-detail></p>
        </div>
      </div>

      <input type="hidden" data-signer-id>

      <button class="p-1.5 rounded-lg text-error hover:bg-error-container/40 transition shrink-0" data-remove-signer aria-label="Hapus penandatangan" title="Hapus Baris" type="button">
        <span class="material-symbols-outlined text-[20px]" aria-hidden="true">delete</span>
      </button>
    </div>

    <div class="hidden items-center gap-2 px-3 py-1 sm:gap-3" data-connector aria-hidden="true">
      <span class="size-9 shrink-0" data-order-spacer hidden></span>
      <div class="flex w-8 flex-col items-center justify-center text-brand">
        <div class="h-3.5 w-0.5 rounded-full bg-brand/80"></div>
        <span class="material-symbols-outlined -mt-1 text-[16px] leading-none">arrow_drop_down</span>
      </div>
    </div>
  </div>
</template>

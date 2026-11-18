<template id="signer-order-control">
  <details class="relative shrink-0" name="signer-order" data-signer-order hidden>
    <summary class="flex size-9 cursor-grab touch-none list-none select-none items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container-low focus-visible:outline-brand active:cursor-grabbing [&::-webkit-details-marker]:hidden" data-signer-grip aria-describedby="signer-order-hint" aria-keyshortcuts="ArrowUp ArrowDown" title="Ubah urutan penandatangan">
      <span class="material-symbols-outlined text-[22px]" aria-hidden="true">drag_indicator</span>
    </summary>

    <div class="absolute left-0 top-full z-20 mt-1 w-32 rounded-lg border border-outline-variant bg-white p-1 shadow-md">
      <button type="button" class="block w-full rounded px-2 py-1.5 text-left text-xs hover:bg-surface-container-low disabled:opacity-40" data-signer-step="up">Naikkan</button>
      <button type="button" class="block w-full rounded px-2 py-1.5 text-left text-xs hover:bg-surface-container-low disabled:opacity-40" data-signer-step="down">Turunkan</button>
    </div>
  </details>
</template>

<div id="signer-search-row" hidden>
  <div class="flex items-center gap-2 rounded-lg border border-dashed border-outline bg-surface p-3 sm:gap-3">
    <span class="size-9 shrink-0" id="search-order-spacer" aria-hidden="true" hidden></span>
    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-container text-xs font-bold text-white" id="search-row-number">1</div>

    <div class="relative min-w-0 flex-1">
      <label class="sr-only" for="signer-search-input">Cari penandatangan</label>
      <input class="h-10 w-full rounded-lg border border-outline-variant bg-white pl-9 pr-3 text-sm text-primary outline-none focus:border-brand focus:ring-1 focus:ring-brand" id="signer-search-input" aria-controls="signer-results" autocomplete="off" maxlength="100" placeholder="Cari nama, NIP/NIM, atau email PNJ" type="text">
      <span class="material-symbols-outlined pointer-events-none absolute left-2.5 top-3 text-[18px] text-on-surface-variant" aria-hidden="true">person_search</span>

      <div class="absolute inset-x-0 top-full z-20 mt-1 overflow-hidden rounded-lg border border-outline-variant bg-white shadow-md" id="signer-dropdown" hidden>
        <p class="px-3 py-2 text-xs text-on-surface-variant" id="signer-search-message"></p>
        <ul class="max-h-56 divide-y divide-surface-container overflow-y-auto" id="signer-results" aria-label="Hasil pencarian penandatangan"></ul>
      </div>
    </div>

    <button class="flex size-9 shrink-0 items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container" id="signer-search-close" aria-label="Tutup pencarian penandatangan" type="button">
      <span class="material-symbols-outlined text-[20px]" aria-hidden="true">close</span>
    </button>
  </div>
</div>

<aside class="fixed top-0 left-0 h-screen w-[280px] bg-surface-container-lowest border-r border-outline-variant z-40 flex flex-col justify-between p-4 select-none">

  <div class="flex flex-col gap-4">

    <div class="flex items-center gap-3 px-2 py-1">
      <img src="/Tandain/assets/img/logo.png" alt="Logo Tandain" class="w-10 h-10 rounded-xl object-cover shadow-sm">

      <div class="flex flex-col">
        <div class="flex items-center gap-1.5">
          <span class="font-headline-sm text-headline-sm font-bold text-primary tracking-tight">Tandain</span>
        </div>
      </div>
    </div>

    <nav class="flex flex-col gap-1 mt-1">

      <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= ($data['active_menu'] ?? '') === 'dashboard' ? 'bg-surface-container text-secondary font-semibold border-l-4 border-secondary' : 'text-on-surface-variant font-medium hover:bg-surface-container-low hover:text-on-surface'; ?> transition-all" href="/Tandain/dashboard">
        <span class="material-symbols-outlined text-[20px]" style="<?= ($data['active_menu'] ?? '') === 'dashboard' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>">home</span>
        <span class="font-label-md text-label-md">Beranda</span>
      </a>

      <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= ($data['active_menu'] ?? '') === 'apply' ? 'bg-surface-container text-secondary font-semibold border-l-4 border-secondary' : 'text-on-surface-variant font-medium hover:bg-surface-container-low hover:text-on-surface'; ?> transition-colors" href="/Tandain/document/create">
        <span class="material-symbols-outlined text-[20px]" style="<?= ($data['active_menu'] ?? '') === 'apply' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>">upload_file</span>
        <span class="font-label-md text-label-md">Ajukan Dokumen</span>
      </a>

      <div class="flex flex-col">
        <a class="flex items-center justify-between px-3 py-2.5 rounded-lg <?= in_array($data['active_menu'] ?? '', ['uploaded', 'signed']) ? 'text-primary font-semibold bg-surface-container-low' : 'text-on-surface-variant font-medium hover:bg-surface-container-low hover:text-on-surface'; ?> transition-colors" href="/Tandain/document">
          <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px] <?= in_array($data['active_menu'] ?? '', ['uploaded', 'signed']) ? 'text-secondary' : ''; ?>">folder_shared</span>
            <span class="font-label-md text-label-md">Dokumen Saya</span>
          </div>

          <span class="material-symbols-outlined text-[16px] text-outline">expand_more</span>
        </a>

        <div class="pl-9 pr-2 py-1 flex flex-col gap-1">
          <a class="px-2 py-1.5 rounded text-[13px] font-medium <?= ($data['active_menu'] ?? '') === 'uploaded' ? 'bg-surface-container text-secondary font-semibold border-l-4 border-secondary -ml-[14px] pl-[10px]' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'; ?> transition-colors" href="/Tandain/document">Diunggah</a>
          <a class="px-2 py-1.5 rounded text-[13px] font-medium <?= ($data['active_menu'] ?? '') === 'signed' ? 'bg-surface-container text-secondary font-semibold border-l-4 border-secondary -ml-[14px] pl-[10px]' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'; ?> transition-colors" href="/Tandain/document/signed">Ditandatangani</a>
        </div>
      </div>

      <div class="flex flex-col">
        <a class="flex items-center justify-between px-3 py-2.5 rounded-lg text-on-surface-variant font-medium hover:bg-surface-container-low hover:text-on-surface transition-colors" href="/Tandain/certificate">
          <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px]">verified_user</span>
            <span class="font-label-md text-label-md">Tanda Tangan & Sertifikat</span>
          </div>

          <span class="material-symbols-outlined text-[16px] text-outline">expand_more</span>
        </a>

        <div class="pl-9 pr-2 py-1 flex flex-col gap-1">
          <a class="px-2 py-1.5 rounded text-[13px] font-medium text-on-surface-variant hover:text-primary hover:bg-surface-container-low transition-colors" href="/Tandain/certificate">Gambar Tanda Tangan</a>
          <a class="px-2 py-1.5 rounded text-[13px] font-medium text-on-surface-variant hover:text-primary hover:bg-surface-container-low transition-colors" href="/Tandain/certificate">Sertifikat Digital</a>
        </div>
      </div>

    </nav>

  </div>

  <div class="border-t border-outline-variant pt-3 flex flex-col gap-1">
    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-[#b91c1c] font-medium hover:bg-[#fee2e2] transition-colors" href="/Tandain/logout">
      <span class="material-symbols-outlined text-[19px]">logout</span>
      <span class="font-label-md text-label-md">Keluar</span>
    </a>
  </div>

</aside>

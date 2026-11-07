<?php
$activeMenu = $data['active_menu'] ?? '';
$activeItemClass = 'bg-surface-container text-secondary font-semibold border-l-4 border-secondary';
$inactiveItemClass = 'text-on-surface-variant font-medium hover:bg-surface-container-low hover:text-on-surface';
$signatureActive = in_array($activeMenu, ['signature_image', 'certificate', 'certificate_create'], true);
$administrationActive = in_array($activeMenu, ['accounts', 'templates', 'archive'], true);
?>

<aside class="fixed top-0 left-0 h-screen w-[280px] bg-surface-container-lowest border-r border-outline-variant z-40 flex flex-col justify-between p-4 select-none overflow-y-auto">

  <div class="flex flex-col gap-4">

    <a class="flex items-center gap-3 px-2 py-1" href="/Tandain/dashboard">
      <img src="/Tandain/assets/img/logo.png" alt="Logo Tandain" class="w-10 h-10 rounded-xl object-cover shadow-sm">
      <span class="font-headline-sm text-headline-sm font-bold text-primary tracking-tight">Tandain</span>
    </a>

    <nav aria-label="Navigasi utama" class="flex flex-col gap-1 mt-1">

      <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $activeMenu === 'dashboard' ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/dashboard" <?= $activeMenu === 'dashboard' ? 'aria-current="page"' : ''; ?>>
        <span class="material-symbols-outlined text-[20px]" style="<?= $activeMenu === 'dashboard' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>">home</span>
        <span class="font-label-md text-label-md">Beranda</span>
      </a>

      <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $activeMenu === 'verification' ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="#" <?= $activeMenu === 'verification' ? 'aria-current="page"' : ''; ?>>
        <span class="material-symbols-outlined text-[20px]">verified_user</span>
        <span class="font-label-md text-label-md">Verifikasi Dokumen</span>
      </a>

      <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $activeMenu === 'apply' ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/document/create" <?= $activeMenu === 'apply' ? 'aria-current="page"' : ''; ?>>
        <span class="material-symbols-outlined text-[20px]" style="<?= $activeMenu === 'apply' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>">upload_file</span>
        <span class="font-label-md text-label-md">Ajukan Dokumen</span>
      </a>

      <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $activeMenu === 'uploaded' ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/document" <?= $activeMenu === 'uploaded' ? 'aria-current="page"' : ''; ?>>
        <span class="material-symbols-outlined text-[20px]" style="<?= $activeMenu === 'uploaded' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>">folder_open</span>
        <span class="font-label-md text-label-md">Dokumen Diunggah</span>
      </a>

      <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= in_array($activeMenu, ['approval', 'signed'], true) ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/inbox" <?= in_array($activeMenu, ['approval', 'signed'], true) ? 'aria-current="page"' : ''; ?>>
        <span class="material-symbols-outlined text-[20px]">fact_check</span>
        <span class="font-label-md text-label-md">Persetujuan Dokumen</span>
      </a>

      <div class="flex flex-col">
        <a class="flex items-center justify-between px-3 py-2.5 rounded-lg <?= $signatureActive ? 'text-primary font-semibold bg-surface-container-low' : $inactiveItemClass; ?> transition-colors" href="/Tandain/certificate" aria-expanded="true">
          <span class="flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px] <?= $signatureActive ? 'text-secondary' : ''; ?>">verified_user</span>
            <span class="font-label-md text-label-md">Tanda Tangan &amp; Sertifikat</span>
          </span>
          <span class="material-symbols-outlined text-[16px] text-outline">expand_more</span>
        </a>

        <div class="ml-8 pl-3 pr-2 py-1 flex flex-col gap-1 border-l border-dashed border-secondary/60">
          <a class="px-2 py-1.5 rounded text-[14px] font-medium <?= $activeMenu === 'certificate' ? 'bg-surface-container text-secondary font-semibold border-l-2 border-secondary pl-[10px]' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'; ?> transition-colors" href="/Tandain/certificate" <?= $activeMenu === 'certificate' ? 'aria-current="page"' : ''; ?>>Sertifikat Digital</a>
          <a class="px-2 py-1.5 rounded text-[14px] font-medium <?= $activeMenu === 'signature_image' ? 'bg-surface-container text-secondary font-semibold border-l-2 border-secondary pl-[10px]' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'; ?> transition-colors" href="/Tandain/certificate/sign" <?= $activeMenu === 'signature_image' ? 'aria-current="page"' : ''; ?>>Gambar Tanda Tangan</a>
        </div>
      </div>

      <div class="flex flex-col">
        <a class="flex items-center justify-between px-3 py-2.5 rounded-lg <?= $administrationActive ? 'text-primary font-semibold bg-surface-container-low' : $inactiveItemClass; ?> transition-colors" href="#" aria-expanded="true">
          <span class="flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px] <?= $administrationActive ? 'text-secondary' : ''; ?>">admin_panel_settings</span>
            <span class="font-label-md text-label-md">Administrasi</span>
          </span>
          <span class="material-symbols-outlined text-[16px] text-outline">expand_more</span>
        </a>

        <div class="ml-8 pl-3 pr-2 py-1 flex flex-col gap-1 border-l border-dashed border-secondary/60">
          <a class="px-2 py-1.5 rounded text-[14px] font-medium <?= $activeMenu === 'accounts' ? 'bg-surface-container text-secondary font-semibold border-l-2 border-secondary pl-[10px]' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'; ?> transition-colors" href="#" <?= $activeMenu === 'accounts' ? 'aria-current="page"' : ''; ?>>Manajemen Akun</a>
          <a class="px-2 py-1.5 rounded text-[14px] font-medium <?= $activeMenu === 'templates' ? 'bg-surface-container text-secondary font-semibold border-l-2 border-secondary pl-[10px]' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'; ?> transition-colors" href="#" <?= $activeMenu === 'templates' ? 'aria-current="page"' : ''; ?>>Template Surat</a>
          <a class="px-2 py-1.5 rounded text-[14px] font-medium <?= $activeMenu === 'archive' ? 'bg-surface-container text-secondary font-semibold border-l-2 border-secondary pl-[10px]' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'; ?> transition-colors" href="#" <?= $activeMenu === 'archive' ? 'aria-current="page"' : ''; ?>>Dokumen Arsip &amp; Tolak</a>
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

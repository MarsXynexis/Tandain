<?php
$activeMenu = $data['active_menu'] ?? '';
$activeItemClass = 'bg-[#eff6ff] text-secondary font-semibold border-l-4 border-secondary';
$inactiveItemClass = 'text-on-surface-variant font-medium hover:bg-surface-container-low hover:text-on-surface';

$userRole = $_SESSION['user']['role_name'] ?? '';

if (empty($userRole) && isset($_SESSION['user']['role_display_name'])) {
  if ($_SESSION['user']['role_display_name'] === 'Administrator Tata Usaha') {
    $userRole = 'admin_tu';
  }
}

$isAdmin = ($userRole === 'admin_tu');
$signatureActive = in_array($activeMenu, ['signature_image', 'certificate', 'certificate_create'], true);
$isApprovalActive = in_array($activeMenu, ['approval', 'signed'], true);
$isTriageActive = in_array($activeMenu, ['triage', 'triage_review'], true);
$isAccountsActive = in_array($activeMenu, ['accounts', 'accounts_create', 'accounts_edit'], true);
?>

<aside id="desktop-sidebar" class="fixed inset-y-0 left-0 z-40 hidden w-[280px] overflow-y-auto border-r border-outline-variant bg-surface-container-lowest lg:block">
  <div id="sidebar-panel" class="flex min-h-full flex-col justify-between gap-6 p-4 select-none">

    <div class="flex flex-col gap-4">

      <div class="flex items-center justify-between gap-2">
        <a class="flex items-center gap-3 px-2 py-1" href="/Tandain/dashboard">
          <img src="/Tandain/assets/img/app-icon.png" alt="" class="h-10 w-10 shrink-0 object-contain drop-shadow-sm">
          <span class="font-headline-sm text-headline-sm font-bold text-primary tracking-tight">Tandain</span>
        </a>

        <button type="button" id="sidebar-close" autofocus aria-label="Tutup menu navigasi" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container-low lg:hidden">
          <span class="material-symbols-outlined text-[22px]" aria-hidden="true">close</span>
        </button>
      </div>

      <nav aria-label="Navigasi utama" class="flex flex-col gap-1 mt-1">

        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $activeMenu === 'dashboard' ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/dashboard" <?= $activeMenu === 'dashboard' ? 'aria-current="page"' : ''; ?>>
          <span class="material-symbols-outlined text-[20px]" style="<?= $activeMenu === 'dashboard' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>">home</span>
          <span class="font-label-md text-label-md">Beranda</span>
        </a>

        <?php if ($isAdmin): ?>

          <div class="pt-2 pb-1 px-3">
            <p class="font-label-sm text-[11px] font-bold tracking-wider uppercase text-outline">Persuratan</p>
          </div>

          <a class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $isTriageActive ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/triage" <?= $isTriageActive ? 'aria-current="page"' : ''; ?>>
            <span class="material-symbols-outlined text-[20px]" style="<?= $isTriageActive ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>">move_to_inbox</span>
            <span class="font-label-md text-label-md">Disposisi Dokumen</span>
          </a>

          <a class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $activeMenu === 'archive' ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/archive" <?= $activeMenu === 'archive' ? 'aria-current="page"' : ''; ?>>
            <span class="material-symbols-outlined text-[20px]" style="<?= $activeMenu === 'archive' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>">folder_open</span>
            <span class="font-label-md text-label-md">Arsip Dokumen</span>
          </a>

          <div class="pt-3 pb-1 px-3">
            <p class="font-label-sm text-[11px] font-bold tracking-wider uppercase text-outline">Pengaturan Sistem</p>
          </div>

          <a class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $activeMenu === 'templates' ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/template" <?= $activeMenu === 'templates' ? 'aria-current="page"' : ''; ?>>
            <span class="material-symbols-outlined text-[20px]" style="<?= $activeMenu === 'templates' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>">description</span>
            <span class="font-label-md text-label-md">Template Surat</span>
          </a>

          <a class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $isAccountsActive ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/accounts" <?= $isAccountsActive ? 'aria-current="page"' : ''; ?>>
            <span class="material-symbols-outlined text-[20px]" style="<?= $isAccountsActive ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>">manage_accounts</span>
            <span class="font-label-md text-label-md">Manajemen Akun</span>
          </a>

          <a class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $activeMenu === 'audit' ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/audit" <?= $activeMenu === 'audit' ? 'aria-current="page"' : ''; ?>>
            <span class="material-symbols-outlined text-[20px]" style="<?= $activeMenu === 'audit' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>">history</span>
            <span class="font-label-md text-label-md">Log Audit Dokumen</span>
          </a>

        <?php else: ?>

          <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $activeMenu === 'apply' ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/document/create" <?= $activeMenu === 'apply' ? 'aria-current="page"' : ''; ?>>
            <span class="material-symbols-outlined text-[20px]" style="<?= $activeMenu === 'apply' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>">upload_file</span>
            <span class="font-label-md text-label-md">Ajukan Dokumen</span>
          </a>

          <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $activeMenu === 'uploaded' ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/document" <?= $activeMenu === 'uploaded' ? 'aria-current="page"' : ''; ?>>
            <span class="material-symbols-outlined text-[20px]" style="<?= $activeMenu === 'uploaded' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>">folder_open</span>
            <span class="font-label-md text-label-md">Dokumen Diunggah</span>
          </a>

          <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $isApprovalActive ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/inbox" <?= $isApprovalActive ? 'aria-current="page"' : ''; ?>>
            <span class="material-symbols-outlined text-[20px]" <?= $isApprovalActive ? "style=\"font-variation-settings: 'FILL' 1;\"" : ''; ?>>fact_check</span>
            <span class="font-label-md text-label-md">Persetujuan Dokumen</span>
          </a>

          <div class="flex flex-col">
            <button class="flex items-center justify-between px-3 py-2.5 rounded-lg <?= $signatureActive ? 'text-primary font-semibold bg-surface-container-low' : $inactiveItemClass; ?> transition-colors w-full text-left" id="signature-menu-btn" type="button" aria-controls="signature-submenu" aria-expanded="<?= $signatureActive ? 'true' : 'false'; ?>">
              <span class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[20px] <?= $signatureActive ? 'text-secondary' : ''; ?>" aria-hidden="true" <?= $signatureActive ? "style=\"font-variation-settings: 'FILL' 1;\"" : ''; ?>>draw</span>
                <span class="font-label-md text-label-md">Tanda Tangan &amp; Sertifikat</span>
              </span>
              <span class="material-symbols-outlined text-[16px] text-outline transition-transform duration-200 <?= $signatureActive ? 'rotate-180' : ''; ?>" id="signature-chevron">expand_more</span>
            </button>

            <div class="ml-8 pl-3 pr-2 flex-col gap-2 border-l border-dashed border-secondary/60 py-2 <?= $signatureActive ? 'flex' : 'hidden'; ?>" id="signature-submenu">
              <a class="px-2 py-2 rounded text-[12px] font-medium <?= in_array($activeMenu, ['certificate', 'certificate_create'], true) ? 'bg-[#eff6ff] text-secondary font-semibold border-l-2 border-secondary pl-[10px]' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'; ?> transition-colors" href="/Tandain/certificate" <?= in_array($activeMenu, ['certificate', 'certificate_create'], true) ? 'aria-current="page"' : ''; ?>>Sertifikat Digital</a>
              <a class="px-2 py-2 rounded text-[12px] font-medium <?= $activeMenu === 'signature_image' ? 'bg-[#eff6ff] text-secondary font-semibold border-l-2 border-secondary pl-[10px]' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'; ?> transition-colors" href="/Tandain/certificate/sign" <?= $activeMenu === 'signature_image' ? 'aria-current="page"' : ''; ?>>Gambar Tanda Tangan</a>
            </div>
          </div>

        <?php endif; ?>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $activeMenu === 'verification' ? $activeItemClass : $inactiveItemClass; ?> transition-colors" href="/Tandain/verification" <?= $activeMenu === 'verification' ? 'aria-current="page"' : ''; ?>>
          <span class="material-symbols-outlined text-[20px]" <?= $activeMenu === 'verification' ? "style=\"font-variation-settings: 'FILL' 1;\"" : ''; ?>>verified_user</span>
          <span class="font-label-md text-label-md">Verifikasi Dokumen</span>
        </a>
      </nav>

    </div>

    <div class="border-t border-outline-variant pt-3 flex flex-col gap-1">
      <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-[#b91c1c] font-medium hover:bg-[#fee2e2] transition-colors" href="/Tandain/logout">
        <span class="material-symbols-outlined text-[19px]">logout</span>
        <span class="font-label-md text-label-md">Keluar</span>
      </a>
    </div>

  </div>
</aside>

<dialog id="mobile-sidebar" aria-label="Menu navigasi" class="fixed inset-y-0 left-0 right-auto m-0 h-dvh max-h-none w-[min(280px,calc(100vw-3rem))] max-w-none overflow-y-auto overscroll-contain border-0 bg-surface-container-lowest p-0 text-on-surface backdrop:bg-slate-950/45"></dialog>

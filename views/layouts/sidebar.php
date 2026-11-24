<?php
$activeMenu = $data['active_menu'] ?? '';
$activeItemClass = 'bg-[#0D4E85]/95 text-white font-semibold shadow-[inset_0_1px_0_rgba(255,255,255,0.16),0_4px_12px_-2px_rgba(0,18,38,0.35)]';
$inactiveItemClass = 'text-[#D6E6F5] font-medium hover:bg-white/[0.08] hover:text-white';

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

<aside id="desktop-sidebar" class="fixed inset-y-0 left-0 z-40 hidden w-[280px] overflow-y-auto border-r border-[#0A3E70] bg-gradient-to-b from-[#002647] via-[#00315C] to-[#001F3B] text-white lg:block">
  <div id="sidebar-panel" class="relative flex min-h-full flex-col justify-between gap-6 overflow-hidden p-4 select-none">

    <svg class="pointer-events-none absolute -right-10 -top-10 h-48 w-48 select-none" viewBox="0 0 200 200" aria-hidden="true">
      <path d="M 38,0 C 52,58 112,86 200,74 L 200,0 Z" fill="#0B4C86" fill-opacity="0.34"/>
      <circle cx="148" cy="48" r="76" fill="none" stroke="#85BCF0" stroke-opacity="0.2" stroke-width="1.25"/>
      <circle cx="148" cy="48" r="54" fill="none" stroke="#F5C358" stroke-opacity="0.26" stroke-dasharray="5 6" stroke-width="1.25"/>
    </svg>

    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-[280px] overflow-hidden select-none" aria-hidden="true">
      <svg class="h-full w-full" viewBox="0 0 280 280" preserveAspectRatio="xMidYMax slice">
        <defs>
          <linearGradient id="sidebar-art-fade" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#00294E" stop-opacity="1"/>
            <stop offset="28%" stop-color="#00294E" stop-opacity="0"/>
          </linearGradient>
        </defs>

        <path d="M 0,128 C 72,88 176,144 280,96 L 280,280 L 0,280 Z" fill="#073D6E" fill-opacity="0.62"/>

        <g fill="#0C4D87" fill-opacity="0.66">
          <polygon points="10,120 56,90 102,120"/>
          <rect x="14" y="120" width="84" height="6"/>
          <rect x="20" y="126" width="10" height="58"/>
          <rect x="38" y="126" width="10" height="58"/>
          <rect x="64" y="126" width="10" height="58"/>
          <rect x="82" y="126" width="10" height="58"/>
          <rect x="10" y="182" width="92" height="10"/>
        </g>

        <g fill="#094275" fill-opacity="0.72">
          <path d="M 188,192 L 188,136 L 274,136 L 274,192 Z"/>
          <path d="M 196,192 L 196,156 A 10,10 0 0 1 216,156 L 216,192 Z" fill="#032647" fill-opacity="0.82"/>
          <path d="M 224,192 L 224,156 A 10,10 0 0 1 244,156 L 244,192 Z" fill="#032647" fill-opacity="0.82"/>
          <path d="M 252,192 L 252,156 A 10,10 0 0 1 272,156 L 272,192 Z" fill="#032647" fill-opacity="0.82"/>
        </g>

        <g transform="translate(114 82) rotate(-7)">
          <rect x="0" y="0" width="78" height="98" rx="6" fill="#0D5596" fill-opacity="0.72" stroke="#8AC0F2" stroke-opacity="0.26" stroke-width="1"/>
          <rect x="12" y="14" width="38" height="6" rx="2" fill="#8AC0F2" fill-opacity="0.38"/>
          <rect x="12" y="28" width="54" height="3.5" rx="1.5" fill="#8AC0F2" fill-opacity="0.28"/>
          <rect x="12" y="37" width="48" height="3.5" rx="1.5" fill="#8AC0F2" fill-opacity="0.28"/>
          <rect x="12" y="46" width="36" height="3.5" rx="1.5" fill="#8AC0F2" fill-opacity="0.28"/>
          <circle cx="23" cy="72" r="9.5" fill="#1567B0" fill-opacity="0.78" stroke="#F5C358" stroke-opacity="0.52" stroke-width="1.1"/>
          <path d="M 19,72 L 22,75 L 28,69" fill="none" stroke="#F5C358" stroke-opacity="0.72" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M 38,75 C 42,67 47,78 52,69 C 55,64 58,73 61,70" fill="none" stroke="#F5C358" stroke-opacity="0.65" stroke-width="1.6" stroke-linecap="round"/>
          <path d="M 62,70 L 66,57 L 73,61 L 65,71 Z" fill="#F5C358" fill-opacity="0.65"/>
          <line x1="38" y1="80" x2="66" y2="80" stroke="#8AC0F2" stroke-opacity="0.42" stroke-width="1.5" stroke-linecap="round"/>
        </g>

        <g fill="#0E5A9E" fill-opacity="0.66">
          <path d="M 6,198 C 8,172 24,158 40,156 C 40,174 26,190 6,198 Z"/>
          <path d="M 24,200 C 30,182 44,174 58,176 C 52,190 38,198 24,200 Z"/>
          <path d="M 272,198 C 270,174 252,162 238,160 C 240,178 254,192 272,198 Z"/>
        </g>

        <path d="M 0,192 C 72,168 158,214 280,178 L 280,280 L 0,280 Z" fill="#032A4F" fill-opacity="0.94"/>
        <path d="M 0,192 C 72,168 158,214 280,178" fill="none" stroke="#5CA0DE" stroke-opacity="0.28" stroke-width="1.2"/>
        <path d="M 0,224 C 94,198 188,242 280,214 L 280,280 L 0,280 Z" fill="#001933" fill-opacity="0.97"/>

        <rect x="0" y="0" width="280" height="76" fill="url(#sidebar-art-fade)"/>
      </svg>
    </div>

    <div class="relative z-10 flex flex-col gap-5">

      <div class="flex items-center justify-between gap-2 border-b border-white/10 pb-4">
        <a class="group flex min-w-0 items-center gap-3 rounded-xl px-1 py-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" href="/Tandain/dashboard">
          <img src="/Tandain/assets/img/app-icon.png" alt="Logo Tandain" class="h-10 w-10 shrink-0 rounded-xl object-contain ring-1 ring-white/25 shadow-sm transition-transform duration-150 group-hover:scale-[1.03]">

          <div class="min-w-0">
            <span class="block truncate font-headline-sm text-[18px] font-bold leading-tight tracking-tight text-white">Tandain eSign</span>
            <span class="mt-0.5 block truncate text-[11px] font-medium leading-tight text-[#BDD6EE]">Digital Signature Platform</span>
          </div>
        </a>

        <button type="button" id="sidebar-close" autofocus aria-label="Tutup menu navigasi" class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-[#D6E6F5] transition-colors hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358] lg:hidden">
          <span class="material-symbols-outlined text-[22px]" aria-hidden="true">close</span>
        </button>
      </div>

      <nav aria-label="Navigasi utama" class="flex flex-col gap-1.5">

        <a class="relative flex min-h-[44px] items-center gap-3 rounded-xl px-3.5 py-2.5 <?= $activeMenu === 'dashboard' ? $activeItemClass : $inactiveItemClass; ?> transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" href="/Tandain/dashboard" <?= $activeMenu === 'dashboard' ? 'aria-current="page"' : ''; ?>>
          <?php if ($activeMenu === 'dashboard'): ?>
            <span class="absolute left-0 top-1/2 h-5 w-[3.5px] -translate-y-1/2 rounded-r-full bg-[#F5C358]" aria-hidden="true"></span>
          <?php endif; ?>
          <span class="material-symbols-outlined text-[20px] <?= $activeMenu === 'dashboard' ? 'text-[#F5C358]' : ''; ?>" style="<?= $activeMenu === 'dashboard' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>" aria-hidden="true">home</span>
          <span class="font-label-md text-[13px] leading-snug">Beranda</span>
        </a>

        <?php if ($isAdmin): ?>

          <div class="pt-3 pb-1 px-3.5">
            <p class="font-label-sm text-[11px] font-semibold tracking-[0.05em] uppercase text-[#99C2EA]">Persuratan</p>
          </div>

          <a class="relative flex min-h-[44px] items-center gap-3 rounded-xl px-3.5 py-2.5 <?= $isTriageActive ? $activeItemClass : $inactiveItemClass; ?> transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" href="/Tandain/triage" <?= $isTriageActive ? 'aria-current="page"' : ''; ?>>
            <?php if ($isTriageActive): ?>
              <span class="absolute left-0 top-1/2 h-5 w-[3.5px] -translate-y-1/2 rounded-r-full bg-[#F5C358]" aria-hidden="true"></span>
            <?php endif; ?>
            <span class="material-symbols-outlined text-[20px] <?= $isTriageActive ? 'text-[#F5C358]' : ''; ?>" style="<?= $isTriageActive ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>" aria-hidden="true">move_to_inbox</span>
            <span class="font-label-md text-[13px] leading-snug">Disposisi Dokumen</span>
          </a>

          <a class="relative flex min-h-[44px] items-center gap-3 rounded-xl px-3.5 py-2.5 <?= $activeMenu === 'archive' ? $activeItemClass : $inactiveItemClass; ?> transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" href="/Tandain/archive" <?= $activeMenu === 'archive' ? 'aria-current="page"' : ''; ?>>
            <?php if ($activeMenu === 'archive'): ?>
              <span class="absolute left-0 top-1/2 h-5 w-[3.5px] -translate-y-1/2 rounded-r-full bg-[#F5C358]" aria-hidden="true"></span>
            <?php endif; ?>
            <span class="material-symbols-outlined text-[20px] <?= $activeMenu === 'archive' ? 'text-[#F5C358]' : ''; ?>" style="<?= $activeMenu === 'archive' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>" aria-hidden="true">folder_open</span>
            <span class="font-label-md text-[13px] leading-snug">Arsip Dokumen</span>
          </a>

          <div class="pt-3 pb-1 px-3.5">
            <p class="font-label-sm text-[11px] font-semibold tracking-[0.05em] uppercase text-[#99C2EA]">Pengaturan Sistem</p>
          </div>

          <a class="relative flex min-h-[44px] items-center gap-3 rounded-xl px-3.5 py-2.5 <?= $activeMenu === 'templates' ? $activeItemClass : $inactiveItemClass; ?> transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" href="/Tandain/template" <?= $activeMenu === 'templates' ? 'aria-current="page"' : ''; ?>>
            <?php if ($activeMenu === 'templates'): ?>
              <span class="absolute left-0 top-1/2 h-5 w-[3.5px] -translate-y-1/2 rounded-r-full bg-[#F5C358]" aria-hidden="true"></span>
            <?php endif; ?>
            <span class="material-symbols-outlined text-[20px] <?= $activeMenu === 'templates' ? 'text-[#F5C358]' : ''; ?>" style="<?= $activeMenu === 'templates' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>" aria-hidden="true">description</span>
            <span class="font-label-md text-[13px] leading-snug">Template Surat</span>
          </a>

          <a class="relative flex min-h-[44px] items-center gap-3 rounded-xl px-3.5 py-2.5 <?= $isAccountsActive ? $activeItemClass : $inactiveItemClass; ?> transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" href="/Tandain/accounts" <?= $isAccountsActive ? 'aria-current="page"' : ''; ?>>
            <?php if ($isAccountsActive): ?>
              <span class="absolute left-0 top-1/2 h-5 w-[3.5px] -translate-y-1/2 rounded-r-full bg-[#F5C358]" aria-hidden="true"></span>
            <?php endif; ?>
            <span class="material-symbols-outlined text-[20px] <?= $isAccountsActive ? 'text-[#F5C358]' : ''; ?>" style="<?= $isAccountsActive ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>" aria-hidden="true">manage_accounts</span>
            <span class="font-label-md text-[13px] leading-snug">Manajemen Akun</span>
          </a>

          <a class="relative flex min-h-[44px] items-center gap-3 rounded-xl px-3.5 py-2.5 <?= $activeMenu === 'audit' ? $activeItemClass : $inactiveItemClass; ?> transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" href="/Tandain/audit" <?= $activeMenu === 'audit' ? 'aria-current="page"' : ''; ?>>
            <?php if ($activeMenu === 'audit'): ?>
              <span class="absolute left-0 top-1/2 h-5 w-[3.5px] -translate-y-1/2 rounded-r-full bg-[#F5C358]" aria-hidden="true"></span>
            <?php endif; ?>
            <span class="material-symbols-outlined text-[20px] <?= $activeMenu === 'audit' ? 'text-[#F5C358]' : ''; ?>" style="<?= $activeMenu === 'audit' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>" aria-hidden="true">history</span>
            <span class="font-label-md text-[13px] leading-snug">Log Audit Dokumen</span>
          </a>

        <?php else: ?>

          <a class="relative flex min-h-[44px] items-center gap-3 rounded-xl px-3.5 py-2.5 <?= $activeMenu === 'apply' ? $activeItemClass : $inactiveItemClass; ?> transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" href="/Tandain/document/create" <?= $activeMenu === 'apply' ? 'aria-current="page"' : ''; ?>>
            <?php if ($activeMenu === 'apply'): ?>
              <span class="absolute left-0 top-1/2 h-5 w-[3.5px] -translate-y-1/2 rounded-r-full bg-[#F5C358]" aria-hidden="true"></span>
            <?php endif; ?>
            <span class="material-symbols-outlined text-[20px] <?= $activeMenu === 'apply' ? 'text-[#F5C358]' : ''; ?>" style="<?= $activeMenu === 'apply' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>" aria-hidden="true">upload_file</span>
            <span class="font-label-md text-[13px] leading-snug">Ajukan Dokumen</span>
          </a>

          <a class="relative flex min-h-[44px] items-center gap-3 rounded-xl px-3.5 py-2.5 <?= $activeMenu === 'uploaded' ? $activeItemClass : $inactiveItemClass; ?> transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" href="/Tandain/document" <?= $activeMenu === 'uploaded' ? 'aria-current="page"' : ''; ?>>
            <?php if ($activeMenu === 'uploaded'): ?>
              <span class="absolute left-0 top-1/2 h-5 w-[3.5px] -translate-y-1/2 rounded-r-full bg-[#F5C358]" aria-hidden="true"></span>
            <?php endif; ?>
            <span class="material-symbols-outlined text-[20px] <?= $activeMenu === 'uploaded' ? 'text-[#F5C358]' : ''; ?>" style="<?= $activeMenu === 'uploaded' ? 'font-variation-settings: &quot;FILL&quot; 1;' : ''; ?>" aria-hidden="true">folder_open</span>
            <span class="font-label-md text-[13px] leading-snug">Dokumen Diunggah</span>
          </a>

          <a class="relative flex min-h-[44px] items-center gap-3 rounded-xl px-3.5 py-2.5 <?= $isApprovalActive ? $activeItemClass : $inactiveItemClass; ?> transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" href="/Tandain/inbox" <?= $isApprovalActive ? 'aria-current="page"' : ''; ?>>
            <?php if ($isApprovalActive): ?>
              <span class="absolute left-0 top-1/2 h-5 w-[3.5px] -translate-y-1/2 rounded-r-full bg-[#F5C358]" aria-hidden="true"></span>
            <?php endif; ?>
            <span class="material-symbols-outlined text-[20px] <?= $isApprovalActive ? 'text-[#F5C358]' : ''; ?>" <?= $isApprovalActive ? "style=\"font-variation-settings: 'FILL' 1;\"" : ''; ?> aria-hidden="true">fact_check</span>
            <span class="font-label-md text-[13px] leading-snug">Persetujuan Dokumen</span>
          </a>

          <div class="flex flex-col">
            <button class="flex min-h-[44px] w-full items-center justify-between rounded-xl px-3.5 py-2.5 text-left <?= $signatureActive ? 'bg-[#0B4374]/85 text-white font-semibold' : $inactiveItemClass; ?> transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" id="signature-menu-btn" type="button" aria-controls="signature-submenu" aria-expanded="<?= $signatureActive ? 'true' : 'false'; ?>">
              <span class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[20px] <?= $signatureActive ? 'text-[#F5C358]' : ''; ?>" aria-hidden="true" <?= $signatureActive ? "style=\"font-variation-settings: 'FILL' 1;\"" : ''; ?>>draw</span>
                <span class="font-label-md text-[13px] leading-snug">Tanda Tangan &amp; Sertifikat</span>
              </span>
              <span class="material-symbols-outlined text-[16px] text-[#BDD6EE] transition-transform duration-200 <?= $signatureActive ? 'rotate-180' : ''; ?>" id="signature-chevron" aria-hidden="true">expand_more</span>
            </button>

            <div class="ml-7 flex-col gap-1.5 border-l border-dashed border-[#7CB3E8]/35 py-2 pl-3 pr-1 <?= $signatureActive ? 'flex' : 'hidden'; ?>" id="signature-submenu">
              <a class="relative flex min-h-[40px] items-center rounded-lg px-3 py-2 text-[12.5px] font-medium <?= in_array($activeMenu, ['certificate', 'certificate_create'], true) ? 'bg-[#0D4E85]/95 text-white font-semibold' : 'text-[#C5DBF0] hover:bg-white/[0.08] hover:text-white'; ?> transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" href="/Tandain/certificate" <?= in_array($activeMenu, ['certificate', 'certificate_create'], true) ? 'aria-current="page"' : ''; ?>>
                <?php if (in_array($activeMenu, ['certificate', 'certificate_create'], true)): ?>
                  <span class="absolute left-0 top-1/2 h-4 w-[3px] -translate-y-1/2 rounded-r-full bg-[#F5C358]" aria-hidden="true"></span>
                <?php endif; ?>
                <span>Sertifikat Digital</span>
              </a>

              <a class="relative flex min-h-[40px] items-center rounded-lg px-3 py-2 text-[12.5px] font-medium <?= $activeMenu === 'signature_image' ? 'bg-[#0D4E85]/95 text-white font-semibold' : 'text-[#C5DBF0] hover:bg-white/[0.08] hover:text-white'; ?> transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" href="/Tandain/certificate/sign" <?= $activeMenu === 'signature_image' ? 'aria-current="page"' : ''; ?>>
                <?php if ($activeMenu === 'signature_image'): ?>
                  <span class="absolute left-0 top-1/2 h-4 w-[3px] -translate-y-1/2 rounded-r-full bg-[#F5C358]" aria-hidden="true"></span>
                <?php endif; ?>
                <span>Gambar Tanda Tangan</span>
              </a>
            </div>
          </div>

        <?php endif; ?>

        <a class="relative flex min-h-[44px] items-center gap-3 rounded-xl px-3.5 py-2.5 <?= $activeMenu === 'verification' ? $activeItemClass : $inactiveItemClass; ?> transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]" href="/Tandain/verification" <?= $activeMenu === 'verification' ? 'aria-current="page"' : ''; ?>>
          <?php if ($activeMenu === 'verification'): ?>
            <span class="absolute left-0 top-1/2 h-5 w-[3.5px] -translate-y-1/2 rounded-r-full bg-[#F5C358]" aria-hidden="true"></span>
          <?php endif; ?>
          <span class="material-symbols-outlined text-[20px] <?= $activeMenu === 'verification' ? 'text-[#F5C358]' : ''; ?>" <?= $activeMenu === 'verification' ? "style=\"font-variation-settings: 'FILL' 1;\"" : ''; ?> aria-hidden="true">verified_user</span>
          <span class="font-label-md text-[13px] leading-snug">Verifikasi Dokumen</span>
        </a>

      </nav>

    </div>

    <div class="relative z-10 pt-2">
      <form method="POST" action="/Tandain/logout">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">

        <button type="submit" class="flex min-h-[44px] w-full items-center gap-3 rounded-xl border border-white/15 bg-[#001D38]/90 px-3.5 py-2.5 text-left font-medium text-[#E6F0FA] transition-colors duration-150 hover:border-[#DC2626] hover:bg-[#991B1B] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F5C358]">
          <span class="material-symbols-outlined text-[19px]" aria-hidden="true">logout</span>
          <span class="font-label-md text-[13px] leading-snug">Keluar</span>
        </button>
      </form>
    </div>

  </div>
</aside>

<dialog id="mobile-sidebar" aria-label="Menu navigasi" class="fixed inset-y-0 left-0 right-auto m-0 h-dvh max-h-none w-[min(280px,calc(100vw-3rem))] max-w-none overflow-y-auto overscroll-contain border-0 bg-gradient-to-b from-[#002647] via-[#00315C] to-[#001F3B] p-0 text-white backdrop:bg-slate-950/55"></dialog>

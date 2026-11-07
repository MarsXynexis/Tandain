<div class="flex-1 ml-[280px] flex flex-col min-h-screen bg-background">
  <?php
  $breadcrumbsByMenu = [
      'dashboard' => [['label' => 'Beranda']],
      'apply' => [['label' => 'Ajukan Dokumen']],
      'uploaded' => [['label' => 'Dokumen Diunggah']],
      'signed' => [['label' => 'Persetujuan Dokumen']],
      'signature_image' => [
          ['label' => 'Tanda Tangan & Sertifikat', 'href' => '/Tandain/certificate'],
          ['label' => 'Gambar Tanda Tangan'],
      ],
      'certificate' => [
          ['label' => 'Tanda Tangan & Sertifikat', 'href' => '/Tandain/certificate'],
          ['label' => 'Sertifikat Digital'],
      ],
      'certificate_create' => [
          ['label' => 'Tanda Tangan & Sertifikat', 'href' => '/Tandain/certificate'],
          ['label' => 'Sertifikat Digital', 'href' => '/Tandain/certificate'],
          ['label' => 'Buat Sertifikat Baru'],
      ],
      'verification' => [['label' => 'Verifikasi Dokumen']],
      'approval' => [['label' => 'Persetujuan Dokumen']],
      'accounts' => [
          ['label' => 'Administrasi'],
          ['label' => 'Manajemen Akun'],
      ],
      'templates' => [
          ['label' => 'Administrasi'],
          ['label' => 'Template Surat'],
      ],
      'archive' => [
          ['label' => 'Administrasi'],
          ['label' => 'Dokumen Arsip & Tolak'],
      ],
  ];
  $breadcrumbs = $breadcrumbsByMenu[$data['active_menu'] ?? ''] ?? [['label' => 'Beranda']];
  ?>

  <header class="h-16 min-h-16 bg-surface-container-lowest border-b border-outline-variant sticky top-0 z-30 flex items-center justify-between gap-6 px-8">
    <nav aria-label="Breadcrumb" class="min-w-0 overflow-x-auto">
      <ol class="flex items-center gap-2 whitespace-nowrap">
        <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
          <?php $isCurrent = $index === array_key_last($breadcrumbs); ?>
          <li class="flex items-center gap-2">
            <?php if ($index > 0): ?>
              <span class="font-body-sm text-body-sm text-on-surface-variant" aria-hidden="true">/</span>
            <?php endif; ?>
            <?php if (!$isCurrent && isset($breadcrumb['href'])): ?>
              <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="<?= htmlspecialchars($breadcrumb['href'], ENT_QUOTES, 'UTF-8'); ?>">
                <?= htmlspecialchars($breadcrumb['label'], ENT_QUOTES, 'UTF-8'); ?>
              </a>
            <?php else: ?>
              <span class="font-body-sm text-body-sm <?= $isCurrent ? 'font-semibold text-primary' : 'text-on-surface-variant'; ?>" <?= $isCurrent ? 'aria-current="page"' : ''; ?>>
                <?= htmlspecialchars($breadcrumb['label'], ENT_QUOTES, 'UTF-8'); ?>
              </span>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ol>
    </nav>

    <div class="flex shrink-0 items-center gap-3">
      <div class="flex h-8 w-8 items-center justify-center rounded-full border border-outline text-on-surface-variant">
        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">account_circle</span>
      </div>
      <div class="flex min-w-0 flex-col">
        <span class="max-w-[220px] truncate font-label-md text-label-md font-semibold leading-tight text-primary">
          <?= htmlspecialchars($data['user']['full_name'] ?? 'Pengguna', ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <?php $userRoleLabel = $data['user']['position'] ?? $data['user']['role_display_name'] ?? ''; ?>
        <?php if ($userRoleLabel !== ''): ?>
          <span class="max-w-[220px] truncate text-[11px] leading-4 text-on-surface-variant">
            <?= htmlspecialchars($userRoleLabel, ENT_QUOTES, 'UTF-8'); ?>
          </span>
        <?php endif; ?>
      </div>
    </div>
  </header>

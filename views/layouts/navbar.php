<div class="flex min-h-screen min-w-0 flex-1 flex-col bg-background lg:ml-[280px]">
  <?php
  $breadcrumbsByMenu = [
    'dashboard' => [['label' => 'Beranda']],
    'apply' => [['label' => 'Ajukan Dokumen']],
    'uploaded' => [
      ['label' => 'Dokumen Diunggah', 'href' => '/Tandain/document']
    ],
    'signed' => [
      ['label' => 'Persetujuan Dokumen', 'href' => '/Tandain/inbox'],

    ],
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
    'triage' => [['label' => 'Disposisi Dokumen']],
    'triage_review' => [
      ['label' => 'Disposisi Dokumen', 'href' => '/Tandain/triage'],
      ['label' => 'Telaah Dokumen'],
    ],
    'accounts' => [
      ['label' => 'Administrasi'],
      ['label' => 'Manajemen Akun'],
    ],
    'accounts_create' => [
      ['label' => 'Administrasi'],
      ['label' => 'Manajemen Akun', 'href' => '/Tandain/accounts'],
      ['label' => 'Tambah Akun'],
    ],
    'accounts_edit' => [
      ['label' => 'Administrasi'],
      ['label' => 'Manajemen Akun', 'href' => '/Tandain/accounts'],
      ['label' => 'Ubah Akun'],
    ],
    'templates' => [
      ['label' => 'Administrasi'],
      ['label' => 'Template Surat'],
    ],
    'audit' => [
      ['label' => 'Administrasi'],
      ['label' => 'Log Audit Dokumen'],
    ],
    'archive' => [
      ['label' => 'Administrasi'],
      ['label' => 'Arsip Dokumen'],
    ],
  ];

  $breadcrumbs = $breadcrumbsByMenu[$data['active_menu'] ?? ''] ?? [['label' => 'Beranda']];

  $documentTitle = trim((string) (
    $data['document']['title']
    ?? $data['document']['document_title']
    ?? $data['documentTitle']
    ?? $data['document_title']
    ?? ''
  ));

  if ($documentTitle !== '' && in_array($data['active_menu'] ?? '', ['uploaded', 'signed', 'triage_review'], true)) {
    $breadcrumbs[] = ['label' => $documentTitle];
  }
  ?>

  <header class="sticky top-0 z-30 flex h-16 min-h-16 items-center justify-between gap-3 border-b border-outline-variant bg-surface-container-lowest px-6 lg:gap-6 lg:px-8">
    <div class="flex min-w-0 items-center gap-2 lg:gap-0">
      <button type="button" id="sidebar-open" aria-label="Buka menu navigasi" aria-controls="mobile-sidebar" aria-expanded="false" aria-haspopup="dialog" class="-ml-2 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container-low lg:hidden">
        <span class="material-symbols-outlined text-[22px]" aria-hidden="true">menu</span>
      </button>

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
    </div>

    <div class="flex max-w-[45%] min-w-0 shrink-0 flex-col text-right sm:max-w-[220px]">
      <span class="truncate font-label-md text-label-md font-semibold leading-tight text-primary">
        <?= htmlspecialchars($data['user']['full_name'] ?? 'Pengguna', ENT_QUOTES, 'UTF-8'); ?>
      </span>

      <?php $userRoleLabel = $data['user']['position'] ?? $data['user']['role_display_name'] ?? ''; ?>
      <?php if ($userRoleLabel !== ''): ?>
        <span class="truncate text-[11px] leading-4 text-on-surface-variant">
          <?= htmlspecialchars($userRoleLabel, ENT_QUOTES, 'UTF-8'); ?>
        </span>
      <?php endif; ?>
    </div>
  </header>
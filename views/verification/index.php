<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= htmlspecialchars($data['title'] ?? 'Verifikasi Dokumen - Tandain', ENT_QUOTES, 'UTF-8'); ?></title>

  <link href="/Tandain/assets/css/fonts.css" rel="stylesheet">
  <link href="/Tandain/assets/css/material-symbols.css" rel="stylesheet">

  <script src="/Tandain/assets/js/tailwind.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            "surface-tint": "#515b92",
            "on-surface-variant": "#45464f",
            "on-error-container": "#93000a",
            "outline-variant": "#c6c5d0",
            "surface-container": "#eeeeee",
            "on-primary": "#ffffff",
            "background": "#f9f9f9",
            "error-container": "#ffdad6",
            "surface-bright": "#f9f9f9",
            "inverse-surface": "#303030",
            "outline": "#767680",
            "on-surface": "#1b1b1b",
            "primary": "#1b204a",
            "primary-container": "#151b3d",
            "secondary-container": "#dbe1ff",
            "surface-container-low": "#f3f3f3",
            "surface": "#f9f9f9",
            "surface-container-high": "#e8e8e8",
            "on-secondary-container": "#00174b",
            "surface-container-lowest": "#ffffff",
            "secondary": "#004B87"
          },
          fontFamily: {
            "body-lg": ["Inter", "sans-serif"],
            "body-md": ["Inter", "sans-serif"],
            "headline-xl-mobile": ["Plus Jakarta Sans", "sans-serif"],
            "headline-md": ["Plus Jakarta Sans", "sans-serif"],
            "label-md": ["Plus Jakarta Sans", "sans-serif"],
            "body-sm": ["Inter", "sans-serif"],
            "label-sm": ["Plus Jakarta Sans", "sans-serif"],
            "label-lg": ["Plus Jakarta Sans", "sans-serif"],
            "code-tabular": ["JetBrains Mono", "monospace"],
            "headline-xl": ["Plus Jakarta Sans", "sans-serif"],
            "headline-sm": ["Plus Jakarta Sans", "sans-serif"],
            "headline-lg": ["Plus Jakarta Sans", "sans-serif"],
            "headline-lg-mobile": ["Plus Jakarta Sans", "sans-serif"]
          },
          fontSize: {
            "body-lg": ["16px", { lineHeight: "26px", letterSpacing: "-0.005em", fontWeight: "400" }],
            "body-md": ["14px", { lineHeight: "22px", letterSpacing: "0", fontWeight: "400" }],
            "headline-xl-mobile": ["28px", { lineHeight: "36px", letterSpacing: "-0.02em", fontWeight: "700" }],
            "headline-md": ["22px", { lineHeight: "28px", letterSpacing: "-0.015em", fontWeight: "600" }],
            "label-md": ["12px", { lineHeight: "16px", letterSpacing: "0.02em", fontWeight: "600" }],
            "body-sm": ["12px", { lineHeight: "18px", letterSpacing: "0.005em", fontWeight: "400" }],
            "label-sm": ["11px", { lineHeight: "14px", letterSpacing: "0.04em", fontWeight: "700" }],
            "label-lg": ["14px", { lineHeight: "20px", letterSpacing: "0.01em", fontWeight: "600" }],
            "code-tabular": ["13px", { lineHeight: "18px", letterSpacing: "0.02em", fontWeight: "500" }],
            "headline-xl": ["36px", { lineHeight: "44px", letterSpacing: "-0.025em", fontWeight: "700" }],
            "headline-sm": ["18px", { lineHeight: "24px", letterSpacing: "-0.01em", fontWeight: "600" }],
            "headline-lg-mobile": ["24px", { lineHeight: "32px", letterSpacing: "-0.015em", fontWeight: "700" }],
            "headline-lg": ["30px", { lineHeight: "38px", letterSpacing: "-0.02em", fontWeight: "700" }]
          }
        }
      }
    };
  </script>

  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    h1, h2, h3, h4, h5, h6, .font-headline-lg, .font-headline-md, .font-headline-sm, .font-headline-xl {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .material-symbols-outlined {
      font-family: 'Material Symbols Outlined' !important;
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      display: inline-block;
      vertical-align: middle;
      line-height: 1;
    }

    .paper-shadow {
      box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.08), 0 4px 14px 0 rgba(15, 23, 42, 0.05);
    }
  </style>
</head>

<body class="bg-background text-on-surface antialiased min-h-screen flex flex-col font-body-md text-body-md">

  <header class="bg-surface-container-lowest border-b border-outline-variant/40 sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
      <a href="/Tandain/verification" class="flex items-center gap-3">
        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-[#c7c5cf] bg-[#151b3d] p-px shadow-[0_1px_1px_rgba(0,0,0,0.05)]" role="img" aria-label="Logo Tandain">
          <span class="material-symbols-outlined text-[18px] text-[#E3DFFF]" aria-hidden="true">draw</span>
        </span>
        <span class="font-headline-sm text-headline-sm font-bold text-primary tracking-tight">Tandain</span>
      </a>

      <a href="/Tandain/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-label-lg font-label-lg text-primary border border-outline-variant hover:bg-surface-container-low rounded-lg transition-colors duration-150 shadow-sm">
        <span class="material-symbols-outlined text-[20px]">dashboard</span>
        <span>Dashboard</span>
      </a>
    </div>
  </header>

  <?php
  $status = $data['status'] ?? 'upload';
  $document = $data['document'] ?? null;
  $signers = is_array($document['signers'] ?? null) ? $document['signers'] : [];
  $isExpired = !empty($data['is_expired']);

  $previewFilePath = '';
  if (!empty($document['final_file_path'])) {
    $previewFilePath = $document['final_file_path'];
  } elseif (!empty($document['original_file_path'])) {
    $previewFilePath = $document['original_file_path'];
  }
  ?>

  <?php if (($status === 'success' || $status === 'expired') && !empty($document)): ?>

    <main class="flex-1 max-w-6xl w-full mx-auto px-6 py-8">
      <div class="max-w-5xl mx-auto space-y-7">

        <?php if ($status === 'expired' || $isExpired): ?>

          <section class="bg-[#fffbeb] border border-[#fde68a] rounded-xl p-5 shadow-sm">
            <div class="flex items-center gap-4">
              <div class="flex-shrink-0 h-11 w-11 rounded-full bg-surface-container-lowest shadow-sm border border-[#fef3c7] flex items-center justify-center text-[#d97706]">
                <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;">warning</span>
              </div>
              <div class="flex-1">
                <h2 class="font-headline-sm text-headline-sm font-bold text-[#78350f]">Dokumen Kedaluwarsa</h2>
                <p class="font-body-md text-body-md text-[#92400e] mt-0.5 leading-relaxed">Tanda tangan terverifikasi asli, tetapi masa berlaku dokumen telah habis.</p>
              </div>
            </div>
          </section>

        <?php else: ?>

          <section class="bg-emerald-50 border border-emerald-200/90 rounded-xl p-5 shadow-sm">
            <div class="flex items-center gap-4">
              <div class="flex-shrink-0 h-11 w-11 rounded-full bg-surface-container-lowest shadow-sm border border-emerald-100 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
              </div>
              <div class="flex-1">
                <h2 class="font-headline-sm text-headline-sm font-bold text-emerald-950">Dokumen Valid</h2>
                <p class="font-body-md text-body-md text-emerald-900/90 mt-0.5 leading-relaxed">Dokumen resmi terdaftar dan masa berlaku masih aktif</p>
              </div>
            </div>
          </section>

        <?php endif; ?>

        <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl shadow-sm overflow-hidden">
          <div class="grid grid-cols-1 md:grid-cols-12 divide-y md:divide-y-0 md:divide-x divide-outline-variant/50">

            <div class="md:col-span-7 p-6 space-y-5 flex flex-col justify-between">
              <div class="flex items-center justify-between border-b border-outline-variant/40 pb-3">
                <div class="flex items-center gap-2.5">
                  <div class="p-1.5 rounded-lg bg-surface-container-low text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">description</span>
                  </div>
                  <h2 class="font-headline-md text-headline-sm font-bold text-primary tracking-tight">Informasi Dokumen</h2>
                </div>
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant" title="Informasi Berkas">info</span>
              </div>

              <div class="space-y-3.5 flex-1">
                <div class="flex items-start justify-between py-1 border-b border-outline-variant/30">
                  <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Judul Dokumen</span>
                  <span class="font-headline-sm text-[14px] font-bold text-primary text-right max-w-[60%]"><?= htmlspecialchars($document['title'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>

                <div class="flex items-start justify-between py-1 border-b border-outline-variant/30">
                  <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Nomor Dokumen</span>
                  <span class="font-body-md text-[13px] font-medium text-slate-700 text-right font-code-tabular"><?= htmlspecialchars($document['document_number'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>

                <div class="flex items-start justify-between py-1 border-b border-outline-variant/30">
                  <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Pemohon</span>
                  <div class="text-right">
                    <span class="font-headline-sm text-[13px] font-bold text-primary block"><?= htmlspecialchars($document['requester_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="font-code-tabular text-[11px] text-slate-500 block"><?= htmlspecialchars($document['requester_nim'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                </div>

                <div class="flex items-start justify-between py-1 border-b border-outline-variant/30">
                  <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Tanggal Disahkan</span>
                  <span class="font-body-md text-[13px] font-medium text-slate-700 text-right"><?= !empty($document['issued_at']) ? date('d M Y, H:i', strtotime($document['issued_at'])) . ' WIB' : (!empty($document['created_at']) ? date('d M Y, H:i', strtotime($document['created_at'])) . ' WIB' : '-'); ?></span>
                </div>

                <div class="flex items-start justify-between py-1">
                  <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Masa Berlaku</span>
                  <span class="font-body-md text-[13px] font-medium text-slate-800 text-right"><?= !empty($document['expires_at']) ? date('d M Y', strtotime($document['expires_at'])) : 'Selamanya'; ?></span>
                </div>
              </div>
            </div>

            <div class="md:col-span-5 p-6 bg-surface-container-low/40 flex flex-col justify-between space-y-4">
              <div class="flex items-center justify-between border-b border-outline-variant/40 pb-3">
                <div class="flex items-center gap-2">
                  <h2 class="font-headline-md text-headline-sm font-bold text-primary tracking-tight">Daftar Penandatangan</h2>
                </div>
                <span class="text-xs font-semibold text-on-surface-variant font-code-tabular"><?= count($signers); ?> Pejabat</span>
              </div>

              <div class="space-y-3 flex-1 flex flex-col justify-center">

                <?php if (empty($signers)): ?>
                  <div class="p-6 text-center text-on-surface-variant text-xs bg-surface-container-lowest rounded-xl border border-outline-variant/50">
                    Belum ada penandatangan yang ditugaskan pada dokumen ini.
                  </div>
                <?php else: ?>
                  <?php foreach ($signers as $signer): ?>
                    <?php
                    $isSigned = ($signer['status'] === 'SIGNED');
                    $isCurrent = ($signer['status'] === 'CURRENT_TURN');
                    $isRejected = ($signer['status'] === 'REJECTED');

                    $signerStatusBadge = 'bg-surface-container text-on-surface-variant';
                    $signerStatusText = 'Menunggu Giliran';

                    if ($isSigned) {
                      $signerStatusBadge = 'bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]';
                      $signerStatusText = 'Tertandatangani';
                    } elseif ($isCurrent) {
                      $signerStatusBadge = 'bg-[#dbeafe] text-[#1d4ed8] border border-[#bfdbfe]';
                      $signerStatusText = 'Giliran Menandatangani';
                    } elseif ($isRejected) {
                      $signerStatusBadge = 'bg-[#fee2e2] text-[#b91c1c] border border-[#fecaca]';
                      $signerStatusText = 'Menolak Menandatangani';
                    }
                    ?>
                    <div class="bg-surface-container-lowest border border-outline-variant/50 rounded-xl p-3.5 shadow-sm space-y-2">
                      <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                          <div class="h-7 w-7 rounded-full bg-primary-container text-surface-container-lowest font-headline-sm text-[12px] font-bold flex items-center justify-center flex-shrink-0">
                            <?= htmlspecialchars((string)($signer['signing_order'] ?? 1), ENT_QUOTES, 'UTF-8'); ?>
                          </div>
                          <div class="min-w-0">
                            <h3 class="font-headline-sm text-[13px] font-bold text-primary truncate"><?= htmlspecialchars($signer['full_name'] ?? 'Penandatangan', ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="font-body-sm text-[11px] text-on-surface-variant truncate"><?= htmlspecialchars($signer['position'] ?? 'Civitas Akademika', ENT_QUOTES, 'UTF-8'); ?></p>
                          </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-semibold <?= $signerStatusBadge; ?> shrink-0"><?= $signerStatusText; ?></span>
                      </div>

                      <div class="flex items-center justify-between text-[10.5px] text-slate-500 pt-1.5 border-t border-slate-100">
                        <?php if ($isSigned && !empty($signer['signed_at'])): ?>
                          <span class="font-code-tabular font-medium text-slate-600"><?= date('d M Y, H:i', strtotime($signer['signed_at'])) . ' WIB'; ?></span>
                          <?php if (!empty($signer['certificate_serial'])): ?>
                            <span class="font-code-tabular text-[10px] text-slate-400 truncate max-w-[140px]" title="Seri Sertifikat: <?= htmlspecialchars($signer['certificate_serial'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($signer['certificate_serial'], ENT_QUOTES, 'UTF-8'); ?></span>
                          <?php endif; ?>
                        <?php else: ?>
                          <span class="font-body-sm text-[11px] text-slate-400">Belum dibubuhkan</span>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>

              </div>
            </div>

          </div>
        </div>

        <div class="pt-1">
          <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
            <?php if (!empty($previewFilePath)): ?>
              <a href="/Tandain/<?= htmlspecialchars($previewFilePath, ENT_QUOTES, 'UTF-8'); ?>" download class="flex-1 w-full bg-[#004B87] hover:bg-[#003b6b] active:scale-[0.99] text-white font-label-lg text-label-lg py-3.5 px-5 rounded-lg flex items-center justify-center gap-2.5 shadow-sm transition-all duration-150">
                <span class="material-symbols-outlined text-[20px]">download</span>
                <span>Unduh Salinan PDF</span>
              </a>
            <?php endif; ?>

            <a href="/Tandain/verification" class="w-full sm:w-auto bg-surface-container-lowest hover:bg-surface-container-low text-primary border border-outline-variant/60 font-label-lg text-label-lg py-3.5 px-6 rounded-lg flex items-center justify-center gap-2 shadow-sm transition-all duration-150">
              <span class="material-symbols-outlined text-[20px]">document_scanner</span>
              <span>Verifikasi Dokumen Lain</span>
            </a>
          </div>
        </div>

        <?php if (!empty($previewFilePath)): ?>
          <section class="space-y-4 pt-4">
            <div class="flex items-center justify-between border-b border-outline-variant/40 pb-3">
              <h2 class="font-headline-md text-headline-md font-bold text-primary tracking-tight">Pratinjau Dokumen Asli</h2>
              <div class="flex items-center gap-2">
                <div class="hidden sm:inline-flex items-center gap-2 px-2.5 py-1 rounded-lg border border-outline-variant/50 bg-surface-container-lowest text-on-surface-variant text-xs">
                  <span class="material-symbols-outlined text-[16px]">search</span>
                  <span class="font-medium text-slate-700">100%</span>
                  <span class="material-symbols-outlined text-[16px]">zoom_in</span>
                </div>
                <a href="/Tandain/<?= htmlspecialchars($previewFilePath, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="inline-flex items-center justify-center p-1 rounded-lg border border-outline-variant/50 bg-surface-container-lowest text-on-surface-variant hover:text-primary hover:bg-surface-container-low transition-colors" title="Buka Pratinjau Penuh">
                  <span class="material-symbols-outlined text-[18px]">fullscreen</span>
                </a>
              </div>
            </div>

            <div class="bg-surface-container-low p-3 sm:p-5 rounded-xl border border-outline-variant/50 flex justify-center">
              <iframe src="/Tandain/<?= htmlspecialchars($previewFilePath, ENT_QUOTES, 'UTF-8'); ?>" class="w-full h-[640px] rounded-lg border border-outline-variant bg-white shadow-sm" title="Pratinjau Dokumen Asli"></iframe>
            </div>
          </section>
        <?php endif; ?>

      </div>
    </main>

  <?php elseif ($status === 'error'): ?>

    <main class="flex-1 max-w-6xl w-full mx-auto px-6 py-8">
      <div class="max-w-4xl mx-auto py-10 sm:py-12 space-y-8">
        <div class="space-y-2 text-center">
          <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Verifikasi Dokumen</h1>
          <p class="font-body-md text-body-md text-on-surface-variant">Validasi keaslian berkas dan tanda tangan elektronik resmi.</p>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-8 sm:p-10 shadow-sm space-y-8">
          <div class="rounded-xl border border-red-200 bg-red-50/70 p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0 shadow-sm">
              <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">cancel</span>
            </div>
            <div class="flex-1 space-y-1">
              <h3 class="text-lg font-bold text-red-800 font-headline-sm">Dokumen Tidak Terdaftar</h3>
              <p class="text-sm text-red-700 font-body-sm leading-relaxed">Berkas tidak ditemukan dalam pangkalan data sistem atau telah mengalami modifikasi/manipulasi digital.</p>
            </div>
          </div>

          <div class="bg-surface-container-low border border-outline-variant/50 rounded-xl p-6 space-y-4">
            <h4 class="text-sm font-semibold text-primary font-headline-sm inline-flex items-center gap-2 pb-3 border-b border-outline-variant/40 w-full">
              <span class="material-symbols-outlined text-[20px] text-on-surface-variant">description</span>
              <span>Rincian Berkas Pemeriksaan</span>
            </h4>
            <div class="grid grid-cols-1 gap-4 text-left font-body-sm sm:grid-cols-2">
              <div class="space-y-1">
                <span class="text-xs text-on-surface-variant font-medium">Nama Berkas</span>
                <p class="text-sm font-semibold text-primary break-all"><?= htmlspecialchars($data['file_name'] ?: 'berkas_pemeriksaan.pdf', ENT_QUOTES, 'UTF-8'); ?></p>
              </div>
              <div class="space-y-1">
                <span class="text-xs text-on-surface-variant font-medium">Ukuran Berkas</span>
                <p class="text-sm font-semibold text-primary"><?= ($data['file_size'] > 0) ? number_format($data['file_size'] / (1024 * 1024), 2) . ' MB' : '-'; ?></p>
              </div>
            </div>
          </div>

          <div class="flex flex-col gap-3 pt-2">
            <a href="/Tandain/verification" class="w-full bg-[#004B87] hover:bg-[#003b6b] active:scale-[0.99] text-white font-label-lg text-label-lg py-3.5 px-5 rounded-lg flex items-center justify-center shadow-sm transition-all duration-150">
              <span>Unggah Ulang Berkas</span>
            </a>
          </div>
        </div>
      </div>
    </main>

  <?php else: ?>

    <main class="flex-1 max-w-6xl w-full mx-auto px-6 py-8">
      <div class="max-w-4xl mx-auto py-10 sm:py-12 space-y-8">
        <div class="space-y-2 text-center">
          <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Verifikasi Dokumen</h1>
          <p class="font-body-md text-body-md text-on-surface-variant">Validasi keaslian berkas dan tanda tangan elektronik resmi.</p>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-8 sm:p-10 shadow-sm space-y-8">
          <form method="POST" action="/Tandain/verification/check" enctype="multipart/form-data" class="space-y-8" id="verification-form">
            <div class="border-2 border-dashed border-outline-variant hover:border-secondary rounded-xl py-12 px-8 flex flex-col items-center justify-center text-center bg-surface-container-low cursor-pointer transition-colors duration-200" id="drop-zone">
              <div class="w-14 h-14 rounded-xl bg-surface-container-lowest border border-outline-variant/50 flex items-center justify-center text-[#004B87] shadow-sm mb-4">
                <span class="material-symbols-outlined text-[32px]">picture_as_pdf</span>
              </div>

              <h3 id="drop-zone-text" class="font-headline-sm text-headline-sm font-semibold text-primary mb-1.5">Tarik file PDF ke sini, atau klik Pilih File</h3>
              <p id="drop-zone-subtext" class="font-body-sm text-body-sm text-on-surface-variant mb-6">Format PDF (Maks. 2 MB)</p>

              <input type="file" name="document_file" accept="application/pdf,.pdf" class="hidden" id="file-upload" required>
              <button type="button" id="browse-btn" class="bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-primary font-label-lg text-label-lg py-2.5 px-6 rounded-lg inline-flex items-center shadow-sm transition-colors duration-150">
                <span>Pilih File</span>
              </button>
            </div>

            <div class="flex flex-col gap-3 pt-1">
              <button type="submit" id="submit-btn" disabled class="w-full bg-[#004B87] text-white font-label-lg text-label-lg py-3 px-5 rounded-lg flex items-center justify-center shadow-sm transition-all duration-150 opacity-50 cursor-not-allowed">
                <span>Periksa Dokumen</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>

  <?php endif; ?>

  <footer class="mt-auto py-6 border-t border-outline-variant/40 bg-surface-container-lowest text-center">
    <div class="max-w-4xl mx-auto px-6 text-center text-on-surface-variant font-body-sm text-body-sm">
      <p>&copy; 2026 Politeknik Negeri Jakarta</p>
    </div>
  </footer>

</body>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('drop-zone');
    const fileUpload = document.getElementById('file-upload');
    const browseBtn = document.getElementById('browse-btn');
    const dropZoneText = document.getElementById('drop-zone-text');
    const dropZoneSubtext = document.getElementById('drop-zone-subtext');
    const submitBtn = document.getElementById('submit-btn');
    const form = document.getElementById('verification-form');

    if (!dropZone || !fileUpload) return;

    const defaultText = dropZoneText.textContent;
    const defaultSubtext = dropZoneSubtext.textContent;
    const MAX_SIZE = 2 * 1024 * 1024;

    function enableSubmit() {
      if (!submitBtn) return;
      submitBtn.removeAttribute('disabled');
      submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
      submitBtn.classList.add('hover:bg-[#003b6b]', 'active:scale-[0.99]', 'cursor-pointer');
    }

    function disableSubmit() {
      if (!submitBtn) return;
      submitBtn.setAttribute('disabled', 'disabled');
      submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
      submitBtn.classList.remove('hover:bg-[#003b6b]', 'active:scale-[0.99]', 'cursor-pointer');
    }

    function handleFile() {
      const file = fileUpload.files && fileUpload.files[0];
      if (!file) {
        disableSubmit();
        return;
      }

      const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
      if (!isPdf) {
        alert('Format berkas tidak valid. Harap pilih berkas PDF.');
        resetDropZone();
        return;
      }

      if (file.size > MAX_SIZE) {
        alert('Ukuran berkas melebihi batas maksimal 2 MB.');
        resetDropZone();
        return;
      }

      dropZoneText.textContent = file.name;
      dropZoneSubtext.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
      dropZone.classList.add('border-emerald-500', 'bg-emerald-50/40');
      enableSubmit();
    }

    function resetDropZone() {
      fileUpload.value = '';
      dropZoneText.textContent = defaultText;
      dropZoneSubtext.textContent = defaultSubtext;
      dropZone.classList.remove('border-emerald-500', 'bg-emerald-50/40');
      disableSubmit();
    }

    if (form) {
      form.addEventListener('submit', (e) => {
        if (!fileUpload.files || fileUpload.files.length === 0) {
          e.preventDefault();
          alert('Harap pilih berkas PDF terlebih dahulu.');
        }
      });
    }

    dropZone.addEventListener('click', () => {
      fileUpload.click();
    });

    browseBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      fileUpload.click();
    });

    fileUpload.addEventListener('change', handleFile);

    ['dragenter', 'dragover'].forEach((eventName) => {
      dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('border-secondary', 'bg-surface-container');
      });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
      dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('border-secondary', 'bg-surface-container');
      });
    });

    dropZone.addEventListener('drop', (e) => {
      if (e.dataTransfer && e.dataTransfer.files.length > 0) {
        fileUpload.files = e.dataTransfer.files;
        handleFile();
      }
    });
  });
</script>

</html>

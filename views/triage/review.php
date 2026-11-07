<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$document = $data['document'];
$canDispose = !empty($data['can_dispose']);
$currentStatus = $document['status'];

if ($currentStatus === 'PENDING_TRIAGE') {
    $statusLabel = 'Menunggu Disposisi';
    $statusClass = 'bg-[#fef3c7] text-[#b45309] border-[#fde68a]';
    $statusIcon = 'hourglass_top';
    $pageTitle = 'Pemeriksaan & Disposisi Dokumen';
    $pageSubtitle = 'Verifikasi berkas permohonan dan tentukan alur pihak penandatangan';
    $backUrl = '/Tandain/triage';
} elseif ($currentStatus === 'IN_PROGRESS') {
    $statusLabel = 'Sedang Diproses';
    $statusClass = 'bg-[#dbeafe] text-[#1d4ed8] border-[#bfdbfe]';
    $statusIcon = 'sync';
    $pageTitle = 'Rincian Dokumen';
    $pageSubtitle = 'Dokumen telah didisposisikan dan sedang menunggu tanda tangan';
    $backUrl = '/Tandain/triage?status=dispatched';
} elseif ($currentStatus === 'COMPLETED') {
    $statusLabel = 'Dokumen Selesai';
    $statusClass = 'bg-[#dcfce7] text-[#15803d] border-[#bbf7d0]';
    $statusIcon = 'check';
    $pageTitle = 'Rincian Dokumen';
    $pageSubtitle = 'Dokumen telah selesai ditandatangani dan tersimpan dalam arsip';
    $backUrl = '/Tandain/archive?status=COMPLETED';
} elseif ($currentStatus === 'REJECTED') {
    $statusLabel = 'Ditolak';
    $statusClass = 'bg-[#fee2e2] text-[#b91c1c] border-[#fecaca]';
    $statusIcon = 'close';
    $pageTitle = 'Rincian Dokumen';
    $pageSubtitle = 'Pengajuan dokumen ditolak pada meja disposisi Tata Usaha';
    $backUrl = '/Tandain/archive?status=REJECTED';
} elseif ($currentStatus === 'CANCELLED') {
    $statusLabel = 'Dibatalkan';
    $statusClass = 'bg-surface-container text-on-surface-variant border-outline-variant';
    $statusIcon = 'block';
    $pageTitle = 'Rincian Dokumen';
    $pageSubtitle = 'Dokumen tidak lagi berada dalam alur tanda tangan';
    $backUrl = '/Tandain/archive?status=CANCELLED';
} else {
    $statusLabel = 'Draf';
    $statusClass = 'bg-surface-container text-on-surface-variant border-outline-variant';
    $statusIcon = 'edit_note';
    $pageTitle = 'Rincian Dokumen';
    $pageSubtitle = 'Dokumen masih berstatus draf dan belum diajukan';
    $backUrl = '/Tandain/dashboard';
}

$filePath = $document['original_file_path'] ?? '';
$fileExists = !empty($filePath) && file_exists($filePath);
$fileName = !empty($filePath) ? basename($filePath) : 'Berkas tidak tersedia';
?>

  <main class="flex-1 p-8 mx-auto w-full max-w-full flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
      <div>
        <div class="flex flex-wrap items-center gap-3">
          <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight font-bold"><?= $pageTitle; ?></h1>
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-label-sm font-semibold border <?= $statusClass; ?>">
            <span class="material-symbols-outlined text-[14px]"><?= $statusIcon; ?></span>
            <span><?= $statusLabel; ?></span>
          </span>
        </div>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1"><?= $pageSubtitle; ?></p>
      </div>

      <a class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-outline-variant font-label-md text-label-md text-primary hover:bg-surface-container-low transition-colors shrink-0" href="<?= $backUrl; ?>">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        <span>Kembali</span>
      </a>
    </div>

    <?php if ($currentStatus === 'REJECTED' && !empty($document['rejection_reason'])): ?>
      <div class="flex items-start gap-3 rounded-xl border border-[#fecaca] bg-[#fef2f2] px-4 py-3 text-[#b91c1c] font-body-sm text-body-sm">
        <span class="material-symbols-outlined text-[20px]">block</span>
        <div>
          <p class="font-semibold">Alasan penolakan</p>
          <p class="mt-0.5"><?= htmlspecialchars($document['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!$canDispose): ?>
      <div class="flex items-start gap-3 rounded-xl border border-outline-variant bg-surface-container-low px-4 py-3 text-on-surface-variant font-body-sm text-body-sm">
        <span class="material-symbols-outlined text-[20px]">lock</span>
        <span>Halaman ini hanya menampilkan rincian. Disposisi tidak dapat diubah karena dokumen sudah keluar dari antrean.</span>
      </div>
    <?php endif; ?>

    <?php if ($canDispose): ?>
      <form class="flex flex-col gap-6" method="POST" action="/Tandain/triage/processDisposition" id="disposition-form" novalidate>
        <input type="hidden" name="document_id" value="<?= htmlspecialchars($document['id'], ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>

      <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 pb-3 border-b border-surface-container">
          <span class="material-symbols-outlined text-secondary text-[22px]">description</span>
          <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Berkas &amp; Informasi Dokumen</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <p class="block font-label-md text-label-md text-primary font-semibold mb-1">Judul Dokumen</p>
            <p class="font-body-md text-body-md text-on-surface font-medium"><?= htmlspecialchars($document['title'], ENT_QUOTES, 'UTF-8'); ?></p>
          </div>

          <div>
            <p class="block font-label-md text-label-md text-primary font-semibold mb-1">Pemohon</p>
            <p class="font-body-md text-body-md text-on-surface font-medium"><?= htmlspecialchars($document['requester_name'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="font-body-sm text-body-sm text-on-surface-variant">NIM/NIP: <?= htmlspecialchars($document['requester_nim'], ENT_QUOTES, 'UTF-8'); ?></p>
          </div>

          <div>
            <p class="block font-label-md text-label-md text-primary font-semibold mb-1">Tanggal Diajukan</p>
            <p class="font-body-md text-body-md text-on-surface font-medium"><?= date('d F Y, H:i', strtotime($document['created_at'])); ?> WIB</p>
          </div>
        </div>

        <?php if (!empty($document['triage_admin_name'])): ?>
          <div class="mt-4 pt-4 border-t border-surface-container">
            <p class="font-body-sm text-body-sm text-on-surface-variant">Didisposisikan oleh <span class="font-semibold text-primary"><?= htmlspecialchars($document['triage_admin_name'], ENT_QUOTES, 'UTF-8'); ?></span></p>
          </div>
        <?php endif; ?>

        <div class="mt-6 pt-5 border-t border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">
              <span class="material-symbols-outlined text-[24px]">picture_as_pdf</span>
            </div>

            <div class="min-w-0">
              <p class="font-label-md text-label-md font-semibold text-primary truncate"><?= htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'); ?></p>
              <p class="font-body-sm text-xs text-on-surface-variant">Berkas permohonan surat digital</p>
            </div>
          </div>

          <?php if ($fileExists): ?>
            <a href="/Tandain/<?= htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 border border-outline rounded-lg text-primary font-label-md text-label-md bg-white hover:bg-surface-container transition-all shrink-0">
              <span class="material-symbols-outlined text-[18px]">visibility</span>
              <span>Lihat Berkas</span>
            </a>
          <?php else: ?>
            <span class="inline-flex items-center gap-2 px-4 py-2 border border-outline-variant rounded-lg text-on-surface-variant font-label-md text-label-md bg-surface-container-low shrink-0">
              <span class="material-symbols-outlined text-[18px]">info</span>
              <span>Berkas tidak tersedia</span>
            </span>
          <?php endif; ?>
        </div>
      </section>

      <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-surface-container">
          <span class="material-symbols-outlined text-secondary text-[22px]">tag</span>
          <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Nomor &amp; Legalitas Surat Dinas</h2>
        </div>

        <?php if ($canDispose): ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="document_number">
                Nomor Naskah Kedinasan Resmi PNJ <span class="text-error">*</span>
              </label>
              <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition" id="document_number" name="document_number" required type="text" value="<?= htmlspecialchars($document['document_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Contoh: 042/PL3/TIK/PNJ/2026">
            </div>

            <div>
              <label class="block font-label-md text-label-md text-primary font-semibold mb-2" for="expires_at">
                Batas Kedaluwarsa Dokumen <span class="font-normal text-on-surface-variant">(Opsional)</span>
              </label>
              <input class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition cursor-pointer" id="expires_at" name="expires_at" type="date" min="<?= date('Y-m-d'); ?>" value="<?= htmlspecialchars(!empty($document['expires_at']) ? date('Y-m-d', strtotime($document['expires_at'])) : '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <p class="block font-label-md text-label-md text-primary font-semibold mb-1">Nomor Naskah Kedinasan</p>
              <p class="font-body-md text-body-md text-on-surface font-medium"><?= !empty($document['document_number']) ? htmlspecialchars($document['document_number'], ENT_QUOTES, 'UTF-8') : 'Belum ditetapkan'; ?></p>
            </div>

            <div>
              <p class="block font-label-md text-label-md text-primary font-semibold mb-1">Batas Kedaluwarsa</p>
              <p class="font-body-md text-body-md text-on-surface font-medium"><?= !empty($document['expires_at']) ? date('d F Y', strtotime($document['expires_at'])) : 'Tanpa batas kedaluwarsa'; ?></p>
            </div>
          </div>
        <?php endif; ?>
      </section>

      <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm" id="section-signers">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-surface-container mb-4 pb-4">
          <div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-secondary text-[22px]">route</span>
              <h2 class="font-headline-sm text-headline-sm text-primary font-semibold">Alur Pihak Penandatangan</h2>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Tentukan pihak berwenang dan urutan penandatanganan dokumen elektronik</p>
          </div>

          <?php if ($canDispose): ?>
            <div class="flex items-start md:items-center gap-3 shrink-0 pt-2 sm:pt-0 sm:border-l sm:border-surface-container sm:pl-6">
              <label class="relative inline-flex items-center cursor-pointer">
                <input <?= (!isset($document['is_sequential']) || $document['is_sequential']) ? 'checked' : ''; ?> class="sr-only peer" id="sequential-check" name="is_sequential" type="checkbox" value="1">
                <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#004B87]"></div>
              </label>

              <div>
                <span class="font-label-md text-label-md text-primary block leading-tight font-semibold">Penandatanganan mengikuti urutan</span>
                <span class="text-xs text-on-surface-variant font-normal block leading-relaxed">Berkas diteruskan bertahap dari penandatangan pertama ke berikutnya</span>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <div class="flex flex-col gap-4">

          <?php if ($canDispose): ?>
            <div>
              <button class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[#004B87] hover:bg-[#003d6d] text-white font-label-md text-label-md font-semibold shadow-sm transition" id="btn-show-add-signer" type="button">
                <span class="material-symbols-outlined text-[18px] mr-1">add</span>
                <span>Tambah Penandatangan</span>
              </button>
            </div>
          <?php endif; ?>

          <div class="flex flex-col gap-3" id="signers-list">

            <?php if (empty($data['current_signers'])): ?>
              <div class="rounded-xl border border-dashed border-outline bg-surface px-4 py-6 text-center text-on-surface-variant font-body-sm text-body-sm" id="signers-empty">
                Belum ada penandatangan yang ditetapkan.
              </div>
            <?php endif; ?>

            <?php $order = 1; ?>
            <?php foreach ($data['current_signers'] as $signer): ?>
              <div class="signer-row flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border border-outline-variant bg-surface-container-lowest gap-4">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                  <div class="signer-badge w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0"><?= $order++; ?></div>

                  <div class="flex-1 min-w-0">
                    <p class="font-label-lg text-label-lg font-bold text-primary truncate" data-field="name"><?= htmlspecialchars($signer['signer_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="font-body-sm text-body-sm text-on-surface-variant truncate mt-0.5">
                      <?= htmlspecialchars($signer['signer_email'], ENT_QUOTES, 'UTF-8'); ?> &bull; NIP/NIM <?= htmlspecialchars($signer['signer_nim_nip'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <p class="text-[11px] text-on-surface-variant mt-0.5" data-field="role"><?= htmlspecialchars($signer['signer_position'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                  </div>
                </div>

                <?php if ($canDispose): ?>
                  <input type="hidden" name="signer_ids[]" value="<?= htmlspecialchars($signer['signer_id'], ENT_QUOTES, 'UTF-8'); ?>">

                  <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap shrink-0">
                    <div class="flex items-center gap-1.5">
                      <label class="font-label-sm text-xs text-on-surface-variant font-medium">Bentuk TTE:</label>
                      <select name="stamp_types[]" class="px-2.5 py-1.5 text-xs rounded-lg border border-outline-variant bg-surface-container-lowest text-primary font-medium focus:outline-none focus:border-secondary">
                        <option value="DUAL_DISPLAY" <?= (($signer['stamp_type'] ?? 'DUAL_DISPLAY') === 'DUAL_DISPLAY') ? 'selected' : ''; ?>>Tanda Tangan &amp; QR</option>
                        <option value="SIGNATURE_ONLY" <?= (($signer['stamp_type'] ?? '') === 'SIGNATURE_ONLY') ? 'selected' : ''; ?>>Tanda Tangan Saja</option>
                        <option value="QR_ONLY" <?= (($signer['stamp_type'] ?? '') === 'QR_ONLY') ? 'selected' : ''; ?>>QR Code Saja</option>
                      </select>
                    </div>

                    <div class="flex items-center gap-1.5">
                      <label class="font-label-sm text-xs text-on-surface-variant font-medium">Hal:</label>
                      <input type="number" min="1" max="999" name="page_numbers[]" value="<?= htmlspecialchars((string)($signer['page_number'] ?? 1), ENT_QUOTES, 'UTF-8'); ?>" class="w-16 px-2 py-1.5 text-xs text-center rounded-lg border border-outline-variant bg-surface-container-lowest text-primary font-medium focus:outline-none focus:border-secondary">
                    </div>

                    <button class="p-1.5 rounded-lg text-error hover:bg-error-container/40 transition shrink-0" onclick="removeSignerRow(this)" title="Hapus Baris" type="button">
                      <span class="material-symbols-outlined text-[20px]">delete</span>
                    </button>
                  </div>
                <?php else: ?>
                  <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap shrink-0">
                    <span class="inline-flex items-center rounded-full bg-surface-container px-2.5 py-1 text-[11px] font-semibold text-on-surface-variant border border-outline-variant">
                      <?= ($signer['stamp_type'] ?? 'DUAL_DISPLAY') === 'SIGNATURE_ONLY' ? 'Tanda Tangan Saja' : ((($signer['stamp_type'] ?? '') === 'QR_ONLY') ? 'QR Code Saja' : 'Tanda Tangan & QR'); ?>
                    </span>

                    <span class="text-[11px] text-on-surface-variant">Hal. <?= htmlspecialchars((string)($signer['page_number'] ?? 1), ENT_QUOTES, 'UTF-8'); ?></span>

                    <?php if ($document['status'] === 'COMPLETED' || $signer['status'] === 'SIGNED'): ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]">
                        <span class="material-symbols-outlined text-[14px]">check</span>
                        <span>Sudah Ditandatangani</span>
                      </span>
                    <?php elseif ($document['status'] === 'REJECTED' || $document['status'] === 'CANCELLED'): ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-surface-container text-on-surface-variant border border-outline-variant">
                        <span class="material-symbols-outlined text-[14px]">block</span>
                        <span>Tidak Dilanjutkan</span>
                      </span>
                    <?php elseif ($signer['status'] === 'CURRENT_TURN'): ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#fef3c7] text-[#b45309] border border-[#fde68a]">
                        <span class="material-symbols-outlined text-[14px]">hourglass_top</span>
                        <span>Menunggu Tanda Tangan</span>
                      </span>
                    <?php elseif ($signer['status'] === 'REJECTED'): ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#fee2e2] text-[#b91c1c] border border-[#fecaca]">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                        <span>Ditolak</span>
                      </span>
                    <?php elseif ($signer['status'] === 'CANCELLED'): ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-surface-container text-on-surface-variant border border-outline-variant">
                        <span class="material-symbols-outlined text-[14px]">block</span>
                        <span>Dibatalkan</span>
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-surface-container text-on-surface-variant border border-outline-variant">
                        <span>Menunggu Giliran</span>
                      </span>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>

          </div>

          <?php if ($canDispose): ?>
            <div class="flex flex-col md:flex-row items-center justify-between p-4 rounded-xl border border-dashed border-outline bg-surface gap-4 hidden" id="signer-search-row">
              <div class="flex items-center gap-4 flex-1 w-full">
                <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0" id="next-signer-order">1</div>

                <div class="flex-1 flex gap-2">
                  <div class="relative flex-1">
                    <input class="w-full pl-9 pr-3 py-2 rounded-lg border border-outline-variant bg-white font-body-sm text-body-sm text-primary focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none" id="signer-search-input" placeholder="Cari nama, NIP/NIM, atau email resmi kampus" type="text" autocomplete="off">
                    <span class="material-symbols-outlined absolute left-2.5 top-2.5 text-[18px] text-on-surface-variant">person_search</span>
                  </div>

                  <button class="px-3.5 py-2 bg-[#004B87] hover:bg-[#003d6d] text-white rounded-lg font-label-md text-label-md flex items-center gap-1 transition shadow-sm shrink-0" id="signer-search-btn" type="button">
                    <span class="material-symbols-outlined text-[16px]">search</span>
                    <span>Cari</span>
                  </button>
                </div>
              </div>

              <button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition shrink-0 self-end md:self-center" id="signer-search-cancel" title="Batalkan" type="button">
                <span class="material-symbols-outlined text-[20px]">close</span>
              </button>
            </div>

            <div class="hidden flex-col gap-2 p-3 rounded-xl border border-outline-variant bg-surface-container-low max-h-72 overflow-y-auto" id="search-results-box"></div>

            <div class="hidden items-start gap-3 rounded-xl border border-[#fecaca] bg-[#fef2f2] px-4 py-3 text-[#b91c1c] font-body-sm text-body-sm" id="disposition-error" role="alert">
              <span class="material-symbols-outlined text-[20px]">error</span>
              <span id="disposition-error-text"></span>
            </div>
          <?php endif; ?>

        </div>

      </section>

    <?php if ($canDispose): ?>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-5 border-t border-outline-variant">
          <button class="border border-red-300 text-red-600 hover:bg-red-50 px-6 py-2.5 rounded-lg font-label-lg text-label-lg font-medium transition inline-flex items-center gap-2" onclick="openRejectModal()" type="button">
            <span class="material-symbols-outlined text-[18px]">block</span>
            <span>Tolak Pengajuan</span>
          </button>

          <div class="flex items-center gap-4">
            <a class="border border-outline-variant text-on-surface-variant hover:bg-surface-container hover:text-on-surface px-6 py-2.5 rounded-lg font-label-lg text-label-lg font-medium transition inline-block" href="/Tandain/triage">
              Batal
            </a>

            <button class="bg-[#004B87] hover:bg-[#003d6d] text-white px-8 py-2.5 rounded-lg font-label-lg text-label-lg font-semibold shadow-sm flex items-center gap-2 transition active:scale-[0.99]" type="submit">
              <span>Konfirmasi Disposisi &amp; Teruskan</span>
              <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </button>
          </div>
        </div>

      </form>

      <template id="signer-row-template">
        <div class="signer-row flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border border-outline-variant bg-surface-container-lowest gap-4">
          <div class="flex items-center gap-4 flex-1 min-w-0">
            <div class="signer-badge w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0"></div>

            <div class="flex-1 min-w-0">
              <p class="font-label-lg text-label-lg font-bold text-primary truncate" data-field="name"></p>
              <p class="font-body-sm text-body-sm text-on-surface-variant truncate mt-0.5">
                <span data-field="email"></span> &bull; NIP/NIM <span data-field="nip"></span>
              </p>
              <p class="text-[11px] text-on-surface-variant mt-0.5" data-field="role"></p>
            </div>
          </div>

          <input type="hidden" name="signer_ids[]" value="">

          <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap shrink-0">
            <div class="flex items-center gap-1.5">
              <label class="font-label-sm text-xs text-on-surface-variant font-medium">Bentuk TTE:</label>
              <select name="stamp_types[]" class="px-2.5 py-1.5 text-xs rounded-lg border border-outline-variant bg-surface-container-lowest text-primary font-medium focus:outline-none focus:border-secondary">
                <option value="DUAL_DISPLAY" selected>Tanda Tangan &amp; QR</option>
                <option value="SIGNATURE_ONLY">Tanda Tangan Saja</option>
                <option value="QR_ONLY">QR Code Saja</option>
              </select>
            </div>

            <div class="flex items-center gap-1.5">
              <label class="font-label-sm text-xs text-on-surface-variant font-medium">Hal:</label>
              <input type="number" min="1" max="999" name="page_numbers[]" value="1" class="w-16 px-2 py-1.5 text-xs text-center rounded-lg border border-outline-variant bg-surface-container-lowest text-primary font-medium focus:outline-none focus:border-secondary">
            </div>

            <button class="p-1.5 rounded-lg text-error hover:bg-error-container/40 transition shrink-0" onclick="removeSignerRow(this)" title="Hapus Baris" type="button">
              <span class="material-symbols-outlined text-[20px]">delete</span>
            </button>
          </div>
        </div>
      </template>

      <div class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4 hidden" id="reject-modal">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl max-w-lg w-full p-6 shadow-xl flex flex-col gap-4">
          <div class="flex items-center justify-between border-b border-surface-container pb-3">
            <div class="flex items-center gap-2 text-red-600">
              <span class="material-symbols-outlined text-[22px]">block</span>
              <h3 class="font-headline-sm text-headline-sm font-semibold">Tolak Pengajuan Dokumen</h3>
            </div>

            <button class="text-on-surface-variant hover:text-primary" onclick="closeRejectModal()" type="button">
              <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
          </div>

          <form action="/Tandain/triage/reject" class="flex flex-col gap-4" method="POST">
            <input name="document_id" type="hidden" value="<?= htmlspecialchars($document['id'], ENT_QUOTES, 'UTF-8'); ?>">

            <div>
              <label class="block font-label-md text-label-md text-primary font-semibold mb-1" for="modal_rejection_reason">
                Alasan Penolakan <span class="text-error">*</span>
              </label>
              <textarea class="w-full px-3.5 py-2.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-primary font-body-sm text-body-sm focus:border-red-500 outline-none transition" id="modal_rejection_reason" name="rejection_reason" placeholder="Tuliskan alasan formal penolakan berkas mahasiswa..." required rows="3"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
              <button class="px-4 py-2 border border-outline-variant text-on-surface-variant hover:bg-surface-container rounded-lg font-label-md text-label-md font-medium transition" onclick="closeRejectModal()" type="button">
                Batal
              </button>

              <button class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-label-md text-label-md font-semibold transition" type="submit">
                Tolak Berkas
              </button>
            </div>
          </form>
        </div>
      </div>

    <?php endif; ?>

  </main>

<?php if ($canDispose): ?>
  <script>
    const signersList = document.getElementById('signers-list');
    const signerTemplate = document.getElementById('signer-row-template');
    const btnShowAddSigner = document.getElementById('btn-show-add-signer');
    const signerSearchRow = document.getElementById('signer-search-row');
    const signerSearchInput = document.getElementById('signer-search-input');
    const signerSearchBtn = document.getElementById('signer-search-btn');
    const signerSearchCancel = document.getElementById('signer-search-cancel');
    const searchResultsBox = document.getElementById('search-results-box');
    const dispositionForm = document.getElementById('disposition-form');
    const dispositionError = document.getElementById('disposition-error');
    const dispositionErrorText = document.getElementById('disposition-error-text');
    const nextSignerOrder = document.getElementById('next-signer-order');

    let searchTimer = null;

    function getSignerRows() {
      return signersList.querySelectorAll('.signer-row');
    }

    function updateSignerBadges() {
      const rows = getSignerRows();
      const emptyState = document.getElementById('signers-empty');

      rows.forEach((row, index) => {
        const badge = row.querySelector('.signer-badge');

        if (badge) {
          badge.textContent = index + 1;
        }
      });

      if (emptyState) {
        emptyState.classList.toggle('hidden', rows.length > 0);
      }

      if (nextSignerOrder) {
        nextSignerOrder.textContent = rows.length + 1;
      }
    }

    function removeSignerRow(button) {
      const row = button.closest('.signer-row');

      if (row) {
        row.remove();
        updateSignerBadges();
      }
    }

    function resetSearch() {
      signerSearchInput.value = '';
      searchResultsBox.innerHTML = '';
      searchResultsBox.classList.add('hidden');
      searchResultsBox.classList.remove('flex');
      signerSearchRow.classList.add('hidden');
    }

    function showDispositionError(message) {
      dispositionErrorText.textContent = message;
      dispositionError.classList.remove('hidden');
      dispositionError.classList.add('flex');
      dispositionError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function hideDispositionError() {
      dispositionError.classList.add('hidden');
      dispositionError.classList.remove('flex');
      dispositionErrorText.textContent = '';
    }

    function addSigner(user) {
      const row = signerTemplate.content.firstElementChild.cloneNode(true);

      row.querySelector('[data-field="name"]').textContent = user.full_name;
      row.querySelector('[data-field="email"]').textContent = user.email;
      row.querySelector('[data-field="nip"]').textContent = user.identity_number;

      const roleText = user.position ? user.role_display_name + ' • ' + user.position : user.role_display_name;
      row.querySelector('[data-field="role"]').textContent = roleText;
      row.querySelector('input[name="signer_ids[]"]').value = user.id;

      signersList.appendChild(row);
      updateSignerBadges();
      hideDispositionError();
      resetSearch();
    }

    function createSearchResult(user) {
      const item = document.createElement('div');
      item.className = 'flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-lg bg-surface-container-lowest border border-outline-variant gap-3';

      const info = document.createElement('div');
      info.className = 'flex-1 min-w-0';

      const name = document.createElement('p');
      name.className = 'font-label-md text-label-md font-bold text-primary truncate';
      name.textContent = user.full_name;

      const detail = document.createElement('p');
      detail.className = 'font-body-sm text-body-sm text-on-surface-variant truncate';
      detail.textContent = user.email + ' • NIP/NIM ' + user.identity_number;

      const role = document.createElement('p');
      role.className = 'text-[11px] text-on-surface-variant mt-0.5';
      role.textContent = user.position ? user.role_display_name + ' • ' + user.position : user.role_display_name;

      info.appendChild(name);
      info.appendChild(detail);
      info.appendChild(role);

      const actions = document.createElement('div');
      actions.className = 'flex items-center gap-2 shrink-0';

      const cert = document.createElement('span');

      if (Number(user.has_active_cert) === 1) {
        cert.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#dcfce7] text-[#15803d] border border-[#bbf7d0]';
        cert.textContent = 'Sertifikat Aktif';
      } else {
        cert.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container text-on-surface-variant border border-outline-variant';
        cert.textContent = 'Belum Bersertifikat';
      }

      actions.appendChild(cert);

      if (Number(user.has_active_cert) === 1) {
        const addButton = document.createElement('button');
        addButton.type = 'button';
        addButton.className = 'px-3 py-1.5 bg-[#004B87] hover:bg-[#003d6d] text-white rounded-lg font-label-md text-label-md font-semibold transition';
        addButton.textContent = 'Tambahkan';
        addButton.addEventListener('click', function () {
          addSigner(user);
        });
        actions.appendChild(addButton);
      }

      item.appendChild(info);
      item.appendChild(actions);

      return item;
    }

    async function runSignerSearch(query) {
      searchResultsBox.innerHTML = '';

      const loading = document.createElement('div');
      loading.className = 'p-3 text-center text-on-surface-variant font-body-sm text-body-sm';
      loading.textContent = 'Mencari civitas...';
      searchResultsBox.appendChild(loading);
      searchResultsBox.classList.remove('hidden');
      searchResultsBox.classList.add('flex');

      try {
        const response = await fetch('/Tandain/triage/searchSigners?q=' + encodeURIComponent(query), {
          headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) {
          throw new Error('Pencarian gagal.');
        }

        const users = await response.json();
        const existingIds = Array.from(document.querySelectorAll('input[name="signer_ids[]"]')).map(input => String(input.value));
        const availableUsers = users.filter(user => !existingIds.includes(String(user.id)));

        searchResultsBox.innerHTML = '';

        if (availableUsers.length === 0) {
          const empty = document.createElement('div');
          empty.className = 'p-3 text-center text-on-surface-variant font-body-sm text-body-sm';
          empty.textContent = 'Tidak ada civitas aktif yang cocok dengan kata kunci tersebut.';
          searchResultsBox.appendChild(empty);
        } else {
          availableUsers.forEach(user => {
            searchResultsBox.appendChild(createSearchResult(user));
          });
        }
      } catch (error) {
        searchResultsBox.innerHTML = '';

        const failed = document.createElement('div');
        failed.className = 'p-3 text-center text-[#b91c1c] font-body-sm text-body-sm';
        failed.textContent = 'Pencarian gagal. Periksa koneksi lalu coba lagi.';
        searchResultsBox.appendChild(failed);
      }
    }

    if (btnShowAddSigner) {
      btnShowAddSigner.addEventListener('click', function () {
        signerSearchRow.classList.remove('hidden');
        signerSearchInput.focus();
        signerSearchRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      });
    }

    if (signerSearchInput) {
      signerSearchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);

        const query = signerSearchInput.value.trim();

        if (query.length < 2) {
          searchResultsBox.innerHTML = '';
          searchResultsBox.classList.add('hidden');
          searchResultsBox.classList.remove('flex');
          return;
        }

        searchTimer = setTimeout(function () {
          runSignerSearch(query);
        }, 300);
      });

      signerSearchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
          event.preventDefault();
          clearTimeout(searchTimer);

          const query = signerSearchInput.value.trim();

          if (query.length >= 2) {
            runSignerSearch(query);
          }
        }
      });
    }

    if (signerSearchBtn) {
      signerSearchBtn.addEventListener('click', function () {
        clearTimeout(searchTimer);

        const query = signerSearchInput.value.trim();

        if (query.length >= 2) {
          runSignerSearch(query);
        }
      });
    }

    if (signerSearchCancel) {
      signerSearchCancel.addEventListener('click', resetSearch);
    }

    if (dispositionForm) {
      dispositionForm.addEventListener('submit', function (event) {
        hideDispositionError();

        const documentNumber = document.getElementById('document_number').value.trim();
        const signerCount = getSignerRows().length;
        const expiresAt = document.getElementById('expires_at').value;
        const today = '<?= date('Y-m-d'); ?>';

        if (documentNumber === '') {
          event.preventDefault();
          showDispositionError('Nomor naskah kedinasan wajib diisi.');
          document.getElementById('document_number').focus();
        } else if (signerCount === 0) {
          event.preventDefault();
          showDispositionError('Tetapkan minimal satu penandatangan sebelum menyimpan disposisi.');
          document.getElementById('btn-show-add-signer').focus();
        } else if (expiresAt !== '' && expiresAt < today) {
          event.preventDefault();
          showDispositionError('Batas kedaluwarsa dokumen tidak boleh berada di masa lalu.');
          document.getElementById('expires_at').focus();
        }
      });
    }

    function openRejectModal() {
      const modal = document.getElementById('reject-modal');

      if (modal) {
        modal.classList.remove('hidden');
      }
    }

    function closeRejectModal() {
      const modal = document.getElementById('reject-modal');

      if (modal) {
        modal.classList.add('hidden');
      }
    }

    updateSignerBadges();
  </script>
<?php endif; ?>

<?php require_once 'views/layouts/footer.php'; ?>

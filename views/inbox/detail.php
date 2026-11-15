<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>
<?php require_once 'views/layouts/flash.php'; ?>

<?php
$document = $data['document'];

$documentDate = strtotime($document['submitted_at'] ?? date('Y-m-d H:i:s'));
$documentFile = $document['original_file_path'] ?? '';
$documentStatus = [
    'IN_PROGRESS' => 'Dalam Proses Tanda Tangan',
    'COMPLETED' => 'Selesai',
    'PENDING_TRIAGE' => 'Menunggu Disposisi',
    'REJECTED' => 'Ditolak',
    'CANCELLED' => 'Dibatalkan',
][$document['status']] ?? 'Status Tidak Diketahui';
$documentStatusClass = $document['status'] === 'COMPLETED'
    ? 'border-[#bbf7d0] bg-[#f0fdf4] text-[#15803d]'
    : 'border-[#fde68a] bg-[#fffbeb] text-[#b45309]';
?>

<main class="flex-1 w-full p-8">
  <div class="mx-auto w-full">
    <div class="mb-6">
      <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight font-bold">Persetujuan Dokumen</h1>
      <p class="mt-1 font-body-md text-body-md text-on-surface-variant">Tinjau isi berkas, atur posisi tanda tangan, dan setujui dokumen untuk diproses lebih lanjut.</p>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.7fr)_minmax(280px,0.8fr)]">
      <div class="space-y-6">
        <section class="overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest shadow-sm">
          <div class="flex flex-col gap-4 border-b border-outline-variant/70 p-5 md:flex-row md:items-center md:justify-between">
            <div>
              <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-[18px] text-primary" aria-hidden="true">description</span>
                  <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Tinjau Dokumen</h2>
              </div>
              <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Pastikan anda telah membaca dokumen secara menyeluruh.</p>
            </div>

            
          </div>

          <?php if ($documentFile !== ''): ?>
            <div class="h-[1080px] overflow-auto bg-surface-container-low p-4">
              <p id="pdf-loading" class="py-8 text-center font-body-md text-body-md text-on-surface-variant" role="status">Memuat dokumen...</p>
              <div id="pdf-error" class="hidden py-8 text-center font-body-md text-body-md text-error" role="alert"></div>
              <div id="pdf-container" class="space-y-4 overflow-x-auto pb-2" aria-label="Pratinjau dokumen dengan posisi tanda tangan"></div>
            </div>
          <?php else: ?>
            <p class="p-6 text-center font-body-md text-body-md text-on-surface-variant">Pratinjau dokumen tidak tersedia.</p>
          <?php endif; ?>

        </section>

        <div class="grid gap-5 md:grid-cols-2">
          <article class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4 shadow-sm">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[18px] text-[#b91c1c]" aria-hidden="true">edit_note</span>
              <h3 class="font-headline-sm text-headline-sm font-semibold text-primary">Penolakan &amp; Catatan Revisi</h3>
            </div>
            <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">Jika ada revisi, tuliskan alasan atau catatan untuk pengajuan kembali.</p>

            <label class="mt-4 block">
              <span class="sr-only">Penolakan &amp; Catatan Revisi</span>
              <textarea class="h-28 w-full rounded-lg border border-outline-variant bg-surface-container-low px-3 py-2.5 font-body-md text-body-md text-primary placeholder:text-on-surface-variant/70 focus:border-secondary focus:outline-none focus:ring-1 focus:ring-secondary" placeholder="Berikan catatan atau alasan revisi..."></textarea>
            </label>

            <button type="button" class="mt-4 inline-flex items-center gap-2 rounded-md border border-[#f3b4b4] bg-[#fff0f0] px-3 py-2 font-label-md text-label-md font-semibold text-[#b91c1c] transition-colors hover:bg-[#fde7e7]">
              <span class="material-symbols-outlined text-[16px]" aria-hidden="true">block</span>
              Tolak &amp; Kirim Catatan Revisi
            </button>
          </article>

          <article class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4 shadow-sm">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[18px] text-[#0d8d60]" aria-hidden="true">check_circle</span>
              <h3 class="font-headline-sm text-headline-sm font-semibold text-primary">Persetujuan &amp; Pembubuhan TTE</h3>
            </div>
            <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">Masukkan passphrase sertifikat Anda untuk menyetujui dan membubuhkan tanda tangan digital.</p>

            <?php if (($document['signer_status'] ?? '') === 'CURRENT_TURN' && ($document['status'] ?? '') === 'IN_PROGRESS'): ?>
              <form method="POST" action="/Tandain/inbox/agree">
                <input type="hidden" name="document_id" value="<?= htmlspecialchars($document['id'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['stamp_csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <label class="mt-4 block">
                  <span class="mb-1.5 block font-label-md text-label-md text-primary">Passphrase Sertifikat</span>
                  <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant" aria-hidden="true">lock</span>
                    <input
                      type="password"
                      name="passphrase"
                      required
                      autocomplete="current-password"
                      placeholder="Masukkan passphrase sertifikat"
                      class="h-11 w-full rounded-lg border border-outline-variant bg-surface-container-low pl-10 pr-3 font-body-md text-body-md text-primary focus:border-secondary focus:outline-none focus:ring-1 focus:ring-secondary"
                      aria-label="Passphrase sertifikat"
                    >
                  </div>
                </label>

                <button type="submit" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-md bg-[#0f8f5a] px-4 py-3 font-label-md text-label-md font-semibold text-white shadow-sm transition-colors hover:bg-[#0e7d4f]">
                  <span class="material-symbols-outlined text-[16px]" aria-hidden="true">check</span>
                  <span>Setujui &amp; Bubuhkan Tanda Tangan</span>
                </button>
              </form>
            <?php else: ?>
              <p class="mt-4 rounded-lg bg-surface-container-low px-3 py-2.5 font-body-sm text-body-sm text-on-surface-variant">Tindakan persetujuan hanya tersedia saat dokumen menunggu giliran tanda tangan Anda.</p>
            <?php endif; ?>
          </article>
        </div>
      </div>

      <aside class="space-y-6">
        <section class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm">
          <div class="flex items-start justify-between gap-3">
            <div>
              <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-on-surface-variant">Pemohon</p>
              <h3 class="mt-2 font-headline-sm text-headline-sm font-semibold text-primary"><?= htmlspecialchars($document['requester_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
            </div>
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary-container text-on-primary">
              <span class="material-symbols-outlined text-[20px]" aria-hidden="true">account_circle</span>
            </span>
          </div>

          <div class="mt-5 space-y-3 border-t border-outline-variant/70 pt-4 text-sm">
            <div>
              <p class="font-label-md text-label-md text-on-surface-variant">Judul Dokumen</p>
              <p class="mt-1 font-body-md text-body-md font-medium text-primary"><?= htmlspecialchars($document['title'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <div>
              <p class="font-label-md text-label-md text-on-surface-variant">Tanggal Diajukan</p>
              <p class="mt-1 font-body-md text-body-md text-on-surface"><?= date('d M Y', $documentDate); ?> • <?= date('H:i', $documentDate); ?> WIB</p>
            </div>

            <div>
              <p class="font-label-md text-label-md text-on-surface-variant">Status</p>
              <span class="mt-1 inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[12px] font-semibold <?= $documentStatusClass; ?>">
                <span class="material-symbols-outlined text-[12px]" aria-hidden="true"><?= $document['status'] === 'COMPLETED' ? 'check_circle' : 'hourglass_top'; ?></span>
                <?= htmlspecialchars($documentStatus, ENT_QUOTES, 'UTF-8'); ?>
              </span>
            </div>
          </div>
        </section>

        <section class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm">
          <h3 class="font-headline-sm text-headline-sm font-semibold text-primary">Tanda Tangan</h3>
          <div class="mt-4 space-y-3">
            <?php foreach ($data['signers'] as $index => $signer): ?>
              <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-outline-variant bg-surface-container-low px-3 py-2.5">
                <div class="min-w-0">
                  <p class="truncate font-body-md text-body-md font-medium text-primary"><?= htmlspecialchars($signer['signer_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <p class="font-body-sm text-body-sm text-on-surface-variant">Halaman <?= (int) $signer['page_number']; ?></p>
                </div>
                <?php if ($signer['signer_id'] === $data['user']['id']): ?>
                  <span class="inline-flex items-center gap-1 rounded-full bg-[#fef3c7] px-2.5 py-1 text-[12px] font-semibold text-[#78350f]">
                    <span class="material-symbols-outlined text-[12px]" aria-hidden="true">person</span>
                    Anda
                  </span>
                <?php endif; ?>
                <span class="material-symbols-outlined shrink-0 text-[20px] text-secondary" aria-hidden="true">draw</span>
                <?php if ($signer['signer_id'] === $data['user']['id'] && $signer['status'] === 'CURRENT_TURN'): ?>
                  <form method="POST" action="/Tandain/inbox/updateStampType" class="flex w-full flex-wrap gap-2 border-t border-outline-variant/70 pt-3">
                    <input type="hidden" name="document_id" value="<?= htmlspecialchars($document['id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['stamp_csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <button
                      type="submit"
                      name="stamp_type"
                      value="SIGNATURE_ONLY"
                      aria-pressed="<?= $signer['stamp_type'] === 'SIGNATURE_ONLY' ? 'true' : 'false'; ?>"
                      class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-md border px-3 py-2 font-label-md text-label-md font-semibold transition-colors <?= $signer['stamp_type'] === 'SIGNATURE_ONLY' ? 'border-secondary bg-secondary text-white' : 'border-outline-variant bg-white text-primary hover:bg-surface-container-high'; ?>"
                    >
                      <span class="material-symbols-outlined text-[16px]" aria-hidden="true">draw</span>
                      TTD Polos
                    </button>
                    <button
                      type="submit"
                      name="stamp_type"
                      value="DUAL_DISPLAY"
                      aria-pressed="<?= $signer['stamp_type'] === 'DUAL_DISPLAY' ? 'true' : 'false'; ?>"
                      class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-md border px-3 py-2 font-label-md text-label-md font-semibold transition-colors <?= $signer['stamp_type'] === 'DUAL_DISPLAY' ? 'border-secondary bg-secondary text-white' : 'border-outline-variant bg-white text-primary hover:bg-surface-container-high'; ?>"
                    >
                      <span class="material-symbols-outlined text-[16px]" aria-hidden="true">qr_code_2</span>
                      TTD + QR
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      </aside>
    </div>
  </div>
</main>

<?php if ($documentFile !== ''): ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    const pdfUrl = <?= json_encode('/Tandain/' . ltrim($documentFile, '/'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const signerPositions = <?= json_encode(array_map(static function ($signer) {
        $signaturePath = $signer['signer_signature_image_path'] ?? '';
        $signatureUrl = '';
        if (
            is_string($signaturePath)
            && preg_match('/\Auploads\/signatures\/[A-Za-z0-9._-]+\.png\z/D', $signaturePath)
        ) {
            $signatureFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $signaturePath);
            if (is_file($signatureFile)) {
                $signatureUrl = '/Tandain/' . $signaturePath . '?v=' . filemtime($signatureFile);
            }
        }

        return [
            'name' => $signer['signer_name'],
            'page' => (int) $signer['page_number'],
            'x' => (float) $signer['coord_x'],
            'y' => (float) $signer['coord_y'],
            'order' => (int) $signer['signing_order'],
            'stampType' => $signer['stamp_type'],
            'signatureUrl' => $signatureUrl,
        ];
    }, $data['signers']), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const qrUrl = <?= json_encode('/Tandain/certificate/qr/' . rawurlencode($document['id']), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    const pdfContainer = document.getElementById('pdf-container');
    const pdfLoading = document.getElementById('pdf-loading');
    const pdfError = document.getElementById('pdf-error');

    pdfjsLib.getDocument(pdfUrl).promise
      .then(async (pdf) => {
        for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
          const page = await pdf.getPage(pageNumber);
          const baseViewport = page.getViewport({ scale: 1 });
          const scale = Math.min(1.3, pdfContainer.clientWidth / baseViewport.width);
          const viewport = page.getViewport({ scale: Math.max(scale, 0.1) });
          const pageWrapper = document.createElement('div');
          pageWrapper.className = 'pdf-page relative mx-auto overflow-hidden border border-outline-variant bg-white shadow-sm';
          pageWrapper.style.width = `${viewport.width}px`;
          pageWrapper.style.height = `${viewport.height}px`;
          pageWrapper.dataset.page = String(pageNumber);

          const canvas = document.createElement('canvas');
          canvas.width = Math.ceil(viewport.width);
          canvas.height = Math.ceil(viewport.height);
          canvas.style.width = `${viewport.width}px`;
          canvas.style.height = `${viewport.height}px`;
          pageWrapper.appendChild(canvas);
          pdfContainer.appendChild(pageWrapper);

          await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;

          signerPositions
            .filter((signer) => signer.page === pageNumber)
            .forEach((signer) => {
              const marker = document.createElement('div');
              const qrOnly = signer.stampType === 'QR_ONLY';
              const signatureOnly = signer.stampType === 'SIGNATURE_ONLY';
              const markerWidth = qrOnly ? 60 : 160;
              const markerHeight = 60;
              marker.className = 'absolute z-10 flex items-center justify-center gap-0.5 overflow-hidden bg-white';
              marker.style.width = `${markerWidth * viewport.scale / 1.3}px`;
              marker.style.height = `${60 * viewport.scale / 1.3}px`;
              marker.style.left = `${Math.max(0, Math.min(1, signer.x)) * viewport.width}px`;
              marker.style.top = `${Math.max(0, Math.min(1, signer.y)) * viewport.height}px`;
              marker.title = `${signer.name} — halaman ${pageNumber}`;

              if (!signatureOnly) {
                const qrImage = document.createElement('img');
                qrImage.src = qrUrl;
                qrImage.alt = `Kode QR dokumen untuk ${signer.name}`;
                qrImage.className = 'h-full shrink-0 object-contain';
                qrImage.style.width = `${markerHeight * viewport.scale / 1.3}px`;
                marker.appendChild(qrImage);
              }

              if (!qrOnly && signer.signatureUrl) {
                const signatureImage = document.createElement('img');
                signatureImage.src = signer.signatureUrl;
                signatureImage.alt = `Tanda tangan ${signer.name}`;
                signatureImage.className = 'h-full min-w-0 flex-1 object-contain';
                marker.appendChild(signatureImage);
              } else if (!qrOnly && !signatureOnly) {
                marker.classList.add('border-2', 'border-dashed', 'border-amber-500', 'px-1', 'text-center', 'text-xs', 'font-semibold', 'text-amber-900');
                marker.textContent = `${signer.name}`;
              }

              pageWrapper.appendChild(marker);
            });
        }

        pdfLoading.classList.add('hidden');
      })
      .catch((error) => {
        console.error('Gagal memuat pratinjau PDF:', error);
        pdfLoading.classList.add('hidden');
        pdfError.textContent = 'Gagal memuat dokumen. Pastikan berkas PDF tersedia dan valid.';
        pdfError.classList.remove('hidden');
      });
  </script>
<?php endif; ?>

<?php require_once 'views/layouts/footer.php'; ?>

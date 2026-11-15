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
    ? 'bg-[#f0fdf4] text-[#15803d]'
    : 'bg-[#bfdbfe] text-[#1d4ed8]';
$canDecide = ($document['signer_status'] ?? '') === 'CURRENT_TURN'
    && ($document['status'] ?? '') === 'IN_PROGRESS';
$currentSigner = null;
foreach ($data['signers'] as $signer) {
    if ($signer['signer_id'] === $data['user']['id']) {
        $currentSigner = $signer;
        break;
    }
}
$hasActiveCertificate = $currentSigner !== null && (int) ($currentSigner['has_active_cert'] ?? 0) === 1;
$hasActiveSignature = $currentSigner !== null && !empty($currentSigner['has_active_signature']);
$canSign = $canDecide && $hasActiveCertificate && $hasActiveSignature;
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


                <?php if ($canDecide): ?>
                    <dialog id="agree-document-modal" aria-labelledby="agree-modal-title" style="position: fixed; top: 50%; left: 50%; margin: 0; transform: translate(-50%, -50%);" class="max-h-[calc(100%-2rem)] w-[min(100%-2rem,32rem)] overflow-y-auto rounded-2xl border border-outline-variant bg-surface-container-lowest p-0 text-on-surface shadow-2xl backdrop:bg-black/50">
                        <form method="POST" action="/Tandain/inbox/agree" class="p-6">
                            <input type="hidden" name="document_id" value="<?= htmlspecialchars($document['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['stamp_csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined mt-0.5 text-[24px] text-[#0d8d60]" aria-hidden="true">verified</span>
                                <div>
                                    <h2 id="agree-modal-title" class="font-headline-sm text-headline-sm font-semibold text-primary">Setujui Dokumen</h2>
                                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Tanda tangan digital akan dibubuhkan setelah passphrase sertifikat terverifikasi.</p>
                                </div>
                            </div>
                            <label class="mt-5 block">
                                <span class="mb-1.5 block font-label-md text-label-md text-primary">Passphrase Sertifikat</span>
                                <input
                                    type="password"
                                    name="passphrase"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Masukkan passphrase sertifikat"
                                    class="h-11 w-full rounded-lg border border-outline-variant bg-surface-container-low px-3 font-body-md text-body-md text-primary focus:border-secondary focus:outline-none focus:ring-1 focus:ring-secondary"
                                    aria-label="Passphrase sertifikat">
                            </label>
                            <div class="mt-6 flex justify-end gap-2">
                                <button type="button" data-modal-close class="rounded-md border border-outline-variant px-4 py-2.5 font-label-md text-label-md font-semibold text-primary hover:bg-surface-container-low">Batal</button>
                                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[#0f8f5a] px-4 py-2.5 font-label-md text-label-md font-semibold text-white hover:bg-[#0e7d4f]">
                                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">check</span>
                                    Setujui &amp; Tanda Tangani
                                </button>
                            </div>
                        </form>
                    </dialog>

                    <dialog id="reject-document-modal" aria-labelledby="reject-modal-title" style="position: fixed; top: 50%; left: 50%; margin: 0; transform: translate(-50%, -50%);" class="max-h-[calc(100%-2rem)] w-[min(100%-2rem,32rem)] overflow-y-auto rounded-2xl border border-outline-variant bg-surface-container-lowest p-0 text-on-surface shadow-2xl backdrop:bg-black/50">
                        <form method="POST" action="/Tandain/inbox/reject" class="p-6">
                            <input type="hidden" name="document_id" value="<?= htmlspecialchars($document['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['stamp_csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined mt-0.5 text-[24px] text-[#b91c1c]" aria-hidden="true">edit_note</span>
                                <div>
                                    <h2 id="reject-modal-title" class="font-headline-sm text-headline-sm font-semibold text-primary">Tolak Dokumen</h2>
                                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Dokumen akan ditolak dan giliran penandatangan lainnya dibatalkan. Sertakan alasan penolakan.</p>
                                </div>
                            </div>
                            <label class="mt-5 block">
                                <span class="mb-1.5 block font-label-md text-label-md text-primary">Alasan Penolakan</span>
                                <textarea
                                    name="rejection_reason"
                                    required
                                    minlength="3"
                                    maxlength="1000"
                                    rows="4"
                                    class="w-full rounded-lg border border-outline-variant bg-surface-container-low px-3 py-2.5 font-body-md text-body-md text-primary placeholder:text-on-surface-variant/70 focus:border-secondary focus:outline-none focus:ring-1 focus:ring-secondary"
                                    placeholder="Jelaskan alasan dokumen ditolak..."></textarea>
                            </label>
                            <div class="mt-6 flex justify-end gap-2">
                                <button type="button" data-modal-close class="rounded-md border border-outline-variant px-4 py-2.5 font-label-md text-label-md font-semibold text-primary hover:bg-surface-container-low">Batal</button>
                                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[#b91c1c] px-4 py-2.5 font-label-md text-label-md font-semibold text-white hover:bg-[#991b1b]">
                                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">block</span>
                                    Tolak Dokumen
                                </button>
                            </div>
                        </form>
                    </dialog>
                    <script>
                        document.addEventListener('click', (event) => {
                            if (!(event.target instanceof Element)) {
                                return;
                            }

                            const trigger = event.target.closest('[data-modal-open]');
                            if (!trigger) {
                                return;
                            }

                            const modal = document.getElementById(trigger.dataset.modalOpen);
                            if (modal instanceof HTMLDialogElement) {
                                modal.showModal();
                            }
                        });

                        document.querySelectorAll('dialog').forEach((modal) => {
                            modal.addEventListener('click', (event) => {
                                if (event.target === modal) {
                                    modal.close();
                                }
                            });
                            modal.querySelectorAll('[data-modal-close]').forEach((button) => {
                                button.addEventListener('click', () => modal.close());
                            });
                        });
                    </script>
                <?php endif; ?>
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
                            <span class="mt-1 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-label-sm font-label-sm <?= $documentStatusClass; ?>">
                                <span class="material-symbols-outlined text-[14px]" aria-hidden="true"><?= $document['status'] === 'COMPLETED' ? 'check_circle' : 'sync'; ?></span>
                                <span><?= htmlspecialchars($documentStatus, ENT_QUOTES, 'UTF-8'); ?></span>
                            </span>
                        </div>
                    </div>
                    <div class="flex justify-between mt-6 gap-2">
                        <?php if (
                            !empty($document['original_file_path'])
                            && ($document['requester_id'] ?? null) === $data['user']['id']
                        ): ?>
                            <a
                                href="/Tandain/<?= htmlspecialchars(ltrim($document['original_file_path'], '/'), ENT_QUOTES, 'UTF-8'); ?>"
                                download
                                class="inline-flex items-center justify-center gap-2 rounded-md border border-outline-variant bg-white px-4 py-2.5 font-label-md text-label-md font-semibold text-primary transition-colors hover:bg-surface-container-low">
                                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">download</span>
                                Original
                            </a>
                        <?php endif; ?>
                        <?php if (($document['status'] ?? '') === 'COMPLETED' && !empty($document['final_file_path'])): ?>
                            <a
                                href="/Tandain/<?= htmlspecialchars(ltrim($document['final_file_path'], '/'), ENT_QUOTES, 'UTF-8'); ?>"
                                download
                                class="inline-flex items-center justify-center gap-2 rounded-md bg-[#15803d] px-4 py-2.5 font-label-md text-label-md font-semibold text-white shadow-sm transition-colors hover:bg-[#166534]">
                                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">download</span>
                                Signed
                            </a>
                        <?php else: ?>
                            <div class="inline-flex items-center justify-center gap-2 rounded-md bg-[#7b8f82] px-4 py-2.5 font-label-md text-label-md font-semibold text-white shadow-sm cursor-not-allowed" aria-disabled="true">
                                <span class=" material-symbols-outlined text-[18px]" aria-hidden="true">download</span>
                                Signed
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm">
                    <h3 class="font-headline-sm text-headline-sm font-semibold text-primary">Tanda Tangan</h3>
                    <div class="mt-4 space-y-3">
                        <?php foreach ($data['signers'] as $index => $signer): ?>
                            <?php $signedTimestamp = !empty($signer['signed_at']) ? strtotime($signer['signed_at']) : false; ?>
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-outline-variant bg-surface-container-low px-3 py-2.5">
                                <div class="min-w-0">
                                    <p class="truncate font-body-md text-body-md font-medium text-primary"><?= htmlspecialchars($signer['signer_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">Halaman <?= (int) $signer['page_number']; ?></p>
                                    <?php if ($signer['status'] === 'SIGNED' && $signedTimestamp !== false): ?>
                                        <p class="mt-1 inline-flex items-center gap-1 font-body-sm text-body-sm text-on-surface-variant">
                                            <span class="material-symbols-outlined text-[14px]" aria-hidden="true">schedule</span>
                                            Ditandatangani <?= date('d M Y, H:i', $signedTimestamp); ?> WIB
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <?php
                                $signerStatus = [
                                    'WAITING' => ['Menunggu giliran', 'hourglass_top', 'bg-[#fef3c7] text-[#92400e]'],
                                    'CURRENT_TURN' => ['Dalam peninjauan', 'sync', 'bg-[#dbeafe] text-[#1d4ed8]'],
                                    'SIGNED' => ['Signed', 'check_circle', 'bg-[#dcfce7] text-[#15803d]'],
                                    'REJECTED' => ['Ditolak', 'cancel', 'bg-[#fee2e2] text-[#b91c1c]'],
                                ][$signer['status']] ?? null;
                                ?>
                                <?php if ($signerStatus !== null): ?>
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-label-sm font-label-sm <?= $signerStatus[2]; ?>">
                                        <span class="material-symbols-outlined text-[14px]" aria-hidden="true"><?= $signerStatus[1]; ?></span>
                                        <span><?= htmlspecialchars($signerStatus[0], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </span>
                                <?php endif; ?>
                                <?php if ((string) $signer['signer_id'] === (string) $data['user']['id'] && $signer['status'] === 'CURRENT_TURN'): ?>
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-label-sm font-label-sm bg-[#dcfce7] text-[#15803d]">
                                        <span class="material-symbols-outlined text-[14px]" aria-hidden="true">person</span>
                                        <span>Anda</span>
                                    </span>
                                    <form method="POST" action="/Tandain/inbox/updateStampType" class="flex w-full flex-wrap gap-2 border-t border-outline-variant/70 pt-3">
                                        <input type="hidden" name="document_id" value="<?= htmlspecialchars($document['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['stamp_csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <button
                                            type="submit"
                                            name="stamp_type"
                                            value="SIGNATURE_ONLY"
                                            aria-pressed="<?= $signer['stamp_type'] === 'SIGNATURE_ONLY' ? 'true' : 'false'; ?>"
                                            class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-md border px-3 py-2 font-label-md text-label-md font-semibold transition-colors <?= $signer['stamp_type'] === 'SIGNATURE_ONLY' ? 'border-secondary bg-secondary text-white' : 'border-outline-variant bg-white text-primary hover:bg-surface-container-high'; ?>">
                                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">draw</span>
                                            TTD Polos
                                        </button>
                                        <button
                                            type="submit"
                                            name="stamp_type"
                                            value="DUAL_DISPLAY"
                                            aria-pressed="<?= $signer['stamp_type'] === 'DUAL_DISPLAY' ? 'true' : 'false'; ?>"
                                            class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-md border px-3 py-2 font-label-md text-label-md font-semibold transition-colors <?= $signer['stamp_type'] === 'DUAL_DISPLAY' ? 'border-secondary bg-secondary text-white' : 'border-outline-variant bg-white text-primary hover:bg-surface-container-high'; ?>">
                                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">qr_code_2</span>
                                            TTD + QR
                                        </button>
                                        <?php if (!$hasActiveCertificate || !$hasActiveSignature): ?>
                                            <div class="flex justify-between w-full mt-2 flex-wrap gap-2 text-[11px] font-semibold">
                                                <?php if (!$hasActiveCertificate): ?>
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#fee2e2] px-2 py-1 text-[#b91c1c]">
                                                        <span class="material-symbols-outlined text-[13px]" aria-hidden="true">error</span>
                                                        Sertifikat belum aktif
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (!$hasActiveSignature): ?>
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#fee2e2] px-2 py-1 text-[#b91c1c]">
                                                        <span class="material-symbols-outlined text-[13px]" aria-hidden="true">error</span>
                                                        Tanda tangan belum tersedia
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <?php if ($document['signer_status'] === 'CURRENT_TURN'): ?>
                    <section class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="font-headline-sm text-headline-sm font-semibold text-primary">Tanda Tangani?</h3>
                            </div>
                            <?php if ($canDecide): ?>
                                <div class="flex shrink-0 flex-wrap gap-2">
                                    <button type="button" data-modal-open="reject-document-modal" class="inline-flex items-center justify-center gap-2 rounded-md border border-[#f3b4b4] bg-[#fff0f0] px-4 py-2.5 font-label-md text-label-md font-semibold text-[#b91c1c] transition-colors hover:bg-[#fde7e7]">
                                        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">close</span>
                                        Tolak
                                    </button>
                                    <?php if ($canSign): ?>
                                        <button type="button" data-modal-open="agree-document-modal" class="inline-flex items-center justify-center gap-2 rounded-md bg-[#0f8f5a] px-4 py-2.5 font-label-md text-label-md font-semibold text-white shadow-sm transition-colors hover:bg-[#0e7d4f]">
                                            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">check</span>
                                            Setujui
                                        </button>
                                    <?php else: ?>
                                        <span class="inline-flex cursor-not-allowed items-center gap-2 rounded-md bg-surface-container px-4 py-2.5 font-label-md text-label-md font-semibold text-on-surface-variant" title="Aktifkan sertifikat dan tanda tangan sebelum menyetujui">
                                            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">lock</span>
                                            Setujui
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endif; ?>
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
                    const baseViewport = page.getViewport({
                        scale: 1
                    });
                    const scale = Math.min(1.3, pdfContainer.clientWidth / baseViewport.width);
                    const viewport = page.getViewport({
                        scale: Math.max(scale, 0.1)
                    });
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

                    await page.render({
                        canvasContext: canvas.getContext('2d'),
                        viewport
                    }).promise;

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
<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$document = $data['document'];

$hasStampedPdf = !empty($document['final_file_path']);
$documentFile = $hasStampedPdf ? $document['final_file_path'] : ($document['original_file_path'] ?? '');
$canDecide = ($document['signer_status'] ?? '') === 'CURRENT_TURN'
    && ($document['status'] ?? '') === 'IN_PROGRESS';
$currentSigner = null;

foreach ($data['signers'] as $signer) {
    if ($signer['signer_id'] === $data['user']['id']) {
        $currentSigner = $signer;
    }
}

$hasActiveCertificate = $currentSigner !== null && (int) ($currentSigner['has_active_cert'] ?? 0) === 1;
$hasActiveSignature = $currentSigner !== null && !empty($currentSigner['has_active_signature']);
$canSign = $canDecide && $hasActiveCertificate && $hasActiveSignature;
?>

<main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">
    <div class="mx-auto w-full">
        <div class="mb-6">
            <a href="/Tandain/inbox" class="inline-flex items-center gap-1 text-secondary font-label-md text-label-md hover:underline">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span>
                <span>Kembali ke daftar</span>
            </a>

            <h1 class="mt-2 font-headline-lg text-headline-lg text-primary tracking-tight font-bold">Persetujuan Dokumen</h1>
            <p class="mt-1 font-body-md text-body-md text-on-surface-variant">Tinjau isi berkas dan tentukan keputusan persetujuan</p>
        </div>

        <div class="space-y-3 [&:has([data-notice]:not([hidden]))]:mb-6">
            <?php require 'views/layouts/flash.php'; ?>

            <?php if (!empty($data['signing_warning'])): ?>
                <?php $notice = ['type' => 'warning', 'message' => $data['signing_warning']]; ?>
                <?php require 'views/layouts/notice.php'; ?>
            <?php endif; ?>

            <?php if ($document['status'] === 'REJECTED' && !empty($document['rejection_reason'])): ?>
                <?php $notice = ['type' => 'error', 'message' => 'Alasan penolakan: ' . $document['rejection_reason']]; ?>
                <?php require 'views/layouts/notice.php'; ?>
            <?php endif; ?>
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

                        <div class="flex flex-wrap items-center gap-2">
                            <button id="pdf-zoom-out" type="button" class="flex items-center gap-2 bg-surface-container-low px-3 py-1.5 rounded-lg border border-outline-variant" aria-label="Perkecil PDF">
                                <span class="material-symbols-outlined text-[18px] text-primary" aria-hidden="true">zoom_out</span>
                            </button>
                            <input id="pdf-zoom-range" type="range" min="50" max="200" step="10" value="100" class="w-24 accent-brand" aria-label="Skala tampilan PDF">
                            <span id="pdf-zoom-value" class="min-w-[42px] text-center font-code-tabular text-[11px] text-on-surface-variant">100%</span>
                            <button id="pdf-zoom-in" type="button" class="flex items-center gap-2 bg-surface-container-low px-3 py-1.5 rounded-lg border border-outline-variant" aria-label="Perbesar PDF">
                                <span class="material-symbols-outlined text-[18px] text-primary" aria-hidden="true">zoom_in</span>
                            </button>
                        </div>
                    </div>

                    <?php if ($documentFile !== ''): ?>
                        <div class="h-[720px] overflow-y-auto overflow-x-scroll bg-surface-container-low p-4">
                            <p id="pdf-loading" class="py-8 text-center font-body-md text-body-md text-on-surface-variant" role="status">Memuat dokumen...</p>
                            <div id="pdf-error" class="hidden py-8 text-center font-body-md text-body-md text-error" role="alert"></div>
                            <div id="pdf-container" class="min-w-full w-max space-y-4 pb-2" aria-label="Pratinjau dokumen dengan posisi tanda tangan"></div>
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
                            <div class="mt-6 flex flex-wrap justify-end gap-3">
                                <button type="button" data-modal-close class="btn-md inline-flex border border-outline-variant font-semibold text-primary hover:bg-surface-container-low">Batal</button>
                                <button type="submit" class="btn-lg inline-flex items-center gap-2 bg-[#0f8f5a] font-semibold text-white hover:bg-[#0e7d4f]">
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
                            <div class="mt-6 flex flex-wrap justify-end gap-3">
                                <button type="button" data-modal-close class="btn-md inline-flex border border-outline-variant font-semibold text-primary hover:bg-surface-container-low">Batal</button>
                                <button type="submit" class="btn-md inline-flex items-center gap-2 bg-[#b91c1c] font-semibold text-white hover:bg-[#991b1b]">
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

            <?php $identityLabel = ($document['requester_role_name'] ?? '') === 'pegawai' ? 'NIP' : (($document['requester_role_name'] ?? '') === 'mahasiswa' ? 'NIM' : 'NIM/NIP'); ?>
            <aside class="space-y-6">
                <section class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-headline-sm text-headline-sm font-semibold text-primary">Pemohon</h3>
                            <p class="mt-2 font-body-md text-body-md font-semibold text-primary"><?= htmlspecialchars($document['requester_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php $requesterIdentityNumber = trim((string) ($document['requester_nim_nip'] ?? '')); ?>
                            <p class="font-body-sm text-body-sm text-on-surface-variant"><?= $identityLabel; ?>: <?= htmlspecialchars($requesterIdentityNumber !== '' ? $requesterIdentityNumber : 'Tidak tersedia', ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant"><?= htmlspecialchars(formatUserAffiliation($document['requester_position'] ?? null, $document['requester_study_program'] ?? null, $document['requester_role_display_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant"><?= htmlspecialchars($document['requester_email'] ?? 'Email Tidak tersedia', ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3 border-t border-outline-variant/70 pt-4 text-sm">
                        <div>
                            <p class="font-label-md text-label-md text-on-surface-variant">Judul Dokumen</p>
                            <p class="mt-1 font-body-md text-body-md font-medium text-primary"><?= htmlspecialchars($document['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>

                        <?php if (!empty($document['document_number'])): ?>
                            <div>
                                <p class="font-label-md text-label-md text-on-surface-variant">Nomor Naskah</p>
                                <p class="mt-1 font-code-tabular text-body-sm font-medium text-primary"><?= htmlspecialchars($document['document_number'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        <?php endif; ?>

                        <div>
                            <p class="font-label-md text-label-md text-on-surface-variant">Tanggal Diajukan</p>
                            <p class="mt-1 font-body-md text-body-md text-on-surface"><?= formatTanggalIndo($document['submitted_at'] ?? null, 'datetime'); ?></p>
                        </div>

                        <div>
                            <p class="font-label-md text-label-md text-on-surface-variant mb-2">Status</p>
                            <?php renderStatusBadge($document['status']); ?>
                        </div>

                        <div>
                            <?php
                            $signedCount = 0;

                            foreach ($data['signers'] as $signer) {
                                if (!empty($signer['signed_at']) || ($signer['status'] ?? '') === 'SIGNED') {
                                    $signedCount++;
                                }
                            }

                            $totalSigners = count($data['signers']);
                            $percent = $totalSigners > 0 ? (int) round(($signedCount / $totalSigners) * 100) : 0;

                            if ($document['status'] === 'REJECTED') {
                                $percentClass = 'bg-[#b91c1c]';
                            } elseif ($percent === 100 && $totalSigners > 0) {
                                $percentClass = 'bg-[#15803d]';
                            } else {
                                $percentClass = 'bg-brand';
                            }
                            ?>
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="font-label-sm text-[11px] font-semibold text-on-surface-variant"><?= $signedCount; ?>/<?= $totalSigners; ?> tanda tangan</span>
                                <span class="font-code-tabular text-[11px] font-bold text-on-surface-variant"><?= $percent; ?>%</span>
                            </div>

                            <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                                <div class="<?= $percentClass; ?> h-2 rounded-full" style="width: <?= $percent; ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <?php
                    $canDownloadOriginal = !empty($document['original_file_path']) && ($document['requester_id'] ?? null) === $data['user']['id'];
                    $canDownloadSigned = ($document['status'] ?? '') === 'COMPLETED' && !empty($document['final_file_path']);
                    ?>

                    <?php if ($canDownloadOriginal || $canDownloadSigned): ?>
                        <div class="flex flex-wrap items-center gap-2 mt-6">
                            <?php if ($canDownloadOriginal): ?>
                                <a
                                    href="/Tandain/inbox/file/<?= rawurlencode($document['id']); ?>?version=original&amp;download=1"
                                    class="btn-md inline-flex flex-1 items-center justify-center gap-2 border border-outline-variant bg-white font-semibold text-primary transition-colors hover:bg-surface-container-low">
                                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">download</span>
                                    <span>Original</span>
                                </a>
                            <?php endif; ?>

                            <?php if ($canDownloadSigned): ?>
                                <a
                                    href="/Tandain/inbox/detail?document_id=<?= rawurlencode($document['id']); ?>&amp;download=signed"
                                    class="btn-md inline-flex flex-1 items-center justify-center gap-2 bg-[#15803d] font-semibold text-white shadow-sm transition-colors hover:bg-[#166534]">
                                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">download</span>
                                    <span>Signed</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="rounded-xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm">
                    <h3 class="font-headline-sm text-headline-sm font-semibold text-primary">Penandatangan</h3>
                    <div class="mt-4 space-y-3">
                        <?php foreach ($data['signers'] as $index => $signer): ?>
                            <?php $signedTimestamp = !empty($signer['signed_at']) ? strtotime($signer['signed_at']) : false; ?>
                            <div class="flex flex-wrap items-start gap-2 rounded-lg border border-outline-variant bg-surface-container-low px-3 py-2.5">
                                <div class="flex w-full items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="whitespace-normal break-words font-body-md text-body-md font-medium text-primary"><?= htmlspecialchars($signer['signer_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                        <?php $signerAffiliation = formatUserAffiliation($signer['signer_position'] ?? null, $signer['signer_study_program'] ?? null, $signer['signer_role_display_name'] ?? ''); ?>
                                        <?php if ($signerAffiliation !== ''): ?>
                                            <p class="font-body-sm text-xs text-on-surface-variant"><?= htmlspecialchars($signerAffiliation, ENT_QUOTES, 'UTF-8'); ?></p>
                                        <?php endif; ?>
                                        <div class="mt-1 flex items-center gap-2">
                                            <p class="font-body-sm text-body-sm text-on-surface-variant">Halaman <?= (int) $signer['page_number']; ?></p>
                                            <?php if ((string) $signer['signer_id'] === (string) $data['user']['id']): ?>
                                                <?php renderStatusBadge('YOU', 'small'); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex shrink-0 flex-wrap items-center justify-end gap-1.5">
                                        <?php renderStatusBadge($signer['status']); ?>
                                    </div>
                                </div>

                                <?php if ($signer['status'] === 'SIGNED' && $signedTimestamp !== false): ?>
                                    <p class=" inline-flex items-center gap-1 font-body-sm text-body-sm text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[14px]" aria-hidden="true">schedule</span>
                                        Ditandatangani <?= formatTanggalIndo($signedTimestamp, 'datetime'); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($signer['status'] === 'REJECTED' && !empty($signer['rejection_reason'])): ?>
                                    <p class="w-full font-body-sm text-body-sm text-error bg-error-container/70 border border-error-container p-2 rounded">Alasan: <?= htmlspecialchars($signer['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>

                                <?php if ((string) $signer['signer_id'] === (string) $data['user']['id'] && $signer['status'] === 'CURRENT_TURN'): ?>
                                    <form method="POST" action="/Tandain/inbox/updateStampType" class="flex w-full flex-wrap gap-2 border-t border-outline-variant/70 pt-3">
                                        <input type="hidden" name="document_id" value="<?= htmlspecialchars($document['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['stamp_csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <button
                                            type="submit"
                                            name="stamp_type"
                                            value="SIGNATURE_ONLY"
                                            aria-pressed="<?= $signer['stamp_type'] === 'SIGNATURE_ONLY' ? 'true' : 'false'; ?>"
                                            class="btn-sm inline-flex flex-1 items-center justify-center gap-1.5 whitespace-nowrap border font-semibold transition-colors <?= $signer['stamp_type'] === 'SIGNATURE_ONLY' ? 'border-secondary bg-secondary text-white' : 'border-outline-variant bg-white text-primary hover:bg-surface-container-high'; ?>">
                                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">draw</span>
                                            <span>TTD Polos</span>
                                        </button>
                                        <button
                                            type="submit"
                                            name="stamp_type"
                                            value="DUAL_DISPLAY"
                                            aria-pressed="<?= $signer['stamp_type'] === 'DUAL_DISPLAY' ? 'true' : 'false'; ?>"
                                            class="btn-sm inline-flex flex-1 items-center justify-center gap-1.5 whitespace-nowrap border font-semibold transition-colors <?= $signer['stamp_type'] === 'DUAL_DISPLAY' ? 'border-secondary bg-secondary text-white' : 'border-outline-variant bg-white text-primary hover:bg-surface-container-high'; ?>">
                                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">qr_code_2</span>
                                            <span>TTD + QR</span>
                                        </button>
                                        <button
                                            type="submit"
                                            name="stamp_type"
                                            value="QR_ONLY"
                                            aria-pressed="<?= $signer['stamp_type'] === 'QR_ONLY' ? 'true' : 'false'; ?>"
                                            class="btn-sm inline-flex flex-1 items-center justify-center gap-1.5 whitespace-nowrap border font-semibold transition-colors <?= $signer['stamp_type'] === 'QR_ONLY' ? 'border-secondary bg-secondary text-white' : 'border-outline-variant bg-white text-primary hover:bg-surface-container-high'; ?>">
                                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">qr_code</span>
                                            <span>QR Saja</span>
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
                                    <button type="button" data-modal-open="reject-document-modal" class="btn-md inline-flex items-center justify-center gap-2 border border-[#f3b4b4] bg-[#fff0f0] font-semibold text-[#b91c1c] transition-colors hover:bg-[#fde7e7]">
                                        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">close</span>
                                        Tolak
                                    </button>
                                    <?php if ($canSign): ?>
                                        <button type="button" data-modal-open="agree-document-modal" class="btn-md inline-flex items-center justify-center gap-2 bg-[#0f8f5a] font-semibold text-white shadow-sm transition-colors hover:bg-[#0e7d4f]">
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
    <script src="/Tandain/assets/js/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = '/Tandain/assets/js/pdf.worker.min.js';

        const PT_PER_MM = 72 / 25.4;
        const pdfUrl = <?= json_encode('/Tandain/inbox/file/' . rawurlencode($document['id']), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        const hasStampedPdf = <?= json_encode($hasStampedPdf) ?>;
        const signerPositions = <?= json_encode(array_map(static function ($signer) {
                                    $signaturePath = $signer['signer_signature_image_path'] ?? '';
                                    $signatureUrl = '';
                                    if (
                                        is_string($signaturePath)
                                        && preg_match('/\Auploads\/signatures\/[A-Za-z0-9._-]+\.png\z/D', $signaturePath)
                                    ) {
                                        if (is_file($signaturePath)) {
                                            $signatureUrl = '/Tandain/' . $signaturePath . '?v=' . filemtime($signaturePath);
                                        }
                                    }

                                    return [
                                        'name' => $signer['signer_name'],
                                        'status' => $signer['status'],
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
        const zoomRange = document.getElementById('pdf-zoom-range');
        const zoomValue = document.getElementById('pdf-zoom-value');
        const zoomInButton = document.getElementById('pdf-zoom-in');
        const zoomOutButton = document.getElementById('pdf-zoom-out');

        const setPdfZoom = (value) => {
            const zoomPercent = Math.max(50, Math.min(200, Number(value)));
            zoomRange.value = String(zoomPercent);
            zoomValue.textContent = `${zoomPercent}%`;
            pdfContainer.style.zoom = String(zoomPercent / 100);

            zoomOutButton.disabled = zoomPercent <= 50;
            zoomInButton.disabled = zoomPercent >= 200;
        };

        zoomRange.addEventListener('input', (event) => setPdfZoom(event.target.value));
        zoomOutButton.addEventListener('click', () => setPdfZoom(Number(zoomRange.value) - 10));
        zoomInButton.addEventListener('click', () => setPdfZoom(Number(zoomRange.value) + 10));
        setPdfZoom(100);

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
                    const pxPerMm = viewport.scale * PT_PER_MM;
                    const scaleFactor = viewport.scale / 1.3;
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
                        .filter((signer) => signer.page === pageNumber && signer.status !== 'SIGNED')
                        .forEach((signer) => {
                            const marker = document.createElement('div');
                            const qrOnly = signer.stampType === 'QR_ONLY';
                            const signatureOnly = signer.stampType === 'SIGNATURE_ONLY';
                            const heightPx = 18 * pxPerMm;
                            let widthPx = 58 * pxPerMm;

                            if (qrOnly) {
                                widthPx = 18 * pxPerMm;
                            } else if (signatureOnly) {
                                widthPx = 46 * pxPerMm;
                            }

                            const clampedLeft = Math.max(0, Math.min(Math.max(0, Math.min(1, signer.x)) * viewport.width, viewport.width - widthPx));
                            const clampedTop = Math.max(0, Math.min(Math.max(0, Math.min(1, signer.y)) * viewport.height, viewport.height - heightPx));

                            marker.className = 'absolute z-10 flex items-center overflow-hidden bg-white shadow-sm';
                            marker.style.width = `${widthPx}px`;
                            marker.style.height = `${heightPx}px`;
                            marker.style.left = `${clampedLeft}px`;
                            marker.style.top = `${clampedTop}px`;
                            marker.style.gap = qrOnly || signatureOnly ? '0px' : `${2 * pxPerMm}px`;
                            marker.title = `${signer.name} (Halaman ${pageNumber})`;

                            const previewWatermark = document.createElement('span');
                            previewWatermark.textContent = 'PREVIEW';
                            previewWatermark.setAttribute('aria-hidden', 'true');
                            previewWatermark.style.position = 'absolute';
                            previewWatermark.style.zIndex = '20';
                            previewWatermark.style.left = '50%';
                            previewWatermark.style.top = '50%';
                            previewWatermark.style.transform = 'translate(-50%, -50%) rotate(-24deg)';
                            previewWatermark.style.padding = `${Math.max(1, 1 * scaleFactor)}px ${Math.max(3, 3 * scaleFactor)}px`;
                            previewWatermark.style.border = `${Math.max(1, scaleFactor)}px solid rgba(185, 28, 28, 0.75)`;
                            previewWatermark.style.borderRadius = `${Math.max(2, 3 * scaleFactor)}px`;
                            previewWatermark.style.backgroundColor = 'rgba(255, 255, 255, 0.82)';
                            previewWatermark.style.color = 'rgba(185, 28, 28, 0.85)';
                            previewWatermark.style.fontFamily = 'sans-serif';
                            previewWatermark.style.fontSize = `${Math.max(7, 9 * scaleFactor)}px`;
                            previewWatermark.style.fontWeight = '800';
                            previewWatermark.style.lineHeight = '1';
                            previewWatermark.style.letterSpacing = '0.08em';
                            previewWatermark.style.whiteSpace = 'nowrap';
                            previewWatermark.style.pointerEvents = 'none';
                            marker.appendChild(previewWatermark);

                            const makeIpbBox = (topText, bottomText, sigUrl, signerName, isFlex1) => {
                                const box = document.createElement('div');
                                box.style.display = 'flex';
                                box.style.flexDirection = 'column';
                                box.style.justifyContent = 'space-between';
                                box.style.backgroundColor = '#ffffff';
                                box.style.boxSizing = 'border-box';
                                box.style.height = '100%';

                                if (isFlex1) {
                                    box.style.flex = '1';
                                    box.style.minWidth = '0';
                                } else {
                                    box.style.width = '100%';
                                }

                                const topRow = document.createElement('div');
                                topRow.style.display = 'flex';
                                topRow.style.alignItems = 'center';
                                topRow.style.width = '100%';
                                topRow.style.lineHeight = '1';

                                const leftTopLine = document.createElement('span');
                                leftTopLine.style.width = `${Math.max(4, Math.round(6 * scaleFactor))}px`;
                                leftTopLine.style.height = '1px';
                                leftTopLine.style.backgroundColor = '#000000';
                                leftTopLine.style.flexShrink = '0';

                                const topLabel = document.createElement('span');
                                topLabel.textContent = topText;
                                topLabel.style.fontSize = `${Math.max(6.5, Math.round(7.5 * scaleFactor))}px`;
                                topLabel.style.fontFamily = 'monospace, sans-serif';
                                topLabel.style.fontWeight = '500';
                                topLabel.style.color = '#000000';
                                topLabel.style.padding = '0 2px';
                                topLabel.style.whiteSpace = 'nowrap';
                                topLabel.style.flexShrink = '0';

                                const rightTopLine = document.createElement('span');
                                rightTopLine.style.flex = '1';
                                rightTopLine.style.height = '1px';
                                rightTopLine.style.backgroundColor = '#000000';

                                topRow.appendChild(leftTopLine);
                                topRow.appendChild(topLabel);
                                topRow.appendChild(rightTopLine);

                                const midRow = document.createElement('div');
                                midRow.style.flex = '1';
                                midRow.style.display = 'flex';
                                midRow.style.alignItems = 'center';
                                midRow.style.justifyContent = 'center';
                                midRow.style.borderLeft = '1px solid #000000';
                                midRow.style.borderRight = '1px solid #000000';
                                midRow.style.padding = '1px 3px';
                                midRow.style.minHeight = '0';
                                midRow.style.overflow = 'hidden';

                                if (sigUrl) {
                                    const img = document.createElement('img');
                                    img.src = sigUrl;
                                    img.alt = `Tanda tangan ${signerName}`;
                                    img.style.maxHeight = '100%';
                                    img.style.maxWidth = '100%';
                                    img.style.objectFit = 'contain';
                                    midRow.appendChild(img);
                                } else {
                                    const placeholder = document.createElement('span');
                                    placeholder.textContent = signerName;
                                    placeholder.style.fontSize = `${Math.max(7.5, Math.round(9.5 * scaleFactor))}px`;
                                    placeholder.style.fontWeight = '600';
                                    placeholder.style.color = '#475569';
                                    placeholder.style.textAlign = 'center';
                                    placeholder.style.overflow = 'hidden';
                                    placeholder.style.textOverflow = 'ellipsis';
                                    placeholder.style.whiteSpace = 'nowrap';
                                    midRow.appendChild(placeholder);
                                }

                                const botRow = document.createElement('div');
                                botRow.style.display = 'flex';
                                botRow.style.alignItems = 'center';
                                botRow.style.width = '100%';
                                botRow.style.lineHeight = '1';

                                const leftBotLine = document.createElement('span');
                                leftBotLine.style.width = `${Math.max(4, Math.round(6 * scaleFactor))}px`;
                                botRow.appendChild(leftBotLine);
                                leftBotLine.style.height = '1px';
                                leftBotLine.style.backgroundColor = '#000000';
                                leftBotLine.style.flexShrink = '0';

                                const botLabel = document.createElement('span');
                                botLabel.textContent = bottomText;
                                botLabel.style.fontSize = `${Math.max(6, Math.round(7 * scaleFactor))}px`;
                                botLabel.style.fontFamily = 'monospace, sans-serif';
                                botLabel.style.color = '#475569';
                                botLabel.style.padding = '0 2px';
                                botLabel.style.whiteSpace = 'nowrap';
                                botLabel.style.flexShrink = '0';
                                botRow.appendChild(botLabel);

                                const rightBotLine = document.createElement('span');
                                rightBotLine.style.flex = '1';
                                rightBotLine.style.height = '1px';
                                rightBotLine.style.backgroundColor = '#000000';
                                botRow.appendChild(rightBotLine);

                                box.appendChild(topRow);
                                box.appendChild(midRow);
                                box.appendChild(botRow);

                                return box;
                            };

                            if (qrOnly) {
                                const qrImage = document.createElement('img');
                                qrImage.src = qrUrl;
                                qrImage.alt = `Kode QR dokumen untuk ${signer.name}`;
                                qrImage.className = 'h-full w-full object-contain';
                                marker.appendChild(qrImage);
                            } else if (signatureOnly) {
                                const box = makeIpbBox('digitally signed', 'tandain.pnj.ac.id', signer.signatureUrl, signer.name, false);
                                marker.appendChild(box);
                            } else {
                                const qrImage = document.createElement('img');
                                qrImage.src = qrUrl;
                                qrImage.alt = `Kode QR dokumen untuk ${signer.name}`;
                                qrImage.style.width = `${16 * pxPerMm}px`;
                                qrImage.style.height = `${16 * pxPerMm}px`;
                                qrImage.style.objectFit = 'contain';
                                qrImage.style.flexShrink = '0';
                                marker.appendChild(qrImage);

                                const box = makeIpbBox('digitally signed', 'tandain.pnj.ac.id', signer.signatureUrl, signer.name, true);
                                marker.appendChild(box);
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
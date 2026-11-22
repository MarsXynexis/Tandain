<?php

function formatTanggalIndo($value, $format = 'date')
{
    $timestamp = is_int($value) ? $value : false;

    if (is_string($value) && trim($value) !== '') {
        $timestamp = strtotime($value);
    }

    $result = '-';

    if ($timestamp !== false) {
        $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $date = date('d', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
        $time = date('H:i', $timestamp) . ' WIB';

        $result = match ($format) {
            'datetime' => $date . ', ' . $time,
            'time' => $time,
            'time-seconds' => date('H:i:s', $timestamp) . ' WIB',
            default => $date,
        };
    }

    return $result;
}

function renderStatusBadge($status, $size = 'normal')
{
    $statuses = [
        'DRAFT' => ['Draf', 'edit_note', 'neutral'],
        'PENDING_TRIAGE' => ['Menunggu Disposisi', 'pending_actions', 'warning'],
        'IN_PROGRESS' => ['Sedang Diproses', 'sync', 'info'],
        'COMPLETED' => ['Selesai', 'check_circle', 'success'],
        'REJECTED' => ['Ditolak', 'cancel', 'error'],
        'CANCELLED' => ['Dibatalkan', 'block', 'neutral'],
        'WAITING' => ['Menunggu Giliran', 'hourglass_top', 'neutral'],
        'CURRENT_TURN' => ['Menunggu Tanda Tangan', 'draw', 'warning'],
        'SIGNED' => ['Sudah Ditandatangani', 'check_circle', 'success'],
        'ACTIVE' => ['Aktif', 'check_circle', 'success'],
        'INACTIVE' => ['Nonaktif', 'pause_circle', 'neutral'],
        'SUSPENDED' => ['Ditangguhkan', 'block', 'error'],
        'REVOKED' => ['Dicabut', 'block', 'error'],
        'EXPIRED' => ['Kedaluwarsa', 'schedule', 'warning'],
        'CERT_ACTIVE' => ['Sertifikat Aktif', 'verified', 'success'],
        'CERT_INACTIVE' => ['Tanpa Sertifikat Aktif', 'gpp_maybe', 'neutral'],
        'YOU' => ['Anda', 'person', 'success'],
    ];

    [$label, $icon, $tone] = $statuses[$status] ?? ['Status Tidak Diketahui', 'help', 'neutral'];
    $sizeClass = $size === 'small'
        ? ' status-badge--small gap-0.5 px-1.5 py-0.5 text-[11px] leading-[14px]'
        : '';
    $iconClass = $size === 'small' ? ' text-[11px]' : '';
    ?>
    <span class="status-badge<?= $sizeClass; ?>" data-tone="<?= $tone; ?>">
      <span class="material-symbols-outlined<?= $iconClass; ?>" aria-hidden="true"><?= $icon; ?></span>
      <?= $label; ?>
    </span>
    <?php
}

function formatUserAffiliation($position = null, $studyProgram = null, $fallback = '')
{
    $cleanPosition = trim((string) $position);
    $cleanStudyProgram = trim((string) $studyProgram);
    $cleanFallback = trim((string) $fallback);
    $result = $cleanFallback;

    if ($cleanPosition !== '' && $cleanStudyProgram !== '') {
        $result = stripos($cleanPosition, $cleanStudyProgram) !== false
            ? $cleanPosition
            : $cleanPosition . ' • ' . $cleanStudyProgram;
    } elseif ($cleanPosition !== '') {
        $result = $cleanPosition;
    } elseif ($cleanStudyProgram !== '') {
        $result = $cleanStudyProgram;
    }

    return $result;
}

function isValidUuid($id)
{
    return is_string($id) && (bool) preg_match('/\A[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}\z/i', $id);
}

function csrfToken()
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === '') {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

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

function renderStatusBadge($status)
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
    ];

    [$label, $icon, $tone] = $statuses[$status] ?? ['Status Tidak Diketahui', 'help', 'neutral'];
    ?>
    <span class="status-badge" data-tone="<?= $tone; ?>">
      <span class="material-symbols-outlined" aria-hidden="true"><?= $icon; ?></span>
      <?= $label; ?>
    </span>
    <?php
}

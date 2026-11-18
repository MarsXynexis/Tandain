<?php
$notice = $_SESSION['flash'] ?? null;

unset($_SESSION['flash']);

if (!empty($notice['message'])) {
    require 'views/layouts/notice.php';
}

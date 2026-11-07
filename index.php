<?php

date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';

$app = new App;

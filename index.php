<?php

date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once 'vendor/autoload.php';
require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';
require_once 'core/ViewHelpers.php';
require_once 'core/PDFStamper.php';

$app = new App;

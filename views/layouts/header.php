<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= $data['title'] ?? 'Tandain - Platform Tanda Tangan & Segel Elektronik PNJ'; ?></title>
  <link rel="icon" type="image/png" href="/Tandain/assets/img/app-icon.png">

  <link href="/Tandain/assets/css/fonts.css" rel="stylesheet">
  <link href="/Tandain/assets/css/material-symbols.css" rel="stylesheet">
  <link href="/Tandain/assets/css/notices.css" rel="stylesheet">
  <link href="/Tandain/assets/css/ui.css" rel="stylesheet">
  <script src="/Tandain/assets/js/notices.js"></script>

<?php require_once __DIR__ . '/tailwind_tokens.php'; ?>
</head>

<body class="bg-background text-on-surface font-body-md antialiased min-h-screen flex selection:bg-secondary selection:text-on-secondary">

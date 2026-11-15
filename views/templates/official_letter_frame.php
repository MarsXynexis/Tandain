<?php
if (isset($letterSheetId) && is_string($letterSheetId) && $letterSheetId !== '') {
    $sheetId = $letterSheetId;
} else {
    $sheetId = 'pnj-a4-sheet';
}

if (isset($letterBodyId) && is_string($letterBodyId) && $letterBodyId !== '') {
    $bodyId = $letterBodyId;
} else {
    $bodyId = 'pnj-letter-body';
}

if (isset($letterBodyHtml) && is_string($letterBodyHtml)) {
    $initialBodyHtml = $letterBodyHtml;
} else {
    $initialBodyHtml = '';
}
?>

<?php if (!defined('PNJ_LETTER_FRAME_STYLE_INCLUDED')): ?>
<?php define('PNJ_LETTER_FRAME_STYLE_INCLUDED', true); ?>
<style>
  .pnj-a4-sheet {
    width: 210mm;
    min-height: 297mm;
    padding: 16mm 22mm 20mm 22mm;
    background: #ffffff;
    color: #000000;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-sizing: border-box;
    margin: 0 auto;
    font-family: 'Calibri', 'Arial', 'Helvetica', sans-serif;
    text-align: left;
  }

  .pnj-a4-sheet *,
  .pnj-a4-sheet *::before,
  .pnj-a4-sheet *::after {
    box-sizing: border-box;
  }

  .pnj-letter-kop {
    display: flex;
    align-items: center;
    gap: 16px;
    padding-bottom: 10px;
    margin-bottom: 16px;
    border-bottom: 1.5px solid #000000;
  }

  .pnj-letter-kop-logo {
    width: 118px;
    height: 118px;
    object-fit: contain;
    flex-shrink: 0;
    display: block;
  }

  .pnj-letter-kop-text {
    flex: 1;
    text-align: center;
    font-family: 'Times New Roman', Times, serif;
    color: #000000;
    line-height: 1.22;
  }

  .pnj-kop-ministry {
    font-size: 13pt;
    font-weight: 400;
    letter-spacing: 0.01em;
  }

  .pnj-kop-institution {
    font-size: 15pt;
    font-weight: 700;
    margin-top: 1px;
    margin-bottom: 2px;
  }

  .pnj-kop-meta {
    font-size: 10pt;
    font-weight: 400;
    line-height: 1.25;
  }

  .pnj-kop-meta u {
    color: #000000;
    text-decoration: underline;
  }

  .pnj-letter-body {
    flex: 1;
    font-family: 'Calibri', 'Arial', 'Helvetica', sans-serif;
    font-size: 10.5pt;
    line-height: 1.4;
    color: #000000;
  }

  .pnj-letter-body h1,
  .pnj-letter-body h2,
  .pnj-letter-body h3,
  .pnj-letter-body h4 {
    color: #000000;
    font-weight: 700;
    line-height: 1.3;
    margin: 0 0 8px 0;
  }

  .pnj-letter-body h1 {
    font-size: 13pt;
  }

  .pnj-letter-body h2 {
    font-size: 12pt;
  }

  .pnj-letter-body h3 {
    font-size: 11pt;
  }

  .pnj-letter-body h4 {
    font-size: 10.5pt;
  }

  .pnj-letter-body p {
    margin: 0 0 10px 0;
    text-align: justify;
  }

  .pnj-letter-body .text-center,
  .pnj-letter-body .pnj-text-center,
  .pnj-letter-body [align="center"] {
    text-align: center;
  }

  .pnj-letter-body .text-right,
  .pnj-letter-body .pnj-text-right,
  .pnj-letter-body [align="right"] {
    text-align: right;
  }

  .pnj-letter-body .text-left,
  .pnj-letter-body .pnj-text-left,
  .pnj-letter-body [align="left"] {
    text-align: left;
  }

  .pnj-letter-body a {
    color: #000000;
    text-decoration: underline;
  }

  .pnj-letter-body strong,
  .pnj-letter-body b {
    font-weight: 700;
  }

  .pnj-letter-body u {
    text-decoration: underline;
  }

  .pnj-letter-body ol {
    list-style-type: decimal;
    margin: 4px 0 10px 0;
    padding-left: 22px;
  }

  .pnj-letter-body ul {
    list-style-type: disc;
    margin: 4px 0 10px 0;
    padding-left: 22px;
  }

  .pnj-letter-body li {
    margin: 2px 0;
    text-align: left;
  }

  .pnj-letter-body table {
    width: 100%;
    border-collapse: collapse;
    margin: 8px 0 12px 0;
    font-size: 10.5pt;
  }

  .pnj-letter-body th,
  .pnj-letter-body td {
    padding: 3px 6px;
    vertical-align: top;
    text-align: left;
  }

  .pnj-letter-body table[border="1"] th,
  .pnj-letter-body table[border="1"] td,
  .pnj-letter-body table.pnj-table-bordered th,
  .pnj-letter-body table.pnj-table-bordered td {
    border: 1px solid #000000;
    padding: 5px 8px;
  }

  .pnj-letter-body table.pnj-table-bordered th {
    font-weight: 700;
    background: #f5f5f5;
  }

  .pnj-letter-heading {
    text-align: center;
    margin: 4px 0 16px 0;
    line-height: 1.35;
  }

  .pnj-letter-heading p {
    text-align: center;
    margin: 0;
  }

  .pnj-signature-right {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
    page-break-inside: avoid;
    break-inside: avoid;
  }

  .pnj-signature-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    margin-top: 20px;
    page-break-inside: avoid;
    break-inside: avoid;
  }

  .pnj-signature-col {
    width: 48%;
    min-width: 220px;
    text-align: left;
    line-height: 1.35;
  }

  .pnj-signature-col p {
    margin: 0;
    text-align: left;
  }

  .pnj-signature-slot {
    height: 75px;
    min-height: 75px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    margin: 6px 0;
  }

  .pnj-ttd-placeholder {
    width: 100%;
    max-width: 220px;
    height: 75px;
    min-height: 75px;
    border: 1px dashed #64748b;
    background: #f8fafc;
    color: #334155;
    font-size: 8.5pt;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 6px;
    border-radius: 3px;
    margin: 4px 0;
  }

  .pnj-ttd-space {
    width: 100%;
    height: 75px;
    min-height: 75px;
    display: block;
    margin: 4px 0;
  }

  .pnj-qr-placeholder {
    width: 75px;
    height: 75px;
    min-height: 75px;
    border: 1px dashed #64748b;
    background: #f8fafc;
    color: #334155;
    font-size: 7.5pt;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 4px;
    border-radius: 3px;
  }

  .pnj-qr-space {
    width: 75px;
    height: 75px;
    min-height: 75px;
    display: inline-block;
  }

  .pnj-tag-placeholder {
    color: #1d4ed8;
    background: #eff6ff;
    padding: 0 3px;
    border-radius: 2px;
    font-weight: 600;
  }

  .pnj-tembusan {
    margin-top: 18px;
    font-size: 10pt;
    line-height: 1.35;
    text-align: left;
  }

  .pnj-tembusan p {
    margin: 0 0 2px 0;
    text-align: left;
  }

  .pnj-tembusan ol {
    margin: 2px 0 0 0;
    padding-left: 20px;
  }

  .pnj-letter-footer {
    margin-top: 18px;
    padding-top: 8px;
    text-align: center;
    font-family: 'Calibri', 'Arial', 'Helvetica', sans-serif;
    font-size: 8pt;
    line-height: 1.35;
    color: #222222;
  }
</style>
<?php endif; ?>

<article class="pnj-a4-sheet" id="<?= htmlspecialchars($sheetId, ENT_QUOTES, 'UTF-8') ?>">

  <header class="pnj-letter-kop">
    <img class="pnj-letter-kop-logo" src="/Tandain/assets/img/logo-pnj-kop.png?v=3" alt="Logo Politeknik Negeri Jakarta" width="118" height="118">

    <div class="pnj-letter-kop-text">
      <div class="pnj-kop-ministry">KEMENTERIAN PENDIDIKAN TINGGI, SAINS,<br>DAN TEKNOLOGI</div>
      <div class="pnj-kop-institution">POLITEKNIK NEGERI JAKARTA</div>
      <div class="pnj-kop-meta">Jalan Prof. DR. G.A. Siwabessy, Kampus UI, Depok 16425</div>
      <div class="pnj-kop-meta">Telepon (021) 7270036, Hunting, Fax (021) 7270034</div>
      <div class="pnj-kop-meta">Laman: <u>http://www.pnj.ac.id</u>, Pos-el: <u>humas@pnj.ac.id</u></div>
    </div>
  </header>

  <div class="pnj-letter-body" id="<?= htmlspecialchars($bodyId, ENT_QUOTES, 'UTF-8') ?>"><?= $initialBodyHtml ?></div>

  <footer class="pnj-letter-footer">
    <div>Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik</div>
    <div>yang diterbitkan oleh Platform Tandain, Politeknik Negeri Jakarta (PNJ).</div>
  </footer>

</article>

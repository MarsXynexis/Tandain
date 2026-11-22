<?php

use setasign\Fpdi\FpdiException;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\Tcpdf\Fpdi;

class PDFStamper
{
    private const QR_SIZE_MM = 18.0;
    private const DUAL_QR_SIZE_MM = 16.0;
    private const DUAL_GAP_MM = 2.0;
    private const DUAL_BOX_WIDTH_MM = 40.0;
    private const DUAL_WIDTH_MM = 58.0;
    private const DUAL_HEIGHT_MM = 18.0;
    private const SIGNATURE_WIDTH_MM = 46.0;
    private const SIGNATURE_HEIGHT_MM = 18.0;
    private const VIEWER_SCALE = 1.3;

    public static function describePdfException(Throwable $exception): string
    {
        $message = 'Berkas PDF rusak atau tidak dapat diproses oleh mesin stempel.';

        if ($exception instanceof CrossReferenceException) {
            if ($exception->getCode() === CrossReferenceException::COMPRESSED_XREF) {
                $message = 'Format PDF ini menggunakan kompresi modern (PDF 1.5+) yang belum didukung mesin stempel. Simpan ulang PDF melalui Print to PDF atau gunakan Template Surat.';
            } elseif ($exception->getCode() === CrossReferenceException::ENCRYPTED) {
                $message = 'Berkas PDF terproteksi kata sandi atau terenkripsi tidak dapat diproses.';
            } else {
                $message = 'Struktur berkas PDF tidak didukung oleh mesin stempel.';
            }
        }

        return $message;
    }

    public static function validateSourcePdf(string $sourcePdfPath): string
    {
        $errorMessage = '';

        if (!is_file($sourcePdfPath) || !is_readable($sourcePdfPath)) {
            $errorMessage = 'Berkas PDF tidak dapat dibaca.';
        } else {
            try {
                $pdf = new Fpdi();
                $pageCount = $pdf->setSourceFile($sourcePdfPath);

                if ($pageCount < 1) {
                    $errorMessage = 'Berkas PDF tidak memiliki halaman yang valid.';
                }
            } catch (Throwable $exception) {
                $errorMessage = self::describePdfException($exception);
            }
        }

        return $errorMessage;
    }

    public static function stamp(
        string $sourcePdfPath,
        string $signatureAbsolutePath,
        string $documentId,
        int $signingOrder,
        string $stampType,
        int $pageNumber,
        float $coordX,
        float $coordY
    ): string {
        if (!is_file($sourcePdfPath) || !is_readable($sourcePdfPath)) {
            throw new RuntimeException('Berkas PDF sumber tidak tersedia atau tidak dapat dibaca.');
        }

        if (!is_file($signatureAbsolutePath) || !is_readable($signatureAbsolutePath)) {
            throw new RuntimeException('Berkas gambar tanda tangan tidak tersedia atau tidak dapat dibaca.');
        }

        $signatureInfo = @getimagesize($signatureAbsolutePath);

        if (!$signatureInfo || $signatureInfo[2] !== IMAGETYPE_PNG) {
            throw new RuntimeException('Berkas tanda tangan harus berupa gambar PNG yang valid.');
        }

        if (!in_array($stampType, ['DUAL_DISPLAY', 'QR_ONLY', 'SIGNATURE_ONLY'], true)) {
            throw new InvalidArgumentException('Jenis stempel tidak valid.');
        }

        if (
            $pageNumber < 1
            || $signingOrder < 1
            || !isValidUuid($documentId)
            || !is_finite($coordX)
            || !is_finite($coordY)
        ) {
            throw new InvalidArgumentException('Identitas penandatanganan, halaman, atau koordinat stempel tidak valid.');
        }

        $outputDirectory = 'uploads/signed';

        if (!is_dir($outputDirectory) && !mkdir($outputDirectory, 0775, true) && !is_dir($outputDirectory)) {
            throw new RuntimeException('Folder penyimpanan PDF bertanda tangan tidak dapat dibuat.');
        }

        $fileName = sprintf(
            '%s_%d_%s_%s.pdf',
            $documentId,
            $signingOrder,
            date('YmdHis'),
            bin2hex(random_bytes(4))
        );
        $outputPath = $outputDirectory . '/' . $fileName;

        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetCellPadding(0);

        try {
            $pageCount = $pdf->setSourceFile($sourcePdfPath);

            if ($pageNumber > $pageCount) {
                throw new InvalidArgumentException('Halaman penempatan stempel tidak tersedia pada PDF.');
            }

            for ($page = 1; $page <= $pageCount; $page++) {
                $templateId = $pdf->importPage($page);
                $pageSize = $pdf->getTemplateSize($templateId);

                $pdf->AddPage($pageSize['orientation'], [$pageSize['width'], $pageSize['height']]);
                $pdf->useTemplate($templateId);

                if ($page === $pageNumber) {
                    self::drawStamp(
                        $pdf,
                        $signatureAbsolutePath,
                        self::verificationUrl($documentId),
                        $stampType,
                        $coordX,
                        $coordY,
                        (float) $pageSize['width'],
                        (float) $pageSize['height']
                    );
                }
            }

            $pdf->Output(realpath($outputDirectory) . DIRECTORY_SEPARATOR . $fileName, 'F');

            if (!is_file($outputPath) || filesize($outputPath) === 0) {
                throw new RuntimeException('PDF hasil stempel gagal disimpan.');
            }
        } catch (Throwable $exception) {
            if (is_file($outputPath)) {
                @unlink($outputPath);
            }

            throw $exception;
        }

        return $outputPath;
    }

    private static function drawStamp(
        Fpdi $pdf,
        string $signaturePath,
        string $verificationUrl,
        string $stampType,
        float $coordX,
        float $coordY,
        float $pageWidth,
        float $pageHeight
    ): void {
        if ($stampType === 'DUAL_DISPLAY') {
            $stampWidth = self::DUAL_WIDTH_MM;
            $stampHeight = self::DUAL_HEIGHT_MM;
        } elseif ($stampType === 'QR_ONLY') {
            $stampWidth = self::QR_SIZE_MM;
            $stampHeight = self::QR_SIZE_MM;
        } else {
            $stampWidth = self::SIGNATURE_WIDTH_MM;
            $stampHeight = self::SIGNATURE_HEIGHT_MM;
        }

        if ($stampWidth > $pageWidth || $stampHeight > $pageHeight) {
            throw new RuntimeException('Halaman PDF terlalu kecil untuk memuat stempel.');
        }

        $x = self::coordinateToMillimeters($coordX, $pageWidth);
        $y = self::coordinateToMillimeters($coordY, $pageHeight);
        $x = max(0.0, min($x, $pageWidth - $stampWidth));
        $y = max(0.0, min($y, $pageHeight - $stampHeight));

        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect($x, $y, $stampWidth, $stampHeight, 'F');

        if ($stampType === 'QR_ONLY') {
            self::drawQrCode($pdf, $verificationUrl, $x, $y, self::QR_SIZE_MM);
        } elseif ($stampType === 'SIGNATURE_ONLY') {
            self::drawIpbBox(
                $pdf,
                $signaturePath,
                $x,
                $y,
                self::SIGNATURE_WIDTH_MM,
                self::SIGNATURE_HEIGHT_MM
            );
        } else {
            $qrY = $y + (self::DUAL_HEIGHT_MM - self::DUAL_QR_SIZE_MM) / 2.0;

            self::drawQrCode($pdf, $verificationUrl, $x, $qrY, self::DUAL_QR_SIZE_MM);

            self::drawIpbBox(
                $pdf,
                $signaturePath,
                $x + self::DUAL_QR_SIZE_MM + self::DUAL_GAP_MM,
                $y,
                self::DUAL_BOX_WIDTH_MM,
                self::DUAL_HEIGHT_MM
            );
        }
    }

    private static function drawIpbBox(
        Fpdi $pdf,
        string $signaturePath,
        float $boxX,
        float $boxY,
        float $boxWidth,
        float $boxHeight
    ): void {
        $topText = 'digitally signed';
        $bottomText = 'tandain.pnj.ac.id';
        $topLineY = $boxY + 1.4;
        $botLineY = $boxY + $boxHeight - 1.4;
        $leftSegment = 2.0;
        $textPadding = 0.7;

        $pdf->SetDrawColor(15, 23, 42);
        $pdf->SetLineWidth(0.25);

        $pdf->Line($boxX, $topLineY, $boxX, $botLineY);
        $pdf->Line($boxX + $boxWidth, $topLineY, $boxX + $boxWidth, $botLineY);

        $pdf->SetFont('courier', '', 6.0);
        $pdf->SetTextColor(15, 23, 42);
        $topTextWidth = $pdf->GetStringWidth($topText);
        $topTextX = $boxX + $leftSegment + $textPadding;
        $topRightStart = $topTextX + $topTextWidth + $textPadding;

        $pdf->Line($boxX, $topLineY, $boxX + $leftSegment, $topLineY);
        $pdf->Line($topRightStart, $topLineY, $boxX + $boxWidth, $topLineY);
        $pdf->SetXY($topTextX, $topLineY - 1.3);
        $pdf->Cell($topTextWidth, 2.6, $topText, 0, 0, 'L', false);

        $pdf->SetFont('courier', '', 5.8);
        $pdf->SetTextColor(71, 85, 105);
        $botTextWidth = $pdf->GetStringWidth($bottomText);
        $botTextX = $boxX + $leftSegment + $textPadding;
        $botRightStart = $botTextX + $botTextWidth + $textPadding;

        $pdf->Line($boxX, $botLineY, $boxX + $leftSegment, $botLineY);
        $pdf->Line($botRightStart, $botLineY, $boxX + $boxWidth, $botLineY);
        $pdf->SetXY($botTextX, $botLineY - 1.3);
        $pdf->Cell($botTextWidth, 2.6, $bottomText, 0, 0, 'L', false);

        self::drawSignatureImage(
            $pdf,
            $signaturePath,
            $boxX + 1.5,
            $topLineY + 1.4,
            $boxWidth - 3.0,
            ($botLineY - $topLineY) - 2.8
        );
    }

    private static function drawQrCode(Fpdi $pdf, string $url, float $x, float $y, float $size): void
    {
        $style = [
            'border' => false,
            'padding' => 0,
            'fgcolor' => [0, 0, 0],
            'bgcolor' => false,
        ];

        $pdf->write2DBarcode($url, 'QRCODE,M', $x, $y, $size, $size, $style, 'N');
    }

    private static function drawSignatureImage(
        Fpdi $pdf,
        string $signaturePath,
        float $x,
        float $y,
        float $maxWidth,
        float $maxHeight
    ): void {
        $imageInfo = getimagesize($signaturePath);

        if (!$imageInfo || $imageInfo[0] < 1 || $imageInfo[1] < 1) {
            throw new RuntimeException('Berkas gambar tanda tangan tidak valid.');
        }

        $imageRatio = $imageInfo[0] / $imageInfo[1];
        $width = min($maxWidth, $maxHeight * $imageRatio);
        $height = $width / $imageRatio;
        $imageX = $x + ($maxWidth - $width) / 2.0;
        $imageY = $y + ($maxHeight - $height) / 2.0;

        $pdf->Image($signaturePath, $imageX, $imageY, $width, $height, 'PNG');
    }

    private static function coordinateToMillimeters(float $coordinate, float $pageDimension): float
    {
        if ($coordinate <= 1.0) {
            $millimeters = $coordinate * $pageDimension;
        } else {
            $millimeters = $coordinate * 25.4 / (96.0 * self::VIEWER_SCALE);
        }

        return $millimeters;
    }

    private static function verificationUrl(string $documentId): string
    {
        $environmentPath = '.env';
        $environment = is_file($environmentPath) ? parse_ini_file($environmentPath) : [];

        if ($environment === false) {
            throw new RuntimeException('Konfigurasi aplikasi tidak dapat dibaca.');
        }

        $appUrl = trim((string) ($environment['APP_URL'] ?? ''));

        if ($appUrl === '') {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $appUrl = $scheme . '://' . $host . '/Tandain';
        }

        return rtrim($appUrl, '/') . '/verify/' . rawurlencode($documentId);
    }
}

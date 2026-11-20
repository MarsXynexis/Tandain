<?php

use setasign\Fpdi\Tcpdf\Fpdi;

class PDFStamper
{
    private const QR_SIZE_MM = 22.0;
    private const DUAL_WIDTH_MM = 68.0;
    private const DUAL_HEIGHT_MM = 22.0;
    private const SIGNATURE_WIDTH_MM = 45.0;
    private const SIGNATURE_HEIGHT_MM = 22.5;
    private const VIEWER_SCALE = 1.3;

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
            || !preg_match('/\A[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}\z/i', $documentId)
            || !is_finite($coordX)
            || !is_finite($coordY)
        ) {
            throw new InvalidArgumentException('Identitas penandatanganan, halaman, atau koordinat stempel tidak valid.');
        }

        $outputDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads'
            . DIRECTORY_SEPARATOR . 'signed';
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
        $outputPath = $outputDirectory . DIRECTORY_SEPARATOR . $fileName;

        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);

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

            $pdf->Output($outputPath, 'F');
            if (!is_file($outputPath) || filesize($outputPath) === 0) {
                throw new RuntimeException('PDF hasil stempel gagal disimpan.');
            }
        } catch (Throwable $exception) {
            if (is_file($outputPath)) {
                @unlink($outputPath);
            }

            throw $exception;
        }

        return 'uploads/signed/' . $fileName;
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

        if ($stampType === 'QR_ONLY') {
            self::drawQrCode($pdf, $verificationUrl, $x, $y);
            return;
        }

        if ($stampType === 'SIGNATURE_ONLY') {
            self::drawSignatureImage(
                $pdf,
                $signaturePath,
                $x,
                $y,
                self::SIGNATURE_WIDTH_MM,
                self::SIGNATURE_HEIGHT_MM
            );
            return;
        }

        self::drawQrCode($pdf, $verificationUrl, $x, $y);

        $boxX = $x + self::QR_SIZE_MM + 2.0;
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.25);
        $pdf->Rect($boxX, $y, 44.0, self::DUAL_HEIGHT_MM);

        self::drawSignatureImage($pdf, $signaturePath, $boxX + 2.0, $y + 1.0, 40.0, 14.0);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 6.5);
        $pdf->SetXY($boxX + 2.0, $y + 18.0);
        $pdf->Cell(40.0, 3.0, 'Digitally Signed | Tandain.pnj.ac.id', 0, 0, 'L');
    }

    private static function drawQrCode(Fpdi $pdf, string $url, float $x, float $y): void
    {
        $style = [
            'border' => false,
            'padding' => 0,
            'fgcolor' => [0, 0, 0],
            'bgcolor' => false,
        ];

        $pdf->write2DBarcode($url, 'QRCODE,M', $x, $y, self::QR_SIZE_MM, self::QR_SIZE_MM, $style, 'N');
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
            return $coordinate * $pageDimension;
        }

        return $coordinate * 25.4 / (96.0 * self::VIEWER_SCALE);
    }

    private static function verificationUrl(string $documentId): string
    {
        $environmentPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
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

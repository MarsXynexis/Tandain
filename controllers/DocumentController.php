<?php

class DocumentController extends Controller
{
    private const MAX_SIZE = 2097152;

    public function index()
{
    $this->authCheck();

    $documentModel = $this->model('Document');
    $userId = $_SESSION['user']['id'];

    $data['title'] = 'Dokumen Diunggah - Tandain';
    $data['active_menu'] = 'uploaded';
    $data['user'] = $_SESSION['user'];
    $data['documents'] = $documentModel->getAllUserDocuments($userId);

    $this->view('document/index', $data);

}
    public function create()
    {
        $this->authCheck();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
        } else {
            $userModel = $this->model('User');
            $templateModel = $this->model('DocumentTemplate');
            $documentModel = $this->model('Document');
            $currentUser = $userModel->findById($_SESSION['user']['id']);

            $data['title'] = 'Ajukan Dokumen - Tandain';
            $data['active_menu'] = 'apply';
            $data['user'] = $currentUser ? $currentUser : $_SESSION['user'];
            $data['can_sign'] = !empty($userModel->getAssignableSignersByIds([$_SESSION['user']['id']]));
            $data['templates'] = $templateModel->getActiveTemplates();
            $data['next_doc_seq'] = $documentModel->getNextDocumentSequence();

            $this->view('document/create', $data);
        }
    }

    private function store()
    {
        $submissionMode = trim($_POST['submission_mode'] ?? 'upload');
        $title = trim($_POST['title'] ?? '');
        $expiresAtInput = trim($_POST['expires_at'] ?? '');
        $expiresAt = $expiresAtInput !== '' ? $expiresAtInput : null;
        $isTriage = !empty($_POST['is_triage']);
        $sequential = !empty($_POST['is_sequential']);
        $rawSigners = is_array($_POST['signers'] ?? null) ? $_POST['signers'] : [];
        $signerIds = array_values(array_unique(array_filter(array_map('trim', array_filter($rawSigners, 'is_string')))));

        $errorMessage = '';
        $template = null;
        $templateSchema = [];
        $templateFields = [];
        $finalTitle = $title;

        if ($submissionMode !== 'upload' && $submissionMode !== 'template') {
            $errorMessage = 'Mode pengajuan dokumen tidak valid.';
        } elseif ($expiresAt !== null && !$this->isValidDate($expiresAt)) {
            $errorMessage = 'Batas kedaluwarsa dokumen tidak valid.';
        } elseif ($submissionMode === 'upload') {
            if ($title === '') {
                $errorMessage = 'Judul dokumen wajib diisi.';
            } elseif (mb_strlen($title) > 255) {
                $errorMessage = 'Judul dokumen maksimal terdiri dari 255 karakter.';
            }
        } else {
            $templateModel = $this->model('DocumentTemplate');
            $templateCheck = $this->validateTemplateSubmission($templateModel);

            if ($templateCheck['error'] !== '') {
                $errorMessage = $templateCheck['error'];
            } else {
                $template = $templateCheck['template'];
                $templateSchema = $templateCheck['schema'];
                $templateFields = $templateCheck['fields'];

                if ($finalTitle === '') {
                    $userFullName = trim($_SESSION['user']['full_name'] ?? '');

                    if ($userFullName === '') {
                        $userRow = $this->model('User')->findById($_SESSION['user']['id']);
                        $userFullName = trim($userRow['full_name'] ?? '');
                    }

                    $autoTitle = $userFullName !== '' ? $template['title'] . ' - ' . $userFullName : $template['title'];
                    $finalTitle = mb_substr($autoTitle, 0, 255);
                } elseif (mb_strlen($finalTitle) > 255) {
                    $errorMessage = 'Judul dokumen maksimal terdiri dari 255 karakter.';
                }
            }
        }

        if ($errorMessage === '' && !$isTriage) {
            if (!$signerIds) {
                $errorMessage = 'Pilih minimal satu penandatangan.';
            } elseif (count($this->model('User')->getAssignableSignersByIds($signerIds)) !== count($signerIds)) {
                $errorMessage = 'Pilih akun civitas aktif yang memiliki sertifikat TTE dan bukan Admin TU.';
            }
        }

        $file = $_FILES['document_file'] ?? null;

        if ($errorMessage === '') {
            $errorMessage = $this->validateUploadedPdf($file);
        }

        $redirectUrl = '/Tandain/document/create';

        if ($errorMessage === '') {
            $dir = dirname(__DIR__) . '/uploads/documents/';

            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $filename = bin2hex(random_bytes(16)) . '.pdf';

            if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) {
                $errorMessage = 'Gagal menyimpan berkas.';
            } else {
                $hash = hash_file('sha256', $dir . $filename);
                $documentModel = $this->model('Document');
                $userId = $_SESSION['user']['id'];
                $documentNumber = null;

                if ($submissionMode === 'template' && $template) {
                    $submittedNumber = trim($_POST['document_number'] ?? '');

                    if ($submittedNumber !== '' && empty($documentModel->isDocumentNumberTaken($submittedNumber))) {
                        $documentNumber = $submittedNumber;
                    } else {
                        $seq = $documentModel->getNextDocumentSequence();
                        $code = strtoupper(trim($template['template_code'] ?? 'SURAT'));
                        $documentNumber = $seq . '/PL3/' . $code . '/PNJ/' . date('Y');
                    }
                }

                $docData = [
                    'document_number' => $documentNumber,
                    'title' => $finalTitle,
                    'requester_id' => $userId,
                    'original_file_path' => 'uploads/documents/' . $filename,
                    'initial_sha256_hash' => $hash,
                    'status' => $isTriage ? 'PENDING_TRIAGE' : 'DRAFT',
                    'routing_type' => $isTriage ? 'TRIAGE_ASSISTED' : 'DIRECT',
                    'is_sequential' => $sequential,
                    'expires_at' => $expiresAt
                ];

                $docId = $documentModel->createDocument($docData, $isTriage ? [] : $signerIds);

                if (!$docId) {
                    @unlink($dir . $filename);

                    $errorMessage = 'Gagal menyimpan dokumen ke sistem.';
                } else {
                    if ($submissionMode === 'template' && $template) {
                        $auditModel = $this->model('DocumentAuditLog');
                        $auditNotes = $this->buildTemplateAuditNotes($template, $templateSchema, $templateFields);

                        $auditModel->createLog($docId, $userId, 'DOCUMENT_CREATED_FROM_TEMPLATE', $auditNotes);
                    }

                    if ($isTriage) {
                        $this->setFlash('success', 'Dokumen berhasil diajukan dan masuk antrean disposisi.');

                        $redirectUrl = '/Tandain/document';
                    } else {
                        $redirectUrl = '/Tandain/document/position/' . $docId;
                    }
                }
            }
        }

        if ($errorMessage !== '') {
            if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') {
                http_response_code(422);

                header('Content-Type: application/json; charset=utf-8');

                echo json_encode(['ok' => false, 'error' => $errorMessage]);
            } else {
                $this->setFlash('error', $errorMessage);

                $this->redirect('/Tandain/document/create');
            }
        } else {
            $this->redirect($redirectUrl);
        }
    }

    private function validateTemplateSubmission($templateModel)
    {
        $result = [
            'error' => '',
            'template' => null,
            'schema' => [],
            'fields' => []
        ];

        $templateId = trim($_POST['template_id'] ?? '');
        $template = $templateModel->getTemplateById($templateId);

        if (!$template) {
            $result['error'] = 'Template surat tidak ditemukan.';
        } else {
            $isActive = ($template['is_active'] === true || $template['is_active'] === 't' || $template['is_active'] === '1' || $template['is_active'] === 1);

            if (!$isActive) {
                $result['error'] = 'Template surat sedang dinonaktifkan.';
            } else {
                $rawFieldsJson = (string) ($_POST['template_fields_json'] ?? '');
                $submittedFields = trim($rawFieldsJson) !== '' ? json_decode($rawFieldsJson, true) : null;

                if (!is_array($submittedFields)) {
                    $result['error'] = 'Data isian template tidak valid.';
                } else {
                    $schema = json_decode($template['placeholders_schema'] ?? '[]', true);

                    if (!is_array($schema)) {
                        $schema = [];
                    }

                    $cleanFields = [];
                    $fieldError = '';

                    foreach ($schema as $field) {
                        $fieldName = trim((string) ($field['name'] ?? ''));

                        if ($fieldName !== '' && $fieldError === '') {
                            $fieldLabel = trim((string) ($field['label'] ?? $fieldName));
                            $fieldType = trim((string) ($field['type'] ?? 'text'));
                            $isRequired = !empty($field['required']);
                            $fieldValue = trim((string) ($submittedFields[$fieldName] ?? ''));

                            if ($isRequired && $fieldValue === '') {
                                $fieldError = 'Isian "' . $fieldLabel . '" wajib diisi.';
                            } elseif ($fieldType === 'number' && $fieldValue !== '' && !is_numeric($fieldValue)) {
                                $fieldError = 'Isian "' . $fieldLabel . '" harus berupa angka.';
                            } elseif ($fieldType === 'date' && $fieldValue !== '' && !$this->isValidDate($fieldValue)) {
                                $fieldError = 'Isian "' . $fieldLabel . '" harus berupa tanggal yang valid.';
                            } elseif ($fieldType === 'select' && $fieldValue !== '' && is_array($field['options'] ?? null) && count($field['options']) > 0) {
                                $allowedOptions = array_map('trim', $field['options']);

                                if (!in_array($fieldValue, $allowedOptions, true)) {
                                    $fieldError = 'Pilihan pada "' . $fieldLabel . '" tidak valid.';
                                }
                            }

                            $cleanFields[$fieldName] = $fieldValue;
                        }
                    }

                    if ($fieldError !== '') {
                        $result['error'] = $fieldError;
                    } else {
                        $result['template'] = $template;
                        $result['schema'] = $schema;
                        $result['fields'] = $cleanFields;
                    }
                }
            }
        }

        return $result;
    }

    private function validateUploadedPdf($file)
    {
        $errorMessage = '';

        if (!$file || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            $errorMessage = 'Berkas PDF wajib diunggah.';
        } elseif ($file['size'] <= 0 || $file['size'] > self::MAX_SIZE) {
            $errorMessage = 'Ukuran berkas maksimal 2 MB.';
        } else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

            if ($mime !== 'application/pdf') {
                $errorMessage = 'Berkas harus berformat PDF.';
            } else {
                $handle = fopen($file['tmp_name'], 'rb');
                $magicBytes = $handle ? fread($handle, 5) : '';

                if ($handle) {
                    fclose($handle);
                }

                if ($magicBytes !== '%PDF-') {
                    $errorMessage = 'Berkas harus berformat PDF yang valid.';
                } else {
                    $errorMessage = PDFStamper::validateSourcePdf($file['tmp_name']);
                }
            }
        }

        return $errorMessage;
    }

    private function buildTemplateAuditNotes($template, $schema, $fields)
    {
        $summaryParts = [];

        foreach ($schema as $field) {
            $fieldName = trim((string) ($field['name'] ?? ''));
            $fieldLabel = trim((string) ($field['label'] ?? $fieldName));
            $fieldValue = trim((string) ($fields[$fieldName] ?? ''));

            if ($fieldName !== '' && $fieldValue !== '') {
                $compactValue = preg_replace('/\s+/', ' ', $fieldValue);

                if (mb_strlen($compactValue) > 80) {
                    $compactValue = mb_substr($compactValue, 0, 77) . '...';
                }

                $summaryParts[] = $fieldLabel . ': ' . $compactValue;
            }
        }

        $summaryText = count($summaryParts) > 0 ? implode('; ', $summaryParts) : 'Tanpa isian tambahan';

        return 'Dokumen dibuat dari template ' . $template['template_code'] . ' (' . $template['title'] . '). Data: ' . $summaryText;
    }

    private function isValidDate($dateString)
    {
        $date = DateTime::createFromFormat('Y-m-d', $dateString);

        return $date && $date->format('Y-m-d') === $dateString;
    }

    public function position($id = null)
    {
        $this->authCheck();

        $userId = $_SESSION['user']['id'];
        $documentModel = $this->model('Document');
        $doc = $documentModel->getDocumentByIdAndUser($id, $userId);

        if (!$doc || $doc['status'] !== 'DRAFT') {
            $this->redirect('/Tandain/document');
        }

        $data['title'] = 'Atur Posisi TTE - Tandain';
        $data['active_menu'] = 'apply';
        $data['user'] = $_SESSION['user'];
        $data['documentId'] = $doc['id'];
        $data['signers'] = $documentModel->getSignersForPositioning($doc['id']);
        $data['pdfUrl'] = '/Tandain/document/file/' . $doc['id'];

        $this->view('document/position', $data);
    }

    public function file($id = null)
    {
        $this->authCheck();

        $userId = $_SESSION['user']['id'];
        $documentModel = $this->model('Document');
        $doc = $documentModel->getDocumentByIdAndUser($id, $userId);
        $version = $_GET['version'] ?? 'latest';
        $relativePath = '';

        if ($doc) {
            if ($version === 'original') {
                $relativePath = ltrim((string) ($doc['original_file_path'] ?? ''), '/');
            } elseif ($version === 'signed') {
                if ($doc['status'] === 'COMPLETED' && !empty($doc['final_file_path'])) {
                    $relativePath = ltrim((string) $doc['final_file_path'], '/');
                }
            } elseif (!empty($doc['final_file_path'])) {
                $relativePath = ltrim((string) $doc['final_file_path'], '/');
            } else {
                $relativePath = ltrim((string) ($doc['original_file_path'] ?? ''), '/');
            }
        }

        if (
            !$doc
            || !preg_match('/\Auploads\/(?:documents|signed)\/[A-Za-z0-9._-]+\.pdf\z/i', $relativePath)
            || !is_file($relativePath)
            || !is_readable($relativePath)
        ) {
            $this->redirect('/Tandain/document');
        } else {
            header('Content-Type: application/pdf');
            header('Content-Length: ' . filesize($relativePath));
            header('X-Content-Type-Options: nosniff');

            if (!empty($_GET['download'])) {
                $prefix = ($version === 'signed') ? 'signed-document-' : 'original-';
                header('Content-Disposition: attachment; filename="' . $prefix . rawurlencode($doc['id']) . '.pdf"');
            }

            readfile($relativePath);

            exit;
        }
    }

    public function delete($id = null)
    {
        $this->authCheck();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($id)) {
            $documentModel = $this->model('Document');
            $deletedFilePath = $documentModel->deleteDraft($id, $_SESSION['user']['id']);

            if ($deletedFilePath !== false) {
                $relativePath = ltrim((string) $deletedFilePath, '/');

                if (preg_match('/\Auploads\/documents\/[A-Za-z0-9._-]+\.pdf\z/i', $relativePath) && is_file($relativePath)) {
                    @unlink($relativePath);
                }

                $this->setFlash('success', 'Draf dokumen berhasil dihapus.');
            } else {
                $this->setFlash('error', 'Draf dokumen tidak ditemukan atau tidak dapat dihapus.');
            }
        }

        $this->redirect('/Tandain/document');
    }

    public function savePositions($id = null)
    {
        $this->authCheck();

        $userId = $_SESSION['user']['id'];
        $documentModel = $this->model('Document');
        $documentSignerModel = $this->model('DocumentSigner');
        $doc = $documentModel->getDocumentByIdAndUser($id, $userId);

        $redirectUrl = '/Tandain/document';

        if ($doc && $doc['status'] === 'DRAFT') {
            $redirectUrl = '/Tandain/document/position/' . $doc['id'];
            $positions = json_decode($_POST['positions'] ?? '[]', true);
            $isSequential = in_array($doc['is_sequential'], [true, 1, '1', 't', 'true'], true);

            if (!is_array($positions) || !$positions) {
                $this->setFlash('error', 'Tempatkan kotak TTE dulu.');
            } elseif (!$documentModel->updateSignerPositions($doc['id'], $positions, $isSequential, $userId, 'DRAFT')) {
                $this->setFlash('error', 'Gagal menyimpan posisi tanda tangan.');
            } else {
                $isUserCurrentTurn = false;

                foreach ($documentSignerModel->getSignerByDocumentId($doc['id']) as $signer) {
                    if (
                        ($signer['status'] ?? '') === 'CURRENT_TURN'
                        && (string) ($signer['signer_id'] ?? '') === (string) $userId
                    ) {
                        $isUserCurrentTurn = true;
                        break;
                    }
                }

                if ($isUserCurrentTurn) {
                    $redirectUrl = '/Tandain/inbox/detail?document_id=' . rawurlencode((string) $doc['id']);

                    $this->setFlash('success', 'Silakan tandatangani Dokumen Anda.');
                } else {
                    $this->setFlash('success', 'Dokumen berhasil diajukan kepada penandatangan.');

                    $redirectUrl = '/Tandain/document';
                }
            }
        }

        $this->redirect($redirectUrl);
    }

    public function detail()
    {
        $this->authCheck();

        $id = $_GET['id'] ?? '';
        $documentModel = $this->model('Document');
        $document = $documentModel->getDocumentByIdAndUser($id, $_SESSION['user']['id']);

        if (!$document) {
            $this->redirect('/Tandain/document');
        } else {
            $signers = $documentModel->getSignersByDocumentId($document['id']);
            $signedCount = 0;

            foreach ($signers as $signer) {
                if ($signer['status'] === 'SIGNED') {
                    $signedCount++;
                }
            }

            $totalSigners = count($signers);
            $percent = 0;

            if ($totalSigners > 0) {
                $percent = (int) (($signedCount / $totalSigners) * 100);
            }

            
            $data['title'] = 'Detail Dokumen - Tandain';
            $data['active_menu'] = 'uploaded';
            $data['user'] = $_SESSION['user'];
            $data['document'] = $document;
            $data['signers'] = $signers;
            $data['signedCount'] = $signedCount;
            $data['totalSigners'] = $totalSigners;
            $data['percent'] = $percent;

            $this->view('document/detail', $data);
        }
    }

    public function signed()
    {
        $this->authCheck();

        $this->redirect('/Tandain/inbox');
    }
}

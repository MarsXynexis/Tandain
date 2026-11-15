<?php

class TemplateController extends Controller
{
    public function index()
    {
        $this->adminCheck();

        $templateModel = $this->model('DocumentTemplate');

        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? 'all');

        if (!in_array($status, ['all', 'active', 'inactive'], true)) {
            $status = 'all';
        }

        $flash = $_SESSION['flash'] ?? null;

        unset($_SESSION['flash']);

        $data['title'] = 'Template Surat - Tandain';
        $data['active_menu'] = 'templates';
        $data['user'] = $_SESSION['user'];
        $data['templates'] = $templateModel->getAllTemplates($search, $status);
        $data['stats'] = $templateModel->getTemplateStatistics();
        $data['search'] = $search;
        $data['current_status'] = $status;
        $data['flash'] = $flash;

        $this->view('template/index', $data);
    }

    public function create()
    {
        $this->adminCheck();

        $flash = $_SESSION['flash'] ?? null;

        unset($_SESSION['flash']);

        $old = $_SESSION['template_old'] ?? null;

        unset($_SESSION['template_old']);

        $data['title'] = 'Buat Template Surat - Tandain';
        $data['active_menu'] = 'templates';
        $data['user'] = $_SESSION['user'];
        $data['flash'] = $flash;
        $data['old'] = $old;
        $data['schemaFields'] = is_array($old['schema_fields'] ?? null) ? $old['schema_fields'] : [];

        $this->view('template/create', $data);
    }

    public function store()
    {
        $this->adminCheck();

        $redirectUrl = '/Tandain/template';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $templateModel = $this->model('DocumentTemplate');
            $parsed = $this->parseAndValidateSubmission($templateModel);

            if ($parsed['error'] !== '') {
                $this->setFlash('error', $parsed['error']);

                $_SESSION['template_old'] = $parsed['old'];

                $redirectUrl = '/Tandain/template/create';
            } else {
                $payload = $parsed['payload'];
                $payload['created_by'] = $_SESSION['user']['id'];

                $createdId = $templateModel->createTemplate($payload);

                if ($createdId) {
                    $this->setFlash('success', 'Template "' . $payload['title'] . '" berhasil ditambahkan.');

                    $redirectUrl = '/Tandain/template';
                } else {
                    $this->setFlash('error', 'Gagal menyimpan template surat.');

                    $_SESSION['template_old'] = $parsed['old'];

                    $redirectUrl = '/Tandain/template/create';
                }
            }
        }

        $this->redirect($redirectUrl);
    }

    public function edit($id = null)
    {
        $this->adminCheck();

        $templateModel = $this->model('DocumentTemplate');
        $template = !empty($id) ? $templateModel->getTemplateById($id) : null;

        if (empty($template)) {
            $this->setFlash('error', 'Template surat tidak ditemukan.');

            $this->redirect('/Tandain/template');
        } else {
            $flash = $_SESSION['flash'] ?? null;

            unset($_SESSION['flash']);

            $old = $_SESSION['template_old'] ?? null;

            unset($_SESSION['template_old']);

            $decodedSchema = json_decode($template['placeholders_schema'] ?? '[]', true);
            $schemaFields = is_array($decodedSchema) ? $decodedSchema : [];

            if (is_array($old) && ($old['id'] ?? '') === $template['id']) {
                $template['title'] = $old['title'];
                $template['template_code'] = $old['template_code'];
                $template['description'] = $old['description'];
                $template['html_content'] = $old['html_content'];
                $template['is_active'] = $old['is_active'];
                $schemaFields = $old['schema_fields'];
            }

            $data['title'] = 'Ubah Template Surat - Tandain';
            $data['active_menu'] = 'templates';
            $data['user'] = $_SESSION['user'];
            $data['template'] = $template;
            $data['schemaFields'] = $schemaFields;
            $data['flash'] = $flash;

            $this->view('template/edit', $data);
        }
    }

    public function update($id = null)
    {
        $this->adminCheck();

        $redirectUrl = '/Tandain/template';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($id)) {
            $templateModel = $this->model('DocumentTemplate');
            $existing = $templateModel->getTemplateById($id);

            if (empty($existing)) {
                $this->setFlash('error', 'Template surat tidak ditemukan.');
            } else {
                $parsed = $this->parseAndValidateSubmission($templateModel, $id);

                if ($parsed['error'] !== '') {
                    $this->setFlash('error', $parsed['error']);

                    $oldData = $parsed['old'];
                    $oldData['id'] = $id;

                    $_SESSION['template_old'] = $oldData;

                    $redirectUrl = '/Tandain/template/edit/' . $id;
                } else {
                    $updated = $templateModel->updateTemplate($id, $parsed['payload']);

                    if ($updated) {
                        $this->setFlash('success', 'Template "' . $parsed['payload']['title'] . '" berhasil diperbarui.');

                        $redirectUrl = '/Tandain/template';
                    } else {
                        $this->setFlash('error', 'Gagal memperbarui template surat.');

                        $oldData = $parsed['old'];
                        $oldData['id'] = $id;

                        $_SESSION['template_old'] = $oldData;

                        $redirectUrl = '/Tandain/template/edit/' . $id;
                    }
                }
            }
        }

        $this->redirect($redirectUrl);
    }

    public function toggle($id = null)
    {
        $this->adminCheck();

        $redirectUrl = '/Tandain/template';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($id)) {
            $templateModel = $this->model('DocumentTemplate');
            $template = $templateModel->getTemplateById($id);

            if (empty($template)) {
                $this->setFlash('error', 'Template surat tidak ditemukan.');
            } else {
                $toggled = $templateModel->toggleStatus($id);

                if ($toggled) {
                    $isCurrentlyActive = ($template['is_active'] === true || $template['is_active'] === 't' || $template['is_active'] === '1' || $template['is_active'] === 1);
                    $statusLabel = $isCurrentlyActive ? 'dinonaktifkan' : 'diaktifkan';

                    $this->setFlash('success', 'Template "' . $template['title'] . '" berhasil ' . $statusLabel . '.');
                } else {
                    $this->setFlash('error', 'Gagal mengubah status template surat.');
                }
            }
        }

        $this->redirect($redirectUrl);
    }

    public function delete($id = null)
    {
        $this->adminCheck();

        $redirectUrl = '/Tandain/template';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($id)) {
            $templateModel = $this->model('DocumentTemplate');
            $template = $templateModel->getTemplateById($id);

            if (empty($template)) {
                $this->setFlash('error', 'Template surat tidak ditemukan.');
            } else {
                $deleted = $templateModel->deleteTemplate($id);

                if ($deleted) {
                    $this->setFlash('success', 'Template "' . $template['title'] . '" berhasil dihapus.');
                } else {
                    $this->setFlash('error', 'Gagal menghapus template surat.');
                }
            }
        }

        $this->redirect($redirectUrl);
    }

    private function parseAndValidateSubmission($templateModel, $excludeId = null)
    {
        $title = trim((string) ($_POST['title'] ?? ''));
        $templateCode = strtoupper(trim((string) ($_POST['template_code'] ?? '')));
        $description = trim((string) ($_POST['description'] ?? ''));
        $htmlContent = trim((string) ($_POST['html_content'] ?? ''));
        $isActive = !empty($_POST['is_active']) && $_POST['is_active'] !== '0';

        $schemaResult = $this->parseSchemaFieldsFromPost();
        $error = '';

        if ($title === '') {
            $error = 'Judul template surat wajib diisi.';
        } elseif (strlen($title) > 200) {
            $error = 'Judul template maksimal 200 karakter.';
        } elseif ($templateCode === '') {
            $error = 'Kode template wajib diisi.';
        } elseif (strlen($templateCode) > 50) {
            $error = 'Kode template maksimal 50 karakter.';
        } elseif (!preg_match('/^[A-Z0-9_-]+$/', $templateCode)) {
            $error = 'Kode template hanya boleh berisi huruf kapital, angka, tanda hubung (-), atau garis bawah (_).';
        } elseif ($templateModel->isCodeExists($templateCode, $excludeId)) {
            $error = 'Kode template "' . $templateCode . '" sudah digunakan.';
        } elseif ($htmlContent === '') {
            $error = 'Konten HTML surat wajib diisi.';
        } elseif (preg_match('/<script\b/i', $htmlContent)) {
            $error = 'Konten HTML surat tidak boleh mengandung tag script.';
        } elseif (!preg_match('/\{\{kolom_ttd_[1-9][0-9]*\}\}/', $htmlContent)) {
            $error = 'Konten HTML surat wajib memuat minimal satu slot tanda tangan seperti {{kolom_ttd_1}} atau {{kolom_ttd_2}}.';
        } elseif ($schemaResult['error'] !== '') {
            $error = $schemaResult['error'];
        }

        $schemaJson = json_encode($schemaResult['fields'], JSON_UNESCAPED_UNICODE);

        $result = [
            'error' => $error,
            'payload' => [
                'template_code' => $templateCode,
                'title' => $title,
                'description' => $description,
                'html_content' => $htmlContent,
                'placeholders_schema' => $schemaJson !== false ? $schemaJson : '[]',
                'is_active' => $isActive,
            ],
            'old' => [
                'template_code' => $templateCode,
                'title' => $title,
                'description' => $description,
                'html_content' => $htmlContent,
                'is_active' => $isActive,
                'schema_fields' => $schemaResult['fields'],
            ],
        ];

        return $result;
    }

    private function parseSchemaFieldsFromPost()
    {
        $fieldNames = is_array($_POST['field_name'] ?? null) ? array_values($_POST['field_name']) : [];
        $fieldLabels = is_array($_POST['field_label'] ?? null) ? array_values($_POST['field_label']) : [];
        $fieldTypes = is_array($_POST['field_type'] ?? null) ? array_values($_POST['field_type']) : [];
        $fieldRequired = is_array($_POST['field_required'] ?? null) ? array_values($_POST['field_required']) : [];
        $fieldPlaceholders = is_array($_POST['field_placeholder'] ?? null) ? array_values($_POST['field_placeholder']) : [];
        $fieldOptions = is_array($_POST['field_options'] ?? null) ? array_values($_POST['field_options']) : [];

        $rowCount = max(count($fieldNames), count($fieldLabels));
        $allowedTypes = ['text', 'number', 'date', 'textarea', 'select'];
        $schemaFields = [];
        $usedKeys = [];
        $error = '';

        for ($i = 0; $i < $rowCount; $i++) {
            $rawName = trim((string) ($fieldNames[$i] ?? ''));
            $label = trim((string) ($fieldLabels[$i] ?? ''));
            $type = trim((string) ($fieldTypes[$i] ?? 'text'));
            $requiredVal = (string) ($fieldRequired[$i] ?? '0');
            $placeholder = trim((string) ($fieldPlaceholders[$i] ?? ''));
            $optionsRaw = trim((string) ($fieldOptions[$i] ?? ''));

            $normalizedName = $this->normalizeSnakeCase($rawName);
            $optionsList = [];

            if ($type === 'select' && $optionsRaw !== '') {
                $rawParts = explode(',', $optionsRaw);

                foreach ($rawParts as $part) {
                    $trimmedPart = trim($part);

                    if ($trimmedPart !== '') {
                        $optionsList[] = $trimmedPart;
                    }
                }
            }

            if ($error === '') {
                if ($rawName === '' || $normalizedName === '') {
                    $error = 'Nama tag field dinamis ke-' . ($i + 1) . ' wajib diisi dengan karakter huruf atau angka.';
                } elseif ($label === '') {
                    $error = 'Label tampilan field "' . $normalizedName . '" wajib diisi.';
                } elseif (in_array($normalizedName, $usedKeys, true)) {
                    $error = 'Nama tag field "' . $normalizedName . '" tidak boleh duplikat.';
                } elseif (!in_array($type, $allowedTypes, true)) {
                    $error = 'Tipe field untuk "' . $normalizedName . '" tidak valid.';
                } elseif ($type === 'select' && empty($optionsList)) {
                    $error = 'Opsi pilihan wajib diisi untuk field select "' . $label . '".';
                }
            }

            $usedKeys[] = $normalizedName;

            $schemaFields[] = [
                'name' => $normalizedName,
                'label' => $label,
                'type' => in_array($type, $allowedTypes, true) ? $type : 'text',
                'required' => ($requiredVal === '1' || $requiredVal === 'true' || $requiredVal === 'on'),
                'placeholder' => $placeholder,
                'options' => $optionsList,
            ];
        }

        return [
            'error' => $error,
            'fields' => $schemaFields,
        ];
    }

    private function normalizeSnakeCase($rawName)
    {
        $camelSplit = preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', trim((string) $rawName));
        $lowered = strtolower((string) $camelSplit);
        $replaced = preg_replace('/[^a-z0-9_]+/', '_', $lowered);
        $collapsed = preg_replace('/_+/', '_', (string) $replaced);

        return trim((string) $collapsed, '_');
    }
}

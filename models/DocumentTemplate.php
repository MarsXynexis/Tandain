<?php

class DocumentTemplate
{
    private $table = 'document_templates';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAllTemplates($search = '', $status = 'all')
    {
        $sql = "SELECT t.*, u.full_name AS creator_name FROM " . $this->table . " t LEFT JOIN users u ON u.id = t.created_by WHERE 1=1";

        $cleanSearch = trim((string) $search);

        if ($status === 'active') {
            $sql .= " AND t.is_active = TRUE";
        } elseif ($status === 'inactive') {
            $sql .= " AND t.is_active = FALSE";
        }

        if ($cleanSearch !== '') {
            $sql .= " AND (t.title ILIKE :search OR t.template_code ILIKE :search OR COALESCE(t.description, '') ILIKE :search)";
        }

        $sql .= " ORDER BY t.updated_at DESC";

        $this->db->query($sql);

        if ($cleanSearch !== '') {
            $this->db->bind('search', '%' . $cleanSearch . '%');
        }

        return $this->db->resultSet();
    }

    public function getActiveTemplates()
    {
        $sql = "SELECT t.*, u.full_name AS creator_name FROM " . $this->table . " t LEFT JOIN users u ON u.id = t.created_by WHERE t.is_active = TRUE ORDER BY t.title ASC";

        $this->db->query($sql);

        return $this->db->resultSet();
    }

    public function getTemplateById($id)
    {
        $template = false;

        if (isValidUuid($id)) {
            $sql = "SELECT t.*, u.full_name AS creator_name FROM " . $this->table . " t LEFT JOIN users u ON u.id = t.created_by WHERE t.id = :id LIMIT 1";

            $this->db->query($sql);
            $this->db->bind('id', $id);

            $template = $this->db->single();
        }

        return $template;
    }

    public function getTemplateByCode($code)
    {
        $template = false;
        $cleanCode = strtoupper(trim((string) $code));

        if ($cleanCode !== '') {
            $sql = "SELECT t.*, u.full_name AS creator_name FROM " . $this->table . " t LEFT JOIN users u ON u.id = t.created_by WHERE UPPER(t.template_code) = :template_code LIMIT 1";

            $this->db->query($sql);
            $this->db->bind('template_code', $cleanCode);

            $template = $this->db->single();
        }

        return $template;
    }

    public function isCodeExists($code, $excludeId = null)
    {
        $exists = false;
        $cleanCode = strtoupper(trim((string) $code));

        if ($cleanCode !== '') {
            $sql = "SELECT id FROM " . $this->table . " WHERE UPPER(template_code) = :template_code";
            $hasExclude = !empty($excludeId) && isValidUuid($excludeId);

            if ($hasExclude) {
                $sql .= " AND id <> :exclude_id";
            }

            $sql .= " LIMIT 1";

            $this->db->query($sql);
            $this->db->bind('template_code', $cleanCode);

            if ($hasExclude) {
                $this->db->bind('exclude_id', $excludeId);
            }

            $row = $this->db->single();
            $exists = !empty($row);
        }

        return $exists;
    }

    public function createTemplate($data)
    {
        $schemaJson = is_string($data['placeholders_schema'] ?? null)
            ? $data['placeholders_schema']
            : json_encode($data['placeholders_schema'] ?? [], JSON_UNESCAPED_UNICODE);

        $isActive = !empty($data['is_active']) ? 'true' : 'false';

        $sql = "INSERT INTO " . $this->table . " (created_by, template_code, title, description, html_content, placeholders_schema, is_active) VALUES (:created_by, :template_code, :title, :description, :html_content, CAST(:placeholders_schema AS JSONB), :is_active) RETURNING id";

        $this->db->query($sql);
        $this->db->bind('created_by', $data['created_by']);
        $this->db->bind('template_code', strtoupper(trim($data['template_code'])));
        $this->db->bind('title', trim($data['title']));
        $this->db->bind('description', $data['description'] ?? '');
        $this->db->bind('html_content', $data['html_content']);
        $this->db->bind('placeholders_schema', $schemaJson);
        $this->db->bind('is_active', $isActive);

        $row = $this->db->single();
        $createdId = !empty($row['id']) ? $row['id'] : false;

        return $createdId;
    }

    public function updateTemplate($id, $data)
    {
        $updated = false;

        if (isValidUuid($id)) {
            $schemaJson = is_string($data['placeholders_schema'] ?? null)
                ? $data['placeholders_schema']
                : json_encode($data['placeholders_schema'] ?? [], JSON_UNESCAPED_UNICODE);

            $isActive = !empty($data['is_active']) ? 'true' : 'false';

            $sql = "UPDATE " . $this->table . " SET template_code = :template_code, title = :title, description = :description, html_content = :html_content, placeholders_schema = CAST(:placeholders_schema AS JSONB), is_active = :is_active, updated_at = CURRENT_TIMESTAMP WHERE id = :id";

            $this->db->query($sql);
            $this->db->bind('id', $id);
            $this->db->bind('template_code', strtoupper(trim($data['template_code'])));
            $this->db->bind('title', trim($data['title']));
            $this->db->bind('description', $data['description'] ?? '');
            $this->db->bind('html_content', $data['html_content']);
            $this->db->bind('placeholders_schema', $schemaJson);
            $this->db->bind('is_active', $isActive);

            $updated = $this->db->execute();
        }

        return $updated;
    }

    public function toggleStatus($id)
    {
        $result = false;
        $template = $this->getTemplateById($id);

        if ($template) {
            $isCurrentlyActive = ($template['is_active'] === true || $template['is_active'] === 't' || $template['is_active'] === '1' || $template['is_active'] === 1);
            $newStatus = $isCurrentlyActive ? 'false' : 'true';

            $this->db->query("UPDATE " . $this->table . " SET is_active = :is_active, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
            $this->db->bind('id', $id);
            $this->db->bind('is_active', $newStatus);

            $result = $this->db->execute();
        }

        return $result;
    }

    public function deleteTemplate($id)
    {
        $deleted = false;

        if (isValidUuid($id)) {
            $this->db->query("DELETE FROM " . $this->table . " WHERE id = :id");
            $this->db->bind('id', $id);

            $executed = $this->db->execute();
            $deleted = $executed && $this->db->rowCount() > 0;
        }

        return $deleted;
    }

    public function getTemplateStatistics()
    {
        $sql = "SELECT COUNT(*) AS total_templates, COUNT(CASE WHEN is_active = TRUE THEN 1 END) AS active_count, COUNT(CASE WHEN is_active = FALSE THEN 1 END) AS inactive_count FROM " . $this->table;

        $this->db->query($sql);

        return $this->db->single();
    }
}

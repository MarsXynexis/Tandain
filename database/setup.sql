CREATE DATABASE IF NOT EXISTS tandain
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE tandain;

CREATE TABLE roles (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    name VARCHAR(50) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_roles_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    role_id CHAR(36) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    identity_number VARCHAR(50) NOT NULL,
    position VARCHAR(100),
    email VARCHAR(150) NOT NULL,
    password_hash VARCHAR(255),
    signature_image_path VARCHAR(255),
    status ENUM('ACTIVE', 'SUSPENDED') NOT NULL DEFAULT 'ACTIVE',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_identity (identity_number),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_role_id (role_id),
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE signer_certificates (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    user_id CHAR(36) NOT NULL,
    certificate_serial VARCHAR(64) NOT NULL,
    public_key TEXT NOT NULL,
    encrypted_private_key TEXT NOT NULL,
    valid_from DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    valid_until DATETIME NOT NULL,
    status ENUM('ACTIVE', 'REVOKED', 'EXPIRED') NOT NULL DEFAULT 'ACTIVE',
    revoked_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cert_serial (certificate_serial),
    KEY idx_signer_certificates_user_id (user_id),
    CONSTRAINT fk_certificates_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE document_templates (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    created_by CHAR(36) NOT NULL,
    template_code VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    html_content LONGTEXT NOT NULL,
    placeholders_schema JSON,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_template_code (template_code),
    KEY idx_templates_created_by (created_by),
    CONSTRAINT fk_templates_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE documents (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    document_number VARCHAR(100),
    title VARCHAR(255) NOT NULL,
    requester_id CHAR(36) NOT NULL,
    triage_handled_by CHAR(36),
    original_file_path VARCHAR(255) NOT NULL,
    final_file_path VARCHAR(255),
    initial_sha256_hash VARCHAR(64) NOT NULL,
    final_sha256_hash VARCHAR(64),
    status ENUM('DRAFT', 'PENDING_TRIAGE', 'IN_PROGRESS', 'COMPLETED', 'REJECTED', 'CANCELLED') NOT NULL DEFAULT 'DRAFT',
    routing_type ENUM('DIRECT', 'SELF_SIGN', 'TRIAGE_ASSISTED') NOT NULL,
    is_sequential TINYINT(1) NOT NULL DEFAULT 1,
    rejection_reason TEXT,
    issued_at DATETIME,
    expires_at DATETIME,
    completed_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_document_number (document_number),
    KEY idx_documents_requester_id (requester_id),
    KEY idx_documents_status (status),
    KEY idx_documents_final_hash (final_sha256_hash),
    CONSTRAINT fk_documents_requester FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_documents_triage_user FOREIGN KEY (triage_handled_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE document_signers (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    document_id CHAR(36) NOT NULL,
    signer_id CHAR(36) NOT NULL,
    certificate_id CHAR(36),
    signing_order SMALLINT NOT NULL DEFAULT 1,
    status ENUM('WAITING', 'CURRENT_TURN', 'SIGNED', 'REJECTED', 'CANCELLED') NOT NULL DEFAULT 'WAITING',
    stamp_type ENUM('QR_ONLY', 'SIGNATURE_ONLY', 'DUAL_DISPLAY') NOT NULL DEFAULT 'DUAL_DISPLAY',
    page_number SMALLINT NOT NULL DEFAULT 1,
    coord_x DOUBLE NOT NULL DEFAULT 0,
    coord_y DOUBLE NOT NULL DEFAULT 0,
    parent_hash VARCHAR(64),
    signed_hash VARCHAR(64),
    signature_value TEXT,
    rejection_reason TEXT,
    signed_at DATETIME,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_document_signer (document_id, signer_id),
    KEY idx_document_signers_document_id (document_id),
    KEY idx_document_signers_signer_id (signer_id),
    CONSTRAINT fk_doc_signers_document FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE,
    CONSTRAINT fk_doc_signers_signer FOREIGN KEY (signer_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_doc_signers_certificate FOREIGN KEY (certificate_id) REFERENCES signer_certificates(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE document_audit_logs (
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    document_id CHAR(36) NOT NULL,
    user_id CHAR(36),
    action VARCHAR(50) NOT NULL,
    notes TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_audit_logs_document_id (document_id),
    CONSTRAINT fk_audit_logs_document FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE,
    CONSTRAINT fk_audit_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles (name, display_name) VALUES
('admin_tu', 'Administrator Tata Usaha'),
('pegawai', 'Dosen & Tenaga Kependidikan'),
('mahasiswa', 'Mahasiswa');

INSERT INTO users (role_id, full_name, identity_number, email, password_hash, status)
SELECT id, 'Administrator', 'ADMIN001', 'admin@pnj.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ACTIVE'
FROM roles WHERE name = 'admin_tu' LIMIT 1;

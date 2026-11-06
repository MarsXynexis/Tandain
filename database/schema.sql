CREATE EXTENSION IF NOT EXISTS "pgcrypto";

SET timezone = 'Asia/Jakarta';

CREATE TABLE roles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    role_id UUID NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    identity_number VARCHAR(50) NOT NULL UNIQUE,
    position VARCHAR(100),
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255),
    signature_image_path VARCHAR(255),
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    CONSTRAINT chk_users_status CHECK (status IN ('ACTIVE', 'SUSPENDED'))
);

CREATE TABLE signer_certificates (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL,
    certificate_serial VARCHAR(64) NOT NULL UNIQUE,
    public_key TEXT NOT NULL,
    encrypted_private_key TEXT NOT NULL,
    valid_from TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    valid_until TIMESTAMPTZ NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    revoked_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_certificates_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT chk_certificates_status CHECK (status IN ('ACTIVE', 'REVOKED', 'EXPIRED'))
);

CREATE TABLE document_templates (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    created_by UUID NOT NULL,
    template_code VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    html_content TEXT NOT NULL,
    placeholders_schema JSONB NOT NULL DEFAULT '[]'::jsonb,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_templates_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

CREATE TABLE documents (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    document_number VARCHAR(100) UNIQUE,
    title VARCHAR(255) NOT NULL,
    requester_id UUID NOT NULL,
    triage_handled_by UUID,
    original_file_path VARCHAR(255) NOT NULL,
    final_file_path VARCHAR(255),
    initial_sha256_hash VARCHAR(64) NOT NULL,
    final_sha256_hash VARCHAR(64),
    status VARCHAR(30) NOT NULL DEFAULT 'DRAFT',
    routing_type VARCHAR(30) NOT NULL,
    is_sequential BOOLEAN NOT NULL DEFAULT TRUE,
    rejection_reason TEXT,
    issued_at TIMESTAMPTZ,
    expires_at TIMESTAMPTZ,
    completed_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_documents_requester FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_documents_triage_user FOREIGN KEY (triage_handled_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT chk_documents_status CHECK (status IN ('DRAFT', 'PENDING_TRIAGE', 'IN_PROGRESS', 'COMPLETED', 'REJECTED', 'CANCELLED')),
    CONSTRAINT chk_documents_routing_type CHECK (routing_type IN ('DIRECT', 'SELF_SIGN', 'TRIAGE_ASSISTED'))
);

CREATE TABLE document_signers (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    document_id UUID NOT NULL,
    signer_id UUID NOT NULL,
    certificate_id UUID,
    signing_order SMALLINT NOT NULL DEFAULT 1,
    status VARCHAR(20) NOT NULL DEFAULT 'WAITING',
    stamp_type VARCHAR(20) NOT NULL DEFAULT 'DUAL_DISPLAY',
    page_number SMALLINT NOT NULL DEFAULT 1,
    coord_x DOUBLE PRECISION NOT NULL DEFAULT 0,
    coord_y DOUBLE PRECISION NOT NULL DEFAULT 0,
    parent_hash VARCHAR(64),
    signed_hash VARCHAR(64),
    signature_value TEXT,
    rejection_reason TEXT,
    signed_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_doc_signers_document FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE,
    CONSTRAINT fk_doc_signers_signer FOREIGN KEY (signer_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_doc_signers_certificate FOREIGN KEY (certificate_id) REFERENCES signer_certificates(id) ON DELETE RESTRICT,
    CONSTRAINT uq_document_signer UNIQUE (document_id, signer_id),
    CONSTRAINT chk_doc_signers_status CHECK (status IN ('WAITING', 'CURRENT_TURN', 'SIGNED', 'REJECTED', 'CANCELLED')),
    CONSTRAINT chk_doc_signers_stamp_type CHECK (stamp_type IN ('QR_ONLY', 'SIGNATURE_ONLY', 'DUAL_DISPLAY'))
);

CREATE TABLE document_audit_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    document_id UUID NOT NULL,
    user_id UUID,
    action VARCHAR(50) NOT NULL,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_logs_document FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE,
    CONSTRAINT fk_audit_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role_id ON users(role_id);

CREATE INDEX idx_signer_certificates_user_id ON signer_certificates(user_id);
CREATE UNIQUE INDEX idx_unique_active_cert_per_user ON signer_certificates(user_id) WHERE status = 'ACTIVE';

CREATE INDEX idx_documents_requester_id ON documents(requester_id);
CREATE INDEX idx_documents_status ON documents(status);
CREATE INDEX idx_documents_final_hash ON documents(final_sha256_hash);

CREATE INDEX idx_document_signers_document_id ON document_signers(document_id);
CREATE INDEX idx_document_signers_signer_id ON document_signers(signer_id);

CREATE INDEX idx_audit_logs_document_id ON document_audit_logs(document_id);

INSERT INTO roles (name, display_name) VALUES
('admin_tu', 'Administrator Tata Usaha'),
('pegawai', 'Dosen & Tenaga Kependidikan'),
('mahasiswa', 'Mahasiswa');

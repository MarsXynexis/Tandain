CREATE EXTENSION IF NOT EXISTS pgcrypto;

DROP TABLE IF EXISTS document_audit_logs CASCADE;
DROP TABLE IF EXISTS document_signers CASCADE;
DROP TABLE IF EXISTS documents CASCADE;
DROP TABLE IF EXISTS document_templates CASCADE;
DROP TABLE IF EXISTS signer_certificates CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS roles CASCADE;

CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    role_id INT NOT NULL REFERENCES roles(id) ON DELETE RESTRICT,
    full_name VARCHAR(150) NOT NULL,
    identity_number VARCHAR(50) NOT NULL UNIQUE,
    position VARCHAR(100),
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255),
    sso_google_id VARCHAR(100) UNIQUE,
    signature_image_path VARCHAR(255),
    initial_image_path VARCHAR(255),
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE' CHECK (status IN ('ACTIVE', 'SUSPENDED')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE signer_certificates (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    certificate_serial VARCHAR(64) NOT NULL UNIQUE,
    public_key TEXT NOT NULL,
    encrypted_private_key TEXT NOT NULL,
    passphrase_salt VARCHAR(64) NOT NULL,
    encryption_iv VARCHAR(32) NOT NULL,
    valid_from TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    valid_until TIMESTAMPTZ NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE' CHECK (status IN ('ACTIVE', 'REVOKED', 'EXPIRED')),
    revoked_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE document_templates (
    id SERIAL PRIMARY KEY,
    created_by_user_id INT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    template_code VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    html_content TEXT NOT NULL,
    placeholders_schema JSONB,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE documents (
    id BIGSERIAL PRIMARY KEY,
    document_uuid UUID NOT NULL UNIQUE DEFAULT gen_random_uuid(),
    document_number VARCHAR(100),
    title VARCHAR(255) NOT NULL,
    submission_type VARCHAR(20) NOT NULL CHECK (submission_type IN ('TEMPLATE', 'UPLOAD_PDF')),
    template_id INT REFERENCES document_templates(id) ON DELETE SET NULL,
    requester_user_id INT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    original_file_path VARCHAR(255) NOT NULL,
    final_file_path VARCHAR(255),
    final_sha256_hash VARCHAR(64),
    current_status VARCHAR(30) NOT NULL DEFAULT 'IN_PROGRESS' CHECK (current_status IN ('PENDING_TRIAGE', 'IN_PROGRESS', 'COMPLETED', 'REJECTED')),
    routing_type VARCHAR(30) NOT NULL DEFAULT 'DIRECT' CHECK (routing_type IN ('DIRECT', 'SELF_SIGN', 'TRIAGE_ASSISTED')),
    is_sequential BOOLEAN NOT NULL DEFAULT TRUE,
    issued_at DATE,
    expires_at DATE,
    completed_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE document_signers (
    id BIGSERIAL PRIMARY KEY,
    document_id BIGINT NOT NULL REFERENCES documents(id) ON DELETE CASCADE,
    signer_user_id INT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    signing_order SMALLINT NOT NULL DEFAULT 1,
    signer_status VARCHAR(20) NOT NULL DEFAULT 'WAITING' CHECK (signer_status IN ('WAITING', 'CURRENT_TURN', 'SIGNED', 'REJECTED')),
    stamp_type VARCHAR(20) NOT NULL DEFAULT 'DUAL_DISPLAY' CHECK (stamp_type IN ('QR_ONLY', 'SIGNATURE_ONLY', 'DUAL_DISPLAY')),
    page_number SMALLINT NOT NULL DEFAULT 1,
    coord_x DOUBLE PRECISION NOT NULL DEFAULT 0,
    coord_y DOUBLE PRECISION NOT NULL DEFAULT 0,
    signature_value TEXT,
    signed_hash VARCHAR(64),
    rejection_reason TEXT,
    signed_at TIMESTAMPTZ,
    signer_ip_address VARCHAR(45),
    user_agent VARCHAR(255)
);

CREATE TABLE document_audit_logs (
    id BIGSERIAL PRIMARY KEY,
    document_id BIGINT NOT NULL REFERENCES documents(id) ON DELETE CASCADE,
    actor_user_id INT REFERENCES users(id) ON DELETE SET NULL,
    action_event VARCHAR(50) NOT NULL,
    previous_state_hash VARCHAR(64),
    resulting_state_hash VARCHAR(64),
    details_json JSONB,
    ip_address VARCHAR(45) NOT NULL,
    recorded_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_identity ON users(identity_number);
CREATE INDEX idx_documents_uuid ON documents(document_uuid);
CREATE INDEX idx_documents_status ON documents(current_status);
CREATE INDEX idx_documents_requester ON documents(requester_user_id);
CREATE INDEX idx_document_signers_doc_order ON document_signers(document_id, signing_order);
CREATE INDEX idx_document_signers_user ON document_signers(signer_user_id);
CREATE INDEX idx_audit_logs_doc ON document_audit_logs(document_id);
CREATE INDEX idx_audit_logs_jsonb ON document_audit_logs USING GIN (details_json);

INSERT INTO roles (name, display_name, description) VALUES
('admin_tu', 'Administrator Tata Usaha', 'Pengelola persuratan, master template, dan meja disposisi triage'),
('pegawai', 'Tenaga Pendidik & Kependidikan', 'Dosen dan staf pejabat berwenang menandatangani surat dinas/akademik'),
('mahasiswa', 'Mahasiswa', 'Peserta didik dan pengurus organisasi kemahasiswaan');

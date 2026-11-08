-- =============================================================================
-- SETUP DATABASE POSTGRESQL "TANDAIN" (PNJ TIK)
-- DDL: 7 Tabel Utama | DML Seeder: Roles & Users Saja
-- Timezone: Asia/Jakarta (UTC+7) | Password Default Akun: password
-- =============================================================================

BEGIN;

-- -----------------------------------------------------------------------------
-- 0. EKSTENSI & PEMBERSIHAN TABEL (CLEAN SLATE)
-- -----------------------------------------------------------------------------
CREATE EXTENSION IF NOT EXISTS pgcrypto;

DROP TABLE IF EXISTS document_audit_logs CASCADE;
DROP TABLE IF EXISTS document_signers CASCADE;
DROP TABLE IF EXISTS documents CASCADE;
DROP TABLE IF EXISTS document_templates CASCADE;
DROP TABLE IF EXISTS signer_certificates CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS roles CASCADE;

-- Trigger pembaruan otomatis kolom updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

-- -----------------------------------------------------------------------------
-- 1. STRUKTUR TABEL BASIS DATA (DDL)
-- -----------------------------------------------------------------------------

-- 1.1 Tabel: roles
CREATE TABLE roles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 1.2 Tabel: users
CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    role_id UUID NOT NULL REFERENCES roles(id) ON DELETE RESTRICT,
    full_name VARCHAR(150) NOT NULL,
    identity_number VARCHAR(50) NOT NULL UNIQUE,
    position VARCHAR(100),
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255),
    signature_image_path VARCHAR(255),
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE' CHECK (status IN ('ACTIVE', 'SUSPENDED')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_role_id ON users(role_id);
CREATE TRIGGER trg_users_updated_at BEFORE UPDATE ON users FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- 1.3 Tabel: signer_certificates
CREATE TABLE signer_certificates (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    certificate_serial VARCHAR(64) NOT NULL UNIQUE,
    public_key TEXT NOT NULL,
    encrypted_private_key TEXT NOT NULL,
    valid_from TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    valid_until TIMESTAMPTZ NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE' CHECK (status IN ('ACTIVE', 'REVOKED', 'EXPIRED')),
    revoked_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_signer_certificates_user_id ON signer_certificates(user_id);

-- 1.4 Tabel: document_templates
CREATE TABLE document_templates (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    created_by UUID NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    template_code VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    html_content TEXT NOT NULL,
    placeholders_schema JSONB,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_templates_created_by ON document_templates(created_by);
CREATE TRIGGER trg_document_templates_updated_at BEFORE UPDATE ON document_templates FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- 1.5 Tabel: documents
CREATE TABLE documents (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    document_number VARCHAR(100) UNIQUE,
    title VARCHAR(255) NOT NULL,
    requester_id UUID NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    triage_handled_by UUID REFERENCES users(id) ON DELETE SET NULL,
    original_file_path VARCHAR(255) NOT NULL,
    final_file_path VARCHAR(255),
    initial_sha256_hash VARCHAR(64) NOT NULL,
    final_sha256_hash VARCHAR(64),
    status VARCHAR(30) NOT NULL DEFAULT 'DRAFT' CHECK (status IN ('DRAFT', 'PENDING_TRIAGE', 'IN_PROGRESS', 'COMPLETED', 'REJECTED', 'CANCELLED')),
    routing_type VARCHAR(30) NOT NULL CHECK (routing_type IN ('DIRECT', 'SELF_SIGN', 'TRIAGE_ASSISTED')),
    is_sequential BOOLEAN NOT NULL DEFAULT TRUE,
    rejection_reason TEXT,
    issued_at TIMESTAMPTZ,
    expires_at TIMESTAMPTZ,
    completed_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_documents_requester_id ON documents(requester_id);
CREATE INDEX idx_documents_status ON documents(status);
CREATE INDEX idx_documents_final_hash ON documents(final_sha256_hash);
CREATE TRIGGER trg_documents_updated_at BEFORE UPDATE ON documents FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- 1.6 Tabel: document_signers
CREATE TABLE document_signers (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    document_id UUID NOT NULL REFERENCES documents(id) ON DELETE CASCADE,
    signer_id UUID NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    certificate_id UUID REFERENCES signer_certificates(id) ON DELETE RESTRICT,
    signing_order SMALLINT NOT NULL DEFAULT 1,
    status VARCHAR(20) NOT NULL DEFAULT 'WAITING' CHECK (status IN ('WAITING', 'CURRENT_TURN', 'SIGNED', 'REJECTED', 'CANCELLED')),
    stamp_type VARCHAR(20) NOT NULL DEFAULT 'DUAL_DISPLAY' CHECK (stamp_type IN ('QR_ONLY', 'SIGNATURE_ONLY', 'DUAL_DISPLAY')),
    page_number SMALLINT NOT NULL DEFAULT 1,
    coord_x DOUBLE PRECISION NOT NULL DEFAULT 0,
    coord_y DOUBLE PRECISION NOT NULL DEFAULT 0,
    parent_hash VARCHAR(64),
    signed_hash VARCHAR(64),
    signature_value TEXT,
    rejection_reason TEXT,
    signed_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_document_signer UNIQUE (document_id, signer_id)
);

CREATE INDEX idx_document_signers_document_id ON document_signers(document_id);
CREATE INDEX idx_document_signers_signer_id ON document_signers(signer_id);

-- 1.7 Tabel: document_audit_logs
CREATE TABLE document_audit_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    document_id UUID NOT NULL REFERENCES documents(id) ON DELETE CASCADE,
    user_id UUID REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(50) NOT NULL,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_audit_logs_document_id ON document_audit_logs(document_id);

-- -----------------------------------------------------------------------------
-- 2. SEEDER (DML): ROLES & USERS SAJA
-- -----------------------------------------------------------------------------

-- 2.1 Seed Data: roles
INSERT INTO roles (id, name, display_name, created_at) VALUES
('11111111-1111-4111-a111-111111111111', 'admin_tu', 'Administrator Tata Usaha', NOW()),
('22222222-2222-4222-a222-222222222222', 'pegawai', 'Dosen & Tenaga Kependidikan', NOW()),
('33333333-3333-4333-a333-333333333333', 'mahasiswa', 'Mahasiswa', NOW());

-- 2.2 Seed Data: users (Password Hash = password)
INSERT INTO users (id, role_id, full_name, identity_number, position, email, password_hash, signature_image_path, status, created_at, updated_at) VALUES
('a0000000-0000-4000-a000-000000000001', '11111111-1111-4111-a111-111111111111', 'Staf Administrasi Akademik TIK', 'ADMIN001', 'Administrator Tata Usaha TIK', 'admin@pnj.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'uploads/signatures/sign_admin_tu.png', 'ACTIVE', NOW(), NOW()),
('a0000000-0000-4000-a000-000000000002', '22222222-2222-4222-a222-222222222222', 'Ir. Iwan Sonjaya, M.T.', '196803121994031002', 'Dosen Senior & Pengarah Arsitektur', 'iwan.sonjaya@tik.pnj.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'uploads/signatures/sign_iwan_sonjaya.png', 'ACTIVE', NOW(), NOW()),
('a0000000-0000-4000-a000-000000000003', '22222222-2222-4222-a222-222222222222', 'Anita Hidayati, S.Kom., M.Kom.', '197908242008122001', 'Dosen Senior & Koorprodi TI', 'anita.hidayati@tik.pnj.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'uploads/signatures/sign_anita_hidayati.png', 'ACTIVE', NOW(), NOW()),
('a0000000-0000-4000-a000-000000000004', '33333333-3333-4333-a333-333333333333', 'M. Abdul Raziq Siddiq', '2507411052', 'Mahasiswa Prodi Teknik Informatika (TI-2C)', 'm.abdulraziqsiddiq.tik25@mhsw.pnj.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'uploads/signatures/sign_raziq_siddiq.png', 'ACTIVE', NOW(), NOW()),
('a0000000-0000-4000-a000-000000000005', '33333333-3333-4333-a333-333333333333', 'Farhan Hariri', '2507411050', 'Mahasiswa / Ketua Himpunan Mahasiswa TIK', 'farhan.hariri.tik25@mhsw.pnj.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'uploads/signatures/sign_farhan_hariri.png', 'ACTIVE', NOW(), NOW());

COMMIT;

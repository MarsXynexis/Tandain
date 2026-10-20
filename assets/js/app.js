document.addEventListener('DOMContentLoaded', () => {
  initPersonaManager();
  initSidebarToggle();
  initDynamicSigners();
  initSigningFlow();
  initTableSearch();
});

const PERSONAS = {
  mahasiswa: {
    name: "Muhammad Farhan Pratama",
    initials: "FP",
    role: "Mahasiswa (TI-3A)",
    idNumber: "NIM: 2207421034",
    email: "farhan.pratama.tik22@mhsw.pnj.ac.id",
    certSerial: "104C0BA6F5CF110F"
  },
  dosen: {
    name: "Dr. Farida Laila, S.Si., M.Si.",
    initials: "FL",
    role: "Dosen / Kaprodi",
    idNumber: "NIDN: 0003117611",
    email: "farida.laila@tik.pnj.ac.id",
    certSerial: "7B2F8912A34E091C"
  },
  tu: {
    name: "Anita Hidayati, S.Kom., M.Kom.",
    initials: "AH",
    role: "Admin Tata Usaha",
    idNumber: "NIP: 198205122008122001",
    email: "tu.tik@pnj.ac.id",
    certSerial: "99A1C40D81E733B2"
  }
};

function getActivePersona() {
  const saved = localStorage.getItem('tandain_persona') || 'mahasiswa';
  return PERSONAS[saved] || PERSONAS.mahasiswa;
}

function setActivePersona(roleKey) {
  if (PERSONAS[roleKey]) {
    localStorage.setItem('tandain_persona', roleKey);
    applyPersonaToUI(PERSONAS[roleKey]);
    showToast('Peran Diperbarui', `Beralih profil ke: ${PERSONAS[roleKey].name} (${PERSONAS[roleKey].role})`, 'success');
  }
}

function initPersonaManager() {
  applyPersonaToUI(getActivePersona());

  document.querySelectorAll('.js-switch-role').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const role = link.getAttribute('data-role');
      setActivePersona(role);
      setTimeout(() => location.reload(), 300);
    });
  });
}

function applyPersonaToUI(p) {
  document.querySelectorAll('.js-user-name').forEach(el => el.textContent = p.name);
  document.querySelectorAll('.js-user-role').forEach(el => el.textContent = p.role);
  document.querySelectorAll('.js-user-initials').forEach(el => el.textContent = p.initials);
  document.querySelectorAll('.js-user-id').forEach(el => el.textContent = p.idNumber);
  document.querySelectorAll('.js-user-cert').forEach(el => el.textContent = p.certSerial);
}

function initSidebarToggle() {
  const toggleBtn = document.getElementById('btnSidebarToggle');
  const sidebar = document.getElementById('appSidebar');

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
      if (window.innerWidth <= 992 && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
        sidebar.classList.remove('show');
      }
    });
  }
}

const PNJ_CIVITAS = [
  { email: "farida.laila@tik.pnj.ac.id", name: "Dr. Farida Laila, S.Si., M.Si.", role: "Ketua Program Studi TI", initials: "FL", badge: "Kaprodi" },
  { email: "prihatin.oktivasari@tik.pnj.ac.id", name: "Dr. Prihatin Oktivasari, S.Si., M.Si.", role: "Ketua Jurusan TIK PNJ", initials: "PO", badge: "Kajur" },
  { email: "iwan.sonjaya@tik.pnj.ac.id", name: "Ir. Iwan Sonjaya, M.T.", role: "Dosen Pembimbing / Lab Jaringan", initials: "IS", badge: "Dosen" },
  { email: "anita.hidayati@tik.pnj.ac.id", name: "Anita Hidayati, S.Kom., M.Kom.", role: "Koordinator TA / Pembina Ormawa", initials: "AH", badge: "Dosen" },
  { email: "tu.tik@pnj.ac.id", name: "Admin Tata Usaha TIK", role: "Pelayanan Surat & Disposisi", initials: "TU", badge: "Admin TU" },
  { email: "farhan.pratama.tik@pnj.ac.id", name: "Muhammad Farhan Pratama", role: "Mahasiswa D4 TI (TI-3A)", initials: "FP", badge: "Mahasiswa" },
  { email: "dimas.arya.tik@pnj.ac.id", name: "Dimas Arya Pratama", role: "Mahasiswa D4 TI (TI-3A)", initials: "DA", badge: "Mahasiswa" },
  { email: "farras.hakim.tik@pnj.ac.id", name: "Farras Rahman Hakim", role: "Ketua HMD TIK (TI-4B)", initials: "FH", badge: "Mahasiswa" },
  { email: "siti.fathiyyah.tik@pnj.ac.id", name: "Siti Fathiyyah Musiyam", role: "Mahasiswa D4 TI (TI-3A)", initials: "SF", badge: "Mahasiswa" },
  { email: "ahmad.fauzi.tik@pnj.ac.id", name: "Ahmad Fauzi", role: "Mahasiswa D4 TI (TI-4A)", initials: "AF", badge: "Mahasiswa" },
  { email: "nadia.larasati.tik@pnj.ac.id", name: "Nadia Larasati", role: "Ketua Panitia HMD TIK", initials: "NL", badge: "Mahasiswa" }
];

function createSignerRowElement(stepNum, user, pageVal) {
  const row = document.createElement('div');
  row.className = 'signer-row p-3 bg-light rounded-3 border';
  row.setAttribute('data-step', stepNum);
  row.innerHTML = `
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-primary text-white rounded-circle p-2 js-step-num flex-shrink-0" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">${stepNum}</span>

      <div class="signer-search-wrapper flex-grow-1 position-relative js-signer-search-box ${user ? 'd-none' : ''}">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-envelope"></i></span>
          <input type="text" class="form-control border-start-0 border-end-0 js-signer-email-input" placeholder="Cari email atau nama (misal: farida.laila@tik.pnj.ac.id)..." value="${user ? user.email : ''}" autocomplete="off">
          <button type="button" class="btn btn-primary js-signer-search-btn px-3">
            <i class="bi bi-search me-1"></i><span>Cari</span>
          </button>
        </div>
        <div class="signer-search-results d-none js-signer-results"></div>
      </div>

      <div class="signer-confirmed-card flex-grow-1 d-flex align-items-center justify-content-between p-2 px-3 bg-white rounded-3 js-signer-confirmed-box ${user ? '' : 'd-none'}">
        <div class="d-flex align-items-center gap-2 min-w-0">
          <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0 js-confirmed-avatar" style="width: 32px; height: 32px; font-size: 0.75rem;">${user ? user.initials : ''}</div>
          <div class="min-w-0">
            <div class="fw-bold text-dark text-truncate small js-confirmed-name">${user ? user.name : ''}</div>
            <div class="text-secondary text-truncate" style="font-size: 0.725rem;">
              <span class="font-monospace text-primary js-confirmed-email">${user ? user.email : ''}</span> &bull; <span class="js-confirmed-role">${user ? user.role : ''}</span>
            </div>
          </div>
        </div>
        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0 ms-2 js-signer-change-btn" style="font-size: 0.725rem;" title="Ganti target">
          <i class="bi bi-pencil me-1"></i>Ganti
        </button>
        <input type="hidden" name="signer_email[]" class="js-hidden-email" value="${user ? user.email : ''}">
        <input type="hidden" name="signer_name[]" class="js-hidden-name" value="${user ? user.name : ''}">
      </div>

      <div style="width: 80px;" class="flex-shrink-0">
        <input type="number" class="form-control form-control-sm text-center" name="signer_page[]" value="${pageVal || 1}" min="1" max="50" placeholder="Hal." title="Halaman tanda tangan">
      </div>

      <button type="button" class="btn btn-sm btn-outline-danger border-0 js-remove-signer flex-shrink-0" title="Hapus">
        <i class="bi bi-trash"></i>
      </button>
    </div>
  `;
  return row;
}

function executeSignerSearch(row) {
  const input = row.querySelector('.js-signer-email-input');
  const resultsBox = row.querySelector('.js-signer-results');
  const searchBtn = row.querySelector('.js-signer-search-btn');
  if (!input || !resultsBox) return;

  const query = input.value.trim().toLowerCase();
  if (!query) {
    resultsBox.classList.add('d-none');
    resultsBox.innerHTML = '';
    return;
  }

  if (searchBtn) {
    const origHtml = searchBtn.innerHTML;
    searchBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    setTimeout(() => { searchBtn.innerHTML = origHtml; }, 250);
  }

  const matches = PNJ_CIVITAS.filter(u => 
    u.email.toLowerCase().includes(query) ||
    u.name.toLowerCase().includes(query) ||
    u.role.toLowerCase().includes(query)
  );

  resultsBox.innerHTML = '';
  if (matches.length === 0) {
    resultsBox.innerHTML = `
      <div class="p-3 text-center text-secondary small">
        <i class="bi bi-person-x text-warning me-1"></i>
        <span>Tidak ditemukan civitas dengan email atau nama "${query}".</span>
      </div>
    `;
  } else {
    matches.forEach(u => {
      const item = document.createElement('div');
      item.className = 'signer-search-item d-flex align-items-center justify-content-between p-2';
      item.innerHTML = `
        <div class="d-flex align-items-center gap-2 min-w-0">
          <div class="rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.7rem;">${u.initials}</div>
          <div class="min-w-0">
            <div class="fw-bold text-dark small text-truncate">${u.name}</div>
            <div class="text-secondary text-truncate" style="font-size: 0.72rem;">
              <span class="font-monospace text-primary">${u.email}</span> &bull; ${u.role}
            </div>
          </div>
        </div>
        <span class="badge bg-light text-secondary border flex-shrink-0 ms-2" style="font-size: 0.7rem;">${u.badge}</span>
      `;
      item.addEventListener('click', () => {
        confirmSignerSelection(row, u);
      });
      resultsBox.appendChild(item);
    });
  }

  resultsBox.classList.remove('d-none');
}

function confirmSignerSelection(row, u) {
  const searchBox = row.querySelector('.js-signer-search-box');
  const resultsBox = row.querySelector('.js-signer-results');
  const confirmedBox = row.querySelector('.js-signer-confirmed-box');
  const hiddenEmail = row.querySelector('.js-hidden-email');
  const hiddenName = row.querySelector('.js-hidden-name');
  const avatar = row.querySelector('.js-confirmed-avatar');
  const nameEl = row.querySelector('.js-confirmed-name');
  const emailEl = row.querySelector('.js-confirmed-email');
  const roleEl = row.querySelector('.js-confirmed-role');

  if (avatar) avatar.textContent = u.initials;
  if (nameEl) nameEl.textContent = u.name;
  if (emailEl) emailEl.textContent = u.email;
  if (roleEl) roleEl.textContent = u.role;
  if (hiddenEmail) hiddenEmail.value = u.email;
  if (hiddenName) hiddenName.value = u.name;

  if (resultsBox) {
    resultsBox.classList.add('d-none');
    resultsBox.innerHTML = '';
  }
  if (searchBox) searchBox.classList.add('d-none');
  if (confirmedBox) confirmedBox.classList.remove('d-none');

  const stepBadge = row.querySelector('.js-step-num');
  const step = stepBadge ? stepBadge.textContent : '1';
  if (typeof showToast === 'function') {
    showToast('Penandatangan Dipilih', `${u.name} ditetapkan pada urutan ke-${step}.`, 'success');
  }
}

function attachSignerRowEvents(row) {
  const input = row.querySelector('.js-signer-email-input');
  const searchBtn = row.querySelector('.js-signer-search-btn');
  const changeBtn = row.querySelector('.js-signer-change-btn');
  let searchTimeout = null;

  if (input) {
    input.addEventListener('input', () => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        executeSignerSearch(row);
      }, 400);
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        clearTimeout(searchTimeout);
        executeSignerSearch(row);
      }
    });
  }

  if (searchBtn) {
    searchBtn.addEventListener('click', (e) => {
      e.preventDefault();
      clearTimeout(searchTimeout);
      executeSignerSearch(row);
    });
  }

  if (changeBtn) {
    changeBtn.addEventListener('click', () => {
      const confirmedBox = row.querySelector('.js-signer-confirmed-box');
      const searchBox = row.querySelector('.js-signer-search-box');
      const hiddenEmail = row.querySelector('.js-hidden-email');
      if (confirmedBox) confirmedBox.classList.add('d-none');
      if (searchBox) {
        searchBox.classList.remove('d-none');
        if (input) {
          if (hiddenEmail && hiddenEmail.value) input.value = hiddenEmail.value;
          input.focus();
        }
      }
    });
  }
}

function initDynamicSigners() {
  const container = document.getElementById('signerSequenceContainer');
  const btnAdd = document.getElementById('btnAddSignerRow');

  if (!container) return;

  container.querySelectorAll('.signer-row').forEach(row => {
    attachSignerRowEvents(row);
  });
  renumberSigners();

  if (btnAdd) {
    btnAdd.addEventListener('click', () => {
      const rowCount = container.querySelectorAll('.signer-row').length + 1;
      const newRow = createSignerRowElement(rowCount, null, 1);
      container.appendChild(newRow);
      attachSignerRowEvents(newRow);
      renumberSigners();
      const input = newRow.querySelector('.js-signer-email-input');
      if (input) input.focus();
      if (typeof showToast === 'function') {
        showToast('Penandatangan Ditambahkan', `Urutan ke-${rowCount} siap dicari via email.`, 'info');
      }
    });
  }

  container.addEventListener('click', (e) => {
    const removeBtn = e.target.closest('.js-remove-signer');
    if (removeBtn) {
      const rows = container.querySelectorAll('.signer-row');
      if (rows.length <= 1) {
        if (typeof showToast === 'function') {
          showToast('Perhatian', 'Minimal terdapat 1 orang penandatangan.', 'warning');
        }
        return;
      }
      removeBtn.closest('.signer-row').remove();
      renumberSigners();
    }
  });

  const checkTriage = document.getElementById('checkTriageTu');
  const sequenceBlock = document.getElementById('signerSequenceWrapper');
  if (checkTriage && sequenceBlock) {
    checkTriage.addEventListener('change', () => {
      if (checkTriage.checked) {
        sequenceBlock.classList.add('opacity-50', 'pe-none');
        if (typeof showToast === 'function') {
          showToast('Meja Disposisi TU Aktif', 'Rantai penandatangan akan disusun oleh staf Tata Usaha.', 'info');
        }
      } else {
        sequenceBlock.classList.remove('opacity-50', 'pe-none');
      }
    });
  }

  document.querySelectorAll('.js-quick-signer').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const email = btn.getAttribute('data-email');
      const u = PNJ_CIVITAS.find(x => x.email === email);
      if (!u) return;

      let targetRow = null;
      const rows = container.querySelectorAll('.signer-row');
      for (let r of rows) {
        const confirmedBox = r.querySelector('.js-signer-confirmed-box');
        if (confirmedBox && confirmedBox.classList.contains('d-none')) {
          targetRow = r;
          break;
        }
      }

      if (!targetRow) {
        const nextStep = rows.length + 1;
        targetRow = createSignerRowElement(nextStep, u, 1);
        container.appendChild(targetRow);
        attachSignerRowEvents(targetRow);
        renumberSigners();
      } else {
        confirmSignerSelection(targetRow, u);
      }
    });
  });

  const btnPresetAkademik = document.getElementById('presetAkademik');
  const btnPresetOrmawa = document.getElementById('presetOrmawa');

  if (btnPresetAkademik) {
    btnPresetAkademik.addEventListener('click', () => {
      const userIwan = PNJ_CIVITAS.find(x => x.email === 'iwan.sonjaya@tik.pnj.ac.id');
      const userFarida = PNJ_CIVITAS.find(x => x.email === 'farida.laila@tik.pnj.ac.id');
      container.innerHTML = '';
      const r1 = createSignerRowElement(1, userIwan, 1);
      const r2 = createSignerRowElement(2, userFarida, 1);
      container.appendChild(r1);
      container.appendChild(r2);
      attachSignerRowEvents(r1);
      attachSignerRowEvents(r2);
      renumberSigners();
      if (typeof showToast === 'function') {
        showToast('Preset Diterapkan', 'Alur pengesahan Akademik: Pembimbing -> Kaprodi siap.', 'success');
      }
    });
  }

  if (btnPresetOrmawa) {
    btnPresetOrmawa.addEventListener('click', () => {
      const userFarhan = PNJ_CIVITAS.find(x => x.email === 'farhan.pratama.tik@pnj.ac.id');
      const userAnita = PNJ_CIVITAS.find(x => x.email === 'anita.hidayati@tik.pnj.ac.id');
      container.innerHTML = '';
      const r1 = createSignerRowElement(1, userFarhan, 1);
      const r2 = createSignerRowElement(2, userAnita, 1);
      container.appendChild(r1);
      container.appendChild(r2);
      attachSignerRowEvents(r1);
      attachSignerRowEvents(r2);
      renumberSigners();
      if (typeof showToast === 'function') {
        showToast('Preset Diterapkan', 'Alur pengesahan Ormawa: Panitia -> Pembina siap.', 'success');
      }
    });
  }

  document.addEventListener('click', (e) => {
    if (!e.target.closest('.signer-search-wrapper')) {
      document.querySelectorAll('.signer-search-results').forEach(el => el.classList.add('d-none'));
    }
  });
}

function renumberSigners() {
  const container = document.getElementById('signerSequenceContainer');
  if (!container) return;
  const rows = container.querySelectorAll('.signer-row');
  rows.forEach((row, idx) => {
    const badge = row.querySelector('.js-step-num');
    if (badge) badge.textContent = idx + 1;
    const removeBtn = row.querySelector('.js-remove-signer');
    if (removeBtn) {
      if (rows.length === 1) {
        removeBtn.classList.add('d-none');
      } else {
        removeBtn.classList.remove('d-none');
      }
    }
  });
}

function initSigningFlow() {
  document.querySelectorAll('.js-btn-open-sign').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const title = btn.getAttribute('data-doc-title') || 'Dokumen Permohonan TTE';
      const number = btn.getAttribute('data-doc-number') || 'PNJ/TIK/2026/042';
      const sender = btn.getAttribute('data-doc-sender') || 'Pemohon';

      const modalEl = document.getElementById('modalSignDocument');
      if (modalEl) {
        modalEl.querySelector('.js-modal-doc-title').textContent = title;
        modalEl.querySelector('.js-modal-doc-number').textContent = number;
        modalEl.querySelector('.js-modal-doc-sender').textContent = sender;

        const modal = new bootstrap.Modal(modalEl);
        modal.show();
      }
    });
  });

  const formSign = document.getElementById('formExecSignDoc');
  if (formSign) {
    formSign.addEventListener('submit', (e) => {
      e.preventDefault();
      const passphrase = document.getElementById('inputSignPassphrase').value;
      const submitBtn = document.getElementById('btnSubmitSignExec');

      if (!passphrase || passphrase.length < 4) {
        showToast('Kata Sandi Kurang', 'Masukkan kata sandi atau PIN Anda (minimal 4 karakter).', 'warning');
        return;
      }

      const origText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Memproses tanda tangan...`;

      setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = origText;

        const modalEl = document.getElementById('modalSignDocument');
        if (modalEl) {
          const modal = bootstrap.Modal.getInstance(modalEl);
          if (modal) modal.hide();
        }

        formSign.reset();
        showToast('Tanda Tangan Berhasil', 'Dokumen telah berhasil ditandatangani.', 'success');

        const activeRowBadge = document.querySelector('.js-active-target-badge');
        if (activeRowBadge) {
          activeRowBadge.className = 'badge bg-success-subtle text-success border border-success-subtle';
          activeRowBadge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Selesai Ditandatangani';
        }
      }, 800);
    });
  }
}

function initTableSearch() {
  const searchInput = document.getElementById('tableFilterInput');
  const table = document.querySelector('.js-searchable-table');

  if (searchInput && table) {
    searchInput.addEventListener('input', (e) => {
      const q = e.target.value.toLowerCase().trim();
      table.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  }
}

function showToast(title, message, type = 'info') {
  let toastContainer = document.getElementById('toastWrapperBox');
  if (!toastContainer) {
    toastContainer = document.createElement('div');
    toastContainer.id = 'toastWrapperBox';
    toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    document.body.appendChild(toastContainer);
  }

  let bgClass = 'bg-primary text-white';
  let icon = 'bi-info-circle-fill';
  if (type === 'success') { bgClass = 'bg-success text-white'; icon = 'bi-check-circle-fill'; }
  if (type === 'warning') { bgClass = 'bg-warning text-dark'; icon = 'bi-exclamation-triangle-fill'; }
  if (type === 'danger') { bgClass = 'bg-danger text-white'; icon = 'bi-x-circle-fill'; }

  const toastEl = document.createElement('div');
  toastEl.className = 'toast border-0 shadow-lg rounded-3';
  toastEl.setAttribute('role', 'alert');
  toastEl.setAttribute('aria-live', 'assertive');
  toastEl.setAttribute('aria-atomic', 'true');

  toastEl.innerHTML = `
    <div class="toast-header ${bgClass} rounded-top-3">
      <i class="bi ${icon} me-2"></i>
      <strong class="me-auto">${title}</strong>
      <button type="button" class="btn-close ${type === 'warning' ? '' : 'btn-close-white'}" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body bg-white text-dark py-2 px-3" style="font-size: 0.8125rem;">
      ${message}
    </div>
  `;

  toastContainer.appendChild(toastEl);
  const bsToast = new bootstrap.Toast(toastEl, { delay: 4500 });
  bsToast.show();

  toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

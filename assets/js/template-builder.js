function setupTemplateBuilder(config) {
  const options = config && typeof config === 'object' ? config : {};
  const INITIAL_SCHEMA_FIELDS = Array.isArray(options.initialSchemaFields) ? options.initialSchemaFields : [];
  const LOAD_DEFAULT_BODY = Boolean(options.loadDefaultBody);

  const DEFAULT_INITIAL_BODY = '<div class="pnj-letter-heading" style="text-align: center; margin: 0 0 16px 0;">\n  <p style="margin: 0; font-size: 11pt; font-weight: 700;"><u>SURAT KETERANGAN</u></p>\n  <p style="margin: 2px 0 0 0; font-size: 10.5pt;">Nomor {{nomor_surat}}</p>\n</div>\n<p style="text-align: justify; margin: 0 0 10px 0;">Yang bertanda tangan di bawah ini menerangkan dengan sesungguhnya bahwa:</p>\n<table style="width: 100%; border-collapse: collapse; margin: 10px 0 14px 0;">\n  <tr>\n    <td style="width: 160px; padding: 2px 0; vertical-align: top;">Nama Lengkap</td>\n    <td style="width: 16px; padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top; font-weight: 700;">{{nama_lengkap}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">NIM / NIP</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{nomor_induk}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">Program Studi</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{program_studi}}</td>\n  </tr>\n</table>\n<p style="text-align: justify; margin: 0 0 10px 0;">Adalah benar mahasiswa terdaftar aktif di Politeknik Negeri Jakarta pada semester berjalan.</p>\n<p style="text-align: justify; margin: 0 0 20px 0;">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>\n<table style="width: 100%; border-collapse: collapse; margin-top: 24px; page-break-inside: avoid;">\n  <tr>\n    <td style="width: 50%;"></td>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-left: 10px;">\n      <p style="margin: 0 0 2px 0;">Depok, {{tanggal_surat}}</p>\n      <p style="margin: 0 0 4px 0;">Ketua Jurusan Teknik Informatika dan Komputer,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_1}}</div>\n    </td>\n  </tr>\n</table>\n<p style="margin: 16px 0 0 0;"><br></p>\n';

  const PRESETS = {
    sk_mhs: '<div class="pnj-letter-heading" style="text-align: center; margin: 0 0 16px 0;">\n  <p style="margin: 0; font-size: 11pt; font-weight: 700;"><u>SURAT KETERANGAN</u></p>\n  <p style="margin: 2px 0 0 0; font-size: 10.5pt;">Nomor {{nomor_surat}}</p>\n</div>\n<p style="text-align: justify; margin: 0 0 10px 0;">Yang bertanda tangan di bawah ini menerangkan dengan sesungguhnya bahwa:</p>\n<table style="width: 100%; border-collapse: collapse; margin: 10px 0 14px 0;">\n  <tr>\n    <td style="width: 160px; padding: 2px 0; vertical-align: top;">Nama Lengkap</td>\n    <td style="width: 16px; padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top; font-weight: 700;">{{nama_lengkap}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">NIM / NIP</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{nomor_induk}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">Program Studi</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{program_studi}}</td>\n  </tr>\n</table>\n<p style="text-align: justify; margin: 0 0 10px 0;">Adalah benar mahasiswa terdaftar aktif pada jurusan kami untuk tahun akademik berjalan dan berkelakuan baik dalam mengikuti seluruh kegiatan akademik di Politeknik Negeri Jakarta.</p>\n<p style="text-align: justify; margin: 0 0 20px 0;">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>\n<table style="width: 100%; border-collapse: collapse; margin-top: 24px; page-break-inside: avoid;">\n  <tr>\n    <td style="width: 50%;"></td>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-left: 10px;">\n      <p style="margin: 0 0 2px 0;">Depok, {{tanggal_surat}}</p>\n      <p style="margin: 0 0 4px 0;">Ketua Jurusan Teknik Informatika dan Komputer,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_1}}</div>\n    </td>\n  </tr>\n</table>\n<p style="margin: 16px 0 0 0;"><br></p>\n',
    pengumuman: '<div class="pnj-letter-heading" style="text-align: center; margin: 0 0 16px 0;">\n  <p style="margin: 0; font-size: 11pt; font-weight: 700; letter-spacing: 0.5px;">PENGUMUMAN</p>\n  <p style="margin: 2px 0 0 0; font-size: 10.5pt;">Nomor {{nomor_surat}}</p>\n  <p style="margin: 12px 0 0 0; font-size: 10.5pt;">tentang</p>\n  <p style="margin: 2px 0 0 0; font-size: 10.5pt; font-weight: 700;">Perubahan Mekanisme Pengajuan Surat Keterangan Mahasiswa</p>\n</div>\n<p style="text-align: justify; margin: 0 0 12px 0;">Diberitahukan kepada seluruh mahasiswa Politeknik Negeri Jakarta bahwa terdapat perubahan mekanisme pengajuan surat keterangan mahasiswa yang semula diajukan melalui laman surat-akademik.pnj.ac.id, untuk sementara waktu diajukan melalui link sebagai berikut :</p>\n<p style="text-align: center; margin: 12px 0; font-weight: 700;"><a href="https://s.pnj.ac.id/SuratKeteranganOnline" style="color: #000000; text-decoration: underline;">https://s.pnj.ac.id/SuratKeteranganOnline</a></p>\n<p style="text-align: justify; margin: 0 0 12px 0;">Sehubungan dengan perubahan tersebut, seluruh mahasiswa yang membutuhkan surat keterangan diharapkan untuk mengajukan permohonan melalui tautan yang telah disediakan dengan mengisi data dan melengkapi persyaratan sesuai dengan ketentuan yang berlaku.</p>\n<p style="text-align: justify; margin: 0 0 12px 0;">Dengan adanya perubahan mekanisme ini, diharapkan proses pengajuan dan pelayanan surat keterangan mahasiswa dapat berjalan lebih tertib, efektif, dan mudah diakses.</p>\n<p style="text-align: justify; margin: 0 0 20px 0;">Demikian pengumuman ini disampaikan untuk menjadi perhatian seluruh mahasiswa. Atas perhatian dan kerja samanya, diucapkan terima kasih.</p>\n<table style="width: 100%; border-collapse: collapse; margin-top: 20px; page-break-inside: avoid;">\n  <tr>\n    <td style="width: 50%;"></td>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-left: 10px;">\n      <p style="margin: 0 0 2px 0;">{{tanggal_surat}}</p>\n      <p style="margin: 0 0 4px 0;">Wakil Direktur Bidang Akademik,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_1}}</div>\n    </td>\n  </tr>\n</table>\n<div class="pnj-tembusan" style="margin-top: 18px; font-size: 10pt; line-height: 1.35; text-align: left;">\n  <p style="margin: 0 0 2px 0;">Tembusan :</p>\n  <ol style="margin: 2px 0 0 0; padding-left: 20px;">\n    <li>Direktur;</li>\n    <li>Para Wakil Direktur;</li>\n    <li>Para Ketua Jurusan;</li>\n    <li>Koordinator PSDKU Kab. Demak;</li>\n    <li>Koordinator PSDKU Kota Pekalongan;</li>\n    <li>Kabag. Akademik dan Kemahasiswaan.</li>\n  </ol>\n</div>\n<p style="margin: 16px 0 0 0;"><br></p>\n',
    rekomendasi_2ttd: '<div class="pnj-letter-heading" style="text-align: center; margin: 0 0 16px 0;">\n  <p style="margin: 0; font-size: 11pt; font-weight: 700;"><u>SURAT PENGANTAR PRAKTEK KERJA LAPANGAN</u></p>\n  <p style="margin: 2px 0 0 0; font-size: 10.5pt;">Nomor {{nomor_surat}}</p>\n</div>\n<p style="text-align: justify; margin: 0 0 10px 0;">Pimpinan Jurusan Teknik Informatika dan Komputer Politeknik Negeri Jakarta dengan ini menerangkan bahwa mahasiswa berikut:</p>\n<table style="width: 100%; border-collapse: collapse; margin: 10px 0 14px 0;">\n  <tr>\n    <td style="width: 160px; padding: 2px 0; vertical-align: top;">Nama Lengkap</td>\n    <td style="width: 16px; padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top; font-weight: 700;">{{nama_lengkap}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">NIM / NIP</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{nomor_induk}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">Program Studi</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{program_studi}}</td>\n  </tr>\n</table>\n<p style="text-align: justify; margin: 0 0 10px 0;">Diberikan rekomendasi dan pengantar resmi untuk melaksanakan kegiatan Praktek Kerja Lapangan (PKL) / Magang Industri pada instansi mitra sesuai ketentuan kurikulum pendidikan vokasi Politeknik Negeri Jakarta.</p>\n<p style="text-align: justify; margin: 0 0 20px 0;">Demikian surat pengantar ini diberikan untuk dapat dipergunakan sebagaimana mestinya.</p>\n<table style="width: 100%; border-collapse: collapse; margin-top: 24px; page-break-inside: avoid;">\n  <tr>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-right: 15px;">\n      <p style="margin: 0 0 2px 0;">Mengetahui,</p>\n      <p style="margin: 0 0 4px 0;">Koordinator Program Studi,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_1}}</div>\n    </td>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-left: 15px;">\n      <p style="margin: 0 0 2px 0;">Depok, {{tanggal_surat}}</p>\n      <p style="margin: 0 0 4px 0;">Ketua Jurusan TIK,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_2}}</div>\n    </td>\n  </tr>\n</table>\n<p style="margin: 16px 0 0 0;"><br></p>\n'
  };

  const SNIPPETS = {
    title: '<div class="pnj-letter-heading" style="text-align: center; margin: 0 0 16px 0;">\n  <p style="margin: 0; font-size: 11pt; font-weight: 700;"><u>SURAT KETERANGAN</u></p>\n  <p style="margin: 2px 0 0 0; font-size: 10.5pt;">Nomor {{nomor_surat}}</p>\n</div>\n',
    applicant_table: '<table style="width: 100%; border-collapse: collapse; margin: 10px 0 14px 0;">\n  <tr>\n    <td style="width: 160px; padding: 2px 0; vertical-align: top;">Nama Lengkap</td>\n    <td style="width: 16px; padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top; font-weight: 700;">{{nama_lengkap}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">NIM / NIP</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{nomor_induk}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">Program Studi</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{program_studi}}</td>\n  </tr>\n  <tr>\n    <td style="padding: 2px 0; vertical-align: top;">Email</td>\n    <td style="padding: 2px 0; vertical-align: top; text-align: center;">:</td>\n    <td style="padding: 2px 0; vertical-align: top;">{{email}}</td>\n  </tr>\n</table>\n',
    ttd_single: '<table style="width: 100%; border-collapse: collapse; margin-top: 24px; page-break-inside: avoid;">\n  <tr>\n    <td style="width: 50%;"></td>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-left: 10px;">\n      <p style="margin: 0 0 2px 0;">Depok, {{tanggal_surat}}</p>\n      <p style="margin: 0 0 4px 0;">Ketua Jurusan Teknik Informatika dan Komputer,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_1}}</div>\n    </td>\n  </tr>\n</table>\n<p style="margin: 16px 0 0 0;"><br></p>\n',
    ttd_double: '<table style="width: 100%; border-collapse: collapse; margin-top: 24px; page-break-inside: avoid;">\n  <tr>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-right: 15px;">\n      <p style="margin: 0 0 2px 0;">Mengetahui,</p>\n      <p style="margin: 0 0 4px 0;">Koordinator Program Studi,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_1}}</div>\n    </td>\n    <td style="width: 50%; vertical-align: top; text-align: left; padding-left: 15px;">\n      <p style="margin: 0 0 2px 0;">Depok, {{tanggal_surat}}</p>\n      <p style="margin: 0 0 4px 0;">Ketua Jurusan TIK,</p>\n      <div style="height: 75px; min-height: 75px; display: flex; align-items: center; margin: 4px 0;">{{kolom_ttd_2}}</div>\n    </td>\n  </tr>\n</table>\n<p style="margin: 16px 0 0 0;"><br></p>\n',
    tembusan: '<div class="pnj-tembusan" style="margin-top: 18px; font-size: 10pt; line-height: 1.35; text-align: left;">\n  <p style="margin: 0 0 2px 0;">Tembusan :</p>\n  <ol style="margin: 2px 0 0 0; padding-left: 20px;">\n    <li>Wakil Direktur Bidang Akademik;</li>\n    <li>Ketua Jurusan terkait;</li>\n    <li>Arsip.</li>\n  </ol>\n</div>\n<p style="margin: 16px 0 0 0;"><br></p>\n'
  };

  const RESERVED_TAG_KEYS = new Set([
    'nama_lengkap',
    'nama',
    'nomor_induk',
    'nim',
    'nip',
    'nik',
    'program_studi',
    'study_program',
    'prodi',
    'jabatan',
    'position',
    'email',
    'nomor_surat',
    'document_number',
    'tanggal_surat',
    'qr_verifikasi'
  ]);

  const BLOCK_TAGS = new Set(['TABLE', 'DIV', 'P', 'OL', 'UL', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6']);

  const builderForm = document.getElementById('template-builder-form');
  const clientErrorBox = document.getElementById('builder-client-error');
  const titleInput = document.getElementById('template-title');
  const codeInput = document.getElementById('template-code');
  const editableEl = document.getElementById('pnj-letter-body-editable');
  const hiddenHtmlInput = document.getElementById('html-content-hidden');
  const schemaContainer = document.getElementById('schema-fields-container');
  const schemaEmptyState = document.getElementById('schema-empty-state');
  const schemaTemplate = document.getElementById('schema-row-template');
  const addFieldBtn = document.getElementById('btn-add-field');
  const dynamicCheatSheet = document.getElementById('cheat-sheet-dynamic');
  const exportSheetEl = document.getElementById('pnj-a4-export-sheet');
  const testPdfBtn = document.getElementById('btn-test-pdf');
  const testPdfLabel = document.getElementById('btn-test-pdf-label');
  const pdfModal = document.getElementById('pdf-test-modal');
  const pdfIframe = document.getElementById('pdf-test-iframe');
  const pdfMeta = document.getElementById('pdf-test-meta');
  const pdfDownloadLink = document.getElementById('btn-download-test-pdf');
  const closePdfModalBtn = document.getElementById('btn-close-pdf-modal');
  const cancelPdfModalBtn = document.getElementById('btn-cancel-pdf-modal');

  let activePdfBlobUrl = null;
  let savedSelectionRange = null;

  function syncHiddenInput() {
    if (editableEl && hiddenHtmlInput) {
      hiddenHtmlInput.value = editableEl.innerHTML.trim();
    }
  }

  function ensureTrailingParagraph() {
    if (editableEl) {
      const lastChild = editableEl.lastElementChild;

      if (!lastChild || lastChild.tagName !== 'P') {
        const p = document.createElement('p');
        p.style.margin = '16px 0 0 0';
        p.innerHTML = '<br>';
        editableEl.appendChild(p);
      }
    }
  }

  function getActiveAlignment() {
    let alignment = 'justifyLeft';

    if (document.queryCommandState) {
      if (document.queryCommandState('justifyCenter')) {
        alignment = 'justifyCenter';
      } else if (document.queryCommandState('justifyRight')) {
        alignment = 'justifyRight';
      } else if (document.queryCommandState('justifyFull')) {
        alignment = 'justifyFull';
      } else if (document.queryCommandState('justifyLeft')) {
        alignment = 'justifyLeft';
      }
    } else {
      const sel = window.getSelection();

      if (sel && sel.rangeCount > 0 && sel.anchorNode) {
        let node = sel.anchorNode;

        if (node.nodeType === 3) {
          node = node.parentNode;
        }

        if (!editableEl || !editableEl.contains(node)) {
          alignment = null;
        } else {
          let current = node;
          let resolved = null;

          while (current && current !== editableEl && resolved === null) {
            if (current.nodeType === 1) {
              const inlineAlign = current.style ? current.style.textAlign : '';
              const computedAlign = window.getComputedStyle(current).textAlign;
              const activeValue = inlineAlign || computedAlign;

              if (activeValue === 'center') {
                resolved = 'justifyCenter';
              } else if (activeValue === 'right' || activeValue === 'end') {
                resolved = 'justifyRight';
              } else if (activeValue === 'justify') {
                resolved = 'justifyFull';
              } else if (activeValue === 'left' || activeValue === 'start') {
                resolved = 'justifyLeft';
              }
            }

            current = current.parentNode;
          }

          if (resolved !== null) {
            alignment = resolved;
          }
        }
      }
    }

    return alignment;
  }

  function updateToolbarActiveStates() {
    if (editableEl) {
      const sel = window.getSelection();
      let isInsideEditor = false;

      if (sel && sel.rangeCount > 0 && sel.anchorNode) {
        let checkNode = sel.anchorNode;

        if (checkNode.nodeType === 3) {
          checkNode = checkNode.parentNode;
        }

        if (checkNode && (checkNode === editableEl || editableEl.contains(checkNode))) {
          isInsideEditor = true;
        }
      }

      const activeAlign = isInsideEditor ? getActiveAlignment() : null;

      document.querySelectorAll('[data-cmd]').forEach(function (button) {
        const cmd = button.getAttribute('data-cmd');
        let isActive = false;

        if (isInsideEditor && cmd) {
          if (cmd === 'justifyLeft' || cmd === 'justifyCenter' || cmd === 'justifyRight' || cmd === 'justifyFull') {
            if (cmd === activeAlign) {
              isActive = true;
            }
          } else {
            try {
              if (document.queryCommandState && document.queryCommandState(cmd)) {
                isActive = true;
              }
            } catch (e) {
              isActive = false;
            }
          }
        }

        if (isActive) {
          button.classList.add('is-active');
          button.setAttribute('aria-pressed', 'true');
        } else {
          button.classList.remove('is-active');
          button.setAttribute('aria-pressed', 'false');
        }
      });
    }
  }

  function showClientError(message) {
    if (clientErrorBox) {
      showNotice(clientErrorBox, message);
      clientErrorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }

  function clearClientError() {
    if (clientErrorBox) {
      hideNotice(clientErrorBox);
    }
  }

  const STOP_WORDS = new Set([
    'dan', 'atau', 'yang', 'di', 'ke', 'dari', 'untuk', 'pada',
    'saat', 'dengan', 'ini', 'oleh', 'bagi', 'tentang', 'sebagai'
  ]);

  const PHRASE_MAP = {
    'nama lengkap': 'nama',
    'keperluan pengajuan': 'keperluan',
    'keperluan surat': 'keperluan',
    'perihal pengajuan': 'perihal',
    'perihal surat': 'perihal',
    'tujuan pengajuan': 'tujuan',
    'tujuan magang': 'tujuan_magang',
    'tujuan surat': 'tujuan',
    'alamat lengkap': 'alamat',
    'alamat domisili': 'alamat',
    'alasan permohonan': 'alasan',
    'alasan pengajuan': 'alasan',
    'nomor telepon': 'telepon',
    'no telepon': 'telepon',
    'nomor hp': 'no_hp',
    'no hp': 'no_hp',
    'nomor whatsapp': 'no_wa',
    'no wa': 'no_wa',
    'nama instansi': 'instansi',
    'nama perusahaan': 'perusahaan'
  };

  function simplifyLabelToSnakeCase(rawLabel, trimEdges) {
    let text = String(rawLabel || '').toLowerCase().trim();
    let result = '';

    if (text !== '') {
      const phrases = Object.keys(PHRASE_MAP);
      let exactMatch = '';

      for (let i = 0; i < phrases.length; i++) {
        if (text === phrases[i]) {
          exactMatch = PHRASE_MAP[phrases[i]];
          break;
        }
      }

      if (exactMatch !== '') {
        result = exactMatch;
      } else {
        for (let i = 0; i < phrases.length; i++) {
          const phrase = phrases[i];
          const pattern = new RegExp('\\b' + phrase + '\\b', 'g');
          text = text.replace(pattern, PHRASE_MAP[phrase]);
        }

        const rawWords = text.replace(/[^a-z0-9]+/g, ' ').trim().split(/\s+/);
        const filtered = rawWords.filter(function (word) {
          return word && !STOP_WORDS.has(word);
        });

        const activeWords = filtered.length > 0 ? filtered : rawWords;
        result = activeWords.slice(0, 3).join('_');

        if (trimEdges) {
          result = result.replace(/_+/g, '_').replace(/^_+|_+$/g, '');
        }
      }
    }

    return result;
  }

  function slugifySnakeCase(rawText, trimEdges) {
    const text = String(rawText || '').replace(/([a-z0-9])([A-Z])/g, '$1_$2').toLowerCase();
    let normalized = text.replace(/[^a-z0-9_]+/g, '_');

    if (trimEdges) {
      normalized = normalized.replace(/_+/g, '_').replace(/^_+|_+$/g, '');
    }

    return normalized;
  }

  function normalizeTemplateCode(rawCode) {
    return String(rawCode || '').toUpperCase().replace(/\s+/g, '-').replace(/[^A-Z0-9_-]/g, '');
  }

  function isReservedTagKey(tagKey) {
    return RESERVED_TAG_KEYS.has(tagKey) || /^kolom_ttd_\d+$/.test(tagKey);
  }

  function readSchemaRows() {
    const rows = Array.prototype.slice.call(schemaContainer.querySelectorAll('[data-schema-row]'));
    const items = [];

    rows.forEach(function (row) {
      const labelInput = row.querySelector('[data-input="label"]');
      const nameInput = row.querySelector('[data-input="name"]');
      const typeInput = row.querySelector('[data-input="type"]');
      const requiredInput = row.querySelector('[data-input="required"]');
      const placeholderInput = row.querySelector('[data-input="placeholder"]');
      const optionsInput = row.querySelector('[data-input="options"]');
      const rawOptions = optionsInput ? optionsInput.value : '';
      const parsedOptions = rawOptions.split(',').map(function (opt) {
        return opt.trim();
      }).filter(function (opt) {
        return opt !== '';
      });

      items.push({
        rowEl: row,
        labelEl: labelInput,
        nameEl: nameInput,
        optionsEl: optionsInput,
        label: labelInput ? labelInput.value.trim() : '',
        name: slugifySnakeCase(nameInput ? nameInput.value : '', true),
        type: typeInput ? typeInput.value : 'text',
        required: requiredInput ? requiredInput.value === '1' : true,
        placeholder: placeholderInput ? placeholderInput.value.trim() : '',
        options: parsedOptions
      });
    });

    return items;
  }

  function updateRowNumbersAndPreviewBadges() {
    const rows = Array.prototype.slice.call(schemaContainer.querySelectorAll('[data-schema-row]'));

    rows.forEach(function (row, index) {
      const badgeEl = row.querySelector('[data-row-badge]');
      const nameInput = row.querySelector('[data-input="name"]');
      const tagPreviewEl = row.querySelector('[data-row-tag-preview]');
      const cleanName = slugifySnakeCase(nameInput ? nameInput.value : '', true);

      if (badgeEl) {
        badgeEl.textContent = 'Field #' + (index + 1);
      }

      if (tagPreviewEl) {
        if (cleanName !== '') {
          tagPreviewEl.textContent = '{{' + cleanName + '}}';
          tagPreviewEl.classList.remove('hidden');
          tagPreviewEl.classList.add('inline-flex');
        } else {
          tagPreviewEl.textContent = '';
          tagPreviewEl.classList.add('hidden');
          tagPreviewEl.classList.remove('inline-flex');
        }
      }
    });

    if (schemaEmptyState) {
      schemaEmptyState.classList.toggle('hidden', rows.length > 0);
    }
  }

  function renderDynamicCheatSheet() {
    const items = readSchemaRows().filter(function (item) {
      return item.name !== '';
    });

    dynamicCheatSheet.innerHTML = '';

    if (items.length === 0) {
      const emptyHint = document.createElement('span');
      emptyHint.className = 'text-xs text-on-surface-variant/80 italic';
      emptyHint.textContent = 'Belum ada kolom isian formulir.';
      dynamicCheatSheet.appendChild(emptyHint);
    } else {
      items.forEach(function (item) {
        const tagText = '{{' + item.name + '}}';
        const chipBtn = document.createElement('button');

        chipBtn.type = 'button';
        chipBtn.dataset.insertTag = tagText;
        chipBtn.title = item.label !== '' ? item.label : item.name;
        chipBtn.className = 'px-2.5 py-1 rounded-md border border-[#bfdbfe] bg-[#eff6ff] hover:bg-[#dbeafe] text-[#004B87] font-code-tabular text-xs font-semibold transition-colors shadow-sm';
        chipBtn.textContent = tagText;

        chipBtn.addEventListener('mousedown', function (e) {
          e.preventDefault();
        });

        chipBtn.addEventListener('click', function () {
          insertAtCursor(tagText);
        });

        dynamicCheatSheet.appendChild(chipBtn);
      });
    }
  }

  function setNameManualMode(nameInput, toggleLabel, labelInput, isManual, regenerate) {
    if (nameInput) {
      if (isManual) {
        nameInput.dataset.manual = 'true';
        nameInput.readOnly = false;
        nameInput.classList.remove('bg-surface-container/60', 'cursor-default');
        nameInput.classList.add('bg-surface-container-lowest', 'cursor-text');

        if (toggleLabel) {
          toggleLabel.textContent = 'Otomatis';
        }
      } else {
        nameInput.dataset.manual = 'false';
        nameInput.readOnly = true;
        nameInput.classList.add('bg-surface-container/60', 'cursor-default');
        nameInput.classList.remove('bg-surface-container-lowest', 'cursor-text');

        if (toggleLabel) {
          toggleLabel.textContent = 'Ubah';
        }

        if (regenerate) {
          nameInput.value = simplifyLabelToSnakeCase(labelInput ? labelInput.value : '', true);
        }
      }
    }
  }

  function saveCurrentSelection() {
    const sel = window.getSelection();

    if (sel && sel.rangeCount > 0) {
      const range = sel.getRangeAt(0);

      if (editableEl && editableEl.contains(range.commonAncestorContainer)) {
        savedSelectionRange = range.cloneRange();
      }
    }
  }

  function findEnclosingParagraph(node) {
    let current = node;
    let found = null;

    if (current && current.nodeType === 3) {
      current = current.parentNode;
    }

    while (current && current !== editableEl && current.nodeName !== 'TD' && current.nodeName !== 'TH' && found === null) {
      if (current.nodeName === 'P') {
        found = current;
      }

      current = current.parentNode;
    }

    return found;
  }

  function insertAtCursor(text) {
    if (editableEl) {
      let range = null;
      const sel = window.getSelection();

      if (savedSelectionRange && editableEl.contains(savedSelectionRange.commonAncestorContainer)) {
        range = savedSelectionRange.cloneRange();
      } else if (sel && sel.rangeCount > 0) {
        const candidateRange = sel.getRangeAt(0);

        if (editableEl.contains(candidateRange.commonAncestorContainer)) {
          range = candidateRange.cloneRange();
        }
      }

      if (!range) {
        ensureTrailingParagraph();
        const lastChild = editableEl.lastElementChild;
        range = document.createRange();

        if (lastChild) {
          range.selectNodeContents(lastChild);
          range.collapse(false);
        } else {
          range.selectNodeContents(editableEl);
          range.collapse(false);
        }
      }

      editableEl.focus();

      if (sel) {
        sel.removeAllRanges();
        sel.addRange(range);
      }

      range.deleteContents();

      if (text.indexOf('<') !== -1) {
        const temp = document.createElement('div');
        temp.innerHTML = text;
        const hasBlockContent = Array.from(temp.children).some(function (child) {
          return BLOCK_TAGS.has(child.tagName);
        });
        const enclosingP = hasBlockContent ? findEnclosingParagraph(range.commonAncestorContainer) : null;
        const frag = document.createDocumentFragment();
        let node;
        let lastNode;

        while ((node = temp.firstChild)) {
          lastNode = frag.appendChild(node);
        }

        if (enclosingP && enclosingP.parentNode) {
          if (enclosingP.textContent.trim() === '') {
            enclosingP.parentNode.insertBefore(frag, enclosingP);
            enclosingP.parentNode.removeChild(enclosingP);
          } else {
            enclosingP.parentNode.insertBefore(frag, enclosingP.nextSibling);
          }
        } else {
          range.insertNode(frag);
        }

        if (lastNode && sel) {
          range = range.cloneRange();
          range.setStartAfter(lastNode);
          range.collapse(true);
          sel.removeAllRanges();
          sel.addRange(range);
        }
      } else {
        const textNode = document.createTextNode(text);
        range.insertNode(textNode);
        range.setStartAfter(textNode);
        range.collapse(true);

        if (sel) {
          sel.removeAllRanges();
          sel.addRange(range);
        }
      }

      savedSelectionRange = range.cloneRange();

      ensureTrailingParagraph();
      syncHiddenInput();
    }
  }

  function attachRowEvents(row) {
    const labelInput = row.querySelector('[data-input="label"]');
    const nameInput = row.querySelector('[data-input="name"]');
    const toggleBtn = row.querySelector('[data-toggle-custom-name]');
    const toggleLabel = row.querySelector('[data-toggle-label]');
    const tagPreviewBtn = row.querySelector('[data-row-tag-preview]');
    const typeInput = row.querySelector('[data-input="type"]');
    const optionsWrap = row.querySelector('[data-options-wrap]');
    const removeBtn = row.querySelector('[data-remove-field]');

    if (tagPreviewBtn) {
      tagPreviewBtn.addEventListener('click', function () {
        const currentTag = tagPreviewBtn.textContent.trim();

        if (currentTag !== '') {
          insertAtCursor(currentTag);
        }
      });
    }

    if (toggleBtn && nameInput) {
      toggleBtn.addEventListener('click', function () {
        const nextManual = nameInput.dataset.manual !== 'true';

        setNameManualMode(nameInput, toggleLabel, labelInput, nextManual, !nextManual);

        if (nextManual) {
          nameInput.focus();
          nameInput.select();
        }

        updateRowNumbersAndPreviewBadges();
        renderDynamicCheatSheet();
      });
    }

    if (labelInput && nameInput) {
      labelInput.addEventListener('input', function () {
        if (nameInput.dataset.manual !== 'true') {
          nameInput.value = simplifyLabelToSnakeCase(labelInput.value, false);
          updateRowNumbersAndPreviewBadges();
          renderDynamicCheatSheet();
        }
      });

      labelInput.addEventListener('blur', function () {
        if (nameInput.dataset.manual !== 'true') {
          nameInput.value = simplifyLabelToSnakeCase(labelInput.value, true);
          updateRowNumbersAndPreviewBadges();
          renderDynamicCheatSheet();
        }
      });
    }

    if (nameInput) {
      nameInput.addEventListener('input', function () {
        setNameManualMode(nameInput, toggleLabel, labelInput, true, false);
        updateRowNumbersAndPreviewBadges();
        renderDynamicCheatSheet();
      });

      nameInput.addEventListener('blur', function () {
        nameInput.value = slugifySnakeCase(nameInput.value, true);
        updateRowNumbersAndPreviewBadges();
        renderDynamicCheatSheet();
      });
    }

    if (typeInput && optionsWrap) {
      const checkType = function () {
        const isSelect = typeInput.value === 'select';
        optionsWrap.classList.toggle('hidden', !isSelect);
        optionsWrap.classList.toggle('flex', isSelect);
      };

      typeInput.addEventListener('change', checkType);
      checkType();
    }

    if (removeBtn) {
      removeBtn.addEventListener('click', function () {
        row.remove();
        updateRowNumbersAndPreviewBadges();
        renderDynamicCheatSheet();
      });
    }
  }

  function addSchemaRow(initialData) {
    if (schemaTemplate && schemaContainer) {
      const fragment = schemaTemplate.content.cloneNode(true);
      const row = fragment.querySelector('[data-schema-row]');

      if (row) {
        const labelInput = row.querySelector('[data-input="label"]');
        const nameInput = row.querySelector('[data-input="name"]');
        const toggleLabel = row.querySelector('[data-toggle-label]');
        const typeInput = row.querySelector('[data-input="type"]');
        const requiredInput = row.querySelector('[data-input="required"]');
        const placeholderInput = row.querySelector('[data-input="placeholder"]');
        const optionsInput = row.querySelector('[data-input="options"]');

        if (initialData) {
          if (labelInput) {
            labelInput.value = initialData.label || '';
          }

          if (nameInput) {
            nameInput.value = initialData.name || '';
          }

          const hasName = Boolean(initialData.name);
          const autoName = simplifyLabelToSnakeCase(initialData.label || '', true);
          const shouldEnableManual = hasName && initialData.name !== autoName;

          setNameManualMode(nameInput, toggleLabel, labelInput, shouldEnableManual, false);

          if (typeInput && initialData.type) {
            typeInput.value = initialData.type;
          }

          if (requiredInput) {
            requiredInput.value = initialData.required === false ? '0' : '1';
          }

          if (placeholderInput) {
            placeholderInput.value = initialData.placeholder || '';
          }

          if (optionsInput) {
            if (Array.isArray(initialData.options)) {
              optionsInput.value = initialData.options.join(', ');
            } else if (typeof initialData.options === 'string') {
              optionsInput.value = initialData.options;
            }
          }
        } else {
          setNameManualMode(nameInput, toggleLabel, labelInput, false, false);
        }

        attachRowEvents(row);
        schemaContainer.appendChild(row);

        updateRowNumbersAndPreviewBadges();
        renderDynamicCheatSheet();
      }
    }
  }

  function setSelectionToElementEnd(sel, targetEl) {
    const newRange = document.createRange();
    newRange.selectNodeContents(targetEl);
    newRange.collapse(false);
    sel.removeAllRanges();
    sel.addRange(newRange);
  }

  function handleTableEditorKeydown(e, sel, cell, block) {
    const table = cell.closest('table');
    const row = cell.parentElement;
    const isLastRow = row && table && row === table.rows[table.rows.length - 1];
    const isLastCell = row && cell === row.cells[row.cells.length - 1];
    const range = sel.getRangeAt(0);

    if (e.key === 'Enter') {
      const isBlockEmpty = !block || block.textContent.trim() === '' || block.innerHTML === '<br>';

      if (isBlockEmpty && isLastRow && isLastCell) {
        e.preventDefault();

        if (block && cell.children.length > 1) {
          block.remove();
        }

        ensureTrailingParagraph();
        let targetP = table.nextElementSibling;

        if (!targetP || targetP.tagName !== 'P') {
          targetP = document.createElement('p');
          targetP.style.margin = '16px 0 0 0';
          targetP.innerHTML = '<br>';
          table.parentNode.insertBefore(targetP, table.nextSibling);
        }

        setSelectionToElementEnd(sel, targetP);
        syncHiddenInput();
      }
    } else if (e.key === 'ArrowDown' && isLastRow && isLastCell && block === cell.lastElementChild) {
      ensureTrailingParagraph();
      const targetP = table.nextElementSibling;

      if (targetP) {
        e.preventDefault();
        setSelectionToElementEnd(sel, targetP);
      }
    } else if (e.key === 'Backspace') {
      const isBlockEmpty = block && (block.textContent.trim() === '' || block.innerHTML === '<br>');

      if (isBlockEmpty && block.previousElementSibling) {
        e.preventDefault();
        const prev = block.previousElementSibling;
        block.remove();
        setSelectionToElementEnd(sel, prev);
        syncHiddenInput();
      } else if (range.collapsed && range.startOffset === 0) {
        if (cell.cellIndex > 0) {
          e.preventDefault();
          setSelectionToElementEnd(sel, row.cells[cell.cellIndex - 1]);
        } else if (cell.cellIndex === 0) {
          const isRowEmpty = row.textContent.trim() === '';

          if (isRowEmpty) {
            e.preventDefault();
            const prevRow = row.previousElementSibling;
            row.remove();

            if (table.rows.length === 0) {
              const fallbackP = document.createElement('p');
              fallbackP.innerHTML = '<br>';
              fallbackP.style.margin = '0 0 12px 0';
              table.parentNode.insertBefore(fallbackP, table.nextSibling);
              table.remove();
              setSelectionToElementEnd(sel, fallbackP);
            } else if (prevRow) {
              setSelectionToElementEnd(sel, prevRow.cells[prevRow.cells.length - 1]);
            }

            syncHiddenInput();
          } else if (row.previousElementSibling) {
            e.preventDefault();
            const prevRow = row.previousElementSibling;
            setSelectionToElementEnd(sel, prevRow.cells[prevRow.cells.length - 1]);
          } else if (table.previousElementSibling) {
            e.preventDefault();
            setSelectionToElementEnd(sel, table.previousElementSibling);
          }
        }
      }
    }
  }

  function buildSampleValues() {
    const values = {
      nama_lengkap: 'Farhan Hariri',
      nomor_induk: '2507411050',
      program_studi: 'D4 Teknik Informatika',
      email: 'farhan.hariri.tik25@stu.pnj.ac.id',
      jabatan: 'Mahasiswa',
      nomor_surat: '042/PL3/TIK/PNJ/2026',
      tanggal_surat: window.PnjLetterRenderer ? window.PnjLetterRenderer.formatIndonesianDate() : '15 November 2026'
    };

    const schemaItems = readSchemaRows();

    schemaItems.forEach(function (field) {
      if (field.name !== '') {
        let sample = '';

        if (field.placeholder !== '') {
          sample = field.placeholder.replace(new RegExp('^contoh\\s*:\\s*', 'i'), '').trim();
        } else if (field.type === 'select' && field.options.length > 0) {
          sample = field.options[0];
        } else if (field.type === 'date') {
          sample = window.PnjLetterRenderer ? window.PnjLetterRenderer.formatIndonesianDate() : '15 November 2026';
        } else if (field.type === 'number') {
          sample = '4';
        } else {
          sample = '[' + (field.label || field.name) + ']';
        }

        values[field.name] = sample;
      }
    });

    return values;
  }

  function closePdfModal() {
    if (pdfModal) {
      pdfModal.classList.add('hidden');
      pdfModal.classList.remove('flex');
    }

    if (pdfIframe) {
      pdfIframe.src = 'about:blank';
    }

    if (activePdfBlobUrl) {
      URL.revokeObjectURL(activePdfBlobUrl);
      activePdfBlobUrl = null;
    }
  }

  if (addFieldBtn) {
    addFieldBtn.addEventListener('click', function () {
      addSchemaRow();
    });
  }

  if (codeInput) {
    codeInput.addEventListener('input', function () {
      codeInput.value = normalizeTemplateCode(codeInput.value);
    });

    codeInput.addEventListener('blur', function () {
      codeInput.value = normalizeTemplateCode(codeInput.value);
    });
  }

  if (editableEl) {
    if (LOAD_DEFAULT_BODY && editableEl.innerHTML.trim() === '') {
      editableEl.innerHTML = DEFAULT_INITIAL_BODY;
    }

    if (editableEl.innerHTML.trim() !== '') {
      ensureTrailingParagraph();
    }

    syncHiddenInput();
    updateToolbarActiveStates();

    editableEl.addEventListener('input', function () {
      syncHiddenInput();
      updateToolbarActiveStates();
    });

    editableEl.addEventListener('blur', function () {
      syncHiddenInput();
    });

    editableEl.addEventListener('keyup', function () {
      saveCurrentSelection();
      updateToolbarActiveStates();
    });

    editableEl.addEventListener('mouseup', function () {
      saveCurrentSelection();
      updateToolbarActiveStates();
    });

    editableEl.addEventListener('focus', function () {
      saveCurrentSelection();
      updateToolbarActiveStates();
    });

    document.addEventListener('selectionchange', function () {
      saveCurrentSelection();
      updateToolbarActiveStates();
    });

    editableEl.addEventListener('click', function (e) {
      if (e.target === editableEl) {
        ensureTrailingParagraph();
        const lastP = editableEl.lastElementChild;
        const sel = window.getSelection();

        if (lastP && sel) {
          setSelectionToElementEnd(sel, lastP);
        }
      }
    });

    editableEl.addEventListener('keydown', function (e) {
      const sel = window.getSelection();

      if (sel && sel.rangeCount > 0) {
        const range = sel.getRangeAt(0);
        let cell = range.commonAncestorContainer;

        while (cell && cell !== editableEl && cell.nodeName !== 'TD' && cell.nodeName !== 'TH') {
          cell = cell.parentNode;
        }

        if (cell && (cell.nodeName === 'TD' || cell.nodeName === 'TH')) {
          let block = range.commonAncestorContainer;

          while (block && block.parentNode !== cell) {
            block = block.parentNode;
          }

          handleTableEditorKeydown(e, sel, cell, block);
        } else if (e.key === 'Backspace') {
          let block = range.commonAncestorContainer;

          while (block && block.parentNode !== editableEl) {
            block = block.parentNode;
          }

          if (block && block.tagName === 'P' && block.previousElementSibling && block.previousElementSibling.tagName === 'TABLE') {
            const isBlockEmpty = block.textContent.trim() === '' || block.innerHTML === '<br>';

            if (isBlockEmpty && range.startOffset === 0 && range.collapsed) {
              const table = block.previousElementSibling;
              const lastCell = table.querySelector('tr:last-child td:last-child');

              if (lastCell) {
                e.preventDefault();
                setSelectionToElementEnd(sel, lastCell.lastElementChild || lastCell);
              }
            }
          }
        }
      }
    });
  }

  document.querySelectorAll('[data-cmd]').forEach(function (button) {
    button.addEventListener('mousedown', function (e) {
      e.preventDefault();
    });

    button.addEventListener('click', function (e) {
      e.preventDefault();
      const cmd = button.getAttribute('data-cmd');

      if (cmd && editableEl) {
        editableEl.focus();
        document.execCommand(cmd, false, null);
        syncHiddenInput();
        updateToolbarActiveStates();
        setTimeout(updateToolbarActiveStates, 10);
      }
    });
  });

  document.querySelectorAll('[data-snippet-key]').forEach(function (button) {
    button.addEventListener('mousedown', function (e) {
      e.preventDefault();
    });

    button.addEventListener('click', function (e) {
      e.preventDefault();
      const key = button.getAttribute('data-snippet-key');
      const snippet = SNIPPETS[key];

      if (snippet) {
        insertAtCursor(snippet);
        updateToolbarActiveStates();
      }
    });
  });

  document.querySelectorAll('[data-insert-tag]').forEach(function (button) {
    button.addEventListener('mousedown', function (e) {
      e.preventDefault();
    });

    button.addEventListener('click', function (e) {
      e.preventDefault();
      const tag = button.getAttribute('data-insert-tag');

      if (tag) {
        insertAtCursor(tag);
        updateToolbarActiveStates();
      }
    });
  });

  document.querySelectorAll('[data-load-preset]').forEach(function (button) {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      const presetKey = button.getAttribute('data-load-preset');
      const presetHtml = PRESETS[presetKey];

      if (presetHtml && editableEl) {
        editableEl.innerHTML = presetHtml;
        ensureTrailingParagraph();
        syncHiddenInput();
        updateToolbarActiveStates();
        editableEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
  });

  if (builderForm) {
    builderForm.addEventListener('submit', function (event) {
      clearClientError();
      syncHiddenInput();

      const title = titleInput ? titleInput.value.trim() : '';
      const code = codeInput ? codeInput.value.trim() : '';
      const html = hiddenHtmlInput ? hiddenHtmlInput.value.trim() : '';
      let errorMsg = '';
      let focusTarget = null;

      if (title === '') {
        errorMsg = 'Judul template surat wajib diisi.';
      } else if (title.length > 200) {
        errorMsg = 'Judul template maksimal 200 karakter.';
      } else if (code === '') {
        errorMsg = 'Kode template surat wajib diisi.';
      } else if (code.length > 50) {
        errorMsg = 'Kode template maksimal 50 karakter.';
      } else if (!/^[A-Z0-9_-]+$/.test(code)) {
        errorMsg = 'Kode template hanya boleh memuat huruf kapital, angka, tanda hubung (-), dan garis bawah (_).';
      } else if (html === '') {
        errorMsg = 'Konten naskah badan surat tidak boleh kosong.';
      } else if (!/\{\{\s*kolom_ttd_[1-9][0-9]*\s*\}\}/i.test(html)) {
        errorMsg = 'Badan surat wajib memuat minimal satu slot tanda tangan seperti {{kolom_ttd_1}}.';
      } else {
        const schemaItems = readSchemaRows();
        const seenNames = {};

        for (let i = 0; i < schemaItems.length; i++) {
          const item = schemaItems[i];

          if (item.label === '') {
            errorMsg = 'Label tampilan pada Field #' + (i + 1) + ' wajib diisi.';
            focusTarget = item.labelEl;
            break;
          }

          if (item.name === '') {
            errorMsg = 'Kunci tag pada Field #' + (i + 1) + ' wajib diisi.';
            focusTarget = item.nameEl;
            break;
          }

          if (!/^[a-z0-9_]+$/.test(item.name)) {
            errorMsg = 'Kunci tag pada Field #' + (i + 1) + ' hanya boleh memuat huruf kecil, angka, dan garis bawah.';
            focusTarget = item.nameEl;
            break;
          }

          if (isReservedTagKey(item.name)) {
            errorMsg = 'Kunci tag "{{' + item.name + '}}" sudah dipakai oleh variabel sistem.';
            focusTarget = item.nameEl;
            break;
          }

          if (seenNames[item.name]) {
            errorMsg = 'Kunci tag "{{' + item.name + '}}" ganda pada Field #' + (i + 1) + '. Setiap kunci tag harus unik.';
            focusTarget = item.nameEl;
            break;
          }

          seenNames[item.name] = true;

          if (item.type === 'select' && item.options.length === 0) {
            errorMsg = 'Field #' + (i + 1) + ' bertipe Pilihan Dropdown wajib memiliki opsi (pisahkan dengan koma).';
            focusTarget = item.optionsEl;
            break;
          }
        }
      }

      if (errorMsg !== '') {
        event.preventDefault();
        showClientError(errorMsg);

        if (focusTarget) {
          focusTarget.focus();
        }
      }
    });
  }

  if (closePdfModalBtn) {
    closePdfModalBtn.addEventListener('click', closePdfModal);
  }

  if (cancelPdfModalBtn) {
    cancelPdfModalBtn.addEventListener('click', closePdfModal);
  }

  if (testPdfBtn) {
    testPdfBtn.addEventListener('click', function () {
      if (window.PnjLetterRenderer && exportSheetEl && editableEl) {
        syncHiddenInput();
        const rawHtml = editableEl.innerHTML;
        const sampleValues = buildSampleValues();

        window.PnjLetterRenderer.renderIntoSheet(exportSheetEl, rawHtml, sampleValues, { isPreview: true });

        const prevLabel = testPdfLabel ? testPdfLabel.textContent : '';
        testPdfBtn.disabled = true;

        if (testPdfLabel) {
          testPdfLabel.textContent = 'Membuat PDF...';
        }

        setTimeout(function () {
          window.PnjLetterRenderer.generatePdfBlob(exportSheetEl).then(function (pdfBlob) {
            testPdfBtn.disabled = false;

            if (testPdfLabel) {
              testPdfLabel.textContent = prevLabel;
            }

            if (activePdfBlobUrl) {
              URL.revokeObjectURL(activePdfBlobUrl);
              activePdfBlobUrl = null;
            }

            activePdfBlobUrl = URL.createObjectURL(pdfBlob);

            if (pdfIframe) {
              pdfIframe.src = activePdfBlobUrl;
            }

            if (pdfDownloadLink) {
              pdfDownloadLink.href = activePdfBlobUrl;
            }

            if (pdfMeta) {
              const kbSize = (pdfBlob.size / 1024).toFixed(1);
              pdfMeta.textContent = 'Ukuran berkas PDF: ' + kbSize + ' KB • Standar ISO 32000-1 (A4)';
            }

            if (pdfModal) {
              pdfModal.classList.remove('hidden');
              pdfModal.classList.add('flex');
            }
          }).catch(function (error) {
            testPdfBtn.disabled = false;

            if (testPdfLabel) {
              testPdfLabel.textContent = prevLabel;
            }

            showClientError('Gagal merender berkas PDF: ' + (error && error.message ? error.message : error));
          });
        }, 120);
      }
    });
  }

  if (INITIAL_SCHEMA_FIELDS.length > 0) {
    INITIAL_SCHEMA_FIELDS.forEach(function (field) {
      addSchemaRow(field);
    });
  } else {
    updateRowNumbersAndPreviewBadges();
    renderDynamicCheatSheet();
  }
}

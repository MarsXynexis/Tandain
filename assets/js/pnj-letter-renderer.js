(function (window, document) {
  'use strict';

  const INDONESIAN_MONTHS = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember'
  ];

  function hasNonEmptyValue(value) {
    return value !== undefined && value !== null && String(value).trim() !== '';
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function formatEscapedText(value) {
    return escapeHtml(value).replace(/\r\n|\r|\n/g, '<br>');
  }

  function formatDateInstance(dateObj) {
    const day = dateObj.getDate();
    const monthName = INDONESIAN_MONTHS[dateObj.getMonth()];
    const year = dateObj.getFullYear();

    return day + ' ' + monthName + ' ' + year;
  }

  function formatIndonesianDate(dateStr) {
    let result = '';

    if (!hasNonEmptyValue(dateStr)) {
      result = formatDateInstance(new Date());
    } else if (dateStr instanceof Date) {
      if (!isNaN(dateStr.getTime())) {
        result = formatDateInstance(dateStr);
      }
    } else {
      const text = String(dateStr).trim();
      const isoMatch = text.match(/^(\d{4})-(\d{2})-(\d{2})(?:$|[T\s])/);

      if (isoMatch) {
        const yearNum = Number(isoMatch[1]);
        const monthNum = Number(isoMatch[2]);
        const dayNum = Number(isoMatch[3]);

        if (monthNum >= 1 && monthNum <= 12 && dayNum >= 1 && dayNum <= 31) {
          result = dayNum + ' ' + INDONESIAN_MONTHS[monthNum - 1] + ' ' + yearNum;
        } else {
          result = text;
        }
      } else {
        const parsed = new Date(text);

        if (!isNaN(parsed.getTime()) && /^\d/.test(text)) {
          result = formatDateInstance(parsed);
        } else {
          result = text;
        }
      }
    }

    return result;
  }

  function pickFirstValue(map, keys) {
    let found = null;

    if (map && typeof map === 'object') {
      for (let i = 0; i < keys.length; i += 1) {
        const candidateKey = keys[i];

        if (hasNonEmptyValue(map[candidateKey])) {
          found = map[candidateKey];
          break;
        }
      }
    }

    return found;
  }

  function renderSpecialSlot(slotKey, isPreview) {
    let html = '';

    if (slotKey === 'kolom_ttd_1') {
      if (isPreview) {
        html = '<span class="pnj-ttd-placeholder" data-ttd-slot="1">Slot TTD Elektronik 1</span>';
      } else {
        html = '<span class="pnj-ttd-space" data-ttd-slot="1" style="height: 75px; min-height: 75px; width: 100%; display: block;"></span>';
      }
    } else if (slotKey === 'kolom_ttd_2') {
      if (isPreview) {
        html = '<span class="pnj-ttd-placeholder" data-ttd-slot="2">Slot TTD Elektronik 2</span>';
      } else {
        html = '<span class="pnj-ttd-space" data-ttd-slot="2" style="height: 75px; min-height: 75px; width: 100%; display: block;"></span>';
      }
    } else if (slotKey === 'qr_verifikasi') {
      if (isPreview) {
        html = '<span class="pnj-qr-placeholder" data-qr-slot="1">QR Verifikasi</span>';
      } else {
        html = '<span class="pnj-qr-space" data-qr-slot="1" style="width: 75px; height: 75px; min-height: 75px; display: inline-block;"></span>';
      }
    }

    return html;
  }

  function resolveRegularTagValue(rawKey, map) {
    const key = String(rawKey).trim().toLowerCase();
    let value = null;

    if (key === 'nama_lengkap' || key === 'nama') {
      value = pickFirstValue(map, ['nama_lengkap', 'nama', 'full_name']);
    } else if (key === 'nomor_induk' || key === 'nim' || key === 'nip' || key === 'nik') {
      value = pickFirstValue(map, ['nomor_induk', 'nim', 'nip', 'nik', 'identity_number']);
    } else if (key === 'program_studi') {
      value = pickFirstValue(map, ['program_studi', 'study_program', 'prodi']);
    } else if (key === 'study_program') {
      value = pickFirstValue(map, ['study_program', 'program_studi', 'prodi']);
    } else if (key === 'prodi') {
      value = pickFirstValue(map, ['prodi', 'program_studi', 'study_program']);
    } else if (key === 'jabatan') {
      value = pickFirstValue(map, ['jabatan', 'position']);
    } else if (key === 'position') {
      value = pickFirstValue(map, ['position', 'jabatan']);
    } else if (key === 'email') {
      value = pickFirstValue(map, ['email']);
    } else if (key === 'nomor_surat') {
      value = pickFirstValue(map, ['nomor_surat', 'document_number']);
    } else if (key === 'tanggal_surat') {
      const rawDate = pickFirstValue(map, ['tanggal_surat']);

      if (hasNonEmptyValue(rawDate)) {
        value = formatIndonesianDate(rawDate);
      } else {
        value = formatIndonesianDate();
      }
    } else {
      value = pickFirstValue(map, [rawKey, key]);
    }

    return value;
  }

  function replaceTags(bodyHtml, valuesMap, options) {
    const sourceHtml = bodyHtml === undefined || bodyHtml === null ? '' : String(bodyHtml);
    const map = valuesMap && typeof valuesMap === 'object' ? valuesMap : {};
    const isPreview = Boolean(options && options.isPreview);
    const tagPattern = /\{\{\s*([a-zA-Z0-9_]+)\s*\}\}|\{(nama|nim|nik|nip|nomor_induk)\}/g;

    const outputHtml = sourceHtml.replace(tagPattern, function (fullMatch, doubleKey, singleKey) {
      const rawKey = doubleKey || singleKey;
      const normalizedKey = String(rawKey).trim().toLowerCase();
      let replacement = '';

      if (normalizedKey === 'kolom_ttd_1' || normalizedKey === 'kolom_ttd_2' || normalizedKey === 'qr_verifikasi') {
        replacement = renderSpecialSlot(normalizedKey, isPreview);
      } else {
        const resolved = resolveRegularTagValue(rawKey, map);

        if (hasNonEmptyValue(resolved)) {
          replacement = formatEscapedText(resolved);
        } else if (normalizedKey === 'nomor_surat') {
          if (isPreview) {
            replacement = '<span class="pnj-tag-placeholder">[Nomor Surat]</span>';
          } else {
            replacement = '';
          }
        } else if (isPreview) {
          replacement = '<span class="pnj-tag-placeholder">[' + escapeHtml(rawKey) + ']</span>';
        } else {
          replacement = '';
        }
      }

      return replacement;
    });

    return outputHtml;
  }

  function resolveElement(target) {
    let element = null;

    if (typeof target === 'string') {
      element = document.querySelector(target);
    } else if (target && target.nodeType === 1) {
      element = target;
    }

    return element;
  }

  function renderIntoSheet(sheetEl, bodyHtml, valuesMap, options) {
    const sheetElement = resolveElement(sheetEl);

    if (sheetElement) {
      const bodyContainer = sheetElement.querySelector('.pnj-letter-body');
      const targetContainer = bodyContainer ? bodyContainer : sheetElement;
      const renderedHtml = replaceTags(bodyHtml, valuesMap, options);

      targetContainer.innerHTML = renderedHtml;
    }

    return sheetElement;
  }

  function getPdfConstructor() {
    let constructorFn = null;

    if (window.jspdf && typeof window.jspdf.jsPDF === 'function') {
      constructorFn = window.jspdf.jsPDF;
    } else if (typeof window.jsPDF === 'function') {
      constructorFn = window.jsPDF;
    }

    return constructorFn;
  }

  function waitForSingleImage(img) {
    let imagePromise = null;

    if (img.complete && img.naturalWidth > 0) {
      if (typeof img.decode === 'function') {
        imagePromise = img.decode().catch(function () {
          return true;
        });
      } else {
        imagePromise = Promise.resolve(true);
      }
    } else {
      imagePromise = new Promise(function (resolve) {
        img.addEventListener('load', function () {
          resolve(true);
        }, { once: true });

        img.addEventListener('error', function () {
          resolve(false);
        }, { once: true });
      });
    }

    return imagePromise;
  }

  async function waitForAssets(container) {
    const images = Array.prototype.slice.call(container.querySelectorAll('img'));
    const imagePromises = images.map(waitForSingleImage);

    await Promise.all(imagePromises);

    if (document.fonts && document.fonts.ready) {
      await document.fonts.ready;
    }

    return true;
  }

  async function generatePdfBlob(sheetEl) {
    const sourceEl = resolveElement(sheetEl);

    if (!sourceEl) {
      throw new Error('Elemen lembar surat A4 tidak ditemukan.');
    }

    if (typeof window.html2canvas !== 'function') {
      throw new Error('Library html2canvas belum dimuat.');
    }

    const PdfConstructor = getPdfConstructor();

    if (!PdfConstructor) {
      throw new Error('Library jsPDF belum dimuat.');
    }

    const iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.left = '-10000px';
    iframe.style.top = '0';
    iframe.style.width = '794px';
    iframe.style.height = '1123px';
    iframe.style.border = '0';
    iframe.style.zIndex = '-99999';
    iframe.style.background = '#ffffff';

    document.body.appendChild(iframe);

    let pdfBlob = null;

    try {
      const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
      iframeDoc.open();
      iframeDoc.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Render</title>');

      const styleTags = document.querySelectorAll('style, link[rel="stylesheet"]');
      for (let i = 0; i < styleTags.length; i++) {
        iframeDoc.write(styleTags[i].outerHTML);
      }

      iframeDoc.write('</head><body style="margin:0;padding:0;background:#ffffff;overflow:hidden;">');
      iframeDoc.write(sourceEl.outerHTML);
      iframeDoc.write('</body></html>');
      iframeDoc.close();

      const targetEl = iframeDoc.querySelector('.pnj-a4-sheet') || iframeDoc.body.firstElementChild;
      await waitForAssets(iframeDoc.body);

      const captureWidth = targetEl.offsetWidth || 794;
      const captureHeight = targetEl.offsetHeight || 1123;

      const canvas = await window.html2canvas(targetEl, {
        scale: 2,
        useCORS: true,
        backgroundColor: '#ffffff',
        logging: false,
        width: captureWidth,
        height: captureHeight,
        windowWidth: captureWidth,
        windowHeight: captureHeight,
        scrollX: 0,
        scrollY: 0
      });

      const imgData = canvas.toDataURL('image/jpeg', 0.95);
      const pdf = new PdfConstructor('p', 'mm', 'a4');
      const pageWidth = 210;
      const pageHeight = 297;
      const imgWidth = pageWidth;
      const imgHeight = (canvas.height * imgWidth) / canvas.width;

      if (imgHeight <= pageHeight + 1) {
        pdf.addImage(imgData, 'JPEG', 0, 0, imgWidth, Math.min(imgHeight, pageHeight));
      } else {
        let remainingHeight = imgHeight;
        let yOffset = 0;

        pdf.addImage(imgData, 'JPEG', 0, yOffset, imgWidth, imgHeight);
        remainingHeight -= pageHeight;

        while (remainingHeight > 1) {
          yOffset -= pageHeight;
          pdf.addPage();
          pdf.addImage(imgData, 'JPEG', 0, yOffset, imgWidth, imgHeight);
          remainingHeight -= pageHeight;
        }
      }

      pdfBlob = pdf.output('blob');
    } finally {
      if (iframe.parentNode) {
        iframe.parentNode.removeChild(iframe);
      }
    }

    return pdfBlob;
  }

  window.PnjLetterRenderer = {
    replaceTags: replaceTags,
    renderIntoSheet: renderIntoSheet,
    generatePdfBlob: generatePdfBlob,
    formatIndonesianDate: formatIndonesianDate
  };
}(window, document));

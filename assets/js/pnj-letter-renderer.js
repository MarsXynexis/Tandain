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

  function renderSignatureSlot(slotNumber, isPreview) {
    let html = '';

    if (isPreview) {
      html = '<span class="pnj-ttd-placeholder" data-ttd-slot="' + slotNumber + '">Slot TTD Elektronik ' + slotNumber + '</span>';
    } else {
      html = '<span class="pnj-ttd-space" data-ttd-slot="' + slotNumber + '" style="height: 75px; min-height: 75px; width: 100%; display: block;"></span>';
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
      const ttdMatch = normalizedKey.match(/^kolom_ttd_([1-9][0-9]*)$/);
      let replacement = '';

      if (normalizedKey === 'qr_verifikasi') {
        replacement = '';
      } else if (ttdMatch) {
        replacement = renderSignatureSlot(ttdMatch[1], isPreview);
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

    if (img.complete) {
      if (img.naturalWidth > 0) {
        if (typeof img.decode === 'function') {
          imagePromise = img.decode().then(function () {
            return true;
          }, function () {
            return true;
          });
        } else {
          imagePromise = Promise.resolve(true);
        }
      } else {
        imagePromise = Promise.resolve(false);
      }
    } else {
      imagePromise = new Promise(function (resolve) {
        img.addEventListener('load', function () {
          resolve(img.naturalWidth > 0);
        }, { once: true });

        img.addEventListener('error', function () {
          resolve(false);
        }, { once: true });
      });
    }

    return imagePromise;
  }

  async function waitForAssets(container, targetDoc) {
    const images = Array.prototype.slice.call(container.querySelectorAll('img'));
    const imagePromises = images.map(waitForSingleImage);
    const results = await Promise.all(imagePromises);

    if (results.some(function (loaded) {
      return !loaded;
    })) {
      throw new Error('Gambar pada template surat (termasuk kop surat) gagal dimuat.');
    }

    if (targetDoc && targetDoc.fonts && targetDoc.fonts.ready) {
      await targetDoc.fonts.ready;
    }

    if (document.fonts && document.fonts.ready) {
      await document.fonts.ready;
    }

    return true;
  }

  function getDirectTableParts(tableEl) {
    let thead = null;
    const colgroups = [];
    const bodyRows = [];
    const children = Array.prototype.slice.call(tableEl.children || []);

    children.forEach(function (child) {
      if (child.tagName === 'COLGROUP') {
        colgroups.push(child);
      } else if (child.tagName === 'THEAD' && !thead) {
        thead = child;
      } else if (child.tagName === 'TR') {
        bodyRows.push(child);
      } else if (child.tagName === 'TBODY' || child.tagName === 'TFOOT') {
        Array.prototype.slice.call(child.children || []).forEach(function (row) {
          if (row.tagName === 'TR') {
            bodyRows.push(row);
          }
        });
      }
    });

    return {
      thead: thead,
      colgroups: colgroups,
      bodyRows: bodyRows
    };
  }

  function captureTableColumnWidths(tableEl) {
    const parts = getDirectTableParts(tableEl);
    let firstRow = null;
    let widths = [];

    if (parts.thead) {
      const headRows = Array.prototype.slice.call(parts.thead.children || []).filter(function (el) {
        return el.tagName === 'TR';
      });

      if (headRows.length > 0) {
        firstRow = headRows[0];
      }
    }

    if (!firstRow && parts.bodyRows.length > 0) {
      firstRow = parts.bodyRows[0];
    }

    if (firstRow && firstRow.cells && firstRow.cells.length > 0) {
      const cells = Array.prototype.slice.call(firstRow.cells);
      const hasColspan = cells.some(function (cell) {
        return (cell.colSpan || 1) > 1;
      });

      if (!hasColspan) {
        const measured = cells.map(function (cell) {
          return Math.round(cell.getBoundingClientRect().width);
        });

        if (measured.every(function (w) {
          return w > 0;
        })) {
          widths = measured;
        }
      }
    }

    return widths;
  }

  function applyRowColumnWidths(rowEl, colWidths) {
    if (rowEl && colWidths.length > 0 && rowEl.cells && rowEl.cells.length === colWidths.length) {
      for (let c = 0; c < colWidths.length; c++) {
        rowEl.cells[c].style.width = colWidths[c] + 'px';
      }
    }
  }

  function splitTableAcrossPages(tableEl, targetEl, pageHeightPx, topMarginPx, bottomGuardPx, activeWin) {
    const ownerDoc = tableEl.ownerDocument || document;
    const colWidths = captureTableColumnWidths(tableEl);
    let currentTable = tableEl;
    let splitGuard = 0;

    while (currentTable && splitGuard < 50) {
      splitGuard += 1;

      const parts = getDirectTableParts(currentTable);

      if (parts.bodyRows.length <= 1) {
        break;
      }

      if (parts.thead) {
        const headRows = Array.prototype.slice.call(parts.thead.children || []).filter(function (el) {
          return el.tagName === 'TR';
        });

        if (headRows.length > 0) {
          applyRowColumnWidths(headRows[0], colWidths);
        }
      } else {
        applyRowColumnWidths(parts.bodyRows[0], colWidths);
      }

      const sheetRect = targetEl.getBoundingClientRect();
      const tableRect = currentTable.getBoundingClientRect();
      const tableTop = tableRect.top - sheetRect.top;
      const startPage = Math.max(0, Math.floor(tableTop / pageHeightPx));
      const pageBottomLimit = (startPage + 1) * pageHeightPx - bottomGuardPx;
      let splitIndex = -1;

      for (let i = 0; i < parts.bodyRows.length; i++) {
        const rowRect = parts.bodyRows[i].getBoundingClientRect();
        const rowBottom = rowRect.bottom - sheetRect.top;

        if (rowBottom > pageBottomLimit) {
          splitIndex = i;
          break;
        }
      }

      if (splitIndex === -1) {
        break;
      }

      if (splitIndex === 0) {
        if (tableTop > startPage * pageHeightPx + topMarginPx + 4) {
          const nextPageTop = (startPage + 1) * pageHeightPx + topMarginPx;
          const pushPx = Math.ceil(nextPageTop - tableTop);

          if (pushPx > 0) {
            const currentMarginTop = parseFloat(activeWin.getComputedStyle(currentTable).marginTop) || 0;
            currentTable.style.marginTop = (currentMarginTop + pushPx) + 'px';
          }
        } else {
          break;
        }
      } else {
        const nextTable = currentTable.cloneNode(false);
        nextTable.removeAttribute('id');
        nextTable.style.marginTop = '0px';

        parts.colgroups.forEach(function (colgroup) {
          nextTable.appendChild(colgroup.cloneNode(true));
        });

        if (parts.thead) {
          const clonedThead = parts.thead.cloneNode(true);
          const clonedHeadRows = Array.prototype.slice.call(clonedThead.children || []).filter(function (el) {
            return el.tagName === 'TR';
          });

          if (clonedHeadRows.length > 0) {
            applyRowColumnWidths(clonedHeadRows[0], colWidths);
          }

          nextTable.appendChild(clonedThead);
        }

        const nextTbody = ownerDoc.createElement('tbody');

        for (let r = splitIndex; r < parts.bodyRows.length; r++) {
          nextTbody.appendChild(parts.bodyRows[r]);
        }

        if (!parts.thead && parts.bodyRows[splitIndex]) {
          applyRowColumnWidths(parts.bodyRows[splitIndex], colWidths);
        }

        nextTable.appendChild(nextTbody);

        if (!currentTable.parentNode) {
          break;
        }

        currentTable.style.marginBottom = '0px';
        currentTable.parentNode.insertBefore(nextTable, currentTable.nextSibling);

        const updatedSheetRect = targetEl.getBoundingClientRect();
        const nextTableRect = nextTable.getBoundingClientRect();
        const nextTop = nextTableRect.top - updatedSheetRect.top;
        const nextPageStartTop = (startPage + 1) * pageHeightPx + topMarginPx;
        const shiftPx = Math.ceil(nextPageStartTop - nextTop);

        if (shiftPx > 0) {
          nextTable.style.marginTop = shiftPx + 'px';
        }

        currentTable = nextTable;
      }
    }
  }

  function processBlockForPageBreaks(el, targetEl, pageHeightPx, topMarginPx, bottomGuardPx, activeWin) {
    const sheetRect = targetEl.getBoundingClientRect();
    const rect = el.getBoundingClientRect();
    const elHeight = rect.height;

    if (elHeight > 0 && elHeight < pageHeightPx * 0.85) {
      const elTop = rect.top - sheetRect.top;
      const elBottom = rect.bottom - sheetRect.top;
      const startPage = Math.max(0, Math.floor(elTop / pageHeightPx));
      const pageBottomLimit = (startPage + 1) * pageHeightPx - bottomGuardPx;

      if (elBottom > pageBottomLimit) {
        const nextPageTop = (startPage + 1) * pageHeightPx + topMarginPx;
        const shiftPx = Math.ceil(nextPageTop - elTop);

        if (shiftPx > 0) {
          const currentMarginTop = parseFloat(activeWin.getComputedStyle(el).marginTop) || 0;
          el.style.marginTop = (currentMarginTop + shiftPx) + 'px';
        }
      }
    } else if (elHeight >= pageHeightPx * 0.85) {
      if (el.tagName === 'TABLE') {
        splitTableAcrossPages(el, targetEl, pageHeightPx, topMarginPx, bottomGuardPx, activeWin);
      } else {
        const children = Array.prototype.slice.call(el.children || []);

        children.forEach(function (child) {
          processBlockForPageBreaks(child, targetEl, pageHeightPx, topMarginPx, bottomGuardPx, activeWin);
        });
      }
    }
  }

  function applyPageBreakAvoidance(targetEl, pageHeightPx, targetWin) {
    if (targetEl.scrollHeight > pageHeightPx + 2) {
      const bodyEl = targetEl.querySelector('.pnj-letter-body') || targetEl;
      const footerEl = targetEl.querySelector('.pnj-letter-footer');
      const candidates = Array.prototype.slice.call(bodyEl.children);

      if (footerEl) {
        candidates.push(footerEl);
      }

      const topMarginPx = (16 / 297) * pageHeightPx;
      const bottomGuardPx = (10 / 297) * pageHeightPx;
      const activeWin = targetWin || window;

      candidates.forEach(function (el) {
        processBlockForPageBreaks(el, targetEl, pageHeightPx, topMarginPx, bottomGuardPx, activeWin);
      });
    }
  }

  function extractSignatureSlots(targetEl, captureWidth, totalPages, targetWin) {
    const slots = [];
    const seen = {};
    const mmPerPx = 210 / captureWidth;
    const sheetRect = targetEl.getBoundingClientRect();
    const activeWin = targetWin || window;

    targetEl.querySelectorAll('[data-ttd-slot]').forEach(function (el) {
      const slotNum = parseInt(el.getAttribute('data-ttd-slot'), 10);

      if (Number.isInteger(slotNum) && slotNum >= 1 && !seen[slotNum]) {
        const rect = el.getBoundingClientRect();
        const relLeftPx = rect.left - sheetRect.left;
        const relTopPx = rect.top - sheetRect.top;
        const centerYMM = (relTopPx + rect.height / 2) * mmPerPx;
        const page = Math.max(1, Math.min(totalPages, Math.floor(centerYMM / 297) + 1));
        const slotTopInPageMM = (relTopPx * mmPerPx) - ((page - 1) * 297);
        const slotHeightMM = rect.height * mmPerPx;
        const slotLeftMM = relLeftPx * mmPerPx;
        const slotWidthMM = rect.width * mmPerPx;
        const parentAlign = el.parentElement ? activeWin.getComputedStyle(el.parentElement).textAlign : '';
        const rawLeftMM = parentAlign === 'center' ? slotLeftMM + (slotWidthMM - 58) / 2 : slotLeftMM;
        const rawTopMM = slotTopInPageMM + Math.max(0, (slotHeightMM - 18) / 2);
        const clampedLeftMM = Math.max(0, Math.min(rawLeftMM, 210 - 58));
        const clampedTopMM = Math.max(0, Math.min(rawTopMM, 297 - 18));

        seen[slotNum] = true;
        slots.push({
          slot: slotNum,
          page: page,
          x: Number((clampedLeftMM / 210).toFixed(6)),
          y: Number((clampedTopMM / 297).toFixed(6))
        });
      }
    });

    slots.sort(function (a, b) {
      return a.slot - b.slot;
    });

    return slots;
  }

  async function generatePdfWithSlots(sheetEl) {
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

    let output = null;

    try {
      const iframeWin = iframe.contentWindow;
      const iframeDoc = iframe.contentDocument || iframeWin.document;
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
      await waitForAssets(iframeDoc.body, iframeDoc);

      const captureWidth = targetEl.offsetWidth || 794;
      const pageHeightPx = (captureWidth * 297) / 210;

      applyPageBreakAvoidance(targetEl, pageHeightPx, iframeWin);

      const captureHeight = Math.max(targetEl.offsetHeight, targetEl.scrollHeight, Math.ceil(pageHeightPx));

      iframe.style.height = captureHeight + 'px';

      const estimatedPages = Math.max(1, Math.ceil(((captureHeight * 210 / captureWidth) - 1) / 297));
      const extractedSlots = extractSignatureSlots(targetEl, captureWidth, estimatedPages, iframeWin);

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
      let renderedPages = 1;

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
          renderedPages += 1;
          pdf.addImage(imgData, 'JPEG', 0, yOffset, imgWidth, imgHeight);
          remainingHeight -= pageHeight;
        }
      }

      output = {
        blob: pdf.output('blob'),
        slots: extractedSlots,
        pageCount: renderedPages
      };
    } finally {
      if (iframe.parentNode) {
        iframe.parentNode.removeChild(iframe);
      }
    }

    return output;
  }

  async function generatePdfBlob(sheetEl) {
    const result = await generatePdfWithSlots(sheetEl);

    return result.blob;
  }

  window.PnjLetterRenderer = {
    replaceTags: replaceTags,
    renderIntoSheet: renderIntoSheet,
    generatePdfBlob: generatePdfBlob,
    generatePdfWithSlots: generatePdfWithSlots,
    formatIndonesianDate: formatIndonesianDate
  };
}(window, document));

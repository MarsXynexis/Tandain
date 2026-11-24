function setupSignaturePositioner(config) {
  pdfjsLib.GlobalWorkerOptions.workerSrc = '/Tandain/assets/js/pdf.worker.min.js';

  const PDF_URL = config.pdfUrl;
  const SIGNER_IDS = Array.isArray(config.signerIds) ? config.signerIds : [];
  const PT_PER_MM = 72 / 25.4;
  const SLOT_WIDTH_MM = 58;
  const SLOT_HEIGHT_MM = 18;

  const scrollContainer = document.getElementById('pdf-scroll-container');
  const container = document.getElementById('pdf-container');
  const loading = document.getElementById('pdf-loading');
  const clientError = document.getElementById('client-error');
  const boxes = {};

  function getSlotDimensionsPx(scale) {
    const pxPerMm = scale * PT_PER_MM;

    return {
      width: Math.round(SLOT_WIDTH_MM * pxPerMm),
      height: Math.round(SLOT_HEIGHT_MM * pxPerMm),
    };
  }

  function showError(msg) {
    showNotice(clientError, msg);
    clientError.scrollIntoView({
      behavior: 'smooth',
      block: 'center',
    });
  }

  function clearError() {
    hideNotice(clientError);
  }

  function getVisibleViewportBounds() {
    let top = 0;
    let bottom = window.innerHeight;

    if (scrollContainer) {
      const style = window.getComputedStyle(scrollContainer);
      const isScrollable = (style.overflowY === 'auto' || style.overflowY === 'scroll')
        && scrollContainer.scrollHeight > scrollContainer.clientHeight;

      if (isScrollable) {
        const sr = scrollContainer.getBoundingClientRect();
        top = Math.max(0, sr.top);
        bottom = Math.min(window.innerHeight, sr.bottom);
      }
    }

    return { top, bottom };
  }

  function mostVisiblePage() {
    const pages = Array.from(container.querySelectorAll('.pdf-page'));
    const bounds = getVisibleViewportBounds();
    let best = null;
    let bestVisible = -Infinity;

    pages.forEach((p) => {
      const r = p.getBoundingClientRect();
      const visible = Math.min(r.bottom, bounds.bottom) - Math.max(r.top, bounds.top);

      if (visible > bestVisible) {
        bestVisible = visible;
        best = p;
      }
    });

    return best;
  }

  function boxesOverlap(a, b) {
    let overlap = false;

    if (a && b && a !== b && a.parentElement === b.parentElement) {
      const aLeft = a.offsetLeft;
      const aTop = a.offsetTop;
      const aRight = aLeft + a.offsetWidth;
      const aBottom = aTop + a.offsetHeight;
      const bLeft = b.offsetLeft;
      const bTop = b.offsetTop;
      const bRight = bLeft + b.offsetWidth;
      const bBottom = bTop + b.offsetHeight;

      overlap = aLeft < bRight - 1 && aRight > bLeft + 1 && aTop < bBottom - 1 && aBottom > bTop + 1;
    }

    return overlap;
  }

  function findOverlappingSignerIds() {
    const overlappingIds = new Set();
    const ids = Object.keys(boxes);

    for (let i = 0; i < ids.length; i++) {
      for (let j = i + 1; j < ids.length; j++) {
        if (boxesOverlap(boxes[ids[i]], boxes[ids[j]])) {
          overlappingIds.add(ids[i]);
          overlappingIds.add(ids[j]);
        }
      }
    }

    return overlappingIds;
  }

  function refreshOverlapState() {
    const overlappingIds = findOverlappingSignerIds();

    Object.entries(boxes).forEach(([id, box]) => {
      const isOverlapping = overlappingIds.has(id);
      box.classList.toggle('border-red-600', isOverlapping);
      box.classList.toggle('bg-red-100/70', isOverlapping);
      box.classList.toggle('text-red-900', isOverlapping);
      box.classList.toggle('border-blue-600', !isOverlapping);
      box.classList.toggle('bg-blue-100/70', !isOverlapping);
      box.classList.toggle('text-blue-900', !isOverlapping);
    });
  }

  function setStatus(signerId, placed) {
    const card = document.querySelector('[data-signer-card="' + signerId + '"]');

    if (card) {
      card.querySelector('[data-status]').textContent = placed ? 'Sudah ditempatkan' : 'Belum ditempatkan';
      card.querySelector('[data-status]').classList.toggle('text-green-700', placed);
      card.querySelector('.add-box').classList.toggle('hidden', placed);
      card.querySelector('.remove-box').classList.toggle('hidden', !placed);
      card.querySelector('.remove-box').classList.toggle('flex-1', placed);
    }
  }

  function makeDraggable(el) {
    el.addEventListener('pointerdown', (e) => {
      e.preventDefault();
      el.setPointerCapture(e.pointerId);

      const rect = el.getBoundingClientRect();
      const offsetX = e.clientX - rect.left;
      const offsetY = e.clientY - rect.top;

      const move = (ev) => {
        const pages = Array.from(container.querySelectorAll('.pdf-page'));
        let target = el.parentElement;

        for (const p of pages) {
          const r = p.getBoundingClientRect();

          if (ev.clientX >= r.left && ev.clientX <= r.right && ev.clientY >= r.top && ev.clientY <= r.bottom) {
            if (p.dataset.tooSmall !== '1') {
              target = p;
            }
            break;
          }
        }

        if (target !== el.parentElement) {
          const targetScale = Number(target.dataset.scale || 1.3);
          const dims = getSlotDimensionsPx(targetScale);
          el.style.width = Math.min(dims.width, target.clientWidth) + 'px';
          el.style.height = Math.min(dims.height, target.clientHeight) + 'px';
          target.appendChild(el);
        }

        const tr = target.getBoundingClientRect();
        let x = ev.clientX - tr.left - offsetX;
        let y = ev.clientY - tr.top - offsetY;
        x = Math.max(0, Math.min(x, target.clientWidth - el.offsetWidth));
        y = Math.max(0, Math.min(y, target.clientHeight - el.offsetHeight));
        el.style.left = x + 'px';
        el.style.top = y + 'px';
        refreshOverlapState();
      };

      const up = () => {
        el.removeEventListener('pointermove', move);
        el.removeEventListener('pointerup', up);
        el.removeEventListener('pointercancel', up);
        refreshOverlapState();
      };

      el.addEventListener('pointermove', move);
      el.addEventListener('pointerup', up);
      el.addEventListener('pointercancel', up);
    });
  }

  pdfjsLib.getDocument(PDF_URL).promise
    .then(async (pdf) => {
      loading.remove();

      for (let n = 1; n <= pdf.numPages; n++) {
        const page = await pdf.getPage(n);
        const baseViewport = page.getViewport({ scale: 1 });
        const pageWidthMm = baseViewport.width / PT_PER_MM;
        const pageHeightMm = baseViewport.height / PT_PER_MM;
        const availableWidth = container.clientWidth;
        const scale = Math.max(0.1, Math.min(1.3, availableWidth / baseViewport.width));
        const viewport = page.getViewport({ scale });

        const wrap = document.createElement('div');
        wrap.className = 'pdf-page relative mx-auto shadow border bg-white';
        wrap.dataset.page = n;
        wrap.dataset.scale = String(scale);
        wrap.dataset.tooSmall = (pageWidthMm < SLOT_WIDTH_MM || pageHeightMm < SLOT_HEIGHT_MM) ? '1' : '0';
        wrap.style.width = viewport.width + 'px';
        wrap.style.height = viewport.height + 'px';

        const canvas = document.createElement('canvas');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        canvas.style.display = 'block';
        wrap.appendChild(canvas);
        container.appendChild(wrap);

        await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
      }
    })
    .catch(() => {
      loading.textContent = 'Gagal memuat dokumen. Pastikan berkas PDF valid.';
      loading.classList.add('text-error');
    });

  document.querySelectorAll('.add-box').forEach((btn) => {
    btn.addEventListener('click', () => {
      clearError();
      const id = btn.dataset.signer;

      if (!boxes[id]) {
        const wrap = mostVisiblePage();

        if (!wrap) {
          showError('Dokumen belum selesai dimuat.');
        } else if (wrap.dataset.tooSmall === '1') {
          showError('Halaman PDF terlalu kecil untuk kotak TTE.');
        } else {
          const pageScale = Number(wrap.dataset.scale || 1.3);
          const dims = getSlotDimensionsPx(pageScale);
          const boxW = Math.min(dims.width, wrap.clientWidth);
          const boxH = Math.min(dims.height, wrap.clientHeight);

          const box = document.createElement('div');
          box.className = 'absolute border-2 border-blue-600 bg-blue-100/70 text-xs font-semibold text-blue-900 flex items-center justify-center text-center px-1 cursor-move select-none z-10';
          box.style.width = boxW + 'px';
          box.style.height = boxH + 'px';

          const bounds = getVisibleViewportBounds();
          const r = wrap.getBoundingClientRect();
          const maxLeft = Math.max(0, wrap.clientWidth - boxW);
          const maxTop = Math.max(0, wrap.clientHeight - boxH);
          const baseLeft = Math.max(0, (wrap.clientWidth - boxW) / 2);
          const baseTop = Math.max(0, Math.min(Math.max(0, bounds.top - r.top) + 40, maxTop));
          const pageBoxes = Object.values(boxes).filter((existing) => existing.parentElement === wrap);
          let placedLeft = Math.min(baseLeft, maxLeft);
          let placedTop = baseTop;
          const stepY = boxH + 12;

          for (let attempt = 0; attempt < 15; attempt++) {
            const candidateTop = Math.min(maxTop, baseTop + attempt * stepY);
            const hitsExisting = pageBoxes.some((existing) => {
              const eLeft = existing.offsetLeft;
              const eTop = existing.offsetTop;
              const eRight = eLeft + existing.offsetWidth;
              const eBottom = eTop + existing.offsetHeight;

              return placedLeft < eRight - 1 && (placedLeft + boxW) > eLeft + 1 && candidateTop < eBottom - 1 && (candidateTop + boxH) > eTop + 1;
            });

            if (!hitsExisting) {
              placedTop = candidateTop;
              break;
            }
          }

          box.style.left = placedLeft + 'px';
          box.style.top = placedTop + 'px';

          box.textContent = btn.dataset.label;
          wrap.appendChild(box);
          boxes[id] = box;
          makeDraggable(box);
          setStatus(id, true);
          refreshOverlapState();
        }
      }
    });
  });

  document.querySelectorAll('.remove-box').forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.signer;

      if (boxes[id]) {
        boxes[id].remove();
        delete boxes[id];
      }

      setStatus(id, false);
      refreshOverlapState();
    });
  });

  document.getElementById('positions-form').addEventListener('submit', (e) => {
    clearError();

    const missing = SIGNER_IDS.filter((id) => !boxes[id]);
    const hasTooSmallPage = SIGNER_IDS.some((id) => boxes[id] && boxes[id].parentElement.dataset.tooSmall === '1');
    const overlapping = findOverlappingSignerIds();

    if (missing.length > 0) {
      e.preventDefault();
      showError('Tempatkan semua kotak TTE.');
    } else if (hasTooSmallPage) {
      e.preventDefault();
      showError('Halaman PDF terlalu kecil untuk kotak TTE.');
    } else if (overlapping.size > 0) {
      e.preventDefault();
      refreshOverlapState();
      showError('Kotak TTE bertumpuk. Geser salah satunya.');
    } else {
      const data = SIGNER_IDS.map((signerId) => {
        const el = boxes[signerId];
        const p = el.parentElement;

        return {
          signer_id: signerId,
          page: parseInt(p.dataset.page, 10),
          x: el.offsetLeft / p.clientWidth,
          y: el.offsetTop / p.clientHeight,
        };
      });

      document.getElementById('positions').value = JSON.stringify(data);
    }
  });
}

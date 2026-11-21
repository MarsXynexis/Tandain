<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

  <div>
    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight font-bold">Atur Posisi TTE</h1>
    <p class="font-body-md text-body-md text-on-surface-variant mt-1">Klik nama penandatangan untuk memunculkan kotak TTE, lalu geser ke posisi yang diinginkan pada dokumen</p>
  </div>

  <div class="hidden space-y-3 [&:has([data-notice]:not([hidden]))]:block">
    <?php require 'views/layouts/flash.php'; ?>

    <?php $notice = ['type' => 'error', 'id' => 'client-error']; ?>
    <?php require 'views/layouts/notice.php'; ?>
  </div>

  <div class="flex flex-col lg:flex-row gap-4 items-start">

    <div class="flex-1 min-w-0 w-full max-w-none lg:sticky lg:top-20 lg:h-[calc(100dvh-6rem)] lg:overflow-y-auto">
      <div class="text-sm text-on-surface-variant py-6 text-center" id="pdf-loading">Memuat dokumen...</div>
      <div id="pdf-container" class="space-y-4 overflow-x-auto pb-4"></div>
    </div>

    <div class="w-full lg:w-72 shrink-0 space-y-2 lg:sticky lg:top-20">

      <h2 class="font-headline-sm text-headline-sm text-primary font-semibold mb-1">Penandatangan</h2>

      <?php foreach ($signers as $i => $s): ?>
        <div class="p-3 rounded-lg border border-outline-variant bg-surface-container-lowest" data-signer-card="<?= htmlspecialchars($s['id']) ?>">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs shrink-0"><?= $i + 1 ?></div>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-bold text-primary truncate"><?= htmlspecialchars($s['name']) ?></p>
              <p class="text-xs text-on-surface-variant" data-status>Belum ditempatkan</p>
            </div>
          </div>
          <div class="flex gap-2 mt-3">
            <button type="button"
              class="btn-md inline-flex add-box flex-1 bg-brand hover:bg-brand-hover text-white font-semibold transition"
              data-signer="<?= htmlspecialchars($s['id']) ?>"
              data-stamp-type="<?= htmlspecialchars($s['stamp_type'] ?? 'DUAL_DISPLAY') ?>"
              data-label="<?= $i + 1 ?>. <?= htmlspecialchars($s['name']) ?>">
              Tempatkan kotak
            </button>
            <button type="button"
              class="btn-md remove-box hidden border border-outline-variant text-error font-semibold hover:bg-error-container/40 transition"
              data-signer="<?= htmlspecialchars($s['id']) ?>">
              Hapus
            </button>
          </div>
        </div>
      <?php endforeach; ?>

      <p class="text-xs text-on-surface-variant pt-1">Kotak muncul di halaman yang sedang terlihat dan bisa digeser ke halaman mana pun.</p>
      <div class="flex flex-wrap items-center justify-between gap-3 pt-4 mt-4 border-t border-outline-variant">
        <form method="POST" action="/Tandain/document/delete/<?= htmlspecialchars($documentId) ?>" class="inline" data-confirm-form>
          <button type="submit" class="btn-md inline-flex items-center gap-1 border border-outline-variant text-[#b91c1c] hover:bg-red-50 transition-colors" title="Hapus Draf">
            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">delete</span>
            <span>Hapus</span>
          </button>
        </form>

        <form id="positions-form" method="POST" action="/Tandain/document/savePositions/<?= htmlspecialchars($documentId) ?>">
          <input type="hidden" name="positions" id="positions">
          <button class="btn-md bg-brand hover:bg-brand-hover text-white font-semibold shadow-sm flex items-center gap-2 transition active:scale-[0.99]" type="submit">
            <span>Simpan &amp; Ajukan</span>
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
          </button>
      </div>
      </form>
    </div>



  </div>



</main>

<script src="/Tandain/assets/js/pdf.min.js"></script>
<script>
  pdfjsLib.GlobalWorkerOptions.workerSrc = '/Tandain/assets/js/pdf.worker.min.js';

  const PDF_URL = <?= json_encode($pdfUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  const SIGNER_IDS = <?= json_encode(array_column($signers, 'id'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  const PT_PER_MM = 72 / 25.4;

  const container = document.getElementById('pdf-container');
  const loading = document.getElementById('pdf-loading');
  const clientError = document.getElementById('client-error');
  const boxes = {};

  function getStampDimensionsPx(stampType, scale) {
    const pxPerMm = scale * PT_PER_MM;
    let widthMm = 58;
    let heightMm = 18;

    if (stampType === 'QR_ONLY') {
      widthMm = 18;
    } else if (stampType === 'SIGNATURE_ONLY') {
      widthMm = 46;
    }

    return {
      width: Math.round(widthMm * pxPerMm),
      height: Math.round(heightMm * pxPerMm),
    };
  }

  function showError(msg) {
    showNotice(clientError, msg);
    clientError.scrollIntoView({
      behavior: 'smooth',
      block: 'center'
    });
  }

  function clearError() {
    hideNotice(clientError);
  }

  pdfjsLib.getDocument(PDF_URL).promise
    .then(async (pdf) => {
      loading.remove();

      for (let n = 1; n <= pdf.numPages; n++) {
        const page = await pdf.getPage(n);
        const baseViewport = page.getViewport({
          scale: 1
        });
        const availableWidth = container.clientWidth;
        const scale = Math.max(0.1, Math.min(1.3, availableWidth / baseViewport.width));
        const viewport = page.getViewport({
          scale
        });

        const wrap = document.createElement('div');
        wrap.className = 'pdf-page relative mx-auto shadow border bg-white';
        wrap.dataset.page = n;
        wrap.dataset.scale = String(scale);
        wrap.style.width = viewport.width + 'px';
        wrap.style.height = viewport.height + 'px';

        const canvas = document.createElement('canvas');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        canvas.style.display = 'block';
        wrap.appendChild(canvas);
        container.appendChild(wrap);

        await page.render({
          canvasContext: canvas.getContext('2d'),
          viewport
        }).promise;
      }
    })
    .catch(() => {
      loading.textContent = 'Gagal memuat dokumen. Pastikan berkas PDF valid.';
      loading.classList.add('text-error');
    });

  function mostVisiblePage() {
    const pages = Array.from(container.querySelectorAll('.pdf-page'));
    let best = null;
    let bestVisible = -1;

    pages.forEach((p) => {
      const r = p.getBoundingClientRect();
      const visible = Math.min(r.bottom, window.innerHeight) - Math.max(r.top, 0);

      if (visible > bestVisible) {
        bestVisible = visible;
        best = p;
      }
    });

    return best;
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

  document.querySelectorAll('.add-box').forEach((btn) => {
    btn.addEventListener('click', () => {
      clearError();
      const id = btn.dataset.signer;

      if (!boxes[id]) {
        const wrap = mostVisiblePage();

        if (!wrap) {
          showError('Dokumen belum selesai dimuat.');
        } else {
          const pageScale = Number(wrap.dataset.scale || 1.3);
          const stampType = btn.dataset.stampType || 'DUAL_DISPLAY';
          const dims = getStampDimensionsPx(stampType, pageScale);
          const boxW = Math.min(dims.width, wrap.clientWidth);
          const boxH = Math.min(dims.height, wrap.clientHeight);

          const box = document.createElement('div');
          box.className = 'absolute border-2 border-blue-600 bg-blue-100/70 text-xs font-semibold text-blue-900 flex items-center justify-center text-center px-1 cursor-move select-none z-10';
          box.dataset.stampType = stampType;
          box.style.width = boxW + 'px';
          box.style.height = boxH + 'px';
          box.style.left = Math.max(0, (wrap.clientWidth - boxW) / 2) + 'px';

          const r = wrap.getBoundingClientRect();
          const visibleTop = Math.max(0, -r.top) + 40;
          box.style.top = Math.min(visibleTop, Math.max(0, wrap.clientHeight - boxH)) + 'px';

          box.textContent = btn.dataset.label;
          wrap.appendChild(box);
          boxes[id] = box;
          makeDraggable(box);
          setStatus(id, true);
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
    });
  });

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
            target = p;
            break;
          }
        }

        if (target !== el.parentElement) {
          const targetScale = Number(target.dataset.scale || 1.3);
          const dims = getStampDimensionsPx(el.dataset.stampType || 'DUAL_DISPLAY', targetScale);
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
      };

      const up = () => {
        el.removeEventListener('pointermove', move);
        el.removeEventListener('pointerup', up);
        el.removeEventListener('pointercancel', up);
      };

      el.addEventListener('pointermove', move);
      el.addEventListener('pointerup', up);
      el.addEventListener('pointercancel', up);
    });
  }

  document.getElementById('positions-form').addEventListener('submit', (e) => {
    clearError();

    const missing = SIGNER_IDS.filter((id) => !boxes[id]);

    if (missing.length > 0) {
      e.preventDefault();
      showError('Masih ada penandatangan yang belum ditempatkan kotak TTE-nya.');
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
</script>
<?php $confirmation = ['title' => 'Hapus Draf Dokumen', 'message' => 'Hapus draf dokumen ini secara permanen?', 'action' => 'Hapus Draf']; ?>
<?php require 'views/layouts/confirm.php'; ?>
<?php require_once 'views/layouts/footer.php'; ?>
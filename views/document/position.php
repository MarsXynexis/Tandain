<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

  <main class="flex-1 min-w-0 w-full max-w-none p-5 lg:p-6">

    <div class="mb-5">
      <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight font-bold">Atur Posisi TTE</h1>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1">Klik nama penandatangan untuk memunculkan kotak TTE, lalu geser ke posisi yang diinginkan pada dokumen.</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-300 text-red-700 text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="hidden mb-4 p-3 rounded-lg bg-red-50 border border-red-300 text-red-700 text-sm" id="client-error" role="alert"></div>

    <div class="flex flex-col lg:flex-row gap-4 items-start">

      
      <aside class="w-full lg:w-72 shrink-0 space-y-2 lg:sticky lg:top-4">
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
                class="add-box flex-1 px-3 py-1.5 rounded-lg bg-[#004B87] hover:bg-[#003d6d] text-white text-xs font-semibold transition"
                data-signer="<?= htmlspecialchars($s['id']) ?>"
                data-label="<?= $i + 1 ?>. <?= htmlspecialchars($s['name']) ?>">
                Tempatkan kotak
              </button>
              <button type="button"
                class="remove-box hidden px-3 py-1.5 rounded-lg border border-outline-variant text-error text-xs font-semibold hover:bg-error-container/40 transition"
                data-signer="<?= htmlspecialchars($s['id']) ?>">
                Hapus
              </button>
            </div>
          </div>
        <?php endforeach; ?>

        <p class="text-xs text-on-surface-variant pt-1">Kotak muncul di halaman yang sedang terlihat dan bisa digeser ke halaman mana pun.</p>
      </aside>

      
      <div class="flex-1 min-w-0 w-full">
        <div class="text-sm text-on-surface-variant py-6 text-center" id="pdf-loading">Memuat dokumen...</div>
        <div id="pdf-container" class="space-y-4 overflow-x-auto pb-4"></div>
      </div>

    </div>

    <form id="positions-form" method="POST" action="/Tandain/document/savePositions/<?= htmlspecialchars($documentId) ?>">
      <input type="hidden" name="positions" id="positions">

      <div class="flex justify-between items-center pt-4 mt-4 border-t border-outline-variant">
        <a class="border border-outline-variant text-on-surface-variant hover:bg-surface-container hover:text-on-surface px-6 py-2.5 rounded-lg font-label-lg text-label-lg font-medium transition inline-block" href="/Tandain/document">
          Batal
        </a>

        <button class="bg-[#004B87] hover:bg-[#003d6d] text-white px-8 py-2.5 rounded-lg font-label-lg text-label-lg font-semibold shadow-sm flex items-center gap-2 transition active:scale-[0.99]" type="submit">
          <span>Simpan &amp; Ajukan</span>
          <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
        </button>
      </div>
    </form>

  </main>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <script>
    pdfjsLib.GlobalWorkerOptions.workerSrc =
      'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    const PDF_URL = <?= json_encode($pdfUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const SIGNER_IDS = <?= json_encode(array_column($signers, 'id'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    const BOX_W = 160;
    const BOX_H = 60;

    const container = document.getElementById('pdf-container');
    const loading = document.getElementById('pdf-loading');
    const clientError = document.getElementById('client-error');
    const boxes = {};

    function showError(msg) {
      clientError.textContent = msg;
      clientError.classList.remove('hidden');
      clientError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function clearError() {
      clientError.classList.add('hidden');
      clientError.textContent = '';
    }

    pdfjsLib.getDocument(PDF_URL).promise
      .then(async (pdf) => {
        loading.remove();
        for (let n = 1; n <= pdf.numPages; n++) {
          const page = await pdf.getPage(n);
          const viewport = page.getViewport({ scale: 1.3 });

          const wrap = document.createElement('div');
          wrap.className = 'pdf-page relative mx-auto shadow border bg-white';
          wrap.dataset.page = n;
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
      if (!card) return;
      card.querySelector('[data-status]').textContent = placed ? 'Sudah ditempatkan' : 'Belum ditempatkan';
      card.querySelector('[data-status]').classList.toggle('text-green-700', placed);
      card.querySelector('.add-box').classList.toggle('hidden', placed);
      card.querySelector('.remove-box').classList.toggle('hidden', !placed);
      card.querySelector('.remove-box').classList.toggle('flex-1', placed);
    }

    document.querySelectorAll('.add-box').forEach((btn) => {
      btn.addEventListener('click', () => {
        clearError();
        const id = btn.dataset.signer;
        if (boxes[id]) return;

        const wrap = mostVisiblePage();
        if (!wrap) {
          showError('Dokumen belum selesai dimuat.');
          return;
        }

        const box = document.createElement('div');
        box.className = 'absolute border-2 border-blue-600 bg-blue-100/70 text-xs font-semibold text-blue-900 flex items-center justify-center text-center px-1 cursor-move select-none z-10';
        box.style.width = BOX_W + 'px';
        box.style.height = BOX_H + 'px';
        box.style.left = Math.max(0, (wrap.clientWidth - BOX_W) / 2) + 'px';

        const r = wrap.getBoundingClientRect();
        const visibleTop = Math.max(0, -r.top) + 40;
        box.style.top = Math.min(visibleTop, wrap.clientHeight - BOX_H) + 'px';

        box.textContent = btn.dataset.label;
        wrap.appendChild(box);
        boxes[id] = box;
        makeDraggable(box);
        setStatus(id, true);
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
        return;
      }

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
    });
  </script>

<?php require_once 'views/layouts/footer.php'; ?>
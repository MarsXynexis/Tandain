<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php $signatureUserName = $data['user']['full_name'] ?? 'Pengguna'; ?>

  <main class="flex-1 w-full p-8 flex flex-col gap-6">
    <section class="flex flex-col gap-1">
      <h1 class="font-headline-lg text-headline-lg font-bold tracking-tight text-primary">Gambar Tanda Tangan</h1>
      <p class="font-body-md text-body-md text-on-surface-variant">Kelola spesimen goresan tanda tangan visual untuk pembubuhan dokumen.</p>
    </section>

    <section class="flex items-center gap-6 border border-[#cbd5e1] bg-[#dbeafe] p-6 text-on-surface-variant">
      <span class="material-symbols-outlined shrink-0 text-[32px] text-[#004b87]" aria-hidden="true">info</span>
      <p class="font-body-md text-body-md font-semibold">
        Gambar Tanda Tangan digunakan sebagai representasi visual pada berkas yang disetujui. Pastikan Anda mengunggah gambar berlatar belakang transparan (PNG) atau menggores langsung pada kanvas agar hasil stempel pada surat resmi kampus terlihat rapi dan jelas.
      </p>
    </section>

    <section class="flex flex-col gap-6 rounded-lg border border-surface-container-high bg-surface-container-lowest p-5 shadow-sm md:p-6">
      <header class="border-b border-surface-container-high pb-4">
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-[20px] text-secondary">signature</span>
          <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Tanda Tangan Saya</h2>
      </div>  
        <p class="font-body-sm text-body-sm text-on-surface-variant">Spesimen gambar yang secara otomatis disematkan pada dokumen yang akan ditandatangani.</p>
      </header>

      <div class="flex flex-col gap-2">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <span class="inline-flex items-center gap-1.5 rounded-sm border border-[#bbf7d0] bg-[#dcfce7] px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-[#15803d]">
            <span class="material-symbols-outlined text-[14px]" aria-hidden="true">verified</span>
            Tanda Tangan Aktif Digunakan
          </span>
          <span class="font-label-md text-label-md text-outline">Dimensi: 1080 × 480 px</span>
        </div>

        <div class="relative flex min-h-[280px] flex-wrap items-center justify-center gap-6 overflow-hidden rounded border border-outline-variant bg-[#f1f5f9] p-6 shadow-inner">
          <div class="flex h-40 w-full max-w-[448px] items-center justify-center bg-white shadow-sm">
            <img id="signature-preview" class="max-h-24 max-w-[240px] object-contain" src="/Tandain/assets/img/signature-sample.png" alt="Pratinjau tanda tangan">
          </div>

          <div class="flex w-full max-w-[448px] items-center justify-center bg-white p-4 shadow-sm">
            <div class="relative h-32 w-32 shrink-0" aria-label="Kode QR verifikasi">
              <img class="absolute left-0 top-0 h-32 w-32" src="/Tandain/assets/img/signature-qr-base.svg" alt="">
              <img class="absolute left-[3px] top-[3px] h-[122.435px] w-[122.433px]" src="/Tandain/assets/img/signature-qr-pattern.svg" alt="">
              <img class="absolute left-[3px] top-[3px] h-[25.971px] w-[25.971px]" src="/Tandain/assets/img/signature-qr-corner-top-left.svg" alt="">
              <img class="absolute left-[10px] top-[10px] h-[11.13px] w-[11.13px]" src="/Tandain/assets/img/signature-qr-dot-top-left.svg" alt="">
              <img class="absolute left-[99px] top-[3px] h-[25.971px] w-[25.971px]" src="/Tandain/assets/img/signature-qr-corner-top-right.svg" alt="">
              <img class="absolute left-[107px] top-[10px] h-[11.13px] w-[11.13px]" src="/Tandain/assets/img/signature-qr-dot-top-right.svg" alt="">
              <img class="absolute left-[3px] top-[99px] h-[25.971px] w-[25.971px]" src="/Tandain/assets/img/signature-qr-corner-bottom-left.svg" alt="">
              <img class="absolute left-[10px] top-[107px] h-[11.13px] w-[11.13px]" src="/Tandain/assets/img/signature-qr-dot-bottom-left.svg" alt="">
            </div>
            <div class="ml-1 flex min-h-[128px] min-w-0 flex-col items-center justify-center border-y border-r border-black px-2">
              <img class="h-[85px] w-[212px] max-w-full object-contain" src="/Tandain/assets/img/signature-sample.png" alt="Contoh tanda tangan digital">
              <span class="self-start text-[10px] text-black">Digitally Signed | Tandain.pnj.ac.id</span>
            </div>
          </div>

          <div class="absolute bottom-3 right-3 inline-flex items-center gap-1.5 rounded-sm border border-outline-variant/60 bg-white/90 px-3 py-1.5 backdrop-blur">
            <span class="material-symbols-outlined text-[14px] text-secondary" aria-hidden="true">lock</span>
            <span class="max-w-[200px] truncate font-label-md text-label-md font-medium text-primary"><?= htmlspecialchars($signatureUserName, ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
        </div>
      </div>
    </section>

    <section class="flex flex-col gap-6 rounded-lg border border-surface-container-high bg-surface-container-lowest p-5 shadow-sm md:p-6">
      <header class="border-b border-surface-container-high pb-4">

        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-secondary" aria-hidden="true">edit</span>
            <h2 class="font-headline-sm text-headline-sm font-semibold text-primary">Ganti Tanda Tangan</h2>
        </div>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Spesimen gambar yang secara otomatis disematkan pada dokumen yang akan ditandatangani.</p>
      </header>

      <div class="flex flex-col gap-4 pt-2">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <h3 class="font-label-lg text-label-lg font-semibold text-primary">Metode Penggantian</h3>
          <div class="inline-flex rounded border border-outline-variant/50 bg-surface-container-high p-1" role="tablist" aria-label="Metode penggantian tanda tangan">
            <button id="upload-tab" class="rounded-md bg-white px-4 py-1.5 font-label-md text-label-md font-semibold text-primary shadow-sm" type="button" role="tab" aria-selected="true" aria-controls="upload-panel">Unggah Berkas</button>
            <button id="draw-tab" class="rounded-md px-4 py-1.5 font-label-md text-label-md font-medium text-on-surface-variant" type="button" role="tab" aria-selected="false" aria-controls="draw-panel">Gores Langsung (Draw Canvas)</button>
          </div>
        </div>

        <div id="upload-panel" role="tabpanel" aria-labelledby="upload-tab">
          <label id="upload-dropzone" class="flex min-h-[146px] cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-outline-variant bg-surface-container-low p-6 text-center transition-colors hover:border-secondary">
            <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-surface-container-high">
              <span class="material-symbols-outlined text-[24px] text-primary" aria-hidden="true">cloud_upload</span>
            </span>
            <span class="font-body-md text-body-md font-medium text-primary">Tarik berkas PNG transparan ke sini atau <span class="font-semibold text-secondary underline">pilih berkas dari komputer</span>.</span>
            <span class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Maksimal 2MB, latar belakang transparan.</span>
            <input id="signature-file" class="sr-only" type="file" accept="image/png">
          </label>
        </div>

        <div id="draw-panel" class="hidden flex-col gap-3" role="tabpanel" aria-labelledby="draw-tab" hidden>
          <div class="overflow-hidden rounded-lg border-2 border-dashed border-outline-variant bg-white">
            <canvas id="signature-canvas" class="block h-40 w-full touch-none cursor-crosshair"></canvas>
          </div>
          <div class="flex items-center justify-between gap-3">
            <p class="font-body-sm text-body-sm text-on-surface-variant">Goreskan tanda tangan Anda pada area kanvas.</p>
            <button id="clear-canvas" class="inline-flex items-center gap-1 rounded border border-outline-variant px-3 py-1.5 font-label-md text-label-md font-medium text-on-surface hover:bg-surface-container-low" type="button">
              <span class="material-symbols-outlined text-[16px]" aria-hidden="true">ink_eraser</span>
              Hapus
            </button>
          </div>
        </div>

        <p id="signature-feedback" class="hidden font-body-sm text-body-sm text-error" role="status"></p>

        <div class="flex justify-end pt-2">
          <button id="update-signature" class="inline-flex items-center gap-2 rounded bg-[#004b87] px-6 py-2.5 font-label-lg text-label-lg font-semibold text-white shadow-sm transition-colors hover:bg-[#003865] focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2" type="button">
            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">sync</span>
            Perbarui Tanda Tangan
          </button>
        </div>
      </div>
    </section>
  </main>

  <script>
    (() => {
      const uploadTab = document.getElementById('upload-tab');
      const drawTab = document.getElementById('draw-tab');
      const uploadPanel = document.getElementById('upload-panel');
      const drawPanel = document.getElementById('draw-panel');
      const fileInput = document.getElementById('signature-file');
      const dropzone = document.getElementById('upload-dropzone');
      const canvas = document.getElementById('signature-canvas');
      const preview = document.getElementById('signature-preview');
      const feedback = document.getElementById('signature-feedback');
      const context = canvas.getContext('2d');
      let selectedImage = null;
      let drawing = false;
      let changed = false;

      const showFeedback = (message) => {
        feedback.textContent = message;
        feedback.classList.remove('hidden');
      };

      const clearFeedback = () => {
        feedback.textContent = '';
        feedback.classList.add('hidden');
      };

      const setMode = (mode) => {
        const drawMode = mode === 'draw';
        uploadPanel.hidden = drawMode;
        drawPanel.hidden = !drawMode;
        drawPanel.classList.toggle('hidden', !drawMode);
        drawPanel.classList.toggle('flex', drawMode);
        uploadTab.classList.toggle('bg-white', !drawMode);
        uploadTab.classList.toggle('text-primary', !drawMode);
        uploadTab.classList.toggle('shadow-sm', !drawMode);
        uploadTab.classList.toggle('text-on-surface-variant', drawMode);
        uploadTab.setAttribute('aria-selected', String(!drawMode));
        drawTab.classList.toggle('bg-white', drawMode);
        drawTab.classList.toggle('text-primary', drawMode);
        drawTab.classList.toggle('shadow-sm', drawMode);
        drawTab.classList.toggle('text-on-surface-variant', !drawMode);
        drawTab.setAttribute('aria-selected', String(drawMode));
        changed = false;
        clearFeedback();
      };

      const loadFile = (file) => {
        clearFeedback();
        if (!file) return;
        if (file.type !== 'image/png') {
          showFeedback('Pilih berkas PNG.');
          fileInput.value = '';
          return;
        }
        if (file.size > 2 * 1024 * 1024) {
          showFeedback('Ukuran berkas maksimal 2MB.');
          fileInput.value = '';
          return;
        }

        const reader = new FileReader();
        reader.onload = () => {
          const image = new Image();
          image.onload = () => {
            selectedImage = image;
            changed = true;
            showFeedback('Berkas siap dipratinjau. Klik “Perbarui Tanda Tangan” untuk memperbarui pratinjau.');
          };
          image.onerror = () => showFeedback('Berkas PNG tidak dapat dibaca.');
          image.src = reader.result;
        };
        reader.onerror = () => showFeedback('Berkas tidak dapat dibaca. Silakan pilih ulang.');
        reader.readAsDataURL(file);
      };

      const resizeCanvas = () => {
        const bounds = canvas.getBoundingClientRect();
        const ratio = window.devicePixelRatio || 1;
        const imageData = canvas.width && canvas.height ? canvas.toDataURL() : null;
        canvas.width = Math.max(1, Math.round(bounds.width * ratio));
        canvas.height = Math.max(1, Math.round(bounds.height * ratio));
        context.setTransform(ratio, 0, 0, ratio, 0, 0);
        context.lineWidth = 2;
        context.lineCap = 'round';
        context.lineJoin = 'round';
        context.strokeStyle = '#00021c';
        if (imageData) {
          const previous = new Image();
          previous.onload = () => context.drawImage(previous, 0, 0, bounds.width, bounds.height);
          previous.src = imageData;
        }
      };

      const pointFromEvent = (event) => {
        const bounds = canvas.getBoundingClientRect();
        return { x: event.clientX - bounds.left, y: event.clientY - bounds.top };
      };

      uploadTab.addEventListener('click', () => setMode('upload'));
      drawTab.addEventListener('click', () => {
        setMode('draw');
        selectedImage = null;
        requestAnimationFrame(resizeCanvas);
      });
      fileInput.addEventListener('change', () => loadFile(fileInput.files[0]));
      ['dragenter', 'dragover'].forEach((eventName) => dropzone.addEventListener(eventName, (event) => {
        event.preventDefault();
        dropzone.classList.add('border-secondary', 'bg-[#eaf2ff]');
      }));
      ['dragleave', 'drop'].forEach((eventName) => dropzone.addEventListener(eventName, (event) => {
        event.preventDefault();
        dropzone.classList.remove('border-secondary', 'bg-[#eaf2ff]');
      }));
      dropzone.addEventListener('drop', (event) => loadFile(event.dataTransfer.files[0]));
      canvas.addEventListener('pointerdown', (event) => {
        drawing = true;
        changed = true;
        canvas.setPointerCapture(event.pointerId);
        const point = pointFromEvent(event);
        context.beginPath();
        context.moveTo(point.x, point.y);
      });
      canvas.addEventListener('pointermove', (event) => {
        if (!drawing) return;
        const point = pointFromEvent(event);
        context.lineTo(point.x, point.y);
        context.stroke();
      });
      ['pointerup', 'pointercancel', 'lostpointercapture'].forEach((eventName) => {
        canvas.addEventListener(eventName, () => {
          drawing = false;
          context.closePath();
        });
      });
      document.getElementById('clear-canvas').addEventListener('click', () => {
        context.clearRect(0, 0, canvas.width, canvas.height);
        changed = false;
        selectedImage = null;
        clearFeedback();
      });
      document.getElementById('update-signature').addEventListener('click', () => {
        clearFeedback();
        if (!changed) {
          showFeedback('Unggah berkas PNG atau buat tanda tangan di kanvas terlebih dahulu.');
          return;
        }
        if (selectedImage) {
          preview.src = selectedImage.src;
        } else {
          preview.src = canvas.toDataURL('image/png');
        }
        changed = false;
        showFeedback('Pratinjau diperbarui untuk sesi ini. Perubahan belum disimpan ke akun.');
      });

      window.addEventListener('resize', () => {
        if (!drawPanel.hidden) resizeCanvas();
      });
    })();
  </script>

<?php require_once 'views/layouts/footer.php'; ?>
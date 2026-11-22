<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar.php'; ?>
<?php require_once 'views/layouts/navbar.php'; ?>

<?php
$storedSignaturePath = $data['user']['signature_image_path'] ?? '';
$signaturePreviewUrl = '';
$hasActiveSignature = false;

if (is_string($storedSignaturePath) && preg_match('/\Auploads\/signatures\/[A-Za-z0-9._-]+\.png\z/D', $storedSignaturePath)) {
    if (is_file($storedSignaturePath)) {
        $signatureImageInfo = @getimagesize($storedSignaturePath);

        if ($signatureImageInfo && $signatureImageInfo[2] === IMAGETYPE_PNG) {
            $hasActiveSignature = true;
            $signaturePreviewUrl = '/Tandain/' . $storedSignaturePath . '?v=' . filemtime($storedSignaturePath);
        }
    }
}
?>

  <main class="flex-1 min-w-0 w-full p-6 lg:p-8 flex flex-col gap-6">

    <section class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="font-headline-lg text-headline-lg font-bold tracking-tight text-primary">Gambar Tanda Tangan</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Atur spesimen tanda tangan visual untuk pembubuhan dokumen elektronik</p>
      </div>

      <button id="toggle-editor-btn" class="btn-md <?= $hasActiveSignature ? 'inline-flex' : 'hidden'; ?> items-center justify-center gap-2 bg-brand font-semibold text-white shadow-sm transition hover:bg-brand-hover shrink-0" type="button">
        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">edit</span>
        <span>Ganti Tanda Tangan</span>
      </button>
    </section>

    <?php $notice = ['type' => 'success', 'id' => 'signature-success-banner']; ?>
    <?php require 'views/layouts/notice.php'; ?>

    <section id="signature-preview-section" class="rounded-xl border border-outline-variant bg-surface-container-lowest p-4 shadow-sm sm:p-6" <?= $hasActiveSignature ? '' : 'hidden'; ?>>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="flex flex-col gap-2.5">
          <p class="font-label-md text-label-md font-semibold text-primary">Spesimen Tanda Tangan</p>

          <div class="flex h-36 w-full items-center justify-center rounded-xl border border-outline-variant bg-white p-3">
            <img id="signature-preview" class="max-h-full max-w-full object-contain" <?= $hasActiveSignature ? 'src="' . htmlspecialchars($signaturePreviewUrl, ENT_QUOTES, 'UTF-8') . '"' : ''; ?> alt="Spesimen tanda tangan">
          </div>
        </div>

        <div class="flex flex-col gap-2.5">
          <p class="font-label-md text-label-md font-semibold text-primary">Pratinjau Stempel pada Dokumen</p>

          <div class="flex h-36 w-full items-center justify-center rounded-xl border border-outline-variant bg-surface-container-low p-3">
            <div class="flex w-full max-w-[310px] items-stretch justify-center gap-2 rounded-lg border border-outline-variant/70 bg-white p-2.5">
              <div class="flex size-16 shrink-0 flex-col items-center justify-center rounded border border-dashed border-outline-variant bg-surface-container-low text-center sm:size-[88px]" aria-label="Contoh QR verifikasi">
                <span class="material-symbols-outlined text-[30px] text-secondary" aria-hidden="true">qr_code_2</span>
                <span class="mt-0.5 font-label-md text-[10px] font-semibold text-primary">QR Dokumen</span>
              </div>

              <div class="flex min-w-0 flex-1 flex-col justify-between">
                <div class="flex items-center">
                  <span class="w-2 h-[1px] bg-black"></span>
                  <span class="px-1.5 font-mono text-[9px] font-medium text-black whitespace-nowrap leading-none">digitally signed</span>
                  <span class="flex-1 h-[1px] bg-black"></span>
                </div>

                <div class="flex-1 flex items-center justify-center border-l border-r border-black px-2 py-1">
                  <img id="signed-signature-preview" class="max-h-[48px] max-w-full object-contain" <?= $hasActiveSignature ? 'src="' . htmlspecialchars($signaturePreviewUrl, ENT_QUOTES, 'UTF-8') . '"' : ''; ?> alt="Pratinjau tanda tangan pada stempel">
                </div>

                <div class="flex items-center">
                  <span class="w-2 h-[1px] bg-black"></span>
                  <span class="px-1.5 font-mono text-[8px] text-slate-600 whitespace-nowrap leading-none">tandain.pnj.ac.id</span>
                  <span class="flex-1 h-[1px] bg-black"></span>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <section id="signature-editor-section" class="<?= $hasActiveSignature ? 'hidden' : 'flex'; ?> flex-col gap-5 rounded-xl border border-outline-variant bg-surface-container-lowest p-4 shadow-sm sm:p-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-surface-container pb-4">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-[20px] text-secondary" aria-hidden="true">draw</span>
          <h2 id="editor-heading" class="font-headline-sm text-headline-sm font-semibold text-primary"><?= $hasActiveSignature ? 'Ganti Tanda Tangan' : 'Buat Tanda Tangan'; ?></h2>
        </div>

        <div class="inline-flex flex-wrap rounded-lg border border-outline-variant/60 bg-surface-container p-1 self-start sm:self-auto" role="tablist" aria-label="Metode tanda tangan">
          <button id="draw-tab" class="btn-md inline-flex bg-white font-semibold text-primary shadow-sm transition" type="button" role="tab" aria-selected="true" aria-controls="draw-panel">Gores Langsung</button>
          <button id="upload-tab" class="btn-md inline-flex font-medium text-on-surface-variant transition" type="button" role="tab" aria-selected="false" aria-controls="upload-panel">Unggah PNG</button>
        </div>
      </div>

      <input id="signature-csrf-token" type="hidden" value="<?= htmlspecialchars($data['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

      <div id="draw-panel" class="flex flex-col gap-3" role="tabpanel" aria-labelledby="draw-tab">
        <div class="mx-auto w-full max-w-3xl flex flex-col gap-3">
          <div class="relative overflow-hidden rounded-xl border-2 border-dashed border-outline-variant bg-white h-[300px] sm:h-[340px]">
            <div class="pointer-events-none absolute inset-x-10 bottom-16 border-b border-dashed border-slate-200"></div>
            <canvas id="signature-canvas" class="relative z-10 block h-full w-full touch-none cursor-crosshair"></canvas>
          </div>

          <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
              <span class="font-label-sm text-xs text-on-surface-variant">Ketebalan pena:</span>
              <div class="inline-flex rounded-lg border border-outline-variant bg-surface-container-low p-0.5" id="stroke-width-group">
                <button type="button" data-stroke="1.3" class="btn-sm inline-flex stroke-btn font-medium text-on-surface-variant transition">Tipis</button>
                <button type="button" data-stroke="1.8" class="btn-sm inline-flex stroke-btn bg-white font-semibold text-primary shadow-sm transition">Normal</button>
                <button type="button" data-stroke="2.5" class="btn-sm inline-flex stroke-btn font-medium text-on-surface-variant transition">Tebal</button>
              </div>
            </div>

            <button id="clear-canvas" class="btn-md inline-flex items-center gap-1.5 border border-outline-variant font-medium text-on-surface hover:bg-surface-container-low transition" type="button">
              <span class="material-symbols-outlined text-[16px]" aria-hidden="true">ink_eraser</span>
              <span>Bersihkan Kanvas</span>
            </button>
          </div>
        </div>
      </div>

      <div id="upload-panel" class="hidden" role="tabpanel" aria-labelledby="upload-tab" hidden>
        <label id="upload-dropzone" class="mx-auto flex min-h-[240px] w-full max-w-3xl cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-outline-variant bg-surface-container-low p-8 text-center transition-colors hover:border-secondary">
          <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-surface-container-high">
            <span class="material-symbols-outlined text-[24px] text-primary" aria-hidden="true">cloud_upload</span>
          </span>

          <span class="font-body-md text-body-md font-medium text-primary">Tarik berkas PNG transparan ke sini atau <span class="font-semibold text-secondary underline">pilih dari perangkat</span></span>
          <span class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Format PNG latar transparan, maksimal 2 MB</span>

          <span id="upload-preview-container" class="mt-5 hidden w-full max-w-[320px] rounded-lg border border-outline-variant bg-white p-4">
            <img id="upload-preview-image" class="mx-auto max-h-32 max-w-full object-contain" alt="Pratinjau berkas tanda tangan">
          </span>

          <input id="signature-file" class="sr-only" type="file" accept="image/png">
        </label>
      </div>

      <?php $notice = ['type' => 'error', 'id' => 'signature-feedback']; ?>
      <?php require 'views/layouts/notice.php'; ?>

      <div class="flex items-center justify-end gap-3 border-t border-surface-container pt-4">
        <button id="cancel-editor-btn" class="btn-md <?= $hasActiveSignature ? 'inline-flex' : 'hidden'; ?> items-center justify-center border border-outline-variant font-medium text-on-surface-variant transition hover:bg-surface-container hover:text-on-surface" type="button">
          Batal
        </button>

        <button id="update-signature" class="btn-lg inline-flex items-center gap-2 bg-brand font-semibold text-white shadow-sm transition hover:bg-brand-hover focus:ring-2 focus:ring-secondary focus:ring-offset-2" type="button">
          <span class="material-symbols-outlined text-[18px]" aria-hidden="true">check</span>
          <span>Simpan Tanda Tangan</span>
        </button>
      </div>
    </section>

  </main>

  <script>
    (() => {
      const editorSection = document.getElementById('signature-editor-section');
      const editorHeading = document.getElementById('editor-heading');
      const toggleEditorBtn = document.getElementById('toggle-editor-btn');
      const cancelEditorBtn = document.getElementById('cancel-editor-btn');
      const successBanner = document.getElementById('signature-success-banner');

      const uploadTab = document.getElementById('upload-tab');
      const drawTab = document.getElementById('draw-tab');
      const uploadPanel = document.getElementById('upload-panel');
      const drawPanel = document.getElementById('draw-panel');
      const fileInput = document.getElementById('signature-file');
      const dropzone = document.getElementById('upload-dropzone');
      const uploadPreviewContainer = document.getElementById('upload-preview-container');
      const uploadPreviewImage = document.getElementById('upload-preview-image');

      const canvas = document.getElementById('signature-canvas');
      const strokeButtons = document.querySelectorAll('.stroke-btn');
      const previewSection = document.getElementById('signature-preview-section');
      const preview = document.getElementById('signature-preview');
      const signedPreview = document.getElementById('signed-signature-preview');

      const feedback = document.getElementById('signature-feedback');
      const updateButton = document.getElementById('update-signature');
      const context = canvas.getContext('2d');

      let hasSavedSignature = <?= $hasActiveSignature ? 'true' : 'false'; ?>;
      let currentStrokeWidth = 1.8;
      let selectedFile = null;
      let drawing = false;
      let hasCanvasStrokes = false;
      let changed = false;
      let prevPoint = null;
      let prevMid = null;

      const applyBrushSettings = () => {
        context.lineWidth = currentStrokeWidth;
        context.lineCap = 'round';
        context.lineJoin = 'round';
        context.strokeStyle = '#0f172a';
        context.fillStyle = '#0f172a';
      };

      const showFeedback = (message, type = 'error') => showNotice(feedback, message, type);

      const clearFeedback = () => hideNotice(feedback);

      const showSuccessBanner = (message) => showNotice(successBanner, message);

      const hideSuccessBanner = () => hideNotice(successBanner);

      const clearUploadPreview = () => {
        uploadPreviewImage.removeAttribute('src');
        uploadPreviewContainer.classList.add('hidden');
      };

      const clearCanvasBoard = () => {
        const ratio = window.devicePixelRatio || 1;
        context.setTransform(1, 0, 0, 1, 0, 0);
        context.clearRect(0, 0, canvas.width, canvas.height);
        context.setTransform(ratio, 0, 0, ratio, 0, 0);
        applyBrushSettings();
        hasCanvasStrokes = false;
        prevPoint = null;
        prevMid = null;
      };

      const resizeCanvas = () => {
        const bounds = canvas.getBoundingClientRect();

        if (bounds.width > 0 && bounds.height > 0) {
          const ratio = window.devicePixelRatio || 1;
          const snapshot = hasCanvasStrokes ? canvas.toDataURL('image/png') : null;
          const prevWidth = bounds.width;
          const prevHeight = bounds.height;

          canvas.width = Math.max(1, Math.round(bounds.width * ratio));
          canvas.height = Math.max(1, Math.round(bounds.height * ratio));
          context.setTransform(ratio, 0, 0, ratio, 0, 0);
          applyBrushSettings();

          if (snapshot) {
            const previous = new Image();
            previous.onload = () => {
              context.drawImage(previous, 0, 0, prevWidth, prevHeight);
            };
            previous.src = snapshot;
          }
        }
      };

      const openEditor = () => {
        hideSuccessBanner();
        clearFeedback();
        editorSection.classList.remove('hidden');
        editorSection.classList.add('flex');
        toggleEditorBtn.classList.add('hidden');
        toggleEditorBtn.classList.remove('inline-flex');

        if (!drawPanel.hidden) {
          requestAnimationFrame(() => {
            resizeCanvas();
          });
        }

        editorSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      };

      const closeEditor = () => {
        clearFeedback();
        selectedFile = null;
        fileInput.value = '';
        clearUploadPreview();
        clearCanvasBoard();
        changed = false;

        editorSection.classList.add('hidden');
        editorSection.classList.remove('flex');

        if (hasSavedSignature) {
          toggleEditorBtn.classList.remove('hidden');
          toggleEditorBtn.classList.add('inline-flex');
          cancelEditorBtn.classList.remove('hidden');
          cancelEditorBtn.classList.add('inline-flex');
          editorHeading.textContent = 'Ganti Tanda Tangan';
        }
      };

      const setActiveSignature = (imageUrl) => {
        preview.src = imageUrl;
        signedPreview.src = imageUrl;
        previewSection.hidden = false;
        hasSavedSignature = true;
      };

      const setMode = (mode) => {
        const drawMode = mode === 'draw';

        uploadPanel.hidden = drawMode;
        uploadPanel.classList.toggle('hidden', drawMode);
        drawPanel.hidden = !drawMode;
        drawPanel.classList.toggle('hidden', !drawMode);
        drawPanel.classList.toggle('flex', drawMode);

        uploadTab.classList.toggle('bg-white', !drawMode);
        uploadTab.classList.toggle('text-primary', !drawMode);
        uploadTab.classList.toggle('font-semibold', !drawMode);
        uploadTab.classList.toggle('shadow-sm', !drawMode);
        uploadTab.classList.toggle('text-on-surface-variant', drawMode);
        uploadTab.classList.toggle('font-medium', drawMode);
        uploadTab.setAttribute('aria-selected', String(!drawMode));

        drawTab.classList.toggle('bg-white', drawMode);
        drawTab.classList.toggle('text-primary', drawMode);
        drawTab.classList.toggle('font-semibold', drawMode);
        drawTab.classList.toggle('shadow-sm', drawMode);
        drawTab.classList.toggle('text-on-surface-variant', !drawMode);
        drawTab.classList.toggle('font-medium', !drawMode);
        drawTab.setAttribute('aria-selected', String(drawMode));

        clearFeedback();

        if (drawMode) {
          selectedFile = null;
          fileInput.value = '';
          clearUploadPreview();
          changed = hasCanvasStrokes;
          requestAnimationFrame(() => {
            resizeCanvas();
          });
        } else {
          changed = selectedFile !== null;
        }
      };

      const loadFile = (file) => {
        clearFeedback();

        if (file) {
          if (file.type !== 'image/png') {
            showFeedback('Berkas harus berformat PNG transparan.', 'error');
            fileInput.value = '';
            selectedFile = null;
            changed = false;
            clearUploadPreview();
          } else if (file.size > 2 * 1024 * 1024) {
            showFeedback('Ukuran berkas maksimal 2 MB.', 'error');
            fileInput.value = '';
            selectedFile = null;
            changed = false;
            clearUploadPreview();
          } else {
            selectedFile = file;
            const reader = new FileReader();

            reader.onload = () => {
              const image = new Image();

              image.onload = () => {
                changed = true;
                uploadPreviewImage.src = image.src;
                uploadPreviewContainer.classList.remove('hidden');
                showFeedback('Pratinjau berkas siap disimpan.', 'info');
              };

              image.onerror = () => {
                selectedFile = null;
                changed = false;
                clearUploadPreview();
                showFeedback('Berkas PNG tidak valid atau rusak.', 'error');
              };

              image.src = reader.result;
            };

            reader.onerror = () => {
              selectedFile = null;
              changed = false;
              clearUploadPreview();
              showFeedback('Berkas tidak dapat dibaca.', 'error');
            };

            reader.readAsDataURL(file);
          }
        }
      };

      const getCroppedCanvas = (srcCanvas) => {
        const srcCtx = srcCanvas.getContext('2d');
        const width = srcCanvas.width;
        const height = srcCanvas.height;
        const imgData = srcCtx.getImageData(0, 0, width, height).data;
        let minX = width;
        let minY = height;
        let maxX = -1;
        let maxY = -1;

        for (let y = 0; y < height; y++) {
          for (let x = 0; x < width; x++) {
            const alpha = imgData[(y * width + x) * 4 + 3];

            if (alpha > 10) {
              if (x < minX) {
                minX = x;
              }

              if (x > maxX) {
                maxX = x;
              }

              if (y < minY) {
                minY = y;
              }

              if (y > maxY) {
                maxY = y;
              }
            }
          }
        }

        let resultCanvas = srcCanvas;

        if (maxX !== -1 && maxY !== -1) {
          const ratio = window.devicePixelRatio || 1;
          const padding = Math.round(16 * ratio);
          const cropX = Math.max(0, minX - padding);
          const cropY = Math.max(0, minY - padding);
          const cropW = Math.min(width - cropX, (maxX - minX) + padding * 2);
          const cropH = Math.min(height - cropY, (maxY - minY) + padding * 2);

          const cropped = document.createElement('canvas');
          cropped.width = cropW;
          cropped.height = cropH;
          const croppedCtx = cropped.getContext('2d');
          croppedCtx.drawImage(srcCanvas, cropX, cropY, cropW, cropH, 0, 0, cropW, cropH);
          resultCanvas = cropped;
        }

        return resultCanvas;
      };

      const pointFromEvent = (event) => {
        const bounds = canvas.getBoundingClientRect();

        return {
          x: event.clientX - bounds.left,
          y: event.clientY - bounds.top
        };
      };

      toggleEditorBtn.addEventListener('click', openEditor);
      cancelEditorBtn.addEventListener('click', closeEditor);

      uploadTab.addEventListener('click', () => setMode('upload'));
      drawTab.addEventListener('click', () => setMode('draw'));

      strokeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
          currentStrokeWidth = parseFloat(btn.dataset.stroke || '1.8');
          applyBrushSettings();

          strokeButtons.forEach((item) => {
            const active = item === btn;
            item.classList.toggle('bg-white', active);
            item.classList.toggle('text-primary', active);
            item.classList.toggle('font-semibold', active);
            item.classList.toggle('shadow-sm', active);
            item.classList.toggle('text-on-surface-variant', !active);
            item.classList.toggle('font-medium', !active);
          });
        });
      });

      fileInput.addEventListener('change', () => loadFile(fileInput.files[0]));

      ['dragenter', 'dragover'].forEach((eventName) => {
        dropzone.addEventListener(eventName, (event) => {
          event.preventDefault();
          dropzone.classList.add('border-secondary', 'bg-[#eaf2ff]');
        });
      });

      ['dragleave', 'drop'].forEach((eventName) => {
        dropzone.addEventListener(eventName, (event) => {
          event.preventDefault();
          dropzone.classList.remove('border-secondary', 'bg-[#eaf2ff]');
        });
      });

      dropzone.addEventListener('drop', (event) => loadFile(event.dataTransfer.files[0]));

      canvas.addEventListener('pointerdown', (event) => {
        if (canvas.width <= 300 && canvas.height <= 150) {
          resizeCanvas();
        }

        clearFeedback();
        hideSuccessBanner();
        drawing = true;
        hasCanvasStrokes = true;
        changed = true;
        canvas.setPointerCapture(event.pointerId);

        const point = pointFromEvent(event);
        prevPoint = point;
        prevMid = point;
        applyBrushSettings();

        context.beginPath();
        context.arc(point.x, point.y, currentStrokeWidth / 2, 0, Math.PI * 2);
        context.fill();
      });

      canvas.addEventListener('pointermove', (event) => {
        if (drawing && prevPoint && prevMid) {
          const point = pointFromEvent(event);
          const mid = {
            x: (prevPoint.x + point.x) / 2,
            y: (prevPoint.y + point.y) / 2
          };

          context.beginPath();
          context.moveTo(prevMid.x, prevMid.y);
          context.quadraticCurveTo(prevPoint.x, prevPoint.y, mid.x, mid.y);
          context.stroke();

          prevPoint = point;
          prevMid = mid;
        }
      });

      ['pointerup', 'pointercancel', 'lostpointercapture'].forEach((eventName) => {
        canvas.addEventListener(eventName, () => {
          if (drawing && prevPoint && prevMid && (prevMid.x !== prevPoint.x || prevMid.y !== prevPoint.y)) {
            context.beginPath();
            context.moveTo(prevMid.x, prevMid.y);
            context.lineTo(prevPoint.x, prevPoint.y);
            context.stroke();
          }

          drawing = false;
          prevPoint = null;
          prevMid = null;
        });
      });

      document.getElementById('clear-canvas').addEventListener('click', () => {
        clearCanvasBoard();
        changed = false;
        clearFeedback();
      });

      updateButton.addEventListener('click', async () => {
        clearFeedback();
        hideSuccessBanner();

        if (!changed) {
          showFeedback('Goreskan tanda tangan di kanvas atau pilih berkas PNG terlebih dahulu.', 'error');
        } else {
          updateButton.disabled = true;
          updateButton.classList.add('opacity-60', 'cursor-wait');

          try {
            const formData = new FormData();
            formData.append('csrf_token', document.getElementById('signature-csrf-token').value);

            if (selectedFile) {
              formData.append('signature_file', selectedFile, selectedFile.name);
            } else {
              const exportCanvas = getCroppedCanvas(canvas);
              const imageBlob = await new Promise((resolve, reject) => {
                exportCanvas.toBlob((blob) => {
                  if (blob) {
                    resolve(blob);
                  } else {
                    reject(new Error('Gagal menyiapkan gambar dari kanvas.'));
                  }
                }, 'image/png');
              });

              formData.append('signature_file', imageBlob, 'signature.png');
            }

            const response = await fetch('/Tandain/certificate/updateSignature', {
              method: 'POST',
              body: formData,
              headers: { 'Accept': 'application/json' }
            });

            const contentType = response.headers.get('content-type') || '';
            const result = contentType.includes('application/json') ? await response.json() : {};

            if (response.status === 401) {
              showFeedback(result.message || 'Sesi Anda telah berakhir. Silakan masuk kembali.', 'error');

              if (result.redirect) {
                window.location.href = result.redirect;
              }
            } else if (!response.ok) {
              throw new Error(result.message || 'Gagal menyimpan tanda tangan.');
            } else {
              setActiveSignature(result.image_url);
              closeEditor();
              showSuccessBanner(result.message || 'Tanda tangan berhasil disimpan.');
            }
          } catch (error) {
            showFeedback(error.message || 'Terjadi kesalahan saat menyimpan tanda tangan.', 'error');
          } finally {
            updateButton.disabled = false;
            updateButton.classList.remove('opacity-60', 'cursor-wait');
          }
        }
      });

      window.addEventListener('resize', () => {
        if (!editorSection.classList.contains('hidden') && !drawPanel.hidden) {
          resizeCanvas();
        }
      });

      if (!editorSection.classList.contains('hidden') && !drawPanel.hidden) {
        requestAnimationFrame(() => {
          resizeCanvas();
        });
      }
    })();
  </script>

<?php require_once 'views/layouts/footer.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= $data['title'] ?? 'Verifikasi Dokumen Elektronik - Tandain PNJ'; ?></title>

  <link href="/Tandain/assets/css/fonts.css" rel="stylesheet">
  <link href="/Tandain/assets/css/material-symbols.css" rel="stylesheet">

  <script src="/Tandain/assets/js/tailwind.js"></script>
  <script>
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "on-error-container": "#93000a",
            "on-secondary": "#ffffff",
            "outline-variant": "#c7c5cf",
            "on-background": "#191c1e",
            "tertiary-fixed-dim": "#b4c5ff",
            "error": "#ba1a1a",
            "inverse-on-surface": "#eff1f3",
            "surface-container-low": "#f2f4f6",
            "on-tertiary": "#ffffff",
            "inverse-surface": "#2d3133",
            "error-container": "#ffdad6",
            "on-secondary-fixed-variant": "#372abf",
            "primary-fixed-dim": "#bfc4ef",
            "secondary-fixed-dim": "#c3c0ff",
            "on-primary": "#ffffff",
            "inverse-primary": "#bfc4ef",
            "surface-variant": "#e0e3e5",
            "secondary-container": "#6860ef",
            "secondary-fixed": "#e3dfff",
            "on-tertiary-container": "#4c7eff",
            "on-primary-container": "#7e83ab",
            "on-tertiary-fixed-variant": "#003ea8",
            "on-secondary-fixed": "#100069",
            "surface-container": "#eceef0",
            "outline": "#77767f",
            "on-secondary-container": "#fffbff",
            "surface-dim": "#d8dadc",
            "background": "#f7f9fb",
            "secondary": "#4e45d5",
            "surface-container-lowest": "#ffffff",
            "tertiary-container": "#001950",
            "primary-fixed": "#dee0ff",
            "primary": "#00021c",
            "surface-container-highest": "#e0e3e5",
            "tertiary": "#000315",
            "surface-tint": "#565c82",
            "on-error": "#ffffff",
            "surface": "#f7f9fb",
            "on-surface": "#191c1e",
            "tertiary-fixed": "#dbe1ff",
            "primary-container": "#151b3d",
            "on-surface-variant": "#46464e",
            "on-primary-fixed-variant": "#3f4469",
            "on-tertiary-fixed": "#00174b",
            "surface-bright": "#f7f9fb",
            "on-primary-fixed": "#13193b",
            "surface-container-high": "#e6e8ea"
          },
          borderRadius: {
            "DEFAULT": "0.125rem",
            "lg": "0.25rem",
            "xl": "0.5rem",
            "full": "0.75rem"
          },
          spacing: {
            "gutter-mobile": "1rem",
            "margin-mobile": "1rem",
            "gutter": "1.5rem",
            "space-md": "1rem",
            "space-lg": "1.5rem",
            "space-xl": "2.5rem",
            "margin": "2rem",
            "space-xs": "0.25rem",
            "space-sm": "0.5rem"
          },
          fontFamily: {
            sans: ["Inter", "sans-serif"],
            "body-lg": ["Inter", "sans-serif"],
            "body-md": ["Inter", "sans-serif"],
            "body-sm": ["Inter", "sans-serif"],
            "label-lg": ["Inter", "sans-serif"],
            "label-md": ["Inter", "sans-serif"],
            "label-sm": ["Inter", "sans-serif"],
            "code-tabular": ["Inter", "sans-serif"],
            "headline-xl": ["Plus Jakarta Sans", "sans-serif"],
            "headline-lg": ["Plus Jakarta Sans", "sans-serif"],
            "headline-md": ["Plus Jakarta Sans", "sans-serif"],
            "headline-sm": ["Plus Jakarta Sans", "sans-serif"],
            "headline-xl-mobile": ["Plus Jakarta Sans", "sans-serif"],
            "headline-lg-mobile": ["Plus Jakarta Sans", "sans-serif"]
          },
          fontSize: {
            "body-lg": ["16px", { lineHeight: "26px", letterSpacing: "-0.005em", fontWeight: "400" }],
            "body-md": ["14px", { lineHeight: "22px", letterSpacing: "0", fontWeight: "400" }],
            "headline-xl-mobile": ["28px", { lineHeight: "36px", letterSpacing: "-0.02em", fontWeight: "700" }],
            "headline-md": ["22px", { lineHeight: "28px", letterSpacing: "-0.015em", fontWeight: "600" }],
            "label-md": ["12px", { lineHeight: "16px", letterSpacing: "0.02em", fontWeight: "600" }],
            "body-sm": ["12px", { lineHeight: "18px", letterSpacing: "0.005em", fontWeight: "400" }],
            "label-sm": ["11px", { lineHeight: "14px", letterSpacing: "0.04em", fontWeight: "700" }],
            "label-lg": ["14px", { lineHeight: "20px", letterSpacing: "0.01em", fontWeight: "600" }],
            "code-tabular": ["13px", { lineHeight: "18px", letterSpacing: "0.02em", fontWeight: "500" }],
            "headline-xl": ["36px", { lineHeight: "44px", letterSpacing: "-0.025em", fontWeight: "700" }],
            "headline-sm": ["18px", { lineHeight: "24px", letterSpacing: "-0.01em", fontWeight: "600" }],
            "headline-lg-mobile": ["24px", { lineHeight: "32px", letterSpacing: "-0.015em", fontWeight: "700" }],
            "headline-lg": ["30px", { lineHeight: "38px", letterSpacing: "-0.02em", fontWeight: "700" }]
          }
        }
      }
    };
  </script>

  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    h1, h2, h3, h4, h5, h6, .font-headline-lg, .font-headline-md, .font-headline-sm, .font-headline-xl {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .material-symbols-outlined {
      font-family: 'Material Symbols Outlined' !important;
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      display: inline-block;
      vertical-align: middle;
      line-height: 1;
    }

    .paper-shadow {
      box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.08), 0 4px 14px 0 rgba(15, 23, 42, 0.05);
    }
  </style>
</head>

<body class="bg-background text-on-surface antialiased min-h-screen flex flex-col font-body-md text-body-md">

  <header class="bg-surface-container-lowest border-b border-outline-variant/40 sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
      <a href="/Tandain/verification" class="flex items-center gap-3">
        <img src="/Tandain/assets/img/logo.png" alt="Logo Tandain" class="w-10 h-10 rounded-xl object-cover shadow-sm">
        <span class="font-headline-sm text-headline-sm font-bold text-primary tracking-tight">Tandain</span>
      </a>

      <a href="/Tandain/dashboard" class="inline-flex items-center gap-2 px-4 py-2 text-label-lg font-label-lg text-primary border border-outline-variant hover:bg-surface-container-low rounded-lg transition-colors duration-150 shadow-sm">
        <span class="material-symbols-outlined text-[20px]">dashboard</span>
        <span>Dashboard</span>
      </a>
    </div>
  </header>

  <?php if ($status === 'success' || $status === 'expired'): ?>

    <main class="flex-1 max-w-6xl w-full mx-auto px-6 py-8">
      <div class="max-w-5xl mx-auto space-y-7">

        <section class="bg-emerald-50 border border-emerald-200/90 rounded-xl p-5 shadow-sm">
          <div class="flex items-center gap-4">
            <div class="flex-shrink-0 h-11 w-11 rounded-full bg-surface-container-lowest shadow-sm border border-emerald-100 flex items-center justify-center text-emerald-600">
              <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            </div>
            <div class="flex-1">
              <h2 class="font-headline-sm text-headline-sm font-bold text-emerald-950">Dokumen Valid</h2>
              <p class="font-body-md text-body-md text-emerald-900/90 mt-0.5 leading-relaxed">Dokumen resmi terdaftar dan masa berlaku masih aktif</p>
            </div>
          </div>
        </section>

        <section class="bg-[#fffbeb] border border-[#fde68a] rounded-xl p-5 shadow-sm">
          <div class="flex items-center gap-4">
            <div class="flex-shrink-0 h-11 w-11 rounded-full bg-surface-container-lowest shadow-sm border border-[#fef3c7] flex items-center justify-center text-[#d97706]">
              <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;">warning</span>
            </div>
            <div class="flex-1">
              <h2 class="font-headline-sm text-headline-sm font-bold text-[#78350f]">Dokumen Kedaluwarsa</h2>
              <p class="font-body-md text-body-md text-[#92400e] mt-0.5 leading-relaxed">Tanda tangan terverifikasi asli, tetapi masa berlaku dokumen telah habis.</p>
            </div>
          </div>
        </section>

        <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl shadow-sm overflow-hidden">
          <div class="grid grid-cols-1 md:grid-cols-12 divide-y md:divide-y-0 md:divide-x divide-outline-variant/50">

            <div class="md:col-span-7 p-6 space-y-5 flex flex-col justify-between">
              <div class="flex items-center justify-between border-b border-outline-variant/40 pb-3">
                <div class="flex items-center gap-2.5">
                  <div class="p-1.5 rounded-lg bg-surface-container-low text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">description</span>
                  </div>
                  <h2 class="font-headline-md text-headline-sm font-bold text-primary tracking-tight">Informasi Dokumen</h2>
                </div>
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant" title="Informasi Berkas">info</span>
              </div>

              <div class="space-y-3.5 flex-1">
                <div class="flex items-start justify-between py-1 border-b border-outline-variant/30">
                  <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Judul Dokumen</span>
                  <span class="font-headline-sm text-[14px] font-bold text-primary text-right max-w-[60%]">Lembar Pengesahan Skripsi</span>
                </div>

                <div class="flex items-start justify-between py-1 border-b border-outline-variant/30">
                  <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Nomor Dokumen</span>
                  <span class="font-body-md text-[13px] font-medium text-slate-700 text-right font-code-tabular">PNJ/TIK/SKR/2026/089</span>
                </div>

                <div class="flex items-start justify-between py-1 border-b border-outline-variant/30">
                  <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Pemohon</span>
                  <div class="text-right">
                    <span class="font-headline-sm text-[13px] font-bold text-primary block">Farhan Ibra</span>
                    <span class="font-code-tabular text-[11px] text-slate-500 block">2207411032</span>
                  </div>
                </div>

                <div class="flex items-start justify-between py-1 border-b border-outline-variant/30">
                  <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Tanggal Disahkan</span>
                  <span class="font-body-md text-[13px] font-medium text-slate-700 text-right">24 Feb 2026, 11:15 WIB</span>
                </div>

                <div class="flex items-start justify-between py-1">
                  <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wider">Masa Berlaku</span>
                  <span class="font-body-md text-[13px] font-medium text-slate-800 text-right">Selamanya</span>
                </div>
              </div>
            </div>

            <div class="md:col-span-5 p-6 bg-surface-container-low/40 flex flex-col justify-between space-y-4">
              <div class="flex items-center justify-between border-b border-outline-variant/40 pb-3">
                <div class="flex items-center gap-2">
                  <h2 class="font-headline-md text-headline-sm font-bold text-primary tracking-tight">Daftar Penandatangan</h2>
                </div>
              </div>

              <div class="space-y-3 flex-1 flex flex-col justify-center">

                <div class="bg-surface-container-lowest border border-outline-variant/50 rounded-xl p-3.5 shadow-sm space-y-2">
                  <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                      <div class="h-7 w-7 rounded-full bg-primary-container text-surface-container-lowest font-headline-sm text-[12px] font-bold flex items-center justify-center flex-shrink-0">1</div>
                      <div class="min-w-0">
                        <h3 class="font-headline-sm text-[13px] font-bold text-primary truncate">Anita Hidayati, S.Kom., M.Kom.</h3>
                        <p class="font-body-sm text-[11px] text-on-surface-variant truncate">Dosen Pembimbing</p>
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center justify-between text-[10.5px] text-slate-500 pt-1.5 border-t border-slate-100">
                    <span class="font-code-tabular font-medium text-slate-600">24 Feb 2026, 09:30 WIB</span>
                  </div>
                </div>

                <div class="bg-surface-container-lowest border border-outline-variant/50 rounded-xl p-3.5 shadow-sm space-y-2">
                  <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                      <div class="h-7 w-7 rounded-full bg-primary-container text-surface-container-lowest font-headline-sm text-[12px] font-bold flex items-center justify-center flex-shrink-0">2</div>
                      <div class="min-w-0">
                        <h3 class="font-headline-sm text-[13px] font-bold text-primary truncate">Dr. Bambang W., M.T.</h3>
                        <p class="font-body-sm text-[11px] text-on-surface-variant truncate">Ketua Jurusan TIK</p>
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center justify-between text-[10.5px] text-slate-500 pt-1.5 border-t border-slate-100">
                    <span class="font-code-tabular font-medium text-slate-600">24 Feb 2026, 11:15 WIB</span>
                  </div>
                </div>

              </div>
            </div>

          </div>
        </div>

        <div class="pt-1">
          <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
            <button class="flex-1 w-full bg-[#004B87] hover:bg-[#003b6b] active:scale-[0.99] text-white font-label-lg text-label-lg py-3.5 px-5 rounded-lg flex items-center justify-center gap-2.5 shadow-sm transition-all duration-150" type="button">
              <span class="material-symbols-outlined text-[20px]">download</span>
              <span>Unduh Salinan PDF</span>
            </button>

            <a href="/Tandain/verification" class="w-full sm:w-auto bg-surface-container-lowest hover:bg-surface-container-low text-primary border border-outline-variant/60 font-label-lg text-label-lg py-3.5 px-6 rounded-lg flex items-center justify-center gap-2 shadow-sm transition-all duration-150">
              <span class="material-symbols-outlined text-[20px]">document_scanner</span>
              <span>Verifikasi Dokumen Lain</span>
            </a>
          </div>
        </div>

        <section class="space-y-4 pt-4">
          <div class="flex items-center justify-between border-b border-outline-variant/40 pb-3">
            <h2 class="font-headline-md text-headline-md font-bold text-primary tracking-tight">Pratinjau Dokumen Asli</h2>
            <div class="flex items-center gap-1 bg-surface-container-lowest border border-outline-variant/60 rounded-lg p-1 shadow-sm">
              <button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded transition-colors" title="Perkecil" type="button">
                <span class="material-symbols-outlined text-[18px]">zoom_out</span>
              </button>
              <span class="font-code-tabular text-[11px] text-on-surface-variant px-1 font-semibold">100%</span>
              <button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded transition-colors" title="Perbesar" type="button">
                <span class="material-symbols-outlined text-[18px]">zoom_in</span>
              </button>
              <div class="h-4 w-[1px] bg-outline-variant/50 mx-1"></div>
              <button class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container-low rounded transition-colors" title="Layar Penuh" type="button">
                <span class="material-symbols-outlined text-[18px]">fullscreen</span>
              </button>
            </div>
          </div>

          <div class="bg-surface-container-low p-4 sm:p-8 rounded-xl border border-outline-variant/50 flex justify-center overflow-x-auto">
            <div class="w-full max-w-[620px] bg-surface-container-lowest border border-outline-variant/70 rounded-[2px] paper-shadow p-7 sm:p-10 text-slate-800 text-[11px] leading-relaxed relative selection:bg-secondary/15">
              <div class="text-center pb-2.5">
                <div class="flex items-center justify-center gap-3 mb-1">
                  <div class="w-10 h-10 rounded-full border border-primary/20 flex items-center justify-center bg-surface-container-lowest text-primary flex-shrink-0">
                    <span class="material-symbols-outlined text-[22px]">account_balance</span>
                  </div>
                  <div class="text-center">
                    <p class="font-bold tracking-tight text-[11px] text-slate-900 leading-tight">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</p>
                    <p class="font-extrabold tracking-tight text-[12px] text-slate-900 leading-tight">POLITEKNIK NEGERI JAKARTA</p>
                    <p class="font-bold tracking-tight text-[11px] text-[#004B87] leading-tight">JURUSAN TEKNIK INFORMATIKA DAN KOMPUTER</p>
                  </div>
                </div>
                <p class="text-[9px] text-slate-600 mt-1">Jl. Prof. Dr. G.A. Siwabessy, Kampus UI Depok 16425 | Laman: www.pnj.ac.id</p>
                <div class="mt-2.5 space-y-[2px]">
                  <div class="h-[2px] bg-slate-900 w-full"></div>
                  <div class="h-[0.5px] bg-slate-700 w-full"></div>
                </div>
              </div>

              <div class="text-center my-4">
                <h3 class="font-bold text-[13px] tracking-wide text-slate-900 uppercase">LEMBAR PENGESAHAN SKRIPSI</h3>
                <p class="text-[10px] text-slate-600">Nomor Registrasi: PNJ/TIK/SKR/2026/089</p>
              </div>

              <div class="space-y-3 text-justify text-[10.5px] leading-normal text-slate-800">
                <p>Telah diuji dan dipertahankan di hadapan Dewan Penguji pada tanggal <strong>24 Februari 2026</strong> dan dinyatakan memenuhi syarat kelulusan Program Studi Sarjana Terapan Teknik Informatika:</p>
                <div class="my-3 pl-3 border-l-2 border-slate-300 py-1 space-y-1">
                  <div class="grid grid-cols-12 gap-1">
                    <span class="col-span-4 text-slate-600 font-medium">Nama Mahasiswa</span>
                    <span class="col-span-1">:</span>
                    <span class="col-span-7 font-bold text-slate-900">Farhan Maulana</span>
                  </div>
                  <div class="grid grid-cols-12 gap-1">
                    <span class="col-span-4 text-slate-600 font-medium">Nomor Induk Mahasiswa</span>
                    <span class="col-span-1">:</span>
                    <span class="col-span-7 font-semibold font-code-tabular text-slate-900">2207411032</span>
                  </div>
                  <div class="grid grid-cols-12 gap-1">
                    <span class="col-span-4 text-slate-600 font-medium">Program Studi</span>
                    <span class="col-span-1">:</span>
                    <span class="col-span-7 text-slate-900">Teknik Informatika (D4)</span>
                  </div>
                  <div class="grid grid-cols-12 gap-1 items-start">
                    <span class="col-span-4 text-slate-600 font-medium">Judul Skripsi</span>
                    <span class="col-span-1">:</span>
                    <span class="col-span-7 font-semibold text-slate-900 italic">"Rancang Bangun Sistem Tanda Tangan Elektronik Kampus Berbasis Kriptografi Kunci Publik"</span>
                  </div>
                </div>
                <p class="text-[10px] text-slate-700">Disahkan dan disetujui untuk diarsipkan secara digital di Repositori Institusi Politeknik Negeri Jakarta.</p>
              </div>

              <div class="mt-7 pt-4 grid grid-cols-2 gap-4 text-center">
                <div class="flex flex-col items-center justify-between h-40">
                  <div>
                    <p class="text-[9.5px] text-slate-600">Menyetujui,</p>
                    <p class="font-bold text-[10.5px] text-slate-900">Dosen Pembimbing</p>
                  </div>
                  <div class="my-1.5 p-1.5 bg-slate-50 border border-emerald-300 rounded flex items-center gap-2 max-w-[170px] shadow-sm">
                    <div class="w-8 h-8 bg-surface-container-lowest border border-emerald-200 rounded flex items-center justify-center text-emerald-700 flex-shrink-0">
                      <span class="material-symbols-outlined text-[18px]">qr_code_2</span>
                    </div>
                    <div class="text-left leading-tight">
                      <span class="block text-[8px] font-bold text-emerald-800 uppercase tracking-tighter">TERTANDATANGANI</span>
                      <span class="block text-[7.5px] text-slate-600 font-code-tabular">24/02/2026 09:30</span>
                      <span class="block text-[7px] text-slate-500 font-mono truncate max-w-[90px]">SHA256: 7d4a...e1</span>
                    </div>
                  </div>
                  <div>
                    <p class="font-bold text-[10.5px] text-slate-900 underline decoration-slate-400">Anita Hidayati, S.Kom., M.Kom.</p>
                    <p class="text-[9px] text-slate-600 font-code-tabular">NIP. 198205142008122003</p>
                  </div>
                </div>

                <div class="flex flex-col items-center justify-between h-40">
                  <div>
                    <p class="text-[9.5px] text-slate-600">Mengetahui,</p>
                    <p class="font-bold text-[10.5px] text-slate-900">Ketua Jurusan TIK</p>
                  </div>
                  <div class="my-1.5 p-1.5 bg-slate-50 border border-emerald-300 rounded flex items-center gap-2 max-w-[170px] shadow-sm">
                    <div class="w-8 h-8 bg-surface-container-lowest border border-emerald-200 rounded flex items-center justify-center text-emerald-700 flex-shrink-0">
                      <span class="material-symbols-outlined text-[18px]">qr_code_2</span>
                    </div>
                    <div class="text-left leading-tight">
                      <span class="block text-[8px] font-bold text-emerald-800 uppercase tracking-tighter">TERTANDATANGANI</span>
                      <span class="block text-[7.5px] text-slate-600 font-code-tabular">24/02/2026 11:15</span>
                      <span class="block text-[7px] text-slate-500 font-mono truncate max-w-[90px]">SHA256: b39c...9f</span>
                    </div>
                  </div>
                  <div>
                    <p class="font-bold text-[10.5px] text-slate-900 underline decoration-slate-400">Dr. Bambang W., M.T.</p>
                    <p class="text-[9px] text-slate-600 font-code-tabular">NIP. 197403211999031001</p>
                  </div>
                </div>
              </div>

              <div class="mt-6 pt-2 border-t border-slate-200 flex items-center justify-between text-[8px] text-slate-500">
                <span>Halaman 1 dari 1</span>
                <span>Dokumen Elektronik Tandain PNJ • ID: VEF-20260224-TIK903</span>
              </div>
            </div>
          </div>
        </section>

      </div>
    </main>

  <?php elseif ($status === 'error'): ?>

    <main class="flex-1 max-w-6xl w-full mx-auto px-6 py-8">
      <div class="max-w-4xl mx-auto py-10 sm:py-12 space-y-8">
        <div class="space-y-2 text-center">
          <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Verifikasi Dokumen</h1>
          <p class="font-body-md text-body-md text-on-surface-variant">Validasi keaslian berkas dan tanda tangan elektronik resmi.</p>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-8 sm:p-10 shadow-sm space-y-8">
          <div class="rounded-xl border border-red-200 bg-red-50/70 p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0 shadow-sm">
              <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">cancel</span>
            </div>
            <div class="flex-1 space-y-1">
              <h3 class="text-lg font-bold text-red-800 font-headline-sm">Dokumen Tidak Terdaftar</h3>
              <p class="text-sm text-red-700 font-body-sm leading-relaxed">Berkas tidak ditemukan dalam pangkalan data sistem atau telah mengalami modifikasi/manipulasi digital.</p>
            </div>
          </div>

          <div class="bg-surface-container-low border border-outline-variant/50 rounded-xl p-6 space-y-4">
            <h4 class="text-sm font-semibold text-primary font-headline-sm inline-flex items-center gap-2 pb-3 border-b border-outline-variant/40 w-full">
              <span class="material-symbols-outlined text-[20px] text-on-surface-variant">description</span>
              <span>Rincian Berkas Pemeriksaan</span>
            </h4>
            <div class="grid grid-cols-1 gap-4 text-left font-body-sm sm:grid-cols-2">
              <div class="space-y-1">
                <span class="text-xs text-on-surface-variant font-medium">Nama Berkas</span>
                <p class="text-sm font-semibold text-primary break-all">berkas_pengesahan_skripsi_final.pdf</p>
              </div>
              <div class="space-y-1">
                <span class="text-xs text-on-surface-variant font-medium">Ukuran Berkas</span>
                <p class="text-sm font-semibold text-primary">1.4 MB</p>
              </div>
            </div>
          </div>

          <div class="flex flex-col gap-3 pt-2">
            <a href="/Tandain/verification" class="w-full bg-[#004B87] hover:bg-[#003b6b] active:scale-[0.99] text-white font-label-lg text-label-lg py-3 px-5 rounded-lg flex items-center justify-center gap-2 shadow-sm transition-all duration-150">
              <span class="material-symbols-outlined text-[20px]">upload_file</span>
              <span>Unggah Ulang Berkas</span>
            </a>
            <a href="/Tandain/verification?status=error" class="w-full bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low active:scale-[0.99] text-primary font-label-lg text-label-lg py-3 px-5 rounded-lg flex items-center justify-center gap-2 shadow-sm transition-all duration-150">
              <span class="material-symbols-outlined text-[20px]">qr_code_scanner</span>
              <span>Pindai Kode QR</span>
            </a>
          </div>
        </div>
      </div>
    </main>

  <?php else: ?>

    <main class="flex-1 max-w-6xl w-full mx-auto px-6 py-8">
      <div class="max-w-4xl mx-auto py-10 sm:py-12 space-y-8">
        <div class="space-y-2 text-center">
          <h1 class="font-headline-lg text-headline-lg font-bold text-primary tracking-tight">Verifikasi Dokumen</h1>
          <p class="font-body-md text-body-md text-on-surface-variant">Validasi keaslian berkas dan tanda tangan elektronik resmi.</p>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-xl p-8 sm:p-10 shadow-sm space-y-8">
          <div class="border-2 border-dashed border-outline-variant rounded-xl py-12 px-8 flex flex-col items-center justify-center text-center bg-surface-container-low">
            <div class="w-14 h-14 rounded-xl bg-surface-container-lowest border border-outline-variant/50 flex items-center justify-center text-[#004B87] shadow-sm mb-4">
              <span class="material-symbols-outlined text-[32px]">picture_as_pdf</span>
            </div>
            <h3 class="font-headline-sm text-headline-sm font-semibold text-primary mb-1.5">Tarik file PDF ke sini, atau klik Pilih File</h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant mb-6">Format PDF (Maks. 2 MB)</p>
            <a href="/Tandain/verification?status=success" class="bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low text-primary font-label-lg text-label-lg py-2.5 px-6 rounded-lg inline-flex items-center gap-2 shadow-sm transition-colors duration-150">
              <span class="material-symbols-outlined text-[20px]">upload_file</span>
              <span>Pilih File</span>
            </a>
          </div>

          <div class="flex flex-col gap-3 pt-1">
            <a href="/Tandain/verification?status=success" class="w-full bg-[#004B87] hover:bg-[#003b6b] active:scale-[0.99] text-white font-label-lg text-label-lg py-3 px-5 rounded-lg flex items-center justify-center gap-2 shadow-sm transition-all duration-150">
              <span class="material-symbols-outlined text-[20px]">verified</span>
              <span>Periksa Dokumen</span>
            </a>
            <a href="/Tandain/verification?status=error" class="w-full bg-surface-container-lowest border border-outline-variant hover:bg-surface-container-low active:scale-[0.99] text-primary font-label-lg text-label-lg py-3 px-5 rounded-lg flex items-center justify-center gap-2 shadow-sm transition-all duration-150">
              <span class="material-symbols-outlined text-[20px]">qr_code_scanner</span>
              <span>Pindai Kode QR</span>
            </a>
          </div>
        </div>
      </div>
    </main>

  <?php endif; ?>

  <footer class="mt-auto py-6 border-t border-outline-variant/40 bg-surface-container-lowest text-center">
    <div class="max-w-4xl mx-auto px-6 text-center text-on-surface-variant font-body-sm text-body-sm">
      <p>© 2026 Politeknik Negeri Jakarta</p>
    </div>
  </footer>

</body>
</html>

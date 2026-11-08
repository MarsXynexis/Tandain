<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= $data['title'] ?? 'Tandain - Platform Tanda Tangan & Segel Elektronik PNJ'; ?></title>

  <link href="/Tandain/assets/css/fonts.css" rel="stylesheet">
  <link href="/Tandain/assets/css/globals.css" rel="stylesheet">
  <link href="/Tandain/assets/css/styleguide.css" rel="stylesheet">
  <link href="/Tandain/assets/css/material-symbols.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="/Tandain/assets/js/tailwind.js"></script>
  <script>
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "on-primary-container": "#7e83ab",
            "surface-container-highest": "#e0e3e5",
            "primary-container": "#151b3d",
            "on-tertiary": "#ffffff",
            "surface-variant": "#e0e3e5",
            "inverse-on-surface": "#eff1f3",
            "on-error": "#ffffff",
            "on-primary-fixed-variant": "#3f4469",
            "on-tertiary-fixed-variant": "#003ea8",
            "error-container": "#ffdad6",
            "surface-container": "#eceef0",
            "secondary-container": "#6860ef",
            "background": "#f7f9fb",
            "on-secondary-fixed": "#100069",
            "on-surface": "#191c1e",
            "on-primary": "#ffffff",
            "on-primary-fixed": "#13193b",
            "tertiary-container": "#001950",
            "surface-dim": "#d8dadc",
            "inverse-primary": "#bfc4ef",
            "error": "#ba1a1a",
            "surface-container-lowest": "#ffffff",
            "tertiary-fixed-dim": "#b4c5ff",
            "on-error-container": "#93000a",
            "surface-container-high": "#e6e8ea",
            "primary-fixed": "#dee0ff",
            "on-secondary-container": "#fffbff",
            "surface-bright": "#f7f9fb",
            "surface-tint": "#565c82",
            "secondary-fixed": "#e3dfff",
            "secondary-fixed-dim": "#c3c0ff",
            "surface-container-low": "#f2f4f6",
            "outline-variant": "#c7c5cf",
            "inverse-surface": "#2d3133",
            "tertiary-fixed": "#dbe1ff",
            "tertiary": "#000315",
            "secondary": "#4e45d5",
            "primary": "#00021c",
            "on-surface-variant": "#46464e",
            "on-tertiary-fixed": "#00174b",
            "outline": "#77767f",
            "surface": "#f7f9fb",
            "on-background": "#191c1e",
            "on-tertiary-container": "#4c7eff",
            "on-secondary": "#ffffff",
            "on-secondary-fixed-variant": "#372abf",
            "primary-fixed-dim": "#bfc4ef",
            "variable-collection-blue": "var(--variable-collection-blue)",
            "variable-collection-color": "var(--variable-collection-color)",
            "variable-collection-color-2": "var(--variable-collection-color-2)",
            "variable-collection-color-3": "var(--variable-collection-color-3)",
            "variable-collection-color-4": "var(--variable-collection-color-4)",
            "variable-collection-color-5": "var(--variable-collection-color-5)",
            "variable-collection-darkblue": "var(--variable-collection-darkblue)",
            "variable-collection-green": "var(--variable-collection-green)",
            "variable-collection-orange": "var(--variable-collection-orange)",
            "variable-collection-purple": "var(--variable-collection-purple)",
            "variable-collection-red": "var(--variable-collection-red)",
            "variable-collection-stroke-blue": "var(--variable-collection-stroke-blue)",
            "variable-collection-stroke-green": "var(--variable-collection-stroke-green)",
            "variable-collection-stroke-orange": "var(--variable-collection-stroke-orange)",
            "variable-collection-stroke-purple": "var(--variable-collection-stroke-purple)"
          },
          borderRadius: {
            "DEFAULT": "0.125rem",
            "lg": "0.25rem",
            "xl": "0.5rem",
            "full": "0.75rem"
          },
          spacing: {
            "space-sm": "0.5rem",
            "gutter-mobile": "1rem",
            "space-xl": "2.5rem",
            "gutter": "1.5rem",
            "space-md": "1rem",
            "space-xs": "0.25rem",
            "space-lg": "1.5rem",
            "margin-mobile": "1rem",
            "margin": "2rem"
          },
          fontFamily: {
            "body-lg": ["Inter"],
            "label-lg": ["Inter"],
            "body-sm": ["Inter"],
            "headline-xl": ["Plus Jakarta Sans"],
            "body-md": ["Inter"],
            "code-tabular": ["Inter"],
            "label-sm": ["Inter"],
            "headline-lg": ["Plus Jakarta Sans"],
            "headline-sm": ["Plus Jakarta Sans"],
            "headline-xl-mobile": ["Plus Jakarta Sans"],
            "headline-lg-mobile": ["Plus Jakarta Sans"],
            "headline-md": ["Plus Jakarta Sans"],
            "label-md": ["Inter"]
          },
          fontSize: {
            "body-lg": ["16px", {
              lineHeight: "26px",
              letterSpacing: "-0.005em",
              fontWeight: "400"
            }],
            "label-lg": ["14px", {
              lineHeight: "20px",
              letterSpacing: "0.01em",
              fontWeight: "600"
            }],
            "body-sm": ["12px", {
              lineHeight: "18px",
              letterSpacing: "0.005em",
              fontWeight: "400"
            }],
            "headline-xl": ["36px", {
              lineHeight: "44px",
              letterSpacing: "-0.025em",
              fontWeight: "700"
            }],
            "body-md": ["14px", {
              lineHeight: "22px",
              letterSpacing: "0",
              fontWeight: "400"
            }],
            "code-tabular": ["13px", {
              lineHeight: "18px",
              letterSpacing: "0.02em",
              fontWeight: "500"
            }],
            "label-sm": ["11px", {
              lineHeight: "14px",
              letterSpacing: "0.04em",
              fontWeight: "700"
            }],
            "headline-lg": ["30px", {
              lineHeight: "38px",
              letterSpacing: "-0.02em",
              fontWeight: "700"
            }],
            "headline-sm": ["18px", {
              lineHeight: "24px",
              letterSpacing: "-0.01em",
              fontWeight: "600"
            }],
            "headline-xl-mobile": ["28px", {
              lineHeight: "36px",
              letterSpacing: "-0.02em",
              fontWeight: "700"
            }],
            "headline-lg-mobile": ["24px", {
              lineHeight: "32px",
              letterSpacing: "-0.015em",
              fontWeight: "700"
            }],
            "headline-md": ["22px", {
              lineHeight: "28px",
              letterSpacing: "-0.015em",
              fontWeight: "600"
            }],
            "label-md": ["12px", {
              lineHeight: "16px",
              letterSpacing: "0.02em",
              fontWeight: "600"
            }]
          }
        }
      }
    };
  </script>

  <style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      display: inline-block;
      vertical-align: middle;
      line-height: 1;
    }
  </style>
</head>

<body class="bg-background text-on-surface antialiased min-h-screen flex selection:bg-secondary selection:text-on-secondary">
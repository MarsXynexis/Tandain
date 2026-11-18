  <script src="/Tandain/assets/js/tailwind.js"></script>
  <script>
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "brand": "#004B87",
            "brand-hover": "#003865",
            "brand-soft": "#eff6ff",
            "primary": "#0f172a",
            "on-primary": "#ffffff",
            "primary-container": "#003865",
            "on-primary-container": "#dbeafe",
            "primary-fixed": "#e0f2fe",
            "primary-fixed-dim": "#bae6fd",
            "on-primary-fixed": "#0f172a",
            "on-primary-fixed-variant": "#003865",
            "inverse-primary": "#93c5fd",
            "secondary": "#004B87",
            "on-secondary": "#ffffff",
            "secondary-container": "#dbeafe",
            "on-secondary-container": "#003865",
            "secondary-fixed": "#eff6ff",
            "secondary-fixed-dim": "#bfdbfe",
            "on-secondary-fixed": "#003865",
            "on-secondary-fixed-variant": "#004B87",
            "tertiary": "#003865",
            "on-tertiary": "#ffffff",
            "tertiary-container": "#004B87",
            "on-tertiary-container": "#eff6ff",
            "tertiary-fixed": "#dbeafe",
            "tertiary-fixed-dim": "#93c5fd",
            "on-tertiary-fixed": "#002b4d",
            "on-tertiary-fixed-variant": "#004B87",
            "error": "#ba1a1a",
            "on-error": "#ffffff",
            "error-container": "#ffdad6",
            "on-error-container": "#93000a",
            "background": "#f7f9fb",
            "on-background": "#0f172a",
            "surface": "#f7f9fb",
            "surface-bright": "#f7f9fb",
            "surface-dim": "#d8dadc",
            "surface-container-lowest": "#ffffff",
            "surface-container-low": "#f2f4f6",
            "surface-container": "#eceef0",
            "surface-container-high": "#e6e8ea",
            "surface-container-highest": "#e0e3e5",
            "surface-variant": "#e0e3e5",
            "on-surface": "#191c1e",
            "on-surface-variant": "#475569",
            "outline": "#64748b",
            "outline-variant": "#cbd5e1",
            "inverse-surface": "#2d3133",
            "inverse-on-surface": "#eff1f3",
            "surface-tint": "#004B87"
          },
          spacing: {
            "space-xs": "0.25rem",
            "space-sm": "0.5rem",
            "space-md": "1rem",
            "space-lg": "1.5rem",
            "space-xl": "2.5rem",
            "gutter-mobile": "1rem",
            "gutter": "1.5rem",
            "margin-mobile": "1rem",
            "margin": "2rem"
          },
          fontFamily: {
            "sans": ["Inter", "ui-sans-serif", "system-ui", "sans-serif"],
            "headline-xl": ["Plus Jakarta Sans", "sans-serif"],
            "headline-xl-mobile": ["Plus Jakarta Sans", "sans-serif"],
            "headline-lg": ["Plus Jakarta Sans", "sans-serif"],
            "headline-lg-mobile": ["Plus Jakarta Sans", "sans-serif"],
            "headline-md": ["Plus Jakarta Sans", "sans-serif"],
            "headline-sm": ["Plus Jakarta Sans", "sans-serif"],
            "body-lg": ["Inter", "sans-serif"],
            "body-md": ["Inter", "sans-serif"],
            "body-sm": ["Inter", "sans-serif"],
            "label-lg": ["Inter", "sans-serif"],
            "label-md": ["Inter", "sans-serif"],
            "label-sm": ["Inter", "sans-serif"],
            "code-tabular": ["Inter", "sans-serif"]
          },
          fontSize: {
            "headline-xl": ["36px", { lineHeight: "44px", letterSpacing: "-0.025em", fontWeight: "700" }],
            "headline-xl-mobile": ["28px", { lineHeight: "36px", letterSpacing: "-0.02em", fontWeight: "700" }],
            "headline-lg": ["30px", { lineHeight: "38px", letterSpacing: "-0.02em", fontWeight: "700" }],
            "headline-lg-mobile": ["24px", { lineHeight: "32px", letterSpacing: "-0.015em", fontWeight: "700" }],
            "headline-md": ["22px", { lineHeight: "28px", letterSpacing: "-0.015em", fontWeight: "600" }],
            "headline-sm": ["18px", { lineHeight: "24px", letterSpacing: "-0.01em", fontWeight: "600" }],
            "body-lg": ["16px", { lineHeight: "26px", letterSpacing: "-0.005em", fontWeight: "400" }],
            "body-md": ["14px", { lineHeight: "22px", letterSpacing: "0", fontWeight: "400" }],
            "body-sm": ["12px", { lineHeight: "18px", letterSpacing: "0.005em", fontWeight: "400" }],
            "label-lg": ["14px", { lineHeight: "20px", letterSpacing: "0.01em", fontWeight: "600" }],
            "label-md": ["12px", { lineHeight: "16px", letterSpacing: "0.02em", fontWeight: "600" }],
            "label-sm": ["11px", { lineHeight: "14px", letterSpacing: "0.04em", fontWeight: "700" }],
            "code-tabular": ["13px", { lineHeight: "18px", letterSpacing: "0.02em", fontWeight: "500" }]
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

    .font-code-tabular {
      font-variant-numeric: tabular-nums;
    }
  </style>

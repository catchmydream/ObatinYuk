/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./vendor/robsontenorio/mary/src/View/Components/**/*.php"
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
            colors: {
                medical: {
                    50: '#f0f7ff',
                    100: '#e0effe',
                    600: '#1d4ed8',
                    700: '#1d4ed8',
                }
            },
            boxShadow: {
                'premium': '0 8px 30px rgba(0, 0, 0, 0.03), 0 1px 1px rgba(0, 0, 0, 0.02)',
                'bubble': '0 4px 20px rgba(0, 0, 0, 0.02)',
            }
        },
    },
    plugins: [
        require("daisyui")
    ],
    daisyui: {
        themes: [
            {
                light: {
                    ...require("daisyui/src/theming/themes")["light"],
                    primary: "#1e40af", 
                    secondary: "#10b981", 
                    accent: "#3b82f6",
                    neutral: "#1e293b",
                    "base-100": "#ffffff",
                    "base-200": "#f8fafc",
                    "base-300": "#f1f5f9",
                },
                dark: {
                    ...require("daisyui/src/theming/themes")["dark"],
                    primary: "#60a5fa",
                    secondary: "#34d399",
                    "base-100": "#0b0f19",
                    "base-200": "#111827",
                    "base-300": "#1f2937",
                }
            }
        ]
    }
}

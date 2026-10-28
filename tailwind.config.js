/** Mirrors the inline config the site used with the Tailwind CDN. Run `npm run build` after changing views. */
module.exports = {
  content: ['./views/**/*.php', './src/**/*.php', './config/**/*.php'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
        display: ['"Space Grotesk"', 'sans-serif'],
      },
      colors: {
        brand: {
          blue: '#0369a1',
          sky: '#0ea5e9',
          dark: '#090e17',
          lime: '#d9f99d',
          neon: '#ccff00',
          accent: '#84cc16',
        },
      },
      boxShadow: {
        'card-glass': '0 20px 40px -15px rgba(0, 0, 0, 0.08), 0 0 1px 1px rgba(255, 255, 255, 0.8)',
        'floating-sky': '0 25px 50px -12px rgba(14, 116, 144, 0.25)',
      },
    },
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/container-queries')],
};

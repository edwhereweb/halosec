/** Design tokens for the HaloSec site. Run `npm run build` after changing views or this file. */
module.exports = {
  content: ['./views/**/*.php', './src/**/*.php', './config/**/*.php', './public/js/**/*.js'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
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
          ink: '#0b0f17',
          mist: '#f5f6f8',
          cta: '#d4f844',
          'cta-hover': '#c3e62f',
          red: '#b42318',
          muted: '#5b6474',
          body: '#3d4655',
        },
      },
      boxShadow: {
        'card-glass': '0 20px 40px -15px rgba(0, 0, 0, 0.08), 0 0 1px 1px rgba(255, 255, 255, 0.8)',
        'floating-sky': '0 25px 50px -12px rgba(14, 116, 144, 0.25)',
        lift: '0 18px 40px -18px rgba(11, 15, 23, 0.25)',
        form: '0 24px 60px -30px rgba(11, 15, 23, 0.25)',
        float: '0 24px 50px -20px rgba(11, 15, 23, 0.28)',
      },
      borderRadius: {
        tile: '28px',
      },
      keyframes: {
        halo: { to: { transform: 'rotate(360deg)' } },
        breathe: { '0%, 100%': { opacity: '0.45' }, '50%': { opacity: '1' } },
        float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-12px)' } },
        rise: { from: { opacity: '0', transform: 'translateY(28px)' }, to: { opacity: '1', transform: 'none' } },
        marquee: { to: { transform: 'translateX(-50%)' } },
        grow: { from: { transform: 'scaleY(0)' }, to: { transform: 'scaleY(1)' } },
        slide: { from: { opacity: '0', transform: 'translateX(-16px)' }, to: { opacity: '1', transform: 'none' } },
      },
      animation: {
        halo: 'halo 16s linear infinite',
        'halo-rev': 'halo 26s linear infinite reverse',
        breathe: 'breathe 4s ease-in-out infinite',
        float: 'float 6s ease-in-out infinite',
        rise: 'rise .9s cubic-bezier(.16,1,.3,1) both',
        marquee: 'marquee 38s linear infinite',
        grow: 'grow 1.1s cubic-bezier(.16,1,.3,1) both',
        slide: 'slide .7s cubic-bezier(.16,1,.3,1) both',
      },
    },
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/container-queries')],
};

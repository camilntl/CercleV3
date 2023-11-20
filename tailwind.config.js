/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    'templates/**/*.html.twig',
    'assets/js/**/*.js',
    'assets/js/**/*.jsx'
  ],
  theme: {
    extend: {
      colors: {
        purple: {
          100: '#FEFCFF',
          200: '#F8F0FF',
          300: '#F4E7FF',
          500: '#8308FF',
          700: '#270B44',
        },
        lavande: {
          400: '#B8A7C9',
          500: '#8556B2',
          900: '#36224a',
        },
        blue: {
          100: '#E6F6FF',
          500: '#0747EB',
        },
        green: {
          100: '#E0FFE1',
          500: '#046A0E',
        },
        orange: {
          100: '#FFEFE0',
          500: '#B85712',
        },
        black: '#1A1A1A'
      },
      fontSize: {
        '3xs': '8px',
        '2xs': '10px',
        '3xl': '32px'
      },
      width: {
        '17': '72px'
      },
      height: {
        '17': '72px'
      },
      maxWidth: {
        '2xs': '288px',
      },
      rotate: {
        '45neg': '-45deg',
      }
    }
  },
  plugins: [],
}


/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./src/**/*.{html,ts}'],
  theme: {
    colors: {
      text: '#141414',
      body: '#f4f4f9',
      blue: '#2b3a67',
      teal: '#496a81',
      orange: '#fdca40',
      white: '#ffffff',
    },
    fontFamily: {
      sans: ['Helvetica', 'sans-serif'],
      display: ['Freeman', 'Helvetica', 'sans-serif'],
      body: ['Helvetica', 'sans-serif'],
    },
    lineHeight: 1.6,
    extend: {
      screens: {
        '3xl': '1920px',
      },
      spacing: {
        gutter: '1.5rem',
      },
      gap: {
        gutter: '1.5rem',
      },
    },
  },
  plugins: [],
  corePlugins: {
    container: false,
  },
};

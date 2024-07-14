/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'dark-purple': '#5E5FEF'
      },
      boxShadow: {
        'custom': 'rgba(0, 0, 0, 0.06) 0px 2px 4px 0px inset;'
      }
    },
  },
  plugins: [],
}


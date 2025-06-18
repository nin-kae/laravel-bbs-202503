/** @type {import('tailwindcss').Config} */
export default {
  prefix: 'tw-', // 防止和 Bootstrap class 冲突
  content: [
      './resources/views/**/*.blade.php',
      './resources/js/**/*.js',
      './resources/js/**/*.vue',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

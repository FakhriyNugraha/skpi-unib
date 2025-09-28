// tailwind.config.js
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './storage/framework/views/*.php',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
  ],
  theme: {
    extend: {
      colors: {
        'unib-blue': {
          50:  '#eef5ff',
          100: '#dceaff',
          200: '#b2ceff',
          300: '#89b2ff',
          400: '#5f96ff',
          500: '#357aff',
          600: '#1d61e6',
          700: '#134bb4',  // dipakai di btn-primary
          800: '#0c3682',
          900: '#062251',  // dipakai di heading & gradient
          950: '#031636',
        },
        'teknik-orange': {
          50:  '#fff4ec',
          100: '#ffe9d8',
          200: '#ffcfad',
          300: '#ffb583',
          400: '#ff9b58',
          500: '#ff812e',
          600: '#e06a19',
          700: '#b45414', // dipakai di CTA
          800: '#873f0f',
          900: '#5b2a0a',
          950: '#3d1c07',
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}

import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import lineClamp from '@tailwindcss/line-clamp';

/** @type {import('tailwindcss').Config} */
export default {
  // Use both Blade + JS (and Flowbite’s JS) as content inputs
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './node_modules/flowbite/**/*.js',
  ],

  // You can keep 'media' if you prefer OS dark mode.
  // We'll rely on DaisyUI's data-theme but leave dark class available too.
  darkMode: ['class', '[data-theme="dark"]'],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },

  plugins: [
    forms,
    typography,
    lineClamp,
    require('flowbite/plugin'),
    require('daisyui'),
  ],

  // DaisyUI theme setup (tweak to your brand)
  daisyui: {
    themes: [
      {
        worksphere: {
          'primary':            '#2563eb',
          'primary-content':    '#ffffff',
          'secondary':          '#9333ea',
          'accent':             '#10b981',
          'neutral':            '#111827',
          'base-100':           '#ffffff',
          'base-200':           '#f3f4f6',
          'base-300':           '#e5e7eb',
          'info':               '#0ea5e9',
          'success':            '#22c55e',
          'warning':            '#f59e0b',
          'error':              '#ef4444',
        },
      },
      'light',
      'dark',
    ],
    darkTheme: 'dark',
  },
};

import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Menggunakan Poppins sebagai font utama
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Mendefinisikan warna biru tua dari referensi design
                'bisa-blue': {
                    DEFAULT: '#0F244A', // Warna sidebar utama
                    light: '#1a3a75',   // Warna untuk hover
                },
                'bisa-bg': '#F3F4F6', // Warna background abu-abu terang
            }
        },
    },

    plugins: [forms],
};
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
            colors: {
                // Palette de marque OMenu.ci (source : design tokens de production)
                bg: '#FFFFFF',
                surface: '#FFFFFF',
                ink: {
                    DEFAULT: '#121212',
                    soft: '#5C5C58',
                },
                brand: {
                    black: '#121212',
                    'black-2': '#000000',
                    red: {
                        DEFAULT: '#A9271E',
                        hover: '#86201A',
                        soft: '#F5E1DF',
                    },
                },
                border: {
                    DEFAULT: '#DEDDD9',
                    strong: '#121212',
                },
                error: {
                    DEFAULT: '#A9271E',
                    bg: '#F5E1DF',
                },
                success: {
                    DEFAULT: '#121212',
                    bg: '#EFEFEC',
                },
                whatsapp: {
                    DEFAULT: '#25D366',
                    hover: '#1DA851',
                },
            },
            fontFamily: {
                display: ['Fraunces', 'Georgia', 'Times New Roman', 'serif'],
                sans: ['Work Sans', ...defaultTheme.fontFamily.sans],
                body: ['Work Sans', '-apple-system', 'Segoe UI', 'Roboto', 'Arial', 'sans-serif'],
            },
            borderRadius: {
                sm: '2px',
                md: '3px',
                pill: '999px',
            },
        },
    },

    plugins: [forms],
};

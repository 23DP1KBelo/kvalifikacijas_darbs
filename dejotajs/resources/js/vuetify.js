import { createVuetify } from 'vuetify'

export const vuetify = createVuetify({
    theme: {
        defaultTheme: 'light',
        themes: {
            light: {
                dark: false,
                colors: {
                background: '#edeffa',
                surface: '#FFFFFF',
                primary: '#0D1B2A',
                secondary: '#394a6e',
                text: '#111827',
                },
            },
        dark: {
            dark: true,
            colors: {
            background: '#1d2d44',
            surface: '#1B263B',
            primary: '#0D1B2A',
            secondary: '#394a6e',
            text: '#f2f3f7',
            },
        },
        },
    },
})


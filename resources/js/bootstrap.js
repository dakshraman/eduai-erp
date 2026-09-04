import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

import Echo from 'laravel-echo';

var chat = document.getElementById('chat_settings')?.value;
if (chat === 'reverb') {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
} else if (chat === 'pusher') {
    import('pusher-js').then(({ default: Pusher }) => {
        window.Pusher = Pusher;
        window.Echo = new Echo({
            authEndpoint: window.Laravel.baseUrl + 'broadcasting/auth',
            broadcaster: 'pusher',
            key: document.getElementById('pusher_app_key')?.value,
            cluster: document.getElementById('pusher_app_cluster')?.value,
            forceTLS: false,
        });
    });
}

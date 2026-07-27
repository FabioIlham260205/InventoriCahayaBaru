<?php

return [
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', env('CLIENT_ID')),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', env('CLIENT_SERVER')),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI', rtrim(env('APP_URL', 'http://localhost'), '/').'/oauth/google/callback'),
        'authorize_url' => env('GOOGLE_AUTHORIZE_URL', 'https://accounts.google.com/o/oauth2/v2/auth'),
        'token_url' => env('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token'),
        'userinfo_url' => env('GOOGLE_USERINFO_URL', 'https://openidconnect.googleapis.com/v1/userinfo'),
    ],
];

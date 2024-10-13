<?php

return [
    'allowed_domains_for_office365_login' => env('ALLOWED_DOMAINS_FOR_OFFICE365_LOGIN'),

    'asana_workspace_id' => env('ASANA_WORKSPACE_ID'),
    'asana_workspace_key' => env('ASANA_WORKSPACE_KEY'),
    'asana_workspace_base_uri' => env('ASANA_WORKSPACE_BASE_URI'),
    'ssl_certificate' => env('SSL_CERTIFICATE', ''),
    'default_project_name' => 'Maintenance',
    'api_key' => env('API_KEY'),
];

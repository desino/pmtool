<?php

return [
    'allowed_domains_for_office365_login' => env('ALLOWED_DOMAINS_FOR_OFFICE365_LOGIN'),

    'asana_workspace_id' => env('ASANA_WORKSPACE_ID'),
    'asana_workspace_key' => env('ASANA_WORKSPACE_KEY'),
    'asana_workspace_base_uri' => env('ASANA_WORKSPACE_BASE_URI'),
    'ssl_certificate' => env('SSL_CERTIFICATE', ''),
    'default_project_name' => 'Maintenance',
    'api_key' => env('API_KEY'),
    'initiative_time_booking_load_default_data_days' => 45,
    'un_billable_internal_time_booking' => [
        'initiative_id' => '-1',
        'initiative_name' => 'Other Bookings',
        'projects' => [
            [
                'project_id' => '-1',
                'project_name' => 'Admin & Management'
            ],
            [
                'project_id' => '-2',
                'project_name' => 'Sick Leave'
            ],
            [
                'project_id' => '-3',
                'project_name' => 'Planned Leave'
            ],
            [
                'project_id' => '-4',
                'project_name' => 'Away - Outside Office'
            ],
            [
                'project_id' => '-5',
                'project_name' => 'Research & Training'
            ],
            [
                'project_id' => '-6',
                'project_name' => 'Sales'
            ],
        ],
        'ticket_filters_visible_in_visible' => [
            [
                'value' => 1,
                'label' => 'Visible'
            ],
            [
                'value' => 0,
                'label' => 'In Visible'
            ],
        ]
    ]
];

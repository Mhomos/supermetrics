<?php
/**
 * Hold all needed metric reports
 */
return [
    'app_url' => 'http://super-metrics.local/',
    'reports' => [
        [
            'key' => 'avg_ch_length_per_month',
            'description' => 'Average character length of posts per month',
        ],
        [
            'key' => 'longest_ch_length_per_month',
            'description' => 'Longest post by character length per month',
        ],
        [
            'key' => 'total_per_week',
            'description' => 'Total posts split by week number',
        ],
        [
            'key' => 'avg_per_user_per_month',
            'description' => 'Average number of posts per user per month',
        ],
    ],
    'base_uri' => 'https://api.supermetrics.com',
    'client_id' => 'ju16a6m81mhid5ue1z3v2g0uh',
    'email' => 'your@email.address',
    'name' => 'Your Name',
    'max_page_number' => 10,
    'token_expiry' => 5, // 5 Min.
];

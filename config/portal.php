<?php

declare(strict_types=1);

return [
    /*
    |----------------------------------------------------------------------
    | Available user roles
    |----------------------------------------------------------------------
    */
    'roles' => [
        'admin' => 'admin',
        'employee' => 'employee',
    ],

    /*
    |----------------------------------------------------------------------
    | Maximum allowed document upload size in bytes (default 10 MB)
    |----------------------------------------------------------------------
    */
    'document_max_size_bytes' => (int) env('DOCUMENT_MAX_SIZE_BYTES', 10 * 1024 * 1024),

    /*
    |----------------------------------------------------------------------
    | Allowed document MIME types
    |----------------------------------------------------------------------
    */
    'document_allowed_mimes' => ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
];

<?php

return [
    'datatable' => 'module_single_page',
    'file_ext' => 'pdf|doc|docx|xls|xlsx|zip',
    'groups' => [
        'group-name' => [
            'validation' => [// 0: hidden, 1: visible, 2: required
                'files' => 1,
                'title' => 2,
            ],
        ],
    ],
];

<?php
return [
    'exports' => [
        'chunk_size'             => 1000,
        'pre_calculate_formulas' => false,
        'strict_null_comparison' => false,
        'csv' => [
            'use_bom'                => false,
            'include_separator_line' => false,
            'excel_compatibility'    => false,
        ],
    ],
    'imports' => [
        'read_only'    => true,
        'ignore_empty' => false,
        'heading_row'  => ['formatter' => 'none'],
        'csv' => [
            'delimiter'        => ',',
            'enclosure'        => '"',
            'escape_character' => '\\',
            'contiguous'       => false,
            'input_encoding'   => 'UTF-8',
        ],
        'properties' => [],
    ],
    'extension_detector' => [
        'xlsx' => \Maatwebsite\Excel\Excel::XLSX,
        'xlsm' => \Maatwebsite\Excel\Excel::XLSX,
        'xls'  => \Maatwebsite\Excel\Excel::XLS,
        'csv'  => \Maatwebsite\Excel\Excel::CSV,
        'tsv'  => \Maatwebsite\Excel\Excel::TSV,
        'html' => \Maatwebsite\Excel\Excel::HTML,
        'ods'  => \Maatwebsite\Excel\Excel::ODS,
    ],
    'value_binder' => [
        'default' => \Maatwebsite\Excel\DefaultValueBinder::class,
    ],
    'cache' => [
        'driver' => 'memory',
    ],
    'transactions' => [
        'handler' => 'db',
    ],
    'temporary_files' => [
        'local_path'          => sys_get_temp_dir(),
        'remote_disk'         => null,
        'remote_prefix'       => null,
        'force_resync_remote' => false,
    ],
];

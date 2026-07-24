<?php

namespace Addon\Models;

use App\Core\Database\Model;

class WaveModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'waves';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'name' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'academic_year' => ['type' => 'varchar', 'length' => 50, 'nullable' => true],
        'description' => ['type' => 'text', 'nullable' => true],
        'start_date' => ['type' => 'date', 'nullable' => false],
        'end_date' => ['type' => 'date', 'nullable' => false],
        'is_active' => ['type' => 'boolean', 'nullable' => false, 'default' => 1],
        'registration_fee_total' => ['type' => 'int', 'nullable' => false, 'default' => 0],
        'registration_fee_archive' => ['type' => 'varchar', 'length' => 255, 'nullable' => true]
    ];

    protected array $seed = [
        [
            'name' => 'Gelombang 1',
            'academic_year' => '2026/2027',
            'description' => 'Gelombang Pendaftaran Semester Ganjil Tahap I',
            'start_date' => '2026-06-01',
            'end_date' => '2026-07-31',
            'is_active' => 1
        ],
        [
            'name' => 'Gelombang 2',
            'academic_year' => '2026/2027',
            'description' => 'Gelombang Pendaftaran Semester Ganjil Tahap II',
            'start_date' => '2026-08-01',
            'end_date' => '2026-09-30',
            'is_active' => 1
        ]
    ];
}

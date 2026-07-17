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
        'description' => ['type' => 'text', 'nullable' => true],
        'start_date' => ['type' => 'date', 'nullable' => false],
        'end_date' => ['type' => 'date', 'nullable' => false],
        'is_active' => ['type' => 'boolean', 'nullable' => false, 'default' => 1]
    ];

    protected array $seed = [
        [
            'name' => 'Gelombang 1',
            'start_date' => '2026-06-01',
            'end_date' => '2026-07-31',
            'is_active' => 1
        ],
        [
            'name' => 'Gelombang 2',
            'start_date' => '2026-08-01',
            'end_date' => '2026-09-30',
            'is_active' => 0
        ]
    ];
}

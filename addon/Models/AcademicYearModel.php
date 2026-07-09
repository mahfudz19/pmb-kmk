<?php

namespace Addon\Models;

use App\Core\Database\Model;

class AcademicYearModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'academic_years';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'year' => ['type' => 'varchar', 'length' => 9, 'nullable' => false],
        'is_active' => ['type' => 'boolean', 'nullable' => false, 'default' => 0]
    ];

    protected array $seed = [
        [
            'year' => '2025/2026',
            'is_active' => 0
        ],
        [
            'year' => '2026/2027',
            'is_active' => 1
        ]
    ];
}

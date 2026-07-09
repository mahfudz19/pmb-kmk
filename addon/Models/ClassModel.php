<?php

namespace Addon\Models;

use App\Core\Database\Model;

class ClassModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'classes';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'name' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'is_active' => ['type' => 'boolean', 'nullable' => false, 'default' => 1]
    ];

    protected array $seed = [
        [
            'name' => 'Reguler Pagi',
            'is_active' => 1
        ],
        [
            'name' => 'Reguler Sore',
            'is_active' => 1
        ],
        [
            'name' => 'Kelas Karyawan (Weekend)',
            'is_active' => 1
        ]
    ];
}

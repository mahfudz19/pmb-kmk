<?php

namespace Addon\Models;

use App\Core\Database\Model;

class FacultyModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'faculties';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'code' => ['type' => 'varchar', 'length' => 10, 'nullable' => false, 'unique' => true],
        'name' => ['type' => 'varchar', 'length' => 100, 'nullable' => false]
    ];

    protected array $seed = [
        [
            'code' => 'FIK',
            'name' => 'Fakultas Ilmu Komputer'
        ],
        [
            'code' => 'FEB',
            'name' => 'Fakultas Ekonomi dan Bisnis'
        ]
    ];
}

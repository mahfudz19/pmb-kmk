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
            'code' => 'FFIK',
            'name' => 'Fakultas Farmasi dan Ilmu Kesehatan'
        ],
        [
            'code' => 'FEB',
            'name' => 'Fakultas Ekonomi dan Bisnis'
        ],
        [
            'code' => 'FIK',
            'name' => 'Fakultas Ilmu Komputer'
        ],
        [
            'code' => 'FHIS',
            'name' => 'Fakultas Hukum dan Ilmu Sosial'
        ]
    ];
}

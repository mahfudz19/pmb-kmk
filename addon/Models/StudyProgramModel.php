<?php

namespace Addon\Models;

use App\Core\Database\Model;

class StudyProgramModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'study_programs';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'faculty_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'faculties.id', 'nullable' => false],
        'code' => ['type' => 'varchar', 'length' => 10, 'nullable' => false, 'unique' => true],
        'name' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'quota' => ['type' => 'int', 'nullable' => false, 'default' => 40]
    ];

    protected array $seed = [
        [
            'faculty_id' => 1,
            'code' => 'IF',
            'name' => 'Informatika',
            'quota' => 50
        ],
        [
            'faculty_id' => 1,
            'code' => 'SI',
            'name' => 'Sistem Informasi',
            'quota' => 40
        ],
        [
            'faculty_id' => 2,
            'code' => 'MJ',
            'name' => 'Manajemen',
            'quota' => 60
        ],
        [
            'faculty_id' => 2,
            'code' => 'AK',
            'name' => 'Akuntansi',
            'quota' => 40
        ]
    ];
}

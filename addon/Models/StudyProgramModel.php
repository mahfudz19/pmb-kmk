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
            'code' => 'FAR',
            'name' => 'S1 Farmasi',
            'quota' => 50
        ],
        [
            'faculty_id' => 1,
            'code' => 'D3FAR',
            'name' => 'D3 Farmasi',
            'quota' => 40
        ],
        [
            'faculty_id' => 1,
            'code' => 'APT',
            'name' => 'Profesi Apoteker',
            'quota' => 30
        ],
        [
            'faculty_id' => 1,
            'code' => 'D3KEB',
            'name' => 'D3 Kebidanan',
            'quota' => 40
        ],
        [
            'faculty_id' => 2,
            'code' => 'AK',
            'name' => 'Akuntansi',
            'quota' => 50
        ],
        [
            'faculty_id' => 4,
            'code' => 'HK',
            'name' => 'Hukum',
            'quota' => 50
        ],
        [
            'faculty_id' => 4,
            'code' => 'IK',
            'name' => 'Ilmu Komunikasi',
            'quota' => 50
        ],
        [
            'faculty_id' => 2,
            'code' => 'MJ',
            'name' => 'Manajemen',
            'quota' => 60
        ],
        [
            'faculty_id' => 3,
            'code' => 'IF',
            'name' => 'Informatika',
            'quota' => 60
        ],
        [
            'faculty_id' => 3,
            'code' => 'SI',
            'name' => 'Sistem Informasi',
            'quota' => 50
        ]
    ];
}

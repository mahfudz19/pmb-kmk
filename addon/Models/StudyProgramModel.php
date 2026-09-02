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
        'num_code' => ['type' => 'varchar', 'length' => 10, 'nullable' => true],
        'name' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'quota' => ['type' => 'int', 'nullable' => false, 'default' => 40]
    ];

    protected array $seed = [
        [
            'faculty_id' => 1,
            'code' => 'FAR',
            'num_code' => '01',
            'name' => 'S1 Farmasi',
            'quota' => 50
        ],
        [
            'faculty_id' => 1,
            'code' => 'D3FAR',
            'num_code' => '02',
            'name' => 'D3 Farmasi',
            'quota' => 40
        ],
        [
            'faculty_id' => 1,
            'code' => 'APT',
            'num_code' => '03',
            'name' => 'Profesi Apoteker',
            'quota' => 30
        ],
        [
            'faculty_id' => 1,
            'code' => 'D3KEB',
            'num_code' => '04',
            'name' => 'D3 Kebidanan',
            'quota' => 40
        ],
        [
            'faculty_id' => 2,
            'code' => 'AK',
            'num_code' => '05',
            'name' => 'Akuntansi',
            'quota' => 50
        ],
        [
            'faculty_id' => 4,
            'code' => 'HK',
            'num_code' => '06',
            'name' => 'Hukum',
            'quota' => 50
        ],
        [
            'faculty_id' => 4,
            'code' => 'IK',
            'num_code' => '07',
            'name' => 'Ilmu Komunikasi',
            'quota' => 50
        ],
        [
            'faculty_id' => 2,
            'code' => 'MJ',
            'num_code' => '08',
            'name' => 'Manajemen',
            'quota' => 60
        ],
        [
            'faculty_id' => 3,
            'code' => 'IF',
            'num_code' => '09',
            'name' => 'Informatika',
            'quota' => 60
        ],
        [
            'faculty_id' => 3,
            'code' => 'SI',
            'num_code' => '10',
            'name' => 'Sistem Informasi',
            'quota' => 50
        ]
    ];
}

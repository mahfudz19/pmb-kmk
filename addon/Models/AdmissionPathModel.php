<?php

namespace Addon\Models;

use App\Core\Database\Model;

class AdmissionPathModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'admission_paths';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'name' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'is_active' => ['type' => 'boolean', 'nullable' => false, 'default' => 1]
    ];

    protected array $seed = [
        [
            'name' => 'Jalur Rapor (Prestasi)',
            'is_active' => 1
        ],
        [
            'name' => 'Jalur Ujian Tertulis',
            'is_active' => 1
        ],
        [
            'name' => 'Jalur KIP-Kuliah',
            'is_active' => 1
        ]
    ];
}

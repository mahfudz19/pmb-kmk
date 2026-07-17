<?php

namespace Addon\Models;

use App\Core\Database\Model;

class DocumentTypeModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'document_types';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'name' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'is_required' => ['type' => 'boolean', 'nullable' => false, 'default' => 1],
        'description' => ['type' => 'text', 'nullable' => true]
    ];

    protected array $seed = [
        [
            'name' => 'Scan Rapor Kelas XII / Ijazah SMA',
            'is_required' => 1
        ],
        [
            'name' => 'Scan Kartu Keluarga (KK)',
            'is_required' => 1
        ],
        [
            'name' => 'Scan KTP / Kartu Pelajar',
            'is_required' => 1
        ],
        [
            'name' => 'Pas Foto Flat Latar Merah',
            'is_required' => 1
        ]
    ];
}

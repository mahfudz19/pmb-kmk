<?php

namespace Addon\Models;

use App\Core\Database\Model;

class SelectionRoomModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'selection_rooms';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'name' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'location' => ['type' => 'varchar', 'length' => 100, 'nullable' => false]
    ];

    protected array $seed = [
        [
            'name' => 'Lab CBT 1',
            'location' => 'Gedung A, Lantai 2'
        ],
        [
            'name' => 'Lab CBT 2',
            'location' => 'Gedung A, Lantai 3'
        ],
        [
            'name' => 'Ruang Wawancara 1',
            'location' => 'Gedung Utama, Lantai 1'
        ]
    ];
}

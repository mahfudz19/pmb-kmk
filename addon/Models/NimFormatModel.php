<?php

namespace Addon\Models;

use App\Core\Database\Model;

class NimFormatModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'nim_formats';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'name' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'format_pattern' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'is_active' => ['type' => 'boolean', 'nullable' => false, 'default' => 0]
    ];

    protected array $seed = [
        [
            'name' => 'Format Standar (Tahun + Prodi + No Urut)',
            'format_pattern' => '{YEAR}{PRODI_CODE}{SEQ}',
            'is_active' => 1
        ],
        [
            'name' => 'Format Kustom (Tahun + Tanggal + No Urut)',
            'format_pattern' => '{YEAR}{DATE}{SEQ}',
            'is_active' => 0
        ]
    ];

    public function getActivePattern(): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE is_active = 1 LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}

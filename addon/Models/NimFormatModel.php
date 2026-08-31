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
        'name' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'format_pattern' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'is_active' => ['type' => 'boolean', 'nullable' => false, 'default' => 0]
    ];

    protected array $seed = [
        [
            'name' => 'Format Standar KMK (23013-001)',
            'format_pattern' => '{YEAR2}{PRODI_NUM}{GROUP}-{SEQ}',
            'is_active' => 1
        ],
        [
            'name' => 'Format Standar Alternatif (2026-IF-3-001)',
            'format_pattern' => '{YEAR}-{PRODI_CODE}-{GROUP}-{SEQ}',
            'is_active' => 0
        ],
        [
            'name' => 'Format Tanpa Strip (26013001)',
            'format_pattern' => '{YEAR2}{PRODI_NUM}{GROUP}{SEQ}',
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

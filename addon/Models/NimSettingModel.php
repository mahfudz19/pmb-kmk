<?php

namespace Addon\Models;

use App\Core\Database\Model;

class NimSettingModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'nim_settings';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'key' => ['type' => 'varchar', 'length' => 100, 'nullable' => false, 'unique' => true],
        'value' => ['type' => 'text', 'nullable' => true],
        'description' => ['type' => 'varchar', 'length' => 255, 'nullable' => true]
    ];

    protected array $seed = [
        [
            'key' => 'groups',
            'value' => '{"3":"Reguler","8":"Pindahan","9":"Profesi"}',
            'description' => 'Mapping kode kelompok mahasiswa untuk placeholder {GROUP}'
        ],
        [
            'key' => 'seq_digits',
            'value' => '3',
            'description' => 'Minimum digit untuk sequence urutan {SEQ} (1-5, default 3)'
        ],
        [
            'key' => 'year_digits',
            'value' => '2',
            'description' => 'Jumlah digit tahun untuk placeholder {YEAR} (2 sampai 4 digit, default 2)'
        ],
        [
            'key' => 'date_format',
            'value' => 'DDMMYYYY',
            'description' => 'Format tanggal untuk placeholder {DATE} (kombinasi DD, MM, YYYY)'
        ]
    ];

    public function getSetting(string $key, mixed $default = null): mixed
    {
        $stmt = $this->db->prepare("SELECT value FROM {$this->table} WHERE `key` = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        $row = $stmt->fetch();
        return $row !== false ? $row['value'] : $default;
    }

    public function setSetting(string $key, mixed $value, ?string $description = null): void
    {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE `key` = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        $existing = $stmt->fetch();

        if ($existing) {
            if ($description !== null) {
                $stmtUpdate = $this->db->prepare("UPDATE {$this->table} SET `value` = :value, `description` = :description WHERE `key` = :key");
                $stmtUpdate->execute(['value' => (string)$value, 'description' => $description, 'key' => $key]);
            } else {
                $stmtUpdate = $this->db->prepare("UPDATE {$this->table} SET `value` = :value WHERE `key` = :key");
                $stmtUpdate->execute(['value' => (string)$value, 'key' => $key]);
            }
        } else {
            $stmtInsert = $this->db->prepare("INSERT INTO {$this->table} (`key`, `value`, `description`) VALUES (:key, :value, :description)");
            $stmtInsert->execute(['key' => $key, 'value' => (string)$value, 'description' => $description]);
        }
    }

    public function getAllSettings(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        $rows = $stmt->fetchAll() ?: [];
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = [
                'value' => $row['value'],
                'description' => $row['description'] ?? ''
            ];
        }
        return $settings;
    }

    public function getSettings(): array
    {
        $all = $this->getAllSettings();

        $groups = [];
        $groupsVal = $all['groups']['value'] ?? null;
        if (!empty($groupsVal)) {
            $decoded = json_decode($groupsVal, true);
            if (is_array($decoded)) {
                // If associative (e.g. {"3":"Reguler"}) convert to list of [{key: "3", name: "Reguler"}]
                if (array_is_list($decoded)) {
                    $groups = $decoded;
                } else {
                    foreach ($decoded as $k => $v) {
                        if (is_array($v) && isset($v['key'])) {
                            $groups[] = $v;
                        } else {
                            $groups[] = ['key' => (string)$k, 'name' => (string)$v];
                        }
                    }
                }
            }
        }

        if (empty($groups)) {
            $groups = [
                ['key' => '1', 'name' => 'Reguler 1'],
                ['key' => '2', 'name' => 'Reguler 2'],
                ['key' => '3', 'name' => 'Reguler 3'],
                ['key' => '4', 'name' => 'Karyawan'],
                ['key' => '5', 'name' => 'Pindahan / RPL']
            ];
        }

        return [
            'groups' => $groups,
            'groups_desc' => $all['groups']['description'] ?? 'Mapping kode kelompok mahasiswa untuk placeholder {GROUP}',
            'seq_digits' => isset($all['seq_digits']) ? (int)$all['seq_digits']['value'] : 3,
            'seq_digits_desc' => $all['seq_digits']['description'] ?? 'Minimum digit untuk sequence urutan {SEQ} (1-5, default 3)',
            'year_digits' => isset($all['year_digits']) ? (int)$all['year_digits']['value'] : 2,
            'year_digits_desc' => $all['year_digits']['description'] ?? 'Jumlah digit tahun untuk placeholder {YEAR} (2 sampai 4 digit, default 2)',
            'date_format' => $all['date_format']['value'] ?? 'DDMMYYYY',
            'date_format_desc' => $all['date_format']['description'] ?? 'Format tanggal untuk placeholder {DATE} (kombinasi DD, MM, YYYY)',
        ];
    }

    public function saveSettings(array $settings): void
    {
        if (isset($settings['groups'])) {
            $groupsJson = json_encode($settings['groups']);
            $this->setSetting('groups', $groupsJson, $settings['groups_desc'] ?? 'Mapping kode kelompok mahasiswa untuk placeholder {GROUP}');
        }
        if (isset($settings['seq_digits'])) {
            $this->setSetting('seq_digits', (string)$settings['seq_digits'], $settings['seq_digits_desc'] ?? 'Minimum digit untuk sequence urutan {SEQ} (1-5, default 3)');
        }
        if (isset($settings['year_digits'])) {
            $this->setSetting('year_digits', (string)$settings['year_digits'], $settings['year_digits_desc'] ?? 'Jumlah digit tahun untuk placeholder {YEAR} (2 sampai 4 digit, default 2)');
        }
        if (isset($settings['date_format'])) {
            $this->setSetting('date_format', (string)$settings['date_format'], $settings['date_format_desc'] ?? 'Format tanggal untuk placeholder {DATE} (kombinasi DD, MM, YYYY)');
        }
    }
}

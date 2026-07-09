<?php

namespace Addon\Models;

use App\Core\Database\Model;

class AnnouncementModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'announcements';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'title' => ['type' => 'varchar', 'length' => 150, 'nullable' => false],
        'content' => ['type' => 'text', 'nullable' => false],
        'is_active' => ['type' => 'boolean', 'nullable' => false, 'default' => 1]
    ];

    protected array $seed = [
        [
            'title' => 'Pengumuman Hasil Seleksi PMB KMK Utama',
            'content' => 'Bagi seluruh calon mahasiswa baru yang telah melaksanakan ujian tulis/CBT dan wawancara, silakan meninjau pengumuman kelulusan Anda di bawah ini. Harap lakukan daftar ulang sebelum tanggal 30 Juli 2026.',
            'is_active' => 1
        ]
    ];

    public function getActive(): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}

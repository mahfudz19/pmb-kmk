<?php

namespace Addon\Models;

use App\Core\Database\Model;

class RegistrationModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'registrations';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'user_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'users.id', 'unique' => true, 'nullable' => false],
        'full_name' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'nik' => ['type' => 'varchar', 'length' => 16, 'nullable' => false],
        'nisn' => ['type' => 'varchar', 'length' => 10, 'nullable' => false],
        'birth_place' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'birth_date' => ['type' => 'date', 'nullable' => false],
        'gender' => ['type' => 'enum', 'values' => ['Laki-laki', 'Perempuan'], 'nullable' => false],
        'religion' => ['type' => 'varchar', 'length' => 20, 'nullable' => false],
        'email' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'phone' => ['type' => 'varchar', 'length' => 15, 'nullable' => false],
        'status' => ['type' => 'enum', 'values' => ['Draft', 'Submitted', 'Verified', 'Rejected', 'Released'], 'nullable' => false, 'default' => 'Draft'],
        'academic_year_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'academic_years.id', 'nullable' => true],
        'wave_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'waves.id', 'nullable' => true],
        'admission_path_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'admission_paths.id', 'nullable' => true],
        'class_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'classes.id', 'nullable' => true]
    ];

    protected array $seed = [];

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}

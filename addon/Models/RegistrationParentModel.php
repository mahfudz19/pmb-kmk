<?php

namespace Addon\Models;

use App\Core\Database\Model;

class RegistrationParentModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'registration_parents';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'registration_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'registrations.id', 'unique' => true, 'nullable' => false],
        'father_name' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'father_education' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'father_occupation' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'father_income' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'mother_name' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'mother_education' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'mother_occupation' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'mother_income' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'guardian_name' => ['type' => 'varchar', 'length' => 100, 'nullable' => true],
        'guardian_education' => ['type' => 'varchar', 'length' => 50, 'nullable' => true],
        'guardian_occupation' => ['type' => 'varchar', 'length' => 50, 'nullable' => true],
        'guardian_income' => ['type' => 'varchar', 'length' => 50, 'nullable' => true]
    ];

    protected array $seed = [];

    public function findByRegistrationId(int $registrationId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE registration_id = :registration_id LIMIT 1");
        $stmt->execute(['registration_id' => $registrationId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}

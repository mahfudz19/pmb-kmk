<?php

namespace Addon\Models;

use App\Core\Database\Model;

class RegistrationSpecialNeedModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'registration_special_needs';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'registration_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'registrations.id', 'unique' => true, 'nullable' => false],
        'has_special_needs' => ['type' => 'varchar', 'length' => 5, 'nullable' => false, 'default' => 'Tidak'],
        'student_needs' => ['type' => 'text', 'nullable' => true],
        'father_needs' => ['type' => 'text', 'nullable' => true],
        'mother_needs' => ['type' => 'text', 'nullable' => true],
        'guardian_needs' => ['type' => 'text', 'nullable' => true]
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

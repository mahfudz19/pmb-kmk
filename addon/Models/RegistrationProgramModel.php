<?php

namespace Addon\Models;

use App\Core\Database\Model;

class RegistrationProgramModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'registration_programs';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'registration_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'registrations.id', 'unique' => true, 'nullable' => false],
        'program1_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'study_programs.id', 'nullable' => false],
        'program2_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'study_programs.id', 'nullable' => true]
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

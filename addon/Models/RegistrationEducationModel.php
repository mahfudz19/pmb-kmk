<?php

namespace Addon\Models;

use App\Core\Database\Model;

class RegistrationEducationModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'registration_educations';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'registration_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'registrations.id', 'unique' => true, 'nullable' => false],
        'school_name' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'school_major' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'graduation_year' => ['type' => 'varchar', 'length' => 4, 'nullable' => false],
        'diploma_number' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'average_score' => ['type' => 'decimal', 'precision' => 4, 'scale' => 2, 'nullable' => false],
        'school_address' => ['type' => 'varchar', 'length' => 100, 'nullable' => true],
        'school_address_id_wil' => ['type' => 'varchar', 'length' => 15, 'nullable' => true]
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

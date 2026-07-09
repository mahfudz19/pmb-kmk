<?php

namespace Addon\Models;

use App\Core\Database\Model;

class RegistrationAddressModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'registration_addresses';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'registration_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'registrations.id', 'unique' => true, 'nullable' => false],
        'province' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'city' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'district' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'subdistrict' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'postal_code' => ['type' => 'varchar', 'length' => 5, 'nullable' => false],
        'address' => ['type' => 'text', 'nullable' => false]
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

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
        'province' => ['type' => 'varchar', 'length' => 100, 'nullable' => true],
        'city' => ['type' => 'varchar', 'length' => 100, 'nullable' => true],
        'district' => ['type' => 'varchar', 'length' => 100, 'nullable' => true],
        'subdistrict' => ['type' => 'varchar', 'length' => 100, 'nullable' => true],
        'postal_code' => ['type' => 'varchar', 'length' => 5, 'nullable' => true],
        'address' => ['type' => 'text', 'nullable' => true],
        'citizenship' => ['type' => 'varchar', 'length' => 50, 'nullable' => true],
        'npwp' => ['type' => 'varchar', 'length' => 30, 'nullable' => true],
        'street' => ['type' => 'varchar', 'length' => 100, 'nullable' => true],
        'telephone' => ['type' => 'varchar', 'length' => 15, 'nullable' => true],
        'dusun' => ['type' => 'varchar', 'length' => 50, 'nullable' => true],
        'rt' => ['type' => 'varchar', 'length' => 5, 'nullable' => true],
        'rw' => ['type' => 'varchar', 'length' => 5, 'nullable' => true],
        'kps_receiver' => ['type' => 'varchar', 'length' => 5, 'nullable' => true],
        'transportation' => ['type' => 'varchar', 'length' => 50, 'nullable' => true],
        'living_type' => ['type' => 'varchar', 'length' => 50, 'nullable' => true]
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

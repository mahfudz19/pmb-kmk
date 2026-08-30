<?php

namespace Addon\Models;

use App\Core\Database\Model;

class ReRegistrationModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 're_registrations';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'registration_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'registrations.id', 'unique' => true, 'nullable' => false],
        'skl_path' => ['type' => 'varchar', 'length' => 255, 'nullable' => true],
        'health_path' => ['type' => 'varchar', 'length' => 255, 'nullable' => true],
        'statement_path' => ['type' => 'varchar', 'length' => 255, 'nullable' => true],
        'payment_path' => ['type' => 'varchar', 'length' => 255, 'nullable' => true],
        'payment_amount' => ['type' => 'decimal', 'precision' => 12, 'scale' => 2, 'nullable' => true],
        'status' => ['type' => 'enum', 'values' => ['Pending', 'Approved', 'Rejected'], 'nullable' => false, 'default' => 'Pending'],
        'rejection_reason' => ['type' => 'text', 'nullable' => true],
        'verified_by' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'users.id', 'nullable' => true],
        'verified_at' => ['type' => 'datetime', 'nullable' => true],
        'dynamic_documents' => ['type' => 'text', 'nullable' => true],
        'id_payment' => ['type' => 'int', 'nullable' => true],
        'payment_type' => ['type' => 'varchar', 'length' => 20, 'nullable' => true, 'default' => 'manual']
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

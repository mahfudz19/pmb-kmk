<?php

namespace Addon\Models;

use App\Core\Database\Model;

class RegistrationPaymentModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'registration_payments';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'registration_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'registrations.id', 'unique' => true, 'nullable' => false],
        'bank_name' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'account_name' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'amount' => ['type' => 'decimal', 'precision' => 12, 'scale' => 2, 'nullable' => false],
        'payment_date' => ['type' => 'date', 'nullable' => false],
        'file_path' => ['type' => 'varchar', 'length' => 255, 'nullable' => false],
        'status' => ['type' => 'enum', 'values' => ['Pending', 'Approved', 'Rejected'], 'nullable' => false, 'default' => 'Pending'],
        'rejection_reason' => ['type' => 'text', 'nullable' => true],
        'verified_at' => ['type' => 'datetime', 'nullable' => true],
        'verified_by' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'users.id', 'nullable' => true]
    ];

    protected array $seed = [];

    public function findByRegistrationId(int $registrationId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE registration_id = :registration_id LIMIT 1");
        $stmt->execute(['registration_id' => $registrationId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function getAllPending(): array
    {
        $stmt = $this->db->prepare("
            SELECT rp.*, r.full_name, u.email 
            FROM {$this->table} rp
            JOIN registrations r ON rp.registration_id = r.id
            JOIN users u ON r.user_id = u.id
            ORDER BY rp.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPaginatedPayments(int $limit, int $offset): array
    {
        $stmt = $this->db->prepare("
            SELECT rp.*, r.full_name, u.email 
            FROM {$this->table} rp
            JOIN registrations r ON rp.registration_id = r.id
            JOIN users u ON r.user_id = u.id
            ORDER BY rp.created_at DESC
            LIMIT " . $limit . " OFFSET " . $offset . "
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPaymentsCount(): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM {$this->table} rp
            JOIN registrations r ON rp.registration_id = r.id
            JOIN users u ON r.user_id = u.id
        ");
        $stmt->execute();
        return (int) ($stmt->fetch()['count'] ?? 0);
    }
}

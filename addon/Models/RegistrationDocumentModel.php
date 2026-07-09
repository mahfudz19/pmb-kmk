<?php

namespace Addon\Models;

use App\Core\Database\Model;

class RegistrationDocumentModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'registration_documents';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'registration_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'registrations.id', 'nullable' => false],
        'document_type_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'document_types.id', 'nullable' => false],
        'file_path' => ['type' => 'varchar', 'length' => 255, 'nullable' => false],
        'status' => ['type' => 'enum', 'values' => ['Pending', 'Approved', 'Rejected'], 'nullable' => false, 'default' => 'Pending'],
        'rejection_reason' => ['type' => 'text', 'nullable' => true]
    ];

    protected array $seed = [];

    public function findByRegistrationId(int $registrationId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE registration_id = :registration_id");
        $stmt->execute(['registration_id' => $registrationId]);
        return $stmt->fetchAll();
    }

    public function findByRegAndType(int $registrationId, int $documentTypeId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE registration_id = :registration_id AND document_type_id = :document_type_id LIMIT 1");
        $stmt->execute([
            'registration_id' => $registrationId,
            'document_type_id' => $documentTypeId
        ]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}

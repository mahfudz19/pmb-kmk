<?php

namespace Addon\Models;

use App\Core\Database\Model;

class SelectionResultModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'selection_results';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'registration_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'registrations.id', 'unique' => true, 'nullable' => false],
        'test_score' => ['type' => 'decimal', 'precision' => 5, 'scale' => 2, 'nullable' => true],
        'interview_score' => ['type' => 'decimal', 'precision' => 5, 'scale' => 2, 'nullable' => true],
        'interview_notes' => ['type' => 'text', 'nullable' => true],
        'status' => ['type' => 'enum', 'values' => ['Pending', 'Lulus', 'Cadangan', 'Tidak Lulus'], 'nullable' => false, 'default' => 'Pending'],
        'passed_program_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'study_programs.id', 'nullable' => true],
        'notes' => ['type' => 'text', 'nullable' => true],
        'is_published' => ['type' => 'boolean', 'nullable' => false, 'default' => 0]
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

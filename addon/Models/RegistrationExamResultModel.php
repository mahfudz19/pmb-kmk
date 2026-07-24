<?php

namespace Addon\Models;

use App\Core\Database\Model;

class RegistrationExamResultModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'registration_exam_results';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'registration_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'registrations.id', 'nullable' => false],
        'study_program_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'study_programs.id', 'nullable' => true],
        'stage_index' => ['type' => 'integer', 'nullable' => false],
        'status' => ['type' => 'enum', 'values' => ['Pending', 'Lulus', 'Tidak Lulus', 'Cadangan'], 'nullable' => false, 'default' => 'Pending'],
        'score' => ['type' => 'decimal', 'precision' => 5, 'scale' => 2, 'nullable' => true],
        'notes' => ['type' => 'text', 'nullable' => true]
    ];

    protected array $seed = [];

    public function findByRegistrationId(int $registrationId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE registration_id = :registration_id ORDER BY stage_index ASC");
        $stmt->execute(['registration_id' => $registrationId]);
        return $stmt->fetchAll();
    }
}

<?php

namespace Addon\Models;

use App\Core\Database\Model;

class WaveStudyProgramModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'wave_study_programs';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'wave_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'waves.id', 'nullable' => false],
        'study_program_id' => ['type' => 'bigint', 'unsigned' => true, 'foreign' => 'study_programs.id', 'nullable' => false],
        'reregistration_fee_total' => ['type' => 'decimal', 'precision' => 12, 'scale' => 2, 'nullable' => true],
        'reregistration_fee_archive' => ['type' => 'varchar', 'length' => 255, 'nullable' => true],
        'required_documents' => ['type' => 'text', 'nullable' => true],
        'exam_stages' => ['type' => 'text', 'nullable' => true]
    ];

    protected array $seed = [];

    public function findByWaveId(int $waveId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE wave_id = :wave_id");
        $stmt->execute(['wave_id' => $waveId]);
        return $stmt->fetchAll();
    }

    public function findByWaveAndProgram(int $waveId, int $programId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE wave_id = :wave_id AND study_program_id = :program_id LIMIT 1");
        $stmt->execute(['wave_id' => $waveId, 'program_id' => $programId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}

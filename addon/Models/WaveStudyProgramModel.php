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

    protected array $seed = [
        [
            'wave_id' => 1,
            'study_program_id' => 1,
            'reregistration_fee_total' => 7500000.00,
            'reregistration_fee_archive' => null,
            'required_documents' => '[{"document_type_id":1,"name":"Scan Rapor Kelas XII / Ijazah SMA","description":"Scan berwarna asli / legalisir"},{"document_type_id":2,"name":"Scan Kartu Keluarga (KK)","description":"Scan berwarna asli"},{"document_type_id":3,"name":"Scan KTP / Kartu Pelajar","description":"Scan KTP asli / Kartu Pelajar"},{"document_type_id":4,"name":"Pas Foto Flat Latar Merah","description":"Ukuran 3x4 formal"}]',
            'exam_stages' => '[{"stage_number":1,"date":"2026-07-20","time":"09:00 - 11:00 WIB","place":"Virtual Zoom / Aplikasi CBT","type":"online","description":"Ujian CBT Mandiri Potensi Akademik"}]'
        ],
        [
            'wave_id' => 1,
            'study_program_id' => 2,
            'reregistration_fee_total' => 7000000.00,
            'reregistration_fee_archive' => null,
            'required_documents' => '[{"document_type_id":1,"name":"Scan Rapor Kelas XII / Ijazah SMA","description":"Scan berwarna asli / legalisir"},{"document_type_id":2,"name":"Scan Kartu Keluarga (KK)","description":"Scan berwarna asli"},{"document_type_id":3,"name":"Scan KTP / Kartu Pelajar","description":"Scan KTP asli / Kartu Pelajar"},{"document_type_id":4,"name":"Pas Foto Flat Latar Merah","description":"Ukuran 3x4 formal"}]',
            'exam_stages' => '[{"stage_number":1,"date":"2026-07-20","time":"09:00 - 11:00 WIB","place":"Virtual Zoom / Aplikasi CBT","type":"online","description":"Ujian CBT Mandiri Potensi Akademik"}]'
        ],
        [
            'wave_id' => 1,
            'study_program_id' => 3,
            'reregistration_fee_total' => 8000000.00,
            'reregistration_fee_archive' => null,
            'required_documents' => '[{"document_type_id":1,"name":"Scan Rapor Kelas XII / Ijazah SMA","description":"Scan berwarna asli / legalisir"},{"document_type_id":2,"name":"Scan Kartu Keluarga (KK)","description":"Scan berwarna asli"},{"document_type_id":3,"name":"Scan KTP / Kartu Pelajar","description":"Scan KTP asli / Kartu Pelajar"},{"document_type_id":4,"name":"Pas Foto Flat Latar Merah","description":"Ukuran 3x4 formal"}]',
            'exam_stages' => '[{"stage_number":1,"date":"2026-07-20","time":"09:00 - 11:00 WIB","place":"Virtual Zoom / Aplikasi CBT","type":"online","description":"Ujian CBT Mandiri Potensi Akademik"}]'
        ],
        [
            'wave_id' => 1,
            'study_program_id' => 4,
            'reregistration_fee_total' => 6000000.00,
            'reregistration_fee_archive' => null,
            'required_documents' => '[{"document_type_id":1,"name":"Scan Rapor Kelas XII / Ijazah SMA","description":"Scan berwarna asli / legalisir"},{"document_type_id":2,"name":"Scan Kartu Keluarga (KK)","description":"Scan berwarna asli"},{"document_type_id":3,"name":"Scan KTP / Kartu Pelajar","description":"Scan KTP asli / Kartu Pelajar"},{"document_type_id":4,"name":"Pas Foto Flat Latar Merah","description":"Ukuran 3x4 formal"}]',
            'exam_stages' => '[{"stage_number":1,"date":"2026-07-20","time":"09:00 - 11:00 WIB","place":"Virtual Zoom / Aplikasi CBT","type":"online","description":"Ujian CBT Mandiri Potensi Akademik"}]'
        ],
        [
            'wave_id' => 2,
            'study_program_id' => 1,
            'reregistration_fee_total' => 7500000.00,
            'reregistration_fee_archive' => null,
            'required_documents' => '[{"document_type_id":1,"name":"Scan Rapor Kelas XII / Ijazah SMA","description":"Scan berwarna asli / legalisir"},{"document_type_id":2,"name":"Scan Kartu Keluarga (KK)","description":"Scan berwarna asli"},{"document_type_id":3,"name":"Scan KTP / Kartu Pelajar","description":"Scan KTP asli / Kartu Pelajar"},{"document_type_id":4,"name":"Pas Foto Flat Latar Merah","description":"Ukuran 3x4 formal"}]',
            'exam_stages' => '[{"stage_number":1,"date":"2026-09-20","time":"09:00 - 11:00 WIB","place":"Virtual Zoom / Aplikasi CBT","type":"online","description":"Ujian CBT Mandiri Potensi Akademik"}]'
        ],
        [
            'wave_id' => 2,
            'study_program_id' => 2,
            'reregistration_fee_total' => 7000000.00,
            'reregistration_fee_archive' => null,
            'required_documents' => '[{"document_type_id":1,"name":"Scan Rapor Kelas XII / Ijazah SMA","description":"Scan berwarna asli / legalisir"},{"document_type_id":2,"name":"Scan Kartu Keluarga (KK)","description":"Scan berwarna asli"},{"document_type_id":3,"name":"Scan KTP / Kartu Pelajar","description":"Scan KTP asli / Kartu Pelajar"},{"document_type_id":4,"name":"Pas Foto Flat Latar Merah","description":"Ukuran 3x4 formal"}]',
            'exam_stages' => '[{"stage_number":1,"date":"2026-09-20","time":"09:00 - 11:00 WIB","place":"Virtual Zoom / Aplikasi CBT","type":"online","description":"Ujian CBT Mandiri Potensi Akademik"}]'
        ],
        [
            'wave_id' => 2,
            'study_program_id' => 3,
            'reregistration_fee_total' => 8000000.00,
            'reregistration_fee_archive' => null,
            'required_documents' => '[{"document_type_id":1,"name":"Scan Rapor Kelas XII / Ijazah SMA","description":"Scan berwarna asli / legalisir"},{"document_type_id":2,"name":"Scan Kartu Keluarga (KK)","description":"Scan berwarna asli"},{"document_type_id":3,"name":"Scan KTP / Kartu Pelajar","description":"Scan KTP asli / Kartu Pelajar"},{"document_type_id":4,"name":"Pas Foto Flat Latar Merah","description":"Ukuran 3x4 formal"}]',
            'exam_stages' => '[{"stage_number":1,"date":"2026-09-20","time":"09:00 - 11:00 WIB","place":"Virtual Zoom / Aplikasi CBT","type":"online","description":"Ujian CBT Mandiri Potensi Akademik"}]'
        ],
        [
            'wave_id' => 2,
            'study_program_id' => 4,
            'reregistration_fee_total' => 6000000.00,
            'reregistration_fee_archive' => null,
            'required_documents' => '[{"document_type_id":1,"name":"Scan Rapor Kelas XII / Ijazah SMA","description":"Scan berwarna asli / legalisir"},{"document_type_id":2,"name":"Scan Kartu Keluarga (KK)","description":"Scan berwarna asli"},{"document_type_id":3,"name":"Scan KTP / Kartu Pelajar","description":"Scan KTP asli / Kartu Pelajar"},{"document_type_id":4,"name":"Pas Foto Flat Latar Merah","description":"Ukuran 3x4 formal"}]',
            'exam_stages' => '[{"stage_number":1,"date":"2026-09-20","time":"09:00 - 11:00 WIB","place":"Virtual Zoom / Aplikasi CBT","type":"online","description":"Ujian CBT Mandiri Potensi Akademik"}]'
        ]
    ];

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

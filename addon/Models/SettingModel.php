<?php

namespace Addon\Models;

use App\Core\Database\Model;

class SettingModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'settings';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'key' => ['type' => 'varchar', 'length' => 100, 'nullable' => false, 'unique' => true],
        'value' => ['type' => 'text', 'nullable' => true]
    ];

    protected array $seed = [
        ['key' => 'campus_name', 'value' => 'Kampus Mandiri Kencana'],
        ['key' => 'campus_address', 'value' => 'Jl. Raya Pendidikan No. 45, Jakarta'],
        ['key' => 'campus_email', 'value' => 'info@kmk.ac.id'],
        ['key' => 'campus_phone', 'value' => '021-12345678'],
        ['key' => 'campus_logo', 'value' => '/logo_app/mazu-logo.svg'],
        ['key' => 'registration_number_format', 'value' => 'PMB-{YEAR}-{SEQ}'],
        ['key' => 'smtp_host', 'value' => 'smtp.mailtrap.io'],
        ['key' => 'smtp_port', 'value' => '2525'],
        ['key' => 'smtp_username', 'value' => 'smtp_user'],
        ['key' => 'smtp_password', 'value' => 'smtp_password'],
        ['key' => 'smtp_encryption', 'value' => 'tls'],
        ['key' => 'smtp_from_address', 'value' => 'noreply@pmb.com'],
        ['key' => 'smtp_from_name', 'value' => 'PMB KMK'],
        ['key' => 'registration_fee_total', 'value' => '100000'],
        ['key' => 'registration_fee_archive', 'value' => '']
    ];
}

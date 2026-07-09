<?php

namespace Addon\Models;

use App\Core\Database\Model;

class AuditLogModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'audit_logs';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'user_id' => ['type' => 'integer', 'nullable' => true],
        'username' => ['type' => 'varchar', 'length' => 100, 'nullable' => true],
        'ip_address' => ['type' => 'varchar', 'length' => 45, 'nullable' => true],
        'user_agent' => ['type' => 'text', 'nullable' => true],
        'activity' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'description' => ['type' => 'text', 'nullable' => true]
    ];
}

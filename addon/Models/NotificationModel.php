<?php

namespace Addon\Models;

use App\Core\Database\Model;

class NotificationModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'notifications';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'user_id' => ['type' => 'bigint', 'unsigned' => true, 'nullable' => true],
        'title' => ['type' => 'varchar', 'length' => 150, 'nullable' => false],
        'message' => ['type' => 'text', 'nullable' => false],
        'type' => ['type' => 'varchar', 'length' => 20, 'nullable' => false, 'default' => 'info'],
        'is_read' => ['type' => 'boolean', 'nullable' => false, 'default' => 0]
    ];

    protected array $seed = [];
}

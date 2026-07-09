<?php

namespace Addon\Models;

use App\Core\Database\Model;

class NotificationHistoryModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'notification_history';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'user_id' => ['type' => 'bigint', 'unsigned' => true, 'nullable' => true],
        'recipient_email' => ['type' => 'varchar', 'length' => 100, 'nullable' => true],
        'channel' => ['type' => 'varchar', 'length' => 20, 'nullable' => false],
        'title' => ['type' => 'varchar', 'length' => 150, 'nullable' => false],
        'content' => ['type' => 'text', 'nullable' => false],
        'status' => ['type' => 'varchar', 'length' => 20, 'nullable' => false, 'default' => 'sent']
    ];

    protected array $seed = [];
}

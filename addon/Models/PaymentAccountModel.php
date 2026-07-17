<?php

namespace Addon\Models;

use App\Core\Database\Model;

class PaymentAccountModel extends Model
{
    protected ?string $connection = 'mysql';
    protected string $table = 'payment_accounts';
    protected bool $timestamps = true;

    protected array $schema = [
        'id' => ['type' => 'id', 'primary' => true, 'auto_increment' => true],
        'bank_name' => ['type' => 'varchar', 'length' => 50, 'nullable' => false],
        'account_number' => ['type' => 'varchar', 'length' => 30, 'nullable' => false],
        'account_holder' => ['type' => 'varchar', 'length' => 100, 'nullable' => false],
        'is_active' => ['type' => 'boolean', 'nullable' => false, 'default' => 1]
    ];

    protected array $seed = [
        [
            'bank_name' => 'Mandiri',
            'account_number' => '124-000-987-6543',
            'account_holder' => 'PMB KAMPUS MANDIRI KENCANA',
            'is_active' => 1
        ]
    ];

    public function getActiveAccounts(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE is_active = 1");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

<?php

namespace App\Core\Database;

use App\Core\Database\Database;
use App\Core\Database\DatabaseManager;

abstract class Model
{
  protected Database $db;

  protected ?string $connection = null;
  protected string $table = '';
  protected array $schema = [];
  protected bool $timestamps = true;
  protected string $createdAtColumn = 'created_at';
  protected string $updatedAtColumn = 'updated_at';

  protected array $seed = [];

  public function __construct(DatabaseManager $manager)
  {
    $connectionName = $this->connection;

    if ($connectionName === null || $connectionName === '') {
      $this->db = $manager->connection();
    } else {
      $this->db = $manager->connection($connectionName);
    }
  }

  public function getDb(): Database
  {
    return $this->db;
  }

  public function getConnectionName(): ?string
  {
    return $this->connection;
  }

  public function getTableName(): string
  {
    return $this->table;
  }

  public function getSchema(): array
  {
    return $this->schema;
  }

  public function usesTimestamps(): bool
  {
    return $this->timestamps;
  }

  public function getCreatedAtColumn(): string
  {
    return $this->createdAtColumn;
  }

  public function getUpdatedAtColumn(): string
  {
    return $this->updatedAtColumn;
  }

  public function getSeed(): array
  {
    return $this->seed;
  }

  public function all(): array
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function find(string|int $id): ?array
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
  }

  public function insert(array $data): int
  {
    $validData = [];
    foreach ($data as $key => $value) {
      if (isset($this->schema[$key]) && $key !== 'id') {
        $validData[$key] = $value;
      }
    }

    if ($this->timestamps) {
      $created = $this->createdAtColumn;
      $updated = $this->updatedAtColumn;
      if (isset($this->schema[$created]) && !isset($validData[$created])) {
        $validData[$created] = date('Y-m-d H:i:s');
      }
      if (isset($this->schema[$updated]) && !isset($validData[$updated])) {
        $validData[$updated] = date('Y-m-d H:i:s');
      }
    }

    $columns = implode(', ', array_keys($validData));
    $placeholders = ':' . implode(', :', array_keys($validData));
    $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

    if ($this->db->query($sql, $validData)) {
      return (int) $this->db->lastInsertId();
    }

    throw new \PDOException("Gagal menyisipkan data baru");
  }

  public function updateById(string|int $id, array $data): bool
  {
    $validData = [];
    foreach ($data as $key => $value) {
      if (isset($this->schema[$key]) && $key !== 'id') {
        $validData[$key] = $value;
      }
    }

    if (empty($validData)) {
      return false;
    }

    if ($this->timestamps) {
      $updated = $this->updatedAtColumn;
      if (isset($this->schema[$updated]) && !isset($validData[$updated])) {
        $validData[$updated] = date('Y-m-d H:i:s');
      }
    }

    $setParts = [];
    foreach ($validData as $column => $value) {
      $setParts[] = "{$column} = :{$column}";
    }

    $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";
    $validData['id'] = $id;

    return $this->db->query($sql, $validData);
  }

  public function deleteById(string|int $id): bool
  {
    $sql = "DELETE FROM {$this->table} WHERE id = :id";
    return $this->db->query($sql, ['id' => $id]);
  }
}

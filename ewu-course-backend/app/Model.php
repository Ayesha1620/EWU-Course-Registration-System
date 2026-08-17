<?php

namespace App;

use PDO;

// প্রতিটা table এর জন্য একটা Model class — এখানে থাকা generic জিনিসগুলো সব Model পায়।
// search, Insert, Update, Delete — যেকোনো লেনদেনের পর ফাইনাল result return করে,
// যাতে controller সরাসরি DB query না লিখে Model-কে call করে।

abstract class Model
{
    protected $db;
    protected $table;        // table name (child এ define হয়)
    protected $primaryKey;   // primary key column (child এ define হয়)
    protected $fillable = []; // শুধু এই column গুলো insert/update হতে পারবে (safety)

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // সব row
    public function all(string $orderBy = ''): array
    {
        $orderBy = $orderBy !== ''
            ? ' ORDER BY ' . preg_replace('/[^a-zA-Z0-9_,\s]/', '', $orderBy)
            : '';
        return $this->db->query("SELECT * FROM {$this->table}{$orderBy}")->fetchAll();
    }

    // primary key দিয়ে একটা row
    public function find($id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // নির্দিষ্ট column এর মান দিয়ে সব row (যেমন student এর সব advisor)
    public function where(string $column, $value): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    // নতুন row
    public function create(array $data): array
    {
        $data = $this->filter($data);

        // এই DB-তে primary key auto-increment নয়, তাই আগের সবচেয়ে বড়টার +1
        if (!array_key_exists($this->primaryKey, $data)
            && in_array($this->primaryKey, $this->fillable, true)) {
            $data[$this->primaryKey] = (int)$this->db
                ->query("SELECT COALESCE(MAX({$this->primaryKey}), 0) + 1 FROM {$this->table}")
                ->fetchColumn();
        }

        $columns = array_keys($data);
        $placeholders = array_map(fn($c) => ":{$c}", $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $data;
    }

    // primary key দিয়ে update, true/false return
    public function update($id, array $data): bool
    {
        $data = $this->filter($data);
        if (count($data) === 0) {
            return false;
        }

        $sets = array_map(fn($c) => "{$c} = :{$c}", array_keys($data));
        $data['__pk'] = $id;

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :__pk',
            $this->table,
            implode(', ', $sets),
            $this->primaryKey
        );

        return $this->db->prepare($sql)->execute($data);
    }

    // primary key দিয়ে delete
    public function delete($id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    // দুটো column দিয়ে delete (যেমন prerequisite এর composite key)
    public function deleteWhere(string $column, $value): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$column} = ?");
        return $stmt->execute([$value]);
    }

    // শুধু fillable column গুলোই insert/update এ যেতে দেয়
    private function filter(array $data): array
    {
        return array_intersect_key($data, array_flip($this->fillable));
    }
}
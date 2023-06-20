<?php

namespace Hms\Gateways;

use mysqli;
use mysqli_result;

abstract class BasicTableGateway
{
    protected $conn;
    protected $table;
    protected $fields;
    protected $primary = 'id';

    public function __construct()
    {
        $this->conn = new mysqli("mysql", "root", "test05", "hospitalmanagement");
    }

    public function all(): mysqli_result|false
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table");
        $stmt->execute();
        return $stmt->get_result();
    }

    public function findById($id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE $this->primary = $id");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function findByFields(array $fields): mysqli_result|false
    {
        $sql = "SELECT * FROM $this->table WHERE ";
        $type = "";

        $index = 0;
        foreach ($fields as $key=>$value) {
            $type .= $this->fields[$key];
            $sql .= "$key = ?";

            if ($index < count($fields) -1) {
                $sql .= " AND ";
            }

            $index++;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($type, ...array_values($fields));
        $stmt->execute();
        return $stmt->get_result();
    }

    public function insert($data): int
    {
        $this->validate($data);

        $fields = '`'.implode("`,`", array_keys($data)).'`';
        $placeholders = str_repeat('?,', count($data) - 1) . '?';

        $stmt = $this->conn->prepare("INSERT INTO `$this->table` ($fields) VALUES ($placeholders)");
        $types = ""; // Variable hinzugefügt

        foreach ($data as $key=>$value) {
            $types .= $this->fields[$key];
        }

        $stmt->bind_param($types, ...array_values($data));
        $stmt->execute();

        return 0;
    }

    public function update(int $id, array $data): void
    {
        //$this->validate($data);

        $params = [];
        $set = "";
        foreach ($data as $key => $value) {
            $set .= "`$key` = ?,";
            $params[] = $value;
        }
        $set = rtrim($set, ",");

        $stmt = $this->conn->prepare("UPDATE $this->table SET $set WHERE $this->primary=?");

        $type = "";
        foreach ($data as $key=>$value) {
            $type .= $this->fields[$key];
        }

        $params[] = $id;
        $type .= "i";

        $stmt->bind_param($type, ...$params);
        $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM `$this->table` WHERE `$this->primary`=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    public function getRelation(int $id, string $table, string $type, string $intermediate = null)
    {
        if ($type == "n") {
            $stmt = $this->conn->prepare("SELECT * FROM $table AS p LEFT JOIN $intermediate AS s ON p.id = s.{$table}_id WHERE s.{$this->table}_id = $id");
            
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM $table WHERE id = $id");
        }

        $stmt->execute();

        return $stmt->get_result();
    }

    public function saveRelation(int $objId, array $relationIds, string $table, string $intermediate = null)
    {
        $stmt = $this->conn->prepare("DELETE FROM $intermediate WHERE {$this->table}_id = $objId");
        $stmt->execute();
        foreach ($relationIds as $relationId) {
            $stmt = $this->conn->prepare("INSERT INTO $intermediate ({$this->table}_id, {$table}_id) VALUES ($objId, $relationId)");
            $stmt->execute();
        }
    }

    protected function validate($data)
    {
        $diff = array_diff(array_keys($data), array_keys($this->fields));
        if ($diff) {
            throw new \InvalidArgumentException("Unknown field(s): ". implode($diff));
        }
    }
}

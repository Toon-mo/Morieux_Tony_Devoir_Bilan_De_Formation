<?php

namespace Models;

use PDO;

/**
 * MachineModel
 * 
 * Rôle : Gérer le référentiel des machines laser (Fibre, CO2, Diode)
 */
class MachineModel
{
    private PDO $conn;
    private string $table_name = "machines";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Récupérer toutes les machines
     * Test Postman : GET /api/machines.php
     */
    public function getAllMachines(): array
    {
        $query = "SELECT * FROM {$this->table_name} ORDER BY brand ASC, name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer une machine par ID
     */
    public function getMachineById(int $id): array|false
    {
        $query = "SELECT * FROM {$this->table_name} WHERE machine_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Créer une machine (aligné sur ton schéma SQL)
     */
    public function createMachine(array $data): bool
    {
        $query = "INSERT INTO {$this->table_name} (name, model, laser_type, is_mopa, brand) 
                  VALUES (:name, :model, :laser_type, :is_mopa, :brand)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name'       => $data['name'],
            ':model'      => $data['model'] ?? null,
            ':laser_type' => $data['laser_type'], // FIBRE, CO2, DIODE
            ':is_mopa'    => $data['is_mopa'] ?? 0,
            ':brand'      => $data['brand']
        ]);
    }

    public function updateMachine(int $id, array $data): bool
    {
        $query = "UPDATE {$this->table_name} 
                  SET name = :name, model = :model, laser_type = :laser_type, 
                      is_mopa = :is_mopa, brand = :brand 
                  WHERE machine_id = :id";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name'       => $data['name'],
            ':model'      => $data['model'],
            ':laser_type' => $data['laser_type'],
            ':is_mopa'    => $data['is_mopa'],
            ':brand'      => $data['brand'],
            ':id'         => $id
        ]);
    }

    public function deleteMachine(int $id): bool
    {
        $query = "DELETE FROM {$this->table_name} WHERE machine_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

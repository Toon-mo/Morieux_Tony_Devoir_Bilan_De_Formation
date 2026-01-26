<?php

namespace Models;

use PDO;

/**
 * MaterialModel
 *
 * Rôle :
 * - Gérer les opérations CRUD sur la table `materials`
 * - Fournir les listes de matériaux pour les sélecteurs du formulaire
 */
class MaterialModel
{
    private PDO $conn;
    private string $table_name = "materials";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Récupérer tous les matériaux (triés par nom)
     * 
     * Test Postman : GET /api/materials.php
     */
    public function getAllMaterials(): array
    {
        $query = "SELECT * FROM {$this->table_name} ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer un matériau spécifique
     * 
     * Test Postman : GET /api/materials.php?id={id}
     */
    public function getMaterialById(int $id): array|false
    {
        $query = "SELECT * FROM {$this->table_name} WHERE material_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les matériaux compatibles avec une machine donnée
     * Basé sur les tests déjà existants en base
     * 
     * Test Postman : GET /api/materials.php?machine_id={id}
     */
    public function getMaterialsByMachine(int $machineId): array
    {
        $query = "SELECT DISTINCT m.* 
                  FROM {$this->table_name} m
                  INNER JOIN tests t ON m.material_id = t.material_id
                  WHERE t.machine_id = :machine_id
                  ORDER BY m.name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':machine_id', $machineId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Créer un nouveau matériau
     * 
     * Test Postman : POST /api/materials.php
     */
    public function createMaterial(array $data): bool
    {
        $query = "INSERT INTO {$this->table_name} (name, category, thickness, color) 
                  VALUES (:name, :category, :thickness, :color)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name'      => $data['name'],
            ':category'  => $data['category'] ?? null,
            ':thickness' => $data['thickness'] ?? null,
            ':color'     => $data['color'] ?? null,
        ]);
    }
}

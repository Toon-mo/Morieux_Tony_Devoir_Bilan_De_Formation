<?php

namespace Models;

use PDO;

class TestModel
{
    private PDO $conn;
    private string $table_name = "tests";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // Liste de tous les tests (CORRIGÉE avec category)
    public function getAllTests(): array
    {
        $query = "
        SELECT 
            t.test_id,
            t.title,
            t.image,
            t.description,
            u.username AS author,
            m.name AS machine_name,
            m.laser_type AS laser_type,
            mat.name AS material_name,
            mat.category AS material_category,
            p.speed,
            p.power,
            p.frequency,
            p.pulse
        FROM tests t
        LEFT JOIN users u ON t.user_id = u.user_id
        LEFT JOIN machines m ON t.machine_id = m.machine_id
        LEFT JOIN materials mat ON t.material_id = mat.material_id
        LEFT JOIN parameters p ON t.test_id = p.test_id
        ORDER BY t.created_at DESC
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Structure aplatie pour Vue.js
        return array_map(function ($row) {
            return [
                'test_id'           => $row['test_id'],
                'title'             => $row['title'],
                'image'             => $row['image'],
                'description'       => $row['description'],
                'author'            => $row['author'],
                'machine_name'      => $row['machine_name'],
                'laser_type'        => $row['laser_type'],
                'material_name'     => $row['material_name'],
                'material_category' => $row['material_category'], // ✅ AJOUTÉ
                'speed'             => $row['speed'],
                'power'             => $row['power'],
                'frequency'         => $row['frequency'],
                'pulse'             => $row['pulse'],
            ];
        }, $results);
    }

    // Détail complet d'un test
    public function getTestDetails(int $id)
    {
        $query = "
            SELECT 
                t.*,
                u.username AS author,
                m.name AS machine_name,
                m.laser_type AS laser_type,
                mat.name AS material_name,
                mat.category AS material_category,
                p.speed, p.power, p.frequency, p.pulse,
                p.z_offset, p.nb_passes, p.sweep, p.hatch, p.row_interval, p.wobble
            FROM " . $this->table_name . " t
            LEFT JOIN users u ON t.user_id = u.user_id
            LEFT JOIN machines m ON t.machine_id = m.machine_id
            LEFT JOIN materials mat ON t.material_id = mat.material_id
            LEFT JOIN parameters p ON t.test_id = p.test_id
            WHERE t.test_id = :id
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Création d'un test avec paramètres
    public function createTest(array $data): bool
    {
        try {
            $this->conn->beginTransaction();

            $stmtTest = $this->conn->prepare("
                INSERT INTO " . $this->table_name . " 
                (title, image, machine_id, material_id, user_id, description)
                VALUES (:title, :image, :machine_id, :material_id, :user_id, :description)
            ");
            $stmtTest->execute([
                ':title'       => $data['title'],
                ':image'       => $data['image'],
                ':machine_id'  => $data['machine_id'],
                ':material_id' => $data['material_id'],
                ':user_id'     => $data['user_id'],
                ':description' => $data['description'] ?? null
            ]);

            $testId = $this->conn->lastInsertId();

            $stmtParam = $this->conn->prepare("
                INSERT INTO parameters
                (test_id, speed, power, frequency, pulse, z_offset, nb_passes, sweep, hatch, row_interval, wobble)
                VALUES (:test_id, :speed, :power, :frequency, :pulse, :z_offset, :nb_passes, :sweep, :hatch, :row_interval, :wobble)
            ");
            $stmtParam->execute([
                ':test_id'      => $testId,
                ':speed'        => $data['speed'] ?? 0,
                ':power'        => $data['power'] ?? 0,
                ':frequency'    => $data['frequency'] ?? 0,
                ':pulse'        => $data['pulse'] ?? null,
                ':z_offset'     => $data['z_offset'] ?? 0,
                ':nb_passes'    => $data['nb_passes'] ?? 1,
                ':sweep'        => $data['sweep'] ?? 1,
                ':hatch'        => $data['hatch'] ?? 0,
                ':row_interval' => $data['row_interval'] ?? 0.05,
                ':wobble'       => $data['wobble'] ?? 0
            ]);

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Mise à jour d'un test avec paramètres
    public function updateTest(int $id, array $data): bool
    {
        try {
            $this->conn->beginTransaction();

            $stmtTest = $this->conn->prepare("
                UPDATE " . $this->table_name . "
                SET title = :title,
                    image = :image,
                    machine_id = :machine_id,
                    material_id = :material_id,
                    description = :description
                WHERE test_id = :id
            ");
            $stmtTest->execute([
                ':title'       => $data['title'],
                ':image'       => $data['image'],
                ':machine_id'  => $data['machine_id'],
                ':material_id' => $data['material_id'],
                ':description' => $data['description'] ?? null,
                ':id'          => $id
            ]);

            $stmtParam = $this->conn->prepare("
                UPDATE parameters
                SET speed = :speed, power = :power, frequency = :frequency, pulse = :pulse,
                    z_offset = :z_offset, nb_passes = :nb_passes, sweep = :sweep,
                    hatch = :hatch, row_interval = :row_interval, wobble = :wobble
                WHERE test_id = :id
            ");
            $stmtParam->execute([
                ':speed'        => $data['speed'] ?? 0,
                ':power'        => $data['power'] ?? 0,
                ':frequency'    => $data['frequency'] ?? 0,
                ':pulse'        => $data['pulse'] ?? null,
                ':z_offset'     => $data['z_offset'] ?? 0,
                ':nb_passes'    => $data['nb_passes'] ?? 1,
                ':sweep'        => $data['sweep'] ?? 1,
                ':hatch'        => $data['hatch'] ?? 0,
                ':row_interval' => $data['row_interval'] ?? 0.05,
                ':wobble'       => $data['wobble'] ?? 0,
                ':id'           => $id
            ]);

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Suppression d'un test et de ses paramètres
    public function deleteTest(int $id): bool
    {
        try {
            $this->conn->beginTransaction();

            $stmtParam = $this->conn->prepare("DELETE FROM parameters WHERE test_id = :id");
            $stmtParam->execute([':id' => $id]);

            $stmtTest = $this->conn->prepare("DELETE FROM " . $this->table_name . " WHERE test_id = :id");
            $stmtTest->execute([':id' => $id]);

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}

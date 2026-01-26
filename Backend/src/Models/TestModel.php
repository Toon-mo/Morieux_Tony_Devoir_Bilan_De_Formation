<?php

namespace Models;

use PDO;

/**
 * TestModel
 *
 * Rôle :
 * - Gérer toutes les opérations CRUD liées aux tests de gravure laser
 * - Centraliser l'accès aux tables `tests` et `parameters`
 * - Fournir les données aux contrôleurs appelés par Postman
 *
 * Architecture :
 * Postman → TestController → TestModel → Base de données
 */
class TestModel
{
    /**
     * Connexion PDO
     * @var PDO
     */
    private $conn;

    /**
     * Nom de la table principale
     * @var string
     */
    private $table_name = "tests";

    /**
     * Constructeur
     *
     * @param PDO $db Connexion injectée depuis Database
     */
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /* =========================
       ===== READ (GET) ========
       ========================= */

    /**
     * Récupérer la liste simplifiée des tests (page Home)
     *
     * @return array
     */
    public function getAllTests(): array
    {
        $query = "
            SELECT 
                t.test_id,
                t.title,
                t.image,
                m.name AS machine_name,
                mat.name AS material_name 
            FROM " . $this->table_name . " t
            LEFT JOIN machines m ON t.machine_id = m.machine_id
            LEFT JOIN materials mat ON t.material_id = mat.material_id
            ORDER BY t.created_at DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer le détail complet d'un test (page Détail)
     *
     * @param int $id
     * @return array|false
     */
    public function getTestDetails(int $id)
    {
        $query = "
            SELECT 
                t.*,
                m.name AS machine_name,
                mat.name AS material_name,
                u.username AS author,
                p.speed,
                p.power,
                p.frequency,
                p.pulse,
                p.z_offset,
                p.nb_passes,
                p.row_interval
            FROM " . $this->table_name . " t
            LEFT JOIN machines m ON t.machine_id = m.machine_id
            LEFT JOIN materials mat ON t.material_id = mat.material_id
            LEFT JOIN users u ON t.user_id = u.user_id
            LEFT JOIN parameters p ON t.test_id = p.test_id
            WHERE t.test_id = :id
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       ===== CREATE (POST) =====
       ========================= */

    /**
     * Créer un nouveau test avec ses paramètres
     *
     * @param array $data
     * @return bool
     */
    public function createTest(array $data): bool
    {
        try {
            $this->conn->beginTransaction();

            // 1. Insertion dans la table tests
            $queryTest = "
                INSERT INTO " . $this->table_name . "
                (title, image, machine_id, material_id, user_id, description)
                VALUES
                (:title, :image, :machine_id, :material_id, :user_id, :description)
            ";

            $stmtTest = $this->conn->prepare($queryTest);
            $stmtTest->execute([
                ':title'       => $data['title'],
                ':image'       => $data['image'],
                ':machine_id'  => $data['machine_id'],
                ':material_id' => $data['material_id'],
                ':user_id'     => $data['user_id'],
                ':description' => $data['description'] ?? null
            ]);

            $newTestId = $this->conn->lastInsertId();

            // 2. Insertion des paramètres
            $queryParam = "
                INSERT INTO parameters
                (test_id, speed, power, frequency, pulse, z_offset, nb_passes, sweep, hatch, row_interval, wobble)
                VALUES
                (:test_id, :speed, :power, :frequency, :pulse, :z_offset, :nb_passes, :sweep, :hatch, :row_interval, :wobble)
            ";

            $stmtParam = $this->conn->prepare($queryParam);
            $stmtParam->execute([
                ':test_id'      => $newTestId,
                ':speed'        => $data['speed'],
                ':power'        => $data['power'],
                ':frequency'    => $data['frequency'],
                ':pulse'        => $data['pulse'] ?? null,
                ':z_offset'     => $data['z_offset'] ?? 0,
                ':nb_passes'    => $data['nb_passes'],
                ':sweep'        => $data['sweep'],
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

    /* =========================
       ===== UPDATE (PUT) ======
       ========================= */

    /**
     * Mettre à jour un test et ses paramètres
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateTest(int $id, array $data): bool
    {
        try {
            $this->conn->beginTransaction();

            // Update table tests
            $queryTest = "
                UPDATE " . $this->table_name . "
                SET title = :title,
                    image = :image,
                    machine_id = :machine_id,
                    material_id = :material_id,
                    description = :description
                WHERE test_id = :id
            ";

            $stmtTest = $this->conn->prepare($queryTest);
            $stmtTest->execute([
                ':title'       => $data['title'],
                ':image'       => $data['image'],
                ':machine_id'  => $data['machine_id'],
                ':material_id' => $data['material_id'],
                ':description' => $data['description'] ?? null,
                ':id'          => $id
            ]);

            // Update table parameters
            $queryParam = "
                UPDATE parameters
                SET speed = :speed,
                    power = :power,
                    frequency = :frequency,
                    pulse = :pulse,
                    z_offset = :z_offset,
                    nb_passes = :nb_passes,
                    sweep = :sweep,
                    hatch = :hatch,
                    row_interval = :row_interval,
                    wobble = :wobble
                WHERE test_id = :id
            ";

            $stmtParam = $this->conn->prepare($queryParam);
            $stmtParam->execute([
                ':speed'        => $data['speed'],
                ':power'        => $data['power'],
                ':frequency'    => $data['frequency'],
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

    /* =========================
       ===== DELETE (DELETE) ====
       ========================= */

    /**
     * Supprimer un test
     *
     * @param int $id
     * @return bool
     */
    public function deleteTest(int $id): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE test_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

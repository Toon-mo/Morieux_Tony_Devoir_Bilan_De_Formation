<?php

namespace Models;

use PDO;

/**
 * UserModel
 *
 * Rôle :
 * - Gérer toutes les opérations CRUD sur la table `users`
 * - Servir d'intermédiaire entre les endpoints API et la base de données
 *
 * Utilisation avec Postman :
 * - Postman appelle des endpoints (ex: GET /api/users/1, POST /api/users)
 * - Les contrôleurs utilisent cette classe pour exécuter les requêtes SQL
 * - Les résultats sont ensuite retournés à Postman au format JSON
 */
class UserModel
{
    /**
     * Connexion PDO (injectée depuis la classe Database)
     * @var PDO
     */
    private $conn;

    /**
     * Nom de la table en base
     * @var string
     */
    private $table_name = "users";

    /**
     * Constructeur
     *
     * @param PDO $db Connexion PDO fournie par Database::getConnection()
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /* =========================
       ====== READ (GET) =======
       ========================= */

    /**
     * Récupérer un utilisateur par son ID
     *
     * @param int $id
     * @return array|false
     */
    public function getUserById(int $id)
    {
        $query = "SELECT user_id, username, email
                  FROM " . $this->table_name . "
                  WHERE user_id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer un utilisateur par son email
     *
     * @param string $email
     * @return array|false
     */
    public function getUserByEmail(string $email)
    {
        $query = "SELECT *
                  FROM " . $this->table_name . "
                  WHERE email = :email
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       ===== CREATE (POST) =====
       ========================= */

    /**
     * Créer un nouvel utilisateur
     *
     * @param array $data Données envoyées depuis Postman
     * @return int|false ID du nouvel utilisateur ou false si échec
     */
    public function createUser(array $data)
    {
        $query = "INSERT INTO " . $this->table_name . " (username, email, password_hash)
                  VALUES (:username, :email, :password_hash)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $data['username'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);

        $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt->bindParam(':password_hash', $hashed_password, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return (int)$this->conn->lastInsertId();
        }

        return false;
    }

    /* =========================
       ===== UPDATE (PUT) ======
       ========================= */

    /**
     * Mettre à jour un utilisateur
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateUser(int $id, array $data): bool
    {
        $query = "UPDATE " . $this->table_name . "
                  SET username = :username, email = :email
                  WHERE user_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $data['username'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /* =========================
       ===== DELETE (DELETE) ====
       ========================= */

    /**
     * Supprimer un utilisateur
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

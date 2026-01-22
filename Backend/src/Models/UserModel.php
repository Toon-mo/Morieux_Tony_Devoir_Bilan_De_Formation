<?php

namespace Models;

use PDO;

/**
 * UserModel
 *
 * Rôle :
 * - Gérer toutes les opérations CRUD sur la table `users`
 * - Servir d’intermédiaire entre les endpoints API et la base de données
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
     */
    private $conn;

    /**
     * Nom de la table en base
     */
    private $table_name = "users";

    /**
     * Constructeur
     *
     * @param PDO $db Connexion PDO fournie par Database::getConnection()
     *
     * Fonctionnement avec Postman :
     * - Lorsqu’une requête HTTP arrive depuis Postman
     * - Le contrôleur crée une instance de UserModel
     * - La connexion à la base est transmise ici
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
     * Test Postman :
     * - Méthode : GET
     * - URL : /api/users/{id}
     *
     * @param int $id
     * @return array|false
     */
    public function getUserById($id)
    {
        // Requête SQL sécurisée avec paramètre nommé
        $query = "SELECT user_id, username, email
                  FROM " . $this->table_name . "
                  WHERE user_id = :id
                  LIMIT 0,1"; // On limite à un seul résultat

        // Préparation de la requête (protection contre les injections SQL)
        $stmt = $this->conn->prepare($query);

        // Liaison du paramètre :id avec typage entier
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Exécution de la requête
        $stmt->execute();

        // Retourne l’utilisateur sous forme de tableau associatif
        // (sera encodé en JSON pour Postman)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer un utilisateur par son email
     *
     * Utilisation typique :
     * - Connexion utilisateur (login)
     * - Vérification d’un compte existant
     *
     * Test Postman :
     * - Méthode : POST
     * - Body JSON : { "email": "test@mail.com" }
     *
     * @param string $email
     * @return array|false
     */
    public function getUserByEmail($email)
    {
        $query = "SELECT *
                  FROM " . $this->table_name . "
                  WHERE email = :email
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        // Liaison du paramètre email
        $stmt->bindParam(':email', $email);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       ===== CREATE (POST) =====
       ========================= */

    /**
     * Créer un nouvel utilisateur
     *
     * Test Postman :
     * - Méthode : POST
     * - URL : /api/users
     * - Body JSON :
     *   {
     *     "username": "Tony",
     *     "email": "tony@mail.com",
     *     "password": "secret"
     *   }
     *
     * @param array $data Données envoyées depuis Postman
     * @return int|false
     */
    public function createUser($data)
    {
        $query = "INSERT INTO " . $this->table_name . " (username, email, password_hash)
                  VALUES (:username, :email, :password_hash)";

        $stmt = $this->conn->prepare($query);

        // Liaison des données envoyées par Postman
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);

        // Hashage du mot de passe avant stockage en base (sécurité)
        $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt->bindParam(':password_hash', $hashed_password);

        // Exécution de l'insertion
        if ($stmt->execute()) {
            // Retourne l’ID du nouvel utilisateur
            // (utile pour la réponse JSON dans Postman)
            return $this->conn->lastInsertId();
        }

        return false;
    }

    /* =========================
       ===== UPDATE (PUT) ======
       ========================= */

    /**
     * Mettre à jour un utilisateur
     *
     * Test Postman :
     * - Méthode : PUT
     * - URL : /api/users/{id}
     * - Body JSON :
     *   {
     *     "username": "NouveauNom",
     *     "email": "nouveau@mail.com"
     *   }
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateUser($id, $data)
    {
        $query = "UPDATE " . $this->table_name . "
                  SET username = :username, email = :email
                  WHERE user_id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Retourne true si la mise à jour a réussi
        return $stmt->execute();
    }

    /* =========================
       ===== DELETE (DELETE) ====
       ========================= */

    /**
     * Supprimer un utilisateur
     *
     * Test Postman :
     * - Méthode : DELETE
     * - URL : /api/users/{id}
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Retourne true si la suppression a réussi
        return $stmt->execute();
    }
}

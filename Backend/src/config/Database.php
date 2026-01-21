<?php

namespace Config;

use PDO;
use PDOException;

/**
 * Classe Database
 *
 * Rôle :
 * - Gérer la connexion à la base de données MySQL
 * - Fournir un objet PDO prêt à l'emploi pour les controllers et models
 *
 * Architecture :
 * Postman / Front → Controller → Model → Database (PDO) → MySQL
 *
 * Bonnes pratiques :
 * - Encodage UTF-8 pour les accents
 * - Gestion des erreurs avec exceptions
 * - Objet PDO réutilisable
 */
class Database
{
    /**
     * Hôte de la base de données
     * @var string
     */
    private $host = "localhost";

    /**
     * Nom de la base de données
     * @var string
     */
    private $db_name = "forum_gravure_laser";

    /**
     * Nom d'utilisateur MySQL
     * @var string
     */
    private $username = "root";

    /**
     * Mot de passe MySQL
     * @var string
     */
    private $password = "";

    /**
     * Objet PDO de connexion
     * @var PDO|null
     */
    public $conn;

    /**
     * Récupérer la connexion PDO
     *
     * Usage :
     * $database = new Database();
     * $db = $database->getConnection();
     *
     * @return PDO|null
     */
    public function getConnection()
    {
        $this->conn = null;

        try {
            // Création de la connexion PDO
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );

            // On force l'encodage en UTF-8 pour éviter les problèmes d'accents
            $this->conn->exec("set names utf8");

            // Mode d'erreur : Exceptions PDO
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // Gestion des erreurs de connexion
            echo json_encode([
                "success" => false,
                "message" => "Erreur de connexion à la base de données : " . $exception->getMessage()
            ]);
        }

        return $this->conn;
    }
}

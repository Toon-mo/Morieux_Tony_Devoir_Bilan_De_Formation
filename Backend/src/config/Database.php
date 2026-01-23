<?php

namespace Config;

// Import des classes globales PHP
use PDO;
use PDOException;

// Chargement de l'autoloader Composer (Dotenv)
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Database
 *
 * Rôle :
 * - Centraliser la connexion à la base de données
 * - Charger les variables d’environnement via Dotenv
 * - Retourner une instance PDO sécurisée
 *
 * Architecture :
 * Controllers / Models → Database → PDO → MySQL
 */
class Database
{
    /**
     * Paramètres de connexion
     */
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;

    /**
     * Instance PDO
     */
    public ?PDO $conn = null;

    /**
     * Constructeur
     *
     * Fonctionnement :
     * - Charge le fichier .env à la racine du projet
     * - Initialise les paramètres de connexion
     * - Définit des valeurs par défaut si les variables ne sont pas présentes
     */
    public function __construct()
    {
        // Chargement des variables d'environnement (.env)
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        // Initialisation des paramètres avec fallback
        // L’opérateur ?? permet d’éviter une erreur si la variable n’existe pas
        $this->host     = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name  = $_ENV['DB_NAME'] ?? '';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
    }

    /**
     * Obtenir la connexion à la base de données
     *
     * @return PDO|null
     *
     * Comportement :
     * - Crée une connexion PDO
     * - Force l'encodage UTF-8
     * - Active les exceptions PDO
     * - Retourne null en cas d’échec
     */
    public function getConnection(): ?PDO
    {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->username,
                $this->password
            );

            // Encodage UTF-8 pour éviter les problèmes d'accents
            $this->conn->exec("SET NAMES utf8");

            // Mode erreur : exceptions (indispensable pour debug et API)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->conn;
        } catch (PDOException $exception) {
            // Message clair pour le debug (à désactiver en production)
            echo "Erreur de connexion à la base de données : " . $exception->getMessage();
            return null;
        }
    }
}

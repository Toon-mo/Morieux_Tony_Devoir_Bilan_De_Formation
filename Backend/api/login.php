<?php

/**
 * Point d'entrée API – CONNEXION (LOGIN)
 *
 * Rôle :
 * - Recevoir les requêtes HTTP d'authentification
 * - Initialiser l'autoload, la base de données et le contrôleur
 * - Rediriger la requête vers la méthode login
 *
 * Architecture :
 * Frontend → /api/login.php → UserController → UserModel → Database
 */

// Chargement des dépendances
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/CORS.php';

use Config\Database;
use Controllers\UserController;

// 🔒 Logging conditionnel (développement uniquement)
if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
    error_log("[" . date("Y-m-d H:i:s") . "] LOGIN - Requête reçue : " . $_SERVER['REQUEST_METHOD']);
}

try {
    // Initialisation de la base de données
    $database = new Database();
    $db = $database->getConnection();

    // Instanciation du contrôleur utilisateur
    $controller = new UserController($db);

    // Traitement de la requête
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->login();
    } else {
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Méthode non autorisée"
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur serveur",
        "error" => ($_ENV['APP_ENV'] === 'development') ? $e->getMessage() : null
    ]);
}

<?php

/**
 * Point d’entrée API – LOGIN
 *
 * Rôle :
 * - Recevoir les requêtes HTTP envoyées par Postman
 * - Initialiser l’environnement (autoload, DB, contrôleur)
 * - Rediriger la requête vers la bonne méthode du contrôleur
 *
 * Exemple d’architecture :
 * Postman → /api/login.php → UserController → UserModel → Database
 */

// ==============================
// Chargement de l'autoloader Composer
// ==============================

// Permet le chargement automatique des classes (Database, UserController, UserModel, etc.)
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Controllers\UserController;

// ==============================
// Initialisation de la base de données
// ==============================

// Création de la connexion PDO via la classe Database
// Cette connexion sera utilisée pour toutes les requêtes SQL déclenchées par Postman
$db = (new Database())->getConnection();

// Instanciation du contrôleur utilisateur
// La connexion PDO est injectée dans le contrôleur
$controller = new UserController($db);

// ==============================
// Gestion des requêtes HTTP
// ==============================

/**
 * Test Postman :
 * - Méthode : POST
 * - URL : /api/login.php
 * - Headers :
 *   Content-Type: application/json
 * - Body (JSON) :
 *   {
 *     "email": "tony@mail.com",
 *     "password": "secret"
 *   }
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Appel de la méthode login du contrôleur
    // - Récupère les données envoyées par Postman
    // - Vérifie l’email et le mot de passe
    // - Retourne une réponse JSON
    $controller->login();
} else {

    // Si la méthode HTTP n’est pas autorisée
    http_response_code(405);
    echo json_encode([
        "message" => "Méthode non autorisée"
    ]);
}

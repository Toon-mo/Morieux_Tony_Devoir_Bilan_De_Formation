<?php

/**
 * Point d’entrée API – INSCRIPTION (REGISTER)
 *
 * Rôle :
 * - Recevoir les requêtes HTTP envoyées par Postman
 * - Initialiser l’autoload, la base de données et le contrôleur
 * - Rediriger la requête vers la méthode d’inscription
 *
 * Architecture :
 * Postman → /api/register.php → UserController → UserModel → Database
 */

// ==============================
// Chargement de l'autoloader Composer et CORS
// ==============================

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/CORS.php';

use Config\Database;
use Controllers\UserController;

// ==============================
// Gestion des requêtes OPTIONS (prévols CORS)
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ==============================
// Initialisation de la base de données
// ==============================
try {
    $db = (new Database())->getConnection();
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "message" => "Erreur de connexion à la base de données",
        "error" => $e->getMessage()
    ]);
    exit;
}

// ==============================
// Instanciation du contrôleur utilisateur
// ==============================
$controller = new UserController($db);

// ==============================
// Gestion des requêtes HTTP
// ==============================
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Appel de la méthode register du contrôleur
    // Cette méthode doit gérer :
    // - la récupération des données JSON
    // - la validation des champs
    // - la création de l'utilisateur
    // - le retour JSON avec le code HTTP approprié
    $controller->register();
} else {
    // Méthode non autorisée
    http_response_code(405);
    echo json_encode([
        "message" => "Méthode non autorisée"
    ]);
}

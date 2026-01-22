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
// Chargement de l'autoloader Composer
// ==============================

// Permet le chargement automatique des classes via Composer
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Controllers\UserController;

// ==============================
// Initialisation de la base de données
// ==============================

// Création de la connexion PDO
// Cette connexion sera utilisée pour insérer l’utilisateur en base
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
 * - URL : /api/register.php
 * - Headers :
 *   Content-Type: application/json
 * - Body (JSON) :
 *   {
 *     "username": "Tony",
 *     "email": "tony@mail.com",
 *     "password": "secret"
 *   }
 *
 * Réponses possibles :
 * - 201 : utilisateur créé
 * - 400 : données manquantes
 * - 500 : erreur serveur
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Appel de la méthode register du contrôleur
    // - Récupère les données envoyées par Postman
    // - Vérifie les champs requis
    // - Crée l’utilisateur en base
    // - Retourne une réponse JSON
    $controller->register();
} else {

    // Si la méthode HTTP n’est pas autorisée
    http_response_code(405);
    echo json_encode([
        "message" => "Méthode non autorisée"
    ]);
}

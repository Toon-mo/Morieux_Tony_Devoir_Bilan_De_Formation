<?php
// =========================
// Chargement de l'autoloader Composer
// =========================
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Controllers\UserController;

/**
 * Front Controller pour l'API Users
 *
 * Rôle :
 * - Initialiser la connexion à la base de données
 * - Instancier le UserController
 * - Router les requêtes HTTP vers les bonnes méthodes
 * - Retourner des réponses JSON avec les codes HTTP appropriés
 *
 * Architecture :
 * Postman → this file (api/users/index.php) → UserController → UserModel → Base de données
 */

/* =========================
   ======== CORS ===========
   ========================= */

// Autorise toutes les origines (Postman, navigateur, front-end)
header("Access-Control-Allow-Origin: *");

// Méthodes HTTP autorisées
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// En-têtes autorisés (JSON, Auth, AJAX)
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Réponse en JSON
header("Content-Type: application/json; charset=UTF-8");

// Réponse automatique aux requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/* =========================
   ===== Initialisation ====
   ========================= */

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Instanciation du controller UserController
$controller = new UserController($db);

/* =========================
   ===== Routing HTTP ======
   ========================= */

switch ($_SERVER['REQUEST_METHOD']) {

    case 'GET':
        /**
         * [GET] Récupérer des utilisateurs
         *
         * Test Postman :
         * - Méthode : GET
         * - URL : /api/users
         *   → retourne tous les utilisateurs
         *
         * - Méthode : GET
         * - URL : /api/users?id=1
         *   → retourne un utilisateur spécifique
         *
         * Réponses :
         * - 200 : succès
         * - 404 : utilisateur non trouvé
         */
        if (isset($_GET['id'])) {
            $controller->show($_GET['id']);
        } else {
            $controller->index();
        }
        break;

    case 'POST':
        /**
         * [POST] Création d’un nouvel utilisateur (inscription)
         *
         * Test Postman :
         * - Méthode : POST
         * - URL : /api/users
         * - Body JSON :
         * {
         *   "username": "Tony",
         *   "email": "tony@mail.com",
         *   "password": "password123"
         * }
         *
         * Réponses :
         * - 201 : utilisateur créé
         * - 400 : données invalides
         * - 500 : erreur serveur
         */
        $controller->register();
        break;

    /*
    case 'PUT':
        // [PUT] Mise à jour d’un utilisateur
        break;

    case 'DELETE':
        // [DELETE] Suppression d’un utilisateur
        break;
    */

    default:
        // Méthode HTTP non autorisée
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Méthode non autorisée"
        ]);
        break;
}

/* =========================
   ===== Fin du script =====
   ========================= */

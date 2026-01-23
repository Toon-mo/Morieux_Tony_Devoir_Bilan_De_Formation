<?php
// =========================
// Chargement de l'autoloader Composer
// =========================
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Controllers\CommentController;

/**
 * Front Controller pour l'API Commentaires (ajout)
 *
 * Rôle :
 * - Initialiser la connexion à la base de données
 * - Instancier le CommentController
 * - Router les requêtes HTTP vers la méthode addComment()
 * - Retourner la réponse JSON avec le code HTTP approprié
 *
 * Architecture :
 * Postman → this file (api/comments/add.php) → CommentController → CommentModel → Base de données
 */

/* =========================
   ======= CORS ============
   ========================= */
header("Access-Control-Allow-Origin: *"); // Autorise toutes les origines
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Méthodes autorisées
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Réponse automatique aux requêtes OPTIONS (pré-vol)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/* =========================
   ====== Initialisation ====
   ========================= */
// 1. Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// 2. Instanciation du controller CommentController
$controller = new CommentController($db);

/* =========================
   ===== Gestion de la requête ====
   ========================= */

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        /**
         * [GET] Récupérer tous les commentaires
         * TEST POSTMAN :
         *  - Méthode : GET
         * - URL : /api/comments/add.php
         */
        $controller->getCommentsByTest();
        break;

    case 'POST':
        /**
         * [POST] Ajouter un commentaire
         *
         * Test Postman :
         * - Méthode : POST
         * - URL : /api/comments/.php
         * - Body JSON :
         * {
         *   "user_id": 1,
         *   "test_id": 2,
         *   "content": "Ceci est un commentaire"
         * }
         *
         * Réponses :
         * - 201 : commentaire ajouté avec succès
         * - 400 : données incomplètes
         * - 500 : échec serveur
         */
        $controller->addComment();
        break;

    case 'DELETE':
        /**
         * 
         * [DELETE] Supprimer un commentaire
         * *
         * Test Postman :
         * - Méthode : DELETE
         * - URL : /api/comments.php?id=12
         *
         * Réponses :
         * - 201 : commentaire ajouté avec succès
         * - 400 : données incomplètes
         * - 500 : échec serveur
         */
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id) {
            $controller->deleteComment($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID du commentaire manquant"]);
        }
        break;




    default:
        // Méthode HTTP non autorisée
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Méthode non autorisée"
        ]);
        break;
}

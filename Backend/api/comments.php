<?php
// =========================
// Chargement de l'autoloader Composer
// =========================
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/CORS.php';

use Config\Database;
use Controllers\CommentController;

/**
 * Front Controller pour l'API Commentaires
 *
 * Rôle :
 * - Initialiser la connexion à la base de données
 * - Instancier le CommentController
 * - Router les requêtes HTTP vers la bonne méthode du controller
 * - Retourner la réponse JSON avec le code HTTP approprié
 *
 * Architecture :
 * Postman → this file (api/comments/add.php) → CommentController → CommentModel → Base de données
 */

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
        // [GET] Récupérer les commentaires d'un test via ?test_id=1
        $controller->getCommentsByTest();
        break;

    case 'POST':
        // [POST] Ajouter un commentaire
        $controller->addComment();
        break;

    case 'DELETE':
        // [DELETE] Supprimer un commentaire
        // L'ID sera récupéré directement dans le controller
        $controller->deleteComment();
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

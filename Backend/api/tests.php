<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Controllers\TestController;

/**
 * Front Controller pour l'API Tests
 *
 * Rôle :
 * - Initialiser la connexion à la base de données
 * - Instancier le TestController
 * - Router les requêtes HTTP vers les bonnes méthodes
 * - Gérer les réponses JSON et les codes HTTP
 *
 * Architecture :
 * Postman → this file (index.php ou api/tests.php) → TestController → TestModel → Base de données
 */

/* =========================
   ======== CORS ===========
   ========================= */

// Autorise toutes les origines
header("Access-Control-Allow-Origin: *");

// Autorise les méthodes HTTP GET, POST, PUT, DELETE, OPTIONS
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Autorise certains en-têtes
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Réponse JSON
header("Content-Type: application/json; charset=UTF-8");

// Réponse automatique aux requêtes OPTIONS (pré-vol)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/* =========================
   ===== Initialisation ====
   ========================= */

// Connexion à la base de données via la classe Database
$db = (new Database())->getConnection();

// Instanciation du controller TestController
$controller = new TestController($db);

/* =========================
   ===== Routing HTTP ======
   ========================= */

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $controller->show($_GET['id']);
        } else {
            $controller->index();
        }
        break;

    case 'POST':
        /**
         * [POST] Créer un nouveau test
         *
         * Test Postman :
         * - Méthode : POST
         * - URL : /api/tests
         * - Body JSON : { "name": "Test 1", "description": "Description du test", ... }
         *
         * Réponse :
         * - 201 : test créé
         * - 400 : données invalides
         */
        $controller->createTest();
        break;

    case 'PUT':
        /**
         * [PUT] Mettre à jour un test
         *
         * Test Postman :
         * - Méthode : PUT
         * - URL : /api/tests/{id}
         * - Body JSON : { "name": "Nouveau nom", "description": "Nouvelle description", ... }
         *
         * Réponse :
         * - 200 : test mis à jour
         * - 400 : données invalides
         * - 404 : test non trouvé
         */
        // Pour PUT, on suppose que l'ID est passé via l'URL (ex: /api/tests.php?id=1)
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->updateTest($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID du test manquant"]);
        }
        break;

    case 'DELETE':
        /**
         * [DELETE] Supprimer un test
         *
         * Test Postman :
         * - Méthode : DELETE
         * - URL : /api/tests/{id}
         *
         * Réponse :
         * - 200 : test supprimé
         * - 404 : test non trouvé
         */
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->deleteTest($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID du test manquant"]);
        }
        break;

    default:
        // Méthode HTTP non autorisée
        http_response_code(405);
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}

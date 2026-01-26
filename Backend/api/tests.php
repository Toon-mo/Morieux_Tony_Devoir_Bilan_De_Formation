<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;
use Controllers\TestController;

/* =========================
   ======== CORS =========== 
   ========================= */

// Autorise toutes les origines
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Réponse automatique aux requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/* =========================
   ===== Initialisation ==== 
   ========================= */

$db = (new Database())->getConnection();
$controller = new TestController($db);

/* =========================
   ===== Routing HTTP ====== 
   ========================= */

switch ($_SERVER['REQUEST_METHOD']) {

    case 'GET':
        // GET /api/tests.php?id=3 → test par ID
        // GET /api/tests.php      → tous les tests
        if (isset($_GET['id'])) {
            $controller->show((int)$_GET['id']);
        } else {
            $controller->index();
        }
        break;

    case 'POST':
        // POST /api/tests.php (form-data ou JSON avec image optionnelle)
        $controller->createTest();
        break;

    case 'DELETE':
        // DELETE /api/tests.php?id=3
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->deleteTest((int)$id);
        } else {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "ID du test manquant"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
        break;
}

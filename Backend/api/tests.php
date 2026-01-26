<?php

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

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/CORS.php';

use Config\Database;
use Controllers\TestController;

// Réponse immédiate pour les requêtes OPTIONS (prévols CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ==============================
// Initialisation de la base et du controller
// ==============================
try {
    $db = (new Database())->getConnection();
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "message" => "Erreur de connexion à la base de données",
        "error"   => $e->getMessage()
    ]);
    exit;
}

$controller = new TestController($db);

// ==============================
// Routing HTTP
// ==============================
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->show($id);
        } else {
            $controller->index();
        }
        break;

    case 'POST':
        $controller->createTest();
        break;

    case 'PUT':
    case 'PATCH':
        // Pour PUT/PATCH, l'ID doit être dans l'URL (ex: tests.php?id=1)
        $id = $_GET['id'] ?? null;
        if ($id) {
            // 1. Récupérer le corps JSON
            $putData = json_decode(file_get_contents("php://input"), true);

            // 2. CORRECTION : Envoyer les données au contrôleur pour traitement
            $controller->updateTest($id, $putData);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID du test manquant"]);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->deleteTest($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID du test manquant"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}

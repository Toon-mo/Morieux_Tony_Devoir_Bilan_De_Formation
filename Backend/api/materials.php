<?php
// =========================
// Chargement de l'autoloader Composer
// =========================

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/CORS.php';

use Config\Database;
use Controllers\MaterialController;

/**
 * Front Controller pour l'API Materials
 *
 * Rôle :
 * - Initialiser la connexion à la base de données
 * - Instancier le MaterialController
 * - Router les requêtes HTTP vers les bonnes méthodes
 * - Retourner des réponses JSON avec les codes HTTP appropriés
 *
 * Architecture :
 * Frontend Vue → this file (api/materials.php) → MaterialController → MaterialModel → Base de données
 */

/* =========================
   ===== Initialisation ====
   ========================= */

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Instanciation du controller MaterialController
$controller = new MaterialController($db);

/* =========================
   ===== Routing HTTP ======
   ========================= */

switch ($_SERVER['REQUEST_METHOD']) {

    case 'GET':
        /**
         * [GET] Récupérer des matériaux
         *
         * Test :
         * - Méthode : GET
         * - URL : /api/materials.php
         *   → retourne tous les matériaux
         *
         * - Méthode : GET
         * - URL : /api/materials.php?id=1
         *   → retourne un matériau spécifique
         *
         * - Méthode : GET
         * - URL : /api/materials.php?machine_id=1
         *   → retourne les matériaux compatibles avec la machine
         *
         * Réponses :
         * - 200 : succès
         * - 404 : matériau non trouvé
         */
        if (isset($_GET['id'])) {
            $controller->show($_GET['id']);
        } elseif (isset($_GET['machine_id'])) {
            $controller->getByMachine($_GET['machine_id']);
        } else {
            $controller->index();
        }
        break;

    case 'POST':
        /**
         * [POST] Création d'un nouveau matériau
         *
         * Test :
         * - Méthode : POST
         * - URL : /api/materials.php
         * - Body JSON :
         * {
         *   "name": "Inox 304",
         *   "thickness": 1.5,
         *   "machine_id": 1
         * }
         *
         * Réponses :
         * - 201 : matériau créé
         * - 400 : données invalides
         * - 500 : erreur serveur
         */
        $controller->createMaterial();
        break;

    case 'PUT':
        /**
         * [PUT] Mise à jour d'un matériau
         * 
         * Test :
         * - Méthode : PUT
         * - URL : /api/materials.php?id=1
         * - Body JSON :
         * {
         *   "name": "Nouveau nom",
         *   "thickness": 2.0
         * }
         * 
         * Réponses :
         * - 200 : matériau mis à jour
         * - 400 : données invalides
         * - 404 : matériau non trouvé
         * - 500 : erreur serveur
         */
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->updateMaterial($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID du matériau manquant"]);
        }
        break;

    case 'DELETE':
        /**
         * [DELETE] Supprimer un matériau
         *
         * Test :
         * - Méthode : DELETE
         * - URL : /api/materials.php?id=1
         *
         * Réponse :
         * - 200 : matériau supprimé
         * - 404 : matériau non trouvé
         */
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->deleteMaterial($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID du matériau manquant"]);
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

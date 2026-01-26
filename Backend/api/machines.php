<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// =========================
// Chargement de l'autoloader Composer
// =========================
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/CORS.php';

use Config\Database;
use Controllers\MachineController;

/**
 * Front Controller pour l'API Machines
 *
 * Rôle :
 * - Initialiser la connexion à la base de données
 * - Instancier le MachineController
 * - Router les requêtes HTTP vers les bonnes méthodes
 * - Retourner des réponses JSON avec les codes HTTP appropriés
 *
 * Architecture :
 * Frontend Vue → this file (api/machines.php) → MachineController → MachineModel → Base de données
 */

/* =========================
   ===== Initialisation ====
   ========================= */

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Instanciation du controller MachineController
$controller = new MachineController($db);

/* =========================
   ===== Routing HTTP ======
   ========================= */

switch ($_SERVER['REQUEST_METHOD']) {

    case 'GET':
        /**
         * [GET] Récupérer des machines
         *
         * Test :
         * - Méthode : GET
         * - URL : /api/machines.php
         *   → retourne toutes les machines
         *
         * - Méthode : GET
         * - URL : /api/machines.php?id=1
         *   → retourne une machine spécifique
         *
         * Réponses :
         * - 200 : succès
         * - 404 : machine non trouvée
         */
        if (isset($_GET['id'])) {
            $controller->show($_GET['id']);
        } else {
            $controller->index();
        }
        break;

    case 'POST':
        /**
         * [POST] Création d'une nouvelle machine
         *
         * Test :
         * - Méthode : POST
         * - URL : /api/machines.php
         * - Body JSON :
         * {
         *   "name": "Laser Fiber 60W",
         *   "power": 60,
         *   "wavelength": 1064
         * }
         *
         * Réponses :
         * - 201 : machine créée
         * - 400 : données invalides
         * - 500 : erreur serveur
         */
        $controller->createMachine();
        break;

    case 'PUT':
        /**
         * [PUT] Mise à jour d'une machine
         * 
         * Test :
         * - Méthode : PUT
         * - URL : /api/machines.php?id=1
         * - Body JSON :
         * {
         *   "name": "Nouveau nom",
         *   "power": 80
         * }
         * 
         * Réponses :
         * - 200 : machine mise à jour
         * - 400 : données invalides
         * - 404 : machine non trouvée
         * - 500 : erreur serveur
         */
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->updateMachine($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID de la machine manquant"]);
        }
        break;

    case 'DELETE':
        /**
         * [DELETE] Supprimer une machine
         *
         * Test :
         * - Méthode : DELETE
         * - URL : /api/machines.php?id=1
         *
         * Réponse :
         * - 200 : machine supprimée
         * - 404 : machine non trouvée
         */
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controller->deleteMachine($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID de la machine manquant"]);
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

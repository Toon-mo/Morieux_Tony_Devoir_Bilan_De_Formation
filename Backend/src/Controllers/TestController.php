<?php

namespace Controllers;

use Models\TestModel;

/**
 * TestController
 *
 * Rôle :
 * - Gérer les requêtes HTTP liées aux tests de gravure
 * - Recevoir les appels depuis Postman
 * - Appeler le modèle TestModel
 * - Retourner des réponses JSON avec les bons codes HTTP
 *
 * Architecture :
 * Postman → Routes → TestController → TestModel → Base de données
 */
class TestController
{
    /**
     * Instance du modèle Test
     */
    private $model;

    /**
     * Constructeur
     *
     * @param PDO $db Connexion PDO injectée
     *
     * Le constructeur :
     * - Initialise le modèle
     * - Configure les headers CORS
     * - Gère les requêtes OPTIONS (pré-vol)
     */
    public function __construct($db)
    {
        $this->model = new TestModel($db);

        /* =========================
           ====== HEADERS CORS =====
           ========================= */

        // Autorise l’API à être appelée depuis Postman, navigateur ou front-end
        header("Access-Control-Allow-Origin: *");

        // Autorise certains en-têtes HTTP
        header("Access-Control-Allow-Headers: access");

        // Méthodes HTTP autorisées pour les endpoints tests
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

        // Format de réponse JSON
        header("Content-Type: application/json; charset=UTF-8");

        // En-têtes nécessaires pour le JSON et l’authentification
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        // Réponse automatique aux requêtes OPTIONS (CORS preflight)
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    /* =========================
       ===== READ (GET) ========
       ========================= */

    /**
     * [GET] Liste de tous les tests (vue Home)
     *
     * Test Postman :
     * - Méthode : GET
     * - URL : /api/tests
     *
     * Réponse :
     * - 200 : liste des tests
     */
    public function index()
    {
        $tests = $this->model->getAllTests();

        // Retourne la liste des tests en JSON
        echo json_encode($tests);
    }

    /**
     * [GET] Détails d’un test spécifique
     *
     * Test Postman :
     * - Méthode : GET
     * - URL : /api/tests/{id}
     *
     * @param int $id
     */
    public function show($id)
    {
        $test = $this->model->getTestDetails($id);

        if ($test) {
            echo json_encode($test);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Test non trouvé"]);
        }
    }

    /* =========================
       ===== CREATE (POST) =====
       ========================= */

    /**
     * [POST] Création d’un nouveau test
     *
     * Test Postman :
     * - Méthode : POST
     * - URL : /api/tests
     * - Body : JSON (données du test + paramètres)
     */
    public function createTest()
    {
        // Lecture du body JSON envoyé par Postman
        $data = json_decode(file_get_contents("php://input"), true);

        if ($this->model->createTest($data)) {
            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "Test créé avec succès"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "La création du test a échoué"
            ]);
        }
    }

    /* =========================
       ===== UPDATE (PUT) ======
       ========================= */

    /**
     * [PUT] Mise à jour d’un test existant
     *
     * Test Postman :
     * - Méthode : PUT
     * - URL : /api/tests/{id}
     * - Body : JSON
     *
     * @param int $id
     */
    public function updateTest($id)
    {
        // Lecture des données JSON envoyées
        $data = json_decode(file_get_contents("php://input"), true);

        if ($this->model->updateTest($id, $data)) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Test mis à jour avec succès"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "La mise à jour du test a échoué"
            ]);
        }
    }

    /* =========================
       ===== DELETE (DELETE) ====
       ========================= */

    /**
     * [DELETE] Suppression d’un test
     *
     * Test Postman :
     * - Méthode : DELETE
     * - URL : /api/tests/{id}
     *
     * @param int $id
     */
    public function deleteTest($id)
    {
        if ($this->model->deleteTest($id)) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Test supprimé avec succès"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "La suppression a échoué"
            ]);
        }
    }
}

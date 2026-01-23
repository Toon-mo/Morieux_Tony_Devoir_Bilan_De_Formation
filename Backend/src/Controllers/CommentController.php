<?php

namespace Controllers;

use Models\CommentModel;

/**
 * CommentController
 *
 * Rôle :
 * - Gérer les requêtes HTTP liées aux commentaires
 * - Recevoir les appels depuis Postman
 * - Appeler le modèle CommentModel
 * - Retourner des réponses JSON avec les bons codes HTTP
 *
 * Architecture :
 * Postman → Routes → CommentController → CommentModel → Base de données
 */
class CommentController
{
    /**
     * Instance du modèle Comment
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
        $this->model = new CommentModel($db);

        /* =========================
           ====== HEADERS CORS =====
           ========================= */

        // Autorise toutes les origines (Postman, navigateur, front-end)
        header("Access-Control-Allow-Origin: *");

        // Autorise certains en-têtes HTTP
        header("Access-Control-Allow-Headers: access");

        // Méthodes HTTP autorisées pour les endpoints commentaires
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

        // Type de contenu JSON
        header("Content-Type: application/json; charset=UTF-8");

        // En-têtes supplémentaires nécessaires pour le JSON et l’authentification
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        // Réponse automatique aux requêtes OPTIONS (CORS preflight)
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    /* =========================
       ===== CREATE (POST) =====
       ========================= */

    /**
     * [POST] Ajouter un nouveau commentaire
     *
     * Test Postman :
     * - Méthode : POST
     * - URL : /api/comments
     * - Body JSON :
     * {
     *   "user_id": 1,
     *   "test_id": 2,
     *   "content": "Ceci est un commentaire de test"
     * }
     *
     * Réponses :
     * - 201 : commentaire ajouté avec succès
     * - 400 : données incomplètes
     * - 500 : échec serveur
     */
    public function addComment()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        // Vérification des données
        if (!empty($data['user_id']) && !empty($data['test_id']) && !empty($data['content'])) {
            if ($this->model->createComment($data)) {
                http_response_code(201);
                echo json_encode([
                    "success" => true,
                    "message" => "Commentaire ajouté avec succès"
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => "Échec de l'ajout du commentaire"
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Données incomplètes"
            ]);
        }
    }

    /* =========================
       ===== READ (GET) ========
       ========================= */

    /**
     * [GET] Récupérer les commentaires d’un test
     *
     * Test Postman :
     * - Méthode : GET
     * - URL : /api/comments?test_id=1
     *
     * Réponses :
     * - 200 : liste des commentaires
     * - 400 : test_id manquant
     * - 404 : aucun commentaire trouvé (optionnel selon implémentation)
     */
    public function getCommentsByTest()
    {
        $testId = $_GET['test_id'] ?? '';

        if (!empty($testId)) {
            $comments = $this->model->getCommentsByTestId($testId);

            if (!empty($comments)) {
                http_response_code(200);
                echo json_encode($comments);
            } else {
                http_response_code(404);
                echo json_encode([
                    "message" => "Aucun commentaire trouvé pour ce test"
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "message" => "ID de test manquant"
            ]);
        }
    }
}

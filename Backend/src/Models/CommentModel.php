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

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: access");
        header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        // Réponse automatique aux requêtes OPTIONS
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    /* =========================
       ===== READ (GET) ========
       ========================= */

    /**
     * [GET] Liste des commentaires d’un test
     *
     * Test Postman :
     * - Méthode : GET
     * - URL : /api/comments?test_id=1
     *
     * @param int $test_id
     *
     * Réponses :
     * - 200 : liste des commentaires
     * - 404 : aucun commentaire trouvé
     */
    public function getByTest($test_id)
    {
        $comments = $this->model->getCommentsByTestId($test_id);

        if (!empty($comments)) {
            echo json_encode($comments);
        } else {
            http_response_code(404);
            echo json_encode([
                "message" => "Aucun commentaire trouvé pour ce test"
            ]);
        }
    }

    /* =========================
       ===== CREATE (POST) =====
       ========================= */

    /**
     * [POST] Création d’un commentaire
     *
     * Test Postman :
     * - Méthode : POST
     * - URL : /api/comments
     * - Body : JSON
     *
     * Exemple Body :
     * {
     *   "test_id": 1,
     *   "user_id": 2,
     *   "content": "Très bon test"
     * }
     *
     * Réponses :
     * - 201 : commentaire créé
     * - 400 : données invalides
     * - 500 : erreur serveur
     */
    public function createComment()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        // Vérification minimale des données
        if (
            empty($data['test_id']) ||
            empty($data['user_id']) ||
            empty($data['content'])
        ) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Données manquantes"
            ]);
            return;
        }

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
                "message" => "La création du commentaire a échoué"
            ]);
        }
    }

    /* =========================
       ===== DELETE (DELETE) ====
       ========================= */

    /**
     * [DELETE] Suppression d’un commentaire
     *
     * Test Postman :
     * - Méthode : DELETE
     * - URL : /api/comments/{comment_id}
     *
     * @param int $comment_id
     *
     * Réponses :
     * - 200 : commentaire supprimé
     * - 500 : erreur serveur
     */
    public function deleteComment($comment_id)
    {
        if ($this->model->deleteComment($comment_id)) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Commentaire supprimé avec succès"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "La suppression du commentaire a échoué"
            ]);
        }
    }
}

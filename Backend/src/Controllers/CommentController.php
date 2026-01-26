<?php

namespace Controllers;

require_once __DIR__ . '/../Config/CORS.php';

use Models\CommentModel;
use PDO;

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
    private CommentModel $model;

    /**
     * Constructeur
     *
     * @param PDO $db Connexion PDO injectée
     */
    public function __construct(PDO $db)
    {
        $this->model = new CommentModel($db);
    }

    /* =========================
       ===== CREATE (POST) =====
       ========================= */

    /**
     * [POST] Ajouter un nouveau commentaire
     */
    public function addComment(): void
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

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
     * [GET] Récupérer les commentaires d'un test
     */
    public function getCommentsByTest(): void
    {
        header('Content-Type: application/json');
        $testId = $_GET['test_id'] ?? null;

        if ($testId) {
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

    /* =========================
       ===== DELETE (DELETE) ===
       ========================= */

    /**
     * [DELETE] Supprimer un commentaire
     */
    public function deleteComment(): void
    {
        header('Content-Type: application/json');

        // Récupérer l'id depuis GET ou DELETE
        parse_str(file_get_contents("php://input"), $data);
        $id = $_GET['id'] ?? $data['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "ID de commentaire manquant"
            ]);
            return;
        }

        if ($this->model->deleteComment($id)) {
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

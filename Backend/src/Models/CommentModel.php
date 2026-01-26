<?php

namespace Models;

use PDO;

/**
 * CommentModel
 *
 * Rôle :
 * - Gérer toutes les opérations CRUD liées aux commentaires
 * - Centraliser l'accès à la table `comments`
 * - Fournir les données aux contrôleurs
 *
 * Architecture :
 * Frontend Vue → CommentController → CommentModel → Base de données
 */
class CommentModel
{
    /** Connexion PDO */
    private $conn;

    /** Nom de la table */
    private $table_name = "comments";

    /**
     * Constructeur
     *
     * @param PDO $db Connexion injectée depuis Database
     */
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /* =========================
       ===== READ (GET) ========
       ========================= */

    /**
     * Récupérer les commentaires d'un test spécifique
     *
     * @param int $testId
     * @return array
     */
    public function getCommentsByTestId(int $testId): array
    {
        $query = "
            SELECT 
                c.comment_id,
                c.content,
                c.created_at,
                u.username,
                u.user_id
            FROM {$this->table_name} c
            LEFT JOIN users u ON c.user_id = u.user_id
            WHERE c.test_id = :test_id
            ORDER BY c.created_at DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':test_id', $testId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer un commentaire par son ID
     *
     * @param int $id
     * @return array|false
     */
    public function getCommentById(int $id)
    {
        $query = "
            SELECT 
                c.*,
                u.username
            FROM {$this->table_name} c
            LEFT JOIN users u ON c.user_id = u.user_id
            WHERE c.comment_id = :id
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       ===== CREATE (POST) =====
       ========================= */

    /**
     * Créer un nouveau commentaire
     *
     * @param array $data
     * @return int|false Retourne l'ID du commentaire créé ou false
     */
    public function createComment(array $data)
    {
        $query = "
            INSERT INTO {$this->table_name} (user_id, test_id, content)
            VALUES (:user_id, :test_id, :content)
        ";

        $stmt = $this->conn->prepare($query);

        $success = $stmt->execute([
            ':user_id' => $data['user_id'],
            ':test_id' => $data['test_id'],
            ':content' => $data['content']
        ]);

        return $success ? (int)$this->conn->lastInsertId() : false;
    }

    /* =========================
       ===== UPDATE (PUT) ======
       ========================= */

    /**
     * Mettre à jour un commentaire
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateComment(int $id, array $data): bool
    {
        $query = "
            UPDATE {$this->table_name}
            SET content = :content
            WHERE comment_id = :id
        ";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':content' => $data['content'],
            ':id'      => $id
        ]);
    }

    /* =========================
       ===== DELETE (DELETE) ===
       ========================= */

    /**
     * Supprimer un commentaire
     *
     * @param int $id
     * @return bool
     */
    public function deleteComment(int $id): bool
    {
        $query = "DELETE FROM {$this->table_name} WHERE comment_id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

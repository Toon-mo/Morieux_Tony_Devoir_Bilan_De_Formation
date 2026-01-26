<?php

namespace Models;

use PDO;

class CommentModel
{
    private PDO $conn;
    private string $table_name = "comments";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Crée un commentaire
     */
    public function createComment(array $data): bool
    {
        $query = "INSERT INTO {$this->table_name} (test_id, user_id, content) VALUES (:test_id, :user_id, :content)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':test_id', $data['test_id'], PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':content', $data['content'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Récupère les commentaires avec le nom de l'auteur (Jointure)
     */
    public function getCommentsByTestId(int $test_id): array
    {
        $query = "SELECT c.comment_id, c.content, c.created_at, u.username 
                  FROM {$this->table_name} c 
                  LEFT JOIN users u ON c.user_id = u.user_id 
                  WHERE c.test_id = :test_id 
                  ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':test_id', $test_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime un commentaire
     */
    public function deleteComment(int $comment_id): bool
    {
        $query = "DELETE FROM {$this->table_name} WHERE comment_id = :comment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

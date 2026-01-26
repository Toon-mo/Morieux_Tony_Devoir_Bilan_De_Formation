<?php

namespace Controllers;

require_once __DIR__ . '/../Config/CORS.php';

use Models\TestModel;
use PDO;

class TestController
{
    private TestModel $model;

    public function __construct(PDO $db)
    {
        $this->model = new TestModel($db);
        header('Content-Type: application/json; charset=UTF-8');
    }

    /* =========================
       ===== READ (GET) ========
       ========================= */
    public function index(): void
    {
        try {
            $tests = $this->model->getAllTests();

            // 🔍 Log pour debug (à retirer en production)
            error_log("📊 Nombre de tests récupérés: " . count($tests));
            if (!empty($tests)) {
                error_log("🔍 Premier test: " . json_encode($tests[0]));
            }

            http_response_code(200);
            echo json_encode($tests, JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            error_log("❌ Erreur getAllTests: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur lors de la récupération des tests",
                "error" => $e->getMessage() // Debug uniquement
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function show(int $id): void
    {
        try {
            $test = $this->model->getTestDetails($id);

            if ($test) {
                http_response_code(200);
                echo json_encode($test, JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "message" => "Test non trouvé"
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (\Exception $e) {
            error_log("❌ Erreur getTestDetails($id): " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur lors de la récupération du test"
            ], JSON_UNESCAPED_UNICODE);
        }
    }


    /* =========================
       ===== CREATE (POST) =====
       ========================= */
    public function createTest(): void
    {
        // Vérification des champs obligatoires
        if (empty($_POST['title']) || empty($_POST['machine_id']) || empty($_POST['material_id'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Champs obligatoires manquants (title, machine_id, material_id)"
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $data = $_POST;
        $data['image'] = 'uploads/tests/default.jpg';

        // ===== UPLOAD IMAGE =====
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            $file = $_FILES['image'];

            // Taille max : 5 Mo
            if ($file['size'] > 5 * 1024 * 1024) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "Image trop volumineuse (5 Mo max)"
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            // Vérification MIME
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $mimeType = mime_content_type($file['tmp_name']);

            if (!in_array($mimeType, $allowedTypes)) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "Format d'image non autorisé (JPEG, PNG, WEBP)"
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('test_', true) . '.' . strtolower($extension);

            $uploadDir = __DIR__ . '/../../public/uploads/tests/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                $data['image'] = 'uploads/tests/' . $fileName;
                error_log("✅ Image uploadée: " . $fileName);
            } else {
                error_log("❌ Échec upload image");
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => "Erreur lors de l'upload de l'image"
                ], JSON_UNESCAPED_UNICODE);
                return;
            }
        }

        // Création en base
        try {
            if ($this->model->createTest($data)) {
                error_log("✅ Test créé avec succès");
                http_response_code(201);
                echo json_encode([
                    "success" => true,
                    "message" => "Test créé avec succès",
                    "image" => $data['image']
                ], JSON_UNESCAPED_UNICODE);
            } else {
                error_log("❌ Échec création test en BDD");
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => "Erreur lors de la création du test"
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (\Exception $e) {
            error_log("❌ Exception createTest: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur serveur : " . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /* =========================
       ===== UPDATE (PUT) =====
       ========================= */
    public function updateTest(int $id): void
    {
        // Récupération des données PUT
        $_PUT = [];
        parse_str(file_get_contents("php://input"), $_PUT);

        // Vérification des champs obligatoires
        if (empty($_PUT['title']) || empty($_PUT['machine_id']) || empty($_PUT['material_id'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Champs obligatoires manquants"
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $data = $_PUT;
        $data['image'] = $_PUT['image'] ?? 'uploads/tests/default.jpg';

        try {
            if ($this->model->updateTest($id, $data)) {
                error_log("✅ Test $id mis à jour");
                http_response_code(200);
                echo json_encode([
                    "success" => true,
                    "message" => "Test mis à jour avec succès"
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => "Erreur lors de la mise à jour"
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (\Exception $e) {
            error_log("❌ Exception updateTest: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur serveur"
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /* =========================
       ===== DELETE =====
       ========================= */
    public function deleteTest(int $id): void
    {
        try {
            $test = $this->model->getTestDetails($id);

            if (!$test) {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "message" => "Test non trouvé"
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            // Suppression de l'image si elle existe et n'est pas celle par défaut
            if ($test['image'] && $test['image'] !== 'uploads/tests/default.jpg') {
                $path = __DIR__ . '/../../public/' . $test['image'];
                if (file_exists($path)) {
                    unlink($path);
                    error_log("🗑️ Image supprimée: " . $test['image']);
                }
            }

            if ($this->model->deleteTest($id)) {
                error_log("✅ Test $id supprimé");
                http_response_code(200);
                echo json_encode([
                    "success" => true,
                    "message" => "Test supprimé avec succès"
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => "Échec de la suppression"
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (\Exception $e) {
            error_log("❌ Exception deleteTest: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur serveur"
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}

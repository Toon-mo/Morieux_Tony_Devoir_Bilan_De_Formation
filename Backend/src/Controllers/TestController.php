<?php

namespace Controllers;

require_once __DIR__ . '/../Config/CORS.php';

use Models\TestModel;

class TestController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new TestModel($db);
    }

    /* =========================
       ===== READ (GET) ========
       ========================= */

    public function index()
    {
        try {
            $tests = $this->model->getAllTests();
            http_response_code(200);
            echo json_encode($tests);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur lors de la récupération des tests"
            ]);
        }
    }

    public function show($id)
    {
        try {
            $test = $this->model->getTestDetails($id);

            if ($test) {
                http_response_code(200);
                echo json_encode($test);
            } else {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "message" => "Test non trouvé"
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur lors de la récupération du test"
            ]);
        }
    }

    /* =========================
       ===== CREATE (POST) =====
       ========================= */

    /**
     * [POST] Création d'un nouveau test avec upload d'image sécurisé
     * 
     * Test :
     * - Méthode : POST
     * - URL : /api/tests.php
     * - Content-Type : multipart/form-data
     * - Body : 
     *   - title (string)
     *   - description (string)
     *   - machine_id (int)
     *   - material_id (int)
     *   - user_id (int)
     *   - speed, power, frequency, etc.
     *   - image (file)
     */
    public function CreateTest()
    {
        // Validation des champs obligatoires
        if (empty($_POST['title']) || empty($_POST['machine_id']) || empty($_POST['material_id'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Données incomplètes (title, machine_id, material_id requis)"
            ]);
            return;
        }

        $data = $_POST;

        // 🔒 GESTION SÉCURISÉE DE L'UPLOAD D'IMAGE
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            $file = $_FILES['image'];

            // Validation de la taille (max 5 Mo)
            $maxSize = 5 * 1024 * 1024; // 5 Mo en octets
            if ($file['size'] > $maxSize) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "L'image ne doit pas dépasser 5 Mo"
                ]);
                return;
            }

            // Validation du type MIME
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "Format d'image non autorisé (JPEG, PNG, WEBP uniquement)"
                ]);
                return;
            }

            // Génération d'un nom de fichier sécurisé
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $safeName = time() . "_" . uniqid() . "." . strtolower($extension);

            // Vérification et création du dossier uploads si nécessaire
            $uploadDir = __DIR__ . "/../../public/uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destination = $uploadDir . $safeName;

            // Déplacement du fichier
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $data['image'] = $safeName;
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => "Échec de l'upload de l'image"
                ]);
                return;
            }
        } else {
            // Image par défaut si aucune image n'est fournie
            $data['image'] = 'default.jpg';
        }

        // Création du test en base de données
        try {
            if ($this->model->createTest($data)) {
                http_response_code(201);
                echo json_encode([
                    "success" => true,
                    "message" => "Test créé avec succès",
                    "image" => $data['image']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "success" => false,
                    "message" => "Erreur lors de la création du test"
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur serveur : " . $e->getMessage()
            ]);
        }
    }

    /* =========================
       ===== UPDATE (PUT) ======
       ========================= */

    public function updateTest($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data)) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Aucune donnée à mettre à jour"
            ]);
            return;
        }

        try {
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
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur serveur"
            ]);
        }
    }

    /* =========================
       ===== DELETE (DELETE) ====
       ========================= */

    public function deleteTest($id)
    {
        try {
            // Récupération du test pour supprimer l'image associée
            $test = $this->model->getTestDetails($id);

            if ($test && $test['image'] !== 'default.jpg') {
                $imagePath = __DIR__ . "/../../public/uploads/" . $test['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Suppression physique du fichier
                }
            }

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
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Erreur serveur"
            ]);
        }
    }
}

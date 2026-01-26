<?php

namespace Controllers;

use Models\MaterialModel;
use PDO;

/**
 * MaterialController
 * 
 * Architecture :
 * Postman/Vue.js → api/materials.php → MaterialController → MaterialModel
 */
class MaterialController
{
    private MaterialModel $model;

    public function __construct(PDO $db)
    {
        $this->model = new MaterialModel($db);
    }

    /**
     * [GET] Lister tous les matériaux
     * 
     * Réponses : 200 (Liste JSON), 500 (Erreur)
     */
    public function index(): void
    {
        try {
            $materials = $this->model->getAllMaterials();
            echo json_encode($materials);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erreur serveur"]);
        }
    }

    /**
     * [GET] Détails d'un matériau
     * 
     * @param int $id
     */
    public function show(int $id): void
    {
        try {
            $material = $this->model->getMaterialById($id);
            if ($material) {
                echo json_encode($material);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Matériau inconnu"]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erreur serveur"]);
        }
    }

    /**
     * [GET] Matériaux filtrés par machine
     * 
     * URL Postman : /api/materials.php?machine_id=1
     */
    public function getByMachine(int $machineId): void
    {
        try {
            $materials = $this->model->getMaterialsByMachine($machineId);
            echo json_encode($materials);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erreur de filtrage"]);
        }
    }

    /**
     * [POST] Ajouter un matériau
     * 
     * Body JSON attendu :
     * { "name": "Bois de Chêne", "category": "Bois", "thickness": 5.0 }
     */
    public function createMaterial(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(["message" => "Le nom du matériau est requis"]);
            return;
        }

        try {
            if ($this->model->createMaterial($data)) {
                http_response_code(201);
                echo json_encode(["success" => true, "message" => "Matériau ajouté"]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Échec de l'insertion"]);
        }
    }
}

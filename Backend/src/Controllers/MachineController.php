<?php

namespace Controllers;

use Models\MachineModel;
use PDO;

/**
 * MachineController
 */
class MachineController
{
    private MachineModel $model;

    public function __construct(PDO $db)
    {
        $this->model = new MachineModel($db);
    }

    public function index(): void
    {
        try {
            $machines = $this->model->getAllMachines();
            echo json_encode($machines);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erreur lors de la récupération"]);
        }
    }

    public function show(int $id): void
    {
        $machine = $this->model->getMachineById($id);
        if ($machine) {
            echo json_encode($machine);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Machine non trouvée"]);
        }
    }

    public function createMachine(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data['name']) || empty($data['laser_type'])) {
            http_response_code(400);
            echo json_encode(["message" => "Données incomplètes"]);
            return;
        }

        if ($this->model->createMachine($data)) {
            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Machine ajoutée"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Échec de l'insertion"]);
        }
    }

    public function deleteMachine(int $id): void
    {
        if ($this->model->deleteMachine($id)) {
            echo json_encode(["success" => true, "message" => "Machine supprimée"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Échec de la suppression"]);
        }
    }
}

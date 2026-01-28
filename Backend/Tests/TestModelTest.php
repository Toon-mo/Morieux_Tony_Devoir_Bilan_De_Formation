<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Config\Database;
use Models\TestModel;

class TestModelTest extends TestCase
{
    private $db;
    private $testModel;

    protected function setUp(): void
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->testModel = new TestModel($this->db);
    }

    public function testCreateTestWithParameters()
    {
        $data = [
            'title' => 'Unit Test Engraving',
            'description' => 'Testing SQL Transactions',
            'user_id' => 1,
            'machine_id' => 1,
            'material_id' => 1,
            'speed' => 800,
            'power' => 30,
            'frequency' => 45,
            'pulse' => 200,
            'nb_passes' => 1,
            'sweep' => 1,
            'hatch' => 1,
            'row_interval' => 0.05,
            'wobble' => 0,
            'image' => 'test_unit.jpg'
        ];

        // On teste la méthode de création qui utilise une transaction
        $success = $this->testModel->createTest($data);

        $this->assertTrue($success, "La transaction SQL a échoué");

        // On vérifie si les paramètres ont bien été créés dans la table liée
        $test = $this->db->query("SELECT * FROM tests WHERE title = 'Unit Test Engraving'")->fetch();
        $params = $this->db->query("SELECT * FROM parameters WHERE test_id = " . $test['test_id'])->fetch();

        $this->assertNotEmpty($params);
        $this->assertEquals(800, $params['speed']);

        // Nettoyage après le test
        $this->db->exec("DELETE FROM tests WHERE test_id = " . $test['test_id']);
    }
}

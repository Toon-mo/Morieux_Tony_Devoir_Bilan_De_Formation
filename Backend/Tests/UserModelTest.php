<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Config\Database;
use Models\UserModel;

class UserModelTest extends TestCase
{
    private $db;
    private $userModel;
    private $testEmail = 'test-unitaire@inco.fr';

    protected function setUp(): void
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new UserModel($this->db);

        // Nettoyage préventif
        $this->db->exec("DELETE FROM users WHERE email = '{$this->testEmail}'");
    }

    public function testRegisterUser()
    {
        $data = [
            'username' => 'TestBot',
            'email' => $this->testEmail,
            'password' => 'Secr3tPassword!'
        ];

        $result = $this->userModel->createUser($data);
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testLoginLogic()
    {
        // 1. On crée d'abord l'utilisateur DANS ce test
        $data = [
            'username' => 'LoginBot',
            'email' => $this->testEmail,
            'password' => 'LoginPassword123'
        ];
        $this->userModel->createUser($data);

        // 2. Maintenant on teste si on peut le récupérer
        $user = $this->userModel->getUserByEmail($this->testEmail);

        $this->assertIsArray($user, "L'utilisateur n'a pas été trouvé en base de données");
        $this->assertEquals('LoginBot', $user['username']);

        // 3. Test de la sécurité BCRYPT
        $this->assertTrue(password_verify('LoginPassword123', $user['password_hash']));
        $this->assertFalse(password_verify('mauvais-password', $user['password_hash']));
    }
}

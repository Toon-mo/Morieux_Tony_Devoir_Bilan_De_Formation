<?php

namespace Controllers;

require_once __DIR__ . '/../Config/CORS.php';

use Models\UserModel;

/**
 * UserController
 *
 * Rôle :
 * - Gérer les requêtes HTTP liées aux utilisateurs
 * - Servir de point d'entrée pour les appels API
 * - Valider les données, appeler le modèle, renvoyer du JSON
 *
 * Architecture :
 * Frontend → Routes → UserController → UserModel → Base de données
 */
class UserController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new UserModel($db);
    }

    /* =========================
       ===== CREATE (POST) =====
       ========================= */

    /**
     * [POST] Inscription d'un nouvel utilisateur
     *
     * Test :
     * - Méthode : POST
     * - URL : /api/register.php
     * - Body (JSON) :
     *   {
     *     "username": "Tony",
     *     "email": "tony@mail.com",
     *     "password": "secret"
     *   }
     *
     * Réponses :
     * - 201 : utilisateur créé
     * - 400 : données manquantes ou invalides
     * - 409 : email déjà utilisé
     * - 500 : erreur serveur
     */
    public function register()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validation des champs obligatoires
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Données incomplètes (username, email, password requis)"
            ]);
            return;
        }

        // Validation du format email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Format d'email invalide"
            ]);
            return;
        }

        // Validation de la longueur du mot de passe
        if (strlen($data['password']) < 6) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Le mot de passe doit contenir au moins 6 caractères"
            ]);
            return;
        }

        // Vérification si l'email existe déjà
        $existingUser = $this->model->getUserByEmail($data['email']);
        if ($existingUser) {
            http_response_code(409);
            echo json_encode([
                "success" => false,
                "message" => "Cet email est déjà utilisé"
            ]);
            return;
        }

        // Création de l'utilisateur
        $userId = $this->model->createUser($data);

        if ($userId) {
            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "Utilisateur créé avec succès",
                "user_id" => $userId
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Échec de la création de l'utilisateur"
            ]);
        }
    }

    /* =========================
       ===== READ (GET) ========
       ========================= */

    /**
     * [GET] Récupérer tous les utilisateurs ou rechercher par email
     *
     * Test :
     * - Méthode : GET
     * - URL : /api/users.php
     * - URL : /api/users.php?email=test@mail.com
     *
     * Réponses :
     * - 200 : utilisateur(s) trouvé(s)
     * - 404 : utilisateur non trouvé
     */
    public function index()
    {
        // Si recherche par email
        if (isset($_GET['email']) && !empty($_GET['email'])) {
            $user = $this->model->getUserByEmail($_GET['email']);

            if ($user) {
                // 🔒 SÉCURITÉ : Suppression du hash avant envoi
                unset($user['password_hash']);

                http_response_code(200);
                echo json_encode($user);
            } else {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "message" => "Utilisateur non trouvé"
                ]);
            }
            return;
        }

        // Sinon, retourner tous les utilisateurs (pour admin)
        // Note : À protéger avec un middleware d'authentification
        http_response_code(501);
        echo json_encode([
            "message" => "Fonctionnalité non implémentée"
        ]);
    }

    /* =========================
       ===== LOGIN (POST) ======
       ========================= */

    /**
     * [POST] Connexion utilisateur
     *
     * Test :
     * - Méthode : POST
     * - URL : /api/login.php
     * - Body (JSON) :
     *   {
     *     "email": "tony@mail.com",
     *     "password": "secret"
     *   }
     *
     * Réponses :
     * - 200 : connexion réussie
     * - 401 : identifiants invalides
     * - 400 : champs manquants
     */
    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validation des champs obligatoires
        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Email et mot de passe requis"
            ]);
            return;
        }

        // Recherche de l'utilisateur par email
        $user = $this->model->getUserByEmail($data['email']);

        // Vérification du mot de passe
        if ($user && password_verify($data['password'], $user['password_hash'])) {

            // 🔒 SÉCURITÉ : Suppression du hash avant envoi
            unset($user['password_hash']);

            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Connexion réussie",
                "user" => $user
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "message" => "Email ou mot de passe incorrect"
            ]);
        }
    }

    /* =========================
       ===== READ BY ID ========
       ========================= */

    /**
     * [GET] Recherche d'un utilisateur par ID
     *
     * Test :
     * - Méthode : GET
     * - URL : /api/users.php?id=1
     *
     * @param int $id
     */
    public function show($id)
    {
        $user = $this->model->getUserById($id);

        if ($user) {
            // 🔒 SÉCURITÉ : Suppression du hash avant envoi
            unset($user['password_hash']);

            http_response_code(200);
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Utilisateur non trouvé"
            ]);
        }
    }

    /* =========================
       ===== UPDATE (PUT) ======
       ========================= */

    /**
     * [PUT] Mise à jour d'un utilisateur
     * 
     * Test :
     * - Méthode : PUT
     * - URL : /api/users.php?id=1
     * - Body JSON :
     * {
     *   "username": "NewName",
     *   "email": "newmail@mail.com"
     * }
     * 
     * Réponses :
     * - 200 : utilisateur mis à jour
     * - 400 : données invalides
     * - 404 : utilisateur non trouvé
     * - 500 : erreur serveur
     */
    public function updateUser($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validation minimale
        if (empty($data['username']) && empty($data['email'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Aucune donnée à mettre à jour"
            ]);
            return;
        }

        // Validation du format email si fourni
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Format d'email invalide"
            ]);
            return;
        }

        if ($this->model->updateUser($id, $data)) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Utilisateur mis à jour avec succès"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "La mise à jour a échoué"
            ]);
        }
    }

    /* =========================
       ===== DELETE (DELETE) ====
       ========================= */

    /**
     * [DELETE] Supprimer un utilisateur
     *
     * Test :
     * - Méthode : DELETE
     * - URL : /api/users.php?id=1
     *
     * @param int $id
     */
    public function deleteUser($id)
    {
        if ($this->model->deleteUser($id)) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Utilisateur supprimé avec succès"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "La suppression a échoué"
            ]);
        }
    }
}

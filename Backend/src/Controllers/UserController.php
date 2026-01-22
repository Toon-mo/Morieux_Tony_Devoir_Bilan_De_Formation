<?php

namespace Controllers;

use Models\UserModel;

/**
 * UserController
 *
 * Rôle :
 * - Gérer les requêtes HTTP liées aux utilisateurs
 * - Servir de point d’entrée pour les appels Postman
 * - Valider les données, appeler le modèle, renvoyer du JSON
 *
 * Architecture :
 * Postman → Routes → UserController → UserModel → Base de données
 */
class UserController
{
    /**
     * Instance du modèle utilisateur
     */
    private $model;

    /**
     * Constructeur
     *
     * @param PDO $db Connexion à la base de données
     *
     * Fonctionnement avec Postman :
     * - À chaque requête Postman, le contrôleur est instancié
     * - Le modèle UserModel reçoit la connexion PDO
     * - Les headers CORS permettent les appels API depuis n’importe quel client
     */
    public function __construct($db)
    {
        $this->model = new UserModel($db);

        /* =========================
           ====== HEADERS CORS =====
           ========================= */

        // Autorise l’API à être appelée depuis n’importe quelle origine (Postman, navigateur, front-end)
        header("Access-Control-Allow-Origin: *");

        // Autorise certains en-têtes HTTP
        header("Access-Control-Allow-Headers: access");

        // Méthodes HTTP autorisées pour l’API
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

        // Format de réponse JSON (indispensable pour Postman)
        header("Content-Type: application/json; charset=UTF-8");

        // En-têtes autorisés pour les requêtes JSON / Auth
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        // Réponse automatique aux requêtes OPTIONS (pré-vol CORS)
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    /* =========================
       ===== CREATE (POST) =====
       ========================= */

    /**
     * [POST] Inscription d’un nouvel utilisateur
     *
     * Test Postman :
     * - Méthode : POST
     * - URL : /api/register
     * - Body (JSON) :
     *   {
     *     "username": "Tony",
     *     "email": "tony@mail.com",
     *     "password": "secret"
     *   }
     *
     * Réponses :
     * - 201 : utilisateur créé
     * - 400 : données manquantes
     * - 500 : erreur serveur
     */
    public function register()
    {
        // Récupération des données envoyées par Postman
        $data = json_decode(file_get_contents("php://input"), true);

        // Vérification des champs obligatoires
        if (!empty($data['username']) && !empty($data['email']) && !empty($data['password'])) {

            // Appel du modèle pour créer l’utilisateur
            if ($this->model->createUser($data)) {
                http_response_code(201);
                echo json_encode(["message" => "Utilisateur créé avec succès"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Échec de la création de l'utilisateur"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Données incomplètes"]);
        }
    }

    /* =========================
       ===== READ (GET) ========
       ========================= */

    /**
     * [GET] Recherche d’un utilisateur par email (via query string)
     *
     * Test Postman :
     * - Méthode : GET
     * - URL : /api/users?email=test@mail.com
     *
     * Réponses :
     * - 200 : utilisateur trouvé
     * - 404 : utilisateur non trouvé
     */
    public function index()
    {
        // Récupération de l’email depuis l’URL
        $user = $this->model->getUserByEmail($_GET['email'] ?? '');

        if ($user) {
            // Sécurité : on ne renvoie jamais le hash du mot de passe
            unset($user['password_hash']);
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Utilisateur non trouvé"]);
        }
    }

    /* =========================
       ===== LOGIN (POST) ======
       ========================= */

    /**
     * [POST] Connexion utilisateur
     *
     * Test Postman :
     * - Méthode : POST
     * - URL : /api/login
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
        // Lecture du body JSON envoyé par Postman
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['email']) && !empty($data['password'])) {

            // Recherche de l’utilisateur par email
            $user = $this->model->getUserByEmail($data['email']);

            // Vérification du mot de passe avec le hash stocké en base
            if ($user && password_verify($data['password'], $user['password_hash'])) {

                // On retire le mot de passe avant la réponse
                unset($user['password_hash']);

                http_response_code(200);
                echo json_encode([
                    "message" => "Connexion réussie",
                    "user" => $user
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Email ou mot de passe incorrect"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Identifiants manquants"]);
        }
    }

    /* =========================
       ===== READ BY ID ========
       ========================= */

    /**
     * [GET] Recherche d’un utilisateur par ID
     *
     * Test Postman :
     * - Méthode : GET
     * - URL : /api/users/{id}
     *
     * @param int $id
     */
    public function show($id)
    {
        $user = $this->model->getUserById($id);

        if ($user) {
            unset($user['password_hash']);
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Utilisateur non trouvé"]);
        }
    }

    /* =========================
       ===== READ BY EMAIL =====
       ========================= */

    /**
     * [GET] Recherche d’un utilisateur par email (paramètre direct)
     *
     * Test Postman :
     * - Méthode : GET
     * - URL : /api/users/email/{email}
     *
     * @param string $email
     */
    public function showByEmail($email)
    {
        $user = $this->model->getUserByEmail($email);

        if ($user) {
            unset($user['password_hash']);
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Utilisateur non trouvé"]);
        }
    }
}

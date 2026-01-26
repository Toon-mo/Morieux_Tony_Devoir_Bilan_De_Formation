<?php

/**
 * Configuration CORS (Cross-Origin Resource Sharing)
 * 
 * Ce fichier centralise la gestion des politiques de partage de ressources
 * entre le frontend (Vue.js) et le backend (API PHP).
 * 
 * 🔒 SÉCURITÉ :
 * - En développement : Autorise uniquement localhost:5173 (Vite)
 * - En production : Remplacer par le domaine réel (ex: https://inco-laser.com)
 */

// Récupération de l'environnement
$environment = $_ENV['APP_ENV'] ?? 'development';

// Configuration de l'origine autorisée selon l'environnement
if ($environment === 'production') {
    // 🔒 EN PRODUCTION : Spécifier le domaine exact
    $allowedOrigin = $_ENV['FRONTEND_URL'] ?? 'https://inco-laser.com';
} else {
    // En développement : Autoriser le serveur Vite local
    $allowedOrigin = 'http://localhost:5173';
}

// Application des headers CORS
header("Access-Control-Allow-Origin: {$allowedOrigin}");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// Gestion de la requête de pré-vol (Preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

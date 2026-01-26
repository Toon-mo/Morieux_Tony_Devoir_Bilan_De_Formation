<?php
// Backend/api/image.php

// Vérifie qu'un nom de fichier est passé
if (!isset($_GET['name'])) {
    http_response_code(400);
    echo json_encode(["error" => "Nom de fichier manquant"]);
    exit;
}

$filename = basename($_GET['name']); // sécurisation simple
$filepath = __DIR__ . '/../public/uploads/tests/' . $filename;

// Vérifie que le fichier existe
if (!file_exists($filepath)) {
    http_response_code(404);
    echo json_encode(["error" => "Fichier non trouvé"]);
    exit;
}

// Renvoie l'image avec le bon header
$mime = mime_content_type($filepath);
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
exit;

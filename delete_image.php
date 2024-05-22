<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tirocinio";

$imageId = $_GET['id'];

if (!isset($imageId)) {
    http_response_code(400); 
    echo "ID immagine non specificato.";
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname, 3307);

if ($conn->connect_error) {
    http_response_code(500); 
    echo "Connessione al database fallita: " . $conn->connect_error;
    exit();
}

$sql_delete_segmented_images = "DELETE FROM segmented_images WHERE ID_ImmagineOriginale = ?";
$stmt_delete_segmented_images = $conn->prepare($sql_delete_segmented_images);
$stmt_delete_segmented_images->bind_param("i", $imageId);
$stmt_delete_segmented_images->execute();

if ($stmt_delete_segmented_images->error) {
    http_response_code(500); 
    echo "Errore durante l'eliminazione delle immagini segmentate: " . $stmt_delete_segmented_images->error;
    exit();
}

$sql_delete_images = "DELETE FROM images WHERE ID = ?";
$stmt_delete_images = $conn->prepare($sql_delete_images);
$stmt_delete_images->bind_param("i", $imageId);
$stmt_delete_images->execute();

if ($stmt_delete_images->error) {
    http_response_code(500); 
    echo "Errore durante l'eliminazione delle immagini: " . $stmt_delete_images->error;
    exit();
}

$stmt_delete_segmented_images->close();
$stmt_delete_images->close();
$conn->close();

http_response_code(200); 
echo "Immagine eliminata con successo.";
?>
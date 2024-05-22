<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tirocinio";
$port = 3307;


if (isset($_FILES['imageFile']) && isset($_FILES['segmentedImageFile'])) {
    $originalFile = $_FILES['imageFile'];
    $segmentedFile = $_FILES['segmentedImageFile'];

    $targetDirectory = "upload/"; 
    $targetOriginalFile = $targetDirectory . basename($originalFile['name']);
    $targetSegmentedFile = "segmentedImages/" . uniqid() . "_" . basename($segmentedFile['name']);

    if (move_uploaded_file($originalFile['tmp_name'], $targetOriginalFile) &&
        move_uploaded_file($segmentedFile['tmp_name'], $targetSegmentedFile)) {

        $conn = new mysqli($servername, $username, $password, $dbname, $port);

        if ($conn->connect_error) {
            die("Connessione al database fallita: " . $conn->connect_error);
        }

        $originalFilePath = $targetDirectory . basename($originalFile['name']);

        $checkImageQuery = "SELECT ID FROM images WHERE nome = '$originalFilePath'";
        $checkImageResult = $conn->query($checkImageQuery);

        if ($checkImageResult->num_rows > 0) {
            $row = $checkImageResult->fetch_assoc();
            $originalImageID = $row['ID'];
        } else {
            $sqlOriginal = "INSERT INTO images (nome) VALUES ('$originalFilePath')";
            if ($conn->query($sqlOriginal) === TRUE) {
                $originalImageID = $conn->insert_id;
            } else {
                echo "Errore durante il salvataggio dell'immagine originale nel database: " . $conn->error;
                exit;
            }
        }

        $sqlSegmented = "INSERT INTO segmented_images (ID_ImmagineOriginale, nome) VALUES ('$originalImageID', '$targetSegmentedFile')";
        if ($conn->query($sqlSegmented) === TRUE) {
            echo "Immagini salvate con successo nel database.";
        } else {
            echo "Errore durante il salvataggio dell'immagine segmentata nel database: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "Si è verificato un errore durante il salvataggio delle immagini.";
    }
} else {
    echo "Nessun file inviato.";
}
?>
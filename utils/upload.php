<?php
$uploadDirectory = '../upload/';

if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileName = basename($_FILES['image']['name']);
    $uploadPath = $uploadDirectory . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        echo "Immagine caricata con successo: " . $fileName;
    } else {
        echo "Si è verificato un errore durante il caricamento dell'immagine.";
    }
} else {
    echo "Si è verificato un errore durante l'upload del file.";
}
?>

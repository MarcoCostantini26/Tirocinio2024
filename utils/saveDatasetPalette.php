<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tirocinio";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

$datasetId = intval($data['dataset']);
$paletteId = intval($data['palette']);
$minGradient = $data['minGradient'];
$maxGradient = $data['maxGradient'];

$checkIfExistsQuery = "SELECT * FROM dataset_palette WHERE ID_Dataset = $datasetId";
$result = $conn->query($checkIfExistsQuery);

if ($result->num_rows > 0) {
    $updateQuery = "UPDATE dataset_palette SET ID_Palette = $paletteId, start_color = '$minGradient', end_color = '$maxGradient' WHERE ID_Dataset = $datasetId";
    
    if ($conn->query($updateQuery) === TRUE) {
        echo "Aggiornamento del collegamento nel database effettuato con successo";
    } else {
        echo "Errore durante l'aggiornamento del collegamento nel database: " . $conn->error;
    }
} else {
    $insertQuery = "INSERT INTO dataset_palette (ID_Dataset, ID_Palette, start_color, end_color) VALUES ($datasetId, $paletteId, '$minGradient', '$maxGradient')";
    
    if ($conn->query($insertQuery) === TRUE) {
        echo "Collegamento salvato nel database con successo";
    } else {
        echo "Errore durante il salvataggio del collegamento nel database: " . $conn->error;
    }
}

$conn->close();
?>

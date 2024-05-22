<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tirocinio";

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    $id = $_GET['id'];

    $conn = new mysqli($servername, $username, $password, $dbname, 3307);

    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    $sqlDeleteData = "DELETE data, ds_dataset, dataset 
                  FROM data
                  INNER JOIN ds_dataset ON data.ID_ds_dataset = ds_dataset.ID 
                  INNER JOIN dataset ON ds_dataset.ID_Dataset = dataset.ID
                  WHERE dataset.ID = $id";

    if ($conn->query($sqlDeleteData) === TRUE) {
        $conn->commit();

        http_response_code(200);
        echo "Dataset e dati associati eliminati con successo";
    } else {
        $conn->rollback();

        http_response_code(500);
        echo "Errore durante l'eliminazione del dataset e dei dati associati: " . $conn->error;
    }

    $conn->close();
} else {
    http_response_code(405);
    echo "Metodo non consentito";
}
?>
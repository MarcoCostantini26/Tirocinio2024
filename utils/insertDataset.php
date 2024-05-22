<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tirocinio";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

$datasetName = $data['name'];

$sql = "INSERT INTO dataset (Nome) VALUES ('$datasetName')";
if ($conn->query($sql) === TRUE) {
    $datasetID = $conn->insert_id; 

    foreach ($data['datasets'] as $dataset) {
        $dsName = $dataset['ds_name'];
        $minValue = $dataset['min_value'];
        $maxValue = $dataset['max_value'];

        $sql = "INSERT INTO ds_dataset (nome, min_value, max_value, ID_Dataset) VALUES ('$dsName', $minValue, $maxValue, $datasetID)";
        $conn->query($sql);
        $dsID = $conn->insert_id; 

        foreach ($dataset['data'] as $dataItem) {
            $description = $dataItem['description'];
            $value = $dataItem['value'];

            $sql = "INSERT INTO data (descrizione, valore, ID_ds_dataset) VALUES ('$description', $value, $dsID)";
            $conn->query($sql);
        }
    }

    echo json_encode(array("success" => true, "message" => "Dataset inserito con successo."));
} else {
    echo json_encode(array("success" => false, "message" => "Errore durante l'inserimento del dataset: " . $conn->error));
}

$conn->close();

?>

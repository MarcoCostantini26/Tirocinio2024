<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tirocinio";

$conn = new mysqli($servername, $username, $password, $dbname, 3307);

if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

$name = $conn->real_escape_string($data['name']);
$datasets = $data['datasets'];

$sql = "UPDATE dataset SET Nome = '$name' WHERE ID = {$data['ID']}";

if ($conn->query($sql) === TRUE) {
    $sql_delete_old_data = "DELETE FROM data WHERE ID_ds_dataset IN (SELECT ID FROM ds_dataset WHERE ID_Dataset = {$data['ID']})";
    $conn->query($sql_delete_old_data);
    
    foreach ($datasets as $dataset) {
        $ds_name = $conn->real_escape_string($dataset['ds_name']);
        $min_value = $conn->real_escape_string($dataset['min_value']);
        $max_value = $conn->real_escape_string($dataset['max_value']);
        $sql_insert_ds_dataset = "INSERT INTO ds_dataset (ID_Dataset, Nome, min_value, max_value) VALUES ({$data['ID']}, '$ds_name', $min_value, $max_value)";
        $conn->query($sql_insert_ds_dataset);
        
        $ds_dataset_id = $conn->insert_id;
        
        foreach ($dataset['data'] as $dataItem) {
            $description = $conn->real_escape_string($dataItem['description']);
            $value = $conn->real_escape_string($dataItem['value']);
            $sql_insert_data = "INSERT INTO data (ID_ds_dataset, descrizione, valore) VALUES ($ds_dataset_id, '$description', $value)";
            $conn->query($sql_insert_data);
        }
    }
    
    echo "Dataset aggiornato con successo.";
} else {
    echo "Errore durante l'aggiornamento del dataset: " . $conn->error;
}

$conn->close();
?>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tirocinio";

$conn = new mysqli($servername, $username, $password, $dbname, 3307);

if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}

$dataset_id = $_GET['id'];

$sql = "SELECT ds.ID AS dataset_id, ds.Nome AS dataset_name, dss.ID AS ds_dataset_id, dss.Nome AS ds_name, dss.min_value, dss.max_value, d.ID AS data_id, d.descrizione, d.valore
        FROM dataset AS ds, ds_dataset AS dss, data AS d
        WHERE d.ID_ds_dataset = dss.ID
        AND dss.ID_Dataset = ds.ID
        AND ds.ID = $dataset_id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $dataset_details = array();
    while ($row = $result->fetch_assoc()) {
        $dataset_details['ID'] = $row['dataset_id'];
        $dataset_details['name'] = $row['dataset_name'];
        $dataset_details['datasets'][] = array(
            'ds_id' => $row['ds_dataset_id'],
            'ds_name' => $row['ds_name'],
            'min_value' => $row['min_value'],
            'max_value' => $row['max_value'],
            'data' => array(
                'data_id' => $row['data_id'],
                'description' => $row['descrizione'],
                'value' => $row['valore']
            )
        );
    }
    echo json_encode($dataset_details);
} else {
    echo json_encode(array('error' => 'Nessun dataset trovato con ID: ' . $dataset_id));
}

$conn->close();
?>
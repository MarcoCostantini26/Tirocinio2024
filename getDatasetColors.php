<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tirocinio";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
$datasetId = intval($_POST['dataset']);

$sql = "SELECT C.codice 
        FROM colore C, colori_discreti CD, palette P, dataset_palette DP
        WHERE DP.ID_Dataset = $datasetId
        AND DP.ID_Palette = P.ID
        AND P.ID = CD.ID_Palette
        AND CD.ID_Colore = C.ID
        AND CD.ordine >= DP.start_color 
        AND CD.ordine <= DP.end_color";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $colors = array();

    while($row = $result->fetch_assoc()) {
        array_push($colors, $row['codice']);
    }

    echo json_encode($colors);
} else {
    echo json_encode(array()); 
}

$conn->close();
?>
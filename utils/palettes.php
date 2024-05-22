<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "tirocinio";
$port=3307;

$minGradient = $_GET['minGradient'] ?? 0;
$maxGradient = $_GET['maxGradient'] ?? 100;


$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT palette.ID AS palette_id, palette.nome AS palette_name, GROUP_CONCAT(colore.codice) AS colors 
        FROM palette 
        JOIN colori_discreti ON palette.ID = colori_discreti.ID_Palette 
        JOIN colore ON colori_discreti.ID_Colore = colore.ID 
        WHERE colori_discreti.ordine BETWEEN $minGradient AND $maxGradient
        GROUP BY palette.ID";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $palettes = array();

    while($row = $result->fetch_assoc()) {
        $palette = array(
            'id' => $row['palette_id'],
            'name' => $row['palette_name'],
            'colors' => explode(',', $row['colors'])
        );
        array_push($palettes, $palette);
    }

    echo json_encode($palettes);
} else {
    echo "0 results";
}
$conn->close();
?>

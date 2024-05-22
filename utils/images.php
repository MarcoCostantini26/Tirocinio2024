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

$paletteId = isset($_GET['paletteId']) ? $_GET['paletteId'] + 1 : null;

$sql = "SELECT fi.ID, fi.nome 
        FROM final_images fi";

if ($paletteId !== null) {
    $sql .= " WHERE fi.IDPalette = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $paletteId);
} else {
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

$images = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
}

echo json_encode($images);

$conn->close();
?>

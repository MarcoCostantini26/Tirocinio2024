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

$sql = "SELECT * FROM dataset";
$result = $conn->query($sql);

$datasets = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $datasets[] = array(
            "ID" => $row["ID"],
            "nome" => $row["Nome"]
        );
    }
}

echo json_encode($datasets);

$conn->close();
?>

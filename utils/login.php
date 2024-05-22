<?php

session_start();

header('Content-Type: application/json');

$show_form = false;
$response = array();
$data = json_decode(file_get_contents("php://input"), true);

if(isset($_SESSION["ID"])) {
    $response["error"] = "L'utente è già loggato";
} elseif(isset($data["email"])) {
    $db = new mysqli("localhost", "root", "", "tirocinio", 3307);
    $stmt = $db->prepare("SELECT * FROM utente WHERE email=?");
    $stmt->bind_param("s", $email);
    $email = $data["email"];
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $db->close();

    if(is_null($row)) {
        $response["error"] = "L'utente non esiste";
        $show_form = true;
    } elseif(!password_verify($data["password"], $row["Password"])) {
        $response["error"] = "Password errata";
        $show_form = true;
    } else {
        $_SESSION["ID"] = $row["ID"];
        $response["success"] = "Login effettuato con successo";
    }
} else {
    $show_form = true;
}

if($show_form){
    $response["show_form"] = true;
}

echo json_encode($response);
?>

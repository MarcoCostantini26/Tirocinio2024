<?php

header('Content-Type: application/json');

$response = array();
$data = json_decode(file_get_contents("php://input"), true);

if (isset($_SESSION["ID"])) {
    $response["error"] = "OPS! Sei già registrato";
} elseif (isset($data["email"])) {
    $username = $data["username"];
    $email = $data["email"];
    $password = $data["password"];
    $password2 = $data["password2"];


    $db = new mysqli("localhost", "root", "", "tirocinio", 3307);
    
    if ($password != $password2) {
        $response["error"] = "Le due password non corrispondono";
    } elseif (strlen($username) < 1) {
        $response["error"] = "Il nome utente deve avere almeno un carattere";
    } elseif (strlen($email) < 5) {
        $response["error"] = "La mail non è valida";
    } elseif (strlen($password) < 3) {
        $response["error"] = "La password deve essere lunga almeno 3 caratteri";
    } else {
        $pwhash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO utente(Email,Password,Username) VALUES(?,?,?)");
        $stmt->bind_param("sss", $email, $pwhash, $username);
        try {
            if ($stmt->execute()) {
                $response["success"] = "Registrazione effettuata con successo, ora puoi fare il login";
            } else {
                $response["error"] = "Registrazione non riuscita";
            }
        } catch (mysqli_sql_exception $e) {
            $response["error"] = "Nome utente o e-mail già utilizzati";
        }
    }
    $db->close();
}

echo json_encode($response);

?>
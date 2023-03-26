<?php

require "database.php";
session_start();
if(!isset($_SESSION["user"])) {
  header("Location: login.php");
  return;
}

$id = $_GET["id"];

//  Vemos si el valor del id que nos paso, se encuentra en nuestra base de datos, esto es para evitar las inyecciones SQL, es decir que si no lo encuentra es porque nos mandaron la solicitud desde un curl y no desde la vista en si.
$statement = $conn -> prepare("SELECT * FROM contacts WHERE id = :id");
$statement -> bindParam(":id", $id);
$statement -> execute();

if($statement -> rowCount() == 0){
  http_response_code(404);
  echo("HTTP 404 NOT FOUND");
  return;
}

$contact = $statement->fetch(PDO::FETCH_ASSOC);
if($contact["user_id"] !== $_SESSION["user"]["id"]) {
  http_response_code(403);
  echo("HTTP 403 UNAUTHORIZED");
  return;
}

$statement = $conn -> prepare("DELETE FROM contacts WHERE id = :id");
$statement -> bindParam(":id", $id);
$statement -> execute();

$_SESSION["flash"] = ["message" => "Contact {$contact['name']} deleted"];
header("Location: home.php");

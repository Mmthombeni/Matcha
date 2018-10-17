<?php

require_once ("config/database.php");
require_once ("classes/connectdb.php");

$verified = TRUE;
$errors = NULL;
$handler = NULL;

try{
    $handler = new PDO($DB_DSN . ';dbname=' . $DB_NAME, $DB_USER, $DB_PASSWORD);
    $handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "Connection Failed: " . $e->getMessage();
}

$insert = $handler->prepare("UPDATE `Users` SET Verified = :verified WHERE code LIKE :code");
$insert->bindParam(':verified', $verified);
$insert->bindParam(':code', $_GET['id']);
$insert->execute();
echo "Success";

header("Location: login.php");
exit();
?>
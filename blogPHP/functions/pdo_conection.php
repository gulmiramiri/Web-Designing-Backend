<?php
$servername = "localhost";
$username = "root";
$password = "";

global $pdo;

    try {

    $option = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ,PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ );

    $pdo = new PDO("mysql:host=$servername;dbname=php_project", $username, $password , $option);

    return $pdo; 


} catch(PDOException $e) {
  
    echo "Connection failed: " . $e->getMessage();

}
?>
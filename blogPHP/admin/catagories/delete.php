<?php

    include("../../functions/pdo_conection.php");
    include("../../functions/helpers.php");

    include("../../functions/checkSession.php");

    if(isset($_GET["cat_id"]) && $_GET["cat_id"] !== ""){

    global $pdo;

       $q = "DELETE FROM php_project.`catagories` WHERE id = ?";
                $statment = $pdo->prepare($q);
                $statment->execute([$_GET["cat_id"]]);
                
                
                }
                
                redirect("admin/");

?>


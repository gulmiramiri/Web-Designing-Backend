<?php

    include("../../functions/pdo_conection.php");
    include("../../functions/helpers.php");
        include("../../functions/checkSession.php");



    if(isset($_GET["i"]) && $_GET["i"] !== ""){

    global $pdo;

       $q = "DELETE FROM php_project.`posts` WHERE id = ?";
                $statment = $pdo->prepare($q);
                $statment->execute([$_GET["i"]]);

                
                
                }
                
                redirect("admin/posts/");

?>


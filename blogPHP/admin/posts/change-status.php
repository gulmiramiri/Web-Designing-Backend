<?php

    include("../../functions/pdo_conection.php");
    include("../../functions/helpers.php");
    
    include("../../functions/checkSession.php");



    

    if(isset($_GET["s"]) && $_GET["s"] !== ""){

    global $pdo;


    if ($_GET["s"] == 10){

       $q = "UPDATE posts SET `status` = ? WHERE id=? ";
                $statment = $pdo->prepare($q);
                $statment->execute([1 , $_GET["i"]]);


                
               
                }

                else{



       $q = "UPDATE posts SET `status` = 10 WHERE id=? ";
                $statment = $pdo->prepare($q);
                $statment->execute([$_GET["i"]]);
               

                }

    }
                
                redirect("admin/posts/");

?>


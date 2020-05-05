<?php
    $server = "localhost";
    $user = "root";
    $pass = "";
    $db = "db_crud";

    $connect = mysqli_connect($server, $user, $pass, $db);

    if($connect) {
       // echo "Conectado ao servidor";
    } else {
        echo "Erro na conexão com o servidor";
    }

?>
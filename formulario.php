<?php

include('conexao.php');

$tipo = mysqli_real_escape_string($connect, trim($_GET['tipo']));
$responsavel = mysqli_real_escape_string($connect, trim($_GET['responsavel']));
$lat = $_GET['input_lat'];
$lng = $_GET['input_lng'];
$descricao = mysqli_real_escape_string($connect, trim($_GET['descricao']));


$query = "INSERT INTO t_incidentes (tipo, responsavel, descricao, lat, lng, data_incidente) 
    VALUES ('{$tipo}', '{$responsavel}', '{$descricao}', {$lat}, {$lng}, NOW())";
$row_insert = mysqli_query($connect, $query);

if($row_insert) {
    $return["error"] = false;
    $return["msg"] = "Sucesso ao inserir";
    $return["id"] = mysqli_insert_id($connect);
    // esta salvando
} else {
    $return["error"] = true;
    $return["msg"] = "Erro ao inserir";
    // nao esta salvando
}
echo json_encode($return);
?>
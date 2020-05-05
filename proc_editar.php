<?php
include('conexao.php');

$id = $_GET['id'];
$tipo = $_GET['tipo'];
$lat = $_GET['input_lat'];
$lng = $_GET['input_lng'];
$responsavel = $_GET['responsavel'];
$descricao = $_GET['descricao'];



$query = "UPDATE t_incidentes SET tipo='{$tipo}', responsavel='{$responsavel}',
     descricao='{$descricao}', lat='{$lat}', lng='{$lng}' WHERE id = '{$id}'";
$update = mysqli_query($connect, $query);

if(mysqli_affected_rows($connect)>0) {
    $result['error'] = false;
    $result['msg'] = "Editado com sucesso";
} else {
    $result['error'] = true;
    $result['msg'] = "Não foi possível editar";
}

$result['tipo'] = $tipo;
$result['responsavel'] = $responsavel;
echo json_encode($result);
?>
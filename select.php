<?php
include('conexao.php');
$id = $_GET['id'];

$query_select = "SELECT * FROM t_incidentes WHERE id = '{$id}'";
$select = mysqli_query($connect, $query_select);
$row = mysqli_fetch_array($select);

if($select) {
    $data['error'] = false;
    $data['msg'] = "Dados selecionados com sucesso!";
    $data['tipo'] = $row['tipo'];
    $data['responsavel'] = $row['responsavel'];
    $data['descricao'] = $row['descricao'];
    $data['lat'] = $row['lat'];
    $data['lng'] = $row['lng'];
} else {
    $data['error'] = true;
    $data['msg'] = "Erro ao selecionar os dados!";
}

echo json_encode($data);
?>
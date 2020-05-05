<?php
include('conexao.php');

$id = $_GET['id'];

$query = "DELETE FROM t_incidentes WHERE id = '{$id}'";
$delete = mysqli_query($connect, $query);

if($delete) {
    $result['error'] = false;
    $result['msg'] = "Excluido com sucesso";
} else {
    $result['error'] = true;
    $result['msg'] = "Erro ao excluir";
}

echo json_encode($result);
?>
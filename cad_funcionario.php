<?php
include('conexao.php');

$nome = trim($_GET['nome']);
$sobrenome = trim($_GET['sobrenome']);
$telefone = trim($_GET['telefone']);
$email = trim($_GET['email']);
$data_nascimento = trim($_GET['data_nascimento']);
$sexo = trim($_GET['sexo']);

$query_select = "SELECT email FROM t_funcionarios WHERE email = '{$email}'";
$select = mysqli_query($connect, $query_select);
$array = mysqli_fetch_array($select);

if($array != null) {
    $result['error'] = true;
    $result['msg'] = "Funcionário já cadastrado em sua base de dados!";
} else {
    $query = "INSERT INTO t_funcionarios (nome, sobrenome, telefone, email, data_nascimento, sexo)
    VALUES ('{$nome}', '{$sobrenome}', '{$telefone}', '{$email}', '{$data_nascimento}', '{$sexo}')";
    $insert = mysqli_query($connect, $query);
    
    if($insert) {
    $result['error'] = false;
    $result['msg'] = "Funcionario salvo com sucesso";
    $result['id'] = mysqli_insert_id($connect);
    } else {
        $result['error'] = true;
        $result['msg'] = "Não foi possível adicionar o funcionário";
    }
}
echo json_encode($result);
?>
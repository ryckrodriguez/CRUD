<?php

include('conexao.php');

if($connect->connect_errno) {
    echo "Falhou ao conectar com o banco: " .$connect -> connect_error;
    exit();
}

$usuario = mysqli_real_escape_string($connect, trim($_POST['usuario']));
$senha = mysqli_real_escape_string($connect, trim(md5($_POST['senha'])));
$telefone = mysqli_real_escape_string($connect, trim($_POST['telefone']));
$email = mysqli_real_escape_string($connect, trim($_POST['email']));

$query_select = "SELECT usuario FROM t_usuarios WHERE usuario = '{$usuario}'";
$select = mysqli_query($connect, $query_select);

if(mysqli_affected_rows($connect)>0){
    $result['error'] = true;
    $result['msg'] = "Usuário já existe";
} else {
    $query = "INSERT INTO t_usuarios (usuario, senha, email, telefone, data_cadastro)
        VALUES ('{$usuario}', md5('{$senha}'), '{$email}','{$telefone}', NOW())";
    $insert = mysqli_query($connect, $query);

    if(mysqli_affected_rows($connect)>0) {
        $result['error'] = false;
        $result['msg'] = "Cadastrado com sucesso!";
    } else {
        $result['error'] = true;
        $result['msg'] = "Erro ao cadastrar";
    }
} 


echo json_encode($result);
?>
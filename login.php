<?php 

session_start();
include('conexao.php');

if(empty($_POST['login']) || empty($_POST['senha'])) {
  header('location: login.html');
  exit();
}

$login = mysqli_real_escape_string($connect, $_POST['login']);
$senha = mysqli_real_escape_string($connect, md5($_POST['senha']));
$query_select = "SELECT * FROM t_usuarios WHERE usuario = '{$login}' AND senha = md5('{$senha}')";
$select = mysqli_query($connect, $query_select);
$row = mysqli_num_rows($select);

if ($row == 0){
  $result['error'] = true;
  $result['msg'] = "Usuário ou senha inválidos!";
}else{
  $result['error'] = false;
  $result['msg'] = "Logado com sucesso!";
  $_SESSION['login'] = $login;
}

echo json_encode($result)
?>
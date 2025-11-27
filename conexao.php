<?php
// Conecta ao banco de dados MySQL
$host = "localhost";
$usuario = "root"; // ou outro usuário do seu MySQL
$senha = "";       // senha do MySQL
$banco = "cadastro_usuarios";

// Cria a conexão
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verifica se houve erro
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
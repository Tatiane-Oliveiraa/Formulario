<?php
// Função para validar CPF
function validarCPF($cpf) {
    // Remove tudo que não for número
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Verifica se tem 11 dígitos e não é uma sequência repetida
    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) return false;

    // Validação dos dígitos verificadores
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += $cpf[$i] * (($t + 1) - $i);
        }
        $digito = ((10 * $soma) % 11) % 10;
        if ($cpf[$t] != $digito) return false;
    }

    return true;
}

// Inicializa variáveis
$erros = [];
$nome = $email = $cpf = $cep = $endereco = $bairro = $cidade = $estado = $senha = "";

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura e limpa os dados enviados
    $nome = htmlspecialchars(trim($_POST["nome"] ?? ""));
    $email = htmlspecialchars(trim($_POST["email"] ?? ""));
    $cpf = htmlspecialchars(trim($_POST["cpf"] ?? ""));
    $cep = preg_replace('/[^0-9]/', '', $_POST["cep"] ?? "");
    $endereco = htmlspecialchars($_POST["endereco"] ?? "");
    $bairro = htmlspecialchars($_POST["bairro"] ?? "");
    $cidade = htmlspecialchars($_POST["cidade"] ?? "");
    $estado = htmlspecialchars($_POST["estado"] ?? "");
    $senhaOriginal = $_POST["senha"] ?? "";

    // Validações dos campos
    if (empty($nome)) $erros[] = "O campo nome é obrigatório.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "E-mail inválido.";
    if (empty($cpf) || !validarCPF($cpf)) $erros[] = "CPF inválido.";
    if (empty($cep)) $erros[] = "O campo CEP é obrigatório.";

    // Validação da senha
    if (empty($senhaOriginal) || strlen($senhaOriginal) < 6) {
        $erros[] = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        // Criptografa a senha com segurança
        $senha = password_hash($senhaOriginal, PASSWORD_DEFAULT);
    } 

}  
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Resultado do Cadastro</title>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f0f2f5;
        padding: 40px;
    }

    .container {
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        max-width: 600px;
        margin: auto;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #333;
    }

    .sucesso {
        color: green;
    }

    .erro {
        color: red;
        margin-bottom: 20px;
    }

    ul {
        padding-left: 20px;
    }

    p {
        margin: 8px 0;
    }

    .voltar {
        margin-top: 20px;
        display: inline-block;
        text-decoration: none;
        background: #007bff;
        color: white;
        padding: 10px 15px;
        border-radius: 4px;
    }

    .voltar:hover {
        background: #0056b3;
    }
    </style>
</head>

<body>
    <div class="container">
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <?php if (empty($erros)): ?>
        <h2 class="sucesso">✅ Cadastro realizado com sucesso!</h2>
        <p><strong>Nome:</strong> <?= $nome ?></p>
        <p><strong>Email:</strong> <?= $email ?></p>
        <p><strong>CPF:</strong> <?= $cpf ?></p>
        <p><strong>Endereço:</strong> <?= "$endereco, $bairro - $cidade/$estado" ?></p>
        <p><strong>Senha criptografada:</strong> <?= $senha ?></p>
        <?php else: ?>
        <h2 class="erro">❌ Erros encontrados:</h2>
        <ul>
            <?php foreach ($erros as $erro): ?>
            <li><?= $erro ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <a class="voltar" href="index.html">← Voltar ao formulário</a>
        <?php endif; ?>
    </div>
</body>

</html>
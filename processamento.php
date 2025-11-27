<?php
// Função para validar CPF
function validarCPF($cpf)
{
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return false;

    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += intval($cpf[$i]) * (($t + 1) - $i);
        }
        $digito = (10 * $soma) % 11;
        $digito = ($digito == 10) ? 0 : $digito;
        if (intval($cpf[$t]) !== $digito) return false;
    }
    return true;
}

// Inicializa variáveis
$erros = [];
$nome = $email = $cpf = $cep = $endereco = $bairro = $cidade = $senha = "";

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = htmlspecialchars(trim($_POST["nome"] ?? ""));
    $email = htmlspecialchars(trim($_POST["email"] ?? ""));
    $cpf = htmlspecialchars(trim($_POST["cpf"] ?? ""));
    $cep = preg_replace('/[^0-9]/', '', $_POST["cep"] ?? "");
    $endereco = htmlspecialchars($_POST["endereco"] ?? "");
    $bairro = htmlspecialchars($_POST["bairro"] ?? "");
    $cidade = htmlspecialchars($_POST["cidade"] ?? "");
    $senhaOriginal = $_POST["senha"] ?? "";

    // Validações
    if (empty($nome)) $erros[] = "O campo nome é obrigatório.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "E-mail inválido.";
    if (empty($cpf) || !validarCPF($cpf)) $erros[] = "CPF inválido.";
    if (empty($cep)) $erros[] = "O campo CEP é obrigatório.";

    // Validação da senha
    if (!empty($senhaOriginal) && strlen($senhaOriginal) >= 6) {
        $senha = password_hash($senhaOriginal, PASSWORD_DEFAULT);

        if (!$senha || strlen($senha) < 20) {
            $erros[] = "Erro ao criptografar a senha.";
        }
    } else {
        $erros[] = "A senha deve ter pelo menos 6 caracteres.";
    }



    // Se não houver erros, salva no banco
    if (empty($erros)) {
        require_once "conexao.php";

        $sqlVerifica = "SELECT id FROM usuario WHERE email = ?";
        $stmtVerifica = $conn->prepare($sqlVerifica);
        $stmtVerifica->bind_param("s", $email);
        $stmtVerifica->execute();
        $stmtVerifica->store_result();

        if ($stmtVerifica->num_rows > 0) {
            $erros[] = "Este e-mail já está cadastrado.";
        } else {
            $sqlInsere = "INSERT INTO usuario (nome, email, cpf, cep, endereco, bairro, cidade, senha)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtInsere = $conn->prepare($sqlInsere);

            if (!$stmtInsere) {
                $erros[] = "Erro na preparação da query: " . $conn->error;
            } else {
                $stmtInsere->bind_param("ssssssss", $nome, $email, $cpf, $cep, $endereco, $bairro, $cidade, $senha);

                if (!$stmtInsere->execute()) {
                    $erros[] = "Erro ao salvar no banco: " . $stmtInsere->error;
                }
            }
        }
    }
}
?>

<!-- HTML para exibir resultado -->
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
        <p><strong>Nome:</strong> <?= htmlspecialchars($nome) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
        <p><strong>CPF:</strong> <?= htmlspecialchars($cpf) ?></p>
        <p><strong>Endereço:</strong> <?= htmlspecialchars("$endereco, $bairro - $cidade") ?></p>
        <p><strong>Senha criptografada:</strong> <?= htmlspecialchars($senha) ?></p>
        <?php else: ?>
        <h2 class="erro">❌ Erros encontrados:</h2>
        <ul>
            <?php foreach ($erros as $erro): ?>
            <li><?= htmlspecialchars($erro) ?></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <a class="voltar" href="index.html">← Voltar ao formulário</a>
        <?php endif; ?>
    </div>
</body>

</html>
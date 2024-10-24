<?php
include '../conexao.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_departamento = $_POST["nome-departamento"];

    $nome_departamento = mysqli_real_escape_string($con, $nome_departamento);

    $sql = "INSERT INTO departamento (nome) VALUES ('$nome_departamento')";

    if (mysqli_query($con, $sql)) {
        $message = "Departamento cadastrado com sucesso!";
    } else {
        $message = "Erro ao cadastrar: " . mysqli_error($con);
    }

    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Departamento</title>
    <link rel="stylesheet" href="../styles/invalidar.css">
<body>

<div id="section-departamento-cadastrar" class="content-section">
    <h2>Cadastrar Departamento</h2>
    <p>+ Cadastre um novo departamento</p>
    <div class="form-content">

        <!-- Exibir mensagem de sucesso ou erro -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, 'Erro') !== false) ? 'error' : ''; ?>">
                <?php echo $message; ?>
                <button class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group-patrimonio">
                <label for="nome-departamento">Nome do Departamento</label>
                <input
                    type="text"
                    id="nome-departamento"
                    name="nome-departamento"
                    placeholder="Nome do departamento"
                    required
                />
            </div>
            <button type="submit" class="btn-patrimonio">Cadastrar</button>
        </form>

    </div>
</div>

</body>
</html>

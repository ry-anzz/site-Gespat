<?php
include '../conexao.php';

$message = '';
$departamentos = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["acao"]) && $_POST["acao"] === "transferir") {
    $codigo = $_POST["codigo-transferir"];
    $departamento = $_POST["departamento"];

    $codigo = mysqli_real_escape_string($con, $codigo);
    $departamento = mysqli_real_escape_string($con, $departamento);

    $sql = "UPDATE patrimonio SET fk_departamento_nome = '$departamento' WHERE codigo = '$codigo'";

    if (mysqli_query($con, $sql)) {
        $message = "Transferência realizada com sucesso!";
    } else {
        $message = "Erro ao transferir: " . mysqli_error($con);
    }
}

$sql = "SELECT * FROM departamento";
$result = mysqli_query($con, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $departamentos = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transferir Patrimônio</title>
    <link rel="stylesheet" href="../styles/transferir.css">
</head>
<body>

<div class="main-content-patrimonio">
    <!-- Seção de transferência de patrimônio -->
    <div id="section-transferir" class="content-section">
        <h2>Transferir</h2>
        <p>+ Transfira um patrimônio entre departamentos</p>
        <div class="form-content">
            <?php if (!empty($message)): ?>
                <div class="message <?php echo (strpos($message, 'Erro') !== false) ? 'error' : ''; ?>">
                    <?php echo $message; ?>
                    <button class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group-patrimonio">
                    <label for="codigo-transferir">Código</label>
                    <input type="text" id="codigo-transferir" name="codigo-transferir" placeholder="Código do patrimônio" required>
                </div>
                <div class="form-group-patrimonio">
                    <label for="departamento">Departamento</label>
                    <input id="departamento" name="departamento" placeholder="Nome do Departamento" required></input>
                </div>
                <button type="submit" class="btn-patrimonio">Transferir</button>
                <input type="hidden" name="acao" value="transferir">
            </form>
        </div>
    </div>
</div>

</body>
</html>

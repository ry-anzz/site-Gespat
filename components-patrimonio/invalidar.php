<?php
include '../conexao.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST["codigo"];
    
    $codigo = mysqli_real_escape_string($con, $codigo);

    
    if (!empty($codigo)) {
        $sql = "INSERT INTO invalidos (fk_cod_patrimonio) VALUES ('$codigo')";
        
        if (mysqli_query($con, $sql)) {
            $message = "Patrimônio invalidado com sucesso!";
        } else {
            $message = "Erro ao invalidar: " . mysqli_error($con) . " | SQL: " . $sql;
        }
    } else {
        $message = "O campo código não pode estar vazio.";
    }

    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalidar patrimônio</title>
    <link rel="stylesheet" href="../styles/invalidar.css">
</head>
<body>

<div class="main-content-patrimonio">
    <!-- Invalidar Patrimônio -->
    <div id="section-invalidar" class="content-section">
        <h2>Invalidar</h2>
        <p>+ Invalide um patrimônio existente</p>
        <div class="form-content">

            <?php if (!empty($message)): ?>
                <div class="message <?php echo (strpos($message, 'Erro') !== false) ? 'error' : ''; ?>">
                    <?php echo $message; ?>
                    <button class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group-patrimonio">
                    <label for="codigo">Código</label>
                    <input type="text" id="codigo" name="codigo" placeholder="Código do patrimônio" required>
                </div>
                <button type="submit" class="btn-patrimonio">Invalidar</button>
            </form>

        </div>
    </div>
</div>

</body>
</html>

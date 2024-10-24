<?php
include '../conexao.php';

$departamentos = [];
$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_departamento = $_POST["nome-departamento-consultar"];

    $nome_departamento = mysqli_real_escape_string($con, $nome_departamento);

    if (!empty($nome_departamento)) {
        $sql = "SELECT * FROM departamento WHERE nome LIKE '%$nome_departamento%'";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
            $departamentos = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $erro = "Nenhum departamento encontrado.";
        }
    } else {
        $erro = "Por favor, insira o nome de um departamento para consultar.";
    }

    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Consultar Departamento</title>
    <link rel="stylesheet" href="../styles/relatory.css" />
</head>
<body>

<div id="section-departamento-consultar" class="content-section">
    <h2>Consultar Departamento</h2>
    <p>+ Consulte um departamento existente</p>
    <div class="form-content">

        <!-- Exibir mensagem de erro, se houver -->
        <?php if ($erro): ?>
            <div class="message error">
                <?php echo $erro; ?>
                <button class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="form-group-patrimonio">
            <label for="nome-departamento-consultar">Nome do Departamento</label>
            <input
              type="text"
              id="nome-departamento-consultar"
              name="nome-departamento-consultar"
              placeholder="Nome do departamento"
              required
            />
          </div>
          <button type="submit" class="btn-patrimonio">Consultar</button>
        </form>

        <!-- Exibir resultados da consulta -->
        <?php if (!empty($departamentos)): ?>
            <table class="result-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departamentos as $departamento): ?>
                        <tr>
                            <td><?php echo $departamento['nome']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</div>

</body>
</html>

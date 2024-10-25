<?php
include '../conexao.php'; 

$departamentos = null; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "
        SELECT d.nome, 
               COUNT(p.codigo) AS total_patrimonios,
               COALESCE((SELECT COUNT(*) FROM invalidos i WHERE i.fk_cod_patrimonio IN (SELECT p2.codigo FROM patrimonio p2 WHERE p2.fk_departamento_nome = d.nome)), 0) AS total_invalidos,
               COUNT(p.codigo) - COALESCE((SELECT COUNT(*) FROM invalidos i WHERE i.fk_cod_patrimonio IN (SELECT p2.codigo FROM patrimonio p2 WHERE p2.fk_departamento_nome = d.nome)), 0) AS total_ativos
        FROM departamento d
        LEFT JOIN patrimonio p ON d.nome = p.fk_departamento_nome
        GROUP BY d.nome
    ";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        $departamentos = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $message = "Nenhum departamento cadastrado foi encontrado.";
    }
}
$con->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Relatório Departamento</title>
    <link rel="stylesheet" href="../styles/relatory.css" />
</head>
<body>
    <div id="section-departamento-relatorio" class="content-section">
        <h2>Gerar Relatório Departamento</h2>
        <p>+ Gere relatórios de departamentos cadastrados</p>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, 'Erro') !== false) ? 'error' : 'success'; ?>">
                <?php echo $message; ?>
                <button class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Formulário para gerar relatório -->
        <div class="form-content">
            <form method="POST" action="">
                <div class="form-group-patrimonio">
                    <label for="tipo-relatorio-departamento">Tipo de Relatório</label>
                    <select id="tipo-relatorio-departamento" name="tipo-relatorio-departamento">
                        <option value="todos">Todos os Departamentos</option>
                    </select>
                </div>
                <button type="submit" class="btn-patrimonio">Gerar Relatório</button>
            </form>
        </div>

        <!-- Exibir a tabela de departamentos cadastrados se houver dados -->
        <?php if ($departamentos): ?>
            <div class="table-content">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Total de Patrimônios</th>
                            <th>Total Inválidos</th>
                            <th>Total Ativos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departamentos as $departamento): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($departamento['nome']); ?></td>
                                <td><?php echo $departamento['total_patrimonios']; ?></td>
                                <td><?php echo $departamento['total_invalidos']; ?></td>
                                <td><?php echo $departamento['total_ativos']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>

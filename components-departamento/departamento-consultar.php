<?php
include '../conexao.php'; 

$departamentos = [];
$patrimonios = [];
$erro = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_departamento = $_POST["nome-departamento-consultar"];
    $nome_departamento = mysqli_real_escape_string($con, $nome_departamento);

    if (!empty($nome_departamento)) {
        // Consulta para obter as informações do departamento
        $sql_departamento = "
            SELECT d.nome, 
                   COUNT(p.codigo) AS total_patrimonios,
                   COALESCE((SELECT COUNT(*) FROM invalidos i WHERE i.fk_cod_patrimonio IN (SELECT p2.codigo FROM patrimonio p2 WHERE p2.fk_departamento_nome = d.nome)), 0) AS total_invalidos,
                   COUNT(p.codigo) - COALESCE((SELECT COUNT(*) FROM invalidos i WHERE i.fk_cod_patrimonio IN (SELECT p2.codigo FROM patrimonio p2 WHERE p2.fk_departamento_nome = d.nome)), 0) AS total_ativos
            FROM departamento d
            LEFT JOIN patrimonio p ON d.nome = p.fk_departamento_nome
            WHERE d.nome LIKE '%$nome_departamento%'
            GROUP BY d.nome
        ";

        $result_departamento = mysqli_query($con, $sql_departamento);

        if (mysqli_num_rows($result_departamento) > 0) {
            $departamentos = mysqli_fetch_all($result_departamento, MYSQLI_ASSOC);

            // Consulta para obter todos os patrimônios do departamento, incluindo a imagem
            $sql_patrimonios = "
                SELECT p.*, 
                       (SELECT COUNT(*) FROM invalidos i WHERE i.fk_cod_patrimonio = p.codigo) AS is_invalid
                FROM patrimonio p
                WHERE p.fk_departamento_nome LIKE '%$nome_departamento%'
            ";

            $result_patrimonios = mysqli_query($con, $sql_patrimonios);
            if (mysqli_num_rows($result_patrimonios) > 0) {
                $patrimonios = mysqli_fetch_all($result_patrimonios, MYSQLI_ASSOC);
            }
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

        <!-- Exibir resultados da consulta do departamento -->
        <?php if (!empty($departamentos)): ?>
            <table class="result-table">
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
        <?php endif; ?>

        <!-- Exibir resultados dos patrimônios do departamento -->
        <?php if (!empty($patrimonios)): ?>
            <h3>Patrimônios do Departamento</h3>
            <table class="result-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Fabricante</th>
                        <th>Cor</th>
                        <th>Número de Série</th>
                        <th>Descrição</th>
                        <th>Status</th>
                        <th>Imagem</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patrimonios as $patrimonio): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patrimonio['codigo']); ?></td>
                            <td><?php echo htmlspecialchars($patrimonio['fabricante']); ?></td>
                            <td><?php echo htmlspecialchars($patrimonio['cor']); ?></td>
                            <td><?php echo htmlspecialchars($patrimonio['n_serie']); ?></td>
                            <td><?php echo htmlspecialchars($patrimonio['descricao']); ?></td>
                            <td><?php echo $patrimonio['is_invalid'] > 0 ? 'Inválido' : 'Ativo'; ?></td>
                            <td>
                                <?php if (!empty($patrimonio['arquivoimg'])): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($patrimonio['arquivoimg']); ?>" alt="Imagem do Patrimônio" style="width: 100px; height: auto;" />
                                <?php else: ?>
                                    <span>Sem imagem</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</div>

</body>
</html>

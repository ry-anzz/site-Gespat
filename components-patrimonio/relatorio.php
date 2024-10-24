<?php
include '../conexao.php'; 

$patrimonios = null;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_relatorio = $_POST['tipo-relatorio'] ?? 'todos'; // Captura a seleção

    // Cria a consulta de acordo com a seleção
    if ($tipo_relatorio === 'todos') {
        $sql = "SELECT * FROM patrimonio";
    } elseif ($tipo_relatorio === 'invalidos') {
        $sql = "SELECT p.* FROM patrimonio p
                JOIN invalidos i ON p.codigo = i.fk_cod_patrimonio";
    } elseif ($tipo_relatorio === 'ativos') {
        $sql = "SELECT p.* FROM patrimonio p
                LEFT JOIN invalidos i ON p.codigo = i.fk_cod_patrimonio
                WHERE i.fk_cod_patrimonio IS NULL";
    }

    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        $patrimonios = $result->fetch_all(MYSQLI_ASSOC);

        // Verifica se cada patrimônio está na tabela invalidos
        foreach ($patrimonios as $key => $patrimonio) {
            $codigo = $patrimonio['codigo'];
            $sql_invalidos = "SELECT * FROM invalidos WHERE fk_cod_patrimonio = ?";
            $stmt = $con->prepare($sql_invalidos);
            $stmt->bind_param("i", $codigo);
            $stmt->execute();
            $result_invalidos = $stmt->get_result();

            // Adiciona um campo de status para indicar se está inválido
            $patrimonios[$key]['status'] = $result_invalidos->num_rows > 0 ? 'Inválido' : 'Ativo';

            $stmt->close();
        }
    } else {
        $message = "Nenhum patrimônio cadastrado foi encontrado.";
    }
}
$con->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gerar Relatório</title>
    <link rel="stylesheet" href="../styles/relatory.css" />
</head>
<body>
    <div id="section-relatorio" class="content-section">
        <h2>Gerar relatório</h2>
        <p>+ Gere relatórios de patrimônios cadastrados</p>

        <!-- Exibir a mensagem de erro ou sucesso -->
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
                    <label for="tipo-relatorio">Relatório</label>
                    <select id="tipo-relatorio" name="tipo-relatorio">
                        <option value="todos">Todos os Patrimônios</option>
                        <option value="invalidos">Inválidos</option>
                        <option value="ativos">Ativos</option>
                        <!-- Outras opções de filtros podem ser adicionadas aqui -->
                    </select>
                </div>
                <button type="submit" class="btn-patrimonio">Gerar Relatório</button>
            </form>
        </div>

        <!-- Exibir a tabela de patrimônios cadastrados se houver dados -->
        <?php if ($patrimonios): ?>
            <div class="table-content">
                <table border="1">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Fabricante</th>
                            <th>Cor</th>
                            <th>Número de Série</th>
                            <th>Descrição</th>
                            <th>Departamento</th>
                            <th>Status</th> <!-- Nova coluna para o status -->
                            <th>Imagem</th> <!-- Adicionando coluna para a imagem -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patrimonios as $patrimonio): ?>
                            <tr>
                                <td><?php echo $patrimonio['codigo']; ?></td>
                                <td><?php echo $patrimonio['fabricante']; ?></td>
                                <td><?php echo $patrimonio['cor']; ?></td>
                                <td><?php echo $patrimonio['n_serie']; ?></td>
                                <td><?php echo $patrimonio['descricao']; ?></td>
                                <td><?php echo $patrimonio['fk_departamento_nome']; ?></td>
                                <td><?php echo $patrimonio['status']; ?></td>
                                <td>
                                    <?php if (!empty($patrimonio['arquivoimg'])): ?>
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($patrimonio['arquivoimg']); ?>" alt="Imagem do Patrimônio" style="width: 300px; height: auto;" />
                                    <?php else: ?>
                                        <span>Sem imagem</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

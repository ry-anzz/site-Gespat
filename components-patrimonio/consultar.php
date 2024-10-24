<?php
include '../conexao.php'; 

$patrimonio = null;
$erro = null;
$message = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo-consulta'])) {
    if (!empty($_POST['codigo-consulta'])) {
        $codigo = $_POST['codigo-consulta'];

        if ($con) {
            $sql = "SELECT * FROM patrimonio WHERE codigo = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $codigo);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $patrimonio = $result->fetch_assoc();
            } else {
                $erro = "Patrimônio não encontrado.";
            }
        } else {
            $erro = "Erro de conexão com o banco de dados.";
        }
    } else {
        $erro = "Por favor, insira um código válido.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    $codigo = $_POST["codigo"];
    $fabricante = $_POST['fabricante'];
    $cor = $_POST['cor'];
    $numero_serie = $_POST['numero-serie'];
    $descricao = $_POST['descricao'];
    $departamento = $_POST['departamento'];
    
    // Lógica de imagem
    if (isset($_FILES['imagem']['tmp_name']) && $_FILES['imagem']['tmp_name'] != '') {
        $imagem = file_get_contents($_FILES['imagem']['tmp_name']); // Captura o conteúdo da imagem
        $sql = "UPDATE patrimonio SET fabricante = ?, cor = ?, n_serie = ?, descricao = ?, fk_departamento_nome = ?, arquivoimg = ? WHERE codigo = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sssssss", $fabricante, $cor, $numero_serie, $descricao, $departamento, $imagem, $codigo);
    } else {
        // Atualizar sem alterar a imagem
        $sql = "UPDATE patrimonio SET fabricante = ?, cor = ?, n_serie = ?, descricao = ?, fk_departamento_nome = ? WHERE codigo = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssss", $fabricante, $cor, $numero_serie, $descricao, $departamento, $codigo);
    }

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $message = "Patrimônio alterado com sucesso!";
            $patrimonio = null; 
        } else {
            $message = "Nenhuma alteração feita no patrimônio.";
        }
    } else {
        $message = "Erro na atualização: " . $stmt->error;
    }
    $stmt->close();
}

if (isset($con)) {
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../styles/consultar.css" />
    <title>Consultar/Alterar</title>
</head>
<body>
    <div id="section-consultar" class="content-section">
        <h2>Consultar / Alterar</h2>
        <p>+ Consulte ou altere um patrimônio existente</p>

        <!-- Mensagem de sucesso ou erro -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, 'Erro') !== false) ? 'error' : 'success'; ?>">
                <?php echo $message; ?>
                <button class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        <?php endif; ?>
        
        <?php if ($erro): ?>
            <div class="message error">
                <?php echo $erro; ?>
                <button class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Formulário para consulta -->
        <div class="form-content">
            <form method="POST" action="">
                <div class="form-group-patrimonio">
                    <label for="codigo-consulta">Código</label>
                    <input
                        type="number"
                        id="codigo-consulta"
                        name="codigo-consulta"
                        placeholder="Código do patrimônio"
                        required
                    />
                </div>
                <button type="submit" class="btn-patrimonio">Consultar</button>
            </form>
        </div>

        <?php if ($patrimonio): ?>
            <!-- Exibir os campos preenchidos com os dados do patrimônio -->
            <div class="form-content">
                <h3 class="result-title">Dados do Patrimônio Encontrado</h3>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="codigo" value="<?php echo $patrimonio['codigo']; ?>">
                    <div class="form-group-patrimonio">
                        <label for="fabricante">Fabricante</label>
                        <input type="text" id="fabricante" name="fabricante" value="<?php echo $patrimonio['fabricante']; ?>">
                    </div>
                    <div class="form-group-patrimonio">
                        <label for="cor">Cor</label>
                        <input type="text" id="cor" name="cor" value="<?php echo $patrimonio['cor']; ?>">
                    </div>
                    <div class="form-group-patrimonio">
                        <label for="numero-serie">Número de série</label>
                        <input type="text" id="numero-serie" name="numero-serie" value="<?php echo $patrimonio['n_serie']; ?>">
                    </div>
                    <div class="form-group-patrimonio">
                        <label for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao"><?php echo $patrimonio['descricao']; ?></textarea>
                    </div>
                    <div class="form-group-patrimonio">
                        <label for="departamento">Departamento</label>
                        <input type="text" id="departamento" name="departamento" value="<?php echo $patrimonio['fk_departamento_nome']; ?>">
                    </div>

                    <!-- Exibir imagem se houver -->
                    <?php if (!empty($patrimonio['arquivoimg'])): ?>
                        <div class="image-placeholder-patrimonio" onclick="document.getElementById('imagem').click();" style="position: relative;">
                            <img id="image-preview" src="data:image/jpeg;base64,<?php echo base64_encode($patrimonio['arquivoimg']); ?>" alt="Prévia da imagem" style="width: 100%; height: 100%; object-fit: cover;" />
                            <span id="remove-image" style="position: absolute; top: 5px; right: 5px; display: block; cursor: pointer; background: rgba(255, 255, 255, 0.7); border-radius: 50%; padding: 5px;">&times;</span>
                            <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImage(event)" style="display: none;">
                        </div>
                    <?php else: ?>
                        <div class="image-placeholder-patrimonio" onclick="document.getElementById('imagem').click();" style="position: relative;">
                            <img id="image-preview" src="" alt="Prévia da imagem" style="display: none; width: 100%; height: 100%; object-fit: cover;" />
                            <span id="upload-message">Carregar Imagem</span>
                            <span id="remove-image" style="display: none; cursor: pointer; background: rgba(255, 255, 255, 0.7); border-radius: 50%; padding: 5px;">&times;</span>
                            <input type="file" id="imagem" name="imagem" accept="image/*" onchange="previewImage(event)" style="display: none;">
                        </div>
                    <?php endif; ?>

                    <script>
                    function previewImage(event) {
                        const imagePreview = document.getElementById('image-preview');
                        const file = event.target.files[0];
                        const uploadMessage = document.getElementById('upload-message');
                        const removeImage = document.getElementById('remove-image');

                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                imagePreview.src = e.target.result;
                                imagePreview.style.display = 'block'; // Exibe a imagem
                                uploadMessage.style.display = 'none'; // Esconde o texto
                                removeImage.style.display = 'block'; // Exibe o "X" para remover a imagem
                            }
                            reader.readAsDataURL(file);
                        }
                    }

                    document.getElementById('remove-image').onclick = function() {
                        const imagePreview = document.getElementById('image-preview');
                        const uploadMessage = document.getElementById('upload-message');
                        const removeImage = document.getElementById('remove-image');

                        imagePreview.src = '';
                        imagePreview.style.display = 'none'; // Esconde a imagem
                        uploadMessage.style.display = 'block'; // Exibe o texto de upload
                        removeImage.style.display = 'none'; // Esconde o "X" para remover a imagem
                        document.getElementById('imagem').value = ''; // Limpa o campo de entrada de arquivo
                    };
                    </script>

                    <button type="submit" class="btn-patrimonio-alterar">Alterar</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
include '../conexao.php';

$message = '';
$departamentos = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $codigo = $_POST["codigo"];
    $fabricante = $_POST["fabricante"];
    $cor = $_POST["cor"];
    $numero_serie = $_POST["numero-serie"];
    $descricao = $_POST["descricao"];
    $departamento = $_POST["departamento"];

    $codigo = mysqli_real_escape_string($con, $codigo);
    $fabricante = mysqli_real_escape_string($con, $fabricante);
    $cor = mysqli_real_escape_string($con, $cor);
    $numero_serie = mysqli_real_escape_string($con, $numero_serie);
    $descricao = mysqli_real_escape_string($con, $descricao);
    $departamento = mysqli_real_escape_string($con, $departamento);

    $verificaSql = "SELECT * FROM patrimonio WHERE codigo = '$codigo'";
    $resultado = mysqli_query($con, $verificaSql);

    if (mysqli_num_rows($resultado) > 0) {
        $message = "Erro: O código '$codigo' já está cadastrado!";
    } else {
        $imagem = $_FILES['imagem'];
        $imagemBinaria = null;

        if ($imagem && $imagem['tmp_name']) {
            $imagemBinaria = file_get_contents($imagem['tmp_name']);
            $imagemBinaria = mysqli_real_escape_string($con, $imagemBinaria);
        }

        $sql = "INSERT INTO patrimonio (codigo, fabricante, cor, n_serie, descricao, fk_departamento_nome, arquivoimg) 
                VALUES ('$codigo', '$fabricante', '$cor', '$numero_serie', '$descricao', '$departamento', '$imagemBinaria')";

        if (mysqli_query($con, $sql)) {
            $message = "Cadastro realizado com sucesso!";
        } else {
            $message = "Erro ao cadastrar: " . mysqli_error($con) . " | SQL: " . $sql;
        }
    }

    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar patrimônio</title>
    <link rel="stylesheet" href="../styles/cadastrar.css">
</head>
<body>

<div class="main-content-patrimonio">
    <!-- Cadastrar Patrimônio -->
    <div id="section-cadastrar" class="content-section">
        <h2>Cadastrar</h2>
        <p>+ Cadastre um novo patrimônio</p>
        <div class="form-content">

            <?php if (!empty($message)): ?>
                <div class="message <?php echo (strpos($message, 'Erro') !== false) ? 'error' : ''; ?>">
                    <?php echo $message; ?>
                    <button class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
                </div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group-patrimonio">
                    <label for="codigo">Código</label>
                    <input type="number" id="codigo" name="codigo" placeholder="Código do patrimônio" required>
                </div>
                <div class="form-group-patrimonio">
                    <label for="fabricante">Fabricante</label>
                    <input type="text" id="fabricante" name="fabricante" placeholder="Marca fabricante" required>
                </div>
                <div class="form-group-patrimonio">
                    <label for="cor">Cor</label>
                    <input type="text" id="cor" name="cor" placeholder="Cor do patrimônio" required>
                </div>
                <div class="form-group-patrimonio">
                    <label for="numero-serie">Número de série</label>
                    <input type="text" id="numero-serie" name="numero-serie" placeholder="Número" required>
                </div>
                <div class="form-group-patrimonio">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" placeholder="Descrição do patrimônio" required></textarea>
                </div>
                <div class="form-group-patrimonio">
                    <label for="departamento">Departamento</label>
                    <input id="departamento" name="departamento" placeholder="Nome do Departamento" required>
                </div>
                <div class="image-placeholder-patrimonio" onclick="document.getElementById('imagem').click();">
                    <img id="image-preview" src="" alt="Prévia da imagem" style="display: none; width: 100%; height: 100%; object-fit: cover;" />
                    <span id="upload-message">Carregar Imagem</span>
                    <span id="remove-image" style="display: none;">&times;</span>
                    <input type="file" id="imagem" name="imagem" accept="image/*" required onchange="previewImage(event)" style="display: none;">
                </div>
                <button type="submit" class="btn-patrimonio">Cadastrar</button>
            </form>

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
                    const fileInput = document.getElementById('imagem');

                    imagePreview.src = "";
                    imagePreview.style.display = 'none'; // Esconde a imagem
                    uploadMessage.style.display = 'block'; // Exibe o texto
                    removeImage.style.display = 'none'; // Esconde o "X"
                    fileInput.value = ""; // Limpa o input de arquivo
                    fileInput.style.display = 'none'; // Mantém o input de arquivo escondido
                };
            </script>
        </div>
    </div>
</div>

</body>
</html>

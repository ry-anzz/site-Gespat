<?php
$host  = "localhost:3306";
$user  = "root";
$pass  = "";
$base  = "bdgespat";
$con   = mysqli_connect($host, $user, $pass, $base);

// Verificando se há erros na conexão
if ($con->connect_error) {
    die("Conexão falhou: " . $con->connect_error);
}
?>
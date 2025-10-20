<?php
    include './dbMedsuam.php';
    session_start();

    $user_id = $_SESSION['id'];
    $sql1 = "DELETE FROM telefone WHERE paciente_id_paciente = $user_id";
    mysqli_query($conn, $sql1);


    $sql2 = "DELETE FROM endereco WHERE paciente_id_paciente = $user_id";
    mysqli_query($conn, $sql2);

    $sql4 = "DELETE FROM rg WHERE paciente_id_paciente = $user_id";
    mysqli_query($conn, $sql4);
    
    $sql3 = "DELETE FROM paciente WHERE id_paciente = $user_id";
    mysqli_query($conn, $sql3);

    
    $_SESSION = [];
    session_destroy();
    header('location: cadastroCliente.php');
    exit;

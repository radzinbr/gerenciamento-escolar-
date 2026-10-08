<?php

require_once __DIR__ . '/../../config/config.php';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Sistema de gerenciamento escolar"
    >

    <title><?= APP_NAME ?></title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <!-- CSS do sistema -->
    <link
    rel="stylesheet"
    href="<?= BASE_URL ?>/assets/css/style.css?v=<?= time() ?>"
>
   <!-- versao final do css 
    
    <link
    rel="stylesheet"
    href="<?= BASE_URL ?>/assets/css/style.css?v=1.0.1"
>
   
   
   -->


</head>

<body>
<?php

require_once __DIR__ . '/../config/config.php';

$currentPage = "inicio";

include __DIR__ . '/../views/layouts/sidebar.php';
include __DIR__ . '/../views/layouts/header.php';

?>

<main class="content">

    <div class="container-fluid">

        <h1>Bem-vindo ao Sistema Escolar</h1>

        <p class="text-muted">
            Sistema carregado com sucesso.
        </p>

    </div>

</main>

<?php
    include __DIR__ . '/../views/layouts/footer.php';
?>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

<script
    src="<?= BASE_URL ?>/assets/js/app.js">
</script>

</body>
</html>

<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Valida o ID recebido pela URL ou pelo formulário.
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id <= 0) {
        header('Location: ' . BASE_URL . '/alunos/index.php?erro=id_invalido');
        exit;
    }

    try {
        $database = new Database();
        $pdo = $database->connect();

         $stmt = $pdo->prepare(
            'UPDATE alunos
            SET excluido_em = CURRENT_TIMESTAMP
            WHERE id = :id
            AND excluido_em IS NULL'
        );
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            header('Location: ' . BASE_URL . '/alunos/index.php?sucesso=excluido');
        } else {
            header('Location: ' . BASE_URL . '/alunos/index.php?erro=nao_encontrado');
        }

        exit;

    } catch (PDOException $e) {
        // O banco pode impedir a exclusão se houver registros relacionados.
        header('Location: ' . BASE_URL . '/alunos/index.php?erro=possui_vinculos');
        exit;

    } catch (Exception $e) {
        header('Location: ' . BASE_URL . '/alunos/index.php?erro=banco');
        exit;
    }
}

// O GET serve apenas para mostrar a confirmação.
if (!$id || $id <= 0) {
    header('Location: ' . BASE_URL . '/alunos/index.php?erro=id_invalido');
    exit;
}

try {
    $database = new Database();
    $pdo = $database->connect();

    $stmt = $pdo->prepare(
        'SELECT id, nome_completo, matricula
         FROM alunos
         WHERE id = :id'
    );

    $stmt->execute(['id' => $id]);
    $aluno = $stmt->fetch();

    if (!$aluno) {
        header('Location: ' . BASE_URL . '/alunos/index.php?erro=nao_encontrado');
        exit;
    }

} catch (PDOException $e) {
    header(
        'Location: ' . BASE_URL .
        '/alunos/index.php?erro=possui_vinculos'
    );
    exit;
}

$currentPage = 'alunos';

require_once __DIR__ . '/../../views/layouts/header.php';
require_once __DIR__ . '/../../views/layouts/sidebar.php';

?>

<main class="content">
    <div class="container-fluid">

        <div class="page-header mb-4">
            <div>
                <h1 class="h3">Excluir aluno</h1>
                <p class="text-secondary mb-0">
                    Confirme a exclusão do cadastro selecionado.
                </p>
            </div>
        </div>

        <div class="dashboard-panel">
                <div class="p-4">

                <div class="d-flex align-items-start gap-3 mb-4">
                    <i class="bi bi-exclamation-triangle text-danger fs-2"></i>

                    <div>
                        <h2 class="h5">Tem certeza?</h2>

                        <p class="text-secondary mb-3">
                            Você está prestes a excluir este aluno:
                        </p>

                        <p class="mb-1">
                            <strong>Nome:</strong>
                            <?= htmlspecialchars(
                                $aluno['nome_completo'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>

                        <p class="mb-0">
                            <strong>Matrícula:</strong>
                            <?= htmlspecialchars(
                                $aluno['matricula'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    </div>
                </div>

                <div class="alert alert-warning" role="alert">
                    Essa operação pode ser impedida caso existam
                    matrículas ou outros registros vinculados ao aluno.
                </div>

                <form method="POST"
                      action="<?= BASE_URL ?>/alunos/excluir.php">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $aluno['id'] ?>"
                    >

                    <div class="d-flex gap-2 justify-content-end">
                        <a
                            href="<?= BASE_URL ?>/alunos/index.php"
                            class="btn btn-outline-secondary"
                        >
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>
                            Confirmar exclusão
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>

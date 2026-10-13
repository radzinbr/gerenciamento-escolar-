
<?php

session_start();

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$currentPage = 'professores';

function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id || $id < 1) {
    header('Location: ' . BASE_URL . '/professores/index.php?erro=id_invalido');
    exit;
}

try {
    $database = new Database();
    $pdo = $database->connect();

    $stmt = $pdo->prepare(
        'SELECT id, nome_completo, cpf, especialidade, status
         FROM professores
         WHERE id = :id'
    );

    $stmt->execute(['id' => $id]);
    $professor = $stmt->fetch();

    if (!$professor) {
        header('Location: ' . BASE_URL . '/professores/index.php?erro=nao_encontrado');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = (string) ($_POST['csrf_token'] ?? '');

        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            die('Sessão inválida. Atualize a página e tente novamente.');
        }

        $stmt = $pdo->prepare(
            'DELETE FROM professores WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);

        header('Location: ' . BASE_URL . '/professores/index.php?sucesso=excluido');
        exit;
    }
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        $erro = 'Não é possível excluir este professor porque ele possui vínculos com outras informações do sistema. Considere alterar seu status para inativo.';
    } else {
        $erro = 'Não foi possível realizar a operação no banco de dados.';
    }
} catch (Exception $e) {
    $erro = 'Não foi possível conectar ao banco de dados.';
}
require_once __DIR__ . '/../../views/layouts/sidebar.php';
require_once __DIR__ . '/../../views/layouts/header.php';
?>

<div class="content">
    <div class="page-header">
        <div>
            <h1>Excluir professor</h1>
            <p>Confirme a exclusão do cadastro selecionado.</p>
        </div>

        <a
            href="<?= BASE_URL ?>/professores/index.php"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left"></i>
            Voltar
        </a>
    </div>

    <?php if (isset($erro)): ?>
        <div class="alert alert-danger" role="alert">
            <?= e($erro) ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-panel">
        <div class="p-4">
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                Tem certeza de que deseja excluir este professor?
                Esta ação não poderá ser desfeita.
            </div>

            <p>
                <strong>Nome:</strong>
                <?= e($professor['nome_completo']) ?>
            </p>

            <p>
                <strong>CPF:</strong>
                <?= e($professor['cpf'] ?: 'Não informado') ?>
            </p>

            <p>
                <strong>Especialidade:</strong>
                <?= e($professor['especialidade'] ?: 'Não informada') ?>
            </p>

            <p>
                <strong>Status:</strong>
                <?= e(ucfirst($professor['status'])) ?>
            </p>

            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= (int) $id ?>">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_token']) ?>"
                >

                <div class="form-actions mt-4">
                    <a
                        href="<?= BASE_URL ?>/professores/index.php"
                        class="btn btn-outline-secondary"
                    >
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i>
                        Confirmar exclusão
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>
```
```php
<?php

session_start();

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

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

$turma = null;
$erro = '';
$pdo = null;

if (!$id || $id < 1) {
    $erro = 'O identificador da turma é inválido.';
} else {
    try {
        $database = new Database();
        $pdo = $database->connect();

        $stmt = $pdo->prepare(
            "SELECT id, nome, ano_letivo, serie, turno, sala
             FROM turmas
             WHERE id = :id
             LIMIT 1"
        );

        $stmt->execute(['id' => $id]);
        $turma = $stmt->fetch();

        if (!$turma) {
            $erro = 'A turma solicitada não foi encontrada.';
        }
    } catch (Throwable $e) {
        $erro = 'Não foi possível carregar os dados da turma.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $turma && $pdo) {
    $token = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $erro = 'Sessão inválida. Atualize a página e tente novamente.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "DELETE FROM turmas WHERE id = :id"
            );

            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() === 0) {
                $erro = 'A turma já foi removida ou não foi encontrada.';
            } else {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                header(
                    'Location: ' . BASE_URL .
                    '/turmas/index.php?sucesso=excluida'
                );
                exit;
            }
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erro = 'Não é possível excluir esta turma porque existem matrículas ou outros registros vinculados a ela. Você pode editar a turma e alterar seu status para Inativa.';
            } else {
                $erro = 'Ocorreu um erro ao tentar excluir a turma.';
            }
        }
    }
}

require_once __DIR__ . '/../../views/layouts/header.php';
require_once __DIR__ . '/../../views/layouts/sidebar.php';
?>

<main class="content">

    <div class="page-header mb-4">
        <h1 class="h3 mb-1">Excluir turma</h1>
        <p class="text-secondary mb-0">
            Confirme se deseja realmente excluir esta turma.
        </p>
    </div>

    <?php if ($erro !== ''): ?>
        <div class="alert alert-danger" role="alert">
            <?= e($erro) ?>
        </div>
    <?php endif; ?>

    <?php if ($turma): ?>

        <section class="dashboard-panel">

            <div class="dashboard-panel-header">
                <h2 class="h5 mb-0">Confirmar exclusão</h2>
            </div>

            <div class="p-4">

                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Esta ação tentará remover a turma do sistema. Se houver
                    registros vinculados, o banco impedirá a exclusão.
                </div>

                <dl class="row mb-4">

                    <dt class="col-sm-3">Nome</dt>
                    <dd class="col-sm-9"><?= e($turma['nome']) ?></dd>

                    <dt class="col-sm-3">Ano letivo</dt>
                    <dd class="col-sm-9"><?= e($turma['ano_letivo']) ?></dd>

                    <dt class="col-sm-3">Série</dt>
                    <dd class="col-sm-9">
                        <?= e($turma['serie'] ?: 'Não informada') ?>
                    </dd>

                    <dt class="col-sm-3">Turno</dt>
                    <dd class="col-sm-9">
                        <?php
                        $turnos = [
                            'manha' => 'Manhã',
                            'tarde' => 'Tarde',
                            'noite' => 'Noite',
                            'integral' => 'Integral'
                        ];
                        ?>
                        <?= e($turnos[$turma['turno']] ?? $turma['turno']) ?>
                    </dd>

                    <dt class="col-sm-3">Sala</dt>
                    <dd class="col-sm-9">
                        <?= e($turma['sala'] ?: 'Não informada') ?>
                    </dd>

                </dl>

                <form method="POST" action="">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $turma['id'] ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($_SESSION['csrf_token']) ?>"
                    >

                    <div class="form-actions d-flex justify-content-end gap-2">

                        <a
                            href="<?= BASE_URL ?>/turmas/index.php"
                            class="btn btn-outline-secondary"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            <i class="bi bi-trash me-1"></i>
                            Confirmar exclusão
                        </button>

                    </div>

                </form>

            </div>
        </section>

    <?php else: ?>

        <a
            href="<?= BASE_URL ?>/turmas/index.php"
            class="btn btn-outline-secondary"
        >
            Voltar para turmas
        </a>

    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>
<td class="text-end">
    <a
        href="<?= BASE_URL ?>/turmas/editar.php?id=<?= (int) $turma['id'] ?>"
        class="btn btn-sm btn-outline-primary"
        title="Editar turma"
        aria-label="Editar turma"
    >
        <i class="bi bi-pencil"></i>
    </a>

    <a
        href="<?= BASE_URL ?>/turmas/excluir.php?id=<?= (int) $turma['id'] ?>"
        class="btn btn-sm btn-outline-danger"
        title="Excluir turma"
        aria-label="Excluir turma"
    >
        <i class="bi bi-trash"></i>
    </a>
</td>
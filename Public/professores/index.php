
<?php

session_start();

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$currentPage = 'professores';
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$busca = trim($_GET['busca'] ?? '');
$status = trim($_GET['status'] ?? '');

$professores = [];
$erro = false;

try {
    $database = new Database();
    $pdo = $database->connect();

    $sql = "
        SELECT
            p.id,
            p.nome_completo,
            p.cpf,
            p.email,
            p.especialidade,
            p.telefone,
            p.status
        FROM professores p
        WHERE 1 = 1
<<<<<<< HEAD
        AND excluido_em IS NULL
=======
>>>>>>> 5248147c2fd061e17af26d41c118153a0d44fe7a
    ";

    $parametros = [];

    if ($busca !== '') {
        $sql .= "
            AND (
                p.nome_completo LIKE :busca
                OR p.cpf LIKE :busca
                OR p.email LIKE :busca
                AND excluido_em IS NULL
            )
        ";

        $parametros['busca'] = '%' . $busca . '%';
    }

    $statusPermitidos = ['ativo', 'inativo'];

    if (in_array($status, $statusPermitidos, true)) {
        $sql .= " AND p.status = :status";
        $parametros['status'] = $status;
    }

    $sql .= " ORDER BY p.nome_completo ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);

    $professores = $stmt->fetchAll();

} catch (Throwable $e) {
    $erro = true;
}

require_once __DIR__ . '/../../views/layouts/header.php';
require_once __DIR__ . '/../../views/layouts/sidebar.php';
?>

<main class="content">

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-1">Professores</h1>
            <p class="text-secondary mb-0">
                Gerencie os professores cadastrados na instituição.
            </p>
        </div>

        <a
            href="<?= BASE_URL ?>/professores/cadastrar.php"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg me-1"></i>
            Novo professor
        </a>
    </div>

    <?php if (isset($_GET['sucesso'])): ?>

        <?php if ($_GET['sucesso'] === 'cadastrado'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Professor cadastrado com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['sucesso'] === 'atualizado'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Professor atualizado com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['sucesso'] === 'excluido'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Professor excluído com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <?php if ($erro): ?>

        <div class="alert alert-danger" role="alert">
            Não foi possível carregar os professores.
            Verifique a conexão com o banco de dados e a estrutura da tabela.
        </div>

    <?php else: ?>

        <section class="dashboard-panel mt-4">

            <div class="dashboard-panel-header">
                <h2 class="h5 mb-0">Professores cadastrados</h2>

                <span class="badge text-bg-primary">
                    <?= count($professores) ?> resultado(s)
                </span>
            </div>

            <div class="p-3">

                <form method="GET" class="row g-3 mb-4">

                    <div class="col-md-7">
                        <label for="busca" class="form-label">
                            Pesquisar professor
                        </label>

                        <input
                            type="search"
                            id="busca"
                            name="busca"
                            class="form-control"
                            placeholder="Nome, CPF ou e-mail"
                            value="<?= e($busca) ?>"
                        >
                    </div>

                    <div class="col-md-3">
                        <label for="status" class="form-label">
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="form-select"
                        >
                            <option value="">Todos</option>

                            <option value="ativo"
                                <?= $status === 'ativo' ? 'selected' : '' ?>>
                                Ativo
                            </option>

                            <option value="inativo"
                                <?= $status === 'inativo' ? 'selected' : '' ?>>
                                Inativo
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            <i class="bi bi-search me-1"></i>
                            Filtrar
                        </button>
                    </div>

                    <div class="col-12">
                        <a
                            href="<?= BASE_URL ?>/professores/index.php"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            Limpar filtros
                        </a>
                    </div>

                </form>

                <?php if (empty($professores)): ?>

                    <div class="empty-state text-center py-5">
                        <i class="bi bi-person-workspace fs-1 text-secondary"></i>

                        <h3 class="h5 mt-3">
                            Nenhum professor encontrado
                        </h3>

                        <p class="text-secondary">
                            Cadastre um professor ou altere os filtros da pesquisa.
                        </p>

                        <a
                            href="<?= BASE_URL ?>/professores/cadastrar.php"
                            class="btn btn-primary"
                        >
                            Cadastrar primeiro professor
                        </a>
                    </div>

                <?php else: ?>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">

                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>E-mail</th>
                                    <th>Telefone</th>
                                    <th>Especialidade</th>
                                    <th>Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($professores as $professor): ?>

                                    <tr>
                                        <td>
                                            <strong>
                                                <?= e($professor['nome_completo']) ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?= e($professor['cpf'] ?: '—') ?>
                                        </td>

                                        <td>
                                            <?= e($professor['email'] ?: '—') ?>
                                        </td>
                                        
                                        <td>
                                            <?= e($professor['telefone'] ?: '—') ?>
                                        </td>

                                        <td>
                                            <?= e($professor['especialidade'] ?: '—') ?>
                                        </td>

                                        <td>
                                            <?php if ($professor['status'] === 'ativo'): ?>
                                                <span class="badge text-bg-success">
                                                    Ativo
                                                </span>
                                            <?php else: ?>
                                                <span class="badge text-bg-secondary">
                                                    Inativo
                                                </span>
                                            <?php endif; ?> 
                                        </td>

                                        <td class="text-end">
                                            <a
                                                href="<?= BASE_URL ?>/professores/editar.php?id=<?= (int) $professor['id'] ?>"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Editar professor"
                                                aria-label="Editar professor"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <a
                                                href="<?= BASE_URL ?>/professores/excluir.php?id=<?= (int) $professor['id'] ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Excluir professor"
                                                aria-label="Excluir professor"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>
                            </tbody>

                        </table>
                    </div>

                <?php endif; ?>

            </div>
        </section>

    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>
```
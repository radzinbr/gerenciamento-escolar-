
<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$currentPage = 'alunos';

$alunos = [];
$turmas = [];
$erro = null;

$busca = trim($_GET['busca'] ?? '');
$turmaId = filter_input(INPUT_GET, 'turma', FILTER_VALIDATE_INT);
$status = $_GET['status'] ?? '';

if (!in_array($status, ['ativo', 'inativo'], true)) {
    $status = '';
}

if (!$turmaId || $turmaId <= 0) {
    $turmaId = null;
}

try {
    $database = new Database();
    $pdo = $database->connect();

    // Carrega as turmas disponíveis para o filtro.
    $stmtTurmas = $pdo->query(
        'SELECT id, nome
         FROM turmas
         ORDER BY nome'
    );

    $turmas = $stmtTurmas->fetchAll();

    // Monta a consulta de alunos.
    $sql = "
        SELECT
            a.id,
            a.matricula,
            a.nome_completo,
            a.data_nascimento,
            a.status,
            t.nome AS turma_nome
        FROM alunos a
        LEFT JOIN matriculas m
            ON m.aluno_id = a.id
            AND m.ano_letivo = YEAR(CURDATE())
            AND m.status = 'ativa'
        LEFT JOIN turmas t
            ON t.id = m.turma_id
        WHERE 1 = 1
    ";

    $params = [];

    // Busca por nome ou matrícula.
    if ($busca !== '') {
        $sql .= "
            AND (
                a.nome_completo LIKE :nome
                OR a.matricula LIKE :matricula
            )
        ";

        $params['nome'] = '%' . $busca . '%';
        $params['matricula'] = '%' . $busca . '%';
    }

    // Filtra pela turma.
    if ($turmaId !== null) {
        $sql .= " AND t.id = :turma_id";
        $params['turma_id'] = $turmaId;
    }

    // Filtra pelo status do aluno.
    if ($status !== '') {
        $sql .= " AND a.status = :status";
        $params['status'] = $status;
    }

    $sql .= " ORDER BY a.nome_completo ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $alunos = $stmt->fetchAll();

} catch (Exception $e) {
    $erro = 'Não foi possível carregar os alunos. Verifique a conexão e a estrutura do banco de dados.';
}

// Escapa os dados exibidos no HTML.
function e($valor): string
{
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

include __DIR__ . '/../../views/layouts/header.php';
include __DIR__ . '/../../views/layouts/sidebar.php';

?>

<main class="content">
    <div class="container-fluid">

        <!-- CABEÇALHO -->
        <div class="page-header mb-4">
            <div>
                <h1 class="fw-bold mb-1">Alunos</h1>
                <p class="text-muted mb-0">
                    Gerencie os alunos cadastrados no sistema.
                </p>
            </div>

            <a
                href="<?= BASE_URL ?>/alunos/cadastrar.php"
                class="btn btn-primary"
            >
                <i class="bi bi-person-plus me-2"></i>
                Cadastrar aluno
            </a>
        </div>

        <!-- MENSAGENS -->
        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'excluido'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                Aluno excluído com sucesso.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php
                $mensagens = [
                    'id_invalido' => 'O ID informado é inválido.',
                    'nao_encontrado' => 'Aluno não encontrado.',
                    'possui_vinculos' => 'Não foi possível excluir: o aluno possui registros vinculados.',
                    'banco' => 'Ocorreu um erro ao acessar o banco de dados.'
                ];

                echo e($mensagens[$_GET['erro']] ?? 'Ocorreu um erro.');
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($erro !== null): ?>
            <div class="alert alert-danger">
                <?= e($erro) ?>
            </div>
        <?php endif; ?>

        <!-- FILTROS -->
        <div class="dashboard-panel mb-4">
            <div class="p-3">
                <form method="GET" action="">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="busca" class="form-label">
                                Buscar aluno
                            </label>

                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>

                                <input
                                    type="text"
                                    id="busca"
                                    name="busca"
                                    class="form-control"
                                    placeholder="Nome ou matrícula"
                                    value="<?= e($busca) ?>"
                                >
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="turma" class="form-label">
                                Turma
                            </label>

                            <select
                                id="turma"
                                name="turma"
                                class="form-select"
                            >
                                <option value="">Todas as turmas</option>

                                <?php foreach ($turmas as $turma): ?>
                                    <option
                                        value="<?= (int) $turma['id'] ?>"
                                        <?= $turmaId === (int) $turma['id'] ? 'selected' : '' ?>
                                    >
                                        <?= e($turma['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
                                <option value="ativo" <?= $status === 'ativo' ? 'selected' : '' ?>>
                                    Ativo
                                </option>
                                <option value="inativo" <?= $status === 'inativo' ? 'selected' : '' ?>>
                                    Inativo
                                </option>
                            </select>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a
                            href="<?= BASE_URL ?>/alunos/index.php"
                            class="btn btn-outline-secondary"
                        >
                            Limpar filtros
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel me-1"></i>
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABELA -->
        <div class="dashboard-panel">

            <div class="dashboard-panel-header">
                <div>
                    <h5 class="mb-1">Alunos cadastrados</h5>
                    <small class="text-muted">
                        Lista de alunos do sistema
                    </small>
                </div>

                <span class="badge text-bg-secondary">
                    <?= count($alunos) ?>
                    <?= count($alunos) === 1 ? 'aluno' : 'alunos' ?>
                </span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">

                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Nome</th>
                            <th>Turma</th>
                            <th>Data de nascimento</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($erro !== null): ?>

                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    Não foi possível carregar os dados.
                                </td>
                            </tr>

                        <?php elseif (empty($alunos)): ?>

                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="bi bi-people"></i>

                                        <h6>Nenhum aluno encontrado</h6>

                                        <p class="text-muted mb-3">
                                            Não existem alunos para os filtros selecionados.
                                        </p>

                                        <a
                                            href="<?= BASE_URL ?>/alunos/cadastrar.php"
                                            class="btn btn-primary btn-sm"
                                        >
                                            <i class="bi bi-person-plus me-1"></i>
                                            Cadastrar aluno
                                        </a>
                                    </div>
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($alunos as $aluno): ?>
                                <tr>
                                    <td>
                                        <?= e($aluno['matricula']) ?>
                                    </td>

                                    <td>
                                        <?= e($aluno['nome_completo']) ?>
                                    </td>

                                    <td>
                                        <?= e($aluno['turma_nome'] ?? 'Sem turma') ?>
                                    </td>

                                    <td>
                                        <?php if (!empty($aluno['data_nascimento'])): ?>
                                            <?= date(
                                                'd/m/Y',
                                                strtotime($aluno['data_nascimento'])
                                            ) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Não informado</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($aluno['status'] === 'ativo'): ?>
                                            <span class="badge text-bg-success">
                                                Ativo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge text-bg-secondary">
                                                <?= e(ucfirst($aluno['status'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a
                                                href="<?= BASE_URL ?>/alunos/editar.php?id=<?= (int) $aluno['id'] ?>"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Editar aluno"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <a
                                                href="<?= BASE_URL ?>/alunos/excluir.php?id=<?= (int) $aluno['id'] ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Excluir aluno"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>

    </div>
</main>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>

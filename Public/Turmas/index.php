```php
<?php



require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$currentPage = 'turmas';

function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$busca = trim($_GET['busca'] ?? ''); 
$ano = trim($_GET['ano'] ?? '');
$status = trim($_GET['status'] ?? '');

$turmas = [];
$anos = [];
$erro = false;

try {
    $database = new Database();
    $pdo = $database->connect();

    // Buscar os anos letivos disponíveis.
    $stmtAnos = $pdo->query(
        "SELECT DISTINCT ano_letivo
         FROM turmas
         ORDER BY ano_letivo DESC"
    );

    $anos = $stmtAnos->fetchAll(PDO::FETCH_COLUMN);

    // Montar a consulta de turmas.
    $sql = "
        SELECT
            t.id,
            t.nome,
            t.ano_letivo,
            t.serie,
            t.turno,
            t.sala,
            t.capacidade,
            t.status,
            COUNT(
                DISTINCT CASE
                    WHEN m.status = 'ativa'
                    THEN m.aluno_id
                END
            ) AS total_alunos
        FROM turmas t
        LEFT JOIN matriculas m
            ON m.turma_id = t.id
            AND m.ano_letivo = t.ano_letivo
        WHERE 1 = 1
    ";

    $parametros = [];

   
if ($busca !== '') {
    $sql .= " AND (
        t.nome LIKE :busca_nome
        OR t.serie LIKE :busca_serie
        OR t.sala LIKE :busca_sala
    )";

    $termoBusca = '%' . $busca . '%';

    $parametros['busca_nome'] = $termoBusca;
    $parametros['busca_serie'] = $termoBusca;
    $parametros['busca_sala'] = $termoBusca;
}


    if ($ano !== '' && ctype_digit($ano)) {
        $sql .= " AND t.ano_letivo = :ano";
        $parametros['ano'] = (int) $ano;
    }

    $statusPermitidos = ['ativa', 'inativa', 'concluida'];

    if (in_array($status, $statusPermitidos, true)) {
        $sql .= " AND t.status = :status";
        $parametros['status'] = $status;
    }

    $sql .= "
        GROUP BY
            t.id,
            t.nome,
            t.ano_letivo,
            t.serie,
            t.turno,
            t.sala,
            t.capacidade,
            t.status
        ORDER BY t.ano_letivo DESC, t.nome ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);

    $turmas = $stmt->fetchAll();

} catch (Throwable $e) {
    $erro = true;
}

require_once __DIR__ . '/../../views/layouts/header.php';
?>

<?php require_once __DIR__ . '/../../views/layouts/sidebar.php'; ?>

<main class="content">

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-1">Turmas</h1>
            <p class="text-secondary mb-0">
                Gerencie as turmas cadastradas no sistema.
            </p>
        </div>

        <a
            href="<?= BASE_URL ?>/turmas/cadastrar.php"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg me-1"></i>
            Nova turma
        </a>
    </div>

    <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'cadastrada'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Turma cadastrada com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'atualizada'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Turma atualizada com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    
    <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'excluida'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Turma excluída com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php endif; ?>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div class="alert alert-danger" role="alert">
            Não foi possível carregar as turmas. Verifique a conexão e a estrutura do banco de dados.
        </div>
    <?php else: ?>

        <section class="dashboard-panel mt-4">

            <div class="dashboard-panel-header">
                <h2 class="h5 mb-0">Turmas cadastradas</h2>
                <span class="badge text-bg-primary">
                    <?= count($turmas) ?> resultado(s)
                </span>
            </div>

            <div class="p-3">

                <form method="GET" class="row g-3 mb-4">

                    <div class="col-md-5">
                        <label for="busca" class="form-label">
                            Pesquisar
                        </label>

                        <input
                            type="search"
                            name="busca"
                            id="busca"
                            class="form-control"
                            placeholder="Nome, série ou sala"
                            value="<?= e($busca) ?>"
                        >
                    </div>

                    <div class="col-md-3">
                        <label for="ano" class="form-label">
                            Ano letivo
                        </label>

                        <select name="ano" id="ano" class="form-select">
                            <option value="">Todos os anos</option>

                            <?php foreach ($anos as $anoDisponivel): ?>
                                <option
                                    value="<?= e($anoDisponivel) ?>"
                                    <?= $ano === (string) $anoDisponivel ? 'selected' : '' ?>
                                >
                                    <?= e($anoDisponivel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="status" class="form-label">
                            Status
                        </label>

                        <select name="status" id="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="ativa" <?= $status === 'ativa' ? 'selected' : '' ?>>
                                Ativa
                            </option>
                            <option value="inativa" <?= $status === 'inativa' ? 'selected' : '' ?>>
                                Inativa
                            </option>
                            <option value="concluida" <?= $status === 'concluida' ? 'selected' : '' ?>>
                                Concluída
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search"></i>
                            Filtrar
                        </button>
                    </div>

                    <div class="col-12">
                        <a
                            href="<?= BASE_URL ?>/turmas/index.php"
                            class="btn btn-outline-secondary btn-sm"
                        >
                            Limpar filtros
                        </a>
                    </div>

                </form>

                <?php if (empty($turmas)): ?>

                    <div class="empty-state text-center py-5">
                        <i class="bi bi-people fs-1 text-secondary"></i>

                        <h3 class="h5 mt-3">Nenhuma turma encontrada</h3>

                        <p class="text-secondary">
                            Cadastre uma turma ou altere os filtros da pesquisa.
                        </p>

                        <a
                            href="<?= BASE_URL ?>/turmas/cadastrar.php"
                            class="btn btn-primary"
                        >
                            Cadastrar primeira turma
                        </a>
                    </div>

                <?php else: ?>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">

                            <thead>
                                <tr>
                                    <th>Turma</th>
                                    <th>Ano letivo</th>
                                    <th>Turno</th>
                                    <th>Sala</th>
                                    <th>Alunos</th>
                                    <th>Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($turmas as $turma): ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($turma['nome']) ?></strong>

                                            <?php if (!empty($turma['serie'])): ?>
                                                <div class="small text-secondary">
                                                    <?= e($turma['serie']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <td><?= e($turma['ano_letivo']) ?></td>

                                        <td>
                                            <?php
                                            $turnos = [
                                                'manha' => 'Manhã',
                                                'tarde' => 'Tarde',
                                                'noite' => 'Noite',
                                                'integral' => 'Integral'
                                            ];
                                            ?>
                                            <?= e($turnos[$turma['turno']] ?? $turma['turno']) ?>
                                        </td>

                                        <td><?= e($turma['sala'] ?: '—') ?></td>

                                        <td>
                                            <?= (int) $turma['total_alunos'] ?>

                                            <?php if ($turma['capacidade'] !== null): ?>
                                                <span class="text-secondary small">
                                                    / <?= (int) $turma['capacidade'] ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php
                                            $statusTurma = [
                                                'ativa' => 'success',
                                                'inativa' => 'secondary',
                                                'concluida' => 'info'
                                            ];

                                            $cor = $statusTurma[$turma['status']] ?? 'secondary';

                                            $nomesStatus = [
                                                'ativa' => 'Ativa',
                                                'inativa' => 'Inativa',
                                                'concluida' => 'Concluída'
                                            ];
                                            ?>

                                            <span class="badge text-bg-<?= e($cor) ?>">
                                                <?= e($nomesStatus[$turma['status']] ?? $turma['status']) ?>
                                            </span>
                                        </td>

                                       
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

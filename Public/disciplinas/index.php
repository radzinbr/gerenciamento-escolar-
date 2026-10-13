
<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';


$currentPage = "disciplinas";
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$busca = trim($_GET['busca'] ?? '');
$status = $_GET['status'] ?? '';

$disciplinas = [];
$erro = null;

try {
    $database = new Database();
    $pdo = $database->connect();

    $sql = 'SELECT
                d.id,
                d.nome,
                d.descricao,
                d.carga_horaria,
                d.status
            FROM disciplinas d
            WHERE d.excluido_em IS NULL';

    $parametros = [];

    if ($busca !== '') {
        $sql .= ' AND (d.nome LIKE :busca_nome
                   OR d.descricao LIKE :busca_descricao)';

        $parametros['busca_nome'] = '%' . $busca . '%';
        $parametros['busca_descricao'] = '%' . $busca . '%';
    }

    if (in_array($status, ['ativa', 'inativa'], true)) {
        $sql .= ' AND d.status = :status';
        $parametros['status'] = $status;
    }

    $sql .= ' ORDER BY d.nome ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);

    $disciplinas = $stmt->fetchAll();

} catch (Exception $e) {
    $erro = 'Não foi possível carregar as disciplinas.';
}

require_once __DIR__ . '/../../views/layouts/header.php';
require_once __DIR__ . '/../../views/layouts/sidebar.php';

?>

<div class="content">

    <div class="page-header">
        <div>
            <h1>Disciplinas</h1>
            <p>Gerencie as disciplinas cadastradas no sistema escolar.</p>
        </div>

        <a
            href="<?= BASE_URL ?>/disciplinas/cadastrar.php"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Nova disciplina
        </a>
    </div>

    <?php if (isset($_GET['sucesso'])): ?>
        <?php
        $mensagens = [
            'cadastrada' => 'Disciplina cadastrada com sucesso!',
            'atualizada' => 'Disciplina atualizada com sucesso!',
            'excluida' => 'Disciplina ocultada com sucesso!'
        ];

        $mensagem = $mensagens[$_GET['sucesso']] ?? null;
        ?>

        <?php if ($mensagem): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= e($mensagem) ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Fechar"
                ></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_GET['erro'])): ?>
        <?php
        $mensagensErro = [
            'id_invalido' => 'O identificador informado é inválido.',
            'nao_encontrado' => 'Disciplina não encontrada.'
        ];

        $mensagemErro = $mensagensErro[$_GET['erro']] ?? null;
        ?>

        <?php if ($mensagemErro): ?>
            <div class="alert alert-danger" role="alert">
                <?= e($mensagemErro) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div class="alert alert-danger" role="alert">
            <?= e($erro) ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-panel">

        <div class="dashboard-panel-header">
            <h2 class="h5 mb-0">Disciplinas cadastradas</h2>

            <span class="badge text-bg-secondary">
                <?= count($disciplinas) ?>
                <?= count($disciplinas) === 1 ? 'disciplina' : 'disciplinas' ?>
            </span>
        </div>

        <div class="p-3">
            <form method="GET" action="" class="row g-3">

                <div class="col-md-7">
                    <label for="busca" class="form-label">
                        Pesquisar disciplina
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="busca"
                        name="busca"
                        value="<?= e($busca) ?>"
                        placeholder="Nome ou descrição..."
                    >
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label">
                        Status
                    </label>

                    <select
                        class="form-select"
                        id="status"
                        name="status"
                    >
                        <option value="">Todos os status</option>

                        <option
                            value="ativa"
                            <?= $status === 'ativa' ? 'selected' : '' ?>
                        >
                            Ativa
                        </option>

                        <option
                            value="inativa"
                            <?= $status === 'inativa' ? 'selected' : '' ?>
                        >
                            Inativa
                        </option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                        Buscar
                    </button>

                    <a
                        href="<?= BASE_URL ?>/disciplinas/index.php"
                        class="btn btn-outline-secondary"
                        title="Limpar filtros"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>

            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead>
                    <tr>
                        <th>Disciplina</th>
                        <th>Descrição</th>
                        <th>Carga horária</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($disciplinas)): ?>

                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-journal-bookmark fs-2 text-secondary"></i>

                                <p class="mt-2 mb-1">
                                    Nenhuma disciplina encontrada.
                                </p>

                                <small class="text-secondary">
                                    Tente alterar os filtros ou cadastre uma disciplina.
                                </small>
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($disciplinas as $disciplina): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= e($disciplina['nome']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?php if (!empty($disciplina['descricao'])): ?>
                                        <?= e($disciplina['descricao']) ?>
                                    <?php else: ?>
                                        <span class="text-secondary">
                                            Sem descrição
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= $disciplina['carga_horaria'] !== null
                                        ? e($disciplina['carga_horaria']) . ' h'
                                        : 'Não informada' ?>
                                </td>

                                <td>
                                    <?php if ($disciplina['status'] === 'ativa'): ?>
                                        <span class="badge text-bg-success">
                                            Ativa
                                        </span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">
                                            Inativa
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end">
                                    <a
                                        href="<?= BASE_URL ?>/disciplinas/editar.php?id=<?= (int) $disciplina['id'] ?>"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Editar disciplina"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <a
                                        href="<?= BASE_URL ?>/disciplinas/excluir.php?id=<?= (int) $disciplina['id'] ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Ocultar disciplina"
                                    >
                                        <i class="bi bi-eye-slash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>

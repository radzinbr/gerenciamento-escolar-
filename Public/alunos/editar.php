
<?php

session_start();

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$currentPage = 'alunos';

function e($valor): string
{
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id || $id <= 0) {
    header('Location: ' . BASE_URL . '/alunos/index.php?erro=id_invalido');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$erros = [];
$aluno = null;
$turmas = [];
$pdo = null;

$campos = [
    'nome',
    'data_nascimento',
    'cpf',
    'rg',
    'sexo',
    'email',
    'telefone',
    'cep',
    'logradouro',
    'numero',
    'bairro',
    'cidade',
    'estado',
    'turma',
    'status'
];

try {
    $database = new Database();
    $pdo = $database->connect();

    // Busca os dados do aluno e a turma vinculada no ano atual.
    $stmt = $pdo->prepare("
        SELECT
            a.*,
            m.turma_id AS turma_atual
        FROM alunos a
        LEFT JOIN matriculas m
            ON m.aluno_id = a.id
            AND m.ano_letivo = :ano
        WHERE a.id = :id
        LIMIT 1
    ");

    $stmt->execute([
        'ano' => (int) date('Y'),
        'id' => $id
    ]);

    $registro = $stmt->fetch();

    if (!$registro) {
        header('Location: ' . BASE_URL . '/alunos/index.php?erro=nao_encontrado');
        exit;
    }

    $aluno = [
        'id' => (int) $registro['id'],
        'matricula' => $registro['matricula'],
        'nome' => $registro['nome_completo'],
        'data_nascimento' => $registro['data_nascimento'] ?? '',
        'cpf' => $registro['cpf'] ?? '',
        'rg' => $registro['rg'] ?? '',
        'sexo' => $registro['sexo'] === 'Nao informado'
            ? ''
            : $registro['sexo'],
        'email' => $registro['email'] ?? '',
        'telefone' => $registro['telefone'] ?? '',
        'cep' => $registro['cep'] ?? '',
        'logradouro' => $registro['logradouro'] ?? '',
        'numero' => $registro['numero'] ?? '',
        'bairro' => $registro['bairro'] ?? '',
        'cidade' => $registro['cidade'] ?? '',
        'estado' => $registro['estado'] ?? '',
        'turma' => $registro['turma_atual'] !== null
            ? (string) $registro['turma_atual']
            : '',
        'status' => $registro['status']
    ];

    // Carrega as turmas ativas e mantém a turma atual disponível.
    $stmtTurmas = $pdo->prepare("
        SELECT id, nome
        FROM turmas
        WHERE status = 'ativa' OR id = :turma_atual
        ORDER BY nome
    ");

    $stmtTurmas->execute([
        'turma_atual' => $aluno['turma'] !== ''
            ? (int) $aluno['turma']
            : 0
    ]);

    $turmas = $stmtTurmas->fetchAll();

} catch (Exception $ex) {
    $erros[] = 'Não foi possível carregar os dados do aluno. Verifique o banco de dados.';
}

// Processa as alterações enviadas pelo formulário.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $aluno !== null && $pdo !== null) {

    $tokenEnviado = $_POST['csrf_token'] ?? '';

    if (
        !is_string($tokenEnviado) ||
        !hash_equals($_SESSION['csrf_token'], $tokenEnviado)
    ) {
        $erros[] = 'Sessão inválida. Atualize a página e tente novamente.';
    }

    // Recupera os campos enviados sem perder os dados em caso de erro.
    foreach ($campos as $campo) {
        if (isset($_POST[$campo]) && is_string($_POST[$campo])) {
            $aluno[$campo] = trim($_POST[$campo]);
        }
    }

    // Validações.
    if ($aluno['nome'] === '') {
        $erros[] = 'O nome completo é obrigatório.';
    } elseif (mb_strlen($aluno['nome']) > 150) {
        $erros[] = 'O nome deve ter no máximo 150 caracteres.';
    }

    $data = null;

    if ($aluno['data_nascimento'] === '') {
        $erros[] = 'A data de nascimento é obrigatória.';
    } else {
        $data = DateTime::createFromFormat(
            '!Y-m-d',
            $aluno['data_nascimento']
        );

        if (
            !$data ||
            $data->format('Y-m-d') !== $aluno['data_nascimento'] ||
            $data > new DateTime('today')
        ) {
            $erros[] = 'Informe uma data de nascimento válida.';
        }
    }

    if (
        $aluno['sexo'] !== '' &&
        !in_array($aluno['sexo'], ['M', 'F'], true)
    ) {
        $erros[] = 'Selecione uma opção válida para sexo.';
    }

    if (
        $aluno['email'] !== '' &&
        !filter_var($aluno['email'], FILTER_VALIDATE_EMAIL)
    ) {
        $erros[] = 'Informe um e-mail válido.';
    }

    if (!in_array($aluno['status'], ['ativo', 'inativo'], true)) {
        $erros[] = 'Selecione um status válido.';
    }

    $limites = [
        'cpf' => 14,
        'rg' => 20,
        'email' => 150,
        'telefone' => 25,
        'cep' => 9,
        'logradouro' => 150,
        'numero' => 20,
        'bairro' => 100,
        'cidade' => 100
    ];

    foreach ($limites as $campo => $limite) {
        if (mb_strlen($aluno[$campo]) > $limite) {
            $erros[] = ucfirst($campo)
                . " deve ter no máximo {$limite} caracteres.";
        }
    }

    if (
        $aluno['estado'] !== '' &&
        !preg_match('/^[A-Z]{2}$/', $aluno['estado'])
    ) {
        $erros[] = 'Selecione uma UF válida.';
    }

    $turmaId = null;

    if ($aluno['turma'] !== '') {
        $turmaId = filter_var(
            $aluno['turma'],
            FILTER_VALIDATE_INT
        );

        if (!$turmaId || $turmaId <= 0) {
            $erros[] = 'Selecione uma turma válida.';
        } else {
            $stmtTurma = $pdo->prepare("
                SELECT id
                FROM turmas
                WHERE id = :id AND status = 'ativa'
            ");

            $stmtTurma->execute(['id' => $turmaId]);

            // Permite manter uma turma que já esteja vinculada.
            if (!$stmtTurma->fetch()) {
                $stmtAtual = $pdo->prepare("
                    SELECT id
                    FROM matriculas
                    WHERE aluno_id = :aluno_id
                      AND turma_id = :turma_id
                      AND ano_letivo = :ano
                    LIMIT 1
                ");

                $stmtAtual->execute([
                    'aluno_id' => $id,
                    'turma_id' => $turmaId,
                    'ano' => (int) date('Y')
                ]);

                if (!$stmtAtual->fetch()) {
                    $erros[] = 'A turma selecionada não existe ou está inativa.';
                }
            }
        }
    }

    if (empty($erros)) {
        try {
            $pdo->beginTransaction();

            // Atualiza os dados pessoais do aluno.
            $stmtUpdate = $pdo->prepare("
                UPDATE alunos
                SET
                    nome_completo = :nome,
                    data_nascimento = :data_nascimento,
                    cpf = :cpf,
                    rg = :rg,
                    sexo = :sexo,
                    email = :email,
                    telefone = :telefone,
                    cep = :cep,
                    logradouro = :logradouro,
                    numero = :numero,
                    bairro = :bairro,
                    cidade = :cidade,
                    estado = :estado,
                    status = :status
                WHERE id = :id
            ");

            $stmtUpdate->execute([
                'nome' => $aluno['nome'],
                'data_nascimento' => $aluno['data_nascimento'],
                'cpf' => $aluno['cpf'] !== '' ? $aluno['cpf'] : null,
                'rg' => $aluno['rg'] !== '' ? $aluno['rg'] : null,
                'sexo' => $aluno['sexo'] !== ''
                    ? $aluno['sexo']
                    : 'Nao informado',
                'email' => $aluno['email'] !== '' ? $aluno['email'] : null,
                'telefone' => $aluno['telefone'] !== '' ? $aluno['telefone'] : null,
                'cep' => $aluno['cep'] !== '' ? $aluno['cep'] : null,
                'logradouro' => $aluno['logradouro'] !== '' ? $aluno['logradouro'] : null,
                'numero' => $aluno['numero'] !== '' ? $aluno['numero'] : null,
                'bairro' => $aluno['bairro'] !== '' ? $aluno['bairro'] : null,
                'cidade' => $aluno['cidade'] !== '' ? $aluno['cidade'] : null,
                'estado' => $aluno['estado'] !== '' ? $aluno['estado'] : null,
                'status' => $aluno['status'],
                'id' => $id
            ]);

            // Consulta a matrícula do aluno no ano atual.
            $anoAtual = (int) date('Y');

            $stmtMatricula = $pdo->prepare("
                SELECT id
                FROM matriculas
                WHERE aluno_id = :aluno_id
                  AND ano_letivo = :ano
                LIMIT 1
                FOR UPDATE
            ");

            $stmtMatricula->execute([
                'aluno_id' => $id,
                'ano' => $anoAtual
            ]);

            $matriculaExistente = $stmtMatricula->fetch();

            if ($turmaId !== null) {
                $statusMatricula = $aluno['status'] === 'ativo'
                    ? 'ativa'
                    : 'trancada';

                if ($matriculaExistente) {
                    // Atualiza a matrícula existente.
                    $stmtUpdateMatricula = $pdo->prepare("
                        UPDATE matriculas
                        SET
                            turma_id = :turma_id,
                            status = :status
                        WHERE id = :id
                    ");

                    $stmtUpdateMatricula->execute([
                        'turma_id' => $turmaId,
                        'status' => $statusMatricula,
                        'id' => $matriculaExistente['id']
                    ]);
                } else {
                    // Cria uma matrícula para o ano atual.
                    $stmtInsertMatricula = $pdo->prepare("
                        INSERT INTO matriculas (
                            aluno_id,
                            turma_id,
                            ano_letivo,
                            data_matricula,
                            status
                        ) VALUES (
                            :aluno_id,
                            :turma_id,
                            :ano,
                            :data_matricula,
                            :status
                        )
                    ");

                    $stmtInsertMatricula->execute([
                        'aluno_id' => $id,
                        'turma_id' => $turmaId,
                        'ano' => $anoAtual,
                        'data_matricula' => date('Y-m-d'),
                        'status' => $statusMatricula
                    ]);
                }

            } elseif ($matriculaExistente) {
                // Preserva o registro histórico, mas encerra a matrícula atual.
                $stmtCancelar = $pdo->prepare("
                    UPDATE matriculas
                    SET status = 'cancelada'
                    WHERE id = :id
                ");

                $stmtCancelar->execute([
                    'id' => $matriculaExistente['id']
                ]);
            }

            $pdo->commit();

            unset($_SESSION['csrf_token']);

            header(
                'Location: ' . BASE_URL
                . '/alunos/index.php?sucesso=atualizado'
            );
            exit;

        } catch (PDOException $ex) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            if ($ex->getCode() === '23000') {
                $erros[] = 'Não foi possível salvar. O CPF informado pode pertencer a outro aluno.';
            } else {
                $erros[] = 'Ocorreu um erro ao atualizar o aluno. Confira os dados e tente novamente.';
            }

        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $erros[] = 'Ocorreu um erro inesperado ao atualizar o aluno.';
        }
    }
}

include __DIR__ . '/../../views/layouts/header.php';
include __DIR__ . '/../../views/layouts/sidebar.php';

?>

<main class="content">
    <div class="container-fluid">

        <div class="page-header mb-4">
            <div>
                <h1 class="fw-bold mb-1">Editar aluno</h1>
                <p class="text-muted mb-0">
                    Atualize as informações do aluno.
                </p>
            </div>

            <a
                href="<?= BASE_URL ?>/alunos/index.php"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-2"></i>
                Voltar
            </a>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger" role="alert">
                <strong>Não foi possível salvar as alterações:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($erros as $erro): ?>
                        <li><?= e($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($aluno !== null): ?>

            <div class="dashboard-panel">

                <div class="dashboard-panel-header">
                    <div>
                        <h5 class="mb-1">Dados do aluno</h5>
                        <small class="text-muted">
                            Atualize os dados pessoais e acadêmicos
                        </small>
                    </div>

                    <span class="badge text-bg-secondary">
                        Matrícula: <?= e($aluno['matricula']) ?>
                    </span>
                </div>

                <div class="p-4">

                    <form method="POST" action="">

                        <input type="hidden" name="id" value="<?= (int) $id ?>">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e($_SESSION['csrf_token']) ?>"
                        >

                        <h6 class="form-section-title">
                            <i class="bi bi-person me-2"></i>
                            Dados pessoais
                        </h6>

                        <div class="row g-3 mb-4">

                            <div class="col-md-8">
                                <label for="nome" class="form-label">
                                    Nome completo
                                </label>
                                <input
                                    type="text"
                                    id="nome"
                                    name="nome"
                                    class="form-control"
                                    maxlength="150"
                                    value="<?= e($aluno['nome']) ?>"
                                    required
                                >
                            </div>

                            <div class="col-md-4">
                                <label for="data_nascimento" class="form-label">
                                    Data de nascimento
                                </label>
                                <input
                                    type="date"
                                    id="data_nascimento"
                                    name="data_nascimento"
                                    class="form-control"
                                    max="<?= date('Y-m-d') ?>"
                                    value="<?= e($aluno['data_nascimento']) ?>"
                                    required
                                >
                            </div>

                            <div class="col-md-4">
                                <label for="cpf" class="form-label">CPF</label>
                                <input
                                    type="text"
                                    id="cpf"
                                    name="cpf"
                                    class="form-control"
                                    maxlength="14"
                                    value="<?= e($aluno['cpf']) ?>"
                                >
                            </div>

                            <div class="col-md-4">
                                <label for="rg" class="form-label">RG</label>
                                <input
                                    type="text"
                                    id="rg"
                                    name="rg"
                                    class="form-control"
                                    maxlength="20"
                                    value="<?= e($aluno['rg']) ?>"
                                >
                            </div>

                            <div class="col-md-4">
                                <label for="sexo" class="form-label">Sexo</label>
                                <select id="sexo" name="sexo" class="form-select">
                                    <option value="">Não informado</option>
                                    <option value="M" <?= $aluno['sexo'] === 'M' ? 'selected' : '' ?>>
                                        Masculino
                                    </option>
                                    <option value="F" <?= $aluno['sexo'] === 'F' ? 'selected' : '' ?>>
                                        Feminino
                                    </option>
                                </select>
                            </div>

                        </div>

                        <h6 class="form-section-title">
                            <i class="bi bi-telephone me-2"></i>
                            Contato
                        </h6>

                        <div class="row g-3 mb-4">

                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control"
                                    maxlength="150"
                                    value="<?= e($aluno['email']) ?>"
                                >
                            </div>

                            <div class="col-md-6">
                                <label for="telefone" class="form-label">
                                    Telefone
                                </label>
                                <input
                                    type="text"
                                    id="telefone"
                                    name="telefone"
                                    class="form-control"
                                    maxlength="25"
                                    value="<?= e($aluno['telefone']) ?>"
                                >
                            </div>

                        </div>

                        <h6 class="form-section-title">
                            <i class="bi bi-geo-alt me-2"></i>
                            Endereço
                        </h6>

                        <div class="row g-3 mb-4">

                            <div class="col-md-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input
                                    type="text"
                                    id="cep"
                                    name="cep"
                                    class="form-control"
                                    maxlength="9"
                                    value="<?= e($aluno['cep']) ?>"
                                >
                            </div>

                            <div class="col-md-7">
                                <label for="logradouro" class="form-label">
                                    Logradouro
                                </label>
                                <input
                                    type="text"
                                    id="logradouro"
                                    name="logradouro"
                                    class="form-control"
                                    maxlength="150"
                                    value="<?= e($aluno['logradouro']) ?>"
                                >
                            </div>

                            <div class="col-md-2">
                                <label for="numero" class="form-label">Número</label>
                                <input
                                    type="text"
                                    id="numero"
                                    name="numero"
                                    class="form-control"
                                    maxlength="20"
                                    value="<?= e($aluno['numero']) ?>"
                                >
                            </div>

                            <div class="col-md-5">
                                <label for="bairro" class="form-label">Bairro</label>
                                <input
                                    type="text"
                                    id="bairro"
                                    name="bairro"
                                    class="form-control"
                                    maxlength="100"
                                    value="<?= e($aluno['bairro']) ?>"
                                >
                            </div>

                            <div class="col-md-5">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input
                                    type="text"
                                    id="cidade"
                                    name="cidade"
                                    class="form-control"
                                    maxlength="100"
                                    value="<?= e($aluno['cidade']) ?>"
                                >
                            </div>

                            <div class="col-md-2">
                                <label for="estado" class="form-label">UF</label>
                                <select id="estado" name="estado" class="form-select">
                                    <option value="">UF</option>

                                    <?php
                                    $estados = [
                                        'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF',
                                        'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA',
                                        'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS',
                                        'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
                                    ];
                                    ?>

                                    <?php foreach ($estados as $uf): ?>
                                        <option
                                            value="<?= $uf ?>"
                                            <?= $aluno['estado'] === $uf ? 'selected' : '' ?>
                                        >
                                            <?= $uf ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>

                        <h6 class="form-section-title">
                            <i class="bi bi-mortarboard me-2"></i>
                            Dados acadêmicos
                        </h6>

                        <div class="row g-3 mb-4">

                            <div class="col-md-6">
                                <label for="turma" class="form-label">Turma</label>

                                <select id="turma" name="turma" class="form-select">
                                    <option value="">Sem turma</option>

                                    <?php foreach ($turmas as $turma): ?>
                                        <option
                                            value="<?= (int) $turma['id'] ?>"
                                            <?= $aluno['turma'] === (string) $turma['id'] ? 'selected' : '' ?>
                                        >
                                            <?= e($turma['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>

                                <select id="status" name="status" class="form-select">
                                    <option value="ativo" <?= $aluno['status'] === 'ativo' ? 'selected' : '' ?>>
                                        Ativo
                                    </option>
                                    <option value="inativo" <?= $aluno['status'] === 'inativo' ? 'selected' : '' ?>>
                                        Inativo
                                    </option>
                                </select>
                            </div>

                        </div>

                        <div class="form-actions">
                            <a
                                href="<?= BASE_URL ?>/alunos/index.php"
                                class="btn btn-outline-secondary"
                            >
                                Cancelar
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>
                                Salvar alterações
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>

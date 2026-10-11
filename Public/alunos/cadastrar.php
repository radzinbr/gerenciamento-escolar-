
<?php

session_start();

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$currentPage = 'alunos';

$erros = [];
$valores = [
    'nome' => '',
    'data_nascimento' => '',
    'cpf' => '',
    'rg' => '',
    'sexo' => '',
    'email' => '',
    'telefone' => '',
    'cep' => '',
    'logradouro' => '',
    'numero' => '',
    'bairro' => '',
    'cidade' => '',
    'estado' => '',
    'turma' => '',
    'status' => 'ativo'
];

$turmas = [];
$pdo = null;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function e($valor): string
{
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Carrega as turmas cadastradas no banco.
try {
    $database = new Database();
    $pdo = $database->connect();

    $stmt = $pdo->query(
        "SELECT id, nome
         FROM turmas
         WHERE status = 'ativa'
         ORDER BY nome"
    );

    $turmas = $stmt->fetchAll();

} catch (Exception $ex) {
    $erros[] = 'Não foi possível carregar as turmas. Verifique o banco de dados.';
}

// Processa o formulário.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($valores as $campo => $valorPadrao) {
        if (isset($_POST[$campo]) && is_string($_POST[$campo])) {
            $valores[$campo] = trim($_POST[$campo]);
        }
    }

    // Proteção CSRF.
    $tokenEnviado = $_POST['csrf_token'] ?? '';

    if (
        !is_string($tokenEnviado) ||
        !hash_equals($_SESSION['csrf_token'], $tokenEnviado)
    ) {
        $erros[] = 'Sessão inválida. Atualize a página e tente novamente.';
    }

    // Validações básicas.
    if ($valores['nome'] === '') {
        $erros[] = 'O nome completo é obrigatório.';
    } elseif (mb_strlen($valores['nome']) > 150) {
        $erros[] = 'O nome deve ter no máximo 150 caracteres.';
    }

    if ($valores['data_nascimento'] === '') {
        $erros[] = 'A data de nascimento é obrigatória.';
    } else {
        $data = DateTime::createFromFormat(
            '!Y-m-d',
            $valores['data_nascimento']
        );

        $dataValida = $data
            && $data->format('Y-m-d') === $valores['data_nascimento']
            && $data <= new DateTime('today');

        if (!$dataValida) {
            $erros[] = 'Informe uma data de nascimento válida.';
        }
    }

    if (
        $valores['sexo'] !== '' &&
        !in_array($valores['sexo'], ['M', 'F'], true)
    ) {
        $erros[] = 'Selecione uma opção válida para sexo.';
    }

    if (
        $valores['email'] !== '' &&
        !filter_var($valores['email'], FILTER_VALIDATE_EMAIL)
    ) {
        $erros[] = 'Informe um e-mail válido.';
    }

    if (!in_array($valores['status'], ['ativo', 'inativo'], true)) {
        $erros[] = 'Selecione um status válido.';
    }

    if (
        $valores['cpf'] !== '' &&
        mb_strlen($valores['cpf']) > 14
    ) {
        $erros[] = 'O CPF deve ter no máximo 14 caracteres.';
    }

    if (
        $valores['rg'] !== '' &&
        mb_strlen($valores['rg']) > 20
    ) {
        $erros[] = 'O RG deve ter no máximo 20 caracteres.';
    }

    if (
        $valores['email'] !== '' &&
        mb_strlen($valores['email']) > 150
    ) {
        $erros[] = 'O e-mail deve ter no máximo 150 caracteres.';
    }

    if (
        $valores['telefone'] !== '' &&
        mb_strlen($valores['telefone']) > 25
    ) {
        $erros[] = 'O telefone deve ter no máximo 25 caracteres.';
    }

    if (
        $valores['cep'] !== '' &&
        mb_strlen($valores['cep']) > 9
    ) {
        $erros[] = 'O CEP deve ter no máximo 9 caracteres.';
    }

    if (
        $valores['logradouro'] !== '' &&
        mb_strlen($valores['logradouro']) > 150
    ) {
        $erros[] = 'O logradouro deve ter no máximo 150 caracteres.';
    }

    if (
        $valores['numero'] !== '' &&
        mb_strlen($valores['numero']) > 20
    ) {
        $erros[] = 'O número deve ter no máximo 20 caracteres.';
    }

    if (
        $valores['bairro'] !== '' &&
        mb_strlen($valores['bairro']) > 100
    ) {
        $erros[] = 'O bairro deve ter no máximo 100 caracteres.';
    }

    if (
        $valores['cidade'] !== '' &&
        mb_strlen($valores['cidade']) > 100
    ) {
        $erros[] = 'A cidade deve ter no máximo 100 caracteres.';
    }

    if (
        $valores['estado'] !== '' &&
        !preg_match('/^[A-Z]{2}$/', $valores['estado'])
    ) {
        $erros[] = 'Selecione uma UF válida.';
    }

    // Valida a turma selecionada.
    $turmaId = null;

    if ($valores['turma'] !== '') {
        $turmaId = filter_var(
            $valores['turma'],
            FILTER_VALIDATE_INT
        );

        if (!$turmaId || $turmaId <= 0) {
            $erros[] = 'Selecione uma turma válida.';
        } elseif ($pdo !== null) {
            $stmtTurma = $pdo->prepare(
                "SELECT id
                 FROM turmas
                 WHERE id = :id AND status = 'ativa'"
            );

            $stmtTurma->execute(['id' => $turmaId]);

            if (!$stmtTurma->fetch()) {
                $erros[] = 'A turma selecionada não existe ou está inativa.';
            }
        }
    }

    // Salva o aluno e, se necessário, a matrícula.
    if (empty($erros) && $pdo !== null) {
        try {
            $pdo->beginTransaction();

            // Gera uma matrícula única.
            $matricula = 'ALU-' . date('Y') . '-'
                . strtoupper(bin2hex(random_bytes(4)));

            $sql = "
                INSERT INTO alunos (
                    matricula,
                    nome_completo,
                    data_nascimento,
                    cpf,
                    rg,
                    sexo,
                    email,
                    telefone,
                    cep,
                    logradouro,
                    numero,
                    bairro,
                    cidade,
                    estado,
                    status
                ) VALUES (
                    :matricula,
                    :nome_completo,
                    :data_nascimento,
                    :cpf,
                    :rg,
                    :sexo,
                    :email,
                    :telefone,
                    :cep,
                    :logradouro,
                    :numero,
                    :bairro,
                    :cidade,
                    :estado,
                    :status
                )
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'matricula' => $matricula,
                'nome_completo' => $valores['nome'],
                'data_nascimento' => $valores['data_nascimento'],
                'cpf' => $valores['cpf'] !== '' ? $valores['cpf'] : null,
                'rg' => $valores['rg'] !== '' ? $valores['rg'] : null,
                'sexo' => $valores['sexo'] !== ''
                    ? $valores['sexo']
                    : 'Nao informado',
                'email' => $valores['email'] !== '' ? $valores['email'] : null,
                'telefone' => $valores['telefone'] !== '' ? $valores['telefone'] : null,
                'cep' => $valores['cep'] !== '' ? $valores['cep'] : null,
                'logradouro' => $valores['logradouro'] !== '' ? $valores['logradouro'] : null,
                'numero' => $valores['numero'] !== '' ? $valores['numero'] : null,
                'bairro' => $valores['bairro'] !== '' ? $valores['bairro'] : null,
                'cidade' => $valores['cidade'] !== '' ? $valores['cidade'] : null,
                'estado' => $valores['estado'] !== '' ? $valores['estado'] : null,
                'status' => $valores['status']
            ]);

            $alunoId = (int) $pdo->lastInsertId();

            // Cria a matrícula escolar quando uma turma foi escolhida.
            if ($turmaId !== null) {
                $statusMatricula = $valores['status'] === 'ativo'
                    ? 'ativa'
                    : 'trancada';

                $stmtMatricula = $pdo->prepare("
                    INSERT INTO matriculas (
                        aluno_id,
                        turma_id,
                        ano_letivo,
                        data_matricula,
                        status
                    ) VALUES (
                        :aluno_id,
                        :turma_id,
                        :ano_letivo,
                        :data_matricula,
                        :status
                    )
                ");

                $stmtMatricula->execute([
                    'aluno_id' => $alunoId,
                    'turma_id' => $turmaId,
                    'ano_letivo' => (int) date('Y'),
                    'data_matricula' => date('Y-m-d'),
                    'status' => $statusMatricula
                ]);
            }

            $pdo->commit();

            // Evita reenviar o formulário ao atualizar a página.
            unset($_SESSION['csrf_token']);

            header(
                'Location: ' . BASE_URL
                . '/alunos/index.php?sucesso=cadastrado'
            );
            exit;

        } catch (PDOException $ex) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            if ($ex->getCode() === '23000') {
                $erros[] = 'Não foi possível cadastrar. O CPF informado pode já estar registrado.';
            } else {
                $erros[] = 'Ocorreu um erro ao salvar o aluno. Confira os dados e tente novamente.';
            }

        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $erros[] = 'Ocorreu um erro inesperado ao cadastrar o aluno.';
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
                <h1 class="fw-bold mb-1">Cadastrar aluno</h1>
                <p class="text-muted mb-0">
                    Preencha os dados para cadastrar um novo aluno.
                </p>
            </div>

            <div class="d-flex gap-2">
                <a
                    href="<?= BASE_URL ?>/alunos/importar.php"
                    class="btn btn-outline-primary"
                >
                    <i class="bi bi-file-earmark-pdf me-2"></i>
                    Importar PDF
                </a>

                <a
                    href="<?= BASE_URL ?>/alunos/index.php"
                    class="btn btn-outline-secondary"
                >
                    <i class="bi bi-arrow-left me-2"></i>
                    Voltar
                </a>
            </div>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger" role="alert">
                <strong>Não foi possível cadastrar o aluno:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($erros as $erro): ?>
                        <li><?= e($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">
                <div>
                    <h5 class="mb-1">Dados do aluno</h5>
                    <small class="text-muted">
                        Informações pessoais e acadêmicas
                    </small>
                </div>
            </div>

            <div class="p-4">

                <form method="POST" action="">

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
                                placeholder="Digite o nome completo"
                                value="<?= e($valores['nome']) ?>"
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
                                value="<?= e($valores['data_nascimento']) ?>"
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
                                placeholder="000.000.000-00"
                                value="<?= e($valores['cpf']) ?>"
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
                                placeholder="Digite o RG"
                                value="<?= e($valores['rg']) ?>"
                            >
                        </div>

                        <div class="col-md-4">
                            <label for="sexo" class="form-label">Sexo</label>

                            <select id="sexo" name="sexo" class="form-select">
                                <option value="">Não informado</option>

                                <option value="M" <?= $valores['sexo'] === 'M' ? 'selected' : '' ?>>
                                    Masculino
                                </option>

                                <option value="F" <?= $valores['sexo'] === 'F' ? 'selected' : '' ?>>
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
                                placeholder="aluno@email.com"
                                value="<?= e($valores['email']) ?>"
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
                                placeholder="(00) 00000-0000"
                                value="<?= e($valores['telefone']) ?>"
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
                                placeholder="00000-000"
                                value="<?= e($valores['cep']) ?>"
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
                                placeholder="Rua, Avenida..."
                                value="<?= e($valores['logradouro']) ?>"
                            >
                        </div>

                        <div class="col-md-2">
                            <label for="numero" class="form-label">
                                Número
                            </label>

                            <input
                                type="text"
                                id="numero"
                                name="numero"
                                class="form-control"
                                maxlength="20"
                                value="<?= e($valores['numero']) ?>"
                            >
                        </div>

                        <div class="col-md-5">
                            <label for="bairro" class="form-label">
                                Bairro
                            </label>

                            <input
                                type="text"
                                id="bairro"
                                name="bairro"
                                class="form-control"
                                maxlength="100"
                                value="<?= e($valores['bairro']) ?>"
                            >
                        </div>

                        <div class="col-md-5">
                            <label for="cidade" class="form-label">
                                Cidade
                            </label>

                            <input
                                type="text"
                                id="cidade"
                                name="cidade"
                                class="form-control"
                                maxlength="100"
                                value="<?= e($valores['cidade']) ?>"
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
                                        <?= $valores['estado'] === $uf ? 'selected' : '' ?>
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

                            <select
                                id="turma"
                                name="turma"
                                class="form-select"
                            >
                                <option value="">Sem turma por enquanto</option>

                                <?php foreach ($turmas as $turma): ?>
                                    <option
                                        value="<?= (int) $turma['id'] ?>"
                                        <?= $valores['turma'] === (string) $turma['id'] ? 'selected' : '' ?>
                                    >
                                        <?= e($turma['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <?php if (empty($turmas)): ?>
                                <small class="text-muted">
                                    Nenhuma turma ativa cadastrada no banco.
                                </small>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>

                            <select
                                id="status"
                                name="status"
                                class="form-select"
                            >
                                <option value="ativo" <?= $valores['status'] === 'ativo' ? 'selected' : '' ?>>
                                    Ativo
                                </option>

                                <option value="inativo" <?= $valores['status'] === 'inativo' ? 'selected' : '' ?>>
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
                            Cadastrar aluno
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</main>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>

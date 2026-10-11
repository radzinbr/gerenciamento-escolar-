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

$valores = [
    'nome' => '',
    'ano_letivo' => date('Y'),
    'serie' => '',
    'turno' => 'manha',
    'sala' => '',
    'capacidade' => '',
    'status' => 'ativa'
];

$erros = [];
$pdo = null;

try {
    $database = new Database();
    $pdo = $database->connect();
} catch (Throwable $e) {
    $erros[] = 'Não foi possível conectar ao banco de dados.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($valores as $campo => $valorPadrao) {
        $valores[$campo] = trim((string) ($_POST[$campo] ?? $valorPadrao));
    }

    $token = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $erros[] = 'Sessão inválida. Atualize a página e tente novamente.';
    }

    // Validar nome.
    if ($valores['nome'] === '') {
        $erros[] = 'O nome da turma é obrigatório.';
    } elseif (strlen($valores['nome']) > 80) {
        $erros[] = 'O nome da turma deve ter no máximo 80 caracteres.';
    }

    // Validar ano letivo.
    $ano = filter_var($valores['ano_letivo'], FILTER_VALIDATE_INT);

    if (
        $ano === false ||
        $ano < 1901 ||
        $ano > 2155
    ) {
        $erros[] = 'Informe um ano letivo válido.';
    }

    // Validar série.
    if (strlen($valores['serie']) > 50) {
        $erros[] = 'A série deve ter no máximo 50 caracteres.';
    }

    // Validar turno.
    $turnosPermitidos = ['manha', 'tarde', 'noite', 'integral'];

    if (!in_array($valores['turno'], $turnosPermitidos, true)) {
        $erros[] = 'Selecione um turno válido.';
    }

    // Validar sala.
    if (strlen($valores['sala']) > 30) {
        $erros[] = 'A sala deve ter no máximo 30 caracteres.';
    }

    // Validar capacidade.
    $capacidade = null;

    if ($valores['capacidade'] !== '') {
        $capacidade = filter_var(
            $valores['capacidade'],
            FILTER_VALIDATE_INT
        );

        if (
            $capacidade === false ||
            $capacidade < 1 ||
            $capacidade > 65535
        ) {
            $erros[] = 'A capacidade deve ser um número entre 1 e 65535.';
        }
    }

    // Validar status.
    $statusPermitidos = ['ativa', 'inativa', 'concluida'];

    if (!in_array($valores['status'], $statusPermitidos, true)) {
        $erros[] = 'Selecione um status válido.';
    }

    // Salvar no banco.
    if (empty($erros) && $pdo !== null) {
        try {
            $sql = "
                INSERT INTO turmas (
                    nome,
                    ano_letivo,
                    serie,
                    turno,
                    sala,
                    capacidade,
                    status
                ) VALUES (
                    :nome,
                    :ano_letivo,
                    :serie,
                    :turno,
                    :sala,
                    :capacidade,
                    :status
                )
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'nome' => $valores['nome'],
                'ano_letivo' => $ano,
                'serie' => $valores['serie'] !== ''
                    ? $valores['serie']
                    : null,
                'turno' => $valores['turno'],
                'sala' => $valores['sala'] !== ''
                    ? $valores['sala']
                    : null,
                'capacidade' => $capacidade,
                'status' => $valores['status']
            ]);

            // Renovar o token após o cadastro.
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            header(
                'Location: ' . BASE_URL .
                '/turmas/index.php?sucesso=cadastrada'
            );
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erros[] = 'Já existe uma turma com esse nome nesse ano letivo.';
            } else {
                $erros[] = 'Não foi possível cadastrar a turma. Tente novamente.';
            }
        }
    } elseif (empty($erros)) {
        $erros[] = 'A conexão com o banco de dados não está disponível.';
    }
}

require_once __DIR__ . '/../../views/layouts/header.php';
require_once __DIR__ . '/../../views/layouts/sidebar.php';
?>

<main class="content">

    <div class="page-header mb-4">
        <h1 class="h3 mb-1">Cadastrar turma</h1>
        <p class="text-secondary mb-0">
            Preencha as informações para cadastrar uma nova turma.
        </p>
    </div>

    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Verifique os seguintes pontos:</strong>

            <ul class="mb-0 mt-2">
                <?php foreach ($erros as $erro): ?>
                    <li><?= e($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <section class="dashboard-panel">

        <div class="dashboard-panel-header">
            <h2 class="h5 mb-0">Informações da turma</h2>
        </div>

        <div class="p-4">

            <form method="POST" action="">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_token']) ?>"
                >

                <div class="form-section-title mb-3">
                    Dados gerais
                </div>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="nome" class="form-label">
                            Nome da turma *
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="nome"
                            name="nome"
                            maxlength="80"
                            placeholder="Ex.: 1º Ano A"
                            value="<?= e($valores['nome']) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-3">
                        <label for="ano_letivo" class="form-label">
                            Ano letivo *
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            id="ano_letivo"
                            name="ano_letivo"
                            min="1901"
                            max="2155"
                            value="<?= e($valores['ano_letivo']) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-3">
                        <label for="serie" class="form-label">
                            Série
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="serie"
                            name="serie"
                            maxlength="50"
                            placeholder="Ex.: 1º ano do Ensino Médio"
                            value="<?= e($valores['serie']) ?>"
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="turno" class="form-label">
                            Turno *
                        </label>

                        <select
                            class="form-select"
                            id="turno"
                            name="turno"
                            required
                        >
                            <option value="manha"
                                <?= $valores['turno'] === 'manha' ? 'selected' : '' ?>>
                                Manhã
                            </option>

                            <option value="tarde"
                                <?= $valores['turno'] === 'tarde' ? 'selected' : '' ?>>
                                Tarde
                            </option>

                            <option value="noite"
                                <?= $valores['turno'] === 'noite' ? 'selected' : '' ?>>
                                Noite
                            </option>

                            <option value="integral"
                                <?= $valores['turno'] === 'integral' ? 'selected' : '' ?>>
                                Integral
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="sala" class="form-label">
                            Sala
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="sala"
                            name="sala"
                            maxlength="30"
                            placeholder="Ex.: Sala 05"
                            value="<?= e($valores['sala']) ?>"
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="capacidade" class="form-label">
                            Capacidade de alunos
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            id="capacidade"
                            name="capacidade"
                            min="1"
                            max="65535"
                            placeholder="Ex.: 35"
                            value="<?= e($valores['capacidade']) ?>"
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label">
                            Status *
                        </label>

                        <select
                            class="form-select"
                            id="status"
                            name="status"
                            required
                        >
                            <option value="ativa"
                                <?= $valores['status'] === 'ativa' ? 'selected' : '' ?>>
                                Ativa
                            </option>

                            <option value="inativa"
                                <?= $valores['status'] === 'inativa' ? 'selected' : '' ?>>
                                Inativa
                            </option>

                            <option value="concluida"
                                <?= $valores['status'] === 'concluida' ? 'selected' : '' ?>>
                                Concluída
                            </option>
                        </select>
                    </div>

                </div>

                <p class="text-secondary small mt-3">
                    * Campos obrigatórios. A capacidade pode ficar vazia
                    caso ainda não tenha sido definida.
                </p>

                <div class="form-actions d-flex justify-content-end gap-2 mt-4">

                    <a
                        href="<?= BASE_URL ?>/turmas/index.php"
                        class="btn btn-outline-secondary"
                    >
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Salvar turma
                    </button>

                </div>

            </form>

        </div>
    </section>

</main>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>
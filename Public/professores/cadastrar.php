
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
    'nome_completo' => '',
    'cpf' => '',
    'especialidade' => '',
    'email' => '',
    'telefone' => '',
    'status' => 'ativo'
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
        $valores[$campo] = trim(
            (string) ($_POST[$campo] ?? $valorPadrao)
        );
    }

    // Validar token de segurança.
    $token = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $erros[] = 'Sessão inválida. Atualize a página e tente novamente.';
    }

    // Validar nome.
    if ($valores['nome_completo'] === '') {
        $erros[] = 'O nome completo é obrigatório.';
    } elseif (strlen($valores['nome_completo']) > 150) {
        $erros[] = 'O nome deve ter no máximo 150 caracteres.';
    }

    // Validar CPF.
    $cpf = preg_replace('/\D/', '', $valores['cpf']);

    if ($valores['cpf'] !== '' && strlen($cpf) !== 11) {
        $erros[] = 'O CPF deve conter 11 números.';
    }

    // Validar RG.
    if (strlen($valores['especialidade']) > 100) {
     $erros[] = 'A especialidade deve ter no máximo 100 caracteres.';
    }

    // Validar e-mail.
    if ($valores['email'] !== '') {
        if (!filter_var($valores['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Informe um e-mail válido.';
        } elseif (strlen($valores['email']) > 150) {
            $erros[] = 'O e-mail deve ter no máximo 150 caracteres.';
        }
    }

    // Validar telefone.
    if (strlen($valores['telefone']) > 25) {
        $erros[] = 'O telefone deve ter no máximo 25 caracteres.';
    }

    // Validar status.
    $statusPermitidos = ['ativo', 'inativo'];

    if (!in_array($valores['status'], $statusPermitidos, true)) {
        $erros[] = 'Selecione um status válido.';
    }

    // Inserir no banco de dados.
    if (empty($erros) && $pdo !== null) {
        try {
            // Salvar o CPF somente com números e formatá-lo.
            $cpfBanco = null;

            if ($cpf !== '') {
                $cpfBanco = substr($cpf, 0, 3) . '.' .
                    substr($cpf, 3, 3) . '.' .
                    substr($cpf, 6, 3) . '-' .
                    substr($cpf, 9, 2);
            }

            $sql = "
                INSERT INTO professores (
                    nome_completo,
                    cpf,
                    especialidade,
                    email,
                    telefone,
                    status
                ) VALUES (
                    :nome_completo,
                    :cpf,
                    :especialidade,
                    :email,
                    :telefone,
                    :status
                )
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'nome_completo' => $valores['nome_completo'],
                'cpf' => $cpfBanco,
                'especialidade' => $valores['especialidade'] !== ''
                    ? $valores['especialidade']
                    : null,
                'email' => $valores['email'] !== ''
                    ? $valores['email']
                    : null,
                'telefone' => $valores['telefone'] !== ''
                    ? $valores['telefone']
                    : null,
                'status' => $valores['status']
            ]);

            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            header(
                'Location: ' . BASE_URL .
                '/professores/index.php?sucesso=cadastrado'
            );
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erros[] = 'Este CPF já está cadastrado para outro professor.';
            } else {
                error_log('Erro ao cadastrar professor: ' . $e->getMessage());

                $erros[] = 'Erro no cadastro: ' . $e->getMessage();
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
        <h1 class="h3 mb-1">Cadastrar professor</h1>
        <p class="text-secondary mb-0">
            Preencha os dados do novo professor.
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
            <h2 class="h5 mb-0">Informações do professor</h2>
        </div>

        <div class="p-4">

            <form method="POST" action="">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_token']) ?>"
                >

                <div class="form-section-title mb-3">
                    Dados pessoais
                </div>

                <div class="row g-3">

                    <div class="col-md-8">
                        <label for="nome_completo" class="form-label">
                            Nome completo *
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="nome_completo"
                            name="nome_completo"
                            maxlength="150"
                            value="<?= e($valores['nome_completo']) ?>"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="cpf" class="form-label">
                            CPF
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="cpf"
                            name="cpf"
                            placeholder="000.000.000-00"
                            maxlength="14"
                            value="<?= e($valores['cpf']) ?>"
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="especialidade" class="form-label">Especialidade</label>
                        <input
                            type="text"
                            class="form-control"
                            id="especialidade"
                            name="especialidade"
                            maxlength="100"
                            value="<?= e($valores['especialidade']) ?>"
                            placeholder="Ex.: Matemática, História, Informática"
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="email" class="form-label">
                            E-mail
                        </label>

                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            maxlength="150"
                            placeholder="professor@escola.com"
                            value="<?= e($valores['email']) ?>"
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="telefone" class="form-label">
                            Telefone
                        </label>

                        <input
                            type="tel"
                            class="form-control"
                            id="telefone"
                            name="telefone"
                            maxlength="25"
                            placeholder="(00) 00000-0000"
                            value="<?= e($valores['telefone']) ?>"
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
                            <option
                                value="ativo"
                                <?= $valores['status'] === 'ativo' ? 'selected' : '' ?>
                            >
                                Ativo
                            </option>

                            <option
                                value="inativo"
                                <?= $valores['status'] === 'inativo' ? 'selected' : '' ?>
                            >
                                Inativo
                            </option>
                        </select>
                    </div>

                </div>

                <p class="text-secondary small mt-3">
                    * Campos obrigatórios. CPF, RG, e-mail e telefone
                    podem ficar vazios se ainda não estiverem disponíveis.
                </p>

                <div class="form-actions d-flex justify-content-end gap-2 mt-4">

                    <a
                        href="<?= BASE_URL ?>/professores/index.php"
                        class="btn btn-outline-secondary"
                    >
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Salvar professor
                    </button>

                </div>

            </form>

        </div>
    </section>

</main>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>
```
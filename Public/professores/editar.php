
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
$professor = null;

try {
    $database = new Database();
    $pdo = $database->connect();

    $stmt = $pdo->prepare(
        'SELECT id, nome_completo, cpf, especialidade, email, telefone, status
         FROM professores
         WHERE id = :id'
    );

    $stmt->execute(['id' => $id]);
    $professor = $stmt->fetch();

    if (!$professor) {
        header('Location: ' . BASE_URL . '/professores/index.php?erro=nao_encontrado');
        exit;
    }

    foreach ($valores as $campo => $valor) {
        $valores[$campo] = (string) ($professor[$campo] ?? $valor);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($valores as $campo => $valor) {
            $valores[$campo] = trim((string) ($_POST[$campo] ?? ''));
        }

        $token = (string) ($_POST['csrf_token'] ?? '');

        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            $erros[] = 'A sessão do formulário expirou. Atualize a página e tente novamente.';
        }

        if ($valores['nome_completo'] === '') {
            $erros[] = 'O nome do professor é obrigatório.';
        } elseif (strlen($valores['nome_completo']) > 150) {
            $erros[] = 'O nome deve ter no máximo 150 caracteres.';
        }

        $cpfNumeros = preg_replace('/\D/', '', $valores['cpf']);

        if ($cpfNumeros !== '' && strlen($cpfNumeros) !== 11) {
            $erros[] = 'O CPF deve conter 11 números.';
        }

        if (strlen($valores['especialidade']) > 100) {
            $erros[] = 'A especialidade deve ter no máximo 100 caracteres.';
        }

        if (
            $valores['email'] !== '' &&
            (!filter_var($valores['email'], FILTER_VALIDATE_EMAIL) ||
             strlen($valores['email']) > 150)
        ) {
            $erros[] = 'Informe um e-mail válido com até 150 caracteres.';
        }

        if (strlen($valores['telefone']) > 25) {
            $erros[] = 'O telefone deve ter no máximo 25 caracteres.';
        }

        if (!in_array($valores['status'], ['ativo', 'inativo'], true)) {
            $erros[] = 'Selecione um status válido.';
        }

        if (empty($erros)) {
            $cpfFormatado = $cpfNumeros !== ''
                ? substr($cpfNumeros, 0, 3) . '.' .
                  substr($cpfNumeros, 3, 3) . '.' .
                  substr($cpfNumeros, 6, 3) . '-' .
                  substr($cpfNumeros, 9, 2)
                : null;

            $sql = 'UPDATE professores SET
                        nome_completo = :nome_completo,
                        cpf = :cpf,
                        especialidade = :especialidade,
                        email = :email,
                        telefone = :telefone,
                        status = :status
                    WHERE id = :id';

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'nome_completo' => $valores['nome_completo'],
                'cpf' => $cpfFormatado,
                'especialidade' => $valores['especialidade'] !== ''
                    ? $valores['especialidade']
                    : null,
                'email' => $valores['email'] !== ''
                    ? $valores['email']
                    : null,
                'telefone' => $valores['telefone'] !== ''
                    ? $valores['telefone']
                    : null,
                'status' => $valores['status'],
                'id' => $id
            ]);

            header('Location: ' . BASE_URL . '/professores/index.php?sucesso=atualizado');
            exit;
        }
    }
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        $erros[] = 'Este CPF já está cadastrado para outro professor.';
    } else {
        $erros[] = 'Não foi possível salvar as alterações. Verifique o banco de dados.';
    }
} catch (Exception $e) {
    $erros[] = 'Não foi possível conectar ao banco de dados.';
}

require_once __DIR__ . '/../../views/layouts/header.php';
require_once __DIR__ . '/../../views/layouts/sidebar.php';
?>

<div class="content">
    <div class="page-header">
        <div>
            <h1>Editar professor</h1>
            <p>Atualize as informações do professor cadastrado.</p>
        </div>

        <a
            href="<?= BASE_URL ?>/professores/index.php"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left"></i>
            Voltar
        </a>
    </div>

    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                <?php foreach ($erros as $erro): ?>
                    <li><?= e($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($professor): ?>
        <div class="dashboard-panel">
            <div class="dashboard-panel-header">
                <h2 class="h5 mb-0">Dados do professor</h2>
            </div>

            <div class="p-4">
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?= (int) $id ?>">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($_SESSION['csrf_token']) ?>"
                    >

                    <div class="form-section-title">Informações pessoais</div>

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
                            <label for="cpf" class="form-label">CPF</label>

                            <input
                                type="text"
                                class="form-control"
                                id="cpf"
                                name="cpf"
                                maxlength="14"
                                value="<?= e($valores['cpf']) ?>"
                                placeholder="000.000.000-00"
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="especialidade" class="form-label">
                                Especialidade
                            </label>

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

                        <div class="col-md-6">
                            <label for="email" class="form-label">E-mail</label>

                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                maxlength="150"
                                value="<?= e($valores['email']) ?>"
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="telefone" class="form-label">
                                Telefone
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="telefone"
                                name="telefone"
                                maxlength="25"
                                value="<?= e($valores['telefone']) ?>"
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label">Status *</label>

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

                    <div class="form-actions mt-4">
                        <a
                            href="<?= BASE_URL ?>/professores/index.php"
                            class="btn btn-outline-secondary"
                        >
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i>
                            Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>


<?php

require_once __DIR__ . '/../../config/config.php';

$currentPage = 'alunos';

include __DIR__ . '/../../views/layouts/header.php';
include __DIR__ . '/../../views/layouts/sidebar.php';

/*
|--------------------------------------------------------------------------
| ID DO ALUNO
|--------------------------------------------------------------------------
|
| Posteriormente vamos utilizar esse ID para buscar o aluno no banco.
|
*/

$id = $_GET['id'] ?? null;

/*
|--------------------------------------------------------------------------
| DADOS TEMPORÁRIOS
|--------------------------------------------------------------------------
|
| Estes dados são apenas para visualizar o formulário.
| Depois serão substituídos pelos dados vindos do MariaDB.
|
*/

$aluno = [
    'nome' => 'João da Silva',
    'data_nascimento' => '2008-05-15',
    'cpf' => '000.000.000-00',
    'rg' => '00.000.000-0',
    'sexo' => 'M',
    'email' => 'joao@email.com',
    'telefone' => '(12) 99999-9999',
    'cep' => '11600-000',
    'logradouro' => 'Rua Exemplo',
    'numero' => '123',
    'bairro' => 'Centro',
    'cidade' => 'São Sebastião',
    'estado' => 'SP',
    'turma' => '1A',
    'status' => 'ativo'
];

?>

<main class="content">

    <div class="container-fluid">

        <!-- CABEÇALHO -->

        <div class="page-header mb-4">

            <div>

                <h1 class="fw-bold mb-1">
                    Editar aluno
                </h1>

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


        <!-- FORMULÁRIO -->

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <div>

                    <h5 class="mb-1">
                        Dados do aluno
                    </h5>

                    <small class="text-muted">
                        Atualize os dados pessoais e acadêmicos
                    </small>

                </div>

                <?php if ($id): ?>

                    <span class="badge text-bg-secondary">
                        ID: <?= htmlspecialchars($id) ?>
                    </span>

                <?php endif; ?>

            </div>


            <div class="p-4">

                <form method="POST">


                    <!-- DADOS PESSOAIS -->

                    <h6 class="form-section-title">

                        <i class="bi bi-person me-2"></i>

                        Dados pessoais

                    </h6>


                    <div class="row g-3 mb-4">

                        <div class="col-md-8">

                            <label
                                for="nome"
                                class="form-label"
                            >
                                Nome completo
                            </label>

                            <input
                                type="text"
                                id="nome"
                                name="nome"
                                class="form-control"
                                value="<?= htmlspecialchars($aluno['nome']) ?>"
                                required
                            >

                        </div>


                        <div class="col-md-4">

                            <label
                                for="data_nascimento"
                                class="form-label"
                            >
                                Data de nascimento
                            </label>

                            <input
                                type="date"
                                id="data_nascimento"
                                name="data_nascimento"
                                class="form-control"
                                value="<?= htmlspecialchars($aluno['data_nascimento']) ?>"
                                required
                            >

                        </div>


                        <div class="col-md-4">

                            <label
                                for="cpf"
                                class="form-label"
                            >
                                CPF
                            </label>

                            <input
                                type="text"
                                id="cpf"
                                name="cpf"
                                class="form-control"
                                value="<?= htmlspecialchars($aluno['cpf']) ?>"
                            >

                        </div>


                        <div class="col-md-4">

                            <label
                                for="rg"
                                class="form-label"
                            >
                                RG
                            </label>

                            <input
                                type="text"
                                id="rg"
                                name="rg"
                                class="form-control"
                                value="<?= htmlspecialchars($aluno['rg']) ?>"
                            >

                        </div>


                        <div class="col-md-4">

                            <label
                                for="sexo"
                                class="form-label"
                            >
                                Sexo
                            </label>

                            <select
                                id="sexo"
                                name="sexo"
                                class="form-select"
                            >

                                <option value="">
                                    Selecione
                                </option>

                                <option
                                    value="M"
                                    <?= $aluno['sexo'] === 'M' ? 'selected' : '' ?>
                                >
                                    Masculino
                                </option>

                                <option
                                    value="F"
                                    <?= $aluno['sexo'] === 'F' ? 'selected' : '' ?>
                                >
                                    Feminino
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- CONTATO -->

                    <h6 class="form-section-title">

                        <i class="bi bi-telephone me-2"></i>

                        Contato

                    </h6>


                    <div class="row g-3 mb-4">

                        <div class="col-md-6">

                            <label
                                for="email"
                                class="form-label"
                            >
                                E-mail
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                value="<?= htmlspecialchars($aluno['email']) ?>"
                            >

                        </div>


                        <div class="col-md-6">

                            <label
                                for="telefone"
                                class="form-label"
                            >
                                Telefone
                            </label>

                            <input
                                type="text"
                                id="telefone"
                                name="telefone"
                                class="form-control"
                                value="<?= htmlspecialchars($aluno['telefone']) ?>"
                            >

                        </div>

                    </div>


                    <!-- ENDEREÇO -->

                    <h6 class="form-section-title">

                        <i class="bi bi-geo-alt me-2"></i>

                        Endereço

                    </h6>


                    <div class="row g-3 mb-4">

                        <div class="col-md-3">

                            <label
                                for="cep"
                                class="form-label"
                            >
                                CEP
                            </label>

                            <input
                                type="text"
                                id="cep"
                                name="cep"
                                class="form-control"
                                value="<?= htmlspecialchars($aluno['cep']) ?>"
                            >

                        </div>


                        <div class="col-md-7">

                            <label
                                for="logradouro"
                                class="form-label"
                            >
                                Logradouro
                            </label>

                            <input
                                type="text"
                                id="logradouro"
                                name="logradouro"
                                class="form-control"
                                value="<?= htmlspecialchars($aluno['logradouro']) ?>"
                            >

                        </div>


                        <div class="col-md-2">

                            <label
                                for="numero"
                                class="form-label"
                            >
                                Número
                            </label>

                            <input
                                type="text"
                                id="numero"
                                name="numero"
                                class="form-control"
                                value="<?= htmlspecialchars($aluno['numero']) ?>"
                            >

                        </div>


                        <div class="col-md-5">

                            <label
                                for="bairro"
                                class="form-label"
                            >
                                Bairro
                            </label>

                            <input
                                type="text"
                                id="bairro"
                                name="bairro"
                                class="form-control"
                                value="<?= htmlspecialchars($aluno['bairro']) ?>"
                            >

                        </div>


                        <div class="col-md-5">

                            <label
                                for="cidade"
                                class="form-label"
                            >
                                Cidade
                            </label>

                            <input
                                type="text"
                                id="cidade"
                                name="cidade"
                                class="form-control"
                                value="<?= htmlspecialchars($aluno['cidade']) ?>"
                            >

                        </div>


                        <div class="col-md-2">

                            <label
                                for="estado"
                                class="form-label"
                            >
                                UF
                            </label>

                            <select
                                id="estado"
                                name="estado"
                                class="form-select"
                            >

                                <option value="">
                                    UF
                                </option>

                                <option
                                    value="SP"
                                    <?= $aluno['estado'] === 'SP' ? 'selected' : '' ?>
                                >
                                    SP
                                </option>

                                <option
                                    value="RJ"
                                    <?= $aluno['estado'] === 'RJ' ? 'selected' : '' ?>
                                >
                                    RJ
                                </option>

                                <option
                                    value="MG"
                                    <?= $aluno['estado'] === 'MG' ? 'selected' : '' ?>
                                >
                                    MG
                                </option>

                                <option
                                    value="PR"
                                    <?= $aluno['estado'] === 'PR' ? 'selected' : '' ?>
                                >
                                    PR
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- DADOS ACADÊMICOS -->

                    <h6 class="form-section-title">

                        <i class="bi bi-mortarboard me-2"></i>

                        Dados acadêmicos

                    </h6>


                    <div class="row g-3 mb-4">

                        <div class="col-md-6">

                            <label
                                for="turma"
                                class="form-label"
                            >
                                Turma
                            </label>

                            <select
                                id="turma"
                                name="turma"
                                class="form-select"
                            >

                                <option value="">
                                    Selecione a turma
                                </option>

                                <option
                                    value="1A"
                                    <?= $aluno['turma'] === '1A' ? 'selected' : '' ?>
                                >
                                    1º Ano A
                                </option>

                                <option
                                    value="1B"
                                    <?= $aluno['turma'] === '1B' ? 'selected' : '' ?>
                                >
                                    1º Ano B
                                </option>

                            </select>

                        </div>


                        <div class="col-md-6">

                            <label
                                for="status"
                                class="form-label"
                            >
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                class="form-select"
                            >

                                <option
                                    value="ativo"
                                    <?= $aluno['status'] === 'ativo' ? 'selected' : '' ?>
                                >
                                    Ativo
                                </option>

                                <option
                                    value="inativo"
                                    <?= $aluno['status'] === 'inativo' ? 'selected' : '' ?>
                                >
                                    Inativo
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- BOTÕES -->

                    <div class="form-actions">

                        <a
                            href="<?= BASE_URL ?>/alunos/index.php"
                            class="btn btn-outline-secondary"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-check-lg me-2"></i>
                            Salvar alterações
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</main>


<?php

include __DIR__ . '/../../views/layouts/footer.php';

?>
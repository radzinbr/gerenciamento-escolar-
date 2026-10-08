<?php

require_once __DIR__ . '/../../config/config.php';

$currentPage = 'alunos';

include __DIR__ . '/../../views/layouts/header.php';
include __DIR__ . '/../../views/layouts/sidebar.php';

?>

<main class="content">

    <div class="container-fluid">

        <!-- CABEÇALHO -->

        <div class="page-header mb-4">

            <div>

                <h1 class="fw-bold mb-1">
                    Cadastrar aluno
                </h1>

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


        <!-- FORMULÁRIO -->

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <div>

                    <h5 class="mb-1">
                        Dados do aluno
                    </h5>

                    <small class="text-muted">
                        Informações pessoais e acadêmicas
                    </small>

                </div>

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
                                placeholder="Digite o nome completo"
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
                                placeholder="000.000.000-00"
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
                                placeholder="Digite o RG"
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

                                <option value="M">
                                    Masculino
                                </option>

                                <option value="F">
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
                                placeholder="aluno@email.com"
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
                                placeholder="(00) 00000-0000"
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
                                placeholder="00000-000"
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
                                placeholder="Rua, Avenida..."
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

                                <option value="SP">
                                    SP
                                </option>

                                <option value="RJ">
                                    RJ
                                </option>

                                <option value="MG">
                                    MG
                                </option>

                                <option value="PR">
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

                                <option>
                                    1º Ano A
                                </option>

                                <option>
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

                                <option value="ativo" selected>
                                    Ativo
                                </option>

                                <option value="inativo">
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
                            Cadastrar aluno
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
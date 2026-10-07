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
                    Alunos
                </h1>

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


        <!-- FILTROS -->
        <div class="dashboard-panel mb-4">

            <div class="p-3">

                <form>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                Buscar aluno
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>

                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Nome ou matrícula"
                                >

                            </div>

                        </div>


                        <div class="col-md-3">

                            <label class="form-label">
                                Turma
                            </label>

                            <select class="form-select">

                                <option selected>
                                    Todas as turmas
                                </option>

                                <option>
                                    1º Ano A
                                </option>

                                <option>
                                    1º Ano B
                                </option>

                                <option>
                                    1º Ano c
                                </option>

                                <option>
                                    2º Ano a
                                </option>
                                
                                <option>
                                    2º Ano B
                                </option>

                                <option>
                                    2º Ano C
                                </option>

                            </select>

                        </div>


                        <div class="col-md-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select class="form-select">

                                <option selected>
                                    Todos
                                </option>

                                <option>
                                    Ativo
                                </option>

                                <option>
                                    Inativo
                                </option>

                            </select>

                        </div>

                    </div>

                </form>

            </div>

        </div>


        <!-- TABELA -->
        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <div>

                    <h5 class="mb-1">
                        Alunos cadastrados
                    </h5>

                    <small class="text-muted">
                        Lista de alunos do sistema
                    </small>

                </div>

                <span class="badge text-bg-secondary">
                    0 alunos
                </span>

            </div>


            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead>

                        <tr>

                            <th>
                                Matrícula
                            </th>

                            <th>
                                Nome
                            </th>

                            <th>
                                Turma
                            </th>

                            <th>
                                Data de nascimento
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="text-end">
                                Ações
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <!--
                            Posteriormente os alunos serão
                            carregados através do banco de dados.
                        -->

                        <tr>

                            <td colspan="6">

                                <div class="empty-state">

                                    <i class="bi bi-people"></i>

                                    <h6>
                                        Nenhum aluno cadastrado
                                    </h6>

                                    <p class="text-muted mb-3">
                                        Comece cadastrando o primeiro aluno.
                                    </p>

                                    <a
                                        href="<?= BASE_URL ?>/alunos/cadastrar.php"
                                        class="btn btn-primary btn-sm"
                                    >
                                        <i class="bi bi-person-plus me-1"></i>
                                        Cadastrar primeiro aluno
                                    </a>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</main>


<?php

include __DIR__ . '/../../views/layouts/footer.php';

?>
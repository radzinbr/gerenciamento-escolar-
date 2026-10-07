<?php

require_once __DIR__ . '/../config/config.php';

$currentPage = "inicio";

include __DIR__ . '/../views/layouts/header.php';
include __DIR__ . '/../views/layouts/sidebar.php';

?>

<main class="content">

    <div class="container-fluid">

        <!-- Cabecalho -->

        <div class="dashboard-header mb-4">

            <div>

                <h1 class="fw-bold mb-1">
                    Dashboard
                </h1>

                <p class="text-muted mb-0">
                    Visão geral do sistema escolar.
                </p>

            </div>

            <div>

                <span class="text-muted">

                    <i class="bi bi-calendar3 me-1"></i>

                    <?= date('d/m/Y') ?>

                </span>

            </div>

        </div>


        <!-- Cards de informacoes -->

        <div class="row g-4 mb-4">

            <!-- ALUNOS -->
            <div class="col-md-6 col-xl-3">

                <div class="dashboard-card">

                    <div class="dashboard-card-icon bg-primary">

                        <i class="bi bi-people-fill"></i>

                    </div>

                    <div>

                        <span class="dashboard-card-title">
                            Alunos
                        </span>

                        <h3 class="dashboard-card-value">
                            0
                        </h3>

                        <small class="text-muted">
                            Total de alunos cadastrados
                        </small>

                    </div>

                </div>

            </div>


            <!-- Professores -->
            <div class="col-md-6 col-xl-3">

                <div class="dashboard-card">

                    <div class="dashboard-card-icon bg-success">

                        <i class="bi bi-person-badge"></i>

                    </div>

                    <div>

                        <span class="dashboard-card-title">
                            Professores
                        </span>

                        <h3 class="dashboard-card-value">
                            0
                        </h3>

                        <small class="text-muted">
                            Total de professores cadastrados
                        </small>

                    </div>

                </div>

            </div>


            <!-- Turmas -->
            <div class="col-md-6 col-xl-3">

                <div class="dashboard-card">

                    <div class="dashboard-card-icon bg-warning">

                        <i class="bi bi-building"></i>

                    </div>

                    <div>

                        <span class="dashboard-card-title">
                            Turmas
                        </span>

                        <h3 class="dashboard-card-value">
                            0
                        </h3>

                        <small class="text-muted">
                            Total de turmas cadastradas
                        </small>

                    </div>

                </div>

            </div>


            <!-- Disciplinas -->
            <div class="col-md-6 col-xl-3">

                <div class="dashboard-card">

                    <div class="dashboard-card-icon bg-danger">

                        <i class="bi bi-book"></i>

                    </div>

                    <div>

                        <span class="dashboard-card-title">
                            Disciplinas
                        </span>

                        <h3 class="dashboard-card-value">
                            0
                        </h3>

                        <small class="text-muted">
                            Total de disciplinas cadastradas
                        </small>

                    </div>

                </div>

            </div>

        </div>


        <!-- Frequencia -->

        <div class="row g-4">

            <!-- Frequencia -->
            <div class="col-lg-8">

                <div class="dashboard-panel">

                    <div class="dashboard-panel-header">

                        <div>

                            <h5 class="mb-1">
                                Frequência dos alunos
                            </h5>

                            <small class="text-muted">
                                Visão geral da frequência dos alunos
                            </small>

                        </div>

                        <button class="btn btn-sm btn-outline-primary">
                            Ver relatório
                        </button>

                    </div>


                    <div class="frequency-placeholder">

                        <i class="bi bi-bar-chart-fill"></i>

                        <p>
                            Gráfico de frequência dos alunos
                        </p>

                        <small>
                            Em breve, você poderá visualizar o gráfico
                            de frequência dos alunos.
                        </small>

                    </div>

                </div>

            </div>


            <!-- Ações Rápidas -->
            <div class="col-lg-4">

                <div class="dashboard-panel">

                    <div class="dashboard-panel-header">

                        <div>

                            <h5 class="mb-1">
                                Ações rápidas
                            </h5>

                            <small class="text-muted">
                                Ações rápidas para agilizar seu trabalho
                            </small>

                        </div>

                    </div>


                    <div class="quick-actions">

                        <!-- Cadastrar Aluno -->
                        <a
                            href="<?= BASE_URL ?>/alunos/cadastrar.php"
                            class="quick-action"
                        >

                            <i class="bi bi-person-plus"></i>

                            <span>
                                Cadastrar aluno
                            </span>

                            <i class="bi bi-chevron-right"></i>

                        </a>


                        <!-- Cadastrar Professor -->
                        <a
                            href="<?= BASE_URL ?>/professores/cadastrar.php"
                            class="quick-action"
                        >

                            <i class="bi bi-person-plus"></i>

                            <span>
                                Cadastrar professor
                            </span>

                            <i class="bi bi-chevron-right"></i>

                        </a>


                        <!-- Criar Turma -->
                        <a
                            href="<?= BASE_URL ?>/turmas/cadastrar.php"
                            class="quick-action"
                        >

                            <i class="bi bi-building-add"></i>

                            <span>
                                Criar turma
                            </span>

                            <i class="bi bi-chevron-right"></i>

                        </a>


                        <!-- Fazer Chamada -->
                        <a
                            href="<?= BASE_URL ?>/chamadas/index.php"
                            class="quick-action"
                        >

                            <i class="bi bi-calendar-check"></i>

                            <span>
                                Fazer chamada
                            </span>

                            <i class="bi bi-chevron-right"></i>

                        </a>


                        <!-- Lançar Notas -->
                        <a
                            href="<?= BASE_URL ?>/avaliacoes/index.php"
                            class="quick-action"
                        >

                            <i class="bi bi-journal-check"></i>

                            <span>
                                Lançar notas
                            </span>

                            <i class="bi bi-chevron-right"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>


        <!-- Chamadas Recentes -->

        <div class="row g-4 mt-1">

            <div class="col-12">

                <div class="dashboard-panel">

                    <div class="dashboard-panel-header">

                        <div>

                            <h5 class="mb-1">
                                Chamadas recentes
                            </h5>

                            <small class="text-muted">
                                Últimas chamadas realizadas
                            </small>

                        </div>

                        <a
                            href="<?= BASE_URL ?>/chamadas/index.php"
                            class="btn btn-sm btn-outline-primary"
                        >
                            Ver todas
                        </a>

                    </div>


                    <div class="table-responsive">

                        <table class="table align-middle mb-0">

                            <thead>

                                <tr>

                                    <th>
                                        Turma
                                    </th>

                                    <th>
                                        Professor
                                    </th>

                                    <th>
                                        Data
                                    </th>

                                    <th>
                                        Presenças
                                    </th>

                                    <th>
                                        Faltas
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <tr>

                                    <td
                                        colspan="6"
                                        class="text-center py-5"
                                    >

                                        <i class="bi bi-calendar-x fs-3 text-muted"></i>

                                        <p class="text-muted mt-2 mb-0">
                                            Nenhuma chamada registrada.
                                        </p>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>


<?php

include __DIR__ . '/../views/layouts/footer.php';

?>
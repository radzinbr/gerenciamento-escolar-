<?php

require_once __DIR__ . '/../../config/config.php';

$currentPage = 'alunos';

include __DIR__ . '/../../views/layouts/header.php';
include __DIR__ . '/../../views/layouts/sidebar.php';


// Dados temporários para testar a interface.
// Posteriormente serão substituídos pelos dados extraídos do PDF.

$alunosEncontrados = [

    [
        'nome' => 'João da Silva',
        'data_nascimento' => '15/05/2008',
        'cpf' => '000.000.000-00',
        'turma' => '1º Ano A'
    ],

    [
        'nome' => 'Maria Oliveira',
        'data_nascimento' => '22/08/2008',
        'cpf' => '111.111.111-11',
        'turma' => '1º Ano A'
    ],

    [
        'nome' => 'Pedro Santos',
        'data_nascimento' => '03/02/2009',
        'cpf' => '222.222.222-22',
        'turma' => '1º Ano B'
    ]

];

?>

<main class="content">

    <div class="container-fluid">

        <!-- CABEÇALHO -->

        <div class="page-header mb-4">

            <div>

                <h1 class="fw-bold mb-1">
                    Revisar alunos
                </h1>

                <p class="text-muted mb-0">
                    Confira os dados identificados no arquivo antes de continuar.
                </p>

            </div>

            <a
                href="<?= BASE_URL ?>/alunos/importar.php"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-2"></i>
                Voltar
            </a>

        </div>


        <!-- ETAPAS -->

        <div class="import-steps mb-4">

            <div class="import-step completed">

                <div class="import-step-number">
                    <i class="bi bi-check"></i>
                </div>

                <div>

                    <strong>
                        Enviar PDF
                    </strong>

                    <small>
                        Arquivo enviado
                    </small>

                </div>

            </div>


            <div class="import-step active">

                <div class="import-step-number">
                    2
                </div>

                <div>

                    <strong>
                        Revisar
                    </strong>

                    <small>
                        Conferir alunos encontrados
                    </small>

                </div>

            </div>


            <div class="import-step">

                <div class="import-step-number">
                    3
                </div>

                <div>

                    <strong>
                        Confirmar
                    </strong>

                    <small>
                        Finalizar importação
                    </small>

                </div>

            </div>

        </div>


        <!-- RESUMO -->

        <div class="row g-3 mb-4">

            <div class="col-md-4">

                <div class="import-summary-card">

                    <div class="import-summary-icon">
                        <i class="bi bi-people"></i>
                    </div>

                    <div>

                        <small>
                            Alunos encontrados
                        </small>

                        <strong>
                            <?= count($alunosEncontrados) ?>
                        </strong>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="import-summary-card">

                    <div class="import-summary-icon success">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <div>

                        <small>
                            Prontos para importar
                        </small>

                        <strong>
                            <?= count($alunosEncontrados) ?>
                        </strong>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="import-summary-card">

                    <div class="import-summary-icon warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>

                    <div>

                        <small>
                            Possíveis duplicados
                        </small>

                        <strong>
                            0
                        </strong>

                    </div>

                </div>

            </div>

        </div>


        <!-- LISTA -->

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <div>

                    <h5 class="mb-1">
                        Alunos identificados
                    </h5>

                    <small class="text-muted">
                        Revise os dados antes de continuar.
                    </small>

                </div>

                <span class="badge text-bg-primary">

                    <?= count($alunosEncontrados) ?>

                    encontrados

                </span>

            </div>


            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead>

                        <tr>

                            <th style="width: 50px;">

                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="selecionarTodos"
                                    checked
                                >

                            </th>

                            <th>
                                Nome
                            </th>

                            <th>
                                Data de nascimento
                            </th>

                            <th>
                                CPF
                            </th>

                            <th>
                                Turma
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($alunosEncontrados as $index => $aluno): ?>

                            <tr>

                                <td>

                                    <input
                                        type="checkbox"
                                        class="form-check-input aluno-checkbox"
                                        name="alunos[]"
                                        value="<?= $index ?>"
                                        checked
                                    >

                                </td>


                                <td>

                                    <strong>
                                        <?= htmlspecialchars($aluno['nome']) ?>
                                    </strong>

                                </td>


                                <td>
                                    <?= htmlspecialchars($aluno['data_nascimento']) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($aluno['cpf']) ?>
                                </td>


                                <td>

                                    <span class="badge text-bg-light">

                                        <?= htmlspecialchars($aluno['turma']) ?>

                                    </span>

                                </td>


                                <td>

                                    <span class="badge text-bg-success">

                                        <i class="bi bi-check-circle me-1"></i>

                                        Pronto

                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


            <!-- RODAPÉ -->

            <div class="import-footer">

                <div>

                    <strong id="contadorSelecionados">
                        <?= count($alunosEncontrados) ?>
                    </strong>

                    <span class="text-muted">
                        aluno(s) selecionado(s)
                    </span>

                </div>


                <div class="d-flex gap-2">

                    <a
                        href="<?= BASE_URL ?>/alunos/importar.php"
                        class="btn btn-outline-secondary"
                    >
                        Cancelar
                    </a>

                    <a
                        href="<?= BASE_URL ?>/alunos/importar-confirmar.php"
                        class="btn btn-primary"
                    >
                        Continuar
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>

                </div>

            </div>

        </div>

    </div>

</main>


<?php

include __DIR__ . '/../../views/layouts/footer.php';

?>
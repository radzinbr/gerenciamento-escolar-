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
                    Importar alunos
                </h1>

                <p class="text-muted mb-0">
                    Cadastre vários alunos através de um arquivo PDF.
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


        <!-- ETAPAS -->

        <div class="import-steps mb-4">

            <div class="import-step active">

                <div class="import-step-number">
                    1
                </div>

                <div>
                    <strong>
                        Enviar PDF
                    </strong>

                    <small>
                        Selecione o arquivo
                    </small>
                </div>

            </div>


            <div class="import-step">

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


        <!-- UPLOAD -->

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <div>

                    <h5 class="mb-1">
                        Enviar arquivo PDF
                    </h5>

                    <small class="text-muted">
                        Selecione um documento contendo os dados dos alunos.
                    </small>

                </div>

            </div>


            <div class="p-4">

                <form
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <div class="pdf-upload-area">

                        <div class="pdf-upload-icon">

                            <i class="bi bi-file-earmark-pdf"></i>

                        </div>


                        <h5>
                            Selecione o arquivo PDF
                        </h5>


                        <p class="text-muted">
                            Arraste o arquivo para esta área ou clique
                            para selecioná-lo.
                        </p>


                        <label
                            for="arquivo_pdf"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-upload me-2"></i>
                            Selecionar PDF
                        </label>


                        <input
                            type="file"
                            id="arquivo_pdf"
                            name="arquivo_pdf"
                            class="d-none"
                            accept=".pdf,application/pdf"
                        >


                        <div
                            id="arquivoSelecionado"
                            class="text-muted mt-3"
                        >
                            Nenhum arquivo selecionado.
                        </div>

                    </div>


                    <!-- INFORMAÇÕES -->

                    <div class="alert alert-info mt-4">

                        <div class="d-flex gap-2">

                            <i class="bi bi-info-circle fs-5"></i>

                            <div>

                                <strong>
                                    Como funciona?
                                </strong>

                                <p class="mb-0 mt-1">
                                    O sistema irá analisar o PDF e identificar
                                    os possíveis alunos encontrados. Antes de
                                    cadastrar qualquer aluno, você poderá
                                    revisar e selecionar quais registros deseja
                                    importar.
                                </p>

                            </div>

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
                            id="btnContinuar"
                            disabled
                        >
                            Continuar
                            <i class="bi bi-arrow-right ms-2"></i>
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
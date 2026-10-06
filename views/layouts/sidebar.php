<?php

$currentPage = $currentPage ?? 'inicio';

?>

<aside class="sidebar">

    <!-- Logo -->
    <div class="sidebar-header">

        <div class="sidebar-logo">
            <i class="bi bi-mortarboard-fill"></i>
        </div>

        <div>
            <h5>SIGE</h5>
            <small>Gestão acadêmica</small>
        </div>

    </div>


    <!-- Menu -->
    <nav class="sidebar-menu">

        <span class="menu-title">
            PRINCIPAL
        </span>

        <a
            href="<?= BASE_URL ?>/index.php"
            class="menu-item <?= $currentPage === 'inicio' ? 'active' : '' ?>"
        >
            <i class="bi bi-house"></i>
            <span>Início</span>
        </a>


        <span class="menu-title">
            GESTÃO
        </span>

        <a
            href="<?= BASE_URL ?>/alunos/index.php"
            class="menu-item <?= $currentPage === 'alunos' ? 'active' : '' ?>"
        >
            <i class="bi bi-people"></i>
            <span>Alunos</span>
        </a>

        <a
            href="<?= BASE_URL ?>/professores/index.php"
            class="menu-item <?= $currentPage === 'professores' ? 'active' : '' ?>"
        >
            <i class="bi bi-person-badge"></i>
            <span>Professores</span>
        </a>

        <a
            href="<?= BASE_URL ?>/turmas/index.php"
            class="menu-item <?= $currentPage === 'turmas' ? 'active' : '' ?>"
        >
            <i class="bi bi-building"></i>
            <span>Turmas</span>
        </a>

        <a
            href="<?= BASE_URL ?>/disciplinas/index.php"
            class="menu-item <?= $currentPage === 'disciplinas' ? 'active' : '' ?>"
        >
            <i class="bi bi-book"></i>
            <span>Disciplinas</span>
        </a>


        <span class="menu-title">
            ACADÊMICO
        </span>

        <a
            href="<?= BASE_URL ?>/chamadas/index.php"
            class="menu-item <?= $currentPage === 'chamadas' ? 'active' : '' ?>"
        >
            <i class="bi bi-calendar-check"></i>
            <span>Chamadas</span>
        </a>

        <a
            href="<?= BASE_URL ?>/presencas/index.php"
            class="menu-item <?= $currentPage === 'presencas' ? 'active' : '' ?>"
        >
            <i class="bi bi-check2-square"></i>
            <span>Presenças / Faltas</span>
        </a>

        <a
            href="<?= BASE_URL ?>/avaliacoes/index.php"
            class="menu-item <?= $currentPage === 'avaliacoes' ? 'active' : '' ?>"
        >
            <i class="bi bi-journal-text"></i>
            <span>Avaliações / Notas</span>
        </a>

        <a
            href="<?= BASE_URL ?>/matriculas/index.php"
            class="menu-item <?= $currentPage === 'matriculas' ? 'active' : '' ?>"
        >
            <i class="bi bi-mortarboard"></i>
            <span>Matrículas</span>
        </a>


        <span class="menu-title">
            SISTEMA
        </span>

        <a
            href="<?= BASE_URL ?>/relatorios/index.php"
            class="menu-item <?= $currentPage === 'relatorios' ? 'active' : '' ?>"
        >
            <i class="bi bi-bar-chart"></i>
            <span>Relatórios</span>
        </a>

        <a
            href="<?= BASE_URL ?>/configuracoes/index.php"
            class="menu-item <?= $currentPage === 'configuracoes' ? 'active' : '' ?>"
        >
            <i class="bi bi-gear"></i>
            <span>Configurações</span>
        </a>

    </nav>


    <!-- Usuário -->
    <div class="sidebar-user">

        <div class="user-avatar">
            R
        </div>

        <div class="user-info">

            <strong>Radrin</strong>

            <small>
                Administrador
            </small>

        </div>

    </div>

</aside>
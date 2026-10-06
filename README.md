# Sistema Escolar Web

Sistema web para gerenciamento escolar desenvolvido com Node.js, Express e MySQL.

O projeto permite controle de alunos, turmas, presença e notas através de uma API REST integrada a um frontend responsivo.

---

# Objetivo

Criar uma aplicação simples, organizada e escalável para:

- gerenciamento de alunos;
- controle de presença;
- lançamento de notas;
- cálculo de médias;
- integração frontend + backend.

---

# Tecnologias Utilizadas

## Backend
- PHP
- mariaDB

## Frontend
- HTML5
- CSS3
- JavaScript
- Bootstrap

## Ferramentas
- Git
- GitHub

---

# Funcionalidades

## Alunos
- cadastrar alunos;
- editar alunos;
- excluir alunos;
- listar alunos;
- buscar alunos por turma.

## Turmas
- gerenciamento de turmas;
- vínculo entre aluno e turma.

## Presença
- registro de presença;
- controle de faltas;
- cálculo de frequência.

## Notas
- lançamento de avaliações;
- cálculo automático de médias;
- gerenciamento de P1, P2 e trabalhos.



---



# PADRÃO DE COMMITS

- Todos os commits devem seguir o padrão:

- tipo: descrição

## Exemplos:

- feat: adiciona cadastro de professores
- feat: implementa tela de turmas
- fix: corrige erro na exclusão de alunos
- refactor: reorganiza serviço de professores
- docs: atualiza documentação do projeto
- test: adiciona testes para cadastro de alunos
- chore: atualiza dependências do projeto
 
## Regras:
- Utilizar letras minúsculas no tipo.
- Escrever a descrição de forma curta e objetiva.
- Utilizar verbo no presente.
- Não colocar ponto final.
- Evitar commits genéricos como "update", "alterações" ou "coisas".
---

---

# Estrutura do Projeto

```txt
sistema/
│
│── public/
│   ├── Components/
│   │     ├── footer.html
│   │     └──navbar.html
│   │
│   │── css/
│   │   └── style.css
│   │
│   │── js/
│   │   ├── alunos.js
│   │   ├── utils.js
│   │   └── api.js
│   │
│   └── pages/
│       ├── alunos.html
│       ├── dashboard.html
│       └── login.html
│
├── Server/
│     ├── controllers/
│     ├── database/
│     │    └──db.js
│     ├── models/
│     ├── routes/
│     │     └──alunosRoutes.js
│     └── app.js
│
│── package.json
└── README.md
```

---

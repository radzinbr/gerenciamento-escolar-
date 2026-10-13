document.addEventListener('DOMContentLoaded', () => {

    console.log('Sistema Escolar carregado.');

});

document.addEventListener('DOMContentLoaded', () => {

    console.log('Sistema Escolar carregado.');


    /*
    |--------------------------------------------------------------------------
    | IMPORTAÇÃO DE PDF
    |--------------------------------------------------------------------------
    */

    const inputPdf = document.getElementById('arquivo_pdf');
    const arquivoSelecionado = document.getElementById('arquivoSelecionado');
    const btnContinuar = document.getElementById('btnContinuar');

    if (inputPdf) {

        inputPdf.addEventListener('change', () => {

            const arquivo = inputPdf.files[0];

            if (!arquivo) {

                arquivoSelecionado.textContent =
                    'Nenhum arquivo selecionado.';

                btnContinuar.disabled = true;

                return;
            }


            if (arquivo.type !== 'application/pdf') {

                arquivoSelecionado.textContent =
                    'Selecione um arquivo PDF válido.';

                btnContinuar.disabled = true;

                inputPdf.value = '';

                return;
            }


            arquivoSelecionado.innerHTML = `
                <i class="bi bi-file-earmark-pdf me-1"></i>
                ${arquivo.name}
            `;

            btnContinuar.disabled = false;

        });

    }

});

/*
|--------------------------------------------------------------------------
| SELECIONAR TODOS OS ALUNOS
|--------------------------------------------------------------------------
*/

const selecionarTodos = document.getElementById('selecionarTodos');
const alunoCheckboxes = document.querySelectorAll('.aluno-checkbox');
const contadorSelecionados = document.getElementById('contadorSelecionados');

if (selecionarTodos) {

    selecionarTodos.addEventListener('change', () => {

        alunoCheckboxes.forEach((checkbox) => {

            checkbox.checked = selecionarTodos.checked;

        });

        atualizarContador();

    });


    alunoCheckboxes.forEach((checkbox) => {

        checkbox.addEventListener('change', atualizarContador);

    });


    function atualizarContador() {

        const selecionados =
            document.querySelectorAll('.aluno-checkbox:checked').length;

        contadorSelecionados.textContent = selecionados;

        selecionarTodos.checked =
            selecionados === alunoCheckboxes.length;

    }

}
document.addEventListener("DOMContentLoaded", function () {
    const checkboxes = document.querySelectorAll("input[name='secoes[]']");
    const sections = document.querySelectorAll(".section");

    // Ocultar todas as seções no carregamento
    sections.forEach(section => {
        section.style.display = "none";
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            const sectionId = `seção-${this.id}`;
            const section = document.getElementById(sectionId);
            if (section) {
                section.style.display = this.checked ? 'block' : 'none';
            }
        });
    });

    // Validação do formulário antes do envio
    document.getElementById("formCadastro").addEventListener("submit", function (event) {
        const email = document.getElementById("email").value;
        const confirmarEmail = document.getElementById("confirmar-email").value.trim();
        const senha = document.getElementById("senha").value;
        const confirmarSenha = document.getElementById("confirmar-senha").value.trim();
        const checkboxesMarcados = document.querySelectorAll('input[name="secoes[]"]:checked').length;

        if (email !== confirmarEmail) {
            event.preventDefault();
            Swal.fire("Erro", "Os e-mails não coincidem.", "error");
        } else if (senha !== confirmarSenha) {
            event.preventDefault();
            Swal.fire("Erro", "As senhas não coincidem.", "error");
        } else if (checkboxesMarcados === 0) {
            event.preventDefault();
            Swal.fire("Erro", "Selecione pelo menos uma classificação.", "error");
        }
    });
});

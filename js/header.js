document.addEventListener("DOMContentLoaded", function() {
    console.log("Header JS carregado!");

    setTimeout(() => {
        const logoutBtn = document.getElementById("logoutBtn");

        if (logoutBtn) {
            console.log("Botão de logout encontrado!");

            logoutBtn.addEventListener("click", function() {
                Swal.fire({
                    title: "Tem certeza?",
                    text: "Você será deslogado da sua conta.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Sim, sair!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log("Usuário confirmou logout!");
                        window.location.href = "logout.php";
                    } else {
                        console.log("Usuário cancelou o logout.");
                    }
                });
            });

        } else {
            console.warn("Botão de logout não encontrado.");
        }
    }, 500); // Pequeno atraso para garantir renderização
});

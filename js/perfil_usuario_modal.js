document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("modal-edicao");
  const btnAbrir = document.querySelector(".btn-familiar");
  const btnFechar = document.querySelector(".fechar");
  const form = modal.querySelector("form");

  btnAbrir.addEventListener("click", () => {
    modal.style.display = "flex";
    modal.classList.add("fade-in");
  });

  btnFechar.addEventListener("click", () => fecharModal());

  window.addEventListener("click", (e) => {
    if (e.target === modal) fecharModal();
  });

  function fecharModal() {
    modal.classList.remove("fade-in");
    modal.classList.add("fade-out");
    setTimeout(() => {
      modal.style.display = "none";
      modal.classList.remove("fade-out");
    }, 300);
  }

  // Submissão via AJAX
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch("atualiza_perfil_ajax.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.status === "ok") {
          Swal.fire("Sucesso", data.mensagem, "success").then(() => {
            window.location.reload();
          });
        } else {
          Swal.fire("Erro", data.mensagem, "error");
        }
      })
      .catch(() => {
        Swal.fire("Erro", "Erro inesperado ao tentar salvar!", "error");
      });
  });
});

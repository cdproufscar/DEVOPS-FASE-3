document.addEventListener("DOMContentLoaded", () => {
    renderizaComponentes();
  });
  
  function abrirCadastroComponente() {
    window.open("cadastro_componente.php", "_blank");
  }
  
  // Renderiza componentes salvos no localStorage
  function renderizaComponentes() {
    const componentesSalvos = JSON.parse(localStorage.getItem("componentes_adicionados")) || [];
    const tbody = document.getElementById("componentes-lista");
    const inputHidden = document.getElementById("componentes_selecionados");
  
    tbody.innerHTML = "";
    const ids = [];
  
    componentesSalvos.forEach((comp) => {
      const tr = document.createElement("tr");
  
      const tdId = document.createElement("td");
      tdId.textContent = comp.id_componente;
  
      const tdNome = document.createElement("td");
      tdNome.textContent = comp.nome;
  
      const tdLink = document.createElement("td");
      const link = document.createElement("a");
      link.href = `detalhar_componente.php?id=${comp.id_componente}`;
      link.target = "_blank";
      link.textContent = "Ver Detalhes";
      tdLink.appendChild(link);
  
      tr.appendChild(tdId);
      tr.appendChild(tdNome);
      tr.appendChild(tdLink);
      tbody.appendChild(tr);
  
      ids.push(comp.id_componente);
    });
  
    inputHidden.value = JSON.stringify(ids);
  }
  

// Abrir modal
function abrirModal(id) {
    document.getElementById(id).classList.add('show');
  }
  
  // Fechar modal
  function fecharModal(id) {
    document.getElementById(id).classList.remove('show');
  }
  
  // Atualizar checkboxes no modal de passos
  function atualizarOpcoesPasso() {
    const materiais = document.querySelectorAll("#listaMateriais tr");
    const ferramentas = document.querySelectorAll("#listaFerramentas tr");
  
    const boxMat = document.getElementById("passo_materiais");
    const boxFer = document.getElementById("passo_ferramentas");
  
    boxMat.innerHTML = "";
    boxFer.innerHTML = "";
  
    materiais.forEach(row => {
      const nome = row.children[1]?.textContent;
      const inputHidden = row.querySelector("input[name^='materiais']");
      const idHidden = inputHidden?.value;
  
      if (nome && idHidden) {
        boxMat.innerHTML += `<label><input type="checkbox" value="${idHidden}"> ${nome}</label>`;
      }
    });
  
    ferramentas.forEach(row => {
      const nome = row.children[1]?.textContent;
      const inputHidden = row.querySelector("input[name^='ferramentas']");
      const idHidden = inputHidden?.value;
  
      if (nome && idHidden) {
        boxFer.innerHTML += `<label><input type="checkbox" value="${idHidden}"> ${nome}</label>`;
      }
    });
  }
  
  // Reorganiza numeração de materiais
  function atualizarNumeracaoMateriais() {
    document.querySelectorAll("#listaMateriais tr").forEach((tr, i) => {
      tr.children[0].textContent = "Material " + (i + 1);
    });
  }
  
  // Reorganiza numeração de ferramentas
  function atualizarNumeracaoFerramentas() {
    document.querySelectorAll("#listaFerramentas tr").forEach((tr, i) => {
      tr.children[0].textContent = "Ferramenta " + (i + 1);
    });
  }
  
  // Adiciona Material
  function adicionarMaterial() {
    const select = document.getElementById("select_material");
    const nome = select.options[select.selectedIndex]?.text;
    const idMaterial = select.value;
    const descricao = select.options[select.selectedIndex]?.dataset.descricao || "";
    const quantidade = document.getElementById("qtd_material").value;
    const unidade = document.getElementById("unidade_material").value;
  
    if (!idMaterial || !quantidade || !unidade) {
      alert("Preencha todos os campos do material.");
      return;
    }
  
    const id = Date.now();
    const tabela = document.getElementById("listaMateriais");
    const form = document.querySelector("form");
  
    const linha = document.createElement("tr");
    linha.innerHTML = `
      <td></td>
      <td>${nome}</td>
      <td>${quantidade}</td>
      <td>${unidade}</td>
      <td>${descricao}</td>
      <td>
        <button type="button" onclick="this.closest('tr').remove(); atualizarNumeracaoMateriais(); atualizarOpcoesPasso();">🗑</button>
      </td>
      <input type="hidden" name="materiais[${id}][id]" value="${idMaterial}">
      <input type="hidden" name="materiais[${id}][quantidade]" value="${quantidade}">
      <input type="hidden" name="materiais[${id}][unidade]" value="${unidade}">
    `;
    tabela.appendChild(linha);
  
    atualizarNumeracaoMateriais();
    atualizarOpcoesPasso();
  
    select.selectedIndex = 0;
    document.getElementById("qtd_material").value = 1;
    document.getElementById("unidade_material").selectedIndex = 0;
  
    fecharModal("modalMaterial");
  }
  
  // Adiciona Ferramenta
  function adicionarFerramenta() {
    const select = document.getElementById("select_ferramenta");
    const nome = select.options[select.selectedIndex]?.text;
    const idFerramenta = select.value;
    const descricao = select.options[select.selectedIndex]?.dataset.descricao || "";
    const dimensao = document.getElementById("dim_ferramenta").value;
  
    if (!idFerramenta || !dimensao) {
      alert("Preencha todos os campos da ferramenta.");
      return;
    }
  
    const id = Date.now();
    const tabela = document.getElementById("listaFerramentas");
    const form = document.querySelector("form");
  
    const linha = document.createElement("tr");
    linha.innerHTML = `
      <td></td>
      <td>${nome}</td>
      <td>${dimensao}</td>
      <td>${descricao}</td>
      <td>
        <button type="button" onclick="this.closest('tr').remove(); atualizarNumeracaoFerramentas(); atualizarOpcoesPasso();">🗑</button>
      </td>
      <input type="hidden" name="ferramentas[${id}][id]" value="${idFerramenta}">
      <input type="hidden" name="ferramentas[${id}][dimensoes]" value="${dimensao}">
    `;
    tabela.appendChild(linha);
  
    atualizarNumeracaoFerramentas();
    atualizarOpcoesPasso();
  
    select.selectedIndex = 0;
    document.getElementById("dim_ferramenta").value = "";
  
    fecharModal("modalFerramenta");
  }
  
  // Adiciona Passo
  let numeroPasso = 1;
  function adicionarPasso() {
    const descricao = document.getElementById("descricao_passo").value.trim();
    if (!descricao) {
      alert("Descreva o passo.");
      return;
    }
  
    const materiais = Array.from(document.querySelectorAll("#passo_materiais input:checked")).map(e => e.value);
    const ferramentas = Array.from(document.querySelectorAll("#passo_ferramentas input:checked")).map(e => e.value);
  
    const matTexto = materiais.length ? materiais.join(', ') : "—";
    const ferTexto = ferramentas.length ? ferramentas.join(', ') : "—";
  
    const id = Date.now();
    const tabela = document.getElementById("listaPassos");
    const form = document.querySelector("form");
  
    const linha = document.createElement("tr");
    linha.innerHTML = `
      <td>${numeroPasso}</td>
      <td>${matTexto}</td>
      <td>${ferTexto}</td>
      <td>${descricao}</td>
      <td><button type="button" onclick="this.closest('tr').remove()">🗑</button></td>
    `;
    tabela.appendChild(linha);
  
    form.insertAdjacentHTML("beforeend", `
      <input type="hidden" name="passos[${id}][descricao]" value="${descricao}">
      ${materiais.map(m => `<input type="hidden" name="passos[${id}][materiais][]" value="${m}">`).join("")}
      ${ferramentas.map(f => `<input type="hidden" name="passos[${id}][ferramentas][]" value="${f}">`).join("")}
    `);
  
    document.getElementById("descricao_passo").value = "";
    document.querySelectorAll("#passo_materiais input").forEach(cb => cb.checked = false);
    document.querySelectorAll("#passo_ferramentas input").forEach(cb => cb.checked = false);
  
    numeroPasso++;
    fecharModal("modalPasso");
  }
  
  // Exibir descrições
  function exibirDescricaoMaterial() {
    const select = document.getElementById("select_material");
    const desc = select.options[select.selectedIndex]?.dataset.descricao || "";
    document.getElementById("desc_material_pop").textContent = desc;
  }
  
  function exibirDescricaoFerramenta() {
    const select = document.getElementById("select_ferramenta");
    const desc = select.options[select.selectedIndex]?.dataset.descricao || "";
    document.getElementById("desc_ferramenta_pop").textContent = desc;
  }
  
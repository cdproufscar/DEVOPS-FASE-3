<!-- Modal: Adicionar Material -->
<div id="modalMaterial" class="modal hidden">
  <div class="modal-content fade-in">
    <button class="close-modal" type="button" onclick="fecharModal('modalMaterial')">✖</button>
    <h3>Adicionar Material</h3>

    <label>Selecione o material:</label>
    <select id="select_material" onchange="exibirDescricaoMaterial()">
      <option value="">-- Escolha um material --</option>
      <?php foreach ($materiais as $mat): ?>
        <option value="<?= $mat['id_material'] ?>" data-descricao="<?= htmlspecialchars($mat['descricao_material']) ?>">
          <?= htmlspecialchars($mat['nome_material']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <p id="desc_material_pop" class="descricao-mini"></p>

    <label>Quantidade:</label>
    <input type="number" id="qtd_material" min="1" value="1">

    <label>Unidade de Medida:</label>
    <select id="unidade_material">
      <option value="un">Unidade (un)</option>
      <option value="m">Metro (m)</option>
      <option value="cm">Centímetro (cm)</option>
      <option value="mm">Milímetro (mm)</option>
      <option value="g">Grama (g)</option>
      <option value="kg">Quilo (kg)</option>
      <option value="l">Litro (l)</option>
      <option value="ml">Mililitro (ml)</option>
    </select>

    <button type="button" onclick="adicionarMaterial()">Adicionar Material</button>
    <a href="cadastrar_material.php" target="_blank" class="btn-secundario">Cadastrar novo material externamente</a>
  </div>
</div>

<!-- Modal: Adicionar Ferramenta -->
<div id="modalFerramenta" class="modal hidden">
  <div class="modal-content fade-in">
    <button class="close-modal" type="button" onclick="fecharModal('modalFerramenta')">✖</button>
    <h3>Adicionar Ferramenta</h3>

    <label>Selecione a ferramenta:</label>
    <select id="select_ferramenta" onchange="exibirDescricaoFerramenta()">
      <option value="">-- Escolha uma ferramenta --</option>
      <?php foreach ($ferramentas as $fer): ?>
        <option value="<?= $fer['id_ferramenta'] ?>" data-descricao="<?= htmlspecialchars($fer['descricao_ferramenta']) ?>">
          <?= htmlspecialchars($fer['nome_ferramenta']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <p id="desc_ferramenta_pop" class="descricao-mini"></p>

    <label>Dimensões:</label>
    <input type="text" id="dim_ferramenta" placeholder="Ex: 10cm x 3cm">

    <button type="button" onclick="adicionarFerramenta()">Adicionar Ferramenta</button>
    <a href="cadastrar_ferramenta.php" target="_blank" class="btn-secundario">Cadastrar nova ferramenta externamente</a>
  </div>
</div>

<!-- Modal: Adicionar Passo-a-Passo -->
<div id="modalPasso" class="modal hidden">
  <div class="modal-content fade-in">
    <button class="close-modal" type="button" onclick="fecharModal('modalPasso')">✖</button>
    <h3>Adicionar Passo a Passo</h3>

    <label>Materiais usados neste passo:</label>
    <div class="scroll-box" id="passo_materiais">
      <!-- Populado dinamicamente pelo JS -->
    </div>

    <label>Ferramentas usadas neste passo:</label>
    <div class="scroll-box" id="passo_ferramentas">
      <!-- Populado dinamicamente pelo JS -->
    </div>

    <label>Descrição / Comentário:</label>
    <textarea id="descricao_passo" rows="4" placeholder="Descreva o que deve ser feito neste passo..."></textarea>

    <button type="button" onclick="adicionarPasso()">Adicionar Passo</button>
  </div>
</div>

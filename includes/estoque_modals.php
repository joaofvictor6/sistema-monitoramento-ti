<!-- Modal Adicionar Item -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Adicionar Novo Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo *</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="">Selecione um tipo</option>
                            <option value="Monitor">Monitor</option>
                            <option value="Teclado">Teclado</option>
                            <option value="Mouse">Mouse</option>
                            <option value="Cabo">Cabo</option>
                            <option value="Adaptador">Adaptador</option>
                            <option value="Fonte">Fonte</option>
                            <option value="HD/SSD">HD/SSD</option>
                            <option value="Memória RAM">Memória RAM</option>
                            <option value="Outro">Outro</option>
                            <option value="Switch">Switch</option>
                             <option value="Modem">Modem</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantidade" class="form-label">Quantidade *</label>
                        <input type="number" class="form-control" id="quantidade" name="quantidade" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="local" class="form-label">Localização *</label>
                        <input type="text" class="form-control" id="local" name="local" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Item -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="editItemModalLabel">Editar Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" id="edit_id" name="id">
                    
                    <div class="mb-3">
                        <label for="edit_nome" class="form-label">Nome *</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_tipo" class="form-label">Tipo *</label>
                        <select class="form-select" id="edit_tipo" name="tipo" required>
                            <option value="Monitor">Monitor</option>
                            <option value="Teclado">Teclado</option>
                            <option value="Mouse">Mouse</option>
                            <option value="Cabo">Cabo</option>
                            <option value="Adaptador">Adaptador</option>
                            <option value="Fonte">Fonte</option>
                            <option value="HD/SSD">HD/SSD</option>
                            <option value="Memória RAM">Memória RAM</option>
                            <option value="Outro">Outro</option>
                            <option value="Switch">Switch</option>
                            <option value="Modem">Modem</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_quantidade" class="form-label">Quantidade *</label>
                        <input type="number" class="form-control" id="edit_quantidade" name="quantidade" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_local" class="form-label">Localização *</label>
                        <input type="text" class="form-control" id="edit_local" name="local" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Exclusão -->
<div class="modal fade" id="deleteItemModal" tabindex="-1" aria-labelledby="deleteItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteItemModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="delete_id" name="id">
                    <p>Tem certeza que deseja remover este item do estoque? Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                </div>
            </form>
        </div>
    </div>
</div>
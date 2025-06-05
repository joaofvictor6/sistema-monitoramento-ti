<!-- Modal Adicionar Impressora -->
<div class="modal fade" id="addPrinterModal" tabindex="-1" aria-labelledby="addPrinterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPrinterModalLabel">Adicionar Nova Impressora</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome/Modelo *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ip" class="form-label">Endereço IP *</label>
                        <input type="text" class="form-control" id="ip" name="ip" required 
                               pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" 
                               title="Formato: XXX.XXX.XXX.XXX">
                    </div>
                    
                    <div class="mb-3">
                        <label for="mac" class="form-label">Endereço MAC *</label>
                        <input type="text" class="form-control" id="mac" name="mac" required
                               pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$" 
                               title="Formato: XX:XX:XX:XX:XX:XX ou XX-XX-XX-XX-XX-XX">
                    </div>
                    
                    <div class="mb-3">
                        <label for="serial" class="form-label">Número de Série *</label>
                        <input type="text" class="form-control" id="serial" name="serial" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="ativo">Ativo</option>
                            <option value="manutencao">Em Manutenção</option>
                            <option value="inativo">Inativo</option>
                        </select>
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

<!-- Modal Editar Impressora -->
<div class="modal fade" id="editPrinterModal" tabindex="-1" aria-labelledby="editPrinterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPrinterModalLabel">Editar Impressora</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" id="edit_id" name="id">
                    
                    <div class="mb-3">
                        <label for="edit_nome" class="form-label">Nome/Modelo *</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_ip" class="form-label">Endereço IP *</label>
                        <input type="text" class="form-control" id="edit_ip" name="ip" required
                               pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" 
                               title="Formato: XXX.XXX.XXX.XXX">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_mac" class="form-label">Endereço MAC *</label>
                        <input type="text" class="form-control" id="edit_mac" name="mac" required
                               pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$" 
                               title="Formato: XX:XX:XX:XX:XX:XX ou XX-XX-XX-XX-XX-XX">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_serial" class="form-label">Número de Série *</label>
                        <input type="text" class="form-control" id="edit_serial" name="serial" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status *</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="ativo">Ativo</option>
                            <option value="manutencao">Em Manutenção</option>
                            <option value="inativo">Inativo</option>
                        </select>
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
<div class="modal fade" id="deletePrinterModal" tabindex="-1" aria-labelledby="deletePrinterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePrinterModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="delete_id" name="id">
                    <p>Tem certeza que deseja remover esta impressora? Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                </div>
            </form>
        </div>
    </div>
</div>
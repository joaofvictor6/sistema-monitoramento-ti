<!-- Modal Adicionar Dispositivo -->
<div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDeviceModalLabel">Adicionar Novo Dispositivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ip" class="form-label">Endereço IP *</label>
                        <input type="text" class="form-control" id="ip" name="ip" required 
                               pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" 
                               title="Formato: XXX.XXX.XXX.XXX">
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo *</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="">Selecione um tipo</option>
                            <option value="Desktop">Desktop</option>
                            <option value="Roteador">Roteador</option>
                            <option value="Switch">Switch</option>
                            <option value="Servidor">Servidor</option>
                            <option value="Firewall">Firewall</option>
                            <option value="Access Point">Access Point</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="Online" selected>Online</option>
                            <option value="Offline">Offline</option>
                            <option value="Manutenção">Manutenção</option>
                            <option value="Desativado">Desativado</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="local" class="form-label">Localização *</label>
                        <input type="text" class="form-control" id="local" name="local" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
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

<!-- Modal Editar Dispositivo -->
<div class="modal fade" id="editDeviceModal" tabindex="-1" aria-labelledby="editDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDeviceModalLabel">Editar Dispositivo</h5>
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
                        <label for="edit_ip" class="form-label">Endereço IP *</label>
                        <input type="text" class="form-control" id="edit_ip" name="ip" required
                               pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" 
                               title="Formato: XXX.XXX.XXX.XXX">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_tipo" class="form-label">Tipo *</label>
                       <select class="form-select" id="tipo" name="tipo" required>
                            <option value="">Selecione um tipo</option>
                            <option value="Desktop">Desktop</option>
                            <option value="Roteador">Roteador</option>
                            <option value="Switch">Switch</option>
                            <option value="Servidor">Servidor</option>
                            <option value="Firewall">Firewall</option>
                            <option value="Access Point">Access Point</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="status">
                            <option value="Online">Online</option>
                            <option value="Offline">Offline</option>
                            <option value="Manutenção">Manutenção</option>
                            <option value="Desativado">Desativado</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_local" class="form-label">Localização *</label>
                        <input type="text" class="form-control" id="edit_local" name="local" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="edit_observacoes" name="observacoes" rows="3"></textarea>
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

<!-- Modal Verificar Dispositivo -->
<div class="modal fade" id="verifyDeviceModal" tabindex="-1" aria-labelledby="verifyDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="verifyDeviceModalLabel">Verificar Dispositivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="verificar">
                    <input type="hidden" id="verify_id" name="id">
                    <p>Atualizar status do dispositivo: <strong id="verify_nome"></strong></p>
                    
                    <div class="mb-3">
                        <label for="verify_status" class="form-label">Novo Status</label>
                        <select class="form-select" id="verify_status" name="status" required>
                            <option value="Online">Online</option>
                            <option value="Offline">Offline</option>
                            <option value="Manutenção">Manutenção</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Exclusão -->
<div class="modal fade" id="deleteDeviceModal" tabindex="-1" aria-labelledby="deleteDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDeviceModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="delete_id" name="id">
                    <p>Tem certeza que deseja remover este dispositivo? Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                </div>
            </form>
        </div>
    </div>
</div>
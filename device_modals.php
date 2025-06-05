<!-- Modal Adicionar Dispositivo -->
<div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDeviceModalLabel">Adicionar Novo Dispositivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Dispositivo</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ip" class="form-label">Endereço IP</label>
                        <input type="text" class="form-control" id="ip" name="ip" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="">Selecione...</option>
                            <option value="Servidor">Servidor</option>
                            <option value="Switch">Switch</option>
                            <option value="Roteador">Roteador</option>
                            <option value="Firewall">Firewall</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Online">Online</option>
                            <option value="Offline">Offline</option>
                            <option value="Manutenção">Manutenção</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="local" class="form-label">Localização</label>
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
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDeviceModalLabel">Editar Dispositivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label for="edit_nome" class="form-label">Nome do Dispositivo</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_ip" class="form-label">Endereço IP</label>
                        <input type="text" class="form-control" id="edit_ip" name="ip" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="edit_tipo" name="tipo" required>
                            <option value="Servidor">Servidor</option>
                            <option value="Switch">Switch</option>
                            <option value="Roteador">Roteador</option>
                            <option value="Firewall">Firewall</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="Online">Online</option>
                            <option value="Offline">Offline</option>
                            <option value="Manutenção">Manutenção</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_local" class="form-label">Localização</label>
                        <input type="text" class="form-control" id="edit_local" name="local" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="edit_observacoes" name="observacoes" rows="3"></textarea>
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

<!-- Modal Verificar Dispositivo -->
<div class="modal fade" id="verifyDeviceModal" tabindex="-1" aria-labelledby="verifyDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="verifyDeviceModalLabel">Verificar Dispositivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="verificar">
                    <input type="hidden" name="id" id="verify_id">
                    <p>Deseja verificar o status atual do dispositivo <strong id="verify_nome"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Verificar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Excluir Dispositivo -->
<div class="modal fade" id="deleteDeviceModal" tabindex="-1" aria-labelledby="deleteDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDeviceModalLabel">Excluir Dispositivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <p>Tem certeza que deseja excluir este dispositivo? Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Editar Dispositivo
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_nome').value = this.dataset.nome;
            document.getElementById('edit_ip').value = this.dataset.ip;
            document.getElementById('edit_tipo').value = this.dataset.tipo;
            document.getElementById('edit_status').value = this.dataset.status;
            document.getElementById('edit_local').value = this.dataset.local;
            document.getElementById('edit_observacoes').value = this.dataset.observacoes;
            
            var modal = new bootstrap.Modal(document.getElementById('editDeviceModal'));
            modal.show();
        });
    });
    
    // Verificar Dispositivo
    document.querySelectorAll('.verify-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('verify_id').value = this.dataset.id;
            document.getElementById('verify_nome').textContent = this.dataset.nome;
            
            var modal = new bootstrap.Modal(document.getElementById('verifyDeviceModal'));
            modal.show();
        });
    });
    
    // Excluir Dispositivo
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('delete_id').value = this.dataset.id;
            
            var modal = new bootstrap.Modal(document.getElementById('deleteDeviceModal'));
            modal.show();
        });
    });
});
</script>
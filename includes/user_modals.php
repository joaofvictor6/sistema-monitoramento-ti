<!-- Modal Adicionar Usuário -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Adicionar Novo Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">E-mail *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="perfil" class="form-label">Perfil *</label>
                            <select class="form-select" id="perfil" name="perfil" required>
                                <option value="">Selecione um perfil</option>
                                <option value="admin">Administrador</option>
                                <option value="tecnico">Técnico</option>
                                <option value="visualizador">Visualizador</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="departamento" class="form-label">Departamento</label>
                            <input type="text" class="form-control" id="departamento" name="departamento">
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Uma senha temporária será gerada e exibida após o cadastro.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cadastrar Usuário</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuário -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Editar Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" id="edit_id" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_nome" class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" id="edit_nome" name="nome" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="edit_email" name="email" readonly>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_perfil" class="form-label">Perfil *</label>
                            <select class="form-select" id="edit_perfil" name="perfil" required>
                                <option value="admin">Administrador</option>
                                <option value="tecnico">Técnico</option>
                                <option value="visualizador">Visualizador</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_departamento" class="form-label">Departamento</label>
                            <input type="text" class="form-control" id="edit_departamento" name="departamento">
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_ativo" name="ativo">
                        <label class="form-check-label" for="edit_ativo">Usuário ativo</label>
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

<!-- Modal Resetar Senha -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel">Resetar Senha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="reset-password">
                    <input type="hidden" id="reset_id" name="id">
                    
                    <p>Tem certeza que deseja resetar a senha do usuário <strong id="reset_nome"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Uma nova senha temporária será gerada e exibida.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Confirmar Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Exclusão -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="delete_id" name="id">
                    <p>Tem certeza que deseja remover o usuário <strong id="delete_nome"></strong>? Esta ação não pode ser desfeita.</p>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Todos os dados associados a este usuário serão perdidos.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                </div>
            </form>
        </div>
    </div>
</div>
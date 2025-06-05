document.addEventListener('DOMContentLoaded', function() {
    // Configurar botões de edição
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            
            // Preencher o modal com os dados do usuário
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_nome').value = this.dataset.nome;
            document.getElementById('edit_email').value = this.dataset.email;
            document.getElementById('edit_perfil').value = this.dataset.perfil;
            document.getElementById('edit_departamento').value = this.dataset.departamento;
            document.getElementById('edit_ativo').checked = this.dataset.ativo === '1';
            
            editModal.show();
        });
    });
    
    // Configurar botões de reset de senha
    const resetButtons = document.querySelectorAll('.reset-btn');
    resetButtons.forEach(button => {
        button.addEventListener('click', function() {
            const resetModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
            
            document.getElementById('reset_id').value = this.dataset.id;
            document.getElementById('reset_nome').textContent = this.dataset.nome;
            
            resetModal.show();
        });
    });
    
    // Configurar botões de exclusão
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            
            document.getElementById('delete_id').value = this.dataset.id;
            document.getElementById('delete_nome').textContent = this.dataset.nome;
            
            deleteModal.show();
        });
    });
    
    // Fechar mensagens de alerta após 5 segundos
    const alertMessages = document.querySelectorAll('.alert');
    alertMessages.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
});
document.addEventListener('DOMContentLoaded', function() {
    // Configurar botões de edição
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const editModal = new bootstrap.Modal(document.getElementById('editDeviceModal'));
            
            // Preencher o modal com os dados do dispositivo
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_nome').value = this.dataset.nome;
            document.getElementById('edit_ip').value = this.dataset.ip;
            document.getElementById('edit_tipo').value = this.dataset.tipo;
            document.getElementById('edit_status').value = this.dataset.status;
            document.getElementById('edit_local').value = this.dataset.local;
            document.getElementById('edit_observacoes').value = this.dataset.observacoes;
            
            editModal.show();
        });
    });
    
    // Configurar botões de verificação
    const verifyButtons = document.querySelectorAll('.verify-btn');
    verifyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const verifyModal = new bootstrap.Modal(document.getElementById('verifyDeviceModal'));
            
            document.getElementById('verify_id').value = this.dataset.id;
            document.getElementById('verify_nome').textContent = this.dataset.nome;
            
            verifyModal.show();
        });
    });
    
    // Configurar botões de exclusão
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteDeviceModal'));
            document.getElementById('delete_id').value = this.dataset.id;
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
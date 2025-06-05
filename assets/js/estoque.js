document.addEventListener('DOMContentLoaded', function() {
    // Configurar botões de edição
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const editModal = new bootstrap.Modal(document.getElementById('editItemModal'));
            
            // Preencher o modal com os dados do item
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_nome').value = this.dataset.nome;
            document.getElementById('edit_tipo').value = this.dataset.tipo;
            document.getElementById('edit_quantidade').value = this.dataset.quantidade;
            document.getElementById('edit_local').value = this.dataset.local;
            
            editModal.show();
        });
    });
    
    // Configurar botões de exclusão
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteItemModal'));
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
    
    // Atualizar status ao alterar quantidade no modal de edição
    const quantidadeInput = document.getElementById('edit_quantidade');
    if (quantidadeInput) {
        quantidadeInput.addEventListener('change', function() {
            updateStatusBadge(this.value);
        });
    }
    
    function updateStatusBadge(quantidade) {
        const statusBadge = document.getElementById('status-badge');
        if (!statusBadge) return;
        
        if (quantidade == 0) {
            statusBadge.className = 'badge bg-danger';
            statusBadge.textContent = 'Esgotado';
        } else if (quantidade < 3) {
            statusBadge.className = 'badge bg-warning';
            statusBadge.textContent = 'Baixo';
        } else {
            statusBadge.className = 'badge bg-success';
            statusBadge.textContent = 'Disponível';
        }
    }
});
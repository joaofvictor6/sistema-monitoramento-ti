document.addEventListener('DOMContentLoaded', function() {
    // Adicionar efeito de foco nos inputs
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.querySelector('label').style.color = '#3498db';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.querySelector('label').style.color = '#2c3e50';
        });
    });
    
    // Mostrar/ocultar senha
    const passwordInput = document.getElementById('password');
    const showPasswordBtn = document.createElement('span');
    showPasswordBtn.innerHTML = '<i class="fas fa-eye"></i>';
    showPasswordBtn.style.position = 'absolute';
    showPasswordBtn.style.right = '15px';
    showPasswordBtn.style.top = '50%';
    showPasswordBtn.style.transform = 'translateY(-50%)';
    showPasswordBtn.style.cursor = 'pointer';
    showPasswordBtn.style.color = '#7f8c8d';
    
    passwordInput.parentElement.style.position = 'relative';
    passwordInput.parentElement.appendChild(showPasswordBtn);
    
    showPasswordBtn.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            this.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            passwordInput.type = 'password';
            this.innerHTML = '<i class="fas fa-eye"></i>';
        }
    });
    
    // Validação do formulário
    const form = document.querySelector('.login-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos.');
            }
        });
    }
});
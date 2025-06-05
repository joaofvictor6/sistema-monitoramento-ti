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
    const passwordInputs = ['password', 'confirm_password'];
    
    passwordInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            const showPasswordBtn = document.createElement('span');
            showPasswordBtn.innerHTML = '<i class="fas fa-eye"></i>';
            showPasswordBtn.style.position = 'absolute';
            showPasswordBtn.style.right = '15px';
            showPasswordBtn.style.top = '50%';
            showPasswordBtn.style.transform = 'translateY(-50%)';
            showPasswordBtn.style.cursor = 'pointer';
            showPasswordBtn.style.color = '#7f8c8d';
            
            input.parentElement.style.position = 'relative';
            input.parentElement.appendChild(showPasswordBtn);
            
            showPasswordBtn.addEventListener('click', function() {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    input.type = 'password';
                    this.innerHTML = '<i class="fas fa-eye"></i>';
                }
            });
        }
    });
    
    // Validação do formulário em tempo real
    const form = document.querySelector('.login-form');
    if (form) {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        function validatePassword() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('As senhas não coincidem');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
        
        password.addEventListener('change', validatePassword);
        confirmPassword.addEventListener('keyup', validatePassword);
        
        form.addEventListener('submit', function(e) {
            if (password.value.length < 8) {
                e.preventDefault();
                alert('A senha deve ter pelo menos 8 caracteres.');
            }
        });
    }
});
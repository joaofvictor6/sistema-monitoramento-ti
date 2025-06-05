document.addEventListener('DOMContentLoaded', function() {
    // Validação de IP no formulário
    document.querySelector('form').addEventListener('submit', function(e) {
        const ipInput = document.getElementById('ip') || document.getElementById('edit_ip');
        if (ipInput) {
            const ip = ipInput.value.trim();
            if (!/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ip)) {
                e.preventDefault();
                alert('Por favor, insira um endereço IP válido.');
                ipInput.focus();
            }
        }
    });

    // Mostrar feedback durante a verificação
    document.querySelectorAll('.verify-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const deviceId = this.dataset.id;
            const deviceName = this.dataset.nome;
            
            fetch(`api/verify_device.php?id=${deviceId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Dispositivo ${deviceName} está ${data.status}`);
                        location.reload();
                    } else {
                        alert(`Erro ao verificar dispositivo: ${data.message}`);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao conectar com o servidor');
                });
        });
    });
});
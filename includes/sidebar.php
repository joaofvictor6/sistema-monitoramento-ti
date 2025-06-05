<?php
// Obter o nome do arquivo atual
$current_page = basename($_SERVER['PHP_SELF']);

// Extrair iniciais do nome do usuário
$nameParts = explode(' ', $_SESSION['user_name']);
$initials = '';
foreach ($nameParts as $part) {
    if (!empty($part)) {
        $initials .= strtoupper(substr($part, 0, 1));
        if (strlen($initials) >= 2) break;
    }
}
?>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="assets/images/icone.png" alt="Logo" class="logo">
    </div>

    <ul class="sidebar-menu">
        <li class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </li>
        <li class="<?= $current_page == 'impressoras.php' ? 'active' : '' ?>">
            <a href="impressoras.php"><i class="fas fa-print"></i> Impressoras</a>
        </li>
        <li class="<?= $current_page == 'dispositivos.php' ? 'active' : '' ?>">
            <a href="dispositivos.php"><i class="fas fa-network-wired"></i> Dispositivos</a>
        </li>
        <li class="<?= $current_page == 'estoque.php' ? 'active' : '' ?>">
            <a href="estoque.php"><i class="fas fa-boxes"></i> Estoque</a>
        </li>
        <li class="<?= $current_page == 'usuarios.php' ? 'active' : '' ?>">
            <a href="usuarios.php"><i class="fas fa-users-cog"></i> Usuários</a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="avatar"><?= $initials ?></div>
            <div class="user-details">
                <div class="name"><?= $_SESSION['user_name'] ?></div>
                <div class="role"><?= ucfirst($_SESSION['user_perfil']) ?></div>
            </div>
        </div>
        <a href="logout.php" class="logout">
            <i class="fas fa-sign-out-alt"></i> Sair
        </a>
    </div>
</div>

<style>
/* Estilos gerais */
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 220px;
    height: 100vh;
    background-color: #2c3e50;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 20px 0;
    box-shadow: 2px 0 6px rgba(0,0,0,0.2);
}

/* Logo */
.sidebar-header {
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.logo {
    width: 60px;
    height: auto;
}


/* Menu */
.sidebar-menu {
    list-style: none;
    padding: 20px 0;
    margin: 0;
}

.sidebar-menu li {
    margin: 10px 0;
}

.sidebar-menu a {
    display: flex;
    align-items: center;
    padding: 10px 20px;
    color: #ecf0f1;
    text-decoration: none;
    font-size: 15px;
    transition: background 0.3s;
    border-left: 3px solid transparent;
}

.sidebar-menu a i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.sidebar-menu .active a {
    background-color: rgba(255,255,255,0.1);
    border-left: 3px solid #1abc9c;
    font-weight: bold;
}

/* Rodapé */
.sidebar-footer {
    border-top: 1px solid rgba(255,255,255,0.1);
    padding: 15px 20px 10px;
}

.user-profile {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.avatar {
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    color: white;
    font-weight: bold;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    font-size: 14px;
}

.user-details .name {
    font-size: 14px;
    color: #ecf0f1;
}

.user-details .role {
    font-size: 12px;
    color: #bdc3c7;
}

.logout {
    display: flex;
    align-items: center;
    color: #ecf0f1;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.2s;
}

.logout i {
    margin-right: 8px;
}

.logout:hover {
    color: #1abc9c;
}
</style>

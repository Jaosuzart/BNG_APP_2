
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(0, 0, 0, 0.2); backdrop-filter: blur(5px); position: relative; z-index: 9999;">
    <div class="container-fluid px-4">
        
        <a class="navbar-brand d-flex align-items-center" href="?ct=main&mt=index">
            <img src="assets/images/logo_32.png" alt="Logo" width="32" height="32" class="d-inline-block align-text-top me-2">
            <span class="fw-bold tracking-wider">BNG</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                
                <li class="nav-item">
                    <a class="nav-link active" href="?ct=main&mt=index">
                        <i class="fas fa-home me-1"></i> Início
                    </a>
                </li>

                <?php if (isset($_SESSION['user'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="?ct=agent&mt=new_client_frm">
                            <i class="fas fa-user-plus me-1"></i> Adicionar Cliente
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['user']) && $_SESSION['user']->profile == 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-tools me-1"></i> Administração
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="?ct=admin&mt=all_clients"><i class="fas fa-list me-2"></i>Todos os Clientes</a></li>
                            <li><a class="dropdown-item" href="?ct=admin&mt=agents_management"><i class="fas fa-users-cog me-2"></i>Gestão de Agentes</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="?ct=admin&mt=stats"><i class="fas fa-chart-pie me-2"></i>Estatísticas</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

            </ul>

            <div class="d-flex text-white align-items-center">
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle fa-lg me-2"></i>
                            
                            <span class="d-inline-block text-truncate" style="max-width: 200px; vertical-align: middle;">
                                <?= htmlspecialchars($_SESSION['user']->name) ?>
                            </span>

                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="userDropdown">
                             <li><a class="dropdown-item text-white mb-2" href="?ct=main&mt=change_password_frm"><i class="fas fa-key me-2"></i>Alterar Password</a></li>
                             <li><hr class="dropdown-divider"></li>
                             <li><a class="dropdown-item" href="?ct=main&mt=logout"><i class="fas fa-sign-out-alt me-2"></i>Sair</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="nav-link text-white" href="?ct=main&mt=login">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </a>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</nav>
<div class="container-fluid py-5 bg-white min-vh-100">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white p-4">
                    <h4 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i> Editar Agente
                    </h4>
                </div>

                <div class="card-body p-5">
                    
                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars($_SESSION['success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['server_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars($_SESSION['server_error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['server_error']); ?>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['validation_errors'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-circle me-2"></i>Erros encontrados:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($_SESSION['validation_errors'] as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['validation_errors']); ?>
                    <?php endif; ?>

                    <form action="?ct=admin&mt=edit_agent_submit" method="post" class="needs-validation" replace>
                        
                        <input type="hidden" name="id" value="<?= aes_encrypt($agent->id) ?>">

                        <div class="mb-4">
                            <label for="text_email" class="form-label fw-bold text-secondary">Email do agente</label>
                            <input type="email" 
                                   name="text_email" 
                                   id="text_email" 
                                   class="form-control form-control-lg bg-light" 
                                   value="<?= htmlspecialchars($agent->name) ?>"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="select_profile" class="form-label fw-bold text-secondary">Perfil</label>
                            <select name="select_profile" id="select_profile" class="form-select form-select-lg" required>
                                <option value="admin" <?= $agent->profile == 'admin' ? 'selected' : '' ?>>Administrador</option>
                                <option value="agent" <?= $agent->profile == 'agent' ? 'selected' : '' ?>>Agente</option>
                            </select>
                        </div>

                        <hr class="my-5">

                        <div class="mb-4">
                            <label for="text_password" class="form-label fw-bold text-secondary">
                                Alterar Senha <span class="fw-normal text-muted small">(Deixe em branco para manter a atual)</span>
                            </label>
                            <input type="password" 
                                   name="text_password" 
                                   id="text_password" 
                                   class="form-control form-control-lg"
                                   placeholder="Nova senha (opcional)"
                                   minlength="12"
                                   maxlength="12">
                            <div class="form-text mt-2">
                                <i class="fas fa-info-circle me-1"></i> Se preencher, deve ter <strong>EXATAMENTE 12 caracteres</strong>.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <a href="?ct=admin&mt=agents_management" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-arrow-left me-2"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                <i class="fas fa-save me-2"></i> Salvar Alterações
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
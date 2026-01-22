<div class="container-fluid py-5 bg-white min-vh-100">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i> Adicionar Novo Agente
                    </h4>
                </div>
                <div class="card-body p-5">

                    <!-- Mensagens de erro -->
                    <?php if (!empty($_SESSION['validation_errors'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>Erros:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($_SESSION['validation_errors'] as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php unset($_SESSION['validation_errors']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['server_error'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['server_error']) ?>
                            <?php unset($_SESSION['server_error']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($_SESSION['success']) ?>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="?ct=admin&mt=new_agent_submit" method="post" class="needs-validation">
                        <div class="mb-4">
                            <label for="text_name" class="form-label fw-bold">
                                Email do agente <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   name="text_name" 
                                   id="text_name" 
                                   class="form-control form-control-lg" 
                                   placeholder="exemplo@dominio.com"
                                   required>
                            <div class="invalid-feedback">
                                Por favor, insira um e-mail válido.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="select_profile" class="form-label fw-bold">
                                Perfil <span class="text-danger">*</span>
                            </label>
                            <select name="select_profile" id="select_profile" class="form-select form-select-lg" required>
                                <option value="">Selecione o perfil</option>
                                <option value="agent">Agente</option>
                                <option value="admin">Administrador</option>
                            </select>
                            <div class="invalid-feedback">
                                Por favor, selecione um perfil.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="text_password" class="form-label fw-bold">Senha inicial <span class="text-danger">*</span></label>
                            <input type="password" 
                                   name="text_password" 
                                   id="text_password" 
                                   class="form-control form-control-lg"
                                   placeholder="Digite exatamente 12 caracteres"
                                   minlength="12"
                                   maxlength="12"
                                   required>
                            <div class="invalid-feedback">A senha deve ter exatamente 12 caracteres.</div>
                            <small class="text-muted"><i class="fas fa-info-circle"></i> A senha deve ter <strong>exatamente 12 caracteres</strong> (inclua maiúsculas e números).</small>
                        </div>

                        <div class="text-center mt-5">
                            <a href="?ct=admin&mt=agents_management" class="btn btn-secondary btn-lg me-4 px-5">
                                <i class="fas fa-arrow-left me-2"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-user-plus me-2"></i> Criar Agente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validação Bootstrap
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>
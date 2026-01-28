<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card p-5 shadow border-0 rounded-3">

                <div class="text-center mb-4">
                    <img src="assets/images/logo_64.png" class="img-fluid mb-3" width="64">
                    <h3 class="fw-bold text-dark"><?= APP_NAME ?></h3>
                    <p class="text-muted small">Bem-vindo de volta!</p>
                </div>

                <form action="?ct=main&mt=login_submit" method="post">
                    
                    <?php if(!empty($server_error)): ?>
                        <div class="alert alert-danger text-center p-2 mb-3 shadow-sm">
                            <i class="fa-solid fa-circle-exclamation me-2"></i><?= $server_error ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-floating mb-3">
                        <input type="email" name="text_username" id="text_username" class="form-control" placeholder="name@example.com" required>
                        <label for="text_username">Email</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" name="text_password" id="text_password" class="form-control" placeholder="Password" required>
                        <label for="text_password">Password</label>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Entrar <i class="fa-solid fa-right-to-bracket ms-2"></i>
                        </button>
                    </div>

                    <div class="text-center mb-3">
                        <a href="?ct=main&mt=reset_password_frm" class="text-decoration-none small text-muted hover-link">
                            Esqueci-me da password
                        </a>
                    </div>

                    <?php if(!empty($validation_errors)): ?>
                        <div class="alert alert-warning p-2 small shadow-sm">
                            <ul class="mb-0 ps-3">
                                <?php foreach($validation_errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                </form>
            </div>
        </div>
    </div>
</div>
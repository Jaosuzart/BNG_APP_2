<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-6 col-sm-8 col-10">
            <div class="card p-4 shadow-sm">

                <div class="d-flex align-items-center justify-content-center my-4">
                    <img src="assets/images/logo_64.png" class="img-fluid me-3" width="60">
                    <h2><strong><?= APP_NAME ?></strong></h2>
                </div>

                <div class="row justify-content-center">
                    <div class="col-8">

                        <p class="text-center mb-4">Indique o seu nome de utilizador ou email.<br>Vamos enviar um código de recuperação.</p>

                        <form action="?ct=main&mt=reset_password_request_submit" method="post" replace>

                            <div class="mb-4">
                                <label for="text_username" class="form-label">Utilizador / Email</label>
                                <input type="text" name="text_username" id="text_username" class="form-control" required>
                            </div>

                            <div class="mb-4 text-center d-grid">
                                <button type="submit" class="btn btn-secondary">
                                    Enviar código <i class="fa-regular fa-paper-plane ms-2"></i>
                                </button>
                            </div>
                            
                            <div class="text-center">
                                <a href="?ct=main&mt=login" class="btn btn-outline-secondary btn-sm">
                                    <i class="fa-solid fa-caret-left me-2"></i>Voltar
                                </a>
                            </div>

                        </form>

                        <?php if(isset($erro)): ?>
                            <div class="alert alert-danger p-2 text-center mt-3">
                                <?= $erro ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
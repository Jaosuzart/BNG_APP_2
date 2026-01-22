<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-6 col-sm-8 col-10">
            <div class="card p-4 shadow-sm" style="border-radius: 15px;">

                <div class="d-flex align-items-center justify-content-center my-4">
                    <img src="assets/images/logo_64.png" class="img-fluid me-3" width="60">
                    <h2><strong><?= APP_NAME ?></strong></h2>
                </div>

                <div class="row justify-content-center">
                    <div class="col-10">

                        <h4 class="text-center mb-4 fw-bold">Código de Verificação</h4>

                        <?php if(isset($success_message)): ?>
                            <div class="alert text-center p-3 shadow-sm" style="background-color: #f0fff4; border: 1px solid #c3e6cb; color: #2f5938; border-radius: 10px;">
                                <div class="mb-2"><i class="fa-solid fa-envelope-circle-check fa-2x"></i></div>
                                <?= $success_message ?>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-secondary">Introduza o código que enviámos por email.</p>
                        <?php endif; ?>

                        <form action="?ct=main&mt=reset_password_check_code_submit" method="post">

                            <div class="mb-4 mt-4">
                                <input type="text" name="text_code" id="text_code" class="form-control form-control-lg fw-bold text-center letter-spacing-2" placeholder="XXXXXX" required style="letter-spacing: 5px; font-size: 1.5rem;">
                            </div>

                            <div class="mb-4 text-center d-grid">
                                <button type="submit" class="btn btn-secondary py-2">
                                    Verificar <i class="fa-solid fa-check ms-2"></i>
                                </button>
                            </div>

                            <div class="text-center">
                                <a href="?ct=main&mt=login" class="btn btn-outline-secondary btn-sm">Cancelar</a>
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
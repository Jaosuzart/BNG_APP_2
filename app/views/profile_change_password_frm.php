<div class="container-fluid py-5 mb-5"> <div class="row justify-content-center">
        <div class="col-lg-5 col-md-6 col-sm-8 col-10">
            <div class="card p-4 shadow-sm" style="border-radius: 15px;">

                <div class="text-center mb-4">
                    <h3 class="fw-bold"><i class="fa-solid fa-key me-2 text-secondary"></i>Alterar Password</h3>
                </div>
                
                <hr class="mb-4">

                <div class="row justify-content-center">
                    <div class="col-12"> <form action="?ct=main&mt=change_password_submit" method="post">

                            <div class="mb-3">
                                <label for="text_current_password" class="form-label text-secondary fw-bold">Password Atual</label>
                                <input type="password" name="text_current_password" id="text_current_password" class="form-control form-control-lg" required>
                            </div>

                            <div class="mb-3">
                                <label for="text_new_password" class="form-label text-secondary fw-bold">Nova Password</label>
                                <input type="password" name="text_new_password" id="text_new_password" class="form-control form-control-lg" required>
                            </div>

                            <div class="mb-4">
                                <label for="text_repeat_new_password" class="form-label text-secondary fw-bold">Repetir Nova Password</label>
                                <input type="password" name="text_repeat_new_password" id="text_repeat_new_password" class="form-control form-control-lg" required>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-4">
                                <a href="?ct=main&mt=index" class="btn btn-outline-secondary px-4 py-2">
                                    <i class="fa-solid fa-xmark me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-secondary px-4 py-2">
                                    <i class="fa-solid fa-check me-2"></i>Alterar
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                <?php if (isset($erro)) : ?>
                    <div class="alert alert-danger p-3 text-center mt-4 mb-0 rounded-3">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i><?= $erro ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($validation_errors)) : ?>
                    <div class="alert alert-danger p-3 text-center mt-4 mb-0 rounded-3">
                        <ul class="mb-0 list-unstyled">
                            <?php foreach ($validation_errors as $error) : ?>
                                <li><i class="fa-solid fa-circle-xmark me-2"></i><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
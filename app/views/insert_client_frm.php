<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fa-solid fa-user-plus me-2"></i>Inserir Novo Cliente</h5>
                </div>
                <div class="card-body p-4">

                    <form action="?ct=agent&mt=new_client_submit" method="post">

                        <div class="mb-3">
                            <label for="text_name" class="form-label fw-bold">Nome Completo <span class="text-danger">*</span></label>
                            <input type="text" name="text_name" id="text_name" class="form-control" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Sexo <span class="text-danger">*</span></label>
                                <div class="mt-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="radio_gender" id="radio_m" value="m" checked>
                                        <label class="form-check-label" for="radio_m">Masculino</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="radio_gender" id="radio_f" value="f">
                                        <label class="form-check-label" for="radio_f">Feminino</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="text_birthdate" class="form-label fw-bold">Data de Nascimento <span class="text-danger">*</span></label>
                                <input type="text" class="form-control flatpickr" name="text_birthdate" id="text_birthdate" placeholder="Selecione a data" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="text_email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="text_email" id="text_email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="text_phone" class="form-label fw-bold">Telefone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="text_phone" id="text_phone" maxlength="9" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="text_interests" class="form-label fw-bold">Interesses</label>
                            <input type="text" class="form-control" name="text_interests" id="text_interests" placeholder="Ex: Futebol, Cinema, Leitura">
                            <div class="form-text">Separe os interesses por vírgula.</div>
                        </div>

                        <?php if(isset($validation_errors)): ?>
                        <div class="alert alert-danger p-3 mb-3">
                            <ul class="mb-0">
                                <?php foreach($validation_errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <?php if(isset($server_error)):?>
                            <div class="alert alert-danger p-3 mb-3">
                                <?= $server_error ?>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="?ct=agent&mt=my_clients" class="btn btn-outline-secondary px-4">
                                <i class="fa-solid fa-arrow-left me-2"></i>Voltar
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fa-solid fa-check me-2"></i>Guardar Cliente
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    flatpickr(".flatpickr", {
        dateFormat: "d-m-Y",
        locale: "pt" 
    });
</script>
<div class="container-fluid mt-5 mb-5">
    <div class="row justify-content-center pb-5">
        <div class="col-lg-8 col-md-10">
            <div class="card p-4 shadow">

                <div class="row justify-content-center">
                    <div class="col-12 col-xl-10">

                        <h4 class="text-center mb-4"><strong>Editar dados do cliente</strong></h4>
                        <hr>

                        <!-- Mensagens de erro de validação -->
                        <?php if (!empty($validation_errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Erros no formulário:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach ($validation_errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Mensagem de erro do servidor -->
                        <?php if (!empty($server_error)): ?>
                            <div class="alert alert-danger text-center">
                                <?= htmlspecialchars($server_error) ?>
                            </div>
                        <?php endif; ?>

                        <form action="?ct=agent&mt=edit_client_submit" method="post" replace >

                            <input type="hidden" name="id_client" value="<?= htmlspecialchars(aes_encrypt($client->id)) ?>">

                            <div class="mb-3">
                                <label for="text_name" class="form-label">Nome <span class="text-danger">*</span></label>
                                <input type="text" name="text_name" id="text_name" class="form-control"
                                       value="<?= htmlspecialchars($client->name) ?>" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Sexo <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="radio_gender"
                                                   id="radio_m" value="m" <?= $client->gender === 'm' ? 'checked' : '' ?> required>
                                            <label class="form-check-label" for="radio_m">Masculino</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="radio_gender"
                                                   id="radio_f" value="f" <?= $client->gender === 'f' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="radio_f">Feminino</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="text_birthdate" class="form-label">Data de nascimento <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control flatpickr" name="text_birthdate"
                                           id="text_birthdate"
                                           value="<?= htmlspecialchars(date('d-m-Y', strtotime($client->birthdate))) ?>"
                                           placeholder="dd-mm-aaaa" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="text_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="text_email" id="text_email" class="form-control"
                                           value="<?= htmlspecialchars($client->email) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="text_phone" class="form-label">Telefone <span class="text-danger">*</span></label>
                                    <input type="text" name="text_phone" id="text_phone" class="form-control"
                                           value="<?= htmlspecialchars($client->phone) ?>" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="text_interests" class="form-label">Interesses</label>
                                <small class="text-muted d-block mb-2">(Separe por vírgulas: ex: futebol, música, viagens)</small>
                                <input type="text" class="form-control" name="text_interests" id="text_interests"
                                       value="<?= htmlspecialchars($client->interests ?? '') ?>">
                            </div>

                            <div class="text-center mt-4">
                                <a href="?ct=agent&mt=my_clients" class="btn btn-outline-secondary me-3 px-4">
                                    <i class="fa-solid fa-xmark me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fa-regular fa-floppy-disk me-2"></i>Atualizar Cliente
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // Inicializa o Flatpickr apenas se o elemento existir
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#text_birthdate", {
            dateFormat: "d-m-Y",
            allowInput: true,
            locale: {
                firstDayOfWeek: 1
            }
        });
    });
</script>
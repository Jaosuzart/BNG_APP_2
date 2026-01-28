<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-12 bg-white p-4 shadow-sm rounded">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0"><i class="fa-solid fa-users me-2"></i>Meus Clientes</h4>
                <div class="text-end">
                    <a href="?ct=agent&mt=upload_file_frm" class="btn btn-secondary btn-sm px-3 mb-1"><i class="fa-solid fa-upload me-2"></i>Carregar</a>
                    <a href="?ct=agent&mt=new_client_frm" class="btn btn-primary btn-sm px-3 mb-1"><i class="fa-solid fa-plus me-2"></i>Novo</a>
                </div>
            </div>

            <hr>

            <?php if (count($clients) == 0) : ?>
                <div class="text-center my-5 text-muted">
                    <i class="fa-regular fa-folder-open fa-3x mb-3"></i>
                    <p>Não existem clientes registados.</p>
                </div>
            <?php else : ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle" id="table_clients">
                        <thead class="table-dark">
                            <tr>
                                <th>Nome</th>
                                <th class="text-center">Sexo</th>
                                <th class="text-center">Nascimento</th>
                                <th>Email</th>
                                <th class="text-center">Telefone</th>
                                <th>Interesses</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client) : ?>
                                <tr class="<?= !empty($client->deleted_at) ? 'table-danger' : '' ?>">
                                    <td><?= htmlspecialchars($client->name) ?></td>
                                    <td class="text-center"><?= $client->gender === 'm' ? 'M' : 'F' ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($client->birthdate)) ?></td>
                                    <td><?= htmlspecialchars($client->email) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($client->phone) ?></td>
                                    <td style="white-space: normal;">
                                        <?= htmlspecialchars($client->interests) ?>
                                    </td>

                                    <td class="text-end">
                                        <?php $esta_eliminado = !empty($client->deleted_at); ?>

                                        <?php if ($esta_eliminado): ?>
                                            <a href="?ct=agent&mt=recover_client&id=<?= aes_encrypt($client->id) ?>" class="btn btn-success btn-sm" title="Recuperar">
                                                <i class="fa-solid fa-trash-arrow-up"></i>
                                            </a>
                                            <a href="?ct=agent&mt=destroy_client&id=<?= aes_encrypt($client->id) ?>"
                                                class="btn btn-dark btn-sm ms-1"
                                                title="Eliminar Permanentemente"
                                                onclick="return confirm('Apagar para sempre?')">
                                                <i class="fa-solid fa-xmark"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="?ct=agent&mt=edit_client_frm&id=<?= aes_encrypt($client->id) ?>" class="btn btn-outline-primary btn-sm" title="Editar">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <a href="?ct=agent&mt=delete_client&id=<?= aes_encrypt($client->id) ?>" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                    <div class="mb-2">
                        <span class="badge bg-secondary">Total: <?= count($clients) ?></span>
                    </div>
                    <div>
                        <a href="?ct=agent&mt=export_clients_xlsx" class="btn btn-success btn-sm px-3 mb-1"><i class="fa-regular fa-file-excel me-2"></i>XLSX</a>
                        <a href="?ct=main&mt=index" class="btn btn-outline-dark btn-sm px-3 mb-1"><i class="fa-solid fa-chevron-left me-2"></i>Voltar</a>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

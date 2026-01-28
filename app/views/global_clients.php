<div class="container-fluid mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-12 bg-white p-4 shadow-sm rounded mb-5 pb-5">

            <div class="row mb-3">
                <div class="col">
                    <h4 class="mb-0"><i class="fa-solid fa-users-viewfinder me-2"></i>Clientes (Global)</h4>
                </div>
            </div>
            <hr>

            <?php if (empty($clients)) : ?>
                <div class="text-center my-5">
                    <p class="text-muted">Não existem clientes registados no sistema.</p>
                </div>
            <?php else : ?>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-nowrap" id="table_clients">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Gender</th>
                                <th class="text-center">Birthdate</th>
                                <th>Email</th>
                                <th class="text-center">Telefone</th>
                                <th>Interesses</th>
                                <th>Agente</th>
                                <th class="text-center">Registo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($client->name ?? '') ?></td>
                                    <td class="text-center"><?= $client->gender === 'm' ? 'M' : 'F' ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($client->birthdate)) ?></td>
                                    <td><?= htmlspecialchars($client->email ?? '') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($client->phone ?? '') ?></td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 150px;">
                                            <?= htmlspecialchars($client->interests ?? '') ?>
                                        </span>
                                    </td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($client->agent ?? '') ?></span></td>
                                    <td class="text-center small"><?= date('d/m/Y H:i', strtotime($client->created_at)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table> 
                </div>

                <div class="row mt-4 align-items-center">
                    <div class="col-md-6 mb-2">
                        <span class="fw-bold">Total: <span class="text-primary"><?= count($clients) ?></span></span>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="?ct=admin&mt=export_clients_XLSX" class="btn btn-success px-4">
                            <i class="fa-regular fa-file-excel me-2"></i>Exportar XLSX
                        </a>
                        <a href="?ct=main&mt=index" class="btn btn-dark px-4 ms-2">
                            <i class="fa-solid fa-chevron-left me-2"></i>Voltar
                        </a>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container-fluid py-5 mb-5">
    <div class="row justify-content-center">

        <div class="col-12 p-4 bg-white shadow-sm rounded">

            <div class="row">
                <div class="col">
                    <h4 class="mb-3"><i class="fa-solid fa-users-viewfinder me-2"></i>Clientes registados (Global)</h4>
                </div>
            </div>

            <hr>

            <?php if (empty($clients)) : ?>
                <div class="text-center my-5">
                    <p>Não existem clientes registados no sistema.</p>
                </div>
            <?php else : ?>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle" id="table_clients">
                        <thead class="table-dark">
                            <tr>
                                <th>Nome</th>
                                <th class="text-center">Sexo</th>
                                <th class="text-center">Data nascimento</th>
                                <th>Email</th>
                                <th class="text-center">Telefone</th>
                                <th>Interesses</th>
                                <th>Agente</th>
                                <th class="text-center">Data de registo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($client->name) ?></td>
                                    <td class="text-center"><?= $client->gender ?></td>
                                    <td class="text-center"><?= $client->birthdate ?></td>
                                    <td><?= htmlspecialchars($client->email) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($client->phone) ?></td>
                                    <td><small><?= htmlspecialchars($client->interests) ?></small></td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($client->agent) ?></span></td>
                                    <td class="text-center small"><?= $client->created_at ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table> 
                </div>

                <div class="row mt-5 align-items-center">
                    
                    <div class="col-12 col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="mb-0 fw-bold fs-5">Total: <span class="text-primary"><?= count($clients) ?></span></p>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="?ct=admin&mt=export_clients_XLSX" class="btn btn-secondary px-4">
                                <i class="fa-regular fa-file-excel me-2"></i>Exportar XLSX
                            </a>
                            <a href="?ct=main&mt=index" class="btn btn-dark px-4">
                                <i class="fa-solid fa-chevron-left me-2"></i>Voltar
                            </a>
                        </div>
                    </div>

                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#table_clients').DataTable({
            pageLength: 10,
            pagingType: "full_numbers",
            language: {
                "sEmptyTable": "Não foi encontrado nenhum registo",
                "sLoadingRecords": "A carregar...",
                "sProcessing": "A processar...",
                "sLengthMenu": "Mostrar _MENU_ registos",
                "sZeroRecords": "Não foram encontrados resultados",
                "sInfo": "Mostrando _START_ até _END_ de _TOTAL_",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registos",
                "sInfoFiltered": "(filtrado de _MAX_ registos)",
                "sSearch": "Procurar:",
                "oPaginate": { "sFirst": "<<", "sPrevious": "<", "sNext": ">", "sLast": ">>" }
            }
        });
    })
</script>
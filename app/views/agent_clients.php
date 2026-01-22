<div class="container-fluid">
    <div class="row justify-content-center">

        <div class="col-12 p-5 bg-white">

            <div class="row">
                <div class="col">
                    <h4 class="mb-3">Clientes registados</h4>
                </div>
                <div class="col text-end">
                    <a href="?ct=agent&mt=upload_file_frm" class="btn btn-secondary px-4"><i class="fa-solid fa-upload me-2"></i>Carregar ficheiro</a>
                    <a href="?ct=agent&mt=new_client_frm" class="btn btn-secondary px-4"><i class="fa-solid fa-plus me-2"></i>Novo cliente</a>
                </div>
            </div>

            <hr>

            <?php if (count($clients) == 0) : ?>
                <div class="text-center my-5">
                    <p>Não existem clientes registados.</p>
                </div>
            <?php else : ?>

                <table class="table table-striped table-bordered" id="table_clients">
                    <thead class="table-dark">
                        <tr>
                            <th>Nome</th>
                            <th class="text-center">Sexo</th>
                            <th class="text-center">Data nascimento</th>
                            <th>Email</th>
                            <th class="text-center">Telefone</th>
                            <th>Interesses</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client) : ?>
                            <tr class="<?= !empty($client->deleted_at) ? 'table-danger' : '' ?>">
                                <td><?= $client->name ?></td>
                                <td class="text-center"><?= $client->gender ?></td>
                                <td class="text-center"><?= $client->birthdate ?></td>
                                <td><?= $client->email ?></td>
                                <td class="text-center"><?= $client->phone ?></td>
                                <td><?= $client->interests ?></td>

                                <td class="text-end">
                                    <?php
                                    // Verifica se existe data de eliminação
                                    $esta_eliminado = !empty($client->deleted_at);
                                    ?>

                                    <?php if ($esta_eliminado): ?>

                                        <a href="?ct=agent&mt=recover_client&id=<?= aes_encrypt($client->id) ?>" class="btn btn-success btn-sm" title="Recuperar">
                                            <i class="fa-solid fa-trash-arrow-up"></i>
                                        </a>

                                        <a href="?ct=agent&mt=destroy_client&id=<?= aes_encrypt($client->id) ?>"
                                            class="btn btn-dark btn-sm ms-1"
                                            title="Eliminar Permanentemente"
                                            onclick="return confirm('Atenção! Isto vai apagar o registo para sempre. Continuar?')">
                                            <i class="fa-solid fa-xmark"></i>
                                        </a>

                                    <?php else: ?>
                                        <a href="?ct=agent&mt=edit_client_frm&id=<?= aes_encrypt($client->id) ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <a href="?ct=agent&mt=delete_client&id=<?= aes_encrypt($client->id) ?>" class="btn btn-outline-danger btn-sm">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
                <div class="row mt-3">
                    <div class="col">
                        <p class="mb-5">Total: <strong><?= count($clients) ?></strong></p>
                    </div>
                    <div class="col text-end">
                        <a href="?ct=agent&mt=export_clients_xlsx" class="btn btn-secondary px-4"><i class="fa-regular fa-file-excel me-2"></i>Exportar para XLSX</a>
                        <a href="?ct=main&mt=index" class="btn btn-secondary px-4"><i class="fa-solid fa-chevron-left me-2"></i>Voltar</a>
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
                decimal: "",
                emptyTable: "Sem dados disponíveis na tabela.",
                info: "Mostrando _START_ até _END_ de _TOTAL_ registos",
                infoEmpty: "Mostrando 0 até 0 de 0 registos",
                infoFiltered: "(Filtrando _MAX_ total de registos)",
                infoPostFix: "",
                thousands: ",",
                lengthMenu: "Mostrando _MENU_ registos por página.",
                loadingRecords: "Carregando...",
                processing: "Processando...",
                search: "Filtrar:",
                zeroRecords: "Nenhum registro encontrado.",
                paginate: {
                    first: "Primeira",
                    last: "Última",
                    next: "Seguinte",
                    previous: "Anterior"
                },
                aria: {
                    sortAscending: ": ative para classificar a coluna em ordem crescente.",
                    sortDescending: ": ative para classificar a coluna em ordem decrescente."
                }
            }
        });
    })
</script>
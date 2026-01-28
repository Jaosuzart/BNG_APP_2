</main>

<footer class="footer mt-auto">
  <div class="container text-center py-3">
      <?= APP_NAME ?> © <?= date("Y") ?> Todos os direitos reservados.
  </div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function () {

  const ptBR = {
    "decimal": "",
    "emptyTable": "Não foi encontrado nenhum registo",
    "info": "Mostrando _START_ a _END_ de _TOTAL_ registos", /* <--- É ESTE TEXTO QUE QUERES */
    "infoEmpty": "Mostrando 0 a 0 de 0 registos",
    "infoFiltered": "(filtrado de _MAX_ registos)",
    "lengthMenu": "Mostrar _MENU_ registos",
    "loadingRecords": "A carregar...",
    "processing": "A processar...",
    "search": "Procurar:",
    "zeroRecords": "Sem resultados",
    "paginate": {
      "first": "<<",
      "last": ">>",
      "next": ">",
      "previous": "<"
    }
  };

  // Tabela Global de Clientes
  if ($("#table_clients").length) {
    $("#table_clients").DataTable({
      pageLength: 10,
      pagingType: "full_numbers",
      language: ptBR
    });
  }

  // Tabela de Estatísticas (Agentes)
  if ($("#table_agents").length) {
    $("#table_agents").DataTable({
      pageLength: 10,
      pagingType: "full_numbers",
      language: ptBR,
      order: [[ 1, "desc" ]] // Ordena por quem tem mais clientes
    });
  }

});
</script>

</body>
</html>
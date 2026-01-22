<!-- ✅ jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- ✅ Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<!-- ✅ Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
window.addEventListener("load", function () {

    // ✅ Só roda se existir a tabela
    if (typeof $ !== "undefined" && $("#table_agents").length) {
        $("#table_agents").DataTable({
            pageLength: 5,
            pagingType: "full_numbers",
            language: {
                "sEmptyTable": "Não foi encontrado nenhum registo",
                "sLoadingRecords": "A carregar...",
                "sProcessing": "A processar...",
                "sLengthMenu": "_MENU_",
                "sZeroRecords": "Sem resultados",
                "sInfo": "_START_ a _END_ de _TOTAL_",
                "sInfoEmpty": "0 de 0",
                "sInfoFiltered": "(filtro de _MAX_)",
                "sSearch": "Procurar:",
                "oPaginate": { "sFirst": "<<", "sPrevious": "<", "sNext": ">", "sLast": ">>" }
            }
        });
    }

    // ✅ Só roda se existir o canvas
    const canvas = document.getElementById("myChart");
    if (canvas && typeof Chart !== "undefined") {
        const labels = <?= $chart_labels ?? '[]' ?>;
        const data = <?= $chart_totals ?? '[]' ?>;

        new Chart(canvas, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: "Total de Clientes",
                    data: data,
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: "y",
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

});
</script>

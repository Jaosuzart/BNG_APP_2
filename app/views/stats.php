<style>
    /* CORREÇÃO 1: Ajusta a caixa de pesquisa para não ser cortada em ecrãs menores */
    div.dataTables_wrapper div.dataTables_filter input {
        width: 100%;
        max-width: 150px; /* Limita a largura para não estourar o cartão */
        margin-left: 0.5em;
    }
    
    /* Garante que em telas muito pequenas a pesquisa e o mostrar fiquem alinhados */
    @media (min-width: 768px) {
        div.dataTables_wrapper div.dataTables_filter {
            text-align: left;
            margin-top: 0.5rem;
        }
    }
</style>

<div class="container-fluid py-5 mb-5 mt-4">

    <div class="row justify-content-center mb-4">
        
        <div class="col-12 col-xl-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white border-0 mt-3 ms-3">
                    <h4 class="mb-0"><i class="fa-solid fa-list me-2"></i>Clientes por Agente</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive px-2"> <table class="table table-striped table-hover w-100" id="table_agents">
                            <thead class="table-dark">
                                <tr>
                                    <th>Agente</th>
                                    <th class="text-center">Clientes</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($agents as $agent): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($agent->agente ?? '') ?></td>
                                        <td class="text-center"><?= $agent->total_clientes ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white border-0 mt-3 ms-3">
                    <h4 class="mb-0"><i class="fa-solid fa-chart-pie me-2"></i>Gráfico Visual</h4>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="height: 300px;">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card shadow border-0 text-center p-3 text-primary h-100">
                <h4><i class="fa-solid fa-user-tie"></i></h4>
                <h5 class="text-dark">Agentes</h5>
                <h3 class="fw-bold"><?= $global_stats['total_agents']->value ?></h3>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card shadow border-0 text-center p-3 text-success h-100">
                <h4><i class="fa-solid fa-users"></i></h4>
                <h5 class="text-dark">Clientes</h5>
                <h3 class="fw-bold"><?= $global_stats['total_clients']->value ?></h3>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card shadow border-0 text-center p-3 text-danger h-100">
                <h4><i class="fa-solid fa-user-xmark"></i></h4>
                <h5 class="text-dark">Eliminados</h5>
                <h3 class="fw-bold"><?= $global_stats['total_deleted_clients']->value ?></h3>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card shadow border-0 text-center p-3 text-info h-100">
                <h4><i class="fa-solid fa-calculator"></i></h4>
                <h5 class="text-dark">Média p/ Agente</h5>
                <h3 class="fw-bold"><?= $global_stats['average_clients_per_agent']->value ?></h3>
            </div>
        </div>
    </div>

    <div class="row mb-5 pb-5">
        <div class="col-12 text-center">
            <div class="card shadow-sm border-0 p-4">
                <h5 class="mb-3 text-muted">Exportar Relatório</h5>
                <div>
                    <a href="?ct=admin&mt=create_pdf_report" target="_blank" class="btn btn-dark btn-lg px-4 me-3 mb-2">
                        <i class="fa-solid fa-file-pdf me-2"></i>Ver PDF
                    </a>
                    <a href="?ct=admin&mt=send_pdf_report_email" class="btn btn-outline-dark btn-lg px-4 mb-2">
                        <i class="fa-regular fa-envelope me-2"></i>Enviar por Email
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
document.addEventListener("DOMContentLoaded", () => {

    const labels = <?= $chart_labels ?? '[]' ?>;
    const totals = <?= $chart_totals ?? '[]' ?>;

    const canvas = document.getElementById("myChart");
    if (!canvas) return;

    // 🔥 pega o gráfico existente pelo canvas (mais confiável)
    const oldChart = Chart.getChart(canvas);
    if (oldChart) oldChart.destroy();

    // ✅ cria o gráfico novo
    window.myChartInstance = new Chart(canvas, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: "Clientes",
                data: totals,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });

});
</script>


<div class="container-fluid py-5 bg-white">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-dark">Gestão de agentes</h3>
                <a href="?ct=admin&mt=new_agent_frm" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i> Novo agente
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nome (email)</th>
                                    <th>Perfil</th>
                                    <th>Último login</th>
                                    <th>Criado em</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($agents)): ?>
                                    <tr><td colspan="5" class="text-center py-4">Nenhum agente encontrado.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($agents as $agent): ?>
                                        
                                        <tr class="<?= !empty($agent->deleted_at) ? 'table-danger' : '' ?>">
                                            
                                            <td class="fw-medium">
                                                <?= htmlspecialchars($agent->name) ?>
                                                <?php if(!empty($agent->deleted_at)): ?>
                                                    <small class="text-danger d-block fw-bold">(Eliminado)</small>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td>
                                                <span class="badge bg-<?= $agent->profile === 'admin' ? 'danger' : 'success' ?>">
                                                    <?= $agent->profile === 'admin' ? 'Administrador' : 'Agente' ?>
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <?= $agent->last_login 
                                                    ? date('d-m-Y H:i', strtotime($agent->last_login))
                                                    : '<em class="text-muted">Nunca</em>'
                                                ?>
                                            </td>
                                            
                                            <td><?= date('d-m-Y H:i', strtotime($agent->created_at)) ?></td>
                                            
                                            <td class="text-center">
                                                <?php 
                                                // Verifica se o agente está eliminado
                                                $esta_eliminado = !empty($agent->deleted_at);
                                                ?>

                                                <?php if ($esta_eliminado): ?>
                                                    
                                                    <a href="?ct=admin&mt=recover_agent&id=<?= aes_encrypt($agent->id) ?>" 
                                                       class="btn btn-success btn-sm text-white shadow-sm" 
                                                       title="Recuperar Agente"
                                                       onclick="return confirm('Deseja realmente recuperar este agente?')">
                                                        <i class="fas fa-trash-arrow-up me-1"></i> Recuperar
                                                    </a>

                                                <?php else: ?>
                                                    
                                                    <a href="?ct=admin&mt=edit_agent_frm&id=<?= aes_encrypt($agent->id) ?>"
                                                       class="btn btn-warning btn-sm text-white me-2 shadow-sm">
                                                        <i class="fas fa-edit"></i> 
                                                    </a>

                                                    <?php if ($agent->profile !== 'admin'): ?>
                                                        <a href="?ct=admin&mt=delete_agent&id=<?= aes_encrypt($agent->id) ?>"
                                                           class="btn btn-danger btn-sm shadow-sm"
                                                           onclick="return confirm('Tem certeza que deseja eliminar este agente?')">
                                                            <i class="fas fa-trash"></i> 
                                                        </a>
                                                    <?php endif; ?>

                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
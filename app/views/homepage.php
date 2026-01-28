<div class="container-fluid py-5"> <div class="row justify-content-center">

        <div class="d-flex flex-row flex-wrap justify-content-center py-3 mb-4">

            <?php if ($user->profile == 'agent') : ?>
                <a href="?ct=agent&mt=my_clients" class="m-3">
                    <div class="home-option p-5 text-center">
                        <h3 class="mb-3"><i class="fa-solid fa-users"></i></h3>
                        <h5>Os meus clientes</h5>
                    </div>
                </a>
            <?php endif; ?>

            <?php if ($user->profile == 'agent') : ?>
                <a href="?ct=agent&mt=new_client_frm" class="m-3">
                    <div class="home-option p-5 text-center">
                        <h3 class="mb-3"><i class="fa-solid fa-user-plus"></i></h3>
                        <h5>Adicionar clientes</h5>
                    </div>
                </a>
            <?php endif; ?>

            <?php if ($user->profile == 'agent') : ?>
                <a href="?ct=agent&mt=upload_file_frm" class=" m-3">
                    <div class="home-option p-5 text-center">
                        <h3 class="mb-3"><i class="fa-solid fa-upload"></i></h3>
                        <h5>Carregar ficheiro</h5>
                    </div>
                </a>
            <?php endif; ?>

            <?php if ($user->profile == 'admin') : ?>
                <a href="?ct=admin&mt=all_clients" class="m-3">
                    <div class="home-option p-5 text-center">
                        <h3 class="mb-3"><i class="fa-solid fa-users"></i></h3>
                        <h5>Clientes</h5>
                    </div>
                </a>
            <?php endif; ?>

            <?php if ($user->profile == 'admin') : ?>
                <a href="?ct=admin&mt=stats" class="m-3">
                    <div class="home-option p-5 text-center">
                        <h3 class="mb-3"><i class="fa-solid fa-chart-column"></i></h3>
                        <h5>Estatística</h5>
                    </div>
                </a>
            <?php endif; ?>

            <?php if ($user->profile == 'admin') : ?>
                <a href="?ct=admin&mt=agents_management" class="m-3">
                    <div class="home-option p-5 text-center">
                        <h3 class="mb-3"><i class="fa-solid fa-user-gear"></i></h3>
                        <h5>Gestão de utilizadores</h5>
                    </div>
                </a>
            <?php endif; ?>

        </div>

    </div>
</div>
<?php

namespace bng\Controllers;

use bng\Controllers\BaseController;
use bng\Models\AdminModel;
use Mpdf\Mpdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Admin extends BaseController
{
    // =======================================================
    public function all_clients()
    {
        if (!check_session() || $_SESSION['user']->profile !== 'admin') {
            header('Location: index.php');
            exit;
        }

        $model = new AdminModel();
        $results = $model->get_all_clients();

        $data['user'] = $_SESSION['user'];
        $data['clients'] = $results->results;

        $this->view('layouts/html_header');
        $this->view('navbar', $data);
        $this->view('global_clients', $data);
        $this->view('footer');
        $this->view('layouts/html_footer', $data);
    }

    public function export_clients_XLSX()
    {
        if (!check_session() || $_SESSION['user']->profile !== 'admin') {
            header('Location: index.php');
            exit;
        }

        $model = new AdminModel();
        $clients = $model->get_all_clients()->results;

        $data = [['name', 'gender', 'birthdate', 'email', 'phone', 'interests', 'agent', 'created_at']];

        foreach ($clients as $client) {
            unset($client->id);
            $data[] = (array)$client;
        }

        $filename = 'clientes_bng_' . date('Ymd_His') . '.xlsx';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Clientes');
        $spreadsheet->addSheet($worksheet);
        $worksheet->fromArray($data);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        logger(get_active_user_name() . " - exportou lista global de clientes (XLSX): {$filename}");
        exit;
    }

    public function stats()
    {
        if (!check_session() || $_SESSION['user']->profile !== 'admin') {
            header('Location: index.php');
            exit;
        }

        $model = new AdminModel();
        $stats_agents = $model->get_agents_clients_stats();
        $data['agents'] = $stats_agents ? $stats_agents : [];

        $data['global_stats'] = $model->get_global_stats();
        $data['user'] = $_SESSION['user'];
if (!empty($data['agents'])) {
    $labels = [];
    $totals = [];
    foreach ($data['agents'] as $agent) {
        $labels[] = $agent->agente;
        $totals[] = $agent->total_clientes;
    }
    $data['chart_labels'] = json_encode($labels);
    $data['chart_totals'] = json_encode($totals);
} else {
    $data['chart_labels'] = json_encode([]);
    $data['chart_totals'] = json_encode([]);
}


$this->view('layouts/html_header', $data);
$this->view('navbar', $data);
$this->view('stats', $data);
$this->view('footer');
$this->view('layouts/html_footer', $data);
    }


   
    public function create_pdf_report($buffer = false)
    {
        if (!check_session() || $_SESSION['user']->profile !== 'admin') {
            header('Location: index.php');
            exit;
        }

        if (ob_get_level()) { ob_end_clean(); }

        if (!$buffer) {
            logger(get_active_user_name() . " - gerou relatório estatístico em PDF.");
        }

        $model = new AdminModel();
        $agents = $model->get_agents_clients_stats();
        $global = $model->get_global_stats();
        $date = date('d-m-Y');

        $total_agents = $global['total_agents']->value ?? 0;
        $total_clients = $global['total_clients']->value ?? 0;
        $total_deleted = $global['total_deleted_clients']->value ?? 0;
        $avg_clients = $global['average_clients_per_agent']->value ?? 0;
        $younger = $global['younger_client']->value ?? '-';
        $older = $global['oldest_client']->value ?? '-';
        $perc_males = $global['percentage_males']->value ?? 0;
        $perc_females = $global['percentage_females']->value ?? 0;

        $rows = '';
        if ($agents) {
            foreach ($agents as $agent) {
                $rows .= sprintf(
                    "<tr><td>%s</td><td class=\"text-center\">%d</td></tr>\n",
                    htmlspecialchars($agent->agente ?? ''),
                    $agent->total_clientes ? $agent->total_clientes : 0
                );
            }
        }

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; }
        .logo { width: 32px; vertical-align: middle; }
        .title { font-size: 18px; font-weight: bold; margin-left: 10px; }
        h3 { text-align: center; margin: 30px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f2f2f2; font-weight: bold; }
        th, td { border: 1px solid #333; padding: 8px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .w-60 { width: 60%; }
        .w-40 { width: 40%; }
    </style>
</head>
<body>
    <div class="header">
        <img src="assets/images/logo_32.png" class="logo" alt="Logo">
        <span class="title">bng app</span>
    </div>

    <h3>RELATÓRIO ESTATÍSTICO - {$date}</h3>

    <h4>Performance por Agente</h4>
    <table>
        <thead>
            <tr><th class="w-60">Agente</th><th class="w-40">N.º de Clientes</th></tr>
        </thead>
        <tbody>{$rows}</tbody>
    </table>

    <h4>Estatísticas Globais</h4>
    <table>
        <thead>
            <tr><th class="w-60">Item</th><th class="w-40">Valor</th></tr>
        </thead>
        <tbody>
            <tr><td>Total agentes:</td><td class="text-right">{$total_agents}</td></tr>
            <tr><td>Total clientes:</td><td class="text-right">{$total_clients}</td></tr>
            <tr><td>Total clientes removidos:</td><td class="text-right">{$total_deleted}</td></tr>
            <tr><td>Média de clientes por agente:</td><td class="text-right">{$avg_clients}</td></tr>
            <tr><td>Cliente mais novo:</td><td class="text-right">{$younger} anos</td></tr>
            <tr><td>Cliente mais velho:</td><td class="text-right">{$older} anos</td></tr>
            <tr><td>Percentagem homens:</td><td class="text-right">{$perc_males} %</td></tr>
            <tr><td>Percentagem mulheres:</td><td class="text-right">{$perc_females} %</td></tr>
        </tbody>
    </table>
</body>
</html>
HTML;

        
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'c', 
            'format' => 'A4',
            'tempDir' => __DIR__ . '/../../tmp/mpdf_core',
        ]);

        $mpdf->WriteHTML($html);

        if ($buffer) {
            return $mpdf->Output('', 'S');
        } else {
            $mpdf->Output('relatorio_bng_' . date('d-m-Y') . '.pdf', 'I');
            exit;
        }
    }

    // =======================================================
    // CORREÇÃO 3: Restaurar função que faltava
    // =======================================================
    public function agents_management()
    {
        if (!check_session() || $_SESSION['user']->profile !== 'admin') {
            header('Location: index.php');
            exit;
        }

        $model = new AdminModel();
        $data['agents'] = $model->get_agents_for_management()->results;
        $data['user'] = $_SESSION['user'];

        $this->view('layouts/html_header', $data);
        $this->view('navbar', $data);
        $this->view('agents_management', $data);
        $this->view('footer');
        // Passar dados para o footer aqui também é boa prática
        $this->view('layouts/html_footer', $data);
    }

    // =======================================================
    public function send_pdf_report_email()
    {
        if (!check_session() || $_SESSION['user']->profile !== 'admin') {
            header('Location: index.php');
            exit;
        }

        $pdf_content = $this->create_pdf_report(true);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = EMAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = EMAIL_USER;
            $mail->Password   = EMAIL_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = EMAIL_PORT;
            $mail->setFrom(EMAIL_FROM, 'BNG App Admin');
            $email_destino = $_SESSION['user']->name;
            $mail->addAddress($email_destino);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Relatório Estatístico bng app - ' . date('d-m-Y');
            $mail->Body    = '<p>Olá,</p><p>Segue em anexo o relatório gerado em ' . date('d-m-Y H:i') . '.</p><p><strong>bng app</strong></p>';
            $mail->AltBody = 'Relatório em anexo.';

            $mail->addStringAttachment($pdf_content, 'relatorio_bng_' . date('d-m-Y') . '.pdf', 'base64', 'application/pdf');

            $mail->send();

            logger(get_active_user_name() . " - enviou relatório PDF por e-mail com sucesso.");
             $_SESSION['success'] = 'Relatório enviado por e-mail com sucesso!';
        } catch (Exception) {
            logger("Erro ao enviar relatório por e-mail: " . $mail->ErrorInfo, 'error');
            die("ERRO DE ENVIO: " . $mail->ErrorInfo . "<br>Verifique a Senha de App no config.php/env.");
        }

        header('Location: ?ct=admin&mt=stats');
        exit;
    }

    // =======================================================
    public function new_agent_frm()
    {
        if (!check_session() || $_SESSION['user']->profile !== 'admin') {
            header('Location: index.php');
            exit;
        }

        $data['user'] = $_SESSION['user'];
        $this->view('layouts/html_header', $data);
        $this->view('navbar', $data);
        $this->view('agents_add_new_frm', $data);
        $this->view('footer');
        $this->view('layouts/html_footer', $data);
    }

    // =======================================================
    public function new_agent_submit()
    {
        if (!check_session() || $_SESSION['user']->profile !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php'); exit;
        }

        $validation_errors = [];
        $email = trim($_POST['text_name'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $validation_errors[] = 'Email inválido.'; }

        $profile = $_POST['select_profile'] ?? '';
        if (!in_array($profile, ['admin', 'agent'])) { $validation_errors[] = 'Perfil inválido.'; }

        $password = $_POST['text_password'] ?? '';
        
        $regex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d.*\d.*\d.*\d)(?=.*\?)(?=.*\*)(?=.*_)(?=.*\.)(?=.*-).{12}$/";
        if (!preg_match($regex, $password)) {
            $validation_errors[] = 'A senha deve ter 12 caracteres: 1 Maiúscula, 1 Minúscula, 4 Dígitos e os símbolos: ? * _ . -';
        }

        if (!empty($validation_errors)) {
            $_SESSION['validation_errors'] = $validation_errors;
            header('Location: ?ct=admin&mt=new_agent_frm');
            exit;
        }

        $model = new AdminModel();
        $result = $model->add_new_agent($email, $profile, $password);

        if (!$result) {
            $_SESSION['server_error'] = 'Erro ao criar agente.';
            header('Location: ?ct=admin&mt=new_agent_frm');
            exit;
        }

        $this->send_welcome_email($email, $password, $profile);

        logger(get_active_user_name() . " criou novo agente: $email (perfil: $profile)");
        $_SESSION['success'] = 'Agente criado com sucesso! Email de boas-vindas enviado.';
        header('Location: ?ct=admin&mt=agents_management');
        exit;
    }

    // =======================================================
    public function edit_agent_frm()
    {
        if (!check_session() || $_SESSION['user']->profile !== 'admin') {
            header('Location: index.php');
            exit;
        }

        $encrypted_id = $_GET['id'] ?? null;
        if (!$encrypted_id || !($id = aes_decrypt($encrypted_id))) {
            $_SESSION['server_error'] = 'ID inválido.';
            header('Location: ?ct=admin&mt=agents_management');
            exit;
        }

        $model = new AdminModel();
        $result = $model->get_agent_by_id($id);

        if (!$result || empty($result->results)) {
            $_SESSION['server_error'] = 'Agente não encontrado.';
            header('Location: ?ct=admin&mt=agents_management');
            exit;
        }

        $data['agent'] = $result->results[0];
        $data['user'] = $_SESSION['user'];

        $this->view('layouts/html_header', $data);
        $this->view('navbar', $data);
        $this->view('agents_edit_frm', $data);
        $this->view('footer');
        $this->view('layouts/html_footer', $data);
    }

    // =======================================================
    public function edit_agent_submit()
    {
        if (!check_session() || $_SESSION['user']->profile !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php'); exit;
        }

        $validation_errors = [];
        $encrypted_id = $_POST['id'] ?? null;
        $id = aes_decrypt($encrypted_id);
        if (!$id) { $validation_errors[] = 'ID inválido.'; }

        $email = trim($_POST['text_email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $validation_errors[] = 'Email inválido.'; }

        $profile = $_POST['select_profile'] ?? '';
        if (!in_array($profile, ['agent', 'admin'])) { $validation_errors[] = 'Perfil inválido.'; }

        $password = $_POST['text_password'] ?? '';
        
        if (!empty($password)) {
             $regex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d.*\d.*\d.*\d)(?=.*\?)(?=.*\*)(?=.*_)(?=.*\.)(?=.*-).{12}$/";
             if (!preg_match($regex, $password)) {
                 $validation_errors[] = 'A nova senha deve ter 12 caracteres: 1 Maiúscula, 1 Minúscula, 4 Dígitos e os símbolos: ? * _ . - ';
             }
        }

        if (!empty($validation_errors)) {
            $_SESSION['validation_errors'] = $validation_errors;
            header('Location: ?ct=admin&mt=edit_agent_frm&id=' . urlencode($encrypted_id));
            exit;
        }

        $model = new AdminModel();
        $model->update_agent($id, $email, $profile, $password);

        $this->send_notification_email($email, $profile === 'admin' ? 'Administrador' : 'Agente');

        $_SESSION['success'] = 'Agente atualizado com sucesso!';
        header('Location: ?ct=admin&mt=agents_management');
        exit;
    }

    // =======================================================
    public function delete_agent()
    {
        if (!check_session() || $_SESSION['user']->profile !== 'admin') {
            header('Location: index.php');
            exit;
        }

        $encrypted_id = $_GET['id'] ?? null;
        if (!$encrypted_id || !($id = aes_decrypt($encrypted_id))) {
            $_SESSION['server_error'] = 'ID inválido.';
            header('Location: ?ct=admin&mt=agents_management');
            exit;
        }

        if ($id == 1) {
            $_SESSION['server_error'] = 'Não é possível eliminar o administrador principal.';
            header('Location: ?ct=admin&mt=agents_management');
            exit;
        }

        $model = new AdminModel();
        $success = $model->delete_agent($id);

        if ($success) {
            logger(get_active_user_name() . " eliminou o agente ID $id");
            $_SESSION['success'] = 'Agente e seus clientes foram eliminados com sucesso!';
        } else {
            $_SESSION['server_error'] = 'Erro ao eliminar agente.';
        }

        header('Location: ?ct=admin&mt=agents_management');
        exit;
    }

    // =======================================================
    public function recover_agent()
    {
        if (!check_session() || $_SESSION['user']->profile !== 'admin') {
            header('Location: index.php');
            exit;
        }

        $id_agent = aes_decrypt($_GET['id']);
        
        if(!$id_agent){
            header('Location: ?ct=admin&mt=agents_management');
            exit;
        }

        $model = new AdminModel();
        $model->recover_agent($id_agent);

        logger(get_active_user_name() . " recuperou o agente ID: $id_agent");
        
        $_SESSION['success'] = 'Agente recuperado com sucesso!';
        header('Location: ?ct=admin&mt=agents_management');
        exit;
    }

    // =======================================================
    private function send_notification_email($to_email, $profile_name)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = EMAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = EMAIL_USER;
            $mail->Password   = EMAIL_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = EMAIL_PORT;

            $mail->setFrom(EMAIL_FROM, 'BNG App Admin');
            $mail->addAddress($to_email);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Atualização na sua conta bng app';
            $mail->Body    = "
                <p>Olá,</p>
                <p>Sua conta no <strong>bng app</strong> foi atualizada pelo administrador.</p>
                <p><strong>Novo perfil:</strong> $profile_name</p>
                " . (!empty($_POST['text_password']) ? "<p>Sua senha foi alterada.</p>" : "<p>Sua senha não foi alterada.</p>") . "
                <p>Caso não reconheça esta ação, entre em contato imediatamente.</p>
                <hr>
                <small>Mensagem automática – não responder.</small>
            ";
            $mail->AltBody = "Sua conta foi atualizada. Perfil: $profile_name.";

            $mail->send();
            logger("E-mail de notificação enviado para: $to_email");

        } catch (Exception $e) {
            logger("Falha ao enviar notificação para $to_email: " . $mail->ErrorInfo, 'error');
            $_SESSION['warning'] = 'Agente atualizado, mas falha no envio de e-mail.';
        }
    }

    // =======================================================
    private function send_welcome_email($to_email, $password, $profile_name)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = EMAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = EMAIL_USER;
            $mail->Password   = EMAIL_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = EMAIL_PORT;

            $mail->setFrom(EMAIL_FROM, 'BNG App Admin');
            $mail->addAddress($to_email);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Bem-vindo ao bng app!';
            $mail->Body    = "
                <h2>Bem-vindo ao bng app!</h2>
                <p>Sua conta foi criada com sucesso.</p>
                <p><strong>Login (e-mail):</strong> $to_email</p>
                <p><strong>Senha inicial:</strong> $password</p>
                <p><strong>Perfil:</strong> $profile_name</p>
                <br>
                <p>Acesse aqui: <a href='http://localhost/APP_NAME2021/public/'>http://localhost/APP_NAME2021/public/</a></p>
                <p><strong>Recomendamos alterar sua senha no primeiro acesso.</strong></p>
                <hr>
                <small>Mensagem automática – não responder.</small>
            ";
            $mail->AltBody = "Bem-vindo! Login: $to_email | Senha: $password | Perfil: $profile_name";

            $mail->send();
            logger("E-mail de boas-vindas enviado para novo agente: $to_email");

        } catch (Exception) {
            logger("Falha ao enviar e-mail de boas-vindas para $to_email: " . $mail->ErrorInfo, 'error');
        }
    }
}
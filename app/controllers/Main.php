<?php

namespace bng\Controllers;

use bng\Controllers\BaseController;
use bng\Models\Agents;

class Main extends BaseController
{
    // =======================================================
    // INDEX
    // =======================================================
    public function index()
    {
        if(!check_session())
        {
            $this->login(); 
            return;
        }

        $data['user'] = $_SESSION['user'];

        $this->view('layouts/html_header');
        $this->view('navbar', $data);
        $this->view('homepage', $data);
        $this->view('layouts/html_footer');
    }

    // =======================================================
    // LOGIN
    // =======================================================
    public function login()
    {
        if(check_session())
        {
            $this->index();
            return;
        }

        $data = [];
        if(!empty($_SESSION['validation_errors']))
        {
            $data['validation_errors'] = $_SESSION['validation_errors'];
            unset($_SESSION['validation_errors']);
        }

        if(!empty($_SESSION['server_error'])){
            $data['server_error'] = $_SESSION['server_error'];
            unset($_SESSION['server_error']);
        }

        $this->view('layouts/html_header');
        $this->view('login_frm', $data);
        $this->view('layouts/html_footer');
    }

    // =======================================================
    // LOGIN SUBMIT
    // =======================================================
    public function login_submit()
    {
        if(check_session()){
            $this->index();
            return;
        }

        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            $this->index();
            return;
        }

        $validation_errors = [];
        if(empty($_POST['text_username']) || empty($_POST['text_password'])){
            $validation_errors[] = "Username e password são obrigatórios.";
        }

        if(!empty($validation_errors)){
            $_SESSION['validation_errors'] = $validation_errors;
            $this->login(); 
            return;
        }

        $username = $_POST['text_username'];
        $password = $_POST['text_password'];

        if(!filter_var($username, FILTER_VALIDATE_EMAIL))
        {
            $validation_errors[] = 'O username tem que ser um email válido.';
            $_SESSION['validation_errors'] = $validation_errors;
            $this->login(); 
            return;
        }

        if(strlen($username) < 5 || strlen($username) > 50){
            $validation_errors[] = 'O username deve ter entre 5 e 50 caracteres.';
            $_SESSION['validation_errors'] = $validation_errors;
            $this->login(); 
            return;
        }

        // --- VALIDAÇÃO DA PASSWORD (LOGIN) ---
        // Verifica comprimento exato
        if(strlen($password) != 12){
            $validation_errors[] = 'A password deve ter exatamente 12 caracteres.';
            $_SESSION['validation_errors'] = $validation_errors;
            $this->login(); 
            return;
        }
        
        // Regra Rigorosa: 1 Maiúscula, 1 Minúscula, 4 Dígitos, e os símbolos ? * _ . -
        // Exemplo que passa: Bng_2025-?.*
        if(!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d.*\d.*\d.*\d)(?=.*\?)(?=.*\*)(?=.*_)(?=.*\.)(?=.*-).{12}$/", $password)){
            $validation_errors[] = "A password incorreta (regra de complexidade).";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->login(); 
            return;
        }

        $model = new Agents();
        $user = $model->get_user_data($username);
      if ($user['status'] != 'success') {
    $data['server_error'] = "Utilizador ou password inválidos.";
    $this->view('layouts/html_header');
    $this->view('login_frm', $data);
    $this->view('layouts/html_footer');
    return;
}
        $results = $model->check_login($username, $password);
        if(!$results['status']){
            $data['server_error'] = "Utilizador ou password inválidos.";
            $this->view('layouts/html_header');
            $this->view('login_frm', $data);
            $this->view('layouts/html_footer');
            return;
        }

        logger("$username - login com sucesso");

        $results = $model->get_user_data($username);
        $_SESSION['user'] = $results['data'];

        $model->set_user_last_login($_SESSION['user']->id);

        $this->index();
    }

    // =======================================================
    // LOGOUT
    // =======================================================
    public function logout()
    {
        if(!check_session()){
            $this->index();
            return;
        }

        logger($_SESSION['user']->name . ' - fez logout');
        unset($_SESSION['user']);
        
        header("Location: ?ct=main&mt=login");
    }

    // =======================================================
    // RECUPERAÇÃO DE PASSWORD (ESQUECI A SENHA)
    // =======================================================

    // 1. Formulário Inicial
    public function reset_password_frm()
    {
        if (isset($_SESSION['user'])) {
            $this->index();
            return;
        }
        
        $this->view('layouts/html_header');
        $this->view('reset_password_frm');
        $this->view('layouts/html_footer');
    }

    // 2. Processar pedido e enviar código (VIA SMTP)
    public function reset_password_request_submit()
    {
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            $this->index();
            return;
        }

        if(empty($_POST['text_username'])){
            $this->reset_password_frm(); 
            return;
        }

        $email = $_POST['text_username'];

        // 1. GERAR CÓDIGO
        $code = rand(100000, 999999);

        // 2. GUARDAR NA SESSÃO
        $_SESSION['reset_code'] = $code;
        $_SESSION['reset_email'] = $email;

        // 3. ENVIAR EMAIL (Caminho corrigido manualmente)
        require_once(__DIR__ . '/../models/SendEmail.php'); 

        $email_sender = new \bng\Models\SendEmail(); 
        $resultado = $email_sender->send_reset_code($email, $code);
if (!is_array($resultado) || !array_key_exists('status', $resultado)) {
    $resultado = [
        'status' => false,
        'error'  => 'Falha inesperada ao enviar email. (send_code não retornou status)'
    ];
}

if (!empty($resultado['status'])) {
    $data['success_message'] =
        "Foi enviado um código de verificação para o email: <strong>$email</strong>.<br>
         Verifique a sua caixa de entrada (e spam).";

    $this->view('layouts/html_header');
    $this->view('reset_password_insert_code', $data);
    $this->view('layouts/html_footer');

} else {

    $erroMsg = $resultado['error'] ?? 'Sem detalhes adicionais.';
    $data['erro'] = "Erro ao enviar email: " . $erroMsg;

    $this->view('layouts/html_header');
    $this->view('reset_password_frm', $data);
    $this->view('layouts/html_footer');
}
    }
    // 3. Inserir Código (View)
    public function reset_password_insert_code()
    {
        if (isset($_SESSION['user'])) {
            $this->index();
            return;
        }
        
        $this->view('layouts/html_header');
        $this->view('reset_password_insert_code');
        $this->view('layouts/html_footer');
    }

    // 4. Verificar se o código está correto (Lógica)
    public function reset_password_check_code_submit()
    {
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            $this->index();
            return;
        }

        if(!isset($_SESSION['reset_code'])){
            $this->index();
            return;
        }

        $code_user = trim($_POST['text_code']);
        $code_system = $_SESSION['reset_code'];

        if($code_user != $code_system){
            $data['erro'] = "O código introduzido está incorreto.";
            $this->view('layouts/html_header');
            $this->view('reset_password_insert_code', $data);
            $this->view('layouts/html_footer');
            return;
        }

        // Avança para a tela de definir a nova password
        $this->reset_password_define_password_frm();
    }

    // 5. Definir Nova Senha (View)
    public function reset_password_define_password_frm()
    {
        if (isset($_SESSION['user'])) {
            $this->index();
            return;
        }
        
        $this->view('layouts/html_header');
        $this->view('reset_password_define_password_frm');
        $this->view('layouts/html_footer');
    }

    // 6. Gravar a Nova Password (SUBMIT FINAL)
    public function reset_password_define_new_password_submit()
    {
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            $this->index();
            return;
        }

        if(!isset($_SESSION['reset_email'])){
            $this->index();
            return;
        }

        $new_password = $_POST['text_new_password'];
        $repeat_password = $_POST['text_repeat_new_password'];
        $email = $_SESSION['reset_email'];

        // --- VALIDAÇÕES ---
        if($new_password != $repeat_password){
            $data['erro'] = "As passwords não coincidem.";
            $this->view('layouts/html_header');
            $this->view('reset_password_define_password_frm', $data);
            $this->view('layouts/html_footer');
            return;
        }

        if(strlen($new_password) != 12){
            $data['erro'] = "A password deve ter exatamente 12 caracteres.";
            $this->view('layouts/html_header');
            $this->view('reset_password_define_password_frm', $data);
            $this->view('layouts/html_footer');
            return;
        }

        // Regra Rigorosa (Mesma do Login)
        $regex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d.*\d.*\d.*\d)(?=.*\?)(?=.*\*)(?=.*_)(?=.*\.)(?=.*-).{12}$/";
        if(!preg_match($regex, $new_password)){
            $data['erro'] = "A password deve ter: 1 Maiúscula, 1 Minúscula, 4 Dígitos e os símbolos: ? * _ . -";
            $this->view('layouts/html_header');
            $this->view('reset_password_define_password_frm', $data);
            $this->view('layouts/html_footer');
            return;
        }

        // Atualizar na Base de Dados (por Email)
        $model = new Agents();
        $model->update_password_by_email($email, $new_password);

        // Limpar a sessão
        unset($_SESSION['reset_code']);
        unset($_SESSION['reset_email']);

        // Mostrar Sucesso
        $this->view('layouts/html_header');
        $this->view('reset_password_define_password_success');
        $this->view('layouts/html_footer');
    }

    // =======================================================
    // CHANGE PASSWORD (PERFIL - LOGADO)
    // =======================================================
    public function change_password_frm()
    {
        if(!check_session()){
            $this->index();
            return;
        }

        $data['user'] = $_SESSION['user'];

        if(!empty($_SESSION['validation_errors'])){
            $data['validation_errors'] = $_SESSION['validation_errors'];
            unset($_SESSION['validation_errors']);
        }

        if(!empty($_SESSION['server_errors'])){
            $data['server_errors'] = $_SESSION['server_errors'];
            unset($_SESSION['server_errors']);
        }

        $this->view('layouts/html_header');
        $this->view('navbar', $data);
        $this->view('profile_change_password_frm', $data);
        $this->view('layouts/html_footer');
    }

    public function change_password_submit()
    {
        if(!check_session()){
            $this->index();
            return;
        }

        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            $this->index();
            return;
        }

        $validation_errors = [];

        if(empty($_POST['text_current_password']) || empty($_POST['text_new_password']) || empty($_POST['text_repeat_new_password'])){
            $validation_errors[] = "Todos os campos são obrigatórios.";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }

        $current_password = $_POST['text_current_password'];
        $new_password = $_POST['text_new_password'];
        $repeat_new_password = $_POST['text_repeat_new_password'];

        // Comprimento 12
        if(strlen($new_password) != 12){
            $validation_errors[] = "A nova password deve ter exatamente 12 caracteres.";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }

        // Regra Rigorosa (Mesma do Reset)
        $regex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d.*\d.*\d.*\d)(?=.*\?)(?=.*\*)(?=.*_)(?=.*\.)(?=.*-).{12}$/";
        if(!preg_match($regex, $new_password)){
            $validation_errors[] = "A nova password deve ter: 1 Maiúscula, 1 Minúscula, 4 Dígitos e os símbolos: ? * _ . -";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }

        if($new_password != $repeat_new_password){
            $validation_errors[] = "As passwords novas não coincidem.";
            $_SESSION['validation_errors'] = $validation_errors;
            $this->change_password_frm();
            return;
        }

        $model = new Agents();
        $results = $model->check_current_password($current_password);

        if(!$results['status']){
            $server_errors[] = "A password atual não está correta.";
            $_SESSION['server_errors'] = $server_errors;
            $this->change_password_frm();
            return;
        }

        $model->update_agent_password($new_password);

        logger($_SESSION['user']->name . " - password alterada com sucesso no perfil.");

        $data['user'] = $_SESSION['user'];
        $this->view('layouts/html_header');
        $this->view('navbar', $data);
        $this->view('profile_change_password_success');
        $this->view('layouts/html_footer');
    }
}
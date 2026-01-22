<?php

namespace bng\Controllers;

use bng\Controllers\BaseController;
use bng\Models\Agents;

class Agent extends BaseController
{
    // =======================================================
    // LISTAR MEUS CLIENTES
    // =======================================================
    public function my_clients()
    {
        if (!check_session() || $_SESSION['user']->profile != 'agent') {
            header('Location: index.php');
            exit;
        }

        // get all agent clients
        $id_agent = $_SESSION['user']->id;
        $model = new Agents();
        $results = $model->get_agent_clients($id_agent);

        $data['user'] = $_SESSION['user'];
        $data['clients'] = $results['data'];

        $this->view('layouts/html_header');
        $this->view('navbar', $data);
        $this->view('agent_clients', $data);
        $this->view('footer');
        $this->view('layouts/html_footer');
    }

    // =======================================================
    // NOVO CLIENTE - FORMULÁRIO
    // =======================================================
    public function new_client_frm()
    {
        if (!check_session() || $_SESSION['user']->profile != 'agent') {
            header('Location: index.php');
            exit;
        }

        $data['user'] = $_SESSION['user'];
        $data['flatpickr'] = true;

        if (!empty($_SESSION['validation_errors'])) {
            $data['validation_errors'] = $_SESSION['validation_errors'];
            unset($_SESSION['validation_errors']);
        }

        if (!empty($_SESSION['server_error'])) {
            $data['server_error'] = $_SESSION['server_error'];
            unset($_SESSION['server_error']);
        }

        $this->view('layouts/html_header', $data);
        $this->view('navbar', $data);
        $this->view('insert_client_frm', $data);
        $this->view('footer');
        $this->view('layouts/html_footer');
    }

    // =======================================================
    // NOVO CLIENTE - SUBMIT
    // =======================================================
    public function new_client_submit()
    {
        if (!check_session() || $_SESSION['user']->profile != 'agent' || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php');
            exit;
        }

        $validation_errors = [];

        // Validações
        if (empty($_POST['text_name'])) {
            $validation_errors[] = "Nome é de preenchimento obrigatório.";
        } elseif (strlen($_POST['text_name']) < 3 || strlen($_POST['text_name']) > 50) {
            $validation_errors[] = "O nome deve ter entre 3 e 50 caracteres.";
        }

        if (empty($_POST['radio_gender'])) {
            $validation_errors[] = "É obrigatório definir o género.";
        }

        if (empty($_POST['text_birthdate'])) {
            $validation_errors[] = "Data de nascimento é obrigatória.";
        } else {
            $birthdate = \DateTime::createFromFormat('d-m-Y', $_POST['text_birthdate']);
            if (!$birthdate) {
                $validation_errors[] = "A data de nascimento não está no formato correto.";
            } else {
                $today = new \DateTime();
                if ($birthdate >= $today) {
                    $validation_errors[] = "A data de nascimento tem que ser anterior ao dia atual.";
                }
            }
        }

        if (empty($_POST['text_email'])) {
            $validation_errors[] = "Email é de preenchimento obrigatório.";
        } elseif (!filter_var($_POST['text_email'], FILTER_VALIDATE_EMAIL)) {
            $validation_errors[] = "Email não é válido.";
        }

        if (empty($_POST['text_phone'])) {
            $validation_errors[] = "Telefone é de preenchimento obrigatório.";
        } elseif (!preg_match("/^9{1}\d{8}$/", $_POST['text_phone'])) {
            $validation_errors[] = "O telefone deve começar por 9 e ter 9 algarismos no total.";
        }

        if (!empty($validation_errors)) {
            $_SESSION['validation_errors'] = $validation_errors;
            $this->new_client_frm();
            return;
        }

        $model = new Agents();
        $results = $model->check_if_client_exists($_POST);

        if ($results['status']) {
            $_SESSION['server_error'] = "Já existe um cliente com esse nome.";
            $this->new_client_frm();
            return;
        }

        $model->add_new_client_to_database($_POST);
        logger(get_active_user_name() . " - adicionou novo cliente: " . $_POST['text_email']);
        $this->my_clients();
    }

    // =======================================================
    // EDITAR CLIENTE - FORMULÁRIO
    // =======================================================
   // =======================================================
    // EDITAR CLIENTE - FORMULÁRIO
    // =======================================================
    public function edit_client_frm()
    {
        if (!check_session() || $_SESSION['user']->profile != 'agent') {
            header('Location: index.php');
            exit;
        }

        if(!isset($_GET['id'])){
            header('Location: ?ct=agent&mt=my_clients');
            exit;
        }

        $id_client = aes_decrypt($_GET['id']);
        if(!$id_client){
            header('Location: ?ct=agent&mt=my_clients');
            exit;
        }

        $model = new Agents();
        $results = $model->get_client_data($id_client);

        if($results['status'] == 'error'){ 
             header('Location: ?ct=agent&mt=my_clients');
             exit;
        }

        $data['user'] = $_SESSION['user'];
        $data['client'] = $results['data'];
        $data['flatpickr'] = true; // Ativa o calendário

        // Recupera erros da sessão
        if (!empty($_SESSION['validation_errors'])) {
            $data['validation_errors'] = $_SESSION['validation_errors'];
            unset($_SESSION['validation_errors']);
        }
        if (!empty($_SESSION['server_error'])) {
            $data['server_error'] = $_SESSION['server_error'];
            unset($_SESSION['server_error']);
        }

        // --- CORREÇÃO AQUI ---
        // Adicionei '$data' para que o cabeçalho saiba que deve carregar o flatpickr
        $this->view('layouts/html_header', $data); 
        
        $this->view('navbar', $data);
        $this->view('edit_client_frm', $data);
        $this->view('footer');
        $this->view('layouts/html_footer');
    }
    // =======================================================
    // EDITAR CLIENTE - SUBMIT
    // =======================================================
    public function edit_client_submit()
    {
        if (!check_session() || $_SESSION['user']->profile != 'agent' || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php');
            exit;
        }

        $id_client = aes_decrypt($_POST['id_client']);
        if(!$id_client){
            header('Location: ?ct=agent&mt=my_clients');
            exit;
        }

        $validation_errors = [];
        if (empty($_POST['text_name']) || empty($_POST['radio_gender']) || empty($_POST['text_birthdate']) || empty($_POST['text_email']) || empty($_POST['text_phone'])) {
            $validation_errors[] = "Todos os campos obrigatórios devem ser preenchidos.";
        } else {
            if (!filter_var($_POST['text_email'], FILTER_VALIDATE_EMAIL)) {
                $validation_errors[] = "Email inválido.";
            }
        }

        if (!empty($validation_errors)) {
            $_SESSION['validation_errors'] = $validation_errors;
            header('Location: ?ct=agent&mt=edit_client_frm&id=' . aes_encrypt($id_client));
            exit;
        }

        // Verifica duplicados (outro cliente com mesmo nome)
        $model = new Agents();
        $results = $model->check_other_client_with_same_name($id_client, $_POST['text_name']);

        if ($results['status']) {
            $_SESSION['server_error'] = "Já existe outro cliente com o mesmo nome.";
            header('Location: ?ct=agent&mt=edit_client_frm&id=' . aes_encrypt($id_client));
            return;
        }

        $model->update_client_data($id_client, $_POST);
        logger(get_active_user_name() . " editou o cliente ID: $id_client");

        header('Location: ?ct=agent&mt=my_clients');
    }

    // =======================================================
    // ELIMINAR CLIENTE (Soft Delete)
    // =======================================================


    // =======================================================
    // RECUPERAR CLIENTE
    // =======================================================
    public function recover_client()
    {
        if (!check_session() || $_SESSION['user']->profile != 'agent') {
            header('Location: index.php');
            exit;
        }

        $id_client = aes_decrypt($_GET['id']); 
        
        if(!$id_client){
            header('Location: ?ct=agent&mt=my_clients');
            exit;
        }

        $model = new Agents();
        $model->recover_client($id_client); // Certifique-se que existe no Model (UPDATE deleted_at = NULL)

        logger(get_active_user_name() . " recuperou o cliente ID: $id_client");
        
        header('Location: ?ct=agent&mt=my_clients');
    }

    // =======================================================
    // UPLOAD FILE FORM
    // =======================================================
    public function upload_file_frm()
    {
        if (!check_session() || $_SESSION['user']->profile != 'agent') {
            header('Location: index.php');
            exit;
        }

        $data['user'] = $_SESSION['user'];

        if (!empty($_SESSION['server_error'])) {
            $data['server_error'] = $_SESSION['server_error'];
            unset($_SESSION['server_error']);
        }
        if (!empty($_SESSION['report'])) {
            $data['report'] = $_SESSION['report'];
            unset($_SESSION['report']);
        }

        $this->view('layouts/html_header');
        $this->view('navbar', $data);
        $this->view('upload_file_with_clients_frm', $data);
        $this->view('footer');
        $this->view('layouts/html_footer');
    }

    // =======================================================
    // UPLOAD FILE SUBMIT
    // =======================================================
    public function upload_file_submit()
    {
        if (!check_session() || $_SESSION['user']->profile != 'agent') {
            header('Location: index.php'); exit;
        }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php'); exit;
        }

        if (empty($_FILES) || empty($_FILES['clients_file']['name'])) {
            $_SESSION['server_error'] = "Faça o carregamento de um ficheiro XLSX ou CSV.";
            $this->upload_file_frm(); return;
        }

        $valid_extensions = ['xlsx', 'csv'];
        $tmp = explode('.', $_FILES['clients_file']['name']);
        $extension = end($tmp);
        if (!in_array($extension, $valid_extensions)) {
            $_SESSION['server_error'] = "O ficheiro deve ser do tipo XLSX ou CSV.";
            $this->upload_file_frm(); return;
        }

        if ($_FILES['clients_file']['size'] > 2000000) {
            $_SESSION['server_error'] = "O ficheiro deve ter, no máximo, 2 MB.";
            $this->upload_file_frm(); return;
        }

        $file_path = __DIR__ . '/../../uploads/dados_' . time() . '.' . $extension;
        if (move_uploaded_file($_FILES['clients_file']['tmp_name'], $file_path)) {
            $result = $this->has_valid_header($file_path);
            if ($result) {
                $this->load_file_data_to_database($file_path);
            } else {
                $_SESSION['server_error'] = "O ficheiro não tem o header no formato correto.";
                $this->upload_file_frm(); return;
            }
        } else {
            $_SESSION['server_error'] = "Erro inesperado no carregamento.";
            $this->upload_file_frm(); return;
        }
    }

    // =======================================================
    // HELPERS DE UPLOAD (Privados)
    // =======================================================
    private function has_valid_header($file_path)
    {
        $data = [];
        $file_info = pathinfo($file_path);

        if ($file_info['extension'] == 'csv') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            $reader->setInputEncoding('UTF-8');
            $reader->setDelimiter(';');
            $reader->setEnclosure('');
            $sheet = $reader->load($file_path);
            $data = $sheet->getActiveSheet()->toArray()[0];
        } else if ($file_info['extension'] == 'xlsx') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file_path);
            $data = $spreadsheet->getActiveSheet()->toArray()[0];
        }

        $valid_header = 'name,gender,birthdate,email,phone,interests';
        return implode(',', $data) == $valid_header ? true : false;
    }

    private function load_file_data_to_database($file_path)
    {
        $data = [];
        $file_info = pathinfo($file_path);

        if ($file_info['extension'] == 'csv') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            $reader->setInputEncoding('UTF-8');
            $reader->setDelimiter(';');
            $reader->setEnclosure('');
            $sheet = $reader->load($file_path);
            $data = $sheet->getActiveSheet()->toArray();
        } else if ($file_info['extension'] == 'xlsx') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file_path);
            $data = $spreadsheet->getActiveSheet()->toArray();
        }

        $model = new Agents();
        $report = ['total' => 0, 'total_carregados' => 0, 'total_nao_carregados' => 0];
        array_shift($data); // Remove header

        foreach ($data as $client) {
            $report['total']++;
            $exists = $model->check_if_client_exists(['text_name' => $client[0]]);
            if (!$exists['status']) {
                $post_data = [
                    'text_name' => $client[0],
                    'radio_gender' => $client[1],
                    'text_birthdate' => $client[2],
                    'text_email' => $client[3],
                    'text_phone' => $client[4],
                    'text_interests' => $client[5],
                ];
                $model->add_new_client_to_database($post_data);
                $report['total_carregados']++;
            } else {
                $report['total_nao_carregados']++;
            }
        }

        logger(get_active_user_name() . " - report upload: " . json_encode($report));
        $report['filename'] = $_FILES['clients_file']['name'];
        $_SESSION['report'] = $report;
        $this->upload_file_frm();
    }

    // =======================================================
    // EXPORT XLSX
    // =======================================================
    public function export_clients_xlsx()
    {
        if (!check_session() || $_SESSION['user']->profile != 'agent') {
            header('Location: index.php'); exit;
        }

        $model = new Agents();
        $id_agent = (int)$_SESSION['user']->id;
        $results = $model->get_agent_clients($id_agent);
        
        $data[] = ['name', 'gender', 'birthdate', 'email', 'phone', 'interests', 'created_at', 'updated_at'];

        if (!empty($results['data'])) {
            foreach($results['data'] as $client){
                $client_row = clone $client;
                unset($client_row->id);
                // Remove campos extra se necessário, ex: deleted_at se não quiser exportar
                $data[] = (array)$client_row;
            }
        }

        $filename = 'output_' . time() . '.xlsx';
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'dados');
        $spreadsheet->addSheet($worksheet);
        $spreadsheet->removeSheetByIndex(0);
        
        if(count($data) > 0){
            $worksheet->fromArray($data);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($filename).'"');
        $writer->save('php://output');

        $total_registos = count($data) > 0 ? count($data) - 1 : 0;
        logger(get_active_user_name() . " - download xlsx: " . $filename);
    }
    // =======================================================
    // DESTRUIR CLIENTE (Ação Final)
    // =======================================================
    public function destroy_client()
    {
        if (!check_session() || $_SESSION['user']->profile != 'agent') {
            header('Location: index.php');
            exit;
        }

        // Obtém o ID da URL
        $id_client = aes_decrypt($_GET['id']);
        
        if(!$id_client){
            header('Location: ?ct=agent&mt=my_clients');
            exit;
        }

        $model = new Agents();
        // Chama a função nova que criámos acima
        $model->hard_delete_client($id_client);

        logger(get_active_user_name() . " destruiu permanentemente o cliente ID: $id_client");
        
        header('Location: ?ct=agent&mt=my_clients');
    }
}
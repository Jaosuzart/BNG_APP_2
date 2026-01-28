<?php

namespace bng\Models;

use bng\Models\BaseModel;

class Agents extends BaseModel
{
    // =======================================================
    // LOGIN
    // =======================================================
    public function check_login($username, $password)
    {
        $params = [':username' => $username];
        $this->db_connect();
        $results = $this->query("SELECT id, passwrd FROM agents WHERE AES_ENCRYPT(:username, '" . MYSQL_AES_KEY . "') = name", $params);

        if ($results->affected_rows == 0) {
            return ['status' => false];
        }
        if (!password_verify($password, $results->results[0]->passwrd)) {
            return ['status' => false];
        }
        return ['status' => true];
    }

    // =======================================================
    // DADOS DO UTILIZADOR
    // =======================================================
   public function get_user_data($username)
{
    $params = [':username' => $username];
    $this->db_connect();

    $results = $this->query(
        "SELECT 
            id,
            CONVERT(AES_DECRYPT(name, '" . MYSQL_AES_KEY . "') USING utf8mb4) AS name,
            profile
         FROM agents
         WHERE AES_ENCRYPT(:username, '" . MYSQL_AES_KEY . "') = name",
        $params
    );

    if ($results->affected_rows == 0) {
        return ['status' => 'error', 'data' => null];
    }

    return ['status' => 'success', 'data' => $results->results[0]];
}

    // =======================================================
    // DEFINIR ÚLTIMO LOGIN
    // =======================================================
    public function set_user_last_login($id)
    {
        $params = [':id' => $id];
        $this->db_connect();
        $this->non_query("UPDATE agents SET last_login = NOW() WHERE id = :id", $params);
    }

    // =======================================================
    // OBTER CLIENTES DO AGENTE (CORRIGIDO)
    // =======================================================
    public function get_agent_clients($id_agent)
    {
        $params = [':id_agent' => $id_agent];
        $this->db_connect();

        // 1. Seleciona deleted_at
        // 2. Traz TODOS (não tem WHERE deleted_at IS NULL)

        $results = $this->query(
            "SELECT 
                id, 
                IFNULL(CONVERT(AES_DECRYPT(name, '" . MYSQL_AES_KEY . "') USING utf8mb4), '') name, 
                gender, 
                birthdate, 
                IFNULL(CONVERT(AES_DECRYPT(email, '" . MYSQL_AES_KEY . "') USING utf8mb4), '') email, 
                IFNULL(CONVERT(AES_DECRYPT(phone, '" . MYSQL_AES_KEY . "') USING utf8mb4), '') phone, 
                interests, 
                created_at, 
                updated_at, 
                deleted_at 
             FROM persons 
             WHERE id_agent = :id_agent",
            $params
        );

        return ['status' => 'success', 'data' => $results->results];
    }

    // =======================================================
    // VERIFICAR SE CLIENTE EXISTE
    // =======================================================
    public function check_if_client_exists($data)
    {
        // Verifica pelo nome e pelo agente logado
        // Nota: O seu código original recebia $post_data, aqui mantive compatibilidade
        $client_name = isset($data['text_name']) ? $data['text_name'] : '';

        $params = [
            ':name' => $client_name,
            ':id_agent' => $_SESSION['user']->id
        ];

        $this->db_connect();
        $results = $this->query(
            "SELECT id FROM persons 
             WHERE AES_ENCRYPT(:name, '" . MYSQL_AES_KEY . "') = name 
             AND id_agent = :id_agent",
            $params
        );

        if ($results->affected_rows == 0) {
            return ['status' => false];
        }
        return ['status' => true];
    }

    // =======================================================
    // ADICIONAR NOVO CLIENTE
    // =======================================================
    public function add_new_client_to_database($data)
    {
        try {
            if (empty($data['text_birthdate'])) {
                throw new \Exception("Data vazia");
            }
            $birthdate = new \DateTime($data['text_birthdate']);
        } catch (\Exception $e) {
            $birthdate = new \DateTime('now');
        }
           $data['text_email'] = trim((string)($data['text_email'] ?? ''));
           $data['text_phone'] = trim((string)($data['text_phone'] ?? ''));

        $id_agent = $_SESSION['user']->id;
        $params = [
            ':name' => $data['text_name'],
            ':gender' => $data['radio_gender'],
            ':birthdate' => $birthdate->format('Y-m-d H:i:s'),
            ':email' => $data['text_email'],
            ':phone' => $data['text_phone'],
            ':interests' => $data['text_interests'],
            ':id_agent' => $id_agent
        ];
        $this->db_connect();
        $this->non_query(
            "INSERT INTO persons VALUES(
                0, 
                AES_ENCRYPT(:name, '" . MYSQL_AES_KEY . "'), 
                :gender, 
                :birthdate, 
                AES_ENCRYPT(:email, '" . MYSQL_AES_KEY . "'), 
                AES_ENCRYPT(:phone, '" . MYSQL_AES_KEY . "'), 
                :interests, 
                :id_agent, 
                NOW(), 
                NOW(), 
                NULL
            )",
            $params
        );
    }

    // =======================================================
    // OBTER DADOS DO CLIENTE
    // =======================================================
    public function get_client_data($id_client)
    {
        $params = [':id_client' => $id_client];
        $this->db_connect();
        $results = $this->query(
            "SELECT 
                id, 
                IFNULL(CONVERT(AES_DECRYPT(name, '" . MYSQL_AES_KEY . "') USING utf8mb4), '') name, 
                gender, 
                birthdate, 
                IFNULL(CONVERT(AES_DECRYPT(email, '" . MYSQL_AES_KEY . "') USING utf8mb4), '') email, 
                IFNULL(CONVERT(AES_DECRYPT(phone, '" . MYSQL_AES_KEY . "') USING utf8mb4), '') phone, 
                interests 
             FROM persons WHERE id = :id_client",
            $params
        );

        if ($results->affected_rows == 0) {
            return ['status' => 'error'];
        }
        return ['status' => 'success', 'data' => $results->results[0]];
    }

    // =======================================================
    // CHECKAR OUTRO CLIENTE COM MESMO NOME
    // =======================================================
    public function check_other_client_with_same_name($id_client, $name)
    {
        $params = [
            ':id_client' => $id_client,
            ':name' => $name,
            ':id_agent' => $_SESSION['user']->id
        ];
        $this->db_connect();
        $results = $this->query(
            "SELECT id FROM persons 
             WHERE id <> :id_client 
             AND id_agent = :id_agent
             AND AES_ENCRYPT(:name, '" . MYSQL_AES_KEY . "') = name",
            $params
        );

        if ($results->affected_rows == 0) {
            return ['status' => false];
        }
        return ['status' => true];
    }

    // =======================================================
    // ATUALIZAR CLIENTE
    // =======================================================
    public function update_client_data($id_client, $data)
    {
        try {
            if (empty($data['text_birthdate'])) {
                throw new \Exception("Data vazia");
            }
            $birthdate = new \DateTime($data['text_birthdate']);
        } catch (\Exception) {
            $birthdate = new \DateTime('now');
        }

        $params = [
            ':id_client' => $id_client,
            ':name' => $data['text_name'],
            ':gender' => $data['radio_gender'],
            ':birthdate' => $birthdate->format('Y-m-d H:i:s'),
            ':email' => $data['text_email'],
            ':phone' => $data['text_phone'],
            ':interests' => $data['text_interests']
        ];
        $this->db_connect();
        $this->non_query(
            "UPDATE persons SET 
                name = AES_ENCRYPT(:name, '" . MYSQL_AES_KEY . "'), 
                gender = :gender, 
                birthdate = :birthdate, 
            $params
                email = AES_ENCRYPT(:email, '" . MYSQL_AES_KEY . "'), 
                phone = AES_ENCRYPT(:phone, '" . MYSQL_AES_KEY . "'), 
                interests = :interests, 
                updated_at = NOW() 
             WHERE id = :id_client",
            $params
        );
    }

    // =======================================================
    // ELIMINAR CLIENTE (SOFT DELETE - CORRIGIDO)
    // =======================================================
    public function delete_client($id_client)
    {
        $params = [':id_client' => $id_client];
        $this->db_connect();

        // CORREÇÃO CRÍTICA: 
        // Antes estava: DELETE FROM ... (Isso apagava para sempre!)
        // Agora está: UPDATE ... SET deleted_at = NOW() (Isso permite recuperar)

        $this->non_query("UPDATE persons SET deleted_at = NOW() WHERE id = :id_client", $params);
    }

    // =======================================================
    // RECUPERAR CLIENTE (RESTAURAR)
    // =======================================================
    public function recover_client($id_client)
    {
        $params = [':id_client' => $id_client];
        $this->db_connect();
        $this->non_query("UPDATE persons SET deleted_at = NULL WHERE id = :id_client", $params);
    }

    // =======================================================
    // VERIFICAR PASSWORD ATUAL
    // =======================================================
    public function check_current_password($current_password)
    {
        $id_user = $_SESSION['user']->id;
        $params = [':id_user' => $id_user];
        $this->db_connect();
        $results = $this->query("SELECT passwrd FROM agents WHERE id = :id_user", $params);

        if ($results->affected_rows == 0) {
            return ['status' => false];
        }
        if (!password_verify($current_password, $results->results[0]->passwrd)) {
            return ['status' => false];
        }
        return ['status' => true];
    }

    // =======================================================
    // ATUALIZAR PASSWORD AGENTE
    // =======================================================
    public function update_agent_password($new_password)
    {
        $id_user = $_SESSION['user']->id;
        $params = [
            ':passwrd' => password_hash($new_password, PASSWORD_DEFAULT),
            ':id_user' => $id_user
        ];
        $this->db_connect();
        $this->non_query("UPDATE agents SET passwrd = :passwrd, updated_at = NOW() WHERE id = :id_user", $params);
    }

    // =======================================================
    // ATUALIZAR PASSWORD POR EMAIL (RECUPERAÇÃO)
    // =======================================================
    public function update_password_by_email($email, $new_password)
    {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $params = [
            ':p' => $hash,
            ':e' => $email
        ];
        $this->db_connect();
        $this->non_query(
            "UPDATE agents SET passwrd = :p, updated_at = NOW() 
             WHERE name = AES_ENCRYPT(:e, '" . MYSQL_AES_KEY . "')",
            $params
        );
    }
    // =======================================================
    // ELIMINAR PERMANENTEMENTE (HARD DELETE)
    // =======================================================
    public function hard_delete_client($id_client)
    {
        $params = [':id' => $id_client];
        $this->db_connect();
        // DELETE FROM apaga para sempre!
        $this->non_query("DELETE FROM persons WHERE id = :id", $params);
    }
   
}

<?php

namespace bng\Models;
use bng\Models\BaseModel;

class Agents extends BaseModel
{
    // =======================================================
    // LOGIN E UTILIZADOR
    // =======================================================
    public function check_login($username, $password)
    {
        $params = [':username' => $username];
        $this->db_connect();
        $results = $this->query("SELECT id, password FROM agents WHERE email = :username", $params);

        if ($results->affected_rows == 0) return ['status' => false];
        if (!password_verify($password, $results->results[0]->password)) return ['status' => false];
        return ['status' => true];
    }

    public function get_user_data($username)
    {
        $params = [':username' => $username];
        $this->db_connect();
        $results = $this->query("SELECT id, email AS name, profile FROM agents WHERE email = :username", $params);

        if ($results->affected_rows == 0) return ['status' => 'error', 'data' => null];
        return ['status' => 'success', 'data' => $results->results[0]];
    }

    public function set_user_last_login($id)
    {
        $params = [':id' => $id];
        $this->db_connect();
        $this->non_query("UPDATE agents SET last_login = NOW() WHERE id = :id", $params);
    }

    // =======================================================
    // GESTÃO DE CLIENTES
    // =======================================================
    public function get_agent_clients($id_agent)
    {
        $params = [':id_agent' => $id_agent];
        $this->db_connect();
        
        // CORRIGIDO: Tabela 'clients' e sem AES_DECRYPT
        $results = $this->query(
            "SELECT id, name, gender, birthdate, email, phone, interests, created_at, updated_at, deleted_at 
             FROM clients 
             WHERE id_agent = :id_agent",
            $params
        );
        return ['status' => 'success', 'data' => $results->results];
    }

    public function check_if_client_exists($data)
    {
        $params = [
            ':name' => $data['text_name'] ?? '',
            ':id_agent' => $_SESSION['user']->id
        ];
        $this->db_connect();
        $results = $this->query("SELECT id FROM clients WHERE name = :name AND id_agent = :id_agent", $params);
        return ['status' => ($results->affected_rows > 0)];
    }

    public function add_new_client_to_database($data)
    {
        try {
            if (empty($data['text_birthdate'])) throw new \Exception("Data vazia");
            $birthdate = new \DateTime($data['text_birthdate']);
        } catch (\Exception $e) {
            $birthdate = new \DateTime('now');
        }

        $params = [
            ':name' => $data['text_name'],
            ':gender' => $data['radio_gender'],
            ':birthdate' => $birthdate->format('Y-m-d H:i:s'),
            ':email' => trim((string)($data['text_email'] ?? '')),
            ':phone' => trim((string)($data['text_phone'] ?? '')),
            ':interests' => $data['text_interests'],
            ':id_agent' => $_SESSION['user']->id
        ];
        $this->db_connect();
        
        // CORRIGIDO: Inserção exata com o nome das colunas na tabela 'clients'
        $this->non_query(
            "INSERT INTO clients (name, gender, birthdate, email, phone, interests, id_agent, created_at, updated_at, deleted_at) 
             VALUES (:name, :gender, :birthdate, :email, :phone, :interests, :id_agent, NOW(), NOW(), NULL)",
            $params
        );
    }

    public function get_client_data($id_client)
    {
        $params = [':id_client' => $id_client];
        $this->db_connect();
        $results = $this->query("SELECT id, name, gender, birthdate, email, phone, interests FROM clients WHERE id = :id_client", $params);

        if ($results->affected_rows == 0) return ['status' => 'error'];
        return ['status' => 'success', 'data' => $results->results[0]];
    }

    public function check_other_client_with_same_name($id_client, $name)
    {
        $params = [':id_client' => $id_client, ':name' => $name, ':id_agent' => $_SESSION['user']->id];
        $this->db_connect();
        $results = $this->query("SELECT id FROM clients WHERE id <> :id_client AND id_agent = :id_agent AND name = :name", $params);
        return ['status' => ($results->affected_rows > 0)];
    }

    public function update_client_data($id_client, $data)
    {
        try {
            if (empty($data['text_birthdate'])) throw new \Exception("Data vazia");
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
            "UPDATE clients SET name = :name, gender = :gender, birthdate = :birthdate, email = :email, phone = :phone, interests = :interests, updated_at = NOW() 
             WHERE id = :id_client",
            $params
        );
    }

    public function delete_client($id_client)
    {
        $this->db_connect();
        $this->non_query("UPDATE clients SET deleted_at = NOW() WHERE id = :id_client", [':id_client' => $id_client]);
    }

    public function recover_client($id_client)
    {
        $this->db_connect();
        $this->non_query("UPDATE clients SET deleted_at = NULL WHERE id = :id_client", [':id_client' => $id_client]);
    }

    public function hard_delete_client($id_client)
    {
        $this->db_connect();
        $this->non_query("DELETE FROM clients WHERE id = :id", [':id' => $id_client]);
    }

    // =======================================================
    // GESTÃO DE SENHAS (AGENTE LOGADO)
    // =======================================================
    public function check_current_password($current_password)
    {
        $params = [':id_user' => $_SESSION['user']->id];
        $this->db_connect();
        $results = $this->query("SELECT password FROM agents WHERE id = :id_user", $params);

        if ($results->affected_rows == 0) return ['status' => false];
        if (!password_verify($current_password, $results->results[0]->password)) return ['status' => false];
        return ['status' => true];
    }

    public function update_agent_password($new_password)
    {
        $params = [':password' => password_hash($new_password, PASSWORD_DEFAULT), ':id_user' => $_SESSION['user']->id];
        $this->db_connect();
        $this->non_query("UPDATE agents SET password = :password, updated_at = NOW() WHERE id = :id_user", $params);
    }

    public function update_password_by_email($email, $new_password)
    {
        $params = [':p' => password_hash($new_password, PASSWORD_DEFAULT), ':e' => $email];
        $this->db_connect();
        $this->non_query("UPDATE agents SET password = :p, updated_at = NOW() WHERE email = :e", $params);
    }
}
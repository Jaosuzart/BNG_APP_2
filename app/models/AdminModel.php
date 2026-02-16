<?php

namespace bng\Models;
use bng\Models\BaseModel;

class AdminModel extends BaseModel
{
    // =======================================================
    // OBTER TODOS OS CLIENTES (GLOBAL)
    // =======================================================
    public function get_all_clients()
    {
        $this->db_connect();
        // CORRIGIDO: Tabela 'clients' e sem AES_DECRYPT
        $results = $this->query(
            "SELECT 
                p.id,
                p.name,
                p.gender,
                p.birthdate,
                p.email,
                p.phone,
                p.interests,
                p.created_at,
                a.email AS agent 
             FROM clients p
             LEFT JOIN agents a ON p.id_agent = a.id
             WHERE p.deleted_at IS NULL
             ORDER BY p.created_at DESC"
        );
        return $results;
    }

    // =======================================================
    // ESTATÍSTICAS: AGENTES E SEUS CLIENTES
    // =======================================================
    public function get_agents_clients_stats()
    {
        $this->db_connect();
        // CORRIGIDO: Tabela 'clients'
        $sql = "SELECT 
                COALESCE(a.email, CONCAT('Agente #', a.id)) AS agente,
                COUNT(p.id) AS total_clientes
            FROM agents a
            LEFT JOIN clients p 
                ON a.id = p.id_agent AND p.deleted_at IS NULL
            WHERE a.deleted_at IS NULL
            GROUP BY a.id";

        $results = $this->query($sql);
        return $results->results;
    }

    // =======================================================
    // ESTATÍSTICAS GLOBAIS
    // =======================================================
    public function get_global_stats()
    {
        $this->db_connect();
        
        // Função segura para evitar erros "Trying to access array offset on null"
        $get_val = function($sql, $default = 0) {
            $res = $this->query($sql);
            if (!empty($res->results) && isset($res->results[0]->value) && $res->results[0]->value !== null) {
                return (object)['value' => $res->results[0]->value];
            }
            return (object)['value' => $default];
        };

        // CORRIGIDO: Buscas na tabela 'clients'
        $results['total_agents'] = $get_val("SELECT COUNT(*) as value FROM agents WHERE deleted_at IS NULL");
        $results['total_clients'] = $get_val("SELECT COUNT(*) as value FROM clients WHERE deleted_at IS NULL");
        $results['total_deleted_clients'] = $get_val("SELECT COUNT(*) as value FROM clients WHERE deleted_at IS NOT NULL");
        
        $total_agents = $results['total_agents']->value;
        $total_clients = $results['total_clients']->value;
        $average = $total_agents > 0 ? $total_clients / $total_agents : 0;
        $results['average_clients_per_agent'] = (object)['value' => number_format($average, 2)];

        $results['younger_client'] = $get_val("SELECT TIMESTAMPDIFF(YEAR, MAX(birthdate), NOW()) as value FROM clients WHERE deleted_at IS NULL", '-');
        $results['oldest_client']  = $get_val("SELECT TIMESTAMPDIFF(YEAR, MIN(birthdate), NOW()) as value FROM clients WHERE deleted_at IS NULL", '-');

        // Evita divisão por zero
        $results['percentage_males'] = $get_val("SELECT ROUND((COUNT(CASE WHEN gender = 'm' THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 2) AS value FROM clients WHERE deleted_at IS NULL");
        $results['percentage_females'] = $get_val("SELECT ROUND((COUNT(CASE WHEN gender = 'f' THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 2) AS value FROM clients WHERE deleted_at IS NULL");

        return $results;
    }

    // =======================================================
    // OBTER AGENTES PARA GESTÃO
    // =======================================================
    public function get_agents_for_management()
    {
        $this->db_connect();
        return $this->query("SELECT id, email as name, profile, last_login, created_at, updated_at, deleted_at FROM agents");
    }

    public function get_agent_by_id($id)
    {
        $params = [':id' => $id];
        $this->db_connect();
        return $this->query("SELECT id, email as name, profile, created_at, updated_at FROM agents WHERE id = :id", $params);
    }

    public function check_if_agent_exists($email)
    {
        $params = [':email' => $email];
        $this->db_connect();
        $results = $this->query("SELECT id FROM agents WHERE email = :email", $params);
        return ['status' => ($results->affected_rows > 0)];
    }

    public function add_new_agent($name, $profile, $password)
    {
        $params = [
            ':email'    => $name, 
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':profile'  => $profile
        ];
        $this->db_connect();
        $this->non_query("INSERT INTO agents (email, password, profile, created_at) VALUES (:email, :password, :profile, NOW())", $params);
        return true;
    }

    public function update_agent($id, $email, $profile, $new_password = null)
    {
        $params = [':id' => $id, ':email' => $email, ':profile' => $profile];
        $sql = "UPDATE agents SET email = :email, profile = :profile";

        if (!empty($new_password)) {
            $params[':password'] = password_hash($new_password, PASSWORD_DEFAULT);
            $sql .= ", password = :password";
        }
        $sql .= " WHERE id = :id";
        
        $this->db_connect();
        return $this->non_query($sql, $params);
    }

    public function delete_agent($id)
    {
        $this->db_connect();
        $this->non_query("UPDATE agents SET deleted_at = NOW() WHERE id = :id", [':id' => $id]);
        return true;
    }

    public function recover_agent($id)
    {
        $this->db_connect();
        $this->non_query("UPDATE agents SET deleted_at = NULL WHERE id = :id", [':id' => $id]);
    }
}
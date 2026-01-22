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
        $results = $this->query(
            "SELECT 
                p.id,
                AES_DECRYPT(p.name, '" . MYSQL_AES_KEY . "') AS name,
                p.gender,
                p.birthdate,
                AES_DECRYPT(p.email, '" . MYSQL_AES_KEY . "') AS email,
                AES_DECRYPT(p.phone, '" . MYSQL_AES_KEY . "') AS phone,
                p.interests,
                p.created_at,
                AES_DECRYPT(a.name, '" . MYSQL_AES_KEY . "') AS agent
             FROM persons p
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
        $results = $this->query(
            "SELECT 
                AES_DECRYPT(a.name, '" . MYSQL_AES_KEY . "') AS agente,
                COUNT(p.id) AS total_clientes
             FROM agents a
             LEFT JOIN persons p ON a.id = p.id_agent AND p.deleted_at IS NULL
             WHERE a.deleted_at IS NULL
             GROUP BY a.id"
        );
        return $results->results;
    }

    // =======================================================
    // ESTATÍSTICAS GLOBAIS
    // =======================================================
   // =======================================================
    // ESTATÍSTICAS GLOBAIS
    // =======================================================
    public function get_global_stats()
    {
        $this->db_connect();
        
        // 1. Totais Básicos
        $results['total_agents'] = $this->query("SELECT COUNT(*) as value FROM agents WHERE deleted_at IS NULL")->results[0];
        $results['total_clients'] = $this->query("SELECT COUNT(*) as value FROM persons WHERE deleted_at IS NULL")->results[0];
        $results['total_deleted_clients'] = $this->query("SELECT COUNT(*) as value FROM persons WHERE deleted_at IS NOT NULL")->results[0];
        
        // 2. Média
        $total_agents = $results['total_agents']->value;
        $total_clients = $results['total_clients']->value;
        $average = $total_agents > 0 ? $total_clients / $total_agents : 0;
        $results['average_clients_per_agent'] = (object)['value' => number_format($average, 2)];

        // --- CÓDIGO QUE FALTAVA ---

        // 3. Idades (Mais novo e Mais velho)
        // TIMESTAMPDIFF calcula a diferença em ANOS entre a data de nascimento e HOJE (NOW)
        $results['younger_client'] = $this->query("SELECT TIMESTAMPDIFF(YEAR, MAX(birthdate), NOW()) as value FROM persons WHERE deleted_at IS NULL")->results[0];
        $results['oldest_client']  = $this->query("SELECT TIMESTAMPDIFF(YEAR, MIN(birthdate), NOW()) as value FROM persons WHERE deleted_at IS NULL")->results[0];

        // 4. Percentagens (Homens e Mulheres)
        // Conta quantos 'm' ou 'f' existem, divide pelo total e multiplica por 100
        $results['percentage_males'] = $this->query(
            "SELECT ROUND((COUNT(CASE WHEN gender = 'm' THEN 1 END) / COUNT(*)) * 100, 2) AS value 
             FROM persons WHERE deleted_at IS NULL"
        )->results[0];

        $results['percentage_females'] = $this->query(
            "SELECT ROUND((COUNT(CASE WHEN gender = 'f' THEN 1 END) / COUNT(*)) * 100, 2) AS value 
             FROM persons WHERE deleted_at IS NULL"
        )->results[0];

        return $results;
    }
    // =======================================================
    // OBTER AGENTES PARA A TELA DE GESTÃO (CORRIGIDO)
    // =======================================================
    // =======================================================
    // OBTER AGENTES PARA GESTÃO (Corrigido para mostrar eliminados)
    // =======================================================
    public function get_agents_for_management()
    {
        $this->db_connect();
        
        // CORREÇÃO:
        // 1. Adicionado 'deleted_at' na lista de campos.
        // 2. NÃO TEM 'WHERE deleted_at IS NULL'. Assim traz todos (ativos e eliminados).
        
        $sql = "SELECT 
                    id, 
                    AES_DECRYPT(name, '" . MYSQL_AES_KEY . "') as name, 
                    profile, 
                    last_login, 
                    created_at, 
                    updated_at, 
                    deleted_at 
                FROM agents"; 
        
        return $this->query($sql);
    }

    // =======================================================
    // OBTER UM AGENTE PELO ID
    // =======================================================
    public function get_agent_by_id($id)
    {
        $params = [':id' => $id];
        $this->db_connect();
        $results = $this->query(
            "SELECT 
                id, 
                AES_DECRYPT(name, '" . MYSQL_AES_KEY . "') as name, 
                profile, 
                created_at, 
                updated_at 
             FROM agents 
             WHERE id = :id", 
            $params
        );
        return $results;
    }

    // =======================================================
    // VERIFICAR SE AGENTE EXISTE (email)
    // =======================================================
    public function check_if_agent_exists($email)
    {
        $params = [':name' => $email];
        $this->db_connect();
        $results = $this->query(
            "SELECT id FROM agents WHERE name = AES_ENCRYPT(:name, '" . MYSQL_AES_KEY . "')", 
            $params
        );

        if ($results->affected_rows == 0) {
            return ['status' => false];
        }
        return ['status' => true];
    }

    // =======================================================
    // ADICIONAR NOVO AGENTE
    // =======================================================
    public function add_new_agent($name, $profile, $password)
    {
        $params = [
            ':name'     => $name,
            ':passwrd'  => password_hash($password, PASSWORD_DEFAULT),
            ':profile'  => $profile
        ];

        $this->db_connect();
        $this->non_query(
            "INSERT INTO agents (name, passwrd, profile, created_at) 
             VALUES (
                AES_ENCRYPT(:name, '" . MYSQL_AES_KEY . "'), 
                :passwrd, 
                :profile, 
                NOW()
             )", 
            $params
        );
        return true;
    }

    // =======================================================
    // ATUALIZAR AGENTE
    // =======================================================
    public function update_agent($id, $email, $profile, $new_password = null)
    {
        $params = [
            ':id'       => $id,
            ':name'     => $email,
            ':profile'  => $profile
        ];

        $sql = "UPDATE agents SET 
                    name = AES_ENCRYPT(:name, '" . MYSQL_AES_KEY . "'),
                    profile = :profile";

        if (!empty($new_password)) {
            $params[':passwrd'] = password_hash($new_password, PASSWORD_DEFAULT);
            $sql .= ", passwrd = :passwrd";
        }

        $sql .= " WHERE id = :id";

        $this->db_connect();
        return $this->non_query($sql, $params);
    }

    // =======================================================
    // ELIMINAR AGENTE (SOFT DELETE)
    // =======================================================
    public function delete_agent($id)
    {
        $params = [
            ':id' => $id
        ];
        $this->db_connect();
        
        // Define a data de eliminação (não apaga o registo)
        $this->non_query("UPDATE agents SET deleted_at = NOW() WHERE id = :id", $params);
        
        return true;
    }

    // =======================================================
    // RECUPERAR AGENTE (RESTAURAR)
    // =======================================================
    public function recover_agent($id)
    {
        $params = [
            ':id' => $id
        ];
        $this->db_connect();
        
        // Limpa a data de eliminação
        $this->non_query("UPDATE agents SET deleted_at = NULL WHERE id = :id", $params);
    }
}
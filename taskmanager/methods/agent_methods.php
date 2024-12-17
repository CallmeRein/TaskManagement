<?php
    require_once __DIR__ . "/employee_methods.php";
    require __DIR__ . "/../database/database_connection.php";

    class Agent_methods extends Employee_method {
        private $conn;
        public function __construct($conn) {
            $this->conn = $conn;
        }

//LOGIN METHODS
        // Agent login method
        public function login($username, $password) {
            $query = "SELECT * FROM agents WHERE agent_username = ? AND agent_password = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
        // Check if the username exists in the agents table
        public function userExist($username) {
            $query = "SELECT * FROM agents WHERE agent_username = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->num_rows > 0;
        }

//LOGOUT METHOD
        // Agent logout method
        public function logout() {
            session_unset();
            session_destroy();
        }

//DATA RETRIEVAL METHODS
        //retrieve agent details
        public function get_Agent_Detail($username) {
            $query = "SELECT agent_id, agent_name FROM agents WHERE agent_username = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }
        // Retrieve tasks assigned to a specific agent
        public function get_Tasks_By_AgentId($agent_id) {
            $query = "SELECT * FROM tasks WHERE agent_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $agent_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        // Retrieve client name for the tasks table
        public function get_Client_Name($client_id) {
            $query = "SELECT client_name FROM clients WHERE client_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $client_id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Return the client name if found
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['client_name'];
            } else {
                return null; // Return null if no client found
            }
        }

//UPDATE TASK
        // Update the status of a task assigned to the agent
        public function update_Task($ticket_number, $status, $agent_id) {
            $query = "UPDATE tasks SET status = ? WHERE ticket_number = ? AND agent_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sss", $status, $ticket_number, $agent_id);
            return $stmt->execute();
        }
    }
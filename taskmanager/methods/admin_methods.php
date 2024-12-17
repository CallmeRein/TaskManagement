<?php
    require_once __DIR__ . "/employee_methods.php";
    require __DIR__ . "/../database/database_connection.php";
    
    class Admin_methods extends Employee_method {
        private $conn;
        public function __construct($conn) {
            $this->conn = $conn;
        }

//LOGIN AND LOGOUT METHODS
       // Admin login method with hardcoded username and password
       public function login($username, $password) {
            if ($username === 'admin' && $password === 'admin') {
                return true;
            }
            return false;
        }
        // Agent logout method
        public function logout() {
            session_unset();
            session_destroy();
        }

//DATA RETRIEVAL METHODS
        // Fetch all agents from the database
        public function get_All_Agents() {
            $query = "SELECT agent_id, agent_name,agent_email, agent_contactnumber FROM agents";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                return false;
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                return false;
            }
            $agents = [];
            while ($row = $result->fetch_assoc()) {
                $agents[] = $row;
            }
            return $agents;
        }
        // Method to check if an agent_id already exists
        public function check_Agent_Exists($agent_id) {
            $query = "SELECT COUNT(*) FROM agents WHERE agent_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $agent_id);
            $stmt->execute();
            $count = 0;
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            
            return $count > 0;
        }
        // Add a new client to the clients table
        public function add_New_Client($client_name, $client_email, $client_contactnumber) {
            $query = "INSERT INTO clients (client_name, client_email, client_contactnumber) 
                    VALUES (?, ?, ?)";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sss", $client_name, $client_email, $client_contactnumber);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        }
        // Retrieve all clients from the database
        public function get_All_Clients() {
            $query = "SELECT client_id, client_name, client_email, client_contactnumber FROM clients";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                return false;
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                return false;
            }
            $clients = [];
            while ($row = $result->fetch_assoc()) {
                $clients[] = $row;
            }
            return $clients;
        }
        // Get client_id based on client name
        public function get_Client_Id_By_Name($client_name) {
            $query = "SELECT client_id FROM clients WHERE client_name = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $client_name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row['client_id'];
            }
            return null; // If client not found
        }

        // Get agent_id based on agent name
        public function get_Agent_Id_By_Name($agent_name) {
            $query = "SELECT agent_id FROM agents WHERE agent_name = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $agent_name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row['agent_id'];
            }
            return null; // If agent not found
        }
        // Fetch all tasks
        public function get_All_Tasks() {
            $query = "SELECT tasks.*, clients.client_name, agents.agent_name FROM tasks 
                    JOIN clients ON tasks.client_id = clients.client_id 
                    JOIN agents ON tasks.agent_id = agents.agent_id";
            return $this->conn->query($query);
        }
        // Fetch tasks by date range
        public function get_Tasks_By_Date($start_date, $end_date) {
            $query = "SELECT tasks.*, clients.client_name, agents.agent_name FROM tasks 
                    JOIN clients ON tasks.client_id = clients.client_id 
                    JOIN agents ON tasks.agent_id = agents.agent_id 
                    WHERE tasks.start_date >= ? AND tasks.end_date <= ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ss", $start_date, $end_date);
            $stmt->execute();
            return $stmt->get_result();
        }
        // Fetch tasks by status
        public function get_Tasks_By_Status($status) {
            $query = "SELECT tasks.*, clients.client_name, agents.agent_name FROM tasks 
                    JOIN clients ON tasks.client_id = clients.client_id 
                    JOIN agents ON tasks.agent_id = agents.agent_id 
                    WHERE tasks.status = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $status);
            $stmt->execute();
            return $stmt->get_result();
        }

//DATA INPUTTING METHODS
        // Add a new agent to the agents table
        public function add_New_Agent($agent_id, $agent_name, $agent_username, $agent_password, $agent_email, $agent_contactnumber) {
            $query = "INSERT INTO agents (agent_id, agent_name, agent_username, agent_password, agent_email, agent_contactnumber) 
                    VALUES (?,?,?,?,?,?)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('ssssss',$agent_id, $agent_name, $agent_username, $agent_password, $agent_email, $agent_contactnumber);
            if ($stmt->execute()) {
                return true;
            }
            return false;
        }
        // Assign task to agent
        public function assign_Task_To_Agent($client_id, $concern, $severity, $agent_id, $status, $start_date, $end_date) {
            $query = "INSERT INTO tasks (client_id, client_concern, severity, agent_id, start_date, end_date, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("issssss", $client_id, $concern, $severity, $agent_id, $start_date, $end_date, $status);
            return $stmt->execute();
        }



//DATA UPDATING METHODS
        // Method to remove a client by client_id
        public function remove_Client($client_id) {
            $query = "DELETE FROM clients WHERE client_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $client_id);
            if ($stmt->execute()) {
                return true;
            }
            return false;
        }
    }
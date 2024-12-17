<?php
session_start();

require __DIR__ . "/database/database_connection.php";
require __DIR__ . "/methods/admin_methods.php";

$admin = new Admin_methods($conn);

if (isset($_POST["logout"])) {
    header("Location: admin_dashboard.php");
    exit;
}

// Handle new agent addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_agent'])) {
    $agent_id = $_POST['agent_id'];
    $agent_name = $_POST['agent_name'];
    $agent_username = $_POST['agent_username'];
    $agent_password = $_POST['agent_password'];
    $agent_email = $_POST['agent_email'];
    $agent_contactnumber = $_POST['agent_contactnumber'];

    // Add new agent to the database
    if(!$admin->check_Agent_Exists($agent_id)){
        if ($admin->add_New_Agent($agent_id, $agent_name, $agent_username, $agent_password, $agent_email, $agent_contactnumber)) {
            $message = "New agent added successfully!";
            header("refresh: 1;");
        } else {
            $message = "Failed to add new agent.";
            header("refresh: 1;");
        }
    }else{
        $message = "Agent ID already exist.";
        header("refresh: 1;");
    }
}

// Retrieve all agents
$agents = $admin->get_All_Agents();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/agents.css">
    <title>Admin - Agents Dashboard</title>
</head>
<body>
    <h1>Admin - Agents</h1>
    <form method="post">
        <button type="submit" name='logout' class="logout-btn">Back</button>
    </form>
    <!-- Add New Agent Section -->
    <div class="dashboard-container">
        <div class="left-section">
            <center>
                <h2>Add New Agent</h2>
                <form method="POST">
                    <input type="text" name="agent_id" placeholder="Agent ID" required>
                    <input type="text" name="agent_name" placeholder="Name" required>
                    <input type="text" name="agent_username" placeholder="Username" required>
                    <input type="password" name="agent_password" placeholder="Password" required>
                    <input type="email" name="agent_email" placeholder="Email" required>
                    <input type="text" name="agent_contactnumber" placeholder="Contact Number" required> <br>
                    <button type="submit" name="add_agent">Add Agent</button>
                </form>
                <?php if (isset($message)): ?>
                    <p><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>
            </center>
        </div>

        <!-- Agent List Section -->
        <div class="right-section">
            <h2>All Agents</h2>
            <table>
                <tr>
                    <th>Agent ID</th>
                    <th>Agent Name</th>
                    <th>Agent Email</th>
                    <th>Contact Number</th>
                </tr>
                <?php 
                    if(!empty($agents)){
                        foreach ($agents as $ag){
                            echo "<tr>";
                            echo    "<td>" . htmlspecialchars($ag['agent_id']) . "</td>";
                            echo    "<td>" . htmlspecialchars($ag['agent_name']) . "</td>";
                            echo    "<td>" . htmlspecialchars($ag['agent_email']) . "</td>";
                            echo    "<td>" . htmlspecialchars($ag['agent_contactnumber']) . "</td>";
                            echo "</tr>";
                        }
                    }else{
                        echo "<tr><td colspan='6'>No agents.</td></tr>";
                    }
                ?>
            </table>
        </div>
    </div>
</body>
</html>

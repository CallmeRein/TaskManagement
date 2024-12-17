<?php
session_start();

require __DIR__ . "/database/database_connection.php";
require __DIR__ . "/methods/admin_methods.php";

$admin = new Admin_methods($conn);

if (isset($_POST["logout"])) {
    header("Location: admin_dashboard.php");
    exit;
}

// Handle task assignment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_task'])) {
    $client_name = $_POST['client_name'];
    $concern = $_POST['concern']; // Capture concern from the form
    $severity = $_POST['severity'];
    $agent_name = $_POST['agent_name'];

    // Get client ID based on the selected client name
    $client_id = $admin->get_Client_Id_By_Name($client_name);
    
    // Get agent ID based on the selected agent name
    $agent_id = $admin->get_Agent_Id_By_Name($agent_name);

    // Set task status to 'pending'
    $status = 'pending';

    // Get the current date for the task assignment
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Add new task assignment to the database
    if ($admin->assign_Task_To_Agent($client_id, $concern, $severity, $agent_id, $status, $start_date, $end_date)) {
        $message = "Task assigned successfully!";
        header("refresh:1");
    } else {
        $message = "Failed to assign task.";
        header("refresh: 1");
    }
}

// Retrieve all clients and agents for dropdowns
$clients = $admin->get_All_Clients();
$agents = $admin->get_All_Agents();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Assign Task</title>
    <link rel="stylesheet" href="css/tasks.css">
</head>
<body>
    <h1>Admin - Assign Task</h1>
    <?php if (isset($message)): ?>
        <div class="message" style="position: absolute; top: 5%; left: 50%; transform: translateX(-50%); z-index: 1000; background-color: #22223b;"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="post">
        <button type="submit" name='logout' class="logout-btn">Back</button>
    </form>

    <div class="form-container">
        <h2>Assign Task</h2>
        <form method="POST">
            <label for="client_id">Client Name:</label>
            <select id="client_id" name="client_name" required>
                <option value="">Select Client</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?php echo htmlspecialchars($client['client_name']); ?>"><?php echo htmlspecialchars($client['client_name']); ?></option>
                <?php endforeach; ?>
            </select><br>

            <!-- New concern input field -->
            <label for="concern">Client Concern:</label>
            <textarea id="concern" name="concern" required placeholder="Describe the concern" rows="4" cols="50"></textarea><br>

            <label for="severity">Severity:</label>
            <select id="severity" name="severity" required>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select><br>

            <label for="agent_id">Assign Agent:</label>
            <select id="agent_id" name="agent_name" required>
                <option value="">Select Agent</option>
                <?php foreach ($agents as $agent): ?>
                    <option value="<?php echo htmlspecialchars($agent['agent_name']); ?>"><?php echo htmlspecialchars($agent['agent_name']); ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required min="<?php echo date('Y-m-d'); ?>"><br>

            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required min="<?php echo date('Y-m-d'); ?>"><br>

            <button type="submit" name="assign_task">Assign Task</button>
            <button type="reset">Cancel</button>
        </form>
    </div>
</body>
</html>

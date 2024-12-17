<?php 
session_start();

require __DIR__ . "/methods/agent_methods.php";
require __DIR__ . "/database/database_connection.php";

$agent = new Agent_methods($conn);

//check whether username is already in SESSION
if (!isset($_SESSION['agent_username'])) {
    header("Location: index.php");
    exit;
}
$username = $_SESSION['agent_username'];

//get agent details
$agentDetails = $agent->get_Agent_Detail($username);

//get tasks
$agent_id = $agentDetails['agent_id'];
$tasks = $agent->get_Tasks_By_AgentId($agent_id);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ticket_number'])) {
    $ticket_number = $_POST['ticket_number'];
    $status = $_POST['status'];

    // Call update_Task method
    if ($agent->update_Task($ticket_number, $status, $agent_id)) {
        $tasks = $agent->get_Tasks_By_AgentId($agent_id);
    } else {
        $error = "Failed to update task status.";
    }
}

if (isset($_POST["logout"])) {
    header("Location: index.php");
    $agent->logout();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/agent_dashboard.css">
    <title>Agent Dashboard</title>
</head>
<body>
    <form method="POST" class="logout-btn">
        <button type="submit" name="logout">Logout</button>
    </form>
    <h1>Welcome, <?php echo htmlspecialchars($agentDetails['agent_name']); ?>!</h1>
    <div class="table-container">
        <h2>Tasks Assigned to You</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <table>
            <tr>
                <th>Ticket Number</th>
                <th>Client Name</th>
                <th>Concern</th>
                <th>Severity</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($tasks as $row): 
                $client_id = $row['client_id'];
                $client_name = $agent->get_Client_Name($client_id); // Fetch client name for each task
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['ticket_number']); ?></td>
                <td><?php echo htmlspecialchars($client_name); ?></td> <!-- Display client name -->
                <td><?php echo htmlspecialchars($row['client_concern']); ?></td>
                <td><?php echo htmlspecialchars($row['severity']); ?></td>
                <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="ticket_number" value="<?php echo htmlspecialchars($row['ticket_number']); ?>">
                        <select name="status">
                            <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="in progress" <?php if ($row['status'] == 'in progress') echo 'selected'; ?>>In Progress</option>
                            <option value="resolved" <?php if ($row['status'] == 'resolved') echo 'selected'; ?>>Resolved</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>

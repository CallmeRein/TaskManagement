<?php
session_start();

require __DIR__ . "/database/database_connection.php";
require __DIR__ . "/methods/admin_methods.php";

$admin = new Admin_methods($conn);

if (isset($_POST["logout"])) {
    header("Location: admin_dashboard.php");
    exit;
}

// Handle new client addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_client'])) {
    $client_name = $_POST['client_name'];
    $client_email = $_POST['client_email'];
    $client_contactnumber = $_POST['client_contactnumber'];

    // Add new client to the database
    if ($admin->add_New_Client($client_name, $client_email, $client_contactnumber)) {
        $message = "New client added successfully!";
        header("refresh: 1");
    } else {
        $message = "Failed to add new client.";
        header("refresh: 1");
    }
}

// Handle client removal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_client'])) {
    $client_id = $_POST['client_id'];

    if ($admin->remove_Client($client_id)) {
        $message = "Client removed successfully!";
        header("refresh: 1");
    } else {
        $message = "Failed to remove client.";
        header("refresh: 1");
    }
}

// Retrieve all clients
$clients = $admin->get_All_Clients();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/clients.css">
    <title>Admin - Clients Dashboard</title>
</head>
<body>
    <h1>Admin - Clients</h1>
    <form method="post">
        <button type="submit" name='logout' class="logout-btn">Back</button>
    </form>
    <!-- Add New Client Section -->
    <div class="dashboard-container">
        <div class="left-section">
            <center>
                <h2>Add New Client</h2>
                <form method="POST">
                    <input type="text" name="client_name" placeholder="Client Name" required>
                    <input type="email" name="client_email" placeholder="Client Email" required>
                    <input type="text" name="client_contactnumber" placeholder="Client Contact Number" required> <br>
                    <button type="submit" name="add_client">Add Client</button>
                </form>
                <!-- Success/Error Message Section -->
                <?php if (isset($message)): ?>
                    <div class="message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
            </center>
        </div>

        <!-- Client List Section -->
        <div class="right-section">
            <h2>All Clients</h2>
            <table>
                <tr>
                    <th>Client ID</th>
                    <th>Client Name</th>
                    <th>Client Email</th>
                    <th>Contact Number</th>
                    <th>Action</th>
                </tr>
                <?php if ($clients && count($clients) > 0): ?>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($client['client_id']); ?></td>
                            <td><?php echo htmlspecialchars($client['client_name']); ?></td>
                            <td><?php echo htmlspecialchars($client['client_email']); ?></td>
                            <td><?php echo htmlspecialchars($client['client_contactnumber']); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="client_id" value="<?php echo $client['client_id']; ?>">
                                    <button type="submit" name="remove_client" class="remove-btn">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No clients found.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>

<?php
session_start();

require __DIR__ . "/database/database_connection.php";
require __DIR__ . "/methods/admin_methods.php";

$admin = new Admin_methods($conn);

// Default query to fetch all tasks
$tasks_result = $admin->get_All_Tasks();

if (isset($_POST['date_search'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $tasks_result = $admin->get_Tasks_By_Date($start_date, $end_date);
}

if (isset($_POST['status_search'])) {
    $status = $_POST['status'];
    $tasks_result = $admin->get_Tasks_By_Status($status);
}

if (isset($_POST['logout'])) {
    header('Location: admin_dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="css/reports.css">
</head>
<body>
    <form method="post">
        <button type="submit" name='logout' class="logout-btn">Back</button>
    </form>
    <h2>Search Reports</h2>
    <div class="search-container">
        

        <!-- Date Range Search -->
        <form method="POST" class="date-search-form">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
            <button type="submit" name="date_search">Search</button>
        </form>

        <!-- Status Search -->
        <form method="POST" class="status-search-form">
            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="pending">Pending</option>
                <option value="in progress">In Progress</option>
                <option value="resolved">Resolved</option>
            </select>
            <button type="submit" name="status_search">Search</button>
        </form>
    </div>
    
    <div class="table-container">
        <h2>Tasks Reports</h2>
        <table>
            <tr>
                <th>Ticket Number</th>
                <th>Client Name</th>
                <th>Concern</th>
                <th>Severity</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Assigned Agent</th>
                <th>Status</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($tasks_result)) { ?>
            <tr>
                <td><?php echo $row['ticket_number']; ?></td>
                <td><?php echo $row['client_name']; ?></td>
                <td><?php echo $row['client_concern']; ?></td>
                <td><?php echo $row['severity']; ?></td>
                <td><?php echo $row['start_date']; ?></td>
                <td><?php echo $row['end_date']; ?></td>
                <td><?php echo $row['agent_name']; ?></td>
                <td><?php echo $row['status']; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Button to Print PDF -->
    <form class='button' action="printpdf.php" method="post" target="_blank">
        <button type="submit">Print PDF</button>
    </form>

</body>
</html>

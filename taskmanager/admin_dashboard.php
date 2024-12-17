<?php
session_start();

require __DIR__ . "/database/database_connection.php";
require __DIR__ . "/methods/admin_methods.php";

$admin = new Admin_methods($conn);

if (isset($_POST["logout"])) {
    header("Location: index.php");
    $admin->logout();
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <nav>
        <a href="clients.php" class="btns">Clients</a>
        <a href="agents.php" class="btns">Agents</a>
        <a href="tasks.php" class="btns">Tasks</a>
        <a href="reports.php" class="btns">Reports</a>
        <form method="post">
            <button type="submit" name='logout' class="btns">Logout</button>
        </form>
    </nav>
    <h1>Welcome to Admin Dashboard</h1>
</body>
</html>

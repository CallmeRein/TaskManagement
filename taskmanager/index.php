<?php 
    session_start();

    require __DIR__ . "/database/database_connection.php";
    require __DIR__ . "/methods/agent_methods.php";
    require __DIR__ . "/methods/admin_methods.php";

    $agent = new Agent_methods($conn);
    $admin = new Admin_methods($conn);

    $loginError = '';

    // Handle form submission for login
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
       $username = $_POST['username'];
       $password = $_POST['password'];

       if($username == 'admin'){
            if($admin->login($username, $password)){
                header('Location: admin_dashboard.php');
                exit; 
            }
            else{
                $loginError = "Admin password incorrect";
                header("refresh: 1;");
            }
       }
       else{
            if($agent->userExist($username)){
                if($agent->login($username, $password)){
                    $_SESSION['agent_username'] = $username;
                    header("Location: agent_dashboard.php");
                    exit;
                }
            }
            else{
                $loginError = "Username does not exist";
                header("refresh: 1;");
            }
       }

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="post">
            <?php if ($loginError): ?>
                <div class="error"><?php echo $loginError; ?></div>
            <?php endif; ?>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>

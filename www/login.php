<?php

session_start();

// Check if user is already logged in
if(isset($_SESSION['user_id'])){
    header("Location: profile.php?user_id=" . $_SESSION['user_id']);
    exit();
}

$pdo = require_once 'connect.php';

// Check if user has submitted a form
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Retrieve form data
    $input_email = filter_input(INPUT_POST, 'email');
    $input_password = filter_input(INPUT_POST, 'password');

    $config = get_config();
    $admin_login = $config['admin_login'];

    if(!$input_email || !$input_password) {
        $error = "Please enter email and password";
    } else if ($input_email === $admin_login) {
        $admin_password = $config['admin_password'];

	if ($input_password === $admin_password) {
	    $_SESSION['user_id'] = 0;
	    $_SESSION['login'] = $admin_login;
	    $_SESSION['is_admin'] = true;

            // Redirect to admin page
            header('Location: admin.php');
            exit();
	} else {
            $error = "Invalid email or password";
	}
    } else {
        // Check if user exists in the database
        $sql = "SELECT id, isAdministrator, password FROM `User` WHERE `emailAddress` = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $input_email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($input_password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login'] = $input_email;
            $_SESSION['is_admin'] = (bool)$user['isAdministrator'];

            // Redirect to profile page
            header('Location: profile.php');
            exit();
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if(isset($error)) { ?>
        <p><?php echo $error; ?></p>
    <?php } ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label>Email:</label><br>
        <input type="text" name="email" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>

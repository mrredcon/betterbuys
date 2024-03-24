<?php

session_start();

// Check if user is already logged in
if(isset($_SESSION['user_id'])){
    header("Location: profile.php?user_id=" . $_SESSION['user_id']);
    exit();
}

require 'connect.php';

// Check if user has submitted a form
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    if(empty($email) || empty($password)){
        $error = "Please enter email and password";
    } else {
        // Check if user exists in the database
        $sql = "SELECT id, isAdministrator FROM `User` WHERE `emailAddress` = :email AND `password` = :password";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user){
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = $user['isAdministrator'];

            // Redirect to profile page
            header("Location: profile.php?user_id=" . $user['id']);
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
        <input type="email" name="email" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>

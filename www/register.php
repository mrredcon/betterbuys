<?php

session_start();
require 'connect.php';

// Check if user has submitted a form
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if user's email is already in use
    $sql = "SELECT COUNT(*) AS count FROM `User` WHERE `emailAddress` = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        echo "Email address is already in use";
        exit();
    }

    /*
    // Generate random confirmation code 
    $confirmation_code = bin2hex(random_bytes(16));
    */

    // Insert user info into the database (unconfirmed)
    $sql = "INSERT INTO `User` (`emailAddress`, `password`, `confirmationCode`) VALUES (:email, :password, :confirmation_code)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->bindParam(':confirmation_code', $confirmation_code, PDO::PARAM_STR);
    $stmt->execute();

    // Redirect user to profile page after registration
    header("Location: profile.php?user_id=" . $pdo->lastInsertId());
    exit();

    /*
    // Send confirmation email to user with a link to CONFIRM account (IN-PROGRESS)
    $to = $email;
    $subject = 'Confirm your email address';
    $message = 'Click the following link to confirm your email address: http://betterbuys.com/confirm.php?code=' . $confirmation_code;
    $headers = 'From: domain@betterbuys.com';

    // Send email
    mail($to, $subject, $message, $headers);
    echo "Registration successful! Please check your email to confirm your account.";
    exit();
    */
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register Account</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>
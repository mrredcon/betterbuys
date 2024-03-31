<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';

// 2^31 - 1, allows compatibility with 32-bit and 64-bit systems
$CONFIRMATION_CODE_MAX = 2147483647;

session_start();
$pdo = require_once 'connect.php';

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

    // Check if user has a pending entry in the database
    // If there is a pending entry...delete it and start over
    $sql = "DELETE FROM PendingUser WHERE `emailAddress` = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo 'Detected unfinished pending registration, deleting them.<br>';
    }

    // Insert user info into the database (unconfirmed)
    $confirmation_code = random_int(1, 2147483647);
    $sql = "INSERT INTO `PendingUser` (`emailAddress`, `isAdministrator`, `password`, `confirmationCode`, `dateCreated`) VALUES (:email, :isAdmin, :password, :code, :date)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':email' => $email,
	':isAdmin' => (int)false, // mysql stores booleans as TINYINT
	':password' => password_hash($password, PASSWORD_DEFAULT),
	':code' => $confirmation_code,
	':date' => date('Y-m-d H:i:s')
    ]);

    $config = get_config();

    //Create a new PHPMailer instance
    $mail = new PHPMailer();
    
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();
    
    //Enable SMTP debugging
    //SMTP::DEBUG_OFF = off (for production use)
    //SMTP::DEBUG_CLIENT = client messages
    //SMTP::DEBUG_SERVER = client and server messages
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    
    //Set the hostname of the mail server
    $mail->Host = $config['smtp_server'];
    //Use `$mail->Host = gethostbyname('smtp.gmail.com');`
    //if your network does not support SMTP over IPv6,
    //though this may cause issues with TLS
    
    //Set the SMTP port number:
    // - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
    // - 587 for SMTP+STARTTLS
    $mail->Port = $config['smtp_port'];
    
    //Set the encryption mechanism to use:
    // - SMTPS (implicit TLS on port 465) or
    // - STARTTLS (explicit TLS on port 587)
    $mail_enc = strtoupper($config['smtp_encryption']);

    if ($mail_enc === "SMTPS") {
    	$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else if ($mail_enc === "STARTTLS") {
    	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        die('Expected "SMTPS" or "STARTTLS" for smtp_encryption but received: "' . $mail_enc . '", aborting.');
    }
    
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    
    //Username to use for SMTP authentication - use full email address for gmail
    $mail->Username = $config['smtp_username'];
    
    //Password to use for SMTP authentication
    $mail->Password = $config['smtp_password'];
    
    //Set who the message is to be sent from
    //Note that with gmail you can only use your account address (same as `Username`)
    //or predefined aliases that you have configured within your account.
    //Do not use user-submitted addresses in here
    $mail->setFrom($config['smtp_from_address'], 'Better Buys');
    
    //Set an alternative reply-to address
    //This is a good place to put user-submitted addresses
    //$mail->addReplyTo('replyto@example.com', 'First Last');
    
    //Set who the message is to be sent to
    $mail->addAddress($email, 'Better Buys User');
    
    //Set the subject line
    $mail->Subject = 'Better Buys account registration';
    
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $mail->isHTML(true);

    $uri = $config['base_url'] . '/confirm.php?code=' . $confirmation_code;
    $mail->Body = 'Welcome to Better Buys!<br><a href="' . $uri . '">Click here to confirm your email address!</a>';
    
    //send the message, check for errors
    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
	echo "Registration successful! Please check your email to confirm your account.";
	exit();
    }

    // Redirect user to profile page after registration
    //header("Location: profile.php?user_id=" . $pdo->lastInsertId());
    //exit();
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


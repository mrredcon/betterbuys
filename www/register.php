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
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    if (!$email) {
	    echo 'Email given was either blank or in an invalid format.';
	    exit();
    }

    $password = filter_input(INPUT_POST, 'password');
    if (!$password) {
	    echo 'Password given was either blank or in an invalid format.';
	    exit();
    }

    // Make sure the password fits our requirements
    // Currently it is hardcoded to be at least 8 characters of
    // alphanumeric characters including symbols and spaces
    if (!preg_match('/[ -~]{8,}/', $password)) {
	    echo 'Given password did not meet the strength requirements.<br>' .
	    'Passwords must be at least 8 characters long, and ' .
	    'contain only letters, symbols, and numbers.';
	    exit();
    }

    // Check if user's email is already in use
    $sql = "SELECT COUNT(*) AS count FROM `User` WHERE `emailAddress` = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        echo "Email address is already in use.";
        exit();
    }

    $config = get_config();

    // Check if we have hit our pending user limit
    $max_pending_users = (int)$config['max_pending_users'];
    if ($max_pending_users <= 0) {
	    die('Invalid configuration for max_pending_users.  Please enter a positive integer.');
    }

    $pending_count = $pdo->query('SELECT COUNT(*) FROM PendingUser');
    $remaining = $max_pending_users - $pending_count->fetchColumn();
    if ($remaining < 1) {
    	// Purge any old entries from PendingUser
	$rows_to_delete = ($remaining * -1) + 1;
	$pdo->query('DELETE FROM PendingUser ORDER BY date ASC LIMIT ' . $rows_to_delete);
	//echo 'Deleted ' . $deleted->fetchColumn() . ' pending users from the database.<br>';
    }

    // Check if this new user has a pending entry in the database
    // If there is a pending entry...delete it and start over
    $sql = "DELETE FROM PendingUser WHERE `emailAddress` = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    // if ($stmt->rowCount() > 0) {
    //     echo 'Detected unfinished pending registration, deleting them.<br>';
    // }

    // Insert user info into the database (unconfirmed)
    $confirmation_code = random_int(1, 2147483647);
    $sql = "INSERT INTO `PendingUser` (`emailAddress`, `password`, `confirmationCode`, `dateCreated`) VALUES (:email, :password, :code, :date)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':email' => $email,
	':password' => password_hash($password, PASSWORD_DEFAULT),
	':code' => $confirmation_code,
	':date' => date('Y-m-d H:i:s')
    ]);

    $user_id = $pdo->lastInsertId();

    //Create a new PHPMailer instance
    $mail = new PHPMailer();
    
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();
    
    //Enable SMTP debugging
    //SMTP::DEBUG_OFF = off (for production use)
    //SMTP::DEBUG_CLIENT = client messages
    //SMTP::DEBUG_SERVER = client and server messages
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    
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

    $params = array(
	    "code" => $confirmation_code,
	    "userid" => $user_id
    );

    $template = file_get_contents($config['new_user_email_file']);
    if ($template == null || strlen($template) == 0) {
	    die('New user registration email template file missing, check new_user_email_file in the configuration file and make sure that file exists.');
    }

    $uri = $config['base_url'] . '/confirm.php?' . http_build_query($params);
    $msg_body = str_replace("CONFIRMATION_LINK_GOES_HERE", $uri, $template);
    $mail->Body = $msg_body;
    
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <title>Register your Better Buys account</title>
    <style>
	html,
	body {
	  height: 100%;
	}
	
	.form-signin {
	  max-width: 330px;
	  padding: 1rem;
	}
	
	.form-signin .form-floating:focus-within {
	  z-index: 2;
	}
	
	.form-signin input[type="email"] {
	  margin-bottom: -1px;
	  border-bottom-right-radius: 0;
	  border-bottom-left-radius: 0;
	}
	
	.form-signin input[type="password"] {
	  margin-bottom: 10px;
	  border-top-left-radius: 0;
	  border-top-right-radius: 0;
	}
    </style>
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
	<main class="form-signin w-100 m-auto">
		<form method="post">
			<h1 class="h3 mb-3 fw-normal">Better Buys</h1>

			<div class="form-floating">
				<input type="text" class="form-control" id="floatingInput" name="email" placeholder="name@example.com" required>
				<label for="floatingInput">Email address</label>
			</div>

			<div class="form-floating">
				<input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required>
				<label for="floatingPassword">Password</label>
			</div>

    			<?php if(isset($error)) { ?>
    			    <p class="mt-3 mb-3 text-danger"><?php echo $error; ?></p>
    			<?php } ?>

			<button class="btn btn-primary w-100 py-2" type="submit" value="Register">Register</button>
			<p class="mt-5 mb-3 text-body-secondary">&copy; 2024 UTSA CS 3773</p>
		</form>
	</main>

	<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

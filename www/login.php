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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <title>Login to your Better Buys account</title>
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

			<button class="btn btn-primary w-100 py-2" type="submit" value="Login">Sign in</button>
			<p class="mt-5 mb-3 text-body-secondary">&copy; 2024 UTSA CS 3773</p>
		</form>
	</main>

	<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

<?php

session_start();

$confirmation_code = filter_input(INPUT_GET, 'code');
$user_id = filter_input(INPUT_GET, 'userid');

// Check if the confirmation code is provided
if ($confirmation_code && $user_id) {
    $confirmation_code = (int)$confirmation_code;
    $user_id = (int)$user_id;

    $pdo = require_once 'connect.php';
    
    // Get the pending user row
    $sql = 'SELECT emailAddress, password, confirmationCode, dateCreated FROM PendingUser WHERE id=' . $user_id;
    $stmt = $pdo->query($sql);
    $pending_user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$pending_user) {
	    echo 'There is no pending registration associated with the given id.';
	    exit();
    }

    // Check to make sure the confirmation code matches
    if ($pending_user['confirmationCode'] != $confirmation_code) {
	    // TODO: Implement a timeout for wrong attempts.
	    echo 'Confirmation code does not match what is on file!';
	    exit();
    }

    // Check if user's email is already in use
    $sql = "SELECT COUNT(*) AS count FROM `User` WHERE `emailAddress` = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $pending_user['emailAddress'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        echo 'Email address is already in use by a registered user!';
        exit();
    }

    // Copy the data from PendingUser into User
    $sql = 'INSERT INTO User (emailAddress, isAdministrator, password) VALUES (:email, :isAdmin, :password)';
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
    	':email' => $pending_user['emailAddress'],
    	':isAdmin' => (int)false, // MySQL stores booleans as TINYINT(1)
    	':password' => $pending_user['password']
    ]);

    if ($result) {
        // good to go, just need to delete the pending user entry/entries
	$sql = 'DELETE FROM PendingUser WHERE emailAddress=:email';
	$stmt = $pdo->prepare($sql);

	$stmt->execute([
		':email' => $pending_user['emailAddress']
	]);

	echo 'Deleted ' . $stmt->rowCount() . ' pending users from the database.<br>';

    	echo "Email address confirmed successfully!";
    } else {
	echo 'Failed to create new user.';
    }
} else {
    echo "Confirmation code not provided.";
}
?>

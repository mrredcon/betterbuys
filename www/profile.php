<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
</head>
<body>
    <?php

    // Check if user is logged in
    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }

    // Check if user is an administrator
    $is_admin = $_SESSION['is_admin'];

    // System check for if user trying to access their account
    $user_to_show = filter_input(INPUT_GET, 'user_id');
    if ($user_to_show && !$is_admin && $user_to_show != $_SESSION['user_id']) {
        // If not, deny access
        echo "Access Denied";
        exit();
    }
    
    // If we are missing the GET parameter, just assume user is viewing their own profile
    if (!$user_to_show) {
	    $user_to_show = $_SESSION['user_id'];
    }

    $pdo = require_once 'connect.php';

    // Fetch user's profile info from the database
    $sql = 'SELECT firstName, lastName, physicalAddress, emailAddress, money, e164PhoneNumber FROM User WHERE id=:user_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_to_show, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$user){
        // If user does not exist. prompt an error message
        echo "User not found";
        exit();
    }

    // Display user's info
    echo "User Profile<br>";
    echo "First Name: " . $user['firstName'] . "<br>";
    echo "Last Name: " . $user['lastName'] . "<br>";
    echo "Email: " . $user['emailAddress'] . "<br>";
    echo "Physical Address: " . $user['physicalAddress'] . "<br>";
    echo "Phone Number: " . $user['e164PhoneNumber'] . "<br>";
    echo "Money: $" . $user['money'] . "<br>";

    // Allow user to edit their profile
    if ($is_admin || $_SESSION['user_id'] == $user_to_show) {
     echo "<a href='edit_profile.php?user_id=$user_to_show'>Edit Profile</a><br>";
    }

    // Delete account link (only visible to logged-in user)
    if ($_SESSION['user_id'] == $user_to_show) {
        echo "<a href='account_delete.php?user_id=$user_to_show'>Delete Account</a><br>";
    }

    // Log out link
    echo "<a href='logout.php'>Log Out</a>";
    ?>
</body>
</html>

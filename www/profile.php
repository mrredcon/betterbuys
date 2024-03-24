<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
</head>
<body>
    <?php

    session_start();

    // Check if user is logged in
    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }

    // Check if user is an administrator
    $is_admin = $_SESSION['is_admin'];

    // System check for if user trying to access their account
    if(!$is_admin && $_SESSION['user_id'] != $_GET['user_id']){
        // If not, deny access
        echo "Access Denied";
        exit();
    }

    require 'connect.php';

    // Fetch user's profile info from the database
    $user_id = $_GET['user_id'];
    $sql = "SELECT * FROM 'User' WHERE 'id' = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
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
    if ($is_admin || $_SESSION['user_id'] == $user_id) {
     echo "<a href='edit_profile.php?user_id=$user_id'>Edit Profile</a>";
    }

    // Delete account link (only visible to logged-in user)
    if ($_SESSION['user_id'] == $user_id) {
        echo "<a href='account_delete.php?user_id=$user_id'>Delete Account</a>";
    }

    // Log out link
    echo "<a href='logout.php'>Log Out</a>";
    ?>
</body>
</html>
<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// Handle form submission for profile edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];

    // Update user's profile info in the database
    $pdo = require_once 'connect.php';
    $sql = 'UPDATE User SET firstName=:first_name, lastName=:last_name, physicalAddress=:address, e164PhoneNumber=:phone_number WHERE id=:user_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
    $stmt->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();

    // Redirect back to profile page after updating
    header("Location: profile.php");
    exit();
}

// Fetch user's profile info from the database
$pdo = require_once 'connect.php';
$sql = 'SELECT firstName, lastName, physicalAddress, e164PhoneNumber FROM User WHERE id=:user_id';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
    // If user does not exist, prompt an error message
    echo "User not found";
    exit();
}

// Display form for editing profile
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
</head>
<body>
    <h2>Edit Profile</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        First Name: <input type="text" name="first_name" value="<?php echo $user['firstName']; ?>"><br><br>
        Last Name: <input type="text" name="last_name" value="<?php echo $user['lastName']; ?>"><br><br>
        Address: <input type="text" name="address" value="<?php echo $user['physicalAddress']; ?>"><br><br>
        Phone Number: <input type="text" name="phone_number" value="<?php echo $user['e164PhoneNumber']; ?>"><br><br>
        <input type="submit" value="Save">
    </form>
    <br>
</body>
</html>

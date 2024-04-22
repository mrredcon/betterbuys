<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
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
    $money = $_POST['money'];

    // Validate money amount
    if ($money < 0) {
        echo "Money amount must be positive.";
        exit();
    }

    // Update user's profile info in the database
    $pdo = require_once 'connect.php';
    $sql = 'UPDATE User SET firstName=:first_name, lastName=:last_name, physicalAddress=:address, e164PhoneNumber=:phone_number, money=:money WHERE id=:user_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
    $stmt->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
    $stmt->bindParam(':money', $money, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();

    // Redirect back to profile page after updating
    header("Location: profile.php");
    exit();
}

// Fetch user's profile info from the database
$pdo = require_once 'connect.php';
$sql = 'SELECT firstName, lastName, physicalAddress, e164PhoneNumber, money FROM User WHERE id=:user_id';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // If user does not exist, prompt an error message
    echo "User not found";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <title>Edit Profile</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Profile</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        First Name: <input type="text" name="first_name" value="<?php echo $user['firstName']; ?>"><br><br>
        Last Name: <input type="text" name="last_name" value="<?php echo $user['lastName']; ?>"><br><br>
        Address: <input type="text" name="address" value="<?php echo $user['physicalAddress']; ?>"><br><br>
        Phone Number: <input type="text" name="phone_number" value="<?php echo $user['e164PhoneNumber']; ?>"><br><br>
        Money: <input type="number" step="0.01" name="money" value="<?php echo $user['money']; ?>"><br><br>
        <input type="submit" value="Save">
    </form>
    <br>
</div>

</body>
</html>

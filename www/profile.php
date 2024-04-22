<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <title>User Profile</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        h2 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .btn-container {
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            margin-right: 10px; /* Add margin between buttons */
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            outline: none;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn:last-child {
            margin-right: 0; /* Remove margin from the last button */
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
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

if (!$user) {
    // If user does not exist. prompt an error message
    echo "Root admin cannot view the profile page or user not found.";
    exit();
}

// Display user's info
echo "<div class='container'>";
echo "<h2>User Profile</h2>";
echo "First Name: " . $user['firstName'] . "<br>";
echo "Last Name: " . $user['lastName'] . "<br>";
echo "Email: " . $user['emailAddress'] . "<br>";
echo "Physical Address: " . $user['physicalAddress'] . "<br>";
echo "Phone Number: " . $user['e164PhoneNumber'] . "<br>";
echo "Money: $" . $user['money'] . "<br>";

// Display previous purchases
echo "<h3>Previous Purchases</h3>";
// Fetch user's previous purchases from the TransactionItem table
$sql = 'SELECT ti.productId, tr.purchaseDate, ti.quantity, tr.subtotal, tr.shippingFee 
            FROM TransactionItem ti
            INNER JOIN Transaction tr ON ti.transactionId = tr.id
            WHERE tr.userId = :user_id';

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_to_show, PDO::PARAM_INT);
$stmt->execute();
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($purchases) > 0) {
    echo "<table>";
    echo "<tr><th>Product</th><th>Date</th><th>Quantity</th><th>Subtotal</th><th>Shipping Fee</th></tr>";
    foreach ($purchases as $purchase) {
        // Fetch product information for each purchase
        $product_id = $purchase['productId'];
        $product_sql = 'SELECT name FROM Product WHERE id=:product_id';
        $product_stmt = $pdo->prepare($product_sql);
        $product_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $product_stmt->execute();
        $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

        echo "<tr>";
        echo "<td>" . $product['name'] . "</td>";
        echo "<td>" . $purchase['purchaseDate'] . "</td>";
        echo "<td>" . $purchase['quantity'] . "</td>";
        echo "<td>$" . $purchase['subtotal'] . "</td>";
        echo "<td>$" . $purchase['shippingFee'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No previous purchases found.<br>";
}

// Allow user to edit their profile
if ($is_admin || $_SESSION['user_id'] == $user_to_show) {
    echo "<div class='btn-container'>";
    echo "<a href='edit_profile.php?user_id=$user_to_show' class='btn'>Edit Profile</a>";
}

// Delete account link (only visible to logged-in user)
if ($_SESSION['user_id'] == $user_to_show) {
    echo "<a href='account_delete.php?user_id=$user_to_show' class='btn'>Delete Account</a>";
}

// Log out link
echo "<a href='logout.php' class='btn'>Log Out</a>";

// Home link
echo "<a href='index.php' class='btn'>Home</a>";
echo "</div>"; // Closing div for .btn-container
echo "</div>"; // Closing div for .container
?>
</body>
</html>

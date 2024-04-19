<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission for adding credit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $amount = floatval($_POST['amount']); // Convert input to float
    // Assume input is full dollars
    $amount = number_format($amount, 2); // Format to two decimal places

    try {
        // Update user's credit in the database
        $pdo = require_once 'connect.php';
        $sql = 'UPDATE User SET money = money + :amount WHERE id = :user_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Debugging ->>
        
        // Check if database update was successful
        if ($stmt->rowCount() > 0) {
            // Update session variable with new credit balance
            if (!isset($_SESSION['money'])) {
                $_SESSION['money'] = 0; // Initialize if not set
            }
            $_SESSION['money'] += $amount;

            // Redirect back to profile page after updating
            header("Location: profile.php");
            exit();
        } else {
            // If database update failed, display error message
            echo "Failed to add credit. No rows affected.<br>";
            echo "User ID: " . $_SESSION['user_id'] . "<br>";
            echo "Amount: " . $amount . "<br>";
            echo "SQL Error: ";
            print_r($stmt->errorInfo());
            exit();
        }
    } catch (PDOException $e) {
        // Print PDOException message for debugging
        echo "Failed to add credit. Error: " . $e->getMessage();
        exit();
    }
}

// Display form for adding credit
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Credit</title>
</head>
<body>
    <h2>Add Credit</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Amount: <input type="text" name="amount" required><br><br>
        <!-- Accepts any quantity, assumed to be full dollars -->
        <input type="submit" value="Add Credit">
    </form>
</body>
</html>

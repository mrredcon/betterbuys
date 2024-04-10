<?php

$pdo = require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // probably needs input validation here?

    $sql = "INSERT INTO 
                    Store ( name, 
                            physicalAddress, 
                            latitude, 
                            longitude, 
                            onlineOnly, 
                            storeNumber) 
            VALUES 
                    (       :name, 
                            :physicalAddress, 
                            :latitude, 
                            :longitude, 
                            :onlineOnly, 
                            :storeNumber)";

    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':name' => $_POST['name'],
        ':physicalAddress' => $_POST['physicalAddress'],
        ':latitude' => $_POST['latitude'],
        ':longitude' => $_POST['longitude'],
        ':onlineOnly' => $_POST['onlineOnly'],
        ':storeNumber' => $_POST['storeNumber'],
    ]);

    // back to the admin page we go!
    header('Location: admin.php');
    exit;
}
?>

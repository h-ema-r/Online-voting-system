<?php

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=localhost;dbname=votersystem", 'root', 'admin');

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //echo "Connected successfully"; 

} catch(PDOException $e) {
    // Handle connection error
    die("Connection failed: " . $e->getMessage());
}

?>

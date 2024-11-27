<?php
include 'connect.php';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $role = $_POST['role'];

    if ($_POST['password'] !== $_POST['password-confirm']) {
        echo "Passwords do not match!";
        exit;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name'];
        $imageTemp = $_FILES['image']['tmp_name'];
        $uploadDir = 'uploads/';

        $imageName = uniqid() . '-' . basename($image);
        $uploadFile = $uploadDir . $imageName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['image']['type'];

        if (!in_array($fileType, $allowedTypes)) {
            echo "Invalid file type!";
            exit;
        }

        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            echo "File size too large!";
            exit;
        }

        if (move_uploaded_file($imageTemp, $uploadFile)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO `votersystem` (name, mobile, password, address, image, role) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bindParam(1, $name, PDO::PARAM_STR);
            $stmt->bindParam(2, $mobile, PDO::PARAM_STR);
            $stmt->bindParam(3, $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(4, $address, PDO::PARAM_STR);
            $stmt->bindParam(5, $uploadFile, PDO::PARAM_STR);
            $stmt->bindParam(6, $role, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo "Data successfully inserted";
            } else {
                echo "Error: " . implode(", ", $stmt->errorInfo());
            }

        } else {
            echo "File upload failed!";
        }
    } else {
        echo "No file uploaded or file upload error!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body class="bg-info">
<h1 class="mt-5 text-center text-white">Online Voting System</h1>
<hr class="text-dark">
<div class="container w-50 bg-light p-4 rounded">
    <h2 class="text-center">Registration</h2>

    <form class="mt-3" method="post" enctype="multipart/form-data" action="login.php">
        <div class="row">
            <div class="form-group col-md-6">
                <input type="text" class="form-control" placeholder="Name" name="name" required autocomplete="off">
            </div>
            <div class="form-group col-md-6">
                <input type="text" class="form-control" placeholder="Mobile" name="mobile" maxlength="10" minlength="10" required autocomplete="off">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                <input type="password" class="form-control" placeholder="Password" name="password" required autocomplete="off">
            </div>
            <div class="form-group col-md-6">
                <input type="password" class="form-control" placeholder="Confirm Password" name="password-confirm" required autocomplete="off">
            </div>
        </div>

        <div class="form-group">
            <input type="text" class="form-control" placeholder="Address" name="address" required autocomplete="off">
        </div>

        <div class="form-group">
            <label for="file-upload">Upload Image:</label>
            <input type="file" class="form-control" id="file-upload" name="image" required>
        </div>

        <div class="form-group">
            <label for="voterclass">Select your role:</label>
            <select class="form-control" id="voterclass" name="role" required>
                <option value="" disabled selected>Select your role</option>
                <option value="voter">Voter</option>
                <option value="group">Group</option>
            </select>
        </div>

        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-primary" name="submit">Register</button>
        </div>
        <div class="mt-3 text-center">
            <p>Already user? <a href="login.php">Login here</a></p>
        </div>
    </form>
</div>    

</body>
</html>

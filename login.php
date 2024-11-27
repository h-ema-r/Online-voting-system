<?php 

$login = 0;
$invalid = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  include 'connect.php';

  // Get data from POST
  $mobile = $_POST['mobile'];
  $password = $_POST['password'];
  $role = $_POST['role'];

  try {
      $sql = "SELECT * FROM `votersystem` WHERE mobile = :mobile AND role = :role";
      
      $stmt = $conn->prepare($sql);

      // Bind the parameters
      $stmt->bindParam(':mobile', $mobile, PDO::PARAM_STR);
      $stmt->bindParam(':role', $role, PDO::PARAM_STR);

      $stmt->execute();

      // Check if any row matches
      if ($stmt->rowCount() > 0) {
          $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch user details

          // Verify password
          if (password_verify($password, $user['password'])) {
              // Password is correct
              session_start();
              $_SESSION['mobile'] = $mobile;
              $_SESSION['role'] = $role;

              // Redirect based on role
              if ($role == 'Voter') {
                  header('location: dashboard.php');
              } else if ($role == 'Group') {
                  header('location: group_home.php');
              }
              exit();
          } else {
              // Invalid password
              $invalid = 1;
          }
      } else {
          // No user found with provided mobile and role
          $invalid = 1;
      }
  } catch (PDOException $e) {
      // Handle error
      echo "Error: " . $e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body class="bg-info">

<h1 class="mt-5 text-center text-white">Online Voting System</h1>
<hr>
<div class="container w-50 bg-white p-4 rounded">
    <h2 class="text-center">Login</h2>
    <form class="mt-3" method="POST">
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Enter mobile" name="mobile" maxlength="10" minlength="10" required autocomplete="off">
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Enter password" name="password" required autocomplete="off">
        </div>
        <div class="form-group">
            <select class="form-control" id="voterclass" name="role">
                <option value="Voter">Voter</option>
                <option value="Group">Group</option>
            </select>
        </div>
        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
    </form>
    


    <div class="mt-3 text-center">
        <p>New user? <a href="register.php">Register here</a></p>
    </div>
</div>

</body>
</html>

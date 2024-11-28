 <?php
session_start();
include 'connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['mobile']) || !isset($_SESSION['role'])) {
    header('location:login.php');
    exit();
}

// Fetch user details
$mobile = $_SESSION['mobile'];
$role = $_SESSION['role'];

try {
    $stmt = $conn->prepare("SELECT * FROM votersystem WHERE mobile = :mobile AND role = :role");
    $stmt->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $stmt->bindParam(':role', $role, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        echo "User not found!";
        exit();
    }

    // Fetch groups and votes
    $groupsStmt = $conn->prepare("SELECT * FROM groups");
    $groupsStmt->execute();
    $groups = $groupsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Handle voting
if (isset($_POST['vote']) && isset($_POST['group_id'])) {
    $groupId = $_POST['group_id'];

    try {
        // Prevent double voting
        if ($user['status'] === 'Voted') {
            echo "<script>alert('You have already voted!');</script>";
        } else {
            // Update group votes
            $voteStmt = $conn->prepare("UPDATE groups SET votes = votes + 1 WHERE id = :group_id");
            $voteStmt->bindParam(':group_id', $groupId, PDO::PARAM_INT);
            $voteStmt->execute();

            // Update user status
            $statusStmt = $conn->prepare("UPDATE votersystem SET status = 'Voted' WHERE mobile = :mobile");
            $statusStmt->bindParam(':mobile', $mobile, PDO::PARAM_STR);
            $statusStmt->execute();

            echo "<script>alert('Vote submitted successfully!');</script>";
            header("Refresh:0"); // Refresh the page to update status
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Handle back and logout
if (isset($_POST['backbtn'])) {
    header('location:login.php');
    exit();
}
if (isset($_POST['logoutbtn'])) {
    session_destroy();
    header('location:register.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System - Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <style>
        #backbtn, #logoutbtn {
            padding: 5px 10px;
            font-size: 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
        }
        #backbtn { float: left; margin-left: 5%; }
        #logoutbtn { float: right; margin-right: 5%; }
        .card { margin-top: 20px; }
    </style>
</head>
<body class="bg-info">
    <div id="headerSection">
        <form method="post">
            <button id="backbtn" name="backbtn">Back</button>
            <button id="logoutbtn" name="logoutbtn">Logout</button>
            <h1 class="text-center text-white mt-5">Online Voting System</h1>
        </form>
    </div>
    <hr>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Profile Information</h5>
                <p><strong>Name:</strong> <?= htmlspecialchars($user['name']); ?></p>
                <p><strong>Mobile:</strong> <?= htmlspecialchars($user['mobile']); ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($user['address']); ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($user['status']); ?></p>
            </div>
        </div>

        <h3 class="text-white mt-4">Vote for a Group</h3>
        <div class="row">
            <?php foreach ($groups as $group): ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($group['name']); ?></h5>
                            <p><strong>Votes:</strong> <?= $group['votes']; ?></p>
                            <form method="post">
                                <input type="hidden" name="group_id" value="<?= $group['id']; ?>">
                                <button type="submit" name="vote" class="btn btn-primary" <?= $user['status'] === 'Voted' ? 'disabled' : ''; ?>>
                                    <?= $user['status'] === 'Voted' ? 'Voted' : 'Vote'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html> 
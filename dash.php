<?php
session_start();

if (!isset($_SESSION['userdata'])) {
    header('location:./');
    exit();
}

$userdata = $_SESSION['userdata'];
$groupdata = $_SESSION['groupdata'];
$status = ($userdata['status'] == 0) ? '<b style="color:red">Not Voted</b>' : '<b style="color:green">Voted</b>';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['backbtn'])) {
        header('location:login.php');
        exit();
    }

    if (isset($_POST['logoutbtn'])) {
        session_destroy();
        header('location:register.php');
        exit();
    }
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
        #backbtn {
            float: left;
            margin: 10px;
        }

        #logoutbtn {
            float: right;
            margin: 10px;
        }

        #profile {
            width: 30%;
            float: left;
        }

        #group {
            width: 60%;
            padding: 20px;
            float: right;
        }

        #mainpanel {
            padding: 10px;
            
        }

        #voted {
            background-color: green;
        }
    </style>
</head>

<body class="bg-info">
    <div class="container-fluid" id="mainsection">
        <form method="post">
            <button id="backbtn" name="backbtn" class="btn btn-secondary">Back</button>
            <button id="logoutbtn" name="logoutbtn" class="btn btn-danger">Logout</button>
            <h1 class="text-center text-white mt-5">Online Voting System</h1>
        </form>
        <hr>

        <div id="mainpanel">
            <!-- Profile Section -->
            <div id="profile" class="bg-white p-4 rounded">
                <h3>User Profile</h3>
                <img src="./<?php echo $userdata['image']; ?>" alt="Profile Picture" height="100" width="100" class="rounded-circle">
                <p><b>Name:</b> <?php echo $userdata['name']; ?></p>
                <p><b>Mobile:</b> <?php echo $userdata['mobile']; ?></p>
                <p><b>Address:</b> <?php echo $userdata['address']; ?></p>
                <p><b>Status:</b> <?php echo $status ?></p>
            </div>



            <!-- Group Section -->
            <div id="group" class="bg-white p-4 rounded">
                <?php
                if (isset($_SESSION['groupdata']) && !empty($_SESSION['groupdata'])) {
                    for ($i = 0; $i < count($_SESSION['groupdata']); $i++) {
                ?>
                        <div>
                            <img style="float:right" src="./<?php echo $_SESSION['groupdata'][$i]['image']; ?>" height="110" width="110" >
                            <p><b>Group Name:</b> <?php echo $_SESSION['groupdata'][$i]['name']; ?></p>
                            <p><b>Votes:</b> <?php echo $_SESSION['groupdata'][$i]['votes']; ?></p>
                            <form method="post" action='vote.php'>
                                <input type="hidden" name="gvotes" value="<?php echo $_SESSION['groupdata'][$i]['votes']; ?>">
                                <input type="hidden" name="group_id" value="<?php echo $_SESSION['groupdata'][$i]['id']; ?>">

                                <?php
                                if ($_SESSION['userdata']['status'] == 0) {
                                ?>
                                    <input type="submit" name="votebtn" value="vote" id="votebtn" class="btn btn-primary">
                                <?php

                                } else {
                                ?>
                                    <button disabled type="button" name="votebtn" value="vote" id="voted">Voted</button>
                                <?php
                                }
                                ?>
                            </form>
                        </div>
                        <hr>
                <?php
                    }
                } else {
                    echo "<p>No groups available.</p>";
                }
                ?>
            </div>

        </div>


</body>

</html>
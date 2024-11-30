<?php
session_start();

include('connect.php');

    $votes = $_POST['gvotes'];
    $total_votes=$votes+1;
    $gid = $_POST['group_id'];
    $uid = $_SESSION['userdata']['id'];


    // Update votes for the group
    $update_votes = $conn->prepare("UPDATE votersystem SET votes = ? WHERE id = ?");
    $update_votes->bindParam(1, $total_votes);
    $update_votes->bindParam(2, $gid);
   $val=1;

    // Update user's voting status
    $update_user_status = $conn->prepare("UPDATE votersystem SET status = ? WHERE id = ?");
    $update_user_status->bindParam(1,$val);
    $update_user_status->bindParam(2, $uid);
   
// Check if both updates are successful
if ($update_votes->execute() && $update_user_status->execute()) {
    // Refresh group data in the session
    $groupSql = "SELECT * FROM `votersystem` WHERE role = 'group'";
    $groupdata = $conn->prepare($groupSql);
    $groupdata->execute();
    $_SESSION['groupdata'] = $groupdata->fetchAll(PDO::FETCH_ASSOC);

    // Update user status in the session
    $_SESSION['userdata']['status'] = 1;

    echo '<script>alert("Vote cast successfully!"); window.location="dash.php";</script>';
} else {
    echo '<script>alert("There was an error casting your vote."); window.location="dash.php";</script>';
}

?>

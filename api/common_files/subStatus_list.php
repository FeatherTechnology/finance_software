<?php
require "../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$cp_id = $_POST['cp_id'];
$sub_status = $_POST['sub_status'];

$response = []; // Define response array

// Run the update query
$qry = $pdo->query("UPDATE `customer_status` SET `coll_status`='$sub_status', `updated_on`=CURRENT_TIMESTAMP WHERE cus_profile_id='$cp_id'");

// Check if the update was successful
if ($qry) {
    $response['status'] = 'success';
    $response['message'] = 'Customer status updated successfully';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to update customer status';
}

$pdo = null; // Close Connection

echo json_encode($response);

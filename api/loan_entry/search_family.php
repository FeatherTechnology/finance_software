<?php
require '../../ajaxconfig.php';

// Initialize an empty response array
$response = array();

// Retrieve POST parameters
$name = isset($_POST['name']) ? $_POST['name'] : '';
$aadhar = isset($_POST['aadhar']) ? $_POST['aadhar'] : '';
$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';

// Construct the query based on the provided search criteria
$query = "SELECT fi.*, le.cus_name as under_customer_name, le.cus_id as under_customer_id 
          FROM family_info fi
          JOIN loan_entry le ON fi.cus_id = le.cus_id 
          WHERE 1=1"; // Base query

if (!empty($name)) {
    $query .= " AND fi.fam_name LIKE '%$name%'";
}

if (!empty($aadhar)) {
    $query .= " AND fi.fam_aadhar = '$aadhar'";
}

if (!empty($mobile)) {
    $query .= " AND fi.fam_mobile = '$mobile'";
}

// Execute the query
$result = $pdo->query($query);

if ($result) {
    // Fetch all matching records
    $response = $result->fetchAll(PDO::FETCH_ASSOC);
}

// Return the response as JSON
echo json_encode($response);
?>



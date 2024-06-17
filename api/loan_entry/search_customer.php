<?php
require '../../ajaxconfig.php';

// Initialize an empty response array
$response = array();

// Retrieve POST parameters
$name = isset($_POST['name']) ? $_POST['name'] : '';
$cusid = isset($_POST['cusid']) ? $_POST['cusid'] : '';
$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';

// Construct the query based on the provided search criteria
$query = "SELECT * FROM loan_entry WHERE 1=1"; // Base query

if (!empty($name)) {
    $query .= " AND cus_name LIKE '%$name%'";
}

if (!empty($cusid)) {
    $query .= " AND cus_id = '$cusid'";
}

if (!empty($mobile)) {
    $query .= " AND (mobile1 = '$mobile' OR mobile2 = '$mobile')";
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


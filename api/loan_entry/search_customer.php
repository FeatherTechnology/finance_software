<?php
require '../../ajaxconfig.php';

// Initialize an empty response array
$response = array();
$familyResponse = array();

// Retrieve POST parameters
$name = isset($_POST['name']) ? $_POST['name'] : '';
$cusid = isset($_POST['cusid']) ? $_POST['cusid'] : '';
$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
$cus_profile_id = isset($_POST['cus_profile_id']) ? $_POST['cus_profile_id'] : '';

// Construct the query based on the provided search criteria
$query = "SELECT DISTINCT * FROM customer_profile WHERE id != '$cus_profile_id'";

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

// Fetch related family info
$familyQuery = "SELECT DISTINCT fi.*, cp.cus_name AS under_customer_name, cp.cus_id AS under_customer_id 
                FROM family_info fi 
                LEFT JOIN customer_profile cp ON fi.cus_id = cp.cus_id 
                WHERE cp.id != '$cus_profile_id'";

if (!empty($name)) {
    $familyQuery .= " AND fi.fam_name LIKE '%$name%'";
}

if (!empty($cusid)) {
    $familyQuery .= " AND fi.fam_aadhar = '$cusid'";
}

if (!empty($mobile)) {
    $familyQuery .= " AND fi.fam_mobile = '$mobile'";
}

$familyResult = $pdo->query($familyQuery);
if ($familyResult) {
    $familyResponse = $familyResult->fetchAll(PDO::FETCH_ASSOC);
}

// Return the response as JSON
echo json_encode(array(
    'customers' => $response,
    'family' => $familyResponse
));

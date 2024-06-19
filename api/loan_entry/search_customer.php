<?php
require '../../ajaxconfig.php';

// Initialize an empty response array
$response = array();
$familyResponse = array();

// Retrieve POST parameters
$name = isset($_POST['name']) ? $_POST['name'] : '';
$cusid = isset($_POST['cusid']) ? $_POST['cusid'] : '';
$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';

// Construct the query based on the provided search criteria
$query = "SELECT * FROM customer_profile WHERE 1=1"; // Base query

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

    if (!empty($response)) {
        $cus_ids = array_column($response, 'cus_id');
        $cus_ids_str = implode(',', array_map(function($id) { return "'$id'"; }, $cus_ids));

        // Fetch related family info
        $familyQuery = "SELECT fi.*, le.cus_name AS under_customer_name, le.cus_id AS under_customer_id
                        FROM family_info fi
                        JOIN customer_profile le ON fi.cus_id = le.cus_id
                        WHERE fi.cus_id IN ($cus_ids_str)";
        $familyResult = $pdo->query($familyQuery);

        if ($familyResult) {
            $familyResponse = $familyResult->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}

// Return the response as JSON
echo json_encode(array(
    'customers' => $response,
    'family' => $familyResponse
));
?>


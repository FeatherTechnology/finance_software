<?php
require '../../ajaxconfig.php';

$response = array();

if (isset($_POST['cus_id']) && isset($_POST['customer_profile_id'])) {
    $cus_id = $_POST['cus_id'];
    $customer_profile_id = $_POST['customer_profile_id'];

    // Prepare and execute the query to fetch the relationship based on the property holder ID
   
    $stmt = $pdo->prepare("SELECT COUNT(*) AS loan_count, MIN(lelc.loan_date) AS first_loan_date  FROM loan_entry_loan_calculation lelc  left join customer_status cs on cs.cus_id = lelc.cus_id WHERE lelc.cus_id = ? and cs.status >=7 ;");

  
    $stmt->execute([$cus_id]);

    // Fetch the result
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['loan_count'] = $row['loan_count'];
        $response['first_loan_date'] = $row['first_loan_date'];
    } else {
        $response['loan_count'] = ''; 
        $response['first_loan_date'] = ''; 
    }
} else {
    $response['loan_count'] = ''; 
    $response['first_loan_date'] = ''; 
}

$pdo = null; // Close the connection

echo json_encode($response);
?>

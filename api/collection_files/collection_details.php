<?php
require  '../../ajaxconfig.php';
$cp_id = $_POST['cp_id'];
$records = array();
$selectIC = $pdo->query("SELECT lelc.loan_category
FROM loan_entry_loan_calculation lelc
JOIN customer_status cs ON lelc.id = cs.loan_calculation_id
WHERE lelc.cus_profile_id = '$cp_id' AND cs.status = 7 ");
if ($selectIC->rowCount() > 0) {
    $row = $selectIC->fetch();
    $records['loan_category'] = $row["loan_category"];
}

echo json_encode($records);

<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];

$qry = $pdo->query("SELECT cus_id, cus_name, cus_data, mobile1, areaname, pic, lc.loan_category, lelc.loan_amnt, lelc.principal_amnt, lelc.interest_amnt, lelc.total_amnt, lelc.due_amnt, lelc.doc_charge_calculate, lelc.processing_fees_calculate, lelc.net_cash,lelc.loan_date, lelc.due_startdate, lelc.maturity_date, lelc.due_period, lelc.profit_type, lelc.due_method, lelc.scheme_due_method, lelc.scheme_day
FROM loan_entry_loan_calculation lelc 
JOIN customer_profile cp ON lelc.cus_profile_id = cp.id 
JOIN area_name_creation anc ON cp.area = anc.id 
LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
WHERE lelc.cus_profile_id ='$id' ");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close connection.

echo json_encode($result);

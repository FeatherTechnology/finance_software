<?php
require '../../ajaxconfig.php';
$cp_id = $_POST['cp_id'];

$qry = $pdo->query("SELECT profit_type, scheme_due_method, due_type FROM loan_entry_loan_calculation where cus_profile_id = '$cp_id' ");
$row = $qry->fetch();
$profit_type = $row['profit_type'];
$scheme_due_method = $row['scheme_due_method'];

if ($profit_type == 0) {
    $response['due_method'] = 'Monthly';
    $response['loan_type'] = $row['due_type'];
} else if ($profit_type == 1) {
    if ($scheme_due_method == 1) {
        $response['due_method'] = 'Monthly';
    } else if ($scheme_due_method == 2) {
        $response['due_method'] = 'Weekly';
    } else if ($scheme_due_method == 3) {
        $response['due_method'] = 'Daily';
    }
    $response['loan_type'] = 'Scheme';
}

echo json_encode($response);

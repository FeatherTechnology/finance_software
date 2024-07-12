<?php
include "../../../ajaxconfig.php";
session_start();
$user_id = $_SESSION['user_id'];

$clr_cat = $_POST['clr_cat'];
$bank_id = $_POST['bank_id'];
$crdb = $_POST['crdb'];
$trans_id = $_POST['trans_id'];

$records = array();

$qry = "SELECT ";

if ($crdb == 'Credit') {
    if ($clr_cat == 1) { // collection
        $qry .= "coll_code AS ref_code FROM collection WHERE trans_id = '$trans_id' AND bank_id = '$bank_id' ";
    } elseif ($clr_cat == 3 || $clr_cat == 4 || $clr_cat == 6 || $clr_cat == 7 || $clr_cat == 8) { //3-other income //4-Exchange //6-investment //7-Deposit //8-EL
        $qry .= "ref_id AS ref_code FROM other_transaction WHERE insert_login_id = '$user_id' AND trans_id = '$trans_id' AND bank_id = '$bank_id'";
    }
} else if ($crdb == 'Debit') {
    if ($clr_cat == 4 || $clr_cat == 6 || $clr_cat == 7 || $clr_cat == 8) { //4-Exchange //6-investment //7-Deposit //8-EL
        $qry .= "ref_id AS ref_code FROM other_transaction WHERE insert_login_id = '$user_id' AND trans_id = '$trans_id' AND bank_id = '$bank_id'";
    } elseif ($clr_cat == 9) { // Expenses
        $qry .= "invoice_id AS ref_code FROM expenses WHERE insert_login_id = '$user_id' AND trans_id = '$trans_id' AND bank_id = '$bank_id'";
    }
}

$runQry = $pdo->query($qry);
if ($runQry->rowCount() > 0) {
    $i = 0;
    while ($row = $runQry->fetch()) {
        $records[$i]['ref_code'] = $row['ref_code'];
    }
}

echo json_encode($records);

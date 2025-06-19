<?php
require '../../ajaxconfig.php';

$userid = $_POST['userid'];
$cp_id = $_POST['cp_id'];
$pending_sts = $_POST['pending_sts'];
$od_sts = $_POST['od_sts'];
$due_nil_sts = $_POST['due_nil_sts'];
$bal_amt = $_POST['bal_amt'];

$curdate = date('Y-m-d');

$qry = $pdo->query("SELECT issue_date FROM loan_issue WHERE cus_profile_id = '$cp_id' ");
$row = $qry->fetch();
if (date('Y-m-d', strtotime($row['issue_date'])) > $curdate && $bal_amt != 0) {
    $sub_sts = 'Current';
} else {
    if ($pending_sts == 'true' && $od_sts == 'false') {
        $sub_sts = 'Pending';
    } else if ($od_sts == 'true' && $due_nil_sts == 'false') {
        $sub_sts = 'OD';
    } else if ($due_nil_sts == 'true') {
        $sub_sts = 'Due Nil';
    } else if ($pending_sts == 'false') {
        $sub_sts = 'Current';
    }
}

$query = $pdo->query("UPDATE `customer_status` SET `coll_status`='$sub_sts', `insert_login_id`='$userid', `created_on`=NOW() WHERE `cus_profile_id`='$cp_id' ");

echo json_encode($query ? 1 : 2);

<?php
require '../../ajaxconfig.php';

$userid = $_POST['userid'];
if (isset($_POST['cp_id'])) {
    $cp_id = $_POST['cp_id'];
}

if (isset($_POST['pending_sts'])) {
    $pending_sts = explode(',', $_POST['pending_sts']);
}

if (isset($_POST['od_sts'])) {
    $od_sts = explode(',', $_POST['od_sts']);
}

if (isset($_POST['due_nil_sts'])) {
    $due_nil_sts = explode(',', $_POST['due_nil_sts']);
}

if (isset($_POST['bal_amt'])) {
    $bal_amt = explode(',', $_POST['bal_amt']);
}

$curdate = date('Y-m-d');
$qry = $pdo->query("SELECT issue_date,cus_id FROM loan_issue WHERE cus_profile_id = '$cp_id' ");
$row = $qry->fetch();
$cus_id = $row['cus_id'];
   $i=1;
if (date('Y-m-d', strtotime($row['issue_date'])) > date('Y-m-d', strtotime($curdate))  and $bal_amt[$i - 1] != 0) { //If the start date is on upcoming date then the sub status is current, until current date reach due_start_from date.
                $sub_sts = 'Current';
        } else {
            if ($pending_sts[$i - 1] == 'true' && $od_sts[$i - 1] == 'false') { //using i as 1 so subract it with 1
                $sub_sts = 'Pending';
                
            } else if ($od_sts[$i - 1] == 'true' && $due_nil_sts[$i - 1] == 'false') {
                $sub_sts = 'OD';
                
            } elseif ($due_nil_sts[$i - 1] == 'true') {
                $sub_sts = 'Due Nil';
                
            } elseif ($pending_sts[$i - 1] == 'false') {
                $sub_sts = 'Current';
                
            }
        }

    $query = $pdo->query("UPDATE `customer_status` SET `coll_status`='$sub_sts', `insert_login_id`='$userid', `created_on`=NOW() WHERE `cus_profile_id`='$cp_id' ");

if($query){
    $result = 1;
}else{
    $result = 2;
}

echo json_encode($result);

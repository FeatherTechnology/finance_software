<?php
require '../../ajaxconfig.php';

$aadhar_num = $_POST['aadhar_num'];
$result = '';
$qry = $pdo->query("SELECT * FROM `customer_status` WHERE aadhar_num='$aadhar_num' AND status = 7 AND status <= 8 ");
if ($qry->rowCount() >0) {
    $result = "Additional"; //Additional
}else{
    $qry = $pdo->query("SELECT * FROM `customer_status` WHERE aadhar_num='$aadhar_num' AND status >= 9 ");
    if($qry->rowCount()>0){
        $result = "Renewal";
    }
}
$pdo = null; //Close connection.

echo json_encode($result);
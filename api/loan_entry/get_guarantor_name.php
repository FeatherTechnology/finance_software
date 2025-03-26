<?php
//Also using in property holder name, KYC Family Member
//Aslo Using in Loan Issue.
//Also using in NOC.
require '../../ajaxconfig.php';

$response =array();
$aadhar_num = $_POST['aadhar_num'];
$qry = $pdo->query("SELECT id, fam_name FROM  family_info WHERE aadhar_num = '$aadhar_num' ");
if ($qry->rowCount() > 0) {
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close Connection

echo json_encode($response);
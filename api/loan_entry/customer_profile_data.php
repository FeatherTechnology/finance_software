<?php
require '../../ajaxconfig.php';

$aadhar_num = $_POST['aadhar_num'];

$qry = $pdo->query("SELECT * FROM `customer_profile` WHERE aadhar_num='$aadhar_num'");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close connection.

echo json_encode($result);
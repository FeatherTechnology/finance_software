<?php
require "../../ajaxconfig.php";
$result = array();

$type = $_POST['type'];
$id = $_POST['id'];
if($type =='1' || $type =='2'){
    $cndtn = "cp.id = '$id'";
}else{
    $cndtn = "fi.id = '$id'";
}
$qry = $pdo->query("SELECT cp.cus_name, fi.id, fi.fam_name, fi.fam_relationship FROM customer_profile cp JOIN family_info fi ON cp.guarantor_name = fi.id WHERE $cndtn ");
if ($qry->rowCount() > 0) {
    $result = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null;
echo json_encode($result);

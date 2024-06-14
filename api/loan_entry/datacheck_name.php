<?php
require '../../ajaxconfig.php';

$qry = $pdo->query("SELECT id, fam_name ,`fam_relationship`, `fam_age`, `fam_live`, `fam_occupation`, `fam_aadhar`, `fam_mobile`FROM  family_info");
if ($qry->rowCount() > 0) {
    $response = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Close Connection

echo json_encode($response);
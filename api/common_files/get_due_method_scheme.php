<?php
require "../../ajaxconfig.php";
$scheme_arr = array();
$schemeDueMethod = $_POST['schemeDueMethod'];
$qry = $pdo->query("SELECT `id`,`scheme_name`, `due_method`, `profit_method`, `interest_rate_percent`, `due_period_percent`, `doc_charge_type`, `doc_charge_min`, `doc_charge_max`, `processing_fee_type`, `processing_fee_min`, `processing_fee_max`,`overdue_penalty_percent` FROM `scheme` WHERE due_method ='$schemeDueMethod' ");
if ($qry->rowCount() > 0) {
    $scheme_arr = $qry->fetchAll(PDO::FETCH_ASSOC);
}
$pdo = null; //Connection Close.
echo json_encode($scheme_arr);

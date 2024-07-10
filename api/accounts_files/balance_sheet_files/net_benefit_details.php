<?php
require "../../../ajaxconfig.php";

$result = array();

$qry = $pdo->query("SELECT COALESCE(SUM(lelc.interest_amnt),0) AS benefit, COALESCE(SUM(lelc.doc_charge_calculate),0) AS doc_charges, COALESCE(SUM(lelc.processing_fees_calculate),0) AS proc_charges FROM `loan_issue` li JOIN loan_entry_loan_calculation lelc ON li.cus_profile_id = lelc.cus_profile_id WHERE DATE(li.issue_date) = CURDATE() AND lelc.due_type ='EMI'"); //Benefit Amount 
if ($qry->rowCount() > 0) {
    $row = $qry->fetch(PDO::FETCH_ASSOC);
    $benefit = $row['benefit'];
    $doc_charges = $row['doc_charges'];
    $proc_charges = $row['proc_charges'];
}

$qry2 = $pdo->query("SELECT COALESCE(SUM(due_amt_track),0) AS due, COALESCE(SUM(princ_amt_track),0) AS princ, COALESCE(SUM(int_amt_track),0) AS intrst, COALESCE(SUM(penalty_track),0) AS penalty, COALESCE(SUM(coll_charge_track),0) AS fine FROM `collection` WHERE DATE(`created_date`) = CURDATE() "); //Collection 
if ($qry2->rowCount() > 0) {
    $row = $qry2->fetch(PDO::FETCH_ASSOC);
    $intrst = $row['intrst'];
    $penalty = $row['penalty'];
    $fine = $row['fine'];
}

$qry3 = $pdo->query("SELECT COALESCE(SUM(amount),0) AS oi_dr FROM `other_transaction` WHERE trans_cat ='8' AND type = '1' AND DATE(`created_on`) = CURDATE() "); //Other Income 
if ($qry3->rowCount() > 0) {
    $oicr = $qry3->fetch(PDO::FETCH_ASSOC)['oi_dr'];
}

$qry4 = $pdo->query("SELECT COALESCE(SUM(amount),0) AS exp_dr FROM `expenses` WHERE DATE(`created_on`) = CURDATE() "); //Expenses 
if ($qry4->rowCount() > 0) {
    $expdr = $qry4->fetch(PDO::FETCH_ASSOC)['exp_dr'];
}

$result[0]['benefit'] = $benefit;
$result[0]['intrst'] = $intrst;
$result[0]['doc_charges'] = $doc_charges;
$result[0]['proc_charges']  = $proc_charges;
$result[0]['penalty'] = $penalty;
$result[0]['fine'] = $fine;
$result[0]['oicr'] = $oicr;

$result[0]['expdr']  = $expdr;

echo json_encode($result);

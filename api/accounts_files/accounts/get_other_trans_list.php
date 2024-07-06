<?php
require "../../../ajaxconfig.php";
@session_start();
$user_id = $_SESSION['user_id'];

$trans_cat = ["1"=>'Deposit', "2"=>'Investment', "3"=>'EL', "4"=>'Exchange', "5"=>'Bank Deposit', "6"=>'Bank Withdrawal', "7"=>'Loan Advance', "8"=>'Other Income'];
$cash_type = ["1"=>'Hand Cash', "2"=>'Bank Cash'];
$crdr = ["1"=>'Credit', "2"=>'Debit'];
$trans_list_arr = array();
$qry = $pdo->query("SELECT a.*, b.name AS transname, d.name as username, e.bank_name as bank_namecash FROM `other_transaction` a JOIN other_trans_name b ON a.name =b.id LEFT JOIN users d ON a.user_name = d.id LEFT JOIN bank_creation e ON a.bank_id = e.id WHERE a.insert_login_id = '$user_id' AND DATE(a.created_on) = CURDATE() ");
if($qry->rowCount()>0){
    while($result = $qry->fetch()){
        $result['coll_mode'] = $cash_type[$result['coll_mode']];
        $result['bank_namecash'] = $result['bank_namecash'];
        $result['trans_cat'] = $trans_cat[$result['trans_cat']];
        $result['name'] = $result['transname'];
        $result['type'] = $crdr[$result['type']];
        $result['username'] = $result['username'];
        $result['action'] = "<span class='icon-trash-2 transDeleteBtn' value='" . $result['id'] . "'></span>";
        $trans_list_arr[] = $result;
    }
}

echo json_encode($trans_list_arr);
?>

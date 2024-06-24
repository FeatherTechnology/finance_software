<?php
require "../../ajaxconfig.php";

$cheque_info_arr = array();
$cus_profile_id = $_POST['cus_profile_id'];
$qry = $pdo->query("SELECT * FROM `cheque_info` WHERE cus_profile_id = '$cus_profile_id' ");
if ($qry->rowCount() > 0) {
    while ($cheque_info = $qry->fetch(PDO::FETCH_ASSOC)) {
        if($cheque_info['holder_type'] =='1'){
            $holder_type = 'Customer';
            
        }else if($cheque_info['holder_type'] =='2'){
            $holder_type = 'Guarantor';
            
        }else if($cheque_info['holder_type'] =='3'){
            $holder_type = 'Family Member';
        }
        $cheque_info['holder_type'] = $holder_type;
        $cheque_info['upload'] = "<a href='uploads/loan_issue/cheque_info/".$cheque_info['upload']."' target='_blank'>".$cheque_info['upload']."</a>";
        $cheque_info['action'] = "<span class='icon-border_color chequeActionBtn' value='" . $cheque_info['id'] . "'></span> <span class='icon-trash-2 chequeDeleteBtn' value='" . $cheque_info['id'] . "'></span>";
        $cheque_info_arr[] = $cheque_info;
    }
}
$pdo = null; //Connection Close.
echo json_encode($cheque_info_arr);
<?php
require "../../ajaxconfig.php";

$endorsement_info_arr = array();
$cp_id = $_POST['cp_id'];
$qry = $pdo->query("SELECT `id`,`gold_type`,`purity`,`weight` FROM `gold_info` WHERE `cus_profile_id` = '$cp_id'");
if ($qry->rowCount() > 0) {
    while ($result = $qry->fetch()) {
        $result['d_noc'] = '';
        $result['h_person'] = '';
        $result['relation'] = '';
        $result['action'] = "<input type='checkbox' class='noc_gold_chkbx' name='noc_gold_chkbx' value='" . $result['id'] . "' >";
        $endorsement_info_arr[] = $result;
    }
}

echo json_encode($endorsement_info_arr);

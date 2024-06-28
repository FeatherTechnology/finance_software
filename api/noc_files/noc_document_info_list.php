<?php
require "../../ajaxconfig.php";

$endorsement_info_arr = array();
$cp_id = $_POST['cp_id'];
$qry = $pdo->query("SELECT di.`id`,di.`doc_name`,di.`doc_type`,fi.`fam_name`,di.`upload` FROM `document_info` di LEFT JOIN `family_info` fi ON di.holder_name = fi.id WHERE di.`cus_profile_id` = '$cp_id'");
if ($qry->rowCount() > 0) {
    while ($result = $qry->fetch()) {
        $result['d_noc'] = '';
        $result['h_person'] = '';
        $result['relation'] = '';
        $result['upload'] = "<a href='uploads/loan_issue/doc_info/" . $result['upload'] . "' target='_blank'>" . $result['upload'] . "</a>";
        $result['action'] = "<input type='checkbox' class='noc_doc_info_chkbx' name='noc_doc_info_chkbx' value='" . $result['id'] . "' >";
        $endorsement_info_arr[] = $result;
    }
}

echo json_encode($endorsement_info_arr);

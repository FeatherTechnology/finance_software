<?php
require "../../../ajaxconfig.php";

$id = $_POST['id'];
if ($id != '0' && $id != '') {
    $qry = $pdo->query("SELECT loan_id FROM loan_entry_loan_calculation WHERE id = '$id'");
    $qry_info = $qry->fetch();
    $loan_ID_final = $qry_info['loan_id'];
} else {

    $qry = $pdo->query("SELECT loan_id FROM loan_entry_loan_calculation WHERE loan_id !='' ORDER BY id DESC ");
    if ($qry->rowCount() > 0) {
        $qry_info = $qry->fetch(); //LD-001
        $usr_code_f = substr($qry_info['loan_id'], 0, 3);
        $usr_code_s = substr($qry_info['loan_id'], 3, 5);
        $final_code = str_pad($usr_code_s + 1, 3, 0, STR_PAD_LEFT);
        $loan_ID_final = $usr_code_f.$final_code;
    } else {
        $loan_ID_final = "LD-" . "001";
    }
}
echo json_encode($loan_ID_final);
?>

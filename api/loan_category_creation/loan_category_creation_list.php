<?php
require '../../ajaxconfig.php';

$loanCatCreation_list_arr = array();
$qry = $pdo->query("SELECT lcc.id, lc.loan_category, lcc.loan_limit FROM loan_category_creation lcc LEFT JOIN loan_category lc ON lcc.loan_category = lc.id ");
if ($qry->rowCount() > 0) {
    while ($loanCatCreationInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        $loanCatCreationInfo['action'] = "<span class='icon-border_color loanCatCreationActionBtn' value='" . $loanCatCreationInfo['id'] . "'></span> <span class='icon-trash-2 loanCatCreationDeleteBtn' value='" . $loanCatCreationInfo['id'] . "'></span>";
        $loanCatCreation_list_arr[] = $loanCatCreationInfo; // Append to the array
    }
}

$pdo = null; //Close Connection.

echo json_encode($loanCatCreation_list_arr);

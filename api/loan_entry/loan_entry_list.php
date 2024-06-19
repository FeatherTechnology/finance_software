<?php
require '../../ajaxconfig.php';

$loan_entry_list_arr = array();
$qry = $pdo->query("SELECT cp.id, cp.cus_id, cp.cus_name, lelc.loan_id, lc.loan_category, lelc.loan_amount, anc.areaname AS area, cp.line, 'branch', cp.mobile1 
FROM customer_profile cp 
LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.loan_category
LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
LEFT JOIN area_name_creation anc ON cp.area = anc.id
WHERE 1 ");
if ($qry->rowCount() > 0) {
    while ($loanEntryInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        $loanEntryInfo['action'] = "<div class='dropdown'><button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button><div class='dropdown-content'><a href='#' class='edit-loan-entry' value='" . $loanEntryInfo['id'] . "' title='Edit details'>Edit</a><a href='#' class='move-loan-entry' value='" . $loanEntryInfo['id'] . "' title='Move'>Move</a></div></div>";
        $loan_entry_list_arr[] = $loanEntryInfo; // Append to the array
    }
}
$pdo = null; //Close Connection.
echo json_encode($loan_entry_list_arr);

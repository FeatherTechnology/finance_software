<?php
require '../../ajaxconfig.php';

$loan_entry_list_arr = array();
$qry = $pdo->query("SELECT cp.id, cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, bc.branch_name , lelc.loan_amount, cp.mobile1, lelc.id as loan_calc_id, cs.id as cus_sts_id, cs.status as c_sts 
FROM customer_profile cp 
LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
LEFT JOIN area_name_creation anc ON cp.area = anc.id
LEFT JOIN area_creation ac ON cp.line = ac.line_id
LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
WHERE cs.status = 4 ORDER BY cp.id DESC");
if ($qry->rowCount() > 0) {
    while ($loanEntryInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        $loanEntryInfo['action'] = "<div class='dropdown'>
            <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
            <div class='dropdown-content'>";
            
            $loanEntryInfo['action'] .= "<a href='#' class='edit-loan-issue' value='" . $loanEntryInfo['id'] . "' data-id='" . $loanEntryInfo['loan_calc_id'] . "' title='Edit details'>Edit</a>";

        $loanEntryInfo['action'] .= "</div></div>";

        $loan_entry_list_arr[] = $loanEntryInfo; // Append to the array
    }
}
$pdo = null; //Close Connection.
echo json_encode($loan_entry_list_arr);
<?php
require '../../ajaxconfig.php';

$loan_entry_list_arr = array();
$qry = $pdo->query("SELECT cp.id, cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, bc.branch_name , cp.mobile1, lelc.id as loan_calc_id, cs.id as cus_sts_id, cs.status as c_sts 
FROM customer_profile cp 
LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
LEFT JOIN area_name_creation anc ON cp.area = anc.id
LEFT JOIN area_creation ac ON cp.line = ac.line_id
LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
WHERE  (cs.status = 3 || cs.status = 5) ORDER BY cp.id DESC");

if ($qry->rowCount() > 0) {
    while ($loanEntryInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        $loanEntryInfo['action'] = "<div class='dropdown'>
            <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
            <div class='dropdown-content'>";
            
        if ($loanEntryInfo['c_sts'] == '3') {
            $loanEntryInfo['action'] .= "<a href='#' class='approval-edit' value='" . $loanEntryInfo['id'] . "' data-id='" . $loanEntryInfo['loan_calc_id']. "' title='Edit details'>Edit</a>";
            $loanEntryInfo['action'] .= "<a href='#' class='approval-approve' value='" . $loanEntryInfo['cus_sts_id'] . "' title='Approve'>Approve</a>";
            $loanEntryInfo['action'] .= "<a href='#' class='approval-cancel' value='" . $loanEntryInfo['cus_sts_id'] . "' title='Cancel'>Cancel</a>";
            $loanEntryInfo['action'] .= "<a href='#' class='approval-revoke' value='" . $loanEntryInfo['cus_sts_id'] . "' title='Revoke'>Revoke</a>";
            
        }
       
        $loanEntryInfo['action'] .= "</div></div>";

        $loan_entry_list_arr[] = $loanEntryInfo; // Append to the array
    }
}
$pdo = null; // Close Connection.
echo json_encode($loan_entry_list_arr);
?>


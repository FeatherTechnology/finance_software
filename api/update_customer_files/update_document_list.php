<?php
require '../../ajaxconfig.php';
$status = [3 => 'Move',4 => 'Approved', 5 => 'Cancel', 6 => 'Revoke', 7 => 'Loan Issued', 8 => 'Closed',9=>'Closed',10=>'NOC'];
$sub_status = [''=>'',1 => 'Consider', 2 => 'Reject'];
$update_doc_list_arr = array();
$cus_id = $_POST['cus_id'];
$qry = $pdo->query("SELECT lelc.id, lelc.loan_id, lc.loan_category, lelc.loan_date,lelc.loan_amount,cs.closed_date,cs.status as c_sts,cs.sub_status as substatus FROM loan_entry_loan_calculation lelc 
LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id 
LEFT JOIN loan_category lc ON lcc.loan_category = lc.id 
LEFT JOIN customer_status cs ON lelc.id = cs.loan_calculation_id 
WHERE cs.cus_id = '$cus_id'");
if ($qry->rowCount() > 0) {
    while ($updateDocInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        $loanDate = new DateTime($updateDocInfo['loan_date']);
        $loanInfo['loan_date'] = $loanDate->format('d-m-Y');
        $closedDate = new DateTime($updateDocInfo['closed_date']);
        $loanInfo['closed_date'] = $closedDate->format('d-m-Y');
        $updateDocInfo['c_sts'] = $status[$updateDocInfo['c_sts']];
        $updateDocInfo['substatus'] = $sub_status[$updateDocInfo['substatus']];
        $updateDocInfo['action'] = "<div class='dropdown'>
            <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
            <div class='dropdown-content'>";

            $updateDocInfo['action'] .= "<a href='#' class='doc-update' value='" . $updateDocInfo['id'] . "' title='update details'>Update</a>";
            $updateDocInfo['action'] .= "<a href='#' class='doc-print' value='" . $updateDocInfo['id'] . "' title='print'>Print</a>";
            
        
        $updateDocInfo['action'] .= "</div></div>";

        $update_doc_list_arr[] = $updateDocInfo; // Append to the array
    }
}
$pdo = null; //Close Connection.
echo json_encode($update_doc_list_arr);


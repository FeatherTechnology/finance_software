<?php
require '../../ajaxconfig.php';
$cus_id = $_POST['cus_id'];
$loan_list_arr = array();
$qry = $pdo->query("SELECT lelc.cus_id, lelc.loan_id, lc.loan_category, lelc.loan_date, lelc.loan_amount, cs.status, cs.sub_status
FROM loan_entry_loan_calculation lelc
JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
JOIN loan_category lc ON lcc.loan_category = lc.id
JOIN customer_status cs ON lelc.id = cs.loan_calculation_id
WHERE lelc.cus_id = '$cus_id' ORDER BY lelc.id DESC");
if ($qry->rowCount() > 0) {
    while ($loanInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        $loanInfo['charts'] = "<div class='dropdown'><button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button><div class='dropdown-content'><a href='#' class='due-chart' value='" . $loanInfo['cus_id'] . "'>Due Chart</a><a href='#' class='penalty-chart' value='" . $loanInfo['cus_id'] . "'>Penalty Chart</a><a href='#' class='fine-chart' value='" . $loanInfo['cus_id'] . "'>Fine Chart</a></div></div>";
        $loanInfo['action'] = "<div class='dropdown'><button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i><div class='dropdown-content'><a href='#' class='pay-due' value='" . $loanInfo['cus_id'] . "'>Pay Due</a><a href='#' class='fine-form' value='" . $loanInfo['cus_id'] . "'>Fine</a></div></div>";
        $loan_list_arr[] = $loanInfo; // Append to the array
    }
}
$pdo = null; //Close Connection.
echo json_encode($loan_list_arr);

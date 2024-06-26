<?php
require '../../ajaxconfig.php';

$cus_id=$_POST['cus_id'];
$loan_list_arr = array();
$status = [2 => 'Loan Calculation', 3 => 'Move', 4 => 'Approved', 5 => 'Cancel', 6 => 'Revoke', 7 => 'Loan Issued', 8 => 'Closed', 9 => 'Closed'];
$sub_status = ['' => '', 1 => 'Consider', 2 => 'Reject'];

$qry = $pdo->query("SELECT lelc.cus_profile_id,lelc.cus_id,lelc.loan_date, lelc.loan_id, lc.loan_category,lelc.loan_amount, cs.status, cs.sub_status
FROM loan_entry_loan_calculation lelc
JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
JOIN loan_category lc ON lcc.loan_category = lc.id
JOIN customer_status cs ON lelc.id = cs.loan_calculation_id
WHERE lelc.cus_id = '$cus_id' ORDER BY lelc.id DESC");
if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        $response = array();
        $loanDate = new DateTime($row['loan_date']);


        $response['loan_date'] = $loanDate->format('d-m-Y');
        $response['loan_id'] = $row['loan_id'];
        $response['loan_category'] = $row['loan_category'];
        $response['loan_amount'] = $row['loan_amount'];
        $response['status'] = $status[$row['status']];
        $response['sub_status'] = $sub_status[$row['sub_status']];
        $response['info'] = "<div class='dropdown'>
            <button class='btn btn-outline-secondary'>
                <i class='fa'>&#xf107;</i>
            </button>
            <div class='dropdown-content'>
                <a href='#' class='customer-profile' value='" . $row['cus_id'] . "'>Customer Profile</a>
                <a href='#' class='loan-calculation' value='" . $row['cus_id'] . "'>Loan Calculation</a>
                <a href='#' class='documentation' value='" . $row['cus_id'] . "'>Documentation</a>
                <a href='#' class='closed-remark' value='" . $row['cus_profile_id'] . "'>Remark View</a>
                <a href='#' class='noc-summary' value='" . $row['cus_id'] . "'>Noc Summary</a>
            </div>
        </div>";

        $response['charts'] = "<div class='dropdown'>
            <button class='btn btn-outline-secondary'>
                <i class='fa'>&#xf107;</i>
            </button>
            <div class='dropdown-content'>
                <a href='#' class='due-chart' value='" . $row['cus_id'] . "'>Due Chart</a>
                <a href='#' class='penalty-chart' value='" . $row['cus_id'] . "'>Penalty Chart</a>
                <a href='#' class='fine-chart' value='" . $row['cus_id'] . "'>Fine Chart</a>
            </div>
        </div>";
        $loan_list_arr[] = $response;
    }
}
$pdo = null; //Close Connection.
echo json_encode($loan_list_arr);

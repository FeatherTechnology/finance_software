<?php
require '../../ajaxconfig.php';

$cus_id = isset($_POST['cus_id']) ? $_POST['cus_id'] : null;
$cus_profile_id = isset($_POST['cus_profile_id']) ? $_POST['cus_profile_id'] : null;
$loan_list_arr = array();
$status = [
    1 => 'Loan Entry', 2 => 'Loan Entry', 3 => 'Loan Approval',
    4 => 'In Loan Issue', 5 => 'Loan Approval', 6 => 'Loan Approval',
    7 => 'Present', 8 => 'Closed', 9 => 'Closed', 10 => 'In NOC'
];

if ($cus_id) {
    $qry = $pdo->query("SELECT lelc.cus_profile_id, lelc.cus_id, lelc.loan_date, lelc.loan_id, lc.loan_category, lelc.loan_amount, cs.status, cs.sub_status
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
            $response['sub_status'] = loanCustomerStatus($pdo, $row['cus_profile_id']); 

            $response['info'] = "<div class='dropdown'>
                <button class='btn btn-outline-secondary'>
                    <i class='fa'>&#xf107;</i>
                </button>
                <div class='dropdown-content'>
                    <a href='#' class='customer-profile' value='" . $row['cus_profile_id'] . "'>Customer Profile</a>
                    <a href='#' class='loan-calculation' value='" . $row['cus_profile_id'] . "'>Loan Calculation</a>
                    <a href='#' class='documentation' value='" . $row['cus_profile_id'] . "'>Documentation</a>
                    <a href='#' class='closed-remark' value='" . $row['cus_profile_id'] . "'>Remark View</a>
                    <a href='#' class='noc-summary' value='" . $row['cus_profile_id'] . "'>Noc Summary</a>
                </div>
            </div>";

            $response['charts'] = "<div class='dropdown'>
                <button class='btn btn-outline-secondary'>
                    <i class='fa'>&#xf107;</i>
                </button>
                <div class='dropdown-content'>
                    <a href='#' class='due-chart' value='" . $row['cus_profile_id'] . "'>Due Chart</a>
                    <a href='#' class='penalty-chart' value='" . $row['cus_profile_id'] . "'>Penalty Chart</a>
                    <a href='#' class='fine-chart' value='" . $row['cus_profile_id'] . "'>Fine Chart</a>
                </div>
            </div>";

            $loan_list_arr[] = $response;
        }
    }
}

$pdo = null; // Close Connection
echo json_encode($loan_list_arr);

function loanCustomerStatus($pdo, $cus_profile_id)
{
    $qry1 = $pdo->query("SELECT lelc.loan_date, cs.status as cs_status, cs.sub_status as sub_sts
    FROM loan_entry_loan_calculation lelc
    JOIN customer_status cs ON lelc.id = cs.loan_calculation_id
    WHERE lelc.cus_profile_id = '$cus_profile_id' ORDER BY lelc.id DESC");

    if ($qry1->rowCount() > 0) {
        $row1 = $qry1->fetch(PDO::FETCH_ASSOC);
        $cs_status = $row1['cs_status'];
        $sub_sts = $row1['sub_sts'];
        $loan_date = $row1['loan_date'];
        $pending_sts = isset($_POST["pending_sts"]) ? explode(',', $_POST["pending_sts"]) : [];
        $od_sts = isset($_POST["od_sts"]) ? explode(',', $_POST["od_sts"]) : [];
        $due_nil_sts = isset($_POST["due_nil_sts"]) ? explode(',', $_POST["due_nil_sts"]) : [];
        $bal_amt = isset($_POST["bal_amt"]) ? explode(',', $_POST["bal_amt"]) : [];
        $i = 1;

        if ($cs_status == '1' || $cs_status == '2') {
            return 'Loan Entry';
        } elseif ($cs_status == '3') {
            return 'In Approval';
        } elseif ($cs_status == '4') {
            return 'In Loan Issue';
        } elseif ($cs_status == '5') {
            return 'Cancel';
        } elseif ($cs_status == '6') {
            return 'Revoke';
        } elseif ($cs_status == '7') {
            $curdate = date('Y-m-d');
            if (date('Y-m-d', strtotime($loan_date)) > date('Y-m-d', strtotime($curdate)) && isset($bal_amt[$i - 1]) && $bal_amt[$i - 1] != 0) {
                return 'Current';
            } else {
                if (isset($pending_sts[$i - 1]) && $pending_sts[$i - 1] == 'true' && isset($od_sts[$i - 1]) && $od_sts[$i - 1] == 'false') {
                    return 'Pending';
                } elseif (isset($od_sts[$i - 1]) && $od_sts[$i - 1] == 'true' && isset($due_nil_sts[$i - 1]) && $due_nil_sts[$i - 1] == 'false') {
                    return 'OD';
                } elseif (isset($due_nil_sts[$i - 1]) && $due_nil_sts[$i - 1] == 'true') {
                    return 'Due Nil';
                } elseif (isset($pending_sts[$i - 1]) && $pending_sts[$i - 1] == 'false') {
                    return 'Current';
                }
            }
        } elseif ($cs_status == '8') {
            return 'In Closed';
        } elseif ($cs_status == '9') {
            if ($sub_sts == '1') {
                return 'Consider';
            } elseif ($sub_sts == '2') {
                return 'Rejected';
            }
        } elseif ($cs_status == '10') {
            return 'Pending';
        } elseif ($cs_status == '11') {
            return 'Completed';
        }
    }

    return ''; // Default return value if no conditions match
}
?>

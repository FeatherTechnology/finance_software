<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];
// $loan_issue_list_arr = array();
// $qry = $pdo->query("SELECT cp.id, cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, bc.branch_name , lelc.loan_amount, cp.mobile1, lelc.id as loan_calc_id, cs.id as cus_sts_id, cs.status as c_sts 
// FROM customer_profile cp 
// LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
// LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
// LEFT JOIN area_name_creation anc ON cp.area = anc.id
// LEFT JOIN area_creation ac ON cp.line = ac.line_id
// LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
// LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
// JOIN users u ON FIND_IN_SET(cp.line, u.line)
// JOIN users us ON FIND_IN_SET(lelc.loan_category, us.loan_category)
// WHERE cs.status = 4 AND u.id ='$user_id' AND us.id ='$user_id' ORDER BY cp.id DESC");
// if ($qry->rowCount() > 0) {
//     while ($loanIssueInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
//         $loanIssueInfo['action'] = "<div class='dropdown'>
//             <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
//             <div class='dropdown-content'>";

//             $loanIssueInfo['action'] .= "<a href='#' class='edit-loan-issue' value='" . $loanIssueInfo['id'] . "' data-id='" . $loanIssueInfo['cus_id'] . "' title='Edit details'>Edit</a>";

//         $loanIssueInfo['action'] .= "</div></div>";

//         $loan_issue_list_arr[] = $loanIssueInfo; // Append to the array
//     }
// }
// $pdo = null; //Close Connection.
// echo json_encode($loan_issue_list_arr);
$column = array(
    'cp.id',
    'lelc.loan_date',
    'cp.cus_id',
    'cp.cus_name',
    'anc.areaname',
    'lnc.linename',
    'bc.branch_name',
    'cp.mobile1',
    'lc.loan_category',
    'lelc.loan_amount',
    'cp.cus_data',
    'cp.id'
);
$query = "SELECT cp.id, lelc.loan_date, cp.cus_id, cp.cus_name, anc.areaname, lnc.linename, bc.branch_name , cp.mobile1, lc.loan_category, lelc.loan_amount, cp.cus_data, lelc.id as loan_calc_id, cs.id as cus_sts_id, cs.status as c_sts 
FROM customer_profile cp 
LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
LEFT JOIN loan_category_creation lcc ON lelc.loan_category = lcc.id
LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
LEFT JOIN loan_category lc ON lcc.loan_category = lc.id
LEFT JOIN area_name_creation anc ON cp.area = anc.id
LEFT JOIN area_creation ac ON cp.line = ac.line_id
LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
JOIN users u ON FIND_IN_SET(cp.line, u.line)
JOIN users us ON FIND_IN_SET(lelc.loan_category, us.loan_category)
WHERE cs.status = 4 AND u.id ='$user_id' AND us.id ='$user_id'";
if (isset($_POST['search'])) {
    if ($_POST['search'] != "") {
        $search = $_POST['search'];
        $query .= " AND (lelc.loan_date LIKE '" . $search . "%'
                      OR cp.cus_id LIKE '%" . $search . "%'
                      OR cp.cus_name LIKE '%" . $search . "%'
                      OR anc.areaname LIKE '%" . $search . "%'
                      OR lnc.linename LIKE '%" . $search . "%'
                      OR bc.branch_name LIKE '%" . $search . "%'
                      OR cp.mobile1 LIKE '%" . $search . "%'
                      OR lc.loan_category LIKE '%" . $search . "%'
                      OR lelc.loan_amount LIKE '%" . $search . "%'
                      OR cp.cus_data LIKE '%" . $search . "%')";
    }
}

if (isset($_POST['order'])) {
    $query .= " ORDER BY " . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'];
} else {
    $query .= ' ';
}
$query1 = '';
if (isset($_POST['length']) && $_POST['length'] != -1) {
    $query1 = ' LIMIT ' . intval($_POST['start']) . ', ' . intval($_POST['length']);
}
$statement = $pdo->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $pdo->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();
$sno = isset($_POST['start']) ? $_POST['start'] + 1 : 1;
$data = [];
foreach ($result as $row) {
    $sub_array = array();

    $sub_array[] = $sno++;
    $sub_array[] = isset($row['loan_date']) && !empty($row['loan_date']) ? date('d-m-Y', strtotime($row['loan_date'])) : '';
    $sub_array[] = isset($row['cus_id']) ? $row['cus_id'] : '';
    $sub_array[] = isset($row['cus_name']) ? $row['cus_name'] : '';
    $sub_array[] = isset($row['areaname']) ? $row['areaname'] : '';
    $sub_array[] = isset($row['linename']) ? $row['linename'] : '';
    $sub_array[] = isset($row['branch_name']) ? $row['branch_name'] : '';
    $sub_array[] = isset($row['mobile1']) ? $row['mobile1'] : '';
    $sub_array[] = isset($row['loan_category']) ? $row['loan_category'] : '';
    $sub_array[] = isset($row['loan_amount']) ? $row['loan_amount'] : '';
    $sub_array[] = isset($row['cus_data']) ? $row['cus_data'] : '';
    $action = "<div class='dropdown'>
    <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
    <div class='dropdown-content'>";
    $action .= "<a href='#' class='edit-loan-issue' value='" . $row['id'] . "' data-id='" . $row['cus_id'] . "' title='Edit details'>Edit</a>";
    $action .= "<a href='#' class='loan-issue-cancel' value='" . $row['cus_sts_id'] . "' title='Cancel'>Cancel</a>";
    $action .= "<a href='#' class='loan-issue-revoke' value='" . $row['cus_sts_id'] . "' title='Revoke'>Revoke</a>";
    $action .= "</div></div>";
    $sub_array[] = $action;
    $data[] = $sub_array;
}
function count_all_data($pdo)
{
    $query = "SELECT COUNT(*) FROM customer_profile";
    $statement = $pdo->prepare($query);
    $statement->execute();
    return $statement->fetchColumn();
}
$output = array(
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    'recordsTotal' => count_all_data($pdo),
    'recordsFiltered' => $number_filter_row,
    'data' => $data
);

echo json_encode($output);
//Format number in Indian Format
function moneyFormatIndia($num1)
{
    if ($num1 < 0) {
        $num = str_replace("-", "", $num1);
    } else {
        $num = $num1;
    }
    $explrestunits = "";
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3, strlen($num));
        $restunits = substr($num, 0, strlen($num) - 3);
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
        $expunit = str_split($restunits, 2);
        for ($i = 0; $i < sizeof($expunit); $i++) {
            if ($i == 0) {
                $explrestunits .= (int)$expunit[$i] . ",";
            } else {
                $explrestunits .= $expunit[$i] . ",";
            }
        }
        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $num;
    }

    if ($num1 < 0 && $num1 != '') {
        $thecash = "-" . $thecash;
    }

    return $thecash;
}

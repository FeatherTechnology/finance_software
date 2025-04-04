<?php
require '../../ajaxconfig.php';

$status = [
    8 => 'Closed', 9 => 'Closed', 10 => 'NOC', 11 => 'NOC'
];

$sub_status = [
    1 => 'Consider',
    2 => 'Reject'
];

$existing_promo_arr = array();
$i = 0;

$existing_details = isset($_POST['existing_details']) ? $_POST['existing_details'] : '';

$qry = $pdo->query("SELECT cus_id FROM customer_status GROUP BY cus_id HAVING COUNT(*) = COUNT(CASE WHEN status >= 9 THEN 1 END);");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        $cus_id = $row['cus_id'];
        // Query for customer details
        $whereCondition = "cp.cus_id='$cus_id'";
        if (!empty($existing_details)) {
            $existing_details_str = "'" . implode("','", $existing_details) . "'";
    
            $whereCondition .= " AND ec.existing_detail IN ($existing_details_str)";
        }
        $customerQry = $pdo->query("SELECT 
                cp.id, cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, 
                bc.branch_name, cp.mobile1, cs.status as c_sts, cs.sub_status as c_substs,ec.created_on as created,ec.existing_detail,cp.created_on as cus_created
                FROM customer_profile cp 
                LEFT JOIN line_name_creation lnc ON cp.line = lnc.id 
                LEFT JOIN area_name_creation anc ON cp.area = anc.id 
                LEFT JOIN area_creation ac ON cp.line = ac.line_id 
                LEFT JOIN branch_creation bc ON ac.branch_id = bc.id 
                LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id  
                LEFT JOIN existing_customer ec ON cp.cus_id = ec.cus_id
                WHERE $whereCondition
                ORDER BY cp.id DESC LIMIT 1");

        if ($customerQry->rowCount() > 0) {
            $customerRow = $customerQry->fetch(PDO::FETCH_ASSOC);
            $customerRow['c_sts'] = isset($status[$customerRow['c_sts']]) ? $status[$customerRow['c_sts']] : '';
            $loanCustomerStatus = loanCustomerStatus($pdo, $cus_id);
            $customerRow['c_substs'] = $loanCustomerStatus;
            if ($customerRow['existing_detail'] !='') {
                $customerRow['action'] = $customerRow['existing_detail'];
            } else {
                $customerRow['action'] = "<div class='dropdown'><button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button><div class='dropdown-content'><a href='#' class='exs_needed' value='" . $customerRow['cus_id'] . "' data='Needed'>Needed</a><a href='#' class='exs_later' value='" . $customerRow['cus_id'] . "' data='Later'>Later</a><a href='#' class='exs_to_follow' value='" . $customerRow['cus_id'] . "' data='To Follow'>To Follow</a></div></div>";
            }
            $existing_promo_arr[$i] = $customerRow;
            $i++;
        }
    }



}

echo json_encode($existing_promo_arr);
$pdo = null; // Close Connection

function loanCustomerStatus($pdo, $cus_id)
{
    $qry1 = $pdo->query("SELECT 
        cs.status as cs_status, cs.sub_status as sub_sts
        FROM loan_entry_loan_calculation lelc
        JOIN customer_status cs ON lelc.id = cs.loan_calculation_id
        WHERE lelc.cus_id = '$cus_id' ORDER BY lelc.id DESC LIMIT 1");

    if ($qry1->rowCount() > 0) {
        $row1 = $qry1->fetch(PDO::FETCH_ASSOC);
        $cs_status = $row1['cs_status'];
        $sub_sts = $row1['sub_sts'];
         if ($cs_status == '9') {
            if ($sub_sts == '1') {
                $status = 'Consider';
            } elseif ($sub_sts == '2') {
                $status = 'Rejected';
            }
        } elseif ($cs_status == '10') {
            $status = 'Pending';
        } elseif ($cs_status == '11') {
            $status = 'Completed';
        } else {
            $status = ''; // Handle any other cases if necessary
        }

        return $status;
    }

    return ''; // Default return value if no conditions match
}

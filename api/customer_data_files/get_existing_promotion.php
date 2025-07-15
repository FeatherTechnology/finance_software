<?php
require '../../ajaxconfig.php';

$status = [
    8 => 'Closed',
    9 => 'Closed',
    10 => 'NOC',
    11 => 'NOC',
    12 => 'NOC Completed'
];

$sub_status = [
    1 => 'Consider',
    2 => 'Reject'
];

$existing_promo_arr = [];
$whereCondition = "";

// Build where condition for existing_details
if (isset($_POST['existing_details']) && !empty($_POST['existing_details'])) {
    $existing_details_str = implode("','", array_map('htmlspecialchars', $_POST['existing_details'])); // Prevent XSS
    $whereCondition = "AND replace(ec.existing_detail,' ','') IN ('$existing_details_str')";
}

// Prepare the query to fetch customers with status >= 9
$query = "SELECT  cp.id, cp.cus_id, cp.aadhar_num, cp.cus_name, anc.areaname AS area, lnc.linename, 
        bc.branch_name, cp.mobile1, cs.status as c_sts, cs.sub_status as c_substs,
        ec.created_on as created, ec.existing_detail, cp.created_on as cus_created
    FROM customer_profile cp
    LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
    LEFT JOIN area_name_creation anc ON cp.area = anc.id
    LEFT JOIN area_creation ac ON cp.line = ac.line_id
    LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
    INNER JOIN (SELECT MAX(id) as max_id FROM customer_profile GROUP BY cus_id) latest ON cp.id = latest.max_id
    LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
    LEFT JOIN existing_customer ec ON cp.id = ec.cus_profile_id
    WHERE cs.status >= 9 AND cs.status NOT IN (13, 14) $whereCondition
    GROUP BY cp.cus_id
    ORDER BY cp.id DESC";

$customerQry = $pdo->query($query);

if ($customerQry->rowCount() > 0) {
    while ($customerRow = $customerQry->fetch(PDO::FETCH_ASSOC)) {
        // Map customer status
        $customerRow['c_sts'] = isset($status[$customerRow['c_sts']]) ? $status[$customerRow['c_sts']] : '';

        // Fetch loan status
        $loanCustomerStatus = loanCustomerStatus($pdo, $customerRow['cus_id']);
        $customerRow['c_substs'] = $loanCustomerStatus;

        // Handle the 'existing_detail' field
        if ($customerRow['existing_detail'] != '') {
            $customerRow['action'] = $customerRow['existing_detail'];
        } else {
            $customerRow['action'] = "<div class='dropdown'><button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button><div class='dropdown-content'><a href='#' class='exs_needed' value='" . $customerRow['cus_id'] . "' data='Needed'>Needed</a><a href='#' class='exs_later' value='" . $customerRow['cus_id'] . "' data='Later'>Later</a><a href='#' class='exs_to_follow' value='" . $customerRow['cus_id'] . "' data='To Follow'>To Follow</a></div></div>";
        }

        // Add the customer to the array
        $existing_promo_arr[] = $customerRow;  // Cleaner way of adding to array
    }
}

echo json_encode($existing_promo_arr);
// Function to fetch loan status of a customer
function loanCustomerStatus($pdo, $cus_id)
{
    $stmt = $pdo->prepare("SELECT cs.status as cs_status, cs.sub_status as sub_sts
        FROM loan_entry_loan_calculation lelc
        JOIN customer_status cs ON lelc.id = cs.loan_calculation_id
        WHERE lelc.cus_id = :cus_id ORDER BY lelc.id DESC LIMIT 1");
    
    $stmt->execute(['cus_id' => $cus_id]);
    $row1 = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row1) {
        $cs_status = $row1['cs_status'];
        $sub_sts = $row1['sub_sts'];
        if ($cs_status == '9') {
            return ($sub_sts == '1') ? 'Consider' : 'Rejected';
        } elseif ($cs_status == '10') {
            return 'Pending';
        } elseif ($cs_status == '11') {
            return 'Completed';
        }else if($cs_status == '12'){
            return ($sub_sts == '1') ? 'Consider' : 'Rejected';
        }
    }

    return ''; // Default return value if no conditions match
}
$pdo = null; // Close Connection
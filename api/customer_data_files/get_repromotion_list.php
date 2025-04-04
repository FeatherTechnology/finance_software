<?php
require '../../ajaxconfig.php';

$status = [
    5 => 'Cancel',
    6 => 'Revoke'
];

$re_promotion_arr = array();
$i = 0;
$qry = $pdo->query("SELECT cus_id FROM customer_status WHERE status = 5 || status = 6 || status = 13 || status = 14 GROUP BY cus_id ");
$repromotion_details = isset($_POST['repromotion_details']) ? $_POST['repromotion_details'] : '';
if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        $cus_id = $row['cus_id'];
        // Query for customer details
        $whereCondition = "cp.cus_id='$cus_id'";
        if (!empty($repromotion_details)) {
            $repromotion_details_str = "'" . implode("','", $repromotion_details) . "'";
    
            $whereCondition .= " AND rc.repromotion_detail	 IN ($repromotion_details_str)";
        }
        $customerQry = $pdo->query("SELECT 
                cp.id, cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, 
                bc.branch_name, cp.mobile1, cs.status as c_sts, cs.sub_status as c_substs, rc.repromotion_detail, rc.created_on as created,cp.created_on as cus_created
                FROM customer_profile cp 
                LEFT JOIN line_name_creation lnc ON cp.line = lnc.id 
                LEFT JOIN area_name_creation anc ON cp.area = anc.id 
                LEFT JOIN area_creation ac ON cp.line = ac.line_id 
                LEFT JOIN branch_creation bc ON ac.branch_id = bc.id 
                LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id  
                LEFT JOIN repromotion_customer rc ON cp.cus_id = rc.cus_id
                WHERE $whereCondition
                ORDER BY cp.id DESC LIMIT 1");

        if ($customerQry->rowCount() > 0) {
            $customerRow = $customerQry->fetch(PDO::FETCH_ASSOC);
            $customerRow['c_sts'] = isset($status[$customerRow['c_sts']]) ? $status[$customerRow['c_sts']] : '';
            if ($customerRow['repromotion_detail'] !='') {
                $customerRow['action'] = $customerRow['repromotion_detail'];
            } else {
                $customerRow['action'] = "<div class='dropdown'><button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button><div class='dropdown-content'><a href='#' class='needed' value='" . $customerRow['cus_id'] . "' data='Needed'>Needed</a><a href='#' class='later' value='" . $customerRow['cus_id'] . "' data='Later'>Later</a><a href='#' class='to_follow' value='" . $customerRow['cus_id'] . "' data='To Follow'>To Follow</a></div></div>";
            }
            $re_promotion_arr[$i] = $customerRow;
            $i++;
        }
    }
}

echo json_encode($re_promotion_arr);
$pdo = null; // Close Connection
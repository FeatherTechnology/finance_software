<?php
require '../../ajaxconfig.php';

$status = [
    5 => 'Cancel',
    6 => 'Revoke'
];

$re_promotion_arr = array();
$i = 0;
$qry = $pdo->query("SELECT cus_id FROM customer_status WHERE status = 5 || status = 6 GROUP BY cus_id ");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
        $cus_id = $row['cus_id'];
        // Query for customer details
        $customerQry = $pdo->query("SELECT 
                cp.id, cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, 
                bc.branch_name, cp.mobile1, cs.status as c_sts, cs.sub_status as c_substs,rc.created_on as created,cp.created_on as cus_created
                FROM customer_profile cp 
                LEFT JOIN line_name_creation lnc ON cp.line = lnc.id 
                LEFT JOIN area_name_creation anc ON cp.area = anc.id 
                LEFT JOIN area_creation ac ON cp.line = ac.line_id 
                LEFT JOIN branch_creation bc ON ac.branch_id = bc.id 
                LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id  
                LEFT JOIN repromotion_customer rc ON cp.cus_id = rc.cus_id
                WHERE cp.cus_id='$cus_id' 
                ORDER BY cp.id DESC LIMIT 1");

        if ($customerQry->rowCount() > 0) {
            $customerRow = $customerQry->fetch(PDO::FETCH_ASSOC);
            $customerRow['c_sts'] = isset($status[$customerRow['c_sts']]) ? $status[$customerRow['c_sts']] : '';
            if ($customerRow['created'] > $customerRow['cus_created']) {
                $customerRow['action'] = '<span>Needed</span>';
            } else {
                $customerRow['action'] = "<input type='checkbox' class='select-checkbox repromotionBtn' value='" . $customerRow['cus_id'] . "'>";
            }
            $re_promotion_arr[$i] = $customerRow;
            $i++;
        }
    }
}

echo json_encode($re_promotion_arr);
$pdo = null; // Close Connection
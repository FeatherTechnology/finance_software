<?php
require '../../ajaxconfig.php';

$status = [4 => 'Approved',5 => 'Cancel',6 => 'Revoke',7 => 'Loan Issued',8 => 'Closed'];
$sub_status = [1=>'Consider',2=>'Reject'];
$existing_promo_arr = array();
$i=0;
$qry = $pdo->query("SELECT cp.id, cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, bc.branch_name, cp.mobile1, cs.status as c_sts ,cs.sub_status as c_substs FROM customer_profile cp 
LEFT JOIN line_name_creation lnc ON cp.line = lnc.id 
LEFT JOIN area_name_creation anc ON cp.area = anc.id 
LEFT JOIN area_creation ac ON cp.line = ac.line_id 
LEFT JOIN branch_creation bc ON ac.branch_id = bc.id LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id 
INNER JOIN (SELECT MAX(id) as max_id FROM customer_profile GROUP BY cus_id) latest ON cp.id = latest.max_id 
WHERE cs.status = '9' AND cs.sub_status = '1' ORDER BY cp.id DESC");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
    $row['c_sts'] = $status[$row['c_sts']];
    $row['c_substs'] = $sub_status[$row['c_substs']];
    $row['action'] = "<input type='checkbox' class='select-checkbox existingNeedBtn' value='" . $row['id'] . "'>";

        $existing_promo_arr[$i] = $row; // Append to the array
        $i++;
    }
}

echo json_encode($existing_promo_arr);
$pdo = null; // Close Connection
?>
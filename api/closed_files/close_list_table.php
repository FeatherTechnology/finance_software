<?php
require '../../ajaxconfig.php';

$closed_list_arr = array();
$qry = $pdo->query("SELECT cp.id, cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, bc.branch_name, cp.mobile1 FROM customer_profile cp 
LEFT JOIN line_name_creation lnc ON cp.line = lnc.id 
LEFT JOIN area_name_creation anc ON cp.area = anc.id 
LEFT JOIN area_creation ac ON cp.line = ac.line_id 
LEFT JOIN branch_creation bc ON ac.branch_id = bc.id LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id 
WHERE cs.status >= '8' GROUP BY cp.cus_id ORDER BY cp.id DESC");
if ($qry->rowCount() > 0) {
    while ($closedInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        $closedInfo['action'] = "<button class='closed-details btn btn-primary' value='" . $closedInfo['cus_id'] . "'>Close</button>";
        $closed_list_arr[] = $closedInfo; // Append to the array
    }
}
$pdo = null; //Close Connection.
echo json_encode($closed_list_arr);

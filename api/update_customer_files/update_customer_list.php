<?php
require '../../ajaxconfig.php';

$update_cus_list_arr = array();
$subQuery = "SELECT MAX(id) as max_id FROM customer_profile GROUP BY cus_id";

$qry = $pdo->query("SELECT cp.id, cp.cus_id, cp.cus_name,anc.areaname AS area, lnc.linename, bc.branch_name , cp.mobile1, cs.id as cus_sts_id, cs.status as c_sts 
FROM customer_profile cp 
LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
LEFT JOIN area_name_creation anc ON cp.area = anc.id
LEFT JOIN area_creation ac ON cp.line = ac.line_id
LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
INNER JOIN ($subQuery) latest ON cp.id = latest.max_id
ORDER BY cp.id DESC");
if ($qry->rowCount() > 0) {
    while ($updateCusInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        $updateCusInfo ['action'] = "<div class='dropdown'>
            <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
            <div class='dropdown-content'>";
            
        // if ($updateCusInfo ['c_sts'] == '1' || $updateCusInfo ['c_sts'] == '2') {
            $updateCusInfo ['action'] .= "<a href='#' class='edit-cus-update' value='" . $updateCusInfo ['id'] . "' title='Edit details'>Edit</a>";
        // }

        $updateCusInfo ['action'] .= "</div></div>";

        $update_cus_list_arr[] = $updateCusInfo ; // Append to the array
    }
}
$pdo = null; //Close Connection.
echo json_encode($update_cus_list_arr);

<?php
require '../../ajaxconfig.php';

$closed_list_arr = array();
$qry = $pdo->query("SELECT cp.id,cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, bc.branch_name, cp.mobile1,cs.status FROM customer_profile cp 
LEFT JOIN line_name_creation lnc ON cp.line = lnc.id 
LEFT JOIN area_name_creation anc ON cp.area = anc.id 
LEFT JOIN area_creation ac ON cp.line = ac.line_id 
LEFT JOIN branch_creation bc ON ac.branch_id = bc.id 
LEFT JOIN customer_status cs ON cp.cus_id = cs.cus_id 
 INNER JOIN (SELECT MAX(id) as max_id FROM customer_profile GROUP BY cus_id) latest ON cp.id = latest.max_id 
WHERE cs.status >= '8' GROUP BY cp.cus_id ORDER BY cp.id DESC");
if ($qry->rowCount() > 0) {
    while ($closedInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        $closedInfo['action'] = "<div class='dropdown'>
        <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
        <div class='dropdown-content'>";

        $closedInfo['action'] .= "<a href='#' class='closed-details' value='" . $closedInfo['cus_id'] . "' title='Close'>Close</a>";
        $qry2 = $pdo->prepare("SELECT cus_id  FROM customer_status WHERE cus_id = ? AND status = 9");
        $qry2->execute([$closedInfo['cus_id']]);
        
        if ($qry2->rowCount() > 0) {
            $statusInfo = $qry2->fetch(PDO::FETCH_ASSOC);
            $closedInfo['action'] .= "<a href='#' class='closed-move' value='" . $closedInfo['cus_id'] . "'>Move</a>";
        }
        $closedInfo['action'] .= "</div></div>";
        $closed_list_arr[] = $closedInfo; // Append to the array

    }
}
$pdo = null; //Close Connection.
echo json_encode($closed_list_arr);

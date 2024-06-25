<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];
$collection_list_arr = array();
$qry = $pdo->query("SELECT * FROM users WHERE collection_access = '1' ");
if ($qry->rowCount() > 0) {
    $qry = $pdo->query("SELECT cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, bc.branch_name, cp.mobile1
    FROM customer_profile cp 
    LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
    LEFT JOIN area_name_creation anc ON cp.area = anc.id
    LEFT JOIN area_creation ac ON cp.line = ac.line_id
    LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
    LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
    WHERE cs.status = 7 ORDER BY cp.id DESC");
    if ($qry->rowCount() > 0) {
        while ($collectionInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
            $collectionInfo['action'] = "<a href='#' class='collection-details' value='" . $collectionInfo['cus_id'] . "'><button class='btn btn-primary'>Collect</button></a>";
            $collection_list_arr[] = $collectionInfo; // Append to the array
        }
    }
}
$pdo = null; //Close Connection.
echo json_encode($collection_list_arr);

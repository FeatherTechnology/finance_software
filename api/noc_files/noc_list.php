<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];
$noc_list_arr = array();
$qry = $pdo->query("SELECT cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, bc.branch_name, cp.mobile1, cs.status, cs.id as c_id
    FROM customer_profile cp 
    LEFT JOIN loan_entry_loan_calculation lelc ON cp.id = lelc.cus_profile_id
    LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
    LEFT JOIN area_name_creation anc ON cp.area = anc.id
    LEFT JOIN area_creation ac ON cp.line = ac.line_id
    LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
    LEFT JOIN customer_status cs ON cp.id = cs.cus_profile_id
    JOIN users u ON FIND_IN_SET(cp.line, u.line)
	JOIN users us ON FIND_IN_SET(lelc.loan_category, us.loan_category)
    WHERE (cs.status = 10 OR cs.status = 11) AND u.id ='$user_id' AND us.id ='$user_id'  GROUP BY cp.cus_id ORDER BY cp.id DESC");
if ($qry->rowCount() > 0) {
    while ($nocInfo = $qry->fetch(PDO::FETCH_ASSOC)) {
        // $nocInfo['action'] = "<a href='#' class='noc-details' value='" . $nocInfo['cus_id'] . "'><button class='btn btn-primary'>View</button></a>";

        $nocInfo['action'] = "<div class='dropdown'><button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button><div class='dropdown-content'><a href='#' class='noc-details' value='" . $nocInfo['cus_id'] . "' title='View NOC'>View</a>";
        
        if($nocInfo['status'] == '11'){
            $nocInfo['action'] .= "<a href='#' id='remove-noc' value='".$nocInfo['c_id']."' title='Remove'>Remove</a>";
        }

        $nocInfo['action'] .= "</div></div>";
        $noc_list_arr[] = $nocInfo; // Append to the array
    }
}

$pdo = null; //Close Connection.
echo json_encode($noc_list_arr);

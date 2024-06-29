<?php
require '../../ajaxconfig.php';

$search_list_arr = array();

$cus_id = isset($_POST['cus_id']) ? $_POST['cus_id'] : '';
$cus_name = isset($_POST['cus_name']) ? $_POST['cus_name'] : '';
$area = isset($_POST['area']) ? $_POST['area'] : '';
$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';

// Initialize the query with the common part
$sql = "SELECT cp.cus_id, cp.cus_name, anc.areaname AS area, lnc.linename, bc.branch_name, cp.mobile1
        FROM customer_profile cp 
        LEFT JOIN line_name_creation lnc ON cp.line = lnc.id
        LEFT JOIN area_name_creation anc ON cp.area = anc.id
        LEFT JOIN area_creation ac ON cp.line = ac.line_id
        LEFT JOIN branch_creation bc ON ac.branch_id = bc.id
        INNER JOIN (SELECT MAX(id) as max_id FROM customer_profile GROUP BY cus_id) latest ON cp.id = latest.max_id 
        WHERE 1=1";

// Create an array to hold the conditions
$conditions = [];
$parameters = [];

// Add conditions based on priority
if (!empty($cus_id)) {
    $conditions[] = "cp.cus_id LIKE :cus_id";
    $parameters[':cus_id'] = '%' . $cus_id . '%';
}
if (!empty($cus_name)) {
    $conditions[] = "cp.cus_name LIKE :cus_name";
    $parameters[':cus_name'] = '%' . $cus_name . '%';
}
if (!empty($mobile)) {
    $conditions[] = "cp.mobile1 LIKE :mobile";
    $parameters[':mobile'] = '%' . $mobile . '%';
}
if (!empty($area)) {
    $conditions[] = "anc.areaname LIKE :cus_area";
    $parameters[':cus_area'] = '%' . $area . '%';
}

// Apply the conditions based on priority
if (count($conditions) > 0) {
    $sql .= " AND (" . implode(" OR ", $conditions) . ")";
}

$sql .= " ORDER BY cp.id DESC";

$stmt = $pdo->prepare($sql);


foreach ($parameters as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}


$stmt->execute();


if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['action'] = "<div class='dropdown'>
            <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
            <div class='dropdown-content'>
                <a href='#' class='view_customer' value='" . $row['cus_id'] . "'>View</a>
            </div>
        </div>";
        $search_list_arr[] = $row; 
    }
}

$pdo = null; // Close Connection

echo json_encode($search_list_arr);
?>

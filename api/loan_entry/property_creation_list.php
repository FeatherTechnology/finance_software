<?php
require '../../ajaxconfig.php';

$property_list_arr = array();
$i=0;
$qry = $pdo->query("SELECT pi.id, pi.property, pi.property_detail, fi.fam_name as property_holder, fi.fam_relationship 
FROM property_info pi 
JOIN family_info fi ON pi.property_holder = fi.id");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
  
    $row['action'] = "<span class='icon-border_color propertyActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;<span class='icon-delete propertyDeleteBtn' value='" . $row['id'] . "'></span>";

        $property_list_arr[$i] = $row; // Append to the array
        $i++;
    }
}

echo json_encode($property_list_arr);
$pdo = null; // Close Connection
?>

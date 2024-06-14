
<?php
require '../../ajaxconfig.php';

$branch_list_arr = array();
$i=0;
$qry = $pdo->query("SELECT bc.id, bc.branch_code, bc.company_name, bc.branch_name, bc.place, st.state_name, dt.district_name, bc.mobile_number, bc.email_id  FROM branch_creation bc LEFT JOIN States st ON bc.state = st.id LEFT JOIN districts dt ON bc.district = dt.id LEFT JOIN taluks tk ON bc.taluk = tk.id");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
  
    $row['action'] = "<span class='icon-border_color branchActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;<span class='icon-delete branchDeleteBtn' value='" . $row['id'] . "'></span>";

        $branch_list_arr[$i] = $row; // Append to the array
        $i++;
    }
}

echo json_encode($branch_list_arr);
$pdo = null; // Close Connection
?>




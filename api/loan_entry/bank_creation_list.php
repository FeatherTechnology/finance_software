<?php
require '../../ajaxconfig.php';

$aadhar_num = $_POST['aadhar_num'];

$bank_list_arr = array();
$i=0;
$qry = $pdo->query("SELECT id,bank_name,branch_name, acc_holder_name, acc_number,ifsc_code FROM bank_info WHERE aadhar_num = '$aadhar_num' ");

if ($qry->rowCount() > 0) {
    while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
  
    $row['action'] = "<span class='icon-border_color bankActionBtn' value='" . $row['id'] . "'></span>&nbsp;&nbsp;&nbsp;<span class='icon-delete bankDeleteBtn' value='" . $row['id'] . "'></span>";

        $bank_list_arr[$i] = $row; // Append to the array
        $i++;
    }
}

echo json_encode($bank_list_arr);
$pdo = null; // Close Connection
?>
<?php
require "../../../ajaxconfig.php";
$exp_cat = ["1"=>Pooja, "2"=>Vehicle, "3"=>Fuel, "4"=>Stationary, "5"=>Press, "6"=>Food, "7"=>Rent, "8"=>EB, "9"=>Mobile bill, "10"=>Office Maintenance, "11"=>Salary, "12"=>Tax & Auditor, "13"=>Int Less, "14"=>Agent Incentive, "15"=>Common, "16"=>Other];
$exp_list_arr = array();
$qry = $pdo->query("SELECT * FROM expenses");
if($qry->rowCount()>0){
    while($result = $qry->fetch()){
        $result['action'] = "<span class='icon-trash-2 expDeleteBtn' value='" . $result['id'] . "'></span>";
        $exp_list_arr[] = $result;
    }
}

echo json_encode($exp_list_arr);
?>

<?php 
require "../../ajaxconfig.php";
$branch_id = $_POST['branch_id'];

$area_name_arr = array();
$qry = $pdo->query("SELECT id,areaname,status FROM area_name_creation WHERE branch_id ='$branch_id' ");
if($qry->rowCount()>0){
    while($areaname_info = $qry->fetch(PDO::FETCH_ASSOC)){
        $areaname_info['status'] = ($areaname_info['status'] =='1') ? 'Enable' : 'Disable';
        $areaname_info['action'] = "<span class='icon-border_color areanameActionBtn' value='" . $areaname_info['id'] . "'></span>  <span class='icon-trash-2 areanameDeleteBtn' value='" . $areaname_info['id'] . "'></span>";
        $area_name_arr[] = $areaname_info;
    }
}

$pdo = null; //Connection Close.

echo json_encode($area_name_arr);
?>
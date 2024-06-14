<?php
require '../../ajaxconfig.php';
@session_start();

$agent_code = $_POST['agent_code'];
$agent_name=$_POST['agent_name'];
$mobile1=$_POST['mobile1'];
$mobile2 = $_POST['mobile2'];
$area = $_POST['area'];
$occupation = $_POST['occupation'];
$user_id = $_SESSION['user_id'];
$agent_id = $_POST['agent_id'];

if($agent_id !=''){
    $qry = $pdo->query("UPDATE `agent_creation` SET `agent_code`='$agent_code',`agent_name`='$agent_name',`mobile1`='$mobile1',`mobile2`='$mobile2',`area`='$area',`occupation`='$occupation',`update_login_id`='$user_id',updated_date = now() WHERE `id`='$agent_id'");
    $result = 0; //update

}else{
    $qry = $pdo->query("INSERT INTO `agent_creation`(`agent_code`, `agent_name`,`mobile1`,`mobile2`, `area`, `occupation`, `insert_login_id`,`created_date`) VALUES ('$agent_code','$agent_name', '$mobile1','$mobile2','$area','$occupation','$user_id',now())");
    $result = 1; //Insert
}

echo json_encode($result);
?>
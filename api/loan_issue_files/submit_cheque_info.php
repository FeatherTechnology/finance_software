<?php
require '../../ajaxconfig.php';
@session_start();
$user_id = $_SESSION['user_id'];

$cq_holder_type = $_POST['cq_holder_type'];
$cq_holder_name = $_POST['cq_holder_name'];
$cq_holder_id = $_POST['cq_holder_id'];
$cq_relationship = $_POST['cq_relationship'];
$cq_bank_name = $_POST['cq_bank_name'];
$cheque_count = $_POST['cheque_count'];
$customer_profile_id = $_POST['customer_profile_id'];
$cus_id = $_POST['cus_id'];
$id = $_POST['id'];


if (!empty($_FILES['cq_upload']['name'])) {
    $path = "../../uploads/loan_issue/cheque_info/";
    $picture = $_FILES['cq_upload']['name'];
    $pic_temp = $_FILES['cq_upload']['tmp_name'];
    $picfolder = $path . $picture;
    $fileExtension = pathinfo($picfolder, PATHINFO_EXTENSION); //get the file extention
    $picture = uniqid() . '.' . $fileExtension;
    while (file_exists($path . $picture)) {
        //this loop will continue until it generates a unique file name
        $picture = uniqid() . '.' . $fileExtension;
    }
    move_uploaded_file($pic_temp, $path . $picture);
} else {
    $picture = $_POST['cq_upload_edit'];
}

$status = 0;
if ($id != '') {
    $qry = $pdo->query("UPDATE `cheque_info` SET `cus_id`='$cus_id',`cus_profile_id`='$customer_profile_id',`holder_type`='$cq_holder_type',`holder_name`='$cq_holder_name',`holder_id`='$cq_holder_id',`relationship`='$cq_relationship',`bank_name`='$cq_bank_name',`cheque_cnt`='$cheque_count',`update_login_id`='$user_id',`updated_on`=now() WHERE id = '$id' ");
    $status = 1; //update
    $last_id = $id;
} else {
    $qry = $pdo->query("INSERT INTO `cheque_info`( `cus_id`, `cus_profile_id`, `holder_type`, `holder_name`, `holder_id`, `relationship`, `bank_name`, `cheque_cnt`, `insert_login_id`, `created_on`) VALUES ('$cus_id','$customer_profile_id','$cq_holder_type','$cq_holder_name','$cq_holder_id','$cq_relationship','$cq_bank_name','$cheque_count','$user_id',now() )");
    $status = 2; //Insert
    $last_id = $pdo->lastInsertId();
}

// INSERT INTO `cheque_upload`(`id`, `cus_id`, `cus_profile_id`, `cheque_info_id`, `cheque_upload_name`) VALUES ('[value-1]','$cus_id','$customer_profile_id','[value-4]','[value-5]')

echo json_encode($status);

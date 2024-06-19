<?php
require '../../ajaxconfig.php';
@session_start();
if (!empty($_FILES['pic']['name'])) {
    $path = "../../uploads/loan_entry/cus_pic/";
    $picture = $_FILES['pic']['name'];
    $pic_temp = $_FILES['pic']['tmp_name'];
    $picfolder = $path . $picture;
    $fileExtension = pathinfo($picfolder, PATHINFO_EXTENSION); //get the file extention
    $picture = uniqid() . '.' . $fileExtension;
    while (file_exists($path . $picture)) {
        //this loop will continue until it generates a unique file name
        $picture = uniqid() . '.' . $fileExtension;
    }
    move_uploaded_file($pic_temp, $path . $picture);
} else {
    $picture = $_POST['per_pic'];
}
$cus_id=$_POST['cus_id'];
$cus_name=$_POST['cus_name'];
$gender=$_POST['gender'];
$dob=$_POST['dob'];
$age=$_POST['age'];
$mobile1=$_POST['mobile1'];
$mobile2=$_POST['mobile2'];
$user_id = $_SESSION['user_id'];
$customer_profile_id =$_POST['customer_profile_id'];
if($customer_profile_id !=''){
    $qry = $pdo->query("UPDATE `customer_profile` SET `cus_id`='$cus_id',`cus_name`='$cus_name',`gender`='$gender',`dob`='$dob',`age`='$age',`mobile1`='$mobile1',`mobile2`='$mobile2',`pic`='$picture',`update_login_id`='$user_id',updated_on = now() WHERE `id`='$customer_profile_id'");
    $status = 0; //update
    $last_id =$customer_profile_id;

}else{
   
    $qry = $pdo->query("INSERT INTO `customer_profile`(`cus_id`, `cus_name`, `gender`, `dob`, `age`, `mobile1`, `mobile2`, `pic`,`insert_login_id`, `created_on` ) VALUES ('$cus_id','$cus_name','$gender','$dob','$age','$mobile1','$mobile2','$picture','$user_id',now())");
    $status = 1; //Insert
    $last_id = $pdo->lastInsertId();

}

$result = array('status'=>$status, 'last_id'=> $last_id);
echo json_encode($result);
?>
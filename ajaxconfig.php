<?php
$timeZoneQry = "SET time_zone = '+5:30' ";


$host = "192.168.1.5";
$db_user = "finance";
$db_pass = "finance@123";
$dbname = "testing_fin";
$pdo = new PDO("mysql:host=$host; dbname=$dbname", $db_user, $db_pass);
$pdo->exec($timeZoneQry);


date_default_timezone_set('Asia/Kolkata');

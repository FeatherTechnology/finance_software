<?php
$timeZoneQry = "SET time_zone = '+5:30' ";


$host = "mysql5049.site4now.net";
$db_user = "a86e03_emiint";
$db_pass = "Loan@123";
$dbname = "db_a86e03_emiint";
$pdo = new PDO("mysql:host=$host; dbname=$dbname", $db_user, $db_pass);
$pdo->exec($timeZoneQry);


date_default_timezone_set('Asia/Kolkata');

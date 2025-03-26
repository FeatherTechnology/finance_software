<?php

require '../../ajaxconfig.php';

// Retrieve the cus_id and cus_profile_id from POST request
$aadhar_num = $_POST['aadhar_num'];
$custProfileId = $_POST['cus_profile_id'];

try {
    // Use prepared statements to avoid SQL injection and ensure proper syntax
    $stmt1 = $pdo->prepare("DELETE FROM customer_status WHERE aadhar_num = :aadhar_num AND cus_profile_id = :cusProfileId");
    $stmt1->execute(['aadhar_num' => $aadhar_num, 'cusProfileId' => $custProfileId]);

    $stmt2 = $pdo->prepare("DELETE FROM customer_profile WHERE aadhar_num = :aadhar_num AND id = :cusProfileId");
    $stmt2->execute(['aadhar_num' => $aadhar_num, 'cusProfileId' => $custProfileId]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>

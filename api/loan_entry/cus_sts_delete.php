<?php

require '../../ajaxconfig.php';

// Retrieve the cus_id and cus_profile_id from POST request
$custId = $_POST['cus_id'];
$custProfileId = $_POST['cus_profile_id'];

try {
    // Use prepared statements to avoid SQL injection and ensure proper syntax
    $stmt1 = $pdo->prepare("DELETE FROM customer_status WHERE cus_id = :cusId AND cus_profile_id = :cusProfileId");
    $stmt1->execute(['cusId' => $custId, 'cusProfileId' => $custProfileId]);

    $stmt2 = $pdo->prepare("DELETE FROM customer_profile WHERE cus_id = :cusId AND id = :cusProfileId");
    $stmt2->execute(['cusId' => $custId, 'cusProfileId' => $custProfileId]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>

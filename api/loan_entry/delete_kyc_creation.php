<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];
$result = '0'; // Default to failure

try {
    $qry = $pdo->prepare("SELECT upload FROM kyc_info WHERE id = :id");
    $qry->bindParam(':id', $id, PDO::PARAM_INT);
    if ($qry->execute()) {
        $row = $qry->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $filePath = "../../uploads/loan_entry/kyc/" . $row['upload'];
            if (is_file($filePath)) {
                unlink($filePath);
            }

            $deleteQry = $pdo->prepare("DELETE FROM kyc_info WHERE id = :id");
            $deleteQry->bindParam(':id', $id, PDO::PARAM_INT);
            if ($deleteQry->execute()) {
                $result = '1'; // Success
            }
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $result = '0'; // Failure
}

$pdo = null; // Close Connection

echo json_encode($result);

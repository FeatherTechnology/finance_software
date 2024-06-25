<?php
require '../../ajaxconfig.php';

$id = $_POST['id'];
$cus_id = $_POST['cus_id'];
$cus_profile_id = $_POST['cus_profile_id'];

try {
    // Fetch all cus_profile_ids associated with the given cus_id
    $profileIdsQry = $pdo->prepare("SELECT * FROM customer_profile WHERE cus_id = :cus_id");
    $profileIdsQry->bindParam(':cus_id', $cus_id, PDO::PARAM_STR);
    $profileIdsQry->execute();
    $profileIds = $profileIdsQry->fetchAll(PDO::FETCH_COLUMN);

    foreach ($profileIds as $profileId) {

        $countQry = $pdo->prepare("SELECT COUNT(*) FROM kyc_info WHERE cus_profile_id = $cus_profile_id and id= $id");
        $countQry->execute();
        $kycCount = $countQry->fetchColumn();

        if ($kycCount == 1) {
            // If there's only one KYC record for any profile associated with this cus_id, do not allow deletion
            $result = '0';
            echo json_encode($result);
            exit;
        } else {
            // Fetch the upload filename for the given KYC record ID
            $fetchQry = $pdo->prepare("SELECT upload FROM kyc_info WHERE id = :id");
            $fetchQry->bindParam(':id', $id, PDO::PARAM_INT);
            $fetchQry->execute();
            $row = $fetchQry->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                // If the file exists, delete it
                $filePath = "../../uploads/loan_entry/kyc/" . $row['upload'];
                if (is_file($filePath)) {
                    unlink($filePath);
                }

                // Delete the KYC record from the database
                $deleteQry = $pdo->prepare("DELETE FROM kyc_info WHERE id = :id");
                $deleteQry->bindParam(':id', $id, PDO::PARAM_INT);
                if ($deleteQry->execute()) {
                    $result = '1'; // Success
                } else {
                    $result = '2'; // Failure to delete record
                }
            } else {
                $result = '2'; // Record not found
            }
            echo json_encode($result);
            exit;
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $result = '2'; // General failure
}

$pdo = null; // Close Connection



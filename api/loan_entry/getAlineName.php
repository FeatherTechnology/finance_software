<?php
require '../../ajaxconfig.php'; // Adjust the path based on your actual configuration file

$response = array();

if (isset($_POST['aline_id'])) {
    $areaId = $_POST['aline_id'];

    // Prepare and execute the query
    $stmt = $pdo->prepare("
        SELECT lnc.linename 
        FROM area_creation ac
        JOIN line_name_creation lnc ON FIND_IN_SET(lnc.id, ac.line_id)
        WHERE ac.id = ? AND ac.status = 1
    ");
    
    $stmt->execute([$areaId]);

    // Fetch the result
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['line'] = $row['linename'];
    } else {
        $response['line'] = ''; // No relationship found
    }
} else {
    $response['line'] = ''; // No area ID provided
}

$pdo = null; // Close the connection

echo json_encode($response);
?>

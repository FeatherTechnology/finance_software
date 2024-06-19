<?php
require '../../ajaxconfig.php'; // Adjust the path based on your actual configuration file

$response = array();

if (isset($_POST['aline_id'])) {
    $areaId = $_POST['aline_id'];
    $stmt = $pdo->query("
        SELECT lnc.linename 
        FROM `area_creation` ac 
        LEFT JOIN line_name_creation lnc ON ac.line_id = lnc.id
        WHERE FIND_IN_SET('$areaId', ac.area_id)
    ");

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

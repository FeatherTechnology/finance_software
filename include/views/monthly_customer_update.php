<?php
include 'C:\xampp\htdocs\finance_software\ajaxconfig.php';

// 1. Get all req_ids
$qry = $pdo->query("SELECT cs.cus_profile_id as cp_id FROM customer_status cs WHERE cs.status = 7 ORDER BY cs.id ASC");
$customer_profile_id = array_column($qry->fetchAll(PDO::FETCH_ASSOC), 'cp_id');

// 2. Split req_ids into chunks of 2 (optional, for grouping only)
$chunks = array_chunk($customer_profile_id, 2);
// $chunks = [
//   [9735, 9766],
//   [9831, 9840],
//   [130, 132]

// ];

foreach ($chunks as $chunk) {
    // Process each req_id individually inside the chunk
    foreach ($chunk as $cp_id) {

        // Prepare POST data with single CpID (because resetCustomerStatus.php expects only one)
        $postData = ['cpID' => $cp_id];
        // Call resetCustomerStatus.php for one req_id
        $ch = curl_init('http://localhost/finance_software/api/collection_files/resetCustomerStatus.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $responseJSON = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($responseJSON, true);
        if (!empty($response) && isset($response['cp_id'])) {
            $pending_sts = (isset($response['pending_customer'][0]) && $response['pending_customer'][0] === true) ? 'true' : 'false';
            $od_sts = (isset($response['od_customer'][0]) && $response['od_customer'][0] === true) ? 'true' : 'false';
            $due_nil_sts = (isset($response['due_nil_customer'][0]) && $response['due_nil_customer'][0] === true) ? 'true' : 'false';
            $balAmnt = $response['balAmnt'][0] ?? 0;
            // Call updateCustomerStatus.php for this cus_profile_id
            $ch2 = curl_init('http://localhost/finance_software/api/collection_files/updateCustomerStatus.php');
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch2, CURLOPT_POSTFIELDS, [
                'cp_id' => $cp_id,
                'pending_sts' => $pending_sts,
                'od_sts' => $od_sts,
                'due_nil_sts' =>  $due_nil_sts,
                'bal_amt' => $balAmnt,
                'userid' => '1'
            ]);
            $updateResponse = curl_exec($ch2);
            curl_close($ch2);

            echo "Updated cp_id $cp_id: $updateResponse <br>";
        } else {
            echo "No data found for cp_id $cp_id <br>";
        }
    }
}

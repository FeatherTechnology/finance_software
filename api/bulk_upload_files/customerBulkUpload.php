<?php
require '../../ajaxconfig.php';
include 'bulkUploadClass.php';
require_once('../../vendor/csvreader/php-excel-reader/excel_reader2.php');
require_once('../../vendor/csvreader/SpreadsheetReader_CSV.php');


$obj = new bulkUploadClass();
$userData = $obj->getUserDetails($pdo);


$allowedFileType = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'text/csv', 'text/xml', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
if (in_array($_FILES["excelFile"]["type"], $allowedFileType)) {

    $excelfolder = $obj->uploadFiletoFolder();

    $Reader = new SpreadsheetReader_CSV($excelfolder);
    $sheetCount = count($Reader->sheets());

    for ($i = 0; $i < $sheetCount; $i++) {
        
        $Reader->ChangeSheet($i);
        $rowChange = 0;
        foreach ($Reader as $Row) {
            
            if ($rowChange != 0) { // omitted 0 to avoid headers
                $data = $obj->fetchAllRowData($Row);

                if (isset($data['cus_profile_id'])) {
                    $data['loan_id'] = $obj->getLoanCode($pdo, $data['cus_profile_id']);
                }
                $data['id'] = $obj->getLoanCategoryId($pdo, $data['loan_category']);
                if (isset($data['linename']) && isset($branch_id)) {
                    $data['linename'] = $obj->getAreaLine($pdo, $data['linename'], $branch_id);
                    $data['linename'] = $data['linename'] == $data['linename'] ? $data['linename'] : 'Invalid'; // This line seems redundant, consider removing it
                } else {
                    $data['linename'] = 'Invalid'; // Handle default case when $data['linename'] or $branch_id are not set
                }


                $data['id'] = $obj->checkAgent($pdo, $data['id']);
                 $checkCustomerData = $obj->checkCustomerData($pdo, $data['cus_id']);
                 $data['cus_data'] = $checkCustomerData['cus_data'];
                $data['id'] = $checkCustomerData['id'];
                $data['id'] = $obj->getSchemeId($pdo, $data['scheme_name']);

                $err_columns = $obj->handleError($data);
                if (empty($err_columns)) {
                    // Call LoanEntryTables function
                    if ($obj->LoanEntryTables($pdo, $data, $userData)) {
                        echo "Loan entry tables updated successfully.<br>";
                    } else {
                        echo "Failed to update loan entry tables.<br>";
                    }
                
                    // Call loanIssueTables function
                    if ($obj->loanIssueTables($pdo, $data, $userData)) {
                        echo "Loan issue tables updated successfully.<br>";
                    } else {
                        echo "Failed to update loan issue tables.<br>";
                    }
                }
                else {
                    $errtxt = "Please Check the input given in Serial No: " . ($rowChange) . " on below. <br><br>";
                    $errtxt .= "<ul>";
                    foreach ($err_columns as $columns) {
                        $errtxt .= "<li>$columns</li>";
                    }
                    $errtxt .= "</ul><br>";
                    $errtxt .= "Insertion completed till Serial No: " . ($rowChange - 1);
                    echo $errtxt;
                    exit();
                }
            }

            $rowChange++;
        }
    }
    $message = 'Bulk Upload Completed.';
} else {
    $message = 'File is not in Excel Format.';
}

echo $message;

<?php
require '../../ajaxconfig.php';

class bulkUploadClass {
    public function uploadFiletoFolder() {
        $excel = $_FILES['excelFile']['name'];
        $excel_temp = $_FILES['excelFile']['tmp_name'];
        $excelfolder = "../../uploads/bulk_upload/excelFile/" . $excel;

        $fileExtension = pathinfo($excelfolder, PATHINFO_EXTENSION); //get the file extension

        $excel = uniqid() . '.' . $fileExtension;
        while (file_exists("../../uploads/bulk_upload/excelFile/" . $excel)) {
            // this loop will continue until it generates a unique file name
            $excel = uniqid() . '.' . $fileExtension;
        }
        $excelfolder = "../../uploads/bulk_upload/excelFile/" . $excel;
        move_uploaded_file($excel_temp, $excelfolder);
        return $excelfolder;
    }

    public function fetchAllRowData($Row) {
        $dataArray = array(
            'cus_id' => isset($Row[1]) ? $Row[1] : "",
            'cus_name' => isset($Row[2]) ? $Row[2] : "",
            'gender' => isset($Row[3]) ? $Row[3] : "",
            'dob' => isset($Row[4]) ? $Row[4] : "",
            'age' => isset($Row[5]) ? $Row[5] : "",
            'mobile' => isset($Row[6]) ? $Row[6] : "",
            'state' => isset($Row[7]) ? $Row[7] : "",
            'district' => isset($Row[8]) ? $Row[8] : "",
            'taluk' => isset($Row[9]) ? $Row[9] : "",
            'address' => isset($Row[10]) ? $Row[10] : "",
            'cus_data' => isset($Row[11]) ? $Row[11] : "",
            'cus_status' => isset($Row[12]) ? $Row[12] : "",
            'guarantor_name' => isset($Row[13]) ? $Row[13] : "",          
            'guarantor_relationship' => isset($Row[14]) ? $Row[14] : "",
            'guarantor_aadhar_no' => isset($Row[15]) ? $Row[15] : "",
            'guarantor_mobile_no' => isset($Row[16]) ? $Row[16] : "",
            'guarantor_age' => isset($Row[17]) ? $Row[17] : "",
            'guarantor_occupation' => isset($Row[18]) ? $Row[18] : "",
            'guarantor_income' => isset($Row[19]) ? $Row[19] : "",
            'resident_type' => isset($Row[20]) ? $Row[20] : "",
            'resident_detail' => isset($Row[21]) ? $Row[21] : "",
            'res_address' => isset($Row[22]) ? $Row[22] : "",
            'native_address' => isset($Row[23]) ? $Row[23] : "",
            'occupation' => isset($Row[24]) ? $Row[24] : "",
            'occ_detail' => isset($Row[25]) ? $Row[25] : "",
            'occ_income' => isset($Row[26]) ? $Row[26] : "",
            'occ_address' => isset($Row[27]) ? $Row[27] : "",
            'area_confirm' => isset($Row[28]) ? $Row[28] : "",
            'area' => isset($Row[29]) ? $Row[29] : "",
            'line' => isset($Row[30]) ? $Row[30] : "",
            'cus_limit' => isset($Row[31]) ? $Row[31] : "",
            'about_cus' => isset($Row[32]) ? $Row[32] : "",
            'loan_category' => isset($Row[33]) ? $Row[33] : "",
            'loan_amount' => isset($Row[34]) ? $Row[34] : "",
            'profit_type' => isset($Row[35]) ? $Row[35] : "",
            'due_method' => isset($Row[36]) ? $Row[36] : "",
            'due_type' => isset($Row[37]) ? $Row[37] : "",
            'profit_method' => isset($Row[38]) ? $Row[38] : "",
            'scheme_due_method' => isset($Row[39]) ? $Row[39] : "",
            'scheme_day' => isset($Row[40]) ? $Row[40] : "",
            'scheme_name' => isset($Row[41]) ? $Row[41] : "",
            'interest_rate' => isset($Row[42]) ? $Row[42] : "",
            'due_period' => isset($Row[43]) ? $Row[43] : "",
            'doc_charge' => isset($Row[44]) ? $Row[44] : "",
            'processing_fees' => isset($Row[45]) ? $Row[45] : "",
            'principal_amnt' => isset($Row[46]) ? $Row[46] : "",
            'interest_amnt' => isset($Row[47]) ? $Row[47] : "",
            'total_amnt' => isset($Row[48]) ? $Row[48] : "",
            'due_amnt' => isset($Row[49]) ? $Row[49] : "",
            'document_charge_calculate' => isset($Row[50]) ? $Row[50] : "",
            'processsing_fees_calculate' => isset($Row[51]) ? $Row[51] : "",
            'net_cash' => isset($Row[52]) ? $Row[52] : "",
            'loan_date' => isset($Row[53]) ? $Row[53] : "",
            'dueStart_date' => isset($Row[54]) ? $Row[54] : "",
            'maturity_date' => isset($Row[55]) ? $Row[55] : "",
            'referred' => isset($Row[56]) ? $Row[56] : "",
            'agent_id' => isset($Row[57]) ? $Row[57] : "",
            'agent_name' => isset($Row[58]) ? $Row[58] : "",
            'payment_mode' => isset($Row[59]) ? $Row[59] : "",
            'issue_amount' => isset($Row[60]) ? $Row[60] : "",
            'transaction_id' => isset($Row[61]) ? $Row[61] : "",
            'cheque_no' => isset($Row[62]) ? $Row[62] : "",
            'issue_date' => isset($Row[63]) ? $Row[63] : "",
            'issue_person' => isset($Row[64]) ? $Row[64] : "",
            'relationship' => isset($Row[65]) ? $Row[65] : "",
        );

        return $dataArray;
    }
}

?>


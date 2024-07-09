<?php
require '../../ajaxconfig.php';

class bulkUploadClass
{
    public function uploadFiletoFolder()
    {
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

    public function fetchAllRowData($Row)
    {
        
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
            'residential_type' => isset($Row[20]) ? $Row[20] : "",
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
            'processing_fees_calculate' => isset($Row[51]) ? $Row[51] : "",
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

        $dataArray['cus_id'] = strlen($dataArray['cus_id']) == 12 ? $dataArray['cus_id'] : 'Invalid';
        $cus_dataArray = ['New' => 'New', 'Existing' => 'Existing'];
        $dataArray['cus_data'] = $this->arrayItemChecker($cus_dataArray, $dataArray['cus_data']);

        $cus_exist_typeArray = ['Additional' => 'Additional', 'Renewal' => 'Renewal'];
        $cus_status = $this->arrayItemChecker($cus_exist_typeArray, $dataArray['cus_status']);
        $dataArray['cus_status'] = ($cus_status == 'Not Found') ? '' : $cus_status; //cause cus_exist_type may not be available
        $dataArray['mobile'] = strlen($dataArray['mobile']) == 10 ? $dataArray['mobile'] : 'Invalid';
        $dob = $this->dateFormatChecker($dataArray['dob']);
        $dataArray['dob'] = ($dob == 'Invalid Date') ? '' : $dob;
        $genderArray = ['Male' => '1', 'Female' => '2', 'Others' => '3'];
        $dataArray['gender'] = $this->arrayItemChecker($genderArray, $dataArray['gender']);

        $stateArray = ['TamilNadu' => 'TamilNadu', 'Puducherry' => 'Puducherry'];
        $dataArray['state'] = $this->arrayItemChecker($stateArray, $dataArray['state']);
        $dataArray['guarantor_aadhar_no'] = strlen($dataArray['guarantor_aadhar_no']) == 12 ? $dataArray['guarantor_aadhar_no'] : 'Invalid';

        $guarantor_relationshipArray = ['Father' => 'Father', 'Mother' => 'Mother', 'Spouse' => 'Spouse', 'Sister' => 'Sister', 'Brother' => 'Brother', 'Son' => 'Son', 'Daughter' => 'Daughter'];
        $dataArray['guarantor_relationship'] = $this->arrayItemChecker($guarantor_relationshipArray, $dataArray['guarantor_relationship']);
        $dataArray['guarantor_mobile_no'] = strlen($dataArray['guarantor_mobile_no']) == 10 ? $dataArray['guarantor_mobile_no'] : 'Invalid';

        $residential_typeArray = ['Own' => '1', 'Rental' => '2', 'Lease' => '3', 'Quarters' => '4'];
        $residential_type = $this->arrayItemChecker($residential_typeArray, $dataArray['residential_type']);
        $dataArray['residential_type'] = ($residential_type == 'Not Found') ? '' : $residential_type; //cause residential_type may not be available

        $area_confirm_typeArray = ['Resident' => '1', 'Occupation' => '2'];
        $dataArray['area_confirm'] = $this->arrayItemChecker($area_confirm_typeArray, $dataArray['area_confirm']);

        $profit_typeArray = ['Calculation' => '1', 'Scheme' => '2'];
        $dataArray['profit_type'] = $this->arrayItemChecker($profit_typeArray, $dataArray['profit_type']);

        $due_method_calcArray = ['Monthly' => 'Monthly', 'Weekly' => 'Weekly', 'Daily' => 'Daily'];
        $dataArray['due_method'] = $this->arrayItemChecker($due_method_calcArray, $dataArray['due_method']);

        $due_typeArray = ['EMI' => 'EMI'];
        $dataArray['due_type'] = $this->arrayItemChecker($due_typeArray, $dataArray['due_type']);

        $profit_methodArray = ['After Interest' => 'after_interest'];
        $dataArray['profit_method'] = $this->arrayItemChecker($profit_methodArray, $dataArray['profit_method']);
        $due_method_schemeArray = ['Monthly' => '1', 'Weekly' => '2', 'Daily' => '3'];
        $scheme_due_method = $this->arrayItemChecker($due_method_schemeArray, $dataArray['scheme_due_method']);
        $dataArray['scheme_due_method'] = ($scheme_due_method == 'Not Found') ? '' : $scheme_due_method; //cause due_method_scheme may not be available

        $dataArray['loan_date'] = $this->dateFormatChecker($dataArray['loan_date']);

        $dataArray['dueStart_date'] = $this->dateFormatChecker($dataArray['dueStart_date']);
        $dataArray['maturity_date'] = $this->dateFormatChecker($dataArray['maturity_date']);

        $referred_typeArray = ['Yes' => '0', 'No' => '1'];
        $dataArray['referred'] = $this->arrayItemChecker($referred_typeArray, $dataArray['referred']);

        $payment_typeArray = ['Cash' => '1', 'Bank transfer' => '2', 'Cheque' => '3'];
        $dataArray['payment_mode'] = $this->arrayItemChecker($payment_typeArray, $dataArray['payment_mode']);

        return $dataArray;
    }
    function dateFormatChecker($checkdate)
    {
        // Attempt to create a DateTime object from the provided date
        $dateTime = DateTime::createFromFormat('Y-m-d', $checkdate);

        // Check if the date is in the correct format
        if ($dateTime !== false && $dateTime->format('Y-m-d') === $checkdate) {
            // Date is in the correct format, no need to change anything
            return $checkdate;
        } else if ($checkdate == '' || preg_match("/^[A-Za-z\s]$/", $checkdate)) {
            return 'Invalid Date';
        } else {
            // Date is not in the correct format, reformat it
            $formattedDor = date('Y-m-d', strtotime($checkdate));
            return $formattedDor;
        }
    }
    function arrayItemChecker($arrayList, $arrayItem)
    {
        if (array_key_exists($arrayItem, $arrayList)) {
            $arrayItem = $arrayList[$arrayItem];
        } else {
            $arrayItem = 'Not Found';
        }
        return $arrayItem;
     
    }
    function getUserDetails($pdo)
    {
        $data = [];
           
        // Check if user_id is set in session
        if (isset($_SESSION["user_id"])) {
            $data['user_id'] = $_SESSION["user_id"];
            
            // Prepare and execute the SQL query
            $stmt = $pdo->prepare("SELECT name, role FROM users WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $data['user_id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($row) {
                $data['user_name'] = $row['name'];
                if ($row['role'] == '7') {
                    $data['user_type'] = 'Admin';
                } elseif ($row['role'] == '9') {
                    $data['user_type'] = 'Dev';
                } else {
                    $data['user_type'] = 'Staff';
                }
            }
        }
    
        return $data;
    }
    
    function getLoanCode($pdo, $cus_profile_id)
    {
        if (!isset($id) || $id == '') {
            $qry = $pdo->query("SELECT loan_id FROM loan_entry_loan_calculation WHERE loan_id !='' ORDER BY id DESC ");
            if ($qry->rowCount() > 0) {
                $qry_info = $qry->fetch(); //LID-101
                $l_no = ltrim(strstr($qry_info['loan_id'], '-'), '-');
                $l_no = $l_no + 1;
                $loan_ID_final = "LID-" . "$l_no";
            } else {
                $loan_ID_final = "LID-" . "101";
            }
        } else {
            $qry = $pdo->query("SELECT loan_id FROM loan_entry_loan_calculation WHERE id = '$cus_profile_id'");
            $qry_info = $qry->fetch();
            $loan_ID_final = $qry_info['loan_id'];
        }
        
        return $loan_ID_final;
    }
    
    function checkCustomerData($pdo, $cus_id)
    {
        $cus_id = strip_tags($cus_id); // Sanitize input
    
        $new_cus_check = $pdo->query("SELECT id FROM customer_profile WHERE cus_id = '$cus_id'");
    
        if ($new_cus_check) {
            if ($new_cus_check->rowCount() == 0) {
                $response['cus_data'] = 'New';
                $response['id'] = '';
            } else {
                $row = $new_cus_check->fetch(PDO::FETCH_ASSOC);
                $response['cus_data'] = 'Existing';
                $response['id'] = $row['id'];
            }
        } else {
            // Handle query error or no result scenario
            $response['cus_data'] = 'New'; // Assuming default to 'New' if query fails
            $response['id'] = '';
        }
    
        return $response;
    }
    
    function getAreaId($pdo, $areaname)
    {
        $stmt = $pdo->prepare("SELECT id FROM area_name_creation WHERE areaname = :areaname");
        $stmt->execute(['areaname' => $areaname]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $area_id = $row["id"];
        } else {
            $area_id = 'Not Found';
        }

        return $area_id;
    }

    function getLoanCategoryId($pdo, $loan_category)
    {
        // Use a prepared statement to prevent SQL injection
        $stmt = $pdo->prepare("SELECT id FROM loan_category_creation WHERE loan_category = :loan_category");
        $stmt->execute(['loan_category' => $loan_category]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $loan_cat_id = $row["id"];
        } else {
            $loan_cat_id = 'Not Found';
        }

        return $loan_cat_id;
    }

    function getAreaLine($pdo, $linename, $branch_id)
    {
        $defaultLinename = 'Invalid'; // Default value
        
        // Use trim() and prepared statement to prevent SQL injection
        $stmt = $pdo->prepare("SELECT linename FROM `line_name_creation` WHERE REPLACE(TRIM(linename), ' ', '') = REPLACE(TRIM(:linename), ' ', '') AND branch_id = :branch_id");
        $stmt->execute(['linename' => $linename, 'branch_id' => $branch_id]);
    
        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $linename = $result['linename'];
        } else {
            $linename = $defaultLinename; // If no matching linename found, set to default
        }
        
        return $linename;
    }
    
    
    function checkAgent($pdo, $agent_name)
    {
        if ($agent_name != '') { // because it's not mandatory
            $stmt = $pdo->prepare("SELECT id FROM `agent_creation` WHERE agent_name = :agent_name");
            $stmt->execute(['agent_name' => $agent_name]);

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $agentCheck = $row["id"];
            } else {
                $agentCheck = 'Not Found';
            }
        } else {
            $agentCheck = '';
        }
        return $agentCheck;
    }

    function getSchemeId($pdo, $scheme_name)
    {
        $stmt = $pdo->prepare("SELECT id FROM scheme WHERE scheme_name = :scheme_name");
        $stmt->execute(['scheme_name' => $scheme_name]);

        if ($stmt->rowCount() > 0) {
            $scheme_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
        } else {
            $scheme_id = '';
        }
        return $scheme_id;
    }
    function LoanEntryTables($pdo, $data, $userData)
    {
        // Print or log $data to see what values are being passed
        echo "Data array contents for family_info insertion:";
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        $insert_fam = $pdo->query("INSERT INTO `family_info`(`cus_id`, `fam_name`, `fam_relationship`, `fam_age`, `fam_occupation`, `fam_aadhar`, `fam_mobile`, `insert_login_id`, `created_on`) 
        VALUES ('" . $data['cus_id'] . "','" . $data['guarantor_name'] . "','" . $data['guarantor_relationship'] . "','" . $data['guarantor_age'] . "','" . $data['guarantor_occupation'] . "','" . $data['guarantor_aadhar_no'] . "','" . $data['guarantor_mobile_no'] . "','')");
    
        $family_id = $pdo->lastInsertId();
    ;
    
        $insert_cp = $pdo->query("INSERT INTO customer_profile (
            cus_id, cus_name, gender, dob, age, mobile1, mobile2, pic, guarentor_name, gu_pic, cus_data, cus_status, res_type, res_details, res_address, native_address, occupation, occ_detail, occ_income, occ_address, area_confirm, area, line, cus_limit, about_cus, insert_login_id, created_on, updated_on
        ) VALUES ('" . strip_tags($data['cus_id']) . "','" . strip_tags($data['cus_name']) . "','" . strip_tags($data['gender']) . "','" . strip_tags($data['dob']) . "','" . strip_tags($data['age']) . "','" . strip_tags($data['mobile1']) . "','" . strip_tags($data['mobile2']) . "','','" . strip_tags($data['guarantor_name']) . "','','" . strip_tags($data['cus_data']) . "','" . strip_tags($data['cus_status']) . "','" . strip_tags($data['residential_type']) . "','" . strip_tags($data['resident_detail']) . "','" . strip_tags($data['res_address']) . "',''" . strip_tags($data['native_address']) . "'','" . strip_tags($data['occupation']) . "','" . strip_tags($data['occ_detail']) . "',''" . strip_tags($data['occ_income']) . "',''" . strip_tags($data['occ_address']) . "','" . strip_tags($data['area_confirm']) . "','" . strip_tags($data['area']) . "','" . strip_tags($data['line']) . "',
            '10','','" . $userData['user_id'] . "','" . strip_tags($data['currentTimeStamp']) . "','" . strip_tags($data['now']) . "'
        )");
    
        $cus_profile_id = $pdo->lastInsertId();
    
    
        $insert_vlc = $pdo->query("INSERT INTO loan_entry_loan_calculation (cus_profile_id, cus_id, loan_id, loan_category, category_info, loan_amount, profit_type, due_method, due_type,profit_method, scheme_due_method, scheme_day, scheme_name, interest_rate, due_period, doc_charge, processing_fees,
            loan_amnt, principal_amnt, interest_amnt, total_amnt, due_amnt, doc_charge_calculate, processing_fees_calculate,net_cash, loan_date, due_startdate, maturity_date, referred, agent_id, agent_name, insert_login_id, created_on, updated_on
        ) VALUES ('" . strip_tags($cus_profile_id) . "', '" . strip_tags($data['cus_id']) . "','" . strip_tags($data['loan_id_calc']) . "', '" . strip_tags($data['loan_category']) . "','" . strip_tags($data['category_info']) . "',
            '" . strip_tags($data['loan_amount']) . "', '" . strip_tags($data['profit_type']) . "','" . strip_tags($data['due_method']) . "','" . strip_tags($data['due_type']) . "',
            '" . strip_tags($data['profit_method']) . "','" . strip_tags($data['scheme_due_method']) . "','" . strip_tags($data['scheme_day']) . "','" . strip_tags($data['scheme_name']) . "',
            '" . strip_tags($data['interest_rate']) . "','" . strip_tags($data['due_period']) . "','" . strip_tags($data['doc_charge']) . "','" . strip_tags($data['processing_fees']) . "','" . strip_tags($data['loan_amnt']) . "','" . strip_tags($data['principal_amnt']) . "',
            '" . strip_tags($data['interest_amnt']) . "', '" . strip_tags($data['total_amnt']) . "', '" . strip_tags($data['due_amnt']) . "', '" . strip_tags($data['doc_charge_calculate']) . "', '" . strip_tags($data['processing_fees_calculate']) . "',
            '" . strip_tags($data['net_cash']) . "','" . strip_tags($data['loan_date']) . "','" . strip_tags($data['due_startdate']) . "','" . strip_tags($data['maturity_date']) . "',
            '" . strip_tags($data['referred']) . "','" . strip_tags($data['agent_id']) . "','" . strip_tags($data['agent_name']) . "','" . $userData['user_id'] . "','" . strip_tags($data['currentTimeStamp']) . "','" . strip_tags($data['now']) . "'
        )");
    
        // Get the last inserted Id
        $id = $pdo->lastInsertId();
    }
    
    function loanIssueTables($pdo, $data, $userData)
    {
        $insert_li_query = "INSERT INTO `loan_issue` 
            (`cus_id`, `cus_profile_id`, `loan_amnt`, `net_cash`, `payment_mode`, `issue_amnt`, `transaction_id`, `cheque_no`, `issue_date`, `issue_person`, `relationship`, `insert_login_id`, `created_on`) 
            VALUES ('" . strip_tags($data['cus_id']) . "','" . strip_tags($data['cus_profile_id']) . "','" . strip_tags($data['loan_amnt']) . "','" . strip_tags($data['net_cash']) . "', '" . strip_tags($data['payment_mode']) . "', 
             '" . strip_tags($data['issue_amnt']) . "', '" . strip_tags($data['transaction_id']) . "','" . strip_tags($data['cheque_no']) . "', '" . strip_tags($data['issue_date']) . "', '" . strip_tags($data['issue_person']) . "', '" . strip_tags($data['relationship']) . "', 
             '" . $userData['user_id'] . "', '" . $data['loan_date'] . "')";
    
        $pdo->query($insert_li_query);
    }
    
    
    function handleError($data)
    {
        $errcolumns = array();

        if ($data['cus_id'] == 'Invalid') {
            $errcolumns[] = 'Customer ID';
        }

        if ($data['cus_data'] == 'Not Found') {
            $errcolumns[] = 'Customer Data';
        }

        if ($data['cus_data'] == 'Existing' && (!preg_match('/^[A-Za-z]+$/', $data['cus_status']) || $data['cus_status'] == '')) {
            $errcolumns[] = 'Customer Existence Type';
        }

        if ($data['cus_name'] == '') {
            $errcolumns[] = 'Customer Name';
        }

        // if ($data['dob'] == 'Invalid Date') {
        //     $errcolumns[] = 'Date of Birth';
        // }

        // if (!preg_match('/^[0-9]+$/', $data['age'])) {
        //     $errcolumns[] = 'Age';
        // }

        if ($data['mobile'] == 'Invalid') {
            $errcolumns[] = 'Mobile Number';
        }

        if ($data['guarantor_name'] == '') {
            $errcolumns[] = 'Guarantor Name';
        }

        if ($data['guarantor_aadhar_no'] == 'Invalid') {
            $errcolumns[] = 'Guarantor Aadhar';
        }

        if (!preg_match('/^[0-9]+$/', $data['guarantor_age'])) {
            $errcolumns[] = 'Guarantor Age';
        }

        if ($data['guarantor_mobile_no'] == 'Invalid') {
            $errcolumns[] = 'Guarantor Mobile Number';
        }

        if (!preg_match('/^[A-Za-z0-9]+$/', $data['guarantor_occupation'])) {
            $errcolumns[] = 'Guarantor Occupation';
        }

        if (!preg_match('/^[0-9]+$/', $data['guarantor_income'])) {
            $errcolumns[] = 'Guarantor Income';
        }

        if ($data['loan_category'] == 'Not Found') {
            $errcolumns[] = 'Loan Category ID';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['total_amnt'])) {
            $errcolumns[] = 'Total Amount';
        }


        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['loan_amount'])) {
            $errcolumns[] = 'Loan Amount';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['cus_limit'])) {
            $errcolumns[] = 'Customer Limit';
        }

        // Condition 1
        if ($data['area_confirm'] != 'Not Found') {
            // Subcondition 1.1
            if ($data['area_confirm'] == '1') {
                if (
                    $data['residential_type'] == ''
                    || $data['residential_detail'] == ''
                    || $data['res_address'] == ''
                    || $data['native_address'] == ''
                ) {
                    $errcolumns[] = 'Residential Type or Details or Address';
                }
            }
        } else {
            $errcolumns[] = 'Area Confirm Type';
        }

        if ($data['occupation'] == 'Not Found') {
            $errcolumns[] = 'Occupation Type';
        }

        if ($data['occ_detail'] == '') {
            $errcolumns[] = 'Occupation Details';
        }

        // Condition 6
        if ($data['loan_date'] == 'Invalid Date') {
            $errcolumns[] = 'Loan Date';
        }

        // Condition 7
        if ($data['profit_type'] != 'Not Found') {
            // Subcondition 7.1
            if ($data['profit_type'] == '1') {
                if (
                    $data['due_method'] == 'Not Found'
                    || $data['due_type'] == 'Not Found'
                    || $data['profit_method'] == 'Not Found'
                ) {
                    $errcolumns[] = 'Due Method Calc or Due Type or Profit Method';
                }
            }

            // Subcondition 7.2
            if ($data['profit_type'] == '2') {
                if ($data['scheme_due_method'] == '' || $data['scheme_id'] == '') {
                    $errcolumns[] = 'Due Method Scheme or Scheme Name';
                }
            }
        } else {
            $errcolumns[] = 'Profit Type';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['interest_rate'])) {
            $errcolumns[] = 'Interest Rate';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['due_period'])) {
            $errcolumns[] = 'Due Period';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['doc_charge'])) {
            $errcolumns[] = 'Document Charge';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['processing_fees'])) {
            $errcolumns[] = 'Processing Fee';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['loan_amount'])) {
            $errcolumns[] = 'Loan Amount Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['principal_amnt'])) {
            $errcolumns[] = 'Principal Amount Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['interest_amnt'])) {
            $errcolumns[] = 'Interest Amount Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['total_amnt'])) {
            $errcolumns[] = 'Total Amount Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['due_amnt'])) {
            $errcolumns[] = 'Due Amount Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['document_charge_calculate'])) {
            $errcolumns[] = 'Document Charge Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['processing_fees_calculate'])) {
            $errcolumns[] = 'Processing Fee Calculation';
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $data['net_cash'])) {
            $errcolumns[] = 'Net Cash Calculation';
        }

        if ($data['dueStart_date'] == 'Invalid Date') {
            $errcolumns[] = 'Due Start From';
        }

        if ($data['maturity_date'] == 'Invalid Date') {
            $errcolumns[] = 'Maturity Date';
        }

        if ($data['issue_date'] == 'Invalid Date') {
            $errcolumns[] = 'Issued Date';
        }

        if ($data['agent_id'] == 'Not Found') {
            $errcolumns[] = 'Agent ID';
        }

        if ($data['issue_person'] == 'Not Found') {
            $errcolumns[] = 'Issue Person';
        }

        if ($data['payment_mode'] == 'Not Found') {
            $errcolumns[] = 'Payment Mode';
        }

        return $errcolumns;
    }


}

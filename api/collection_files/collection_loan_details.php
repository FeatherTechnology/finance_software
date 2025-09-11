<?php
require '../../ajaxconfig.php';
$cp_id = $_POST['cp_id'];

//****************************************************************************************************************************************

// Caution **** Dont Touch any code below..
//get Total amt from ack loan calculation (For monthly interest total amount will not be there, so take principals)*
//get Paid amt from collection table if nothing paid show 0*
//balance amount is Total amt - paid amt*
//get Due amt from ack loan calculation*
//get Pending amt from collection based on last entry against customer profile id (Due amt - paid amt)
//get Payable amt by adding pending and due amount
//get penalty, if due date exceeded put the penalty percentage to the due amt
//get collection charges from collection charges table if exists else 0
$loan_arr = array();
$coll_arr = array();
$response = array(); //Final array to return

$result = $pdo->query("SELECT * FROM `loan_entry_loan_calculation` WHERE cus_profile_id = $cp_id ");
if ($result->rowCount() > 0) {
    $row = $result->fetch();
    $loan_arr = $row;

    if ($loan_arr['total_amnt'] == '' || $loan_arr['total_amnt'] == null) {
        //(For monthly interest total amount will not be there, so take principals)
        $response['total_amt'] = $loan_arr['principal_amnt'];
        $response['loan_type'] = 'interest';
        $loan_arr['loan_type'] = 'interest';
    } else {
        $response['total_amt'] = $loan_arr['total_amnt'];
        $response['loan_type'] = 'emi';
        $loan_arr['loan_type'] = 'emi';
    }
    $response['interest_calculate'] = $loan_arr['interest_calculate'];
    if ($loan_arr['due_amnt'] == '' || $loan_arr['due_amnt'] == null) {
        //(For monthly interest Due amount will not be there, so take interest)
        $response['due_amt'] = $loan_arr['interest_amnt'];
    } else {
        $response['due_amt'] = $loan_arr['due_amnt']; //Due amount will remain same
    }
}
$coll_arr = array();
$result = $pdo->query("SELECT * FROM `collection` WHERE cus_profile_id = $cp_id ");
if ($result->rowCount() > 0) {
    while ($row = $result->fetch()) {
        $coll_arr[] = $row;
    }
    $total_paid = 0;
    $total_paid_princ = 0;
    $total_paid_int = 0;
    $pre_closure = 0;
   $principal_waiver = 0;
    foreach ($coll_arr as $tot) {
        $total_paid += intVal($tot['due_amt_track']); //only calculate due amount not total paid value, because it will have penalty and coll charge also
        $pre_closure += intVal($tot['pre_close_waiver']); //get pre closure value to subract to get balance amount
        $total_paid_princ += intVal($tot['princ_amt_track']);
        $total_paid_int += intVal($tot['int_amt_track']);
        $principal_waiver += intVal($tot['principal_waiver']);

    }
    //total paid amount will be all records again request id should be summed
    $response['total_paid'] = ($loan_arr['loan_type'] == 'emi') ? $total_paid : $total_paid_princ;
    $response['total_waiver'] = ($loan_arr['loan_type'] == 'emi') ? $pre_closure : $principal_waiver;
    $response['total_paid_int'] = $total_paid_int;
    $response['pre_closure'] = $pre_closure;
    $response['principal_waiver'] = $principal_waiver;

    //total amount subracted by total paid amount and subracted with pre closure amount will be balance to be paid
    $response['balance'] = $response['total_amt'] - $response['total_paid'] - $response['total_waiver'];

    if ($loan_arr['loan_type'] == 'interest') {
        $response['due_amt_for1'] = $response['due_amt'];
        $response['due_amt'] = calculateNewInterestAmt($loan_arr['interest_rate'], $response['balance'], $response['interest_calculate']);
    }

    $response = calculateOthers($loan_arr, $response, $pdo);
} else {
    //If collection table dont have rows means there is no payment against that request, so total paid will be 0
    $response['total_paid'] = 0;
    $response['total_paid_int'] = 0;
    $response['pre_closure'] = 0;
    $response['principal_waiver'] = 0;
    //If in collection table, there is no payment means balance amount still remains total amount
    $response['balance'] = $response['total_amt'];

    if ($loan_arr['loan_type'] == 'interest') {
        $response['due_amt_for1'] = $response['due_amt'];
        $response['due_amt'] = calculateNewInterestAmt($loan_arr['interest_rate'], $response['balance'], $response['interest_calculate']);
    }

    $response = calculateOthers($loan_arr, $response, $pdo);
}

//To get the collection charges
$result = $pdo->query("SELECT SUM(coll_charge) as coll_charge FROM `collection_charges` WHERE cus_profile_id = '" . $cp_id . "' ");
$row = $result->fetch();
if ($row['coll_charge'] != null) {

    $coll_charges = $row['coll_charge'];

    $result = $pdo->query("SELECT SUM(coll_charge_track) as coll_charge_track,SUM(coll_charge_waiver) as coll_charge_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
    if ($result->rowCount() > 0) {
        $row = $result->fetch();
        $coll_charge_track = $row['coll_charge_track'];
        $coll_charge_waiver = $row['coll_charge_waiver'];
    } else {
        $coll_charge_track = 0;
        $coll_charge_waiver = 0;
    }

    $response['coll_charge'] = $coll_charges - $coll_charge_track - $coll_charge_waiver;
} else {
    $response['coll_charge'] = 0;
}

function calculateOthers($loan_arr, $response, $pdo)
{
    if (isset($_POST['cp_id'])) {
        $cp_id = $_POST['cp_id'];
    }
    // $cp_id = '11';//***************************************************************************************************************************************************
    $due_start_from = $loan_arr['due_startdate'];
    $maturity_month = $loan_arr['maturity_date'];

    $checkcollection = $pdo->query("SELECT SUM(`due_amt_track`) as totalPaidAmt FROM `collection` WHERE `cus_profile_id` = '$cp_id'"); // To Find total paid amount till Now.
    $checkrow = $checkcollection->fetch();
    $totalPaidAmt = $checkrow['totalPaidAmt'] ?? 0; //null collation operator
    $checkack = $pdo->query("SELECT interest_amnt,due_amnt FROM `loan_entry_loan_calculation` WHERE `cus_profile_id` = '$cp_id'"); // To Find Due Amount.
    $checkAckrow = $checkack->fetch();
    $int_amt_cal = $checkAckrow['interest_amnt'];
    $due_amt = $checkAckrow['due_amnt'];

    if ($loan_arr['due_method'] == 'Monthly' || $loan_arr['scheme_due_method'] == '1') {
        if ($loan_arr['loan_type'] != 'interest') {

            //Convert Date to Year and month, because with date, it will use exact date to loop months, instead of taking end of month
            $due_start_from = date('Y-m', strtotime($due_start_from));
            $maturity_month = date('Y-m', strtotime($maturity_month));

            // Create a DateTime object from the given date
            $maturity_month = new DateTime($maturity_month);
            // Subtract one month from the date
            $maturity_month->modify('-1 month');
            // Format the date as a string
            $maturity_month = $maturity_month->format('Y-m');

            //If Due method is Monthly, Calculate penalty by checking the month has ended or not
            $current_date = date('Y-m');

            $start_date_obj = DateTime::createFromFormat('Y-m', $due_start_from);
            $end_date_obj = DateTime::createFromFormat('Y-m', $maturity_month);
            $current_date_obj = DateTime::createFromFormat('Y-m', $current_date);

            $interval = new DateInterval('P1M'); // Create a one month interval

            //condition start
            $count = 0;
            $loandate_tillnow = 0;
            $countForPenalty = 0;
            $penalty = 0;
            $dueCharge = ($due_amt) ? $due_amt : $int_amt_cal;
            $start = DateTime::createFromFormat('Y-m', $due_start_from);
            $current = DateTime::createFromFormat('Y-m', $current_date);


            for ($i = $start; $i < $current; $start->add($interval)) {
                $loandate_tillnow += 1;
                $toPaytilldate = intval($loandate_tillnow) * intval($dueCharge);
            }

            while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) { // To find loan date count till now from start date.
                $penalty_checking_date  = $start_date_obj->format('Y-m-d'); // This format is for query.. month , year function accept only if (Y-m-d).
                $penalty_date  = $start_date_obj->format('Y-m');


                $checkcollection = $pdo->query("SELECT * FROM `collection` WHERE `cus_profile_id` = '$cp_id' && ((MONTH(coll_date)= MONTH('$penalty_checking_date') || MONTH(trans_date)= MONTH('$penalty_checking_date')) && (YEAR(coll_date)= YEAR('$penalty_checking_date') || YEAR(trans_date)= YEAR('$penalty_checking_date')))");
                $collectioncount = $checkcollection->rowCount(); // Checking whether the collection are inserted on date or not by using penalty_raised_date.

                if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                    $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
                } else {
                    $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
                }
                $row = $result->fetch();
                $penalty_per = $row['overdue']; //get penalty percentage to insert


                if ($loan_arr['loan_type'] == 'interest' and $count == 0) {
                    // if loan type is interest and when this loop for first month crossed then we need to calculate toPaytilldate again
                    // coz for first month interest amount may vary depending on start date of due, so reduce one due amt from it and add the calculated first month interest to it
                    $toPaytilldate = $toPaytilldate - $response['due_amt'] + getTillDateInterest($loan_arr, $response, $pdo, 'fullstartmonth', $cp_id  );
                }
                if ($loan_arr['loan_type'] == 'interest') {
                    $loan_arr[$count]['all_due_amt'] = getTillDateInterest($loan_arr, $start_date_obj, $pdo, 'foreachmonth', $count);
                }

                if ($totalPaidAmt < $toPaytilldate && $collectioncount == 0) {
                    $checkPenalty = $pdo->query("SELECT * from penalty_charges where penalty_date = '$penalty_date' and cus_profile_id = '$cp_id' ");
                    if ($checkPenalty->rowCount() == 0) {
                        $penalty = round((($response['due_amt'] * $penalty_per) / 100) + $penalty);
                        if ($loan_arr['loan_type'] == 'emi') {
                            //if loan type is emi then directly apply penalty when month crossed and above conditions true
                            $qry = $pdo->query("INSERT into penalty_charges (`cus_profile_id`,`penalty_date`, `penalty`, `created_date`) values ('$cp_id','$penalty_date','$penalty',current_timestamp)");
                        } else if ($loan_arr['loan_type'] == 'interest' and  $count != 0) {
                            // if loan type is interest then apply penalty if the loop month is not first
                            // so penalty should not raise, coz a month interest is paid after the month end
                            $qry = $pdo->query("INSERT into penalty_charges (`cus_profile_id`,`penalty_date`, `penalty`, `created_date`) values ('$cp_id','$penalty_date','$penalty',current_timestamp)");
                        }
                    }
                    $countForPenalty++;
                }

                $start_date_obj->add($interval); //increase one month to loop again
                $count++; //Count represents how many months are exceeded
            }
            //condition END

            if ($count > 0) {

                //if Due month exceeded due amount will be as pending with how many months are exceeded and subract pre closure amount if available
                $response['pending'] = ($response['due_amt'] * ($count)) - $response['total_paid'] - $response['pre_closure'];

                // If due month exceeded
                if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                    $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
                } else {
                    $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "'  ");
                }
                $row = $result->fetch();
                $penalty_per = number_format($row['overdue'] * $countForPenalty); //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

                // to get overall penalty paid till now to show pending penalty amount
                $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
                $row = $result->fetch();
                if ($row['penalty'] == null) {
                    $row['penalty'] = 0;
                }
                if ($row['penalty_waiver'] == null) {
                    $row['penalty_waiver'] = 0;
                }
                //to get overall penalty raised till now for this req id
                $result1 = $pdo->query("SELECT SUM(penalty) as penalty FROM `penalty_charges` WHERE cus_profile_id = '" . $cp_id . "' ");
                $row1 = $result1->fetch();
                if ($row1['penalty'] == null) {
                    $penalty = 0;
                } else {
                    $penalty = $row1['penalty'];
                }

                $response['penalty'] = $penalty - $row['penalty'] - $row['penalty_waiver'];

                // $response['till_date_int'] = getTillDateInterest($loan_arr, $response, $pdo, 'from01');
                //Payable amount will be pending amount added with current month due amount
                $response['payable'] = $response['due_amt'] + $response['pending'];

                if ($response['payable'] > $response['balance']) {
                    //if payable is greater than balance then change it as balance amt coz dont collect more than balance
                    //this case will occur when collection status becoms OD
                    $response['payable'] = $response['balance'];
                }
            } else {
                //If still current month is not ended, then pending will be same due amt // pending will be 0 if due date not exceeded
                $response['pending'] = 0; // $response['due_amt'] - $response['total_paid'] - $response['pre_closure'] ;
                //If still current month is not ended, then penalty will be 0
                $response['penalty'] = 0;
                //If still current month is not ended, then payable will be due amt
                // Perform the calculation
                $response['payable'] = $response['due_amt'] - $response['total_paid'] - $response['pre_closure'];
            }
        } else {

            $interest_details = calculateInterestLoan($pdo, $loan_arr, $response);
            // echo $interest_details;
            $all_data = array_merge($response, $interest_details);
            $response = $all_data;
        }
    } else
    if ($loan_arr['scheme_due_method'] == '2') {

        //If Due method is Weekly, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1W'); // Create a one Week interval

        //condition start
        $count = 0;
        $loandate_tillnow = 0;
        $countForPenalty = 0;
        $penalty = 0;

        $dueCharge = ($due_amt) ? $due_amt : $int_amt_cal;
        $start = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $current = DateTime::createFromFormat('Y-m-d', $current_date);

        for ($i = $start; $i < $current; $start->add($interval)) {
            $loandate_tillnow += 1;
            $toPaytilldate = intval($loandate_tillnow) * intval($dueCharge);
        }

        while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) { // To find loan date count till now from start date.

            $penalty_checking_date  = $start_date_obj->format('Y-m-d'); // This format is for query.. month , year function accept only if (Y-m-d).
            $start_date_obj->add($interval);

            $checkcollection = $pdo->query("SELECT * FROM `collection` WHERE `cus_profile_id` = '$cp_id' && ((WEEK(coll_date)= WEEK('$penalty_checking_date') || WEEK(trans_date)= WEEK('$penalty_checking_date')) && (YEAR(coll_date)= YEAR('$penalty_checking_date') || YEAR(trans_date)= YEAR('$penalty_checking_date')))");
            $collectioncount = $checkcollection->rowCount(); // Checking whether the collection are inserted on date or not by using penalty_raised_date.

            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = $row['overdue']; //get penalty percentage to insert
            $count++; //Count represents how many months are exceeded

            if ($totalPaidAmt < $toPaytilldate && $collectioncount == 0) {
                $checkPenalty = $pdo->query("SELECT * from penalty_charges where penalty_date = '$penalty_checking_date' and cus_profile_id = '$cp_id' ");
                if ($checkPenalty->rowCount() == 0) {
                    $penalty = round((($response['due_amt'] * $penalty_per) / 100) + $penalty);
                    $qry = $pdo->query("INSERT into penalty_charges (`cus_profile_id`,`penalty_date`, `penalty`, `created_date`) values ('$cp_id','$penalty_checking_date','$penalty',current_timestamp)");
                }
                $countForPenalty++;
            }
        }
        //condition END

        if ($count > 0) {

            //if Due month exceeded due amount will be as pending with how many months are exceeded and subract pre closure amount if available
            $response['pending'] = ($response['due_amt'] * $count) - $response['total_paid'] - $response['pre_closure'];

            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "'  ");
            }
            $row = $result->fetch();
            $penalty_per = number_format($row['overdue'] * $countForPenalty); //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

            // to get overall penalty paid till now to show pending penalty amount
            $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
            $row = $result->fetch();
            if ($row['penalty'] == null) {
                $row['penalty'] = 0;
            }
            if ($row['penalty_waiver'] == null) {
                $row['penalty_waiver'] = 0;
            }
            //to get overall penalty raised till now for this req id
            $result1 = $pdo->query("SELECT SUM(penalty) as penalty FROM `penalty_charges` WHERE cus_profile_id = '" . $cp_id . "' ");
            $row1 = $result1->fetch();
            if ($row1['penalty'] == null) {
                $penalty = 0;
            } else {
                $penalty = $row1['penalty'];
            }

            // $penalty = intval((($response['due_amt'] * $penalty_per) / 100));

            $response['penalty'] = $penalty - $row['penalty'] - $row['penalty_waiver'];

            //Payable amount will be pending amount added with current month due amount
            $response['payable'] = $response['due_amt'] + $response['pending'];
            if ($response['payable'] > $response['balance']) {
                //if payable is greater than balance then change it as balance amt coz dont collect more than balance
                //this case will occur when collection status becoms OD
                $response['payable'] = $response['balance'];
            }
        } else {
            //If still current month is not ended, then pending will be same due amt // pending will be 0 if due date not exceeded
            $response['pending'] = 0; // $response['due_amt'] - $response['total_paid'] - $response['pre_closure'] ;
            //If still current month is not ended, then penalty will be 0
            $response['penalty'] = 0;
            //If still current month is not ended, then payable will be due amt
            $response['payable'] = $response['due_amt'] - $response['total_paid'] - $response['pre_closure'];
        }
    } elseif ($loan_arr['scheme_due_method'] == '3') {
        //If Due method is Daily, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1D'); // Create a one Week interval

        //condition start
        $count = 0;
        $loandate_tillnow = 0;
        $countForPenalty = 0;
        $penalty = 0;

        $dueCharge = ($due_amt) ? $due_amt : $int_amt_cal;
        $start = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $current = DateTime::createFromFormat('Y-m-d', $current_date);

        for ($i = $start; $i < $current; $start->add($interval)) {
            $loandate_tillnow += 1;
            $toPaytilldate = intval($loandate_tillnow) * intval($dueCharge);
        }

        while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) { // To find loan date count till now from start date.
            $penalty_checking_date  = $start_date_obj->format('Y-m-d'); // This format is for query.. month , year function accept only if (Y-m-d).
            $start_date_obj->add($interval);

            $checkcollection = $pdo->query("SELECT * FROM `collection` WHERE `cus_profile_id` = '$cp_id' && ((DAY(coll_date)= DAY('$penalty_checking_date') || DAY(trans_date)= DAY('$penalty_checking_date')) && (YEAR(coll_date)= YEAR('$penalty_checking_date') || YEAR(trans_date)= YEAR('$penalty_checking_date')))");
            $collectioncount = $checkcollection->rowCount(); // Checking whether the collection are inserted on date or not by using penalty_raised_date.

            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = $row['overdue']; //get penalty percentage to insert
            $count++; //Count represents how many months are exceeded

            if ($totalPaidAmt < $toPaytilldate && $collectioncount == 0) {
                $checkPenalty = $pdo->query("SELECT * from penalty_charges where penalty_date = '$penalty_checking_date' and cus_profile_id = '$cp_id' ");
                if ($checkPenalty->rowCount() == 0) {
                    $penalty = round((($response['due_amt'] * $penalty_per) / 100) + $penalty);
                    $qry = $pdo->query("INSERT into penalty_charges (`cus_profile_id`,`penalty_date`, `penalty`, `created_date`) values ('$cp_id','$penalty_checking_date','$penalty',current_timestamp)");
                }
                $countForPenalty++;
            }
        }
        //condition END

        if ($count > 0) {
            //if Due month exceeded due amount will be as pending with how many months are exceeded and subract pre closure amount if available
            $response['pending'] = ($response['due_amt'] * $count) - $response['total_paid'] - $response['pre_closure'];

            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = number_format($row['overdue'] * $countForPenalty); //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

            // to get overall penalty paid till now to show pending penalty amount
            $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
            $row = $result->fetch();
            if ($row['penalty'] == null) {
                $row['penalty'] = 0;
            }
            if ($row['penalty_waiver'] == null) {
                $row['penalty_waiver'] = 0;
            }
            //to get overall penalty raised till now for this req id
            $result1 = $pdo->query("SELECT SUM(penalty) as penalty FROM `penalty_charges` WHERE cus_profile_id = '" . $cp_id . "' ");
            $row1 = $result1->fetch();
            if ($row1['penalty'] == null) {
                $penalty = 0;
            } else {
                $penalty = $row1['penalty'];
            }

            // $penalty = intval((($response['due_amt'] * $penalty_per) / 100));

            $response['penalty'] = $penalty - $row['penalty'] - $row['penalty_waiver'];

            //Payable amount will be pending amount added with current month due amount
            $response['payable'] = $response['due_amt'] + $response['pending'];
            if ($response['payable'] > $response['balance']) {
                //if payable is greater than balance then change it as balance amt coz dont collect more than balance
                //this case will occur when collection status becoms OD
                $response['payable'] = $response['balance'];
            }
        } else {
            //If still current month is not ended, then pending will be same due amt// pending will be 0 if due date not exceeded
            $response['pending'] = 0; //$response['due_amt'] - $response['total_paid'] - $response['pre_closure'] ;
            //If still current month is not ended, then penalty will be 0
            $response['penalty'] = 0;
            //If still current month is not ended, then payable will be due amt
            $response['payable'] = $response['due_amt'] - $response['total_paid'] - $response['pre_closure'];
        }
    }

    if ($response['pending'] < 0) {
        $response['pending'] = 0;
    }
    if ($response['payable'] < 0) {
        $response['payable'] = 0;
    }
    return $response;
}

function calculateNewInterestAmt($int_rate, $balance, $calculate_method)
{
    if ($calculate_method == 'Month') {
        $int = $balance * ($int_rate / 100);
    } else if ($calculate_method == 'Days') {
        $int = ($balance * ($int_rate / 100) / 30);
    }

    $curInterest = ceil($int / 5) * 5; //to increase Interest to nearest multiple of 5
    if ($curInterest < $int) {
        $curInterest += 5;
    }
    $response = $curInterest;

    return $response;
}
function calculateInterestLoan($pdo, $loan_arr, $response)
{
    if (isset($_POST['cp_id'])) {
        $cp_id = $_POST['cp_id'];
    }
    $due_start_from = $loan_arr['loan_date'];
    $maturity_month = $loan_arr['maturity_date'];

    //Convert Date to Year and month, because with date, it will use exact date to loop months, instead of taking end of month
    $due_start_from = date('Y-m', strtotime($due_start_from));
    $maturity_month = date('Y-m', strtotime($maturity_month));

    // Create a DateTime object from the given date
    $maturity_month = new DateTime($maturity_month);
    // Subtract one month from the date
    $maturity_month->modify('-1 month');
    // Format the date as a string
    $maturity_month = $maturity_month->format('Y-m');

    //If Due method is Monthly, Calculate penalty by checking the month has ended or not
    $current_date = date('Y-m');

    $start_date_obj = DateTime::createFromFormat('Y-m', $due_start_from);
    $end_date_obj = DateTime::createFromFormat('Y-m', $maturity_month);
    $current_date_obj = DateTime::createFromFormat('Y-m', $current_date);

    $interval = new DateInterval('P1M'); // Create a one month interval

    //condition start
    $count = 0;


    while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) {

        $start_date_obj->add($interval); //increase one month to loop again
        $count++; //Count represents how many months are exceeded
    }
    if ($start_date_obj >= $end_date_obj) {
        $count++; //because if the maturity date crossed the pending amount should have the maturity month's amount also so add 1month to count
    }

    if ($count > 0) {
        $interest_paid = getPaidInterest($pdo, $cp_id);

        $res['payable'] = payableCalculation($pdo, $loan_arr, $response) - $interest_paid;
        $res['till_date_int'] = getTillDateInterest($loan_arr, $response, $pdo, 'curmonth', $cp_id) - $interest_paid;
        $res['pending'] = pendingCalculation($pdo, $loan_arr, $response, $cp_id) - $interest_paid;

        if ($res['pending'] < 0) {
            $res['pending'] = 0;
        }
        if ($res['payable'] < 0) {
            $res['payable'] = 0;
        }

        $res['penalty'] = getPenaltyCharges($pdo, $cp_id);
    } else {
        //in this calculate till date Interest when month are not crossed for due starting month
        $res['till_date_int'] = getTillDateInterest($loan_arr, $response, $pdo, 'forstartmonth', $cp_id);
        $res['pending'] = 0;
        $res['payable'] =  0;
        $res['penalty'] = 0;
    }

    $res['payable'] = ceilAmount($res['payable']);
    $res['pending'] = ceilAmount($res['pending']);
    $res['till_date_int'] = ceilAmount($res['till_date_int']);
    return $res;
}

function dueAmtCalculation($pdo, $start_date, $end_date, $due_amt, $loan_arr, $status, $cp_id)
{
    $start = new DateTime($start_date->format('Y-m-d'));
    $end = new DateTime($end_date->format('Y-m-d'));

    $interest_calculate = $loan_arr['interest_calculate'];
    $int_rate = $loan_arr['interest_rate'];
    $result = 0;
    $monthly_interest_data = [];

    $loanRow = $pdo->query("SELECT loan_amnt FROM loan_entry_loan_calculation WHERE cus_profile_id = '" . $cp_id . "'")->fetch(PDO::FETCH_ASSOC);
    $default_balance = $loanRow['loan_amnt'];

    $collections = $pdo->query("SELECT princ_amt_track, coll_date FROM collection 
        WHERE cus_profile_id = '" . $cp_id . "' AND princ_amt_track != '' ORDER BY coll_date ASC")->fetchAll();

    if (!empty($collections)) {

        // <---------------------------------------------------------------- IF COLLECTIONS EXIST ------------------------------------------------------------>

        $collection_index = 0;
        $current_balance = $default_balance;

        while ($start <= $end) {
            $today_str = $start->format('Y-m-d');
            $month_key = $start->format('Y-m-01');
            $paid_principal_today = 0;

            while ($collection_index < count($collections)) {
                $collection = $collections[$collection_index];
                $coll_date = (new DateTime($collection['coll_date']))->format('Y-m-d');
                if ($coll_date == $today_str) {
                    $paid_principal_today += (float)$collection['princ_amt_track'];
                    $collection_index++;
                } else {
                    break;
                }
            }

            $current_balance -= $paid_principal_today;

            $interest_today = calculateNewInterestAmt($int_rate, $current_balance, $interest_calculate);

            if ($interest_calculate === 'Days') {
                $result += $interest_today;
                $monthly_interest_data[$month_key] = ($monthly_interest_data[$month_key] ?? 0) + $interest_today;
            } else {
                $days_in_month = (int)$start->format('t');
                $daily_interest = $interest_today / $days_in_month;
                $result += $daily_interest;
                $monthly_interest_data[$month_key] = ($monthly_interest_data[$month_key] ?? 0) + $daily_interest;
            }

            $start->modify('+1 day');
        }
    } else {
        $monthly_interest_data = [];

        if ($interest_calculate == 'Month') {
            while ($start->format('Y-m') <= $end->format('Y-m')) {
                $month_key = $start->format('Y-m-d');
                $dueperday = $due_amt / intval($start->format('t'));

                if ($status != 'pending') {
                    if ($start->format('m') != $end->format('m')) {
                        $new_end_date = clone $start;
                        $new_end_date->modify('last day of this month');
                        $cur_result = (($start->diff($new_end_date))->days + 1) * $dueperday;
                    } else {
                        $cur_result = (($start->diff($end))->days + 1) * $dueperday;
                    }
                } else {
                    $new_end = clone $start;
                    $new_end->modify("last day of this month");
                    $cur_result = (($start->diff($new_end))->days + 1) * $dueperday;
                }

                $result += $cur_result;
                $monthly_interest_data[$month_key] = ($monthly_interest_data[$month_key] ?? 0) + $cur_result;
                $start->modify('+1 month');
                $start->modify('first day of this month');
            }
        } else if ($interest_calculate == 'Days') {
            while ($start->format('Y-m-d') <= $end->format('Y-m-d')) {
                $month_key = $start->format('Y-m-d');
                $dueperday = $due_amt;
                $result += $dueperday;
                $monthly_interest_data[$month_key] = ($monthly_interest_data[$month_key] ?? 0) + $dueperday;

                $start->modify('+1 day');
            }
        }
    }

    // <------------------------------------------------------------------- Penalty Logic ----------------------------------------------------------------->

    if ($status === 'pending') {
        $penaltyRow = $penaltyRow = $pdo->query("SELECT penalty_type, overdue_penalty FROM loan_category_creation WHERE `id` = '" . $loan_arr['loan_category'] . "' ")->fetch(PDO::FETCH_ASSOC);

        $penalty_val = $penaltyRow['overdue_penalty'] ?? 0;
        $penalty_type = strtolower(trim($penaltyRow['penalty_type'] ?? 'percentage'));

        foreach ($monthly_interest_data as $penalty_date => $cur_result) {
            $paid_interest = getPaidInterest($pdo, $cp_id);
            $unpaid_interest = max(0, $cur_result - $paid_interest);

            if ($unpaid_interest > 0 && $penalty_val > 0) {
                $penalty = ($penalty_type === 'amt') ? round($penalty_val) : round(($unpaid_interest * $penalty_val) / 100);

                $checkPenalty = $pdo->query("SELECT 1 FROM penalty_charges WHERE penalty_date = '$penalty_date' AND cus_profile_id = '" . $cp_id . "'");
                if ($checkPenalty->rowCount() == 0) {
                    $insertQry = $pdo->query("INSERT INTO penalty_charges (cus_profile_id, penalty_date, penalty, created_date) VALUES ('$cp_id', '$penalty_date',  $penalty, NOW())");
                }
            }
        }
    }
    return $result;
}
function payableCalculation($pdo, $loan_arr, $response)
{
    $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));
    $cur_date = new DateTime(date('Y-m-d'));
    $result = 0;
    if (isset($_POST['cp_id'])) {
        $cp_id = $_POST['cp_id'];
    }
    if ($response['interest_calculate'] == "Month") {
        $last_month = clone $cur_date;
        $last_month->modify('-1 month'); // Last month same date
        $st_date = clone $issued_date;

        while ($st_date->format('Y-m') <= $last_month->format('Y-m')) {
            $end_date = clone $st_date;
            $end_date->modify('last day of this month');
            $start = clone $st_date; // Due to mutation in function

            $result += dueAmtCalculation($pdo, $start, $end_date, $response['due_amt'], $loan_arr, 'payable', $cp_id);

            $st_date->modify('+1 month');
            $st_date->modify('first day of this month');
        }
    } elseif ($response['interest_calculate'] == "Days") {
        $last_date = clone $cur_date;
        $last_date->modify('-1 month'); // Last month same date
        $st_date = clone $issued_date;

        while ($st_date->format('Y-m') <= $last_date->format('Y-m')) {
            $end_date = clone $st_date;
            $end_date->modify('last day of this month');
            $start = clone $st_date;

            $result += dueAmtCalculation($pdo, $start, $end_date, $response['due_amt'], $loan_arr, 'payable', $cp_id);
            $st_date->modify('+1 month');
            $st_date->modify('first day of this month');
        }
    }

    return $result;
}

function pendingCalculation($pdo, $loan_arr, $response, $cp_id)
{
    $pending = getTillDateInterest($loan_arr, $response, $pdo, 'pendingmonth', $cp_id);
    return $pending;
}
function getTillDateInterest($loan_arr, $response, $pdo, $data, $cp_id)
{

    if ($data == 'forstartmonth') {

        //to calculate till date Interest if loan is interst based
        if ($loan_arr['loan_type'] == 'interest') {

            //get the loan isued month's date count
            $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));

            //current month's total date
            $cur_date = new DateTime(date('Y-m-d'));

            $result = dueAmtCalculation($pdo, $issued_date, $cur_date, $response['due_amt'], $loan_arr, '', $cp_id);
            // $result = (($issued_date->diff($cur_date))->days) * $issue_month_due;

            //to increase till date Interest to nearest multiple of 5
            $cur_amt = ceil($result / 5) * 5; //ceil will set the number to nearest upper integer//i.e ceil(121/5)*5 = 125
            if ($cur_amt < $result) {
                $cur_amt += 5;
            }
            $result = $cur_amt;
        }
        return $result;
    }
    if ($data == 'curmonth') {
        $cur_date = new DateTime(date('Y-m-d'));
        $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));


        $result = dueAmtCalculation($pdo, $issued_date, $cur_date, $response['due_amt'], $loan_arr, 'TDI', $cp_id);
        return $result;
    }
    if ($data == 'pendingmonth') {
        //for pending value check, goto 2 months before
        //bcoz last month value is on payable, till date int will be on cur date
        $issued_date = new DateTime(date('Y-m-d', strtotime($loan_arr['loan_date'])));
        $cur_date = new DateTime(date('Y-m-d'));
        $cur_date->modify('-2 months');
        $cur_date->modify('last day of this month');
        $result = 0;

        if ($issued_date->format('m') <= $cur_date->format('m')) {
            $result = dueAmtCalculation($pdo, $issued_date, $cur_date, $response['due_amt'], $loan_arr, 'pending', $cp_id);
        }
        return $result;
    }

    return $response;
}
function getPaidInterest($pdo, $cp_id)
{
    $qry = $pdo->query("SELECT COALESCE(SUM(int_amt_track), 0) + COALESCE(SUM(interest_waiver), 0) AS int_paid  FROM `collection` WHERE cus_profile_id = '$cp_id' and (int_amt_track != '' and int_amt_track IS NOT NULL) ");
    $int_paid = $qry->fetch()['int_paid'];
    return intVal($int_paid);
}
function getPenaltyCharges($pdo, $cp_id)
{
    // to get overall penalty paid till now to show pending penalty amount
    $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '$cp_id' ");
    $row = $result->fetch();
    if ($row['penalty'] == null) {
        $row['penalty'] = 0;
    }
    if ($row['penalty_waiver'] == null) {
        $row['penalty_waiver'] = 0;
    }
    //to get overall penalty raised till now for this req id
    $result1 = $pdo->query("SELECT SUM(penalty) as penalty FROM `penalty_charges` WHERE cus_profile_id = '$cp_id' ");
    $row1 = $result1->fetch();
    if ($row1['penalty'] == null) {
        $penalty = 0;
    } else {
        $penalty = $row1['penalty'];
    }

    return $penalty - $row['penalty'] - $row['penalty_waiver'];
}
function ceilAmount($amt)
{
    $cur_amt = ceil($amt / 5) * 5; //ceil will set the number to nearest upper integer//i.e ceil(121/5)*5 = 125
    if ($cur_amt < $amt) {
        $cur_amt += 5;
    }
    return $cur_amt;
}


echo json_encode($response);

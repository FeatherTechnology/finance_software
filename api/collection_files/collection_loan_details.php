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

    foreach ($coll_arr as $tot) {
        $total_paid += intVal($tot['due_amt_track']); //only calculate due amount not total paid value, because it will have penalty and coll charge also
        $pre_closure += intVal($tot['pre_close_waiver']); //get pre closure value to subract to get balance amount
        $total_paid_princ += intVal($tot['princ_amt_track']);
        $total_paid_int += intVal($tot['int_amt_track']);
    }
    //total paid amount will be all records again request id should be summed
    $response['total_paid'] = ($loan_arr['loan_type'] == 'emi') ? $total_paid : $total_paid_princ;
    $response['total_paid_int'] = $total_paid_int;
    $response['pre_closure'] = $pre_closure;

    //total amount subracted by total paid amount and subracted with pre closure amount will be balance to be paid
    $response['balance'] = $response['total_amt'] - $response['total_paid'] - $pre_closure;

    if ($loan_arr['loan_type'] == 'interest') {
        $response['due_amt_for1'] = $response['due_amt'];
        $response['due_amt'] = calculateNewInterestAmt($loan_arr['interest_rate'], $response['balance']);
    }

    $response = calculateOthers($loan_arr, $response, $pdo);
} else {
    //If collection table dont have rows means there is no payment against that request, so total paid will be 0
    $response['total_paid'] = 0;
    $response['total_paid_int'] = 0;
    $response['pre_closure'] = 0;
    //If in collection table, there is no payment means balance amount still remains total amount
    $response['balance'] = $response['total_amt'];

    if ($loan_arr['loan_type'] == 'interest') {
        $response['due_amt_for1'] = $response['due_amt'];
        $response['due_amt'] = calculateNewInterestAmt($loan_arr['interest_rate'], $response['balance']);
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
                $toPaytilldate = $toPaytilldate - $response['due_amt'] + getTillDateInterest($loan_arr, $response, $pdo, 'fullstartmonth', '');
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

            if ($loan_arr['loan_type'] == 'interest') {

                $response['pending'] = (($response['due_amt'] * ($count)) - $response['due_amt'] + getTillDateInterest($loan_arr, $response, $pdo, 'fullstartmonth', '')) - $response['total_paid_int'];
            } else {

                //if Due month exceeded due amount will be as pending with how many months are exceeded and subract pre closure amount if available
                $response['pending'] = ($response['due_amt'] * ($count)) - $response['total_paid'] - $response['pre_closure'];
            }

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


            //Payable amount will be pending amount added with current month due amount
            $response['payable'] = $response['due_amt'] + $response['pending'];


            if ($loan_arr['loan_type'] == 'interest') { // if loan type is interest then we need to calculate pending and payable again

                if ($count == 1) {
                    // if this condition true then, first month of the start date only has been ended
                    // so we need to calculate only the first month interest , not whole interest amount as payable

                    $response['payable'] = $response['pending'];


                    //pending amount will remain zero , coz usually we pay ended month's interest amount only in next month
                    //so when only one month is exceeded, that not the pending 
                    $response['pending'] =  0;
                } else {

                    $response['payable'] =  $response['pending'];

                    if ($count >= 2) {

                        $response['pending'] =  $response['pending'] - $response['due_amt'];
                    }
                }
            }

            if ($response['payable'] > $response['balance']) {
                //if payable is greater than balance then change it as balance amt coz dont collect more than balance
                //this case will occur when collection status becoms OD
                $response['payable'] = $response['balance'];
            }

            //in this calculate till date interest when month are crossed for current month
            $response['till_date_int'] = getTillDateInterest($loan_arr, $response, $pdo, 'from01', '');
        } else {
            //If still current month is not ended, then pending will be same due amt // pending will be 0 if due date not exceeded
            $response['pending'] = 0; // $response['due_amt'] - $response['total_paid'] - $response['pre_closure'] ;
            //If still current month is not ended, then penalty will be 0
            $response['penalty'] = 0;
            //If still current month is not ended, then payable will be due amt
            $response['payable'] = $response['due_amt'] - $response['total_paid'] - $response['pre_closure'];

            if ($loan_arr['loan_type'] == 'interest') {
                $response['payable'] =  0;
            }

            //in this calculate till date interest when month are not crossed for due starting month
            $response['till_date_int'] = getTillDateInterest($loan_arr, $response, $pdo, 'forstartmonth', '');
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

function calculateNewInterestAmt($int_rate, $balance)
{
    //to calculate current interest amount based on current balance value//bcoz interest will be calculated based on current balance amt only for interest loan
    $int = $balance * ($int_rate / 100);
    $curInterest = ceil($int / 5) * 5; //to increase Interest to nearest multiple of 5
    if ($curInterest < $int) {
        $curInterest += 5;
    }
    $response = $curInterest;

    return $response;
}

function getTillDateInterest($loan_arr, $response, $pdo, $data, $count)
{

    if ($data == 'from01') {
        //in this calculate till date interest when month are crossed for current month

        //to calculate till date interest if loan is interst based
        if ($loan_arr['loan_type'] == 'interest') {

            // Get the current month's count of days
            $currentMonthCount = date('t');
            // divide current interest amt for one day of current month
            $amtperDay = $response['due_amt'] / intVal($currentMonthCount);

            $st_date = new DateTime(date('Y-m-01')); // start date
            $tdate = new DateTime(date('Y-m-d') . '+1 day'); //current date
            // $tdate = $tdate->modify('+1 day');//current date +1
            // Calculate the interval between the two dates
            $date_diff = $st_date->diff($tdate);
            // Get the number of days from the interval
            $numberOfDays = $date_diff->days;
            $response = $amtperDay * $numberOfDays;

            //to increase till date Interest to nearest multiple of 5
            $cur_amt = ceil($response / 5) * 5; //ceil will set the number to nearest upper integer//i.e ceil(121/5)*5 = 125
            if ($cur_amt < $response) {
                $cur_amt += 5;
            }
            $response = $cur_amt;
        }
    } else if ($data == 'forstartmonth') {
        //if condition is true then this is , 2 months has been completed.
        //so the pending amt will be only the first month's complete interest amount


        //to calculate till date interest if loan is interst based
        if ($loan_arr['loan_type'] == 'interest') {

            // Get the current month's count of days
            $currentMonthCount = date('t', strtotime($loan_arr['due_startdate']));
            // divide current interest amt for one day of current month
            $amtperDay = $response['due_amt'] / intVal($currentMonthCount);

            $st_date = new DateTime(date('Y-m-d', strtotime($loan_arr['due_startdate']))); // start date
            $tdate = new DateTime(date('Y-m-d') . '+1 day'); //current date
            // $tdate = $tdate->modify('+1 day');//current date +1
            // Calculate the interval between the two dates
            $date_diff = $st_date->diff($tdate);
            // Get the number of days from the interval
            $numberOfDays = $date_diff->days;
            $response = $amtperDay * $numberOfDays;

            //to increase till date Interest to nearest multiple of 5
            $cur_amt = ceil($response / 5) * 5; //ceil will set the number to nearest upper integer//i.e ceil(121/5)*5 = 125
            if ($cur_amt < $response) {
                $cur_amt += 5;
            }
            $response = $cur_amt;

            //if today date is less than start date means make till date interest as 0 else it will show some amount as the different shows
            if ($tdate < $st_date) {
                $response = 0;
            }
        }
    } else if ($data == 'fullstartmonth') {
        //in this calculate till date interest when month are not crossed for due starting month

        //to calculate till date interest if loan is interst based
        if ($loan_arr['loan_type'] == 'interest') {
            // Get the current month's count of days
            $currentMonthCount = date('t', strtotime($loan_arr['due_startdate']));
            // divide current interest amt for one day of current month
            $amtperDay = $response['due_amt_for1'] / intVal($currentMonthCount);

            $st_date = new DateTime(date('Y-m-d', strtotime($loan_arr['due_startdate']))); // start date
            $tdate = new DateTime(date('Y-m-t', strtotime($loan_arr['due_startdate']))); //will take last date of mentioned date's month
            // $tdate = $tdate->modify('+1 day');//current date +1
            // Calculate the interval between the two dates
            $date_diff = $st_date->diff($tdate);
            // Get the number of days from the interval
            $numberOfDays = $date_diff->days;
            $response = ceil($amtperDay * $numberOfDays);

            // //to increase till date Interest to nearest multiple of 5
            // $cur_amt = ceil($response / 5) * 5; //ceil will set the number to nearest upper integer//i.e ceil(121/5)*5 = 125
            // if ($cur_amt < $response) {
            //     $cur_amt += 5;
            // }
            // $response = $cur_amt;
        }
    } else if ($data == 'foreachmonth') {
        if (isset($_POST['cp_id'])) {
            $cp_id = $_POST['cp_id'];
        }

        $start_date = $response->format('Y-m-d');
        $end_date = $response->format('Y-m-t');
        if ($count == 0) { //if count is zero then take first collection entry to calc first month's due amt
            $sql = $pdo->query("SELECT bal_amt, princ_amt_track from collection where cus_profile_id = $cp_id  ORDER BY coll_date ASC ");
            if ($sql->rowCount()) {
                $row = $sql->fetch();
                $bal_amt = $row['bal_amt']; //this is the balance amt for first month

                //calculate interest for that month based on balance amt
                $interest = $bal_amt * ($loan_arr['interest_rate'] / 100);

                // Get the current month's count of days
                $currentMonthCount = date('t', strtotime($start_date));
                $amtperDay = $interest / intVal($currentMonthCount);

                $start_date = new DateTime($start_date); // start date
                $end_date = new DateTime($end_date); //last date of month
                $date_diff = $start_date->diff($end_date);
                $numberOfDays = $date_diff->days;
                $response = ceil($amtperDay * $numberOfDays);
            }
        } elseif ($count > 0) {
            //if count is one then take first collection entry to calc second month's due amt from start date to collection date first
            //then from that collection date to next collection date will be that particular date's due amt
            // else if only one entry or empty in the current month then take bal amt from next collection when available to calculate curr month's due
            //if various collection entry available then take as before said then sum all to get cur month's overall interest to show next month

            $sql = $pdo->query("SELECT bal_amt, princ_amt_track,date(coll_date) from collection where cus_profile_id = $cp_id and (month(coll_date) = month('$start_date') and year(coll_date) = year('$start_date')) ORDER BY coll_date ASC ");
            if ($sql->rowCount()) {
                $i = 0;
                $response = 0;
                while ($row = $sql->fetch()) {
                    $bal_amt = $row['bal_amt']; //this is the balance amt for first month
                    $coll_date = $row['date(coll_date)'];

                    //calculate interest for that month based on balance amt
                    $interest = $bal_amt * ($loan_arr['interest_rate'] / 100);

                    // Get the current month's count of days
                    $currentMonthCount = date('t', strtotime($start_date));
                    $amtperDay = $interest / intVal($currentMonthCount);

                    if ($i == 0) {
                        // set start date as first date of month, coz first time should calculate for month's start point to coll date
                        $start_date = new DateTime(date('Y-m-01', strtotime($start_date)));
                    } else {
                        // change start date as collection date , coz we dont need to calculate due from start of month
                        $start_date = new DateTime(date('Y-m-d', strtotime($start_date)));
                    }

                    $end_date = new DateTime($coll_date); //setting collection date as end date to calculate interst from day 1 to collection date
                    $date_diff = $start_date->diff($end_date);
                    $numberOfDays = $date_diff->days;
                    $response = $response + ceil($amtperDay * $numberOfDays);

                    $start_date = $coll_date; //changing start date as coll date, coz next period until next collection due will be changed 

                    $i++;
                }
                //when loop completed then calculate rest of the month's due amt by taking next collection entry's bal amt. validate that with last collection date above
                $sql = $pdo->query("SELECT bal_amt, princ_amt_track,date(coll_date) from collection where cus_profile_id = $cp_id and month(coll_date) > month('$start_date')  ORDER BY coll_date ASC ");
                if ($sql->rowCount()) {
                    $row = $sql->fetch();
                    $bal_amt = $row['bal_amt'];
                    $coll_date = $row['date(coll_date)'];

                    //calculate interest for that month based on balance amt
                    $interest = $bal_amt * ($loan_arr['interest_rate'] / 100);
                    // Get the current month's count of days
                    $currentMonthCount = date('t', strtotime($start_date));
                    $amtperDay = $interest / intVal($currentMonthCount);
                    // change start date as collection date , coz we dont need to calculate due from start of month
                    $end_date = new DateTime(date('Y-m-t', strtotime($start_date))); //setting end date as start month's end date
                    $start_date = new DateTime(date('Y-m-d', strtotime($start_date))); // taking last collection date from above loop
                    $date_diff = $start_date->diff($end_date);
                    $numberOfDays = $date_diff->days;
                    // echo ceil($amtperDay * $numberOfDays);die;
                    $response = $response + ceil($amtperDay * $numberOfDays);
                }
            }
        }
    }
    return $response;
}

echo json_encode($response);

<?php
require '../../ajaxconfig.php';

if (isset($_POST['cus_id'])) {
    $cus_id = preg_replace('/\D/', '', $_POST['cus_id']);
    // $cus_id = $_POST['cus_id'];
}

$cp_arr = array();
$qry = $pdo->query("SELECT li.cus_profile_id as cp_id FROM loan_issue li JOIN customer_status cs ON li.cus_profile_id = cs.cus_profile_id  where li.cus_id = '$cus_id' and cs.status =7  ORDER BY li.cus_profile_id DESC ");
while ($row = $qry->fetch()) {
    $cp_arr[] = $row['cp_id'];
}


//get Total amt from ack loan calculation (For monthly interest total amount will not be there, so take principals)*
//get Paid amt from collection table if nothing paid show 0*
//balance amount is Total amt - paid amt*
//get Due amt from ack loan calculation*
//get Pending amt from collection based on last entry against request id (Due amt - paid amt)
//get Payable amt by adding pending and due amount
//get penalty, if due date exceeded put the penalty percentage to the due amt
//get collection charges from collection charges table if exists else 0
$loan_arr = array();
$coll_arr = array();
$response = array(); //Final array to return

$cp_id = 0;
$i = 0;
foreach ($cp_arr as $cp_id) {
        
        $response['cp_id'][$i] = $cp_id;

        $result = $pdo->query("SELECT * FROM `loan_entry_loan_calculation` WHERE cus_profile_id = $cp_id ");
    if ($result->rowCount() > 0) {
        $row = $result->fetch();
        $loan_arr = $row;

        if ($loan_arr['total_amnt'] == '' || $loan_arr['total_amnt'] == null) {
            //(For monthly interest total amount will not be there, so take principals)
            $response['total_amt'] = intVal($loan_arr['principal_amnt']);
            $response['loan_type'] = 'interest';
            $loan_arr['loan_type'] = 'interest';
        } else {
            $response['total_amt'] = intVal($loan_arr['total_amnt']);
            $response['loan_type'] = 'emi';
            $loan_arr['loan_type'] = 'emi';
        }


        if ($loan_arr['due_amnt'] == '' || $loan_arr['due_amnt'] == null) {
            //(For monthly interest Due amount will not be there, so take interest)
            $response['due_amt'] = intVal($loan_arr['interest_amnt']);
        } else {
            $response['due_amt'] = intVal($loan_arr['due_amnt']); //Due amount will remain same
        }
    }
    $coll_arr = array();
    $result = $pdo->query("SELECT * FROM `collection` WHERE cus_profile_id ='" . $cp_id . "' ");
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
            $total_paid_princ += intVal($tot['princ_amt_track']);
            $total_paid_int += intVal($tot['int_amt_track']);
            $pre_closure += intVal($tot['pre_close_waiver']); //get pre closure value to subract to get balance amount
        }
        //total paid amount will be all records again cp id should be summed
        $response['total_paid'] = ($loan_arr['loan_type'] == 'emi') ? $total_paid : $total_paid_princ;
        $response['total_paid_int'] = $total_paid_int;
        $response['pre_closure'] = $pre_closure;

        //total amount subracted by total paid amount and subracted with pre closure amount will be balance to be paid
        $response['balance'] = $response['total_amt'] - $response['total_paid'] - $pre_closure;

        if ($loan_arr['loan_type'] == 'interest') {
            $response['due_amt'] = calculateNewInterestAmt($loan_arr, $response);
        }

        $response = calculateOthers($loan_arr, $response, $pdo, $cp_id);
    } else {
        //If collection table dont have rows means there is no payment against that request, so total paid will be 0
        $response['total_paid'] = 0;
        $response['total_paid_int'] = 0;
        $response['pre_closure'] = 0;

        //If in collection table, there is no payment means balance amount still remains total amount
        $response['balance'] = $response['total_amt'];

        if ($loan_arr['loan_type'] == 'interest') {
            $response['due_amt'] = calculateNewInterestAmt($loan_arr, $response);
        }

        $response = calculateOthers($loan_arr, $response, $pdo, $cp_id);
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
    
    $response['payable_as_req'][$i] = $response['payable'];
    $response['pending_as_req'][$i] = $response['pending'];

    //Pending Check
    if ($response['pending'] > 0 && $response['count_of_month'] != 0) {
        $response['pending_customer'][$i] = true;
    } else {
        $response['pending_customer'][$i] = false;
    }
    //OD check
    if ($response['od'] == true) {
        $response['od_customer'][$i] = true;
    } else {
        $response['od_customer'][$i] = false;
    }

    //Due nill Check
    if ($response['due_nil'] == true) {
        $response['due_nil_customer'][$i] = true;
    } else {
        $response['due_nil_customer'][$i] = false;
    }

    $response['balAmnt'][$i] =  $response['balance'];

    $i++;
}

//for knowing the customer status for due followup screen
//this will give the customer's sub status in the order of Legal, Error, OD, Due Nill, Pending, Current
$response['follow_cus_sts'] = checkStatusOfCustomer($response, $loan_arr, $cus_id, $pdo);

function calculateOthers($loan_arr, $response, $pdo, $cp_id)
{
    //**************************************************************************************************************************************************
    $due_start_from = $loan_arr['due_startdate'];
    $maturity_month = $loan_arr['maturity_date'];

    if ($loan_arr['due_method'] == 'Monthly' || $loan_arr['scheme_due_method'] == '1') {
        //Convert Date to Year and month, because with date, it will use exact date to loop months, instead of taking end of month
        $due_start_from = date('Y-m', strtotime($due_start_from));
        $maturity_month = date('Y-m', strtotime($maturity_month));

        //If Due method is Monthly, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m');


        $start_date_obj = DateTime::createFromFormat('Y-m', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m', $current_date);

        $interval = new DateInterval('P1M'); // Create a one month interval

        $count = 0;

        while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) {
            //To raise penalty in seperate table
            $penalty_raised_date  = $start_date_obj->format('Y-m');
            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = $row['overdue']; //get penalty percentage to insert
            $penalty = number_format(($response['due_amt'] * $penalty_per) / 100);

            $start_date_obj->add($interval);
            $count++; //Count represents how many months are exceeded
        }

        $response['count_of_month'] = $count;
        //To check over due, if current date is greater than maturity minth, then i will be OD
        if ($current_date_obj > $end_date_obj) {
            $response['od'] = true;
        } else {
            $response['od'] = false;
        }

        //To check whether due has been nil with other charges

        $qry = $pdo->query("SELECT c.due_amt_track, c.pre_close_waiver, c.princ_amt_track, pc.penalty, pc.paid_amnt AS paid_amntpc, pc.waiver_amnt AS waiver_amntpc, cc.coll_charge, cc.paid_amnt AS paid_amntcc, cc.waiver_amnt AS waiver_amntcc 
        FROM ( SELECT cus_profile_id, SUM(due_amt_track) AS due_amt_track,SUM(pre_close_waiver) AS pre_close_waiver,SUM(princ_amt_track) AS princ_amt_track FROM collection WHERE cus_profile_id = '$cp_id' ) c 
        LEFT JOIN ( SELECT cus_profile_id, SUM(penalty) AS penalty, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt FROM penalty_charges WHERE cus_profile_id = '$cp_id' GROUP BY cus_profile_id ) pc ON c.cus_profile_id = pc.cus_profile_id 
        LEFT JOIN ( SELECT cus_profile_id, SUM(coll_charge) AS coll_charge, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt FROM collection_charges 
        WHERE cus_profile_id = '$cp_id' GROUP BY cus_profile_id ) cc ON c.cus_profile_id = cc.cus_profile_id ");
        $row = $qry->fetch();

        $due_amt_track = intVal($row['due_amt_track']) + intVal($row['pre_close_waiver']);

        if ($loan_arr['loan_type'] == 'interest') {
            $due_amt_track = $row['princ_amt_track'] + $row['pre_close_waiver'];
        }

        //if sum value is null or empty then assign 0 to it
        if ($row['penalty'] == '' or $row['penalty'] == null) {
            $row['penalty'] = 0;
        }
        if ($row['paid_amntpc'] == '' or $row['paid_amntpc'] == null) {
            $row['paid_amntpc'] = 0;
        }
        if ($row['waiver_amntpc'] == '' or $row['waiver_amntpc'] == null) {
            $row['waiver_amntpc'] = 0;
        }
        if ($row['coll_charge'] == '' or $row['coll_charge'] == null) {
            $row['coll_charge'] = 0;
        }
        if ($row['paid_amntcc'] == '' or $row['paid_amntcc'] == null) {
            $row['paid_amntcc'] = 0;
        }
        if ($row['waiver_amntcc'] == '' or $row['waiver_amntcc'] == null) {
            $row['waiver_amntcc'] = 0;
        }

        $curr_penalty = $row['penalty'] - $row['paid_amntpc'] - $row['waiver_amntpc'];
        $curr_charges = $row['coll_charge'] - $row['paid_amntcc'] - $row['waiver_amntcc'];

        $qry = $pdo->query("SELECT SUM(principal_amnt) as principal_amt_cal,SUM(total_amnt) as tot_amt_cal from loan_entry_loan_calculation WHERE cus_profile_id =$cp_id");
        $row = $qry->fetch();

        if ($row['tot_amt_cal'] != 0) {
            $total_for_nil = $row['tot_amt_cal'];
        } else {
            $total_for_nil = $row['principal_amt_cal'];
        }
        $due_nil_check = intVal($total_for_nil) - intVal($due_amt_track);

        if ($due_nil_check == 0) {
            if ($curr_penalty > 0 || $curr_charges > 0) {
                $response['due_nil'] = true;
            } else {
                $response['due_nil'] = false;
            }
        } else {
            $response['due_nil'] = false;
        }

        if ($count > 0) {

            if ($loan_arr['loan_type'] == 'interest') {

                $response['pending'] = (($response['due_amt'] * ($count)) - $response['due_amt'] + getTillDateInterest($loan_arr, $response, $pdo, 'fullstartmonth')) - $response['total_paid_int'];
            } else {
                //if Due month exceeded due amount will be as pending with how many months are exceeded
                $response['pending'] = ($response['due_amt'] * $count) - $response['total_paid'] - $response['pre_closure'];
            }

            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = number_format($row['overdue'] * $count); //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

            $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
            $row = $result->fetch();

            $penalty = number_format(($response['due_amt'] * $penalty_per) / 100);
            $response['penalty'] = intval($penalty) - (($row['penalty']) ? $row['penalty'] : 0) - (($row['penalty_waiver']) ? $row['penalty_waiver'] : 0);

            //Payable amount will be pending amount added with current month due amount
            $response['payable'] = $response['due_amt'] + $response['pending'];


            if ($loan_arr['loan_type'] == 'interest') { // if loan type is interest then we need to calculate pending and payable again

                if ($count == 1) {
                    $response['payable'] = $response['pending'];
                    $response['pending'] =  0;
                } else {
                    $response['payable'] =  $response['pending'];
                    if ($count >= 2) {
                        $response['pending'] =  $response['pending'] - $response['due_amt'];
                    }
                }
            }
        } else {
            //If still current month is not ended, then pending will be same due amt
            $response['pending'] = $response['due_amt'] - $response['total_paid'] - $response['pre_closure'];
            //If still current month is not ended, then penalty will be 0
            $response['penalty'] = 0;
            //If still current month is not ended, then payable will be due amt
            $response['payable'] = 0;

            if ($loan_arr['loan_type'] == 'interest') { //for first month payable will be zero in interest loan
                $response['payable'] =  0;
            }
        }
    } else
    if ($loan_arr['scheme_due_method'] == '2') {

        //If Due method is Weekly, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1W'); // Create a one Week interval

        $count = 0;
        // $qry = $pdo->query("DELETE FROM penalty_charges where req_id = '$cp_id' and (penalty_date != '' or penalty_date != NULL ) ");
        while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) {
            //To raise penalty in seperate table
            $penalty_raised_date  = $start_date_obj->format('Y-m-d');
            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = $row['overdue']; //get penalty percentage to insert

            $penalty = number_format(($response['due_amt'] * $penalty_per) / 100);

            $start_date_obj->add($interval);
            $count++; //Count represents how many months are exceeded
        }
        $response['count_of_month'] = $count;
        //To check over due, if current date is greater than maturity minth, then i will be OD
        if ($current_date_obj > $end_date_obj) {
            $response['od'] = true;
        } else {
            $response['od'] = false;
        }

        //To check whether due has been nil with other charges

        $qry = $pdo->query("SELECT c.due_amt_track, c.pre_close_waiver, c.princ_amt_track, pc.penalty, pc.paid_amnt AS paid_amntpc, pc.waiver_amnt AS waiver_amntpc, cc.coll_charge, cc.paid_amnt AS paid_amntcc, cc.waiver_amnt AS waiver_amntcc 
        FROM ( SELECT cus_profile_id, SUM(due_amt_track) AS due_amt_track,SUM(pre_close_waiver) AS pre_close_waiver,SUM(princ_amt_track) AS princ_amt_track FROM collection WHERE cus_profile_id = '$cp_id' ) c 
        LEFT JOIN ( SELECT cus_profile_id, SUM(penalty) AS penalty, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt FROM penalty_charges WHERE cus_profile_id = '$cp_id' GROUP BY cus_profile_id ) pc ON c.cus_profile_id = pc.cus_profile_id 
        LEFT JOIN ( SELECT cus_profile_id, SUM(coll_charge) AS coll_charge, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt FROM collection_charges 
        WHERE cus_profile_id = '$cp_id' GROUP BY cus_profile_id ) cc ON c.cus_profile_id = cc.cus_profile_id ");
        $row = $qry->fetch();

        $due_amt_track = intVal($row['due_amt_track']) + intVal($row['pre_close_waiver']);
        //if sum value is null or empty then assign 0 to it
        if ($row['penalty'] == '' or $row['penalty'] == null) {
            $row['penalty'] = 0;
        }
        if ($row['paid_amntpc'] == '' or $row['paid_amntpc'] == null) {
            $row['paid_amntpc'] = 0;
        }
        if ($row['waiver_amntpc'] == '' or $row['waiver_amntpc'] == null) {
            $row['waiver_amntpc'] = 0;
        }
        if ($row['coll_charge'] == '' or $row['coll_charge'] == null) {
            $row['coll_charge'] = 0;
        }
        if ($row['paid_amntcc'] == '' or $row['paid_amntcc'] == null) {
            $row['paid_amntcc'] = 0;
        }
        if ($row['waiver_amntcc'] == '' or $row['waiver_amntcc'] == null) {
            $row['waiver_amntcc'] = 0;
        }

        $curr_penalty = $row['penalty'] - $row['paid_amntpc'] - $row['waiver_amntpc'];
        $curr_charges = $row['coll_charge'] - $row['paid_amntcc'] - $row['waiver_amntcc'];

        $qry = $pdo->query("SELECT SUM(principal_amnt) as principal_amt_cal,SUM(total_amnt) as tot_amt_cal from loan_entry_loan_calculation WHERE cus_profile_id =$cp_id");
        $row = $qry->fetch();

        if ($row['tot_amt_cal'] != '') {
            $total_for_nil = $row['tot_amt_cal'];
        } else {
            $total_for_nil = $row['principal_amt_cal'];
        }
        $due_nil_check = intVal($total_for_nil) - intVal($due_amt_track);

        if ($due_nil_check == 0) {
            if ($curr_penalty > 0 || $curr_charges > 0) {
                $response['due_nil'] = true;
            }
        } else {
            $response['due_nil'] = false;
        }

        if ($count > 0) {

            //if Due month exceeded due amount will be as pending with how many months are exceeded
            $response['pending'] = ($response['due_amt'] * $count) - $response['total_paid'] - $response['pre_closure'];

            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = number_format($row['overdue'] * $count); //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

            $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
            $row = $result->fetch();

            $penalty = number_format(($response['due_amt'] * $penalty_per) / 100);
            $response['penalty'] = $penalty - $row['penalty'] - $row['penalty_waiver'];

            //Payable amount will be pending amount added with current month due amount
            $response['payable'] = $response['due_amt'] + $response['pending'];
        } else {
            //If still current month is not ended, then pending will be same due amt
            $response['pending'] = $response['due_amt'] - $response['total_paid'] - $response['pre_closure'];
            //If still current month is not ended, then penalty will be 0
            $response['penalty'] = 0;
            //If still current month is not ended, then payable will be due amt
            $response['payable'] = 0;
        }
    } elseif ($loan_arr['scheme_due_method'] == '3') {
        //If Due method is Daily, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1D'); // Create a one Week interval

        $count = 0;

        while ($start_date_obj < $end_date_obj && $start_date_obj < $current_date_obj) {
            //To raise penalty in seperate table
            $penalty_raised_date  = $start_date_obj->format('Y-m-d');
            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "' ");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = $row['overdue']; //get penalty percentage to insert

            $penalty = ($response['due_amt'] * $penalty_per) / 100;

            $start_date_obj->add($interval);
            $count++; //Count represents how many months are exceeded
        }
        $response['count_of_month'] = $count;
        
        //To check over due, if current date is greater than maturity minth, then i will be OD
        if ($current_date_obj > $end_date_obj) {
            $response['od'] = true;
        } else {
            $response['od'] = false;
        }

        //To check whether due has been nil with other charges

        $qry = $pdo->query("SELECT c.due_amt_track, c.pre_close_waiver, c.princ_amt_track, pc.penalty, pc.paid_amnt AS paid_amntpc, pc.waiver_amnt AS waiver_amntpc, cc.coll_charge, cc.paid_amnt AS paid_amntcc, cc.waiver_amnt AS waiver_amntcc 
        FROM ( SELECT cus_profile_id, SUM(due_amt_track) AS due_amt_track,SUM(pre_close_waiver) AS pre_close_waiver,SUM(princ_amt_track) AS princ_amt_track FROM collection WHERE cus_profile_id = '$cp_id' ) c 
        LEFT JOIN ( SELECT cus_profile_id, SUM(penalty) AS penalty, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt FROM penalty_charges WHERE cus_profile_id = '$cp_id' GROUP BY cus_profile_id ) pc ON c.cus_profile_id = pc.cus_profile_id 
        LEFT JOIN ( SELECT cus_profile_id, SUM(coll_charge) AS coll_charge, SUM(paid_amnt) AS paid_amnt, SUM(waiver_amnt) AS waiver_amnt FROM collection_charges 
        WHERE cus_profile_id = '$cp_id' GROUP BY cus_profile_id ) cc ON c.cus_profile_id = cc.cus_profile_id;");
        $row = $qry->fetch();

        $due_amt_track = intVal($row['due_amt_track']) + intVal($row['pre_close_waiver']);
        //if sum value is null or empty then assign 0 to it
        if ($row['penalty'] == '' or $row['penalty'] == null) {
            $row['penalty'] = 0;
        }
        if ($row['paid_amntpc'] == '' or $row['paid_amntpc'] == null) {
            $row['paid_amntpc'] = 0;
        }
        if ($row['waiver_amntpc'] == '' or $row['waiver_amntpc'] == null) {
            $row['waiver_amntpc'] = 0;
        }
        if ($row['coll_charge'] == '' or $row['coll_charge'] == null) {
            $row['coll_charge'] = 0;
        }
        if ($row['paid_amntcc'] == '' or $row['paid_amntcc'] == null) {
            $row['paid_amntcc'] = 0;
        }
        if ($row['waiver_amntcc'] == '' or $row['waiver_amntcc'] == null) {
            $row['waiver_amntcc'] = 0;
        }

        $curr_penalty = $row['penalty'] - $row['paid_amntpc'] - $row['waiver_amntpc'];
        $curr_charges = $row['coll_charge'] - $row['paid_amntcc'] - $row['waiver_amntcc'];

        $qry = $pdo->query("SELECT SUM(principal_amnt) as principal_amt_cal,SUM(total_amnt) as tot_amt_cal from loan_entry_loan_calculation WHERE cus_profile_id =$cp_id");
        $row = $qry->fetch();

        if ($row['tot_amt_cal'] != '') {
            $total_for_nil = $row['tot_amt_cal'];
        } else {
            $total_for_nil = $row['principal_amt_cal'];
        }
        $due_nil_check = intVal($total_for_nil) - intVal($due_amt_track);

        if ($due_nil_check == 0) {
            if ($curr_penalty > 0 || $curr_charges > 0) {
                $response['due_nil'] = true;
            }
        } else {
            $response['due_nil'] = false;
        }

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
            $penalty_per = number_format($row['overdue'] * $count); //Count represents how many months are exceeded//Number format if percentage exeeded decimals then pernalty may increase

            $result = $pdo->query("SELECT SUM(penalty_track) as penalty,SUM(penalty_waiver) as penalty_waiver FROM `collection` WHERE cus_profile_id = '" . $cp_id . "' ");
            $row = $result->fetch();

            $penalty = number_format(($response['due_amt'] * $penalty_per) / 100);
            $response['penalty'] = intVal($penalty) - intVal($row['penalty']) - intVal($row['penalty_waiver']);

            //Payable amount will be pending amount added with current month due amount
            $response['payable'] = $response['due_amt'] + $response['pending'];
        } else {
            //If still current month is not ended, then pending will be same due amt
            $response['pending'] = $response['due_amt'] - $response['total_paid'] - $response['pre_closure'];
            //If still current month is not ended, then penalty will be 0
            $response['penalty'] = 0;
            //If still current month is not ended, then payable will be due amt
            $response['payable'] = 0;
        }
    }
    if ($response['pending'] < 0) {
        $response['pending'] = 0;
    }
    return $response;
}

function calculateNewInterestAmt($loan_arr, $response)
{
    //to calculate current interest amount based on current balance value//bcoz interest will be calculated based on current balance amt only for interest loan
    $int = $response['balance'] * ($loan_arr['interest_rate'] / 100);
    $curInterest = ceil($int / 5) * 5; //to increase Interest to nearest multiple of 5
    if ($curInterest < $int) {
        $curInterest += 5;
    }
    $response = $curInterest;

    return $response;
}
function getTillDateInterest($loan_arr, $response, $pdo, $data)
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
            $amtperDay = $response['due_amt'] / intVal($currentMonthCount);

            $st_date = new DateTime(date('Y-m-d', strtotime($loan_arr['due_startdate']))); // start date
            $tdate = new DateTime(date('Y-m-t', strtotime($loan_arr['due_startdate']))); //current date
            // Calculate the interval between the two dates
            $date_diff = $st_date->diff($tdate);
            // Get the number of days from the interval
            $numberOfDays = $date_diff->days;
            $response = ceil($amtperDay * $numberOfDays);

        }
    }
    return $response;
}

function checkStatusOfCustomer($response, $loan_arr, $cus_id, $pdo)
{
    if($response){
        for ($i = 0; $i < count($response['pending_customer']); $i++) {
    
            // $query = $pdo->query("SELECT cs.status AS cus_status FROM loan_issue li JOIN customer_status cs ON li.cus_profile_id = cs.cus_profile_id  where li.cus_id = '$cus_id' and cs.status =7 ");
            // $row = $query->fetch();
            $curdate = date('Y-m-d');
    
            if (date('Y-m-d', strtotime($loan_arr['due_startdate'])) > date('Y-m-d', strtotime($curdate))) { //If the start date is on upcoming date then the sub status is current, until current date reach due_start_from date.
                $response['follow_cus_sts'] = 'Current';
    
            } else {
                if ($response['pending_customer'][$i] == true && $response['od_customer'][$i] == false) { //using i as 1 so subract it with 1
                    $response['follow_cus_sts'] = 'Pending';
                    
                } else if ($response['od_customer'][$i] == true && $response['due_nil_customer'][$i] == false) {
                    $response['follow_cus_sts'] = 'OD';
                    
                } elseif ($response['due_nil_customer'][$i] == true) {
                    $response['follow_cus_sts'] = 'Due Nil';
                    
                } elseif ($response['pending_customer'][$i] == false) {
                    $response['follow_cus_sts'] = 'Current';
                    
                }
            }
        }
        return $response['follow_cus_sts'];
    }
}


echo json_encode($response);

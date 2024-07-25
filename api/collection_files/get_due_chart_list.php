<?php
require '../../ajaxconfig.php';


function moneyFormatIndia($num)
{
    $explrestunits = "";
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3, strlen($num));
        $restunits = substr($num, 0, strlen($num) - 3);
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
        $expunit = str_split($restunits, 2);
        for ($i = 0; $i < sizeof($expunit); $i++) {
            if ($i == 0) {
                $explrestunits .= (int)$expunit[$i] . ",";
            } else {
                $explrestunits .= $expunit[$i] . ",";
            }
        }
        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash;
}
?>
<table class="table custom-table" id='dueChartListTable'>


    <?php
    $cp_id = $_POST['cp_id'];
    $cus_id = $_POST['cus_id'];
    $curDateChecker = true;
    if (isset($_POST['closed'])) {
        $closed = $_POST['closed'];
    } else {
        $closed = 'false';
    }
    $loanStart = $pdo->query("SELECT lelc.due_startdate, lelc.maturity_date, lelc.due_method, lelc.scheme_due_method FROM loan_entry_loan_calculation lelc WHERE lelc.cus_profile_id = '$cp_id' ");
    $loanFrom = $loanStart->fetch();
    //If Due method is Monthly, Calculate penalty by checking the month has ended or not
    $due_start_from = $loanFrom['due_startdate'];
    $maturity_month = $loanFrom['maturity_date'];


    if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
        //If Due method is Monthly, Calculate penalty by checking the month has ended or not

        // Create a DateTime object from the given date
        $maturity_month = new DateTime($maturity_month);
        // Subtract one month from the date
        // $maturity_month->modify('-1 month');
        // Format the date as a string
        $maturity_month = $maturity_month->format('Y-m-d');

        $due_start_from = date('Y-m-d', strtotime($due_start_from));
        $maturity_month = date('Y-m-d', strtotime($maturity_month));
        $current_date = date('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);
        $interval = new DateInterval('P1M'); // Create a one month interval
        //$count = 0;
        $i = 1;
        $dueMonth[] = $due_start_from;
        while ($start_date_obj < $end_date_obj) {
            $start_date_obj->add($interval);
            $dueMonth[] = $start_date_obj->format('Y-m-d');
        }
    } else
        if ($loanFrom['scheme_due_method'] == '2') {
        //If Due method is Weekly, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d');

        // Create a DateTime object from the given date
        $maturity_month = new DateTime($maturity_month);
        // Subtract one month from the date
        // $maturity_month->modify('-7 days');
        // Format the date as a string
        $maturity_month = $maturity_month->format('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1W'); // Create a one Week interval

        //$count = 0;
        $i = 1;
        $dueMonth[] = $due_start_from;
        while ($start_date_obj < $end_date_obj) {
            $start_date_obj->add($interval);
            $dueMonth[] = $start_date_obj->format('Y-m-d');
        }
    } else
        if ($loanFrom['scheme_due_method'] == '3') {
        //If Due method is Daily, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d');

        // Create a DateTime object from the given date
        $maturity_month = new DateTime($maturity_month);
        // Subtract one month from the date
        // $maturity_month->modify('-1 days');
        // Format the date as a string
        $maturity_month = $maturity_month->format('Y-m-d');

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1D'); // Create a one Week interval

        //$count = 0;
        $i = 1;
        $dueMonth[] = $due_start_from;
        while ($start_date_obj < $end_date_obj) {
            $start_date_obj->add($interval);
            $dueMonth[] = $start_date_obj->format('Y-m-d');
        }
    }

    $issueDate = $pdo->query("SELECT lelc.due_amnt, lelc.interest_amnt, lelc.total_amnt, lelc.principal_amnt, li.issue_date
    FROM loan_issue li 
    JOIN loan_entry_loan_calculation lelc ON li.cus_profile_id = lelc.cus_profile_id  
    JOIN customer_status cs ON cs.cus_profile_id = li.cus_profile_id
    WHERE li.cus_profile_id = '$cp_id' and cs.status >= 7 ORDER BY lelc.id DESC LIMIT 1 ");

    $loanIssue = $issueDate->fetch();
    //If Due method is Monthly, Calculate penalty by checking the month has ended or not
    if ($loanIssue['total_amnt'] == '' || $loanIssue['total_amnt'] == null) {
        //(For monthly interest total amount will not be there, so take principals)
        $loan_amt = intVal($loanIssue['principal_amnt']);
        $loan_type = 'interest';
    } else {
        $loan_amt = intVal($loanIssue['total_amnt']);
        $loan_type = 'emi';
    }

    $due_amt_1 = $loanIssue['due_amnt'];

    if ($loan_type == 'interest') {
        $princ_amt_1 = $loanIssue['principal_amnt'];
        $due_amt_1 = $loanIssue['interest_amnt'];
    }

    $issue_date = $loanIssue['issue_date'];
    ?>

    <thead>
        <tr><!-- Showing Collection Due Month Start and balance -->
            <th width="15"> Due No </th>
            <th width="8%"> Due Month </th>
            <th> Month </th>
            <?php if ($loan_type == 'emi') { ?>
                <th> Due Amount </th>
            <?php } ?>
            <?php if ($loan_type == 'interest') { ?>
                <th> Principal </th>
                <th> Interest </th>
            <?php } ?>
            <th> Pending </th>
            <th> Payable </th>
            <th> Collection Date </th>
            <?php if ($loan_type == 'emi') { ?>
                <th> Collection Amount </th>
            <?php } ?>
            <?php if ($loan_type == 'interest') { ?>
                <th> Principal Amount </th>
                <th> Interest Amount </th>
            <?php } ?>
            <th> Balance Amount </th>
            <th> Pre Closure </th>
            <th> Role </th>
            <th width="8%"> User ID </th>
            <!-- <th> Collection Method </th> -->
            <th> ACTION </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td> </td>
            <td><?php
                if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                    //For Monthly.
                    echo date('m-Y', strtotime($issue_date));
                } else {
                    //For Weekly && Day.
                    echo date('d-m-Y', strtotime($issue_date));
                } ?></td>
            <td><?php echo date('M', strtotime($issue_date)); ?></td>
            <?php if ($loan_type == 'emi') { ?>
                <td> </td>
            <?php } ?>
            <?php if ($loan_type == 'interest') { ?>
                <td> </td>
                <td> </td>
            <?php } ?>
            <td></td>
            <td></td>
            <td></td>

            <!-- for collected amt -->
            <?php if ($loan_type == 'emi') { ?>
                <td> </td>
            <?php } ?>
            <?php if ($loan_type == 'interest') { ?>
                <td> </td>
                <td> </td>
            <?php } ?>

            <td><?php echo $loan_amt; ?></td>
            <td></td>
            <td></td>
            <td></td>
            <!-- <td></td> -->
            <td></td>
        </tr>
        <?php
        $issued = date('Y-m-d', strtotime($issue_date));
        if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
            //Query for Monthly.
            $run = $pdo->query("SELECT c.coll_code, c.due_amt,c.tot_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track,c.princ_amt_track,c.int_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role
            FROM `collection` c
            LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id
            LEFT JOIN users u ON c.insert_login_id = u.id
            LEFT JOIN role r ON u.role = r.id
            WHERE c.cus_profile_id = '$cp_id' AND (c.due_amt_track != '' or c.pre_close_waiver!='')
            AND(
                (
                    ( MONTH(c.coll_date) >= MONTH('$issued') AND YEAR(c.coll_date) = YEAR('$issued') )
                    AND 
                    ( 
                        (
                            YEAR(c.coll_date) = YEAR('$due_start_from') AND MONTH(c.coll_date) < MONTH('$due_start_from')
                        ) OR (
                            YEAR(c.coll_date) < YEAR('$due_start_from')
                        )
                    )
                ) 
                OR
                (
                    ( MONTH(c.trans_date) >= MONTH('$issued') AND YEAR(c.trans_date) = YEAR('$issued') )
                    AND 
                    ( 
                        (
                            YEAR(c.trans_date) = YEAR('$due_start_from') AND MONTH(c.trans_date) < MONTH('$due_start_from')
                        ) OR (
                            YEAR(c.trans_date) < YEAR('$due_start_from')
                        )
                            AND c.trans_date != '0000-00-00'
                    )
                )
            )");
        } else
        if ($loanFrom['scheme_due_method'] == '2') {
            //Query For Weekly.
            $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role
            FROM `collection` c
            LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id
            LEFT JOIN users u ON c.insert_login_id = u.id
            LEFT JOIN role r ON u.role = r.id
            WHERE c.`cus_profile_id` = '$cp_id' AND (c.due_amt_track != '' or c.pre_close_waiver!='' OR c.princ_amt_track != '')
            AND (
                    (
                        (WEEK(c.coll_date) >= WEEK('$issued') AND YEAR(c.coll_date) = YEAR('$issued'))
                        AND 
                        (
                            (
                                YEAR(c.coll_date) = YEAR('$due_start_from') AND WEEK(c.coll_date) < WEEK('$due_start_from')
                            ) OR (
                                YEAR(c.coll_date) < YEAR('$due_start_from')
                            )
                        )
                    ) 
                    OR
                    (
                        (WEEK(c.trans_date) >= WEEK('$issued') AND YEAR(c.trans_date) = YEAR('$issued'))
                        AND 
                        (
                            (
                                YEAR(c.trans_date) = YEAR('$due_start_from') AND WEEK(c.trans_date) < WEEK('$due_start_from')
                            ) OR (
                                YEAR(c.trans_date) < YEAR('$due_start_from')
                            )
                            AND c.trans_date != '0000-00-00'

                        )
                    )
                )
            ");
        } else
        if ($loanFrom['scheme_due_method'] == '3') {
            //Query For Day.
            $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role
            FROM `collection` c
            LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id
            LEFT JOIN users u ON c.insert_login_id = u.id
            LEFT JOIN role r ON u.role = r.id
            WHERE c.`cus_profile_id` = '$cp_id' AND (c.due_amt_track != '' or c.pre_close_waiver!='')
            AND (
                (DATE(c.coll_date) >= DATE('$issued') AND DATE(c.coll_date) < DATE('$due_start_from') AND DATE(c.coll_date) != '0000-00-00' ) OR
                (DATE(c.trans_date) >= DATE('$issued') AND DATE(c.trans_date) < DATE('$due_start_from') AND DATE(c.trans_date) != '0000-00-00' )
            ) ");
        }

        //For showing data before due start date
        $due_amt_track = 0;
        $waiver = 0;
        $last_bal_amt = 0;
        $bal_amt = 0;
        if ($run->rowCount() > 0) {
            while ($row = $run->fetch()) {
                $collectionAmnt = intVal($row['due_amt_track']);
                $due_amt_track = $due_amt_track + intVal($row['due_amt_track']);
                $waiver = $waiver + intVal($row['pre_close_waiver']);
                if ($loan_type == 'interest') {
                    $PcollectionAmnt = intVal($row['princ_amt_track']);
                    $IcollectionAmnt = intVal($row['int_amt_track']);
                    if ($last_bal_amt != 0) {
                        $bal_amt = $last_bal_amt - $PcollectionAmnt - $waiver;
                    } else {
                        $bal_amt = $loan_amt - $PcollectionAmnt - $waiver;
                    }
                } else {
                    $bal_amt = $loan_amt - $due_amt_track - $waiver;
                }
        ?>
                <tr> <!-- Showing From loan date to due start date. if incase due paid before due start date it has to show seperatly in top row. -->
                    <td></td>
                    <td></td>
                    <td></td>

                    <?php if ($loan_type == 'emi') { ?>
                        <td></td>
                    <?php } ?>
                    <?php if ($loan_type == 'interest') { ?>
                        <td></td>
                        <td></td>
                    <?php } ?>

                    <td><?php $pendingMinusCollection = (intVal($row['pending_amt'])); ?></td>
                    <td><?php $payableMinusCollection = (intVal($row['payable_amt'])); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row['trans_date'] != '0000-00-00' ? $row['trans_date'] : $row['coll_date'])); ?></td>

                    <!-- for collected amt -->
                    <?php if ($loan_type == 'emi') { ?>
                        <td>
                            <?php if ($row['due_amt_track'] > 0) {
                                echo $row['due_amt_track'];
                            } elseif ($row['pre_close_waiver'] > 0) {
                                echo $row['pre_close_waiver'];
                            } ?>
                        </td>
                    <?php } ?>

                    <?php if ($loan_type == 'interest') { ?>
                        <td>
                            <?php if ($PcollectionAmnt > 0) {
                                echo $PcollectionAmnt;
                            } elseif ($row['pre_close_waiver'] > 0) {
                                echo $row['pre_close_waiver'];
                            } ?>
                        </td>
                        <td>
                            <?php if ($IcollectionAmnt > 0) {
                                echo $IcollectionAmnt;
                            } ?>
                        </td>
                    <?php } ?>

                    <td><?php echo $bal_amt; ?></td>
                    <td><?php if ($row['pre_close_waiver'] > 0) {
                            echo $row['pre_close_waiver'];
                        } else {
                            echo '0';
                        } ?></td>
                    <td><?php echo $row['role']; ?>
                    </td>
                    <td><?php echo $row['name']; ?></td>
                    <!-- <td><?php #if ($row['coll_location'] == '1') {echo 'By Self'; } elseif ($row['coll_location'] == '2') { echo 'On Spot';} elseif ($row['coll_location'] == '3') { echo 'Bank Transfer';} ?></td> -->
                    <td> <a class='print_due_coll' id="" value="<?php echo $row['coll_code']; ?>"> <i class="fa fa-print" aria-hidden="true"></i> </a> </td>
                </tr>

                <?php
                if ($loan_type == 'interest') {
                    $last_bal_amt = $bal_amt;
                } else {
                }
            }
        }

        //For showing collection after due start date
        $due_amt_track = 0;
        $waiver = 0;
        $jj = 0;
        $last_int_amt = $due_amt_1;
        if ($loan_type == 'interest') {
            $last_princ_amt = $last_bal_amt;
            // $bal_amt = $last_bal_amt;
        } else {
            $bal_amt = 0;
        }
        $lastCusdueMonth = '1970-00-00';
        foreach ($dueMonth as $cusDueMonth) {
            if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                //Query for Monthly.
                $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.tot_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.princ_amt_track, c.int_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role FROM `collection` c LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id LEFT JOIN users u ON c.insert_login_id = u.id LEFT JOIN role r ON u.role = r.id WHERE (c.`cus_profile_id` = $cp_id) and (c.due_amt_track != '' or c.princ_amt_track!='' or c.int_amt_track!='' or c.pre_close_waiver!='') && ((MONTH(coll_date)= MONTH('$cusDueMonth') || MONTH(trans_date)= MONTH('$cusDueMonth')) && (YEAR(coll_date)= YEAR('$cusDueMonth') || YEAR(trans_date)= YEAR('$cusDueMonth')) )");
            } elseif ($loanFrom['scheme_due_method'] == '2') {
                //Query For Weekly.
                $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role FROM `collection` c LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id LEFT JOIN users u ON c.insert_login_id = u.id LEFT JOIN role r ON u.role = r.id WHERE (c.`cus_profile_id` = $cp_id) and (c.due_amt_track != '' or c.pre_close_waiver!='') && ((WEEK(coll_date)= WEEK('$cusDueMonth') || WEEK(trans_date)= WEEK('$cusDueMonth')) && (YEAR(coll_date)= YEAR('$cusDueMonth') || YEAR(trans_date)= YEAR('$cusDueMonth')) )");
            } elseif ($loanFrom['scheme_due_method'] == '3') {
                //Query For Day.
                $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role FROM `collection` c LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id LEFT JOIN users u ON c.insert_login_id = u.id LEFT JOIN role r ON u.role = r.id WHERE (c.`cus_profile_id` = $cp_id) and (c.due_amt_track != '' or c.pre_close_waiver!='') && 
                ( 
                    ( DAY(coll_date)= DAY('$cusDueMonth') || DAY(trans_date)= DAY('$cusDueMonth') ) && 
                    ( MONTH(coll_date)= MONTH('$cusDueMonth') || MONTH(trans_date)= MONTH('$cusDueMonth') ) && 
                    ( YEAR(coll_date)= YEAR('$cusDueMonth') || YEAR(trans_date)= YEAR('$cusDueMonth') )
                )
                ");
            }

            if ($run->rowCount() > 0) {

                while ($row = $run->fetch()) { 
                    $due_amt_track = intVal($row['due_amt_track']);
                    if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                        $princ_amt_track = intVal($row['princ_amt_track']);
                        $int_amt_track = intVal($row['int_amt_track']);
                    }
                    
                    $waiver = intVal($row['pre_close_waiver']);
                    if ($loan_type == 'emi') {
                        $bal_amt = intVal($row['bal_amt']) - $due_amt_track - $waiver;
                    } else {
                        $bal_amt = intVal($last_princ_amt) - $due_amt_track - $waiver;
                    }

                ?>
                    <tr> <!-- Showing From Due Start date to Maurity date -->
                        <?php
                        if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') { //this is for monthly loan to check lastcusduemonth comparision
                            if (date('Y-m', strtotime($lastCusdueMonth)) != date('Y-m', strtotime($row['coll_date']))) {
                                // this condition is to check whether the same month has collection again. if yes the no need to show month name and due amount and serial number
                        ?>
                                <td><?php echo $i;
                                    $i++; ?></td>
                                <td><?php
                                    if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                                        //For Monthly.
                                        echo date('m-Y', strtotime($cusDueMonth));
                                    } else {
                                        //For Weekly && Day.
                                        echo date('d-m-Y', strtotime($cusDueMonth));
                                    }
                                    ?></td>
                                <td>
                                    <?php
                                    echo date('M', strtotime($cusDueMonth));
                                    ?>
                                </td>

                                <?php if ($loan_type == 'emi') { ?>
                                    <td><?php echo $row['due_amt']; ?></td>
                                <?php } ?>
                                <?php if ($loan_type == 'interest') { ?>
                                    <td><?php echo $last_princ_amt; ?></td>
                                    <td><?php echo $row['due_amt'];
                                        $last_int_amt = $row['due_amt']; ?></td>
                                <?php } ?>


                            <?php } else { ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php }
                        } else { //this is for weekly and daily loan to check lastcusduemonth comparision
                            if (date('Y-m-d', strtotime($lastCusdueMonth)) != date('Y-m-d', strtotime($row['coll_date']))) {
                                // this condition is to check whether the same month has collection again. if yes the no need to show month name and due amount and serial number
                            ?>
                                <td><?php echo $i;
                                    $i++; ?></td>
                                <td><?php
                                    if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                                        //For Monthly.
                                        echo date('m-Y', strtotime($cusDueMonth));
                                    } else {
                                        //For Weekly && Day.
                                        echo date('d-m-Y', strtotime($cusDueMonth));
                                    }
                                    ?></td>
                                <td>
                                    <?php
                                    echo date('M', strtotime($cusDueMonth));
                                    ?>
                                </td>

                                <?php if ($loan_type == 'emi') { ?>
                                    <td><?php echo $row['due_amt']; ?></td>
                                <?php } ?>
                                <?php if ($loan_type == 'interest') { ?>
                                    <td><?php echo $last_princ_amt; ?></td>
                                    <td><?php echo $row['due_amt'];
                                        $last_int_amt = $row['due_amt']; ?></td>
                                <?php } ?>


                            <?php } else { ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                        <?php }
                        } ?>

                        <td><?php $pendingMinusCollection = (intVal($row['pending_amt']));
                            if ($pendingMinusCollection != '') {
                                echo $pendingMinusCollection;
                            } else {
                                echo 0;
                            } ?></td>
                        <td><?php $payableMinusCollection = (intVal($row['payable_amt']));
                            if ($payableMinusCollection != '') {
                                echo $payableMinusCollection;
                            } else {
                                echo 0;
                            } ?></td>
                        <td><?php echo date('d-m-Y', strtotime($row['trans_date'] != '0000-00-00' ? $row['trans_date'] : $row['coll_date'])); ?></td>

                        <!-- for collected amt -->
                        <?php if ($loan_type == 'emi') { ?>
                            <td>
                                <?php if ($row['due_amt_track'] > 0) {
                                    echo $row['due_amt_track'];
                                } elseif ($row['pre_close_waiver'] > 0) {
                                    echo $row['pre_close_waiver'];
                                } ?>
                            </td>
                        <?php } ?>

                        <?php if ($loan_type == 'interest') { ?>
                            <td>
                                <?php if ($princ_amt_track > 0) {
                                    echo $princ_amt_track;
                                } elseif ($row['pre_close_waiver'] > 0) {
                                    echo $row['pre_close_waiver'];
                                } ?>
                            </td>
                            <td>
                                <?php if ($int_amt_track > 0) {
                                    echo $int_amt_track;
                                } ?>
                            </td>
                        <?php } ?>


                        <td><?php echo $bal_amt;
                            if ($loan_type == 'interest') {
                                $last_princ_amt = $bal_amt;
                            } ?></td>
                        <td><?php if ($row['pre_close_waiver'] > 0) {
                                echo $row['pre_close_waiver'];
                            } else {
                                echo '0';
                            } ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <!-- <td><?php #if ($row['coll_location'] == '1') {echo 'By Self';} elseif ($row['coll_location'] == '2') {echo 'On Spot';} elseif ($row['coll_location'] == '3') {echo 'Bank Transfer';} ?></td> -->
                        <td> <a class='print_due_coll' id="" value="<?php echo $row['coll_code']; ?>"> <i class="fa fa-print" aria-hidden="true"></i> </a> </td>
                    </tr>

                <?php $lastCusdueMonth = date('d-m-Y', strtotime($cusDueMonth)); //assign this cusDueMonth to check if coll date is already showed before
                }
            } else { //if not paid on due month. else part will show.
                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php
                        if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                            //For Monthly.
                            echo date('m-Y', strtotime($cusDueMonth));
                        } else {
                            //For Weekly && Day.
                            echo date('d-m-Y', strtotime($cusDueMonth));
                        } ?></td>
                    <td> <?php echo date('M', strtotime($cusDueMonth)); ?> </td>

                    <?php if ($loan_type == 'emi') { ?>
                        <td><?php echo $due_amt_1; ?></td>
                    <?php } ?>
                    <?php if ($loan_type == 'interest') { ?>
                        <td><?php echo $last_princ_amt; ?></td>
                        <td><?php echo $last_int_amt; ?></td>
                    <?php } ?>

                    <?php
                    if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
                        if (date('Y-m', strtotime($cusDueMonth)) <=  date('Y-m')) { ?>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth);
                                echo $response['pending']; ?>
                            </td>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth);
                                echo $response['payable']; ?>
                            </td>
                        <?php } else if (date('Y-m', strtotime($cusDueMonth)) >  date('Y-m') && $curDateChecker == true) { ?>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth); ?>
                            </td>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth); ?>
                            </td>
                        <?php
                            $curDateChecker = false; //set to false because, pending and payable only need one month after current month
                        } else {
                        ?>
                            <td></td>
                            <td></td>
                        <?php
                        }
                    } else {
                        if (date('Y-m-d', strtotime($cusDueMonth)) <=  date('Y-m-d')) { ?>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth);
                                echo $response['pending']; ?>
                            </td>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth);
                                echo $response['payable']; ?>
                            </td>
                        <?php } else if (date('Y-m-d', strtotime($cusDueMonth)) >  date('Y-m-d') && $curDateChecker == true) { ?>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth); ?>
                            </td>
                            <td>
                                <?php $response = getNextLoanDetails($pdo, $cp_id, $cusDueMonth); ?>
                            </td>
                        <?php
                            $curDateChecker = false; //set to false because, pending and payable only need one month after current month
                        } else {
                        ?>
                            <td></td>
                            <td></td>
                    <?php
                        }
                    }
                    ?>

                    <td></td>
                    <!-- for collected amt -->
                    <?php if ($loan_type == 'emi') { ?>
                        <td> </td>
                    <?php } ?>
                    <?php if ($loan_type == 'interest') { ?>
                        <td> </td>
                        <td> </td>
                    <?php } ?>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <!-- <td></td> -->
                    <td></td>
                </tr>

            <?php
                $i++;
            }
        }

        $currentMonth = date('Y-m-d');
        if ($loanFrom['due_method'] == 'Monthly' || $loanFrom['scheme_due_method'] == '1') {
            //Query for Monthly.
            $run = $pdo->query("SELECT c.coll_code, c.due_amt,c.tot_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track,c.princ_amt_track,c.int_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role
            FROM `collection` c
            LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id
            LEFT JOIN users u ON c.insert_login_id = u.id
            LEFT JOIN role r ON u.role = r.id
            WHERE c.`cus_profile_id` = '$cp_id' AND (c.due_amt_track != '' or c.pre_close_waiver!='')
            AND (
                    (MONTH(c.coll_date) > MONTH('$maturity_month') AND MONTH(c.coll_date) <= MONTH('$currentMonth') AND MONTH(c.coll_date) != '0000-00-00' ) OR
                    (MONTH(c.trans_date) > MONTH('$maturity_month') AND MONTH(c.trans_date) <= MONTH('$currentMonth') AND MONTH(c.trans_date) != '0000-00-00' ) 
                ) ");
        } else
        if ($loanFrom['scheme_due_method'] == '2') {
            //Query For Weekly.
            $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role
            FROM `collection` c
            LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id
            LEFT JOIN users u ON c.insert_login_id = u.id
            LEFT JOIN role r ON u.role = r.id
            WHERE c.`cus_profile_id` = '$cp_id' AND (c.due_amt_track != '' or c.pre_close_waiver!='')
            AND (
                (DATE(c.coll_date) > DATE('$maturity_month') AND DATE(c.coll_date) <= DATE('$currentMonth') AND DATE(c.coll_date) != '0000-00-00' ) OR
                (DATE(c.trans_date) > DATE('$maturity_month') AND DATE(c.trans_date) <= DATE('$currentMonth') AND DATE(c.trans_date) != '0000-00-00' )
                ) ");
        } else
        if ($loanFrom['scheme_due_method'] == '3') {
            //Query For Day.
            $run = $pdo->query("SELECT c.coll_code, c.due_amt, c.pending_amt, c.payable_amt, c.coll_date, c.trans_date, c.due_amt_track, c.bal_amt, c.coll_charge_track, c.pre_close_waiver, lelc.due_startdate, lelc.maturity_date, lelc.due_method, u.name, r.role
            FROM `collection` c
            LEFT JOIN loan_entry_loan_calculation lelc ON c.cus_profile_id = lelc.cus_profile_id
            LEFT JOIN users u ON c.insert_login_id = u.id
            LEFT JOIN role r ON u.role = r.id
            WHERE c.`cus_profile_id` = '$cp_id' AND (c.due_amt_track != '' or c.pre_close_waiver!='')
            AND (
                    (DATE(c.coll_date) > DATE('$maturity_month') AND DATE(c.coll_date) <= DATE('$currentMonth') AND DATE(c.coll_date) != '0000-00-00' ) OR
                    (DATE(c.trans_date) > DATE('$maturity_month') AND DATE(c.trans_date) <= DATE('$currentMonth') AND DATE(c.trans_date) != '0000-00-00' )
                ) ");
        }

        if ($run->rowCount() > 0) {
            $due_amt_track = 0;
            $waiver = 0;
            while ($row = $run->fetch()) {
                $collectionAmnt = intVal($row['due_amt_track']);
                $due_amt_track = intVal($row['due_amt_track']);
                $waiver = intVal($row['pre_close_waiver']);
                $bal_amt = $bal_amt - $due_amt_track - $waiver;
            ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>

                    <?php if ($loan_type == 'emi') { ?>
                        <td></td>
                    <?php } ?>
                    <?php if ($loan_type == 'interest') { ?>
                        <td></td>
                        <td></td>
                    <?php } ?>

                    <td><?php $pendingMinusCollection = (intVal($row['pending_amt']));
                        if ($pendingMinusCollection != '') {
                            echo $pendingMinusCollection;
                        } else {
                            echo 0;
                        } ?></td>
                    <td><?php $payableMinusCollection = (intVal($row['payable_amt']));
                        if ($payableMinusCollection != '') {
                            echo $payableMinusCollection;
                        }
                        ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row['coll_date'])); ?></td>

                    <?php if ($loan_type == 'emi') { ?>
                        <td>
                            <?php if ($row['due_amt_track'] > 0) {
                                echo $row['due_amt_track'];
                            } elseif ($row['pre_close_waiver'] > 0) {
                                echo $row['pre_close_waiver'];
                            } ?>
                        </td>
                    <?php } ?>

                    <?php if ($loan_type == 'interest') { ?>
                        <td>
                            <?php if ($PcollectionAmnt > 0) {
                                echo $PcollectionAmnt;
                            } elseif ($row['pre_close_waiver'] > 0) {
                                echo $row['pre_close_waiver'];
                            } ?>
                        </td>
                        <td>
                            <?php if ($IcollectionAmnt > 0) {
                                echo $IcollectionAmnt;
                            } ?>
                        </td>
                    <?php } ?>

                    <td><?php echo $bal_amt; ?></td>
                    <td><?php if ($row['pre_close_waiver'] > 0) {
                            echo $row['pre_close_waiver'];
                        } else {
                            echo '0';
                        } ?></td>
                    <td><?php echo $row['role']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <!-- <td><?php #if ($row['coll_location'] == '1') {echo 'By Self';} elseif ($row['coll_location'] == '2') {echo 'On Spot';} elseif ($row['coll_location'] == '3') {echo 'Bank Transfer';} ?></td> -->
                    <td> <a class='print_due_coll' id="" value="<?php echo $row['coll_code']; ?>"> <i class="fa fa-print" aria-hidden="true"></i> </a> </td>
                </tr>

        <?php
                $i++;
            }
        }
        ?>

    </tbody>
</table>

<?php
function getNextLoanDetails($pdo, $cp_id, $date)
{
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
            $response['due_amt'] = calculateNewInterestAmt($loan_arr, $response);
        }

        $response = calculateOthers($loan_arr, $response, $date, $pdo);
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

        $response = calculateOthers($loan_arr, $response, $date, $pdo);
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

    return $response;
}
function calculateOthers($loan_arr, $response, $date, $pdo)
{

    if (isset($_POST['cp_id'])) {
        $cp_id = $_POST['cp_id'];
    }
    //***************************************************************************************************************************************************
    $due_start_from = $loan_arr['due_startdate'];
    $maturity_month = $loan_arr['maturity_date'];

    $tot_paid_tilldate = 0;
    $preclose_tilldate = 0;


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
        // $maturity_month->modify('-1 month');
        // Format the date as a string
        $maturity_month = $maturity_month->format('Y-m');

        //If Due method is Monthly, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m', strtotime($date));

        $start_date_obj = DateTime::createFromFormat('Y-m', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m', $current_date);

        $interval = new DateInterval('P1M'); // Create a one month interval
        //condition start
        $count = 0;
        $loandate_tillnow = 0;
        $countForPenalty = 0;

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
            $start_date_obj->add($interval);

            $checkcollection = $pdo->query("SELECT * FROM `collection` WHERE `cus_profile_id` = '$cp_id' && ((MONTH(coll_date)= MONTH('$penalty_checking_date') || MONTH(trans_date)= MONTH('$penalty_checking_date')) && (YEAR(coll_date)= YEAR('$penalty_checking_date') || YEAR(trans_date)= YEAR('$penalty_checking_date')))");
            $collectioncount = $checkcollection->rowCount(); // Checking whether the collection are inserted on date or not by using penalty_raised_date.

            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "'");
            } else {
                $result = $pdo->query("SELECT overdue_penalty_percent as overdue FROM `scheme` WHERE `id` = '" . $loan_arr['scheme_name'] . "' ");
            }
            $row = $result->fetch();
            $penalty_per = $row['overdue']; //get penalty percentage to insert
            $penalty = round(($response['due_amt'] * $penalty_per) / 100);


            if ($loan_arr['loan_type'] == 'interest' and $count == 0) {
                // if loan type is interest and when this loop for first month crossed then we need to calculate toPaytilldate again
                // coz for first month interest amount may vary depending on start date of due, so reduce one due amt from it and add the calculated first month interest to it
                $toPaytilldate = $toPaytilldate - $response['due_amt'] + getTillDateInterest($loan_arr, $response, $pdo, 'fullstartmonth', $date);
            }

            if ($totalPaidAmt < $toPaytilldate && $collectioncount == 0) {
                $checkPenalty = $pdo->query("SELECT * from penalty_charges where penalty_date = '$penalty_date' and cus_profile_id = '$cp_id' ");
                if ($checkPenalty->rowCount() == 0) {
                    if ($loan_arr['loan_type'] == 'emi') {
                        //if loan type is emi then directly apply penalty when month crossed and above conditions true
                    } else if ($loan_arr['loan_type'] == 'interest' and  $count != 0) {
                        // if loan type is interest then apply penalty if the loop month is not first
                        // so penalty should not raise, coz a month interest is paid after the month end
                    }
                }
                $countForPenalty++;
            }

            $count++; //Count represents how many months are exceeded
        }
        //condition END

        //this collection query for taking the paid amount until the looping date ($current_date) , to calculate dynamically for due chart
        $qry = $pdo->query("SELECT sum(due_amt_track) as due_amt_track, sum(pre_close_waiver) as pre_close_waiver from `collection` 
        where cus_profile_id = $cp_id 
        AND 
        ( 
            ( ( YEAR(trans_date) = YEAR('$date') AND MONTH(trans_date) <= MONTH('$date') ) OR ( YEAR(trans_date) < YEAR('$date') ) AND trans_date !='0000-00-00') 
            OR 
            ( ( YEAR(coll_date) = YEAR('$date') AND MONTH(coll_date) <= MONTH('$date') ) OR ( YEAR(coll_date) < YEAR('$date') ) )
        ) ");
        if ($qry->rowCount() > 0) {
            $rowss = $qry->fetch();
            $tot_paid_tilldate = intVal($rowss['due_amt_track']);
            $preclose_tilldate = intVal($rowss['pre_close_waiver']);
        }
        if ($count > 0) {


            if ($loan_arr['loan_type'] == 'interest') {

                $response['pending'] = (($response['due_amt'] * ($count)) - $response['due_amt'] + getTillDateInterest($loan_arr, $response, $pdo, 'fullstartmonth', $date)) - $response['total_paid_int'];
            } else {

                //if Due month exceeded due amount will be as pending with how many months are exceeded and subract pre closure amount if available
                $response['pending'] = ($response['due_amt'] * ($count)) - $tot_paid_tilldate - $preclose_tilldate;
            }

            // If due month exceeded
            if ($loan_arr['scheme_name'] == '' || $loan_arr['scheme_name'] == null) {
                $result = $pdo->query("SELECT  overdue_penalty as overdue FROM `loan_category_creation` WHERE `id` = '" . $loan_arr['loan_category'] . "'");
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
                    //if this condition means, more than 1 month is crossed from start month
                    //pending amount will be calculated above for all other loan types as usual
                    //for interest type, we should not calculate due month multiplied by count of month crossed.
                    //in interest loan we need to calculate interest amount of first month by how many days are used in first month only
                    //so that, here subracted one month due amt and added first month's interest based on days spent there
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
            $response['till_date_int'] = getTillDateInterest($loan_arr, $response, $pdo, 'from01', $date);
        } else {
            //If still current month is not ended, then pending will be same due amt // pending will be 0 if due date not exceeded
            $response['pending'] = 0; // $response['due_amt'] - $response['total_paid'] - $response['pre_closure'] ;
            //If still current month is not ended, then penalty will be 0
            $response['penalty'] = 0;
            //If still current month is not ended, then payable will be due amt
            $response['payable'] = $response['due_amt'] - $tot_paid_tilldate - $preclose_tilldate;

            if ($loan_arr['loan_type'] == 'interest') {
                $response['payable'] =  0;
            }

            //in this calculate till date interest when month are not crossed for due starting month
            $response['till_date_int'] = getTillDateInterest($loan_arr, $response, $pdo, 'forstartmonth', $date);
        }
    } else
    if ($loan_arr['scheme_due_method'] == '2') {

        //If Due method is Weekly, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d', strtotime($date));

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1W'); // Create a one Week interval
        //condition start
        $count = 0;
        $loandate_tillnow = 0;
        $countForPenalty = 0;

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
            $penalty = round(($response['due_amt'] * $penalty_per) / 100);
            $count++; //Count represents how many months are exceeded

            if ($totalPaidAmt < $toPaytilldate && $collectioncount == 0) {
                $checkPenalty = $pdo->query("SELECT * from penalty_charges where penalty_date = '$penalty_checking_date' and cus_profile_id = '$cp_id' ");
                if ($checkPenalty->rowCount() == 0) {
                }
                $countForPenalty++;
            }
        }
        //condition END

        //this collection query for taking the paid amount until the looping date ($current_date) , to calculate dynamically for due chart
        $qry = $pdo->query("SELECT sum(due_amt_track) as due_amt_track, sum(pre_close_waiver) as pre_close_waiver from `collection` 
            where cus_profile_id = '$cp_id' 
            AND (
                (YEAR(trans_date) = YEAR('$current_date') AND WEEK(trans_date) <= WEEK('$current_date'))
                OR (YEAR(trans_date) < YEAR('$current_date'))
                OR (YEAR(coll_date) = YEAR('$current_date') AND WEEK(coll_date) <= WEEK('$current_date'))
                OR (YEAR(coll_date) < YEAR('$current_date'))
            ) ");
        if ($qry->rowCount() > 0) {
            $rowss = $qry->fetch();
            $tot_paid_tilldate = intVal($rowss['due_amt_track']);
            $preclose_tilldate = intVal($rowss['pre_close_waiver']);
        }
        if ($count > 0) {
            //if Due month exceeded due amount will be as pending with how many months are exceeded and subract pre closure amount if available
            $response['pending'] = ($response['due_amt'] * $count) - $tot_paid_tilldate - $preclose_tilldate;

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
            $response['payable'] = $response['due_amt'] - $tot_paid_tilldate - $preclose_tilldate;
        }
    } elseif ($loan_arr['scheme_due_method'] == '3') {
        //If Due method is Daily, Calculate penalty by checking the month has ended or not
        $current_date = date('Y-m-d', strtotime($date));

        $start_date_obj = DateTime::createFromFormat('Y-m-d', $due_start_from);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $maturity_month);
        $current_date_obj = DateTime::createFromFormat('Y-m-d', $current_date);

        $interval = new DateInterval('P1D'); // Create a one Week interval
        //condition start
        $count = 0;
        $loandate_tillnow = 0;
        $countForPenalty = 0;

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
            $penalty = round(($response['due_amt'] * $penalty_per) / 100);
            $count++; //Count represents how many months are exceeded

            if ($totalPaidAmt < $toPaytilldate && $collectioncount == 0) {
                $checkPenalty = $pdo->query("SELECT * from penalty_charges where penalty_date = '$penalty_checking_date' and cus_profile_id = '$cp_id' ");
                if ($checkPenalty->rowCount() == 0) {
                }
                $countForPenalty++;
            }
        }
        //condition END

        //this collection query for taking the paid amount until the looping date ($current_date) , to calculate dynamically for due chart
        $qry = $pdo->query("SELECT sum(due_amt_track) as due_amt_track, sum(pre_close_waiver) as pre_close_waiver from `collection` where cus_profile_id = $cp_id and (date(coll_date) <= date('$current_date') or date(trans_date) <= date('$current_date')) ");
        if ($qry->rowCount() > 0) {
            $rowss = $qry->fetch();
            $tot_paid_tilldate = intVal($rowss['due_amt_track']);
            $preclose_tilldate = intVal($rowss['pre_close_waiver']);
        }
        if ($count > 0) {


            //if Due month exceeded due amount will be as pending with how many months are exceeded and subract pre closure amount if available
            $response['pending'] = ($response['due_amt'] * $count) - $tot_paid_tilldate - $preclose_tilldate;

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
            $response['payable'] = $response['due_amt'] - $tot_paid_tilldate - $preclose_tilldate;
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

function calculateNewInterestAmt($loan_arr, $response)
{
    //to calculate current interest amount based on current balance value//bcoz interest will be calculated based on current balance amt only for interest loan
    $int = $response['balance'] * ($loan_arr['int_rate'] / 100);
    $curInterest = ceil($int / 5) * 5; //to increase Interest to nearest multiple of 5
    if ($curInterest < $int) {
        $curInterest += 5;
    }
    $response = $curInterest;

    return $response;
}

function getTillDateInterest($loan_arr, $response, $pdo, $data, $date)
{

    if ($data == 'from01') {
        //in this calculate till date interest when month are crossed for current month

        //to calculate till date interest if loan is interst based
        if ($loan_arr['loan_type'] == 'interest') {

            // Get the current month's count of days
            $currentMonthCount = date('t', strtotime($date));
            // divide current interest amt for one day of current month
            $amtperDay = $response['due_amt'] / intVal($currentMonthCount);

            $st_date = new DateTime(date('Y-m-01', strtotime($date))); // start date
            $tdate = new DateTime(date('Y-m-d', strtotime($date . '+1 day'))); //current date
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
            $tdate = new DateTime(date('Y-m-d', strtotime($date . '+1 day'))); //current date
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
            $amtperDay = $response['due_amt'] / intVal($currentMonthCount);

            $st_date = new DateTime(date('Y-m-d', strtotime($loan_arr['due_startdate']))); // start date
            $tdate = new DateTime(date('Y-m-t', strtotime($loan_arr['due_startdate']))); //current date
            // $tdate = $tdate->modify('+1 day');//current date +1
            // Calculate the interval between the two dates
            $date_diff = $st_date->diff($tdate);
            // Get the number of days from the interval
            $numberOfDays = $date_diff->days;
            $response = ceil($amtperDay * $numberOfDays);
        }
    }
    return $response;
}

?>
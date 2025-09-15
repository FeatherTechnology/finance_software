<?php
include '../../ajaxconfig.php';
$to_date = $_POST['toDate'];
?>

<table id="ledger_view_dailyreport_table" class="table custom-table">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Cus ID</th>
            <th>Aadhar Number</th>
            <th>Loan ID</th>
            <th>Loan Date</th>
            <th>Maturity Date</th>
            <th>Balance Amount</th>
            <th>Sub Status</th>
            <?php
            $todate = new DateTime($to_date);
            $start = new DateTime($todate->format('Y-m-01'));
            $end = new DateTime($todate->format('Y-m-d'));
            $total_dates = 0;
            for ($date = $start; $date <= $end; $date->modify('+1 day')) {
                $total_dates++;
            ?>
                <th>
                    <?php echo $date->format('d'); ?>
                </th>
            <?php
            }
            ?>
            <th>Paid Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT
        cp.id,
        cp.cus_id,
        cp.aadhar_num,
        lelc.loan_id,
        li.issue_date,
        lelc.maturity_date,
        c.princ_amt_track, 
        c.int_amt_track,
        lelc.loan_amount,
        c.coll_sub_status
    FROM
        loan_issue li
    JOIN customer_profile cp ON li.cus_profile_id = cp.id
    JOIN loan_entry_loan_calculation lelc ON li.cus_profile_id = lelc.cus_profile_id
    LEFT JOIN (
        SELECT 
            cus_profile_id, 
            SUM(princ_amt_track) AS princ_amt_track, 
            SUM(int_amt_track) AS int_amt_track,
            SUM(bal_amt) AS bal_amt,
            coll_sub_status
        FROM collection 
        WHERE coll_date <= '$to_date'
        GROUP BY cus_profile_id
    ) c ON li.cus_profile_id = c.cus_profile_id
    WHERE
    lelc.profit_type = 0 AND lelc.due_type = 'Interest' AND lelc.interest_calculate = 'Days'
    AND li.issue_date <= '$to_date'
    AND (
        (IFNULL(c.princ_amt_track, 0) != lelc.loan_amount)
        OR (
            (IFNULL(c.princ_amt_track, 0) = lelc.loan_amount)
            AND EXISTS (
                SELECT 1 FROM collection col 
                WHERE col.cus_profile_id = li.cus_profile_id 
                AND DATE(col.coll_date) = DATE('" . date('Y-m-d', strtotime($to_date)) . "')
            )
        )
    )
    GROUP BY li.cus_profile_id 
    ORDER BY li.id ASC";

        //loan type Scheme = 1 and daily loan =3. 
        $dailyData = $pdo->prepare($query);
        $dailyData->execute();
        $i = 1;
        $total_bal_sum = 0;
        $total_paid_sum = 0;
        while ($dailyInfo = $dailyData->fetch()) {
            $total_paid = 0;
        ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $dailyInfo['cus_id']; ?></td>
                <td><?php echo $dailyInfo['aadhar_num']; ?></td>
                <td><?php echo $dailyInfo['loan_id']; ?></td>
                <td><?php echo date('d-m-Y', strtotime($dailyInfo['issue_date'])); ?></td>
                <td><?php echo date('d-m-Y', strtotime($dailyInfo['maturity_date'])); ?></td>
                <td>
                    <?php
                    $balance_amount = intval($dailyInfo['loan_amount']) - (intval($dailyInfo['princ_amt_track']) + intval($dailyInfo['principal_waiver']));
                    echo moneyFormatIndia($balance_amount);
                    ?>
                </td>
                <td><?php echo ($dailyInfo['coll_sub_status']) ? $dailyInfo['coll_sub_status'] : 'Current'; ?></td>
                <?php
                $start = new DateTime($todate->format('Y-m-01'));
                $end = new DateTime($todate->format('Y-m-d'));
                $total_paid = 0;
                for ($date = $start; $date <= $end; $date->modify('+1 day')) {
                    $coll_qry = $pdo->query('SELECT SUM(int_amt_track) AS int_amt_track FROM collection WHERE cus_profile_id = ' . $dailyInfo['id'] . ' AND date(coll_date) = "' . date('Y-m-d', strtotime($date->format('Y-m-d'))) . '" ORDER BY id DESC ');
                    $int_amt_track = $coll_qry->fetch()['int_amt_track'] ?? 0;
                    $total_paid = $total_paid + $int_amt_track;
                ?>
                    <td><?php echo moneyFormatIndia($int_amt_track); ?></td>
                <?php
                }
                ?>
                <td><?php echo moneyFormatIndia($total_paid); ?></td>
            <?php
            $total_bal_sum += $balance_amount;
            $total_paid_sum += $total_paid;
        }
            ?>

    </tbody>
    <tfoot>
        <?php
        $tfoot = "<tr><td colspan='6'><b>Total</b></td><td><b>" . moneyFormatIndia($total_bal_sum) . "</b></td><td></td><td colspan=" . $total_dates . "></td><td><b>" . moneyFormatIndia($total_paid_sum) . "</b></td></tr>";
        echo $tfoot;
        ?>
    </tfoot>
</table>

<?php
function moneyFormatIndia($num)
{
    $explrestunits = "";
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3);
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
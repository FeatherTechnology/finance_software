<?php
require "../../../ajaxconfig.php";

$loan_issue_list_arr = array();
$cash_type = $_POST['cash_type'];
$bank_id = $_POST['bank_id'];

if ($cash_type == '1') {
    $cndtn = "and li.payment_mode = '1' ";
} elseif ($cash_type == '2') {
    $cndtn = " and  li.bank_name = '$bank_id' ";
}
//collection_mode = 1 - cash; 2 to 5 - bank;
$current_date = date('Y-m-d');
if($cash_type == 1 ){
    $qry = $pdo->query("SELECT b.name, c.linename,COUNT(DISTINCT li.cus_profile_id) AS no_of_loans, COALESCE(SUM(li.cash),0) AS issueAmnt 
    FROM loan_issue li
    JOIN users b ON li.insert_login_id = b.id 
    JOIN line_name_creation c ON b.line = c.id 
    WHERE DATE(li.issue_date) = '$current_date' $cndtn
    GROUP BY b.name, c.linename, li.insert_login_id; ");    
}else{
    $qry = $pdo->query("SELECT b.name, c.linename,COUNT(DISTINCT li.cus_profile_id) AS no_of_loans, COALESCE(SUM(cheque_val) + SUM(transaction_val),0) AS issueAmnt 
    FROM loan_issue li
    JOIN users b ON li.insert_login_id = b.id 
    JOIN line_name_creation c ON b.line = c.id 
    WHERE DATE(li.issue_date) = '$current_date' $cndtn
    GROUP BY b.name, c.linename, li.insert_login_id;" ); 

}  
if ($qry->rowCount() > 0) {
    while ($data = $qry->fetch(PDO::FETCH_ASSOC)) {
        $data['no_of_loans'] = ($data['no_of_loans']) ? $data['no_of_loans'] : 0;
        $data['issueAmnt'] = ($data['issueAmnt']) ? moneyFormatIndia($data['issueAmnt']) : 0;
        $loan_issue_list_arr[] = $data;
    }
}

echo json_encode($loan_issue_list_arr);

//Format number in Indian Format
function moneyFormatIndia($num1)
{
    if ($num1 < 0) {
        $num = str_replace("-", "", $num1);
    } else {
        $num = $num1;
    }
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

    if ($num1 < 0 && $num1 != '') {
        $thecash = "-" . $thecash;
    }

    return $thecash;
}

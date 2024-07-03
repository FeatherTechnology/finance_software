<?php
require "../../../ajaxconfig.php";

$collection_list_arr = array();
$cash_type = $_POST['cash_type'];
$bank_id = $_POST['bank_id'];

if($cash_type =='1'){
    $cndtn = "coll_mode = '1' ";

}elseif($cash_type =='2'){
    $cndtn = "coll_mode != '1' AND bank_id = '$bank_id' ";
    
}
// $qry = $pdo->query("SELECT created_on FROM accounts_collect_entry WHERE user_id = u.id ORDER BY DESC LIMIT 1");
//collection_mode = 1 - cash; 2 to 5 - bank;
$qry = $pdo->query("SELECT u.id as userid, u.name, lnc.linename, bc.branch_name, (SELECT COUNT(*) as no_bills FROM collection sc WHERE $cndtn AND sc.insert_login_id = u.id AND DATE(sc.coll_date) = CURDATE()) as no_of_bills, SUM(c.total_paid_track) + SUM(c.total_waiver) as total_amount, (SELECT SUM(collection_amnt) as collect_amnt FROM accounts_collect_entry WHERE user_id = u.id AND DATE(created_on) = CURDATE()) as collected_amnt
FROM `collection` c
JOIN line_name_creation lnc ON c.line = lnc.id
JOIN branch_creation bc ON c.branch = bc.id
JOIN users u ON FIND_IN_SET(c.line, u.line)
WHERE $cndtn AND DATE(c.coll_date) = CURDATE() GROUP BY u.id ");
if ($qry->rowCount() > 0) {
    while ($data = $qry->fetch(PDO::FETCH_ASSOC)) {
        $disabled = ($data['total_amount'] == $data['collected_amnt']) ? 'disabled' : ''; // 1 - disabled; 2 - enabled;
        $data['total_amount'] = moneyFormatIndia($data['total_amount']);
        $data['action'] = "<a href='#' class='collect-money' value='" . $data['userid'] . "'><button class='btn btn-primary' ".$disabled.">Collect</button></a> ";
        $collection_list_arr[] = $data;
    }
}

echo json_encode($collection_list_arr);

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

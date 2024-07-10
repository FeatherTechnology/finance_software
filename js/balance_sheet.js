$(document).ready(function(){

}); ////// Document END.

$(function(){
    getOpeningBal();
    // getClosingBal();
    getBalSheetDetails();
    getNetBenefitDetails();
});

function getOpeningBal(){
    $.post('api/accounts_files/accounts/opening_balance.php',function(response){
        if(response.length > 0){
            $('#balance_sheet_table tbody tr:first td:nth-child(2)').text(response[0]['opening_balance']);
        }
    },'json').then(function(){
        getClosingBal();
    });
}

function getClosingBal(){
    $.post('api/accounts_files/accounts/closing_balance.php',function(response){
        if(response.length > 0){
            let close = parseInt($('#balance_sheet_table tbody tr:first td:nth-child(2)').text()) + parseInt(response[0]['closing_balance']);
            $('#balance_sheet_table tbody tr:nth-child(18) td:nth-child(3)').text(close);
        }
    },'json');
}

function getBalSheetDetails(){
    $.post('api/accounts_files/balance_sheet_files/balance_sheet_details.php',function(response){
        $('#balance_sheet_table tbody tr:nth-child(3) td:nth-child(2)').text(response[0]['due']);
        $('#balance_sheet_table tbody tr:nth-child(4) td:nth-child(2)').text(response[0]['princ']);
        $('#balance_sheet_table tbody tr:nth-child(5) td:nth-child(2)').text(response[0]['intrst']);
        $('#balance_sheet_table tbody tr:nth-child(6) td:nth-child(2)').text(response[0]['penalty']);
        $('#balance_sheet_table tbody tr:nth-child(7) td:nth-child(2)').text(response[0]['fine']);

        $('#balance_sheet_table tbody tr:nth-child(9) td:nth-child(2)').text(response[0]['invcr']);
        $('#balance_sheet_table tbody tr:nth-child(10) td:nth-child(2)').text(response[0]['depcr']);
        $('#balance_sheet_table tbody tr:nth-child(11) td:nth-child(2)').text(response[0]['elcr']);
        $('#balance_sheet_table tbody tr:nth-child(12) td:nth-child(2)').text(response[0]['exccr']);
        $('#balance_sheet_table tbody tr:nth-child(13) td:nth-child(2)').text(response[0]['contracr']);
        $('#balance_sheet_table tbody tr:nth-child(14) td:nth-child(2)').text(response[0]['oicr']);

        $('#balance_sheet_table tbody tr:nth-child(9) td:nth-child(3)').text(response[0]['invdr']);
        $('#balance_sheet_table tbody tr:nth-child(10) td:nth-child(3)').text(response[0]['depdr']);
        $('#balance_sheet_table tbody tr:nth-child(11) td:nth-child(3)').text(response[0]['eldr']);
        $('#balance_sheet_table tbody tr:nth-child(12) td:nth-child(3)').text(response[0]['excdr']);
        $('#balance_sheet_table tbody tr:nth-child(13) td:nth-child(3)').text(response[0]['contradr']);

        $('#balance_sheet_table tbody tr:nth-child(16) td:nth-child(3)').text(response[0]['advdr']);
        $('#balance_sheet_table tbody tr:nth-child(17) td:nth-child(3)').text(response[0]['expdr']);
        
    },'json').then(function(){
        setTimeout(() => {
            getBalSheetTotal();
        }, 1000);
    });
}

function getBalSheetTotal() {
    var credit_total = 0;
    var debit_total = 0;
    $('#balance_sheet_table tbody tr').each(function () {
        var credit = $(this).find('td:nth-child(2)').text(); // credit amount
        var debit = $(this).find('td:nth-child(3)').text(); // debit amount
        credit_total += parseInt(credit) || 0;
        debit_total += parseInt(debit) || 0;
    });
    
    credit_total = moneyFormatIndia(credit_total);
    debit_total = moneyFormatIndia(debit_total);

    $('#balance_sheet_table tbody tr:nth-child(20) td:nth-child(2)').text(credit_total).css('font-weight','bold');
    $('#balance_sheet_table tbody tr:nth-child(20) td:nth-child(3)').text(debit_total).css('font-weight','bold');
}

function getNetBenefitDetails(){
    $.post('api/accounts_files/balance_sheet_files/net_benefit_details.php',function(response){
        $('#net_benefit_table tbody tr:nth-child(2) td:nth-child(2)').text(response[0]['benefit']);
        $('#net_benefit_table tbody tr:nth-child(3) td:nth-child(2)').text(response[0]['intrst']);
        $('#net_benefit_table tbody tr:nth-child(4) td:nth-child(2)').text(response[0]['doc_charges']);
        $('#net_benefit_table tbody tr:nth-child(5) td:nth-child(2)').text(response[0]['proc_charges']);
        $('#net_benefit_table tbody tr:nth-child(6) td:nth-child(2)').text(response[0]['penalty']);
        $('#net_benefit_table tbody tr:nth-child(7) td:nth-child(2)').text(response[0]['fine']);
        $('#net_benefit_table tbody tr:nth-child(8) td:nth-child(2)').text(response[0]['oicr']);

        $('#net_benefit_table tbody tr:nth-child(10) td:nth-child(3)').text(response[0]['expdr']);
        
    },'json').then(function(){
        setTimeout(() => {
            getNetBenefitTotal();
        }, 1000);
    });
}

function getNetBenefitTotal() {
    let credit_total = 0;
    let debit_total = 0;
    $('#net_benefit_table tbody tr').each(function () {
        let credit = $(this).find('td:nth-child(2)').text(); // credit amount
        let debit = $(this).find('td:nth-child(3)').text(); // debit amount
        credit_total += parseInt(credit) || 0;
        debit_total += parseInt(debit) || 0;
    });
    
    let benefit = credit_total - debit_total;
    credit_total = moneyFormatIndia(credit_total);
    debit_total = moneyFormatIndia(debit_total);
    benefit_total = moneyFormatIndia(benefit);

    $('#net_benefit_table tbody tr:nth-child(12) td:nth-child(2)').text(credit_total).css('font-weight','bold');
    $('#net_benefit_table tbody tr:nth-child(12) td:nth-child(3)').text(debit_total).css('font-weight','bold');
    $('#net_benefit_table tbody tr:nth-child(13) td:nth-child(2)').text(benefit_total).css('font-weight','bold');
}


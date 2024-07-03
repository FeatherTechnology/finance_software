$(document).ready(function(){
    $('input[name=accounts_type]').click(function () {
        let accountsType = $(this).val();
        if (accountsType == '1') {
            $('#coll_card').show(); $('#loan_issued_card').hide(); $('#expenses_card').hide(); $('#other_transaction_card').hide();
            getBankName();
        } else if (accountsType == '2') {
            $('#coll_card').hide(); $('#loan_issued_card').show(); $('#expenses_card').hide(); $('#other_transaction_card').hide();
            getBankName();
        } else if (accountsType == '3') {
            $('#coll_card').hide(); $('#loan_issued_card').hide(); $('#expenses_card').show(); $('#other_transaction_card').hide();
            
        } else if (accountsType == '4') {
            $('#coll_card').hide(); $('#loan_issued_card').hide(); $('#expenses_card').hide(); $('#other_transaction_card').show();
            
        }
    });

    $("input[name='coll_cash_type']").click(function(){
        let collCashType = $(this).val();

        if (collCashType == '2') {
            $('#coll_bank_name').val('').attr('disabled', false);
            $('#accounts_collection_table').DataTable().destroy();
            $('#accounts_collection_table tbody').empty()
        }else{
            $('#coll_bank_name').val('').attr('disabled', true);
            getCollectionList();
        }
    });

    $('#coll_bank_name').change(function(){
        getCollectionList();
    });
    
    $(document).on('click', '.collect-money', function(event){
        event.preventDefault();
        let id = $(this).attr('value');
        let line = $(this).closest('tr').find('td:nth-child(3)').text();
        let branch = $(this).closest('tr').find('td:nth-child(4)').text();
        let no_of_bills = $(this).closest('tr').find('td:nth-child(5)').text();
        let collected_amnt = $(this).closest('tr').find('td:nth-child(6)').text();
        
        $.post('api/accounts_files/accounts/submit_collect.php', {id, line, branch, no_of_bills, collected_amnt}, function(response){
            if (response == '1') {
                swalSuccess('Success', 'Collected Successfully.');
                getCollectionList();
            }else{
                swalError('Error', 'Something went wrong.');
            }

        },'json');
    });

    $('#name_modal_btn').click(function () {
        if ($('#add_other_transaction_modal').is(':visible')) {
            $('#add_other_transaction_modal').hide();
        }
    });

    $('.name_close').click(function () {
        if ($('#add_other_transaction_modal').is(':hidden')) {
            $('#add_other_transaction_modal').show();
        }
    });

});  /////Document END.

$(function(){
    // getOpeningClosingBal();
});

// function getOpeningClosingBal(){

// }

function getCollectionList(){
    let cash_type = $("input[name='coll_cash_type']:checked").val();
    let bank_id = $('#coll_bank_name :selected').val();
    $.post('api/accounts_files/accounts/accounts_collection_list.php', {cash_type, bank_id},function(response){
        let columnMapping = [
            'sno',
            'name',
            'linename',
            'branch_name',
            'no_of_bills',
            'total_amount',
            'action'
        ];
        appendDataToTable('#accounts_collection_table', response, columnMapping);
        setdtable('#accounts_collection_table');
    },'json');
}

function getBankName() {
    $.post('api/common_files/bank_name_list.php', function (response) {
        var bankName = '<option value="">Select Bank Name</option>';
        $.each(response, function (index, value) {
            bankName += '<option value="' + value.id + '" data-id="' + value.account_number + '">' + value.bank_name + '</option>';
        });
        $('#coll_bank_name').html(bankName);
    }, 'json');
}
$(document).ready(function(){
    $('input[name=accounts_type]').click(function () {
        let accountsType = $(this).val();
        if (accountsType == '1') {
            $('#coll_card').show(); $('#loan_issued_card').hide(); $('#expenses_card').hide(); $('#other_transaction_card').hide();
            getBankName('#coll_bank_name');
        } else if (accountsType == '2') {
            $('#coll_card').hide(); $('#loan_issued_card').show(); $('#expenses_card').hide(); $('#other_transaction_card').hide();
            getBankName('');
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
        let cash_type = $("input[name='coll_cash_type']:checked").val();
        let bank_id = $('#coll_bank_name :selected').val();
        
        $.post('api/accounts_files/accounts/submit_collect.php', {id, line, branch, no_of_bills, collected_amnt, cash_type, bank_id}, function(response){
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

    $('#expenses_add').click(function(){
        getBankName('#expenses_bank_name');
        getInvoiceNo();
        getBranchList();
        expensesTable();
    });
    
    $("input[name='expenses_cash_type']").click(function(){
        let expCashType = $(this).val();

        if (expCashType == '2') {
            $('#expenses_bank_name').val('').attr('disabled', false);
        }else{
            $('#expenses_bank_name').val('').attr('disabled', true);
        }
    });

    $('#expenses_category').change(function(){
        let expCat = $(this).val();
        if(expCat =='14'){
            $('.agentDiv').show();
            getAgentName();
        }else{
            $('.agentDiv').hide();
        }
    });

    $('#submit_expenses_creation').click(function(event){
        event.preventDefault();
        let expensesData = {
            'coll_mode' : $("input[name='expenses_cash_type']:checked").val(),
            'bank_id' : $('#expenses_bank_name :selected').val(),
            'invoice_id' : $('#invoice_id').val(),
            'branch_name' : $('#branch_name :selected').val(),
            'expenses_category' : $('#expenses_category :selected').val(),
            'agent_name' : $('#agent_name :selected').val(),
            'expenses_total_issued' : $('#expenses_total_issued').val(),
            'expenses_total_amnt' : $('#expenses_total_amnt').val(),
            'description' : $('#description').val(),
            'expenses_amnt' : $('#expenses_amnt').val()
        }
        if(expensesFormValid(expensesData)){
            $.post('api/accounts_files/accounts/submit_expenses.php',expensesData,function(response){
                if(response =='1'){
                    swalSuccess('Success', 'Expenses added successfully.');
                    expensesTable();
                }else{
                    swalError('Error', 'Failed.');
                }
            },'json');
        }else{
            swalError('Warning', 'Kindly Fill Mandatory Fields.');
        }
    });

    
    $(document).on('click', '.expDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this Expenses?', deleteExp, id);
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

function getBankName(dropdowndId) {
    $.post('api/common_files/bank_name_list.php', function (response) {
        var bankName = '<option value="">Select Bank Name</option>';
        $.each(response, function (index, value) {
            bankName += '<option value="' + value.id + '" data-id="' + value.account_number + '">' + value.bank_name + '</option>';
        });
        $(dropdowndId).empty().html(bankName);
    }, 'json');
}

function getInvoiceNo(){
    $.post('api/accounts_files/accounts/get_invoice_no.php',{},function(response){
        $('#invoice_id').val(response);
    },'json');
}

function getBranchList(){
    $.post('api/common_files/user_mapped_branches.php',function(response){
        let branchOption;
        branchOption += '<option value="">Select Branch Name</option>';
        $.each(response,function(index,value){
            branchOption += '<option value="'+value.id+'">'+value.branch_name+'</option>';
            });
        $('#branch_name').empty().html(branchOption);
    },'json');
}

function getAgentName() {
    $.post('api/agent_creation/agent_creation_list.php', function (response) {
        let appendAgentIdOption = '';
        appendAgentIdOption += '<option value="">Select Agent Name</option>';
        $.each(response, function (index, val) {
            let selected = '';
            let agent_id_edit_it = '';
            if (val.id == agent_id_edit_it) {
                selected = 'selected';
            }
            appendAgentIdOption += '<option value="' + val.id + '" ' + selected + '>' + val.agent_name + '</option>';
        });
        $('#agent_name').empty().append(appendAgentIdOption);
    }, 'json');
}

function expensesFormValid(expensesData){
    for(key in expensesData){
        if(key !='agent_name' && key !='expenses_total_issued' && key !='expenses_total_amnt' && key !='bank_id'){
            if(expensesData[key] =='' || expensesData[key] ==null || expensesData[key] ==undefined){
                return false;
            }
        }
    }

    if(expensesData['coll_mode'] =='2'){
        if(expensesData['bank_id'] =='' || expensesData['bank_id'] ==null || expensesData['bank_id'] == undefined){
            return false;
        }
    }

    if(expensesData['expenses_category']=='14'){
        if(expensesData['agent_name'] =='' || expensesData['agent_name'] ==null || expensesData['agent_name'] ==undefined || expensesData['expenses_total_issued'] =='' || expensesData['expenses_total_issued'] ==null || expensesData['expenses_total_issued'] ==undefined || expensesData['expenses_total_amnt'] =='' || expensesData['expenses_total_amnt'] ==null || expensesData['expenses_total_amnt'] ==undefined ){
            return false;
        }
    }

    return true;
}

function expensesTable(){
    $.post('api/accounts_files/accounts/get_expenses_list.php',function(response){
        let expensesColumn = [
            'sno',
            'invoice_id',
            'branch',
            'expenses_category',
            'description',
            'amount',
            'action'
        ];

        appendDataToTable('#expenses_creation_table', response, expensesColumn);
        setdtable('#expenses_creation_table');
        clearExpForm();
    },'json');
}

function clearExpForm(){
    $('#expenses_amnt').val('');
    $('#expenses_form select').val('');
    $('#expenses_form textarea').val('');
}

function deleteExp(id) {
    $.post('api/accounts_files/accounts/delete_expenses.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Expenses Deleted Successfully');
            expensesTable();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}
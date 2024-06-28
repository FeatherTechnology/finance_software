$(document).ready(function () {
    $(document).on('click', '.noc-details', function (event) {
        event.preventDefault();
        $('#noc_list').hide();
        $('#noc_main_container,.back_to_noc_list').show();
        let cusid = $(this).attr('value');
        getPersonalInfo(cusid);
        getNOCLoanList(cusid);
    })
    $('#back_to_noc_list').click(function (event) {
        event.preventDefault();
        $('#noc_main_container,.back_to_noc_list').hide();
        $('#noc_list').show();
    })
    $(document).on('click', '.noc-summary', function (event) {
        event.preventDefault();
        $('#noc_summary,.back_to_loan_list').show();
        $('#loan_list, #personal_info,.back_to_noc_list').hide();
        let cp_id = $(this).attr('value');
        callAllFunctions(cp_id);
    })

    $(document).on('click','#remark_view',function(event){
        event.preventDefault();
        let cp_id = $(this).attr('value');
        $.post('api/noc_files/closed_remark_details.php', {cp_id}, function(response){
            $('#sub_status').val(response['sub_status']);
            $('#remark').val(response['remark']);
        },'json');
    });

    $('#back_to_loan_list').click(function (event) {
        event.preventDefault();
        $('#noc_summary, .back_to_loan_list').hide();
        $('#loan_list, #personal_info,.back_to_noc_list').show();
    });

    // $('#noc_member').change(function(){
    //     let noc_member = $(this).val();
    //     $.post('api/noc_files/noc_member.php', {noc_member}, function(response){
    //         $('#noc_relation').val(response['name']);
    //     },'json');
    // });
    
    $('#noc_member').change(function () {
        let id = $(this).val();
        if (id != '' && id != 'Customer') {
            getRelationship(id);
        } else if (id == 'Customer') {
            $('#noc_relation').val('Customer');
        } else {
            $('#noc_relation').val('');
        }
        
        setTimeout(() => {
            let date = $('#date_of_noc').val();
            let formattedDate = formatDate(date);
            let name = $('#noc_member').find(":selected").text();
            let relationship = $('#noc_relation').val();
    
            let chequeTable = $('#noc_cheque_list_table').DataTable();
            updateColumns(chequeTable, formattedDate, 6, name, 7, relationship, 8);
    
            let mortTable = $('#noc_mortgage_list_table').DataTable();
            updateColumns(mortTable, formattedDate, 7, name, 8, relationship, 9);
    
            let endorseTable = $('#noc_endorsement_list_table').DataTable();
            updateColumns(endorseTable, formattedDate, 7, name, 8, relationship, 9);
    
            let docTable = $('#noc_document_list_table').DataTable();
            updateColumns(docTable, formattedDate, 5, name, 6, relationship, 7);
    
            let goldTable = $('#noc_gold_list_table').DataTable();
            updateColumns(goldTable, formattedDate, 4, name, 5, relationship, 6);
        }, 1000);
    });

    $('#submit_noc').click(function (event) {
        event.preventDefault();
        if (validate()) {
            //form submit
        }
    });


    setDropdownScripts();
    setCurrentDate('#date_of_noc');
});  //Document END.

$(function () {
    getNOCList();
});

function getNOCList() {
    $.post('api/noc_files/noc_list.php', function (response) {
        let nocColumns = [
            'sno',
            'cus_id',
            'cus_name',
            'area',
            'linename',
            'branch_name',
            'mobile1',
            'action'
        ];
        appendDataToTable('#noc_list_table', response, nocColumns);
        setdtable('#noc_list_table')
    }, 'json');
}

function getPersonalInfo(cus_id) {
    $.post('api/common_files/personal_info.php', { cus_id }, function (response) {
        $('#cus_id').val(response[0].cus_id);
        $('#cus_name').val(response[0].cus_name);
        $('#cus_area').val(response[0].area);
        $('#cus_branch').val(response[0].branch_name);
        $('#cus_line').val(response[0].linename);
        $('#cus_mobile').val(response[0].mobile1);

        let path = "uploads/loan_entry/cus_pic/";
        var img = $('#cus_image');
        img.attr('src', path + response[0].pic);
    }, 'json');
}

function getNOCLoanList(cus_id) {
    $.ajax({
        url: 'api/noc_files/noc_loan_list.php',
        data: { 'cus_id': cus_id},
        type: 'post',
        dataType: 'json',
        cache: false,
        success: function (response) {
            var columnMapping = [
                'sno',
                'loan_id',
                'loan_category',
                'issue_date',
                'closed_date',
                'loan_amount',
                'status',
                'sub_status',
                'action'
            ];
            appendDataToTable('#noc_loan_list_table', response, columnMapping);
            setdtable('#noc_loan_list_table');
            //Dropdown in List Screen
            setDropdownScripts();
        }
    });
}

function callAllFunctions(cp_id){
    getChequeList(cp_id);
    getMortgageList(cp_id);
    getEndorsementList(cp_id);
    getOtherDocumentList(cp_id);
    getGoldList(cp_id);
    // setCurrentDate('#date_of_noc');
    {
        // Get today's date
        var today = new Date().toISOString().split('T')[0];
        //Set NOC date
        $('#date_of_noc').val(today);
    }
    getFamilyMember();
}

function getChequeList(cp_id) {
    $.post('api/noc_files/noc_cheque_list.php', {cp_id}, function(response){
        let nocChequeColumns = [
            'sno',
            'holder_type',
            'holder_name',
            'relationship',
            'bank_name',
            'cheque_no',
            'd_noc',
            'h_person',
            'relation',
            'action'
        ];
        appendDataToTable('#noc_cheque_list_table', response, nocChequeColumns);
        setdtable('#noc_cheque_list_table');
    },'json');
}

function getMortgageList(cp_id) {
    $.post('api/noc_files/noc_mortgage_list.php', {cp_id}, function(response){
        let nocMortgageColumns = [
            'sno',
            'fam_name',
            'relationship',
            'property_details',
            'mortgage_name',
            'designation',
            'reg_office',
            'd_noc',
            'h_person',
            'relation',
            'action'
        ];
        appendDataToTable('#noc_mortgage_list_table', response, nocMortgageColumns);
        setdtable('#noc_mortgage_list_table');
    },'json');
}

function getEndorsementList(cp_id) {
    $.post('api/noc_files/noc_endorsement_list.php', {cp_id}, function(response){
        let nocEndorseColumns = [
            'sno',
            'fam_name',
            'relationship',
            'vehicle_details',
            'endorsement_name',
            'key_original',
            'rc_original',
            'd_noc',
            'h_person',
            'relation',
            'action'
        ];
        appendDataToTable('#noc_endorsement_list_table', response, nocEndorseColumns);
        setdtable('#noc_endorsement_list_table');
    },'json');
}

function getOtherDocumentList(cp_id) {
    $.post('api/noc_files/noc_document_info_list.php', {cp_id}, function(response){
        let nocDocInfoColumns = [
            'sno',
            'doc_name',
            'doc_type',
            'fam_name',
            'upload',
            'd_noc',
            'h_person',
            'relation',
            'action'
        ];
        appendDataToTable('#noc_document_list_table', response, nocDocInfoColumns);
        setdtable('#noc_document_list_table');
    },'json');
}

function getGoldList(cp_id) {
    $.post('api/noc_files/noc_gold_list.php', {cp_id}, function(response){
        let nocGoldColumns = [
            'sno',
            'gold_type',
            'purity',
            'weight',
            'd_noc',
            'h_person',
            'relation',
            'action'
        ];
        appendDataToTable('#noc_gold_list_table', response, nocGoldColumns);
        setdtable('#noc_gold_list_table');
    },'json');
}

function getFamilyMember() {
    let cus_id = $('#cus_id').val();
    let cus_name = $('#cus_name').val();
    $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
        let appendOption = '';
        appendOption += "<option value=''>Select Member Name</option>";
        appendOption += "<option value='Customer'>"+cus_name+"</option>";
        $.each(response, function (index, val) {
            appendOption += "<option value='" + val.id + "'>" + val.fam_name + "</option>";
        });
        $('#noc_member').empty().append(appendOption);
    }, 'json');
}

function getRelationship(id) {
    $.post('api/loan_entry/family_creation_data.php', { id }, function (response) {
        let relationship = response[0].fam_relationship;
        $('#noc_relation').val(relationship);
    }, 'json');
}

function updateColumns(table, dnoc, dnocIndex, name, nameIndex, relation, relationIndex) {
    table.rows().every(function() {
    var data = this.data();
    // var updated = false;
    
    // if (!data[dnocIndex]) {
    data[dnocIndex] = dnoc;
    // updated = true;
    // }
    
    // if (!data[nameIndex]) {
    data[nameIndex] = name;
    // updated = true;
    // }
    
    // if (!data[relationIndex]) {
    data[relationIndex] = relation;
    // updated = true;
    // }

    // if (updated) {
    this.data(data).draw(false);
    // }
});
}

function formatDate(inputDate) {
    // Split the input date into year, month, and day components
    let parts = inputDate.split('-');
    // Rearrange them in dd-mm-yyyy format
    return parts[2] + '-' + parts[1] + '-' + parts[0];
}

function validate() {
    let response = true;

    return response;
}
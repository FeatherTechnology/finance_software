$(document).ready(function () {
    $(document).on('click', '.edit-loan-issue', function () {
        let id = $(this).attr('value');
        $('#customer_profile_id').val(id);
        // let loanCalcId = $(this).attr('data-id');
        swapTableAndCreation();
        getDocNeedTable(id);
        getDocInfoTable();
        getMortInfoTable();
    });

    $('#back_btn').click(function () {
        swapTableAndCreation();
    });

    $('input[name=loan_issue_type]').click(function () {
        let loanIssueType = $(this).val();
        if (loanIssueType == 'loandoc') {
            $('#documentation_form').show(); $('#loan_issue_form').hide();
        } else if (loanIssueType == 'loanissue') {
            $('#documentation_form').hide(); $('#loan_issue_form').show();
            // callLoanCaculationFunctions();
        }
    })

    //Move Loan Entry 
    $(document).on('click', '.move-loan-entry', function () {
        let cus_sts_id = $(this).attr('value');
        $.post('api/common_files/move_to_approval.php', { cus_sts_id }, function (response) {
            if (response == '0') {
                swalSuccess('Success', 'Moved to Approval');
                getLoanIssueTable();
            } else {
                swalError('Alert', 'Failed to Move to Approval');
            }
        }, 'json');
    });

    ///////////////////////////////////////////////////////////////////Cheque info START ////////////////////////////////////////////////////////////////////////////
    $('#cq_holder_type').change(function () {
        let holderType = $(this).val();
        emptyholderFields();
        if (holderType == '1' || holderType == '2') {
            $('.cq_fam_member').hide();
            let cus_profile_id = $('#customer_profile_id').val();
            getNameRelationship(cus_profile_id, holderType);
        } else {
            getFamilyMember('Select Family Member', '#cq_fam_mem')
            $('.cq_fam_member').show();
        }
    });

    $('#cq_fam_mem').change(function(){
        let famMemId = $(this).val();
        if(famMemId !=''){
            getNameRelationship(famMemId, '3');
        }
    });

    $('#submit_cheque_info').click(function(event){
        event.preventDefault();
        let cus_id = $('#cus_id').val();
        let cq_holder_type = $('#cq_holder_type').val();
        let cq_holder_name = $("#cq_holder_name").val();
        let cq_holder_id = $("#cq_holder_name").attr('data-id');
        let cq_relationship = $('#cq_relationship').val();
        let cq_bank_name = $('#cq_bank_name').val();
        let cheque_count = $('#cheque_count').val();
        let cq_upload = $('#cq_upload')[0].files[0];
        let cq_upload_edit = $('#cq_upload_edit').val();
        let customer_profile_id = $('#customer_profile_id').val();
        let cheque_id = $('#cheque_id').val();
        if (cq_holder_type === '' || cq_holder_name === '' || cq_relationship === '' || cq_bank_name === '' || cheque_count =='' || (cq_upload === undefined && cq_upload_edit == '')) {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        }
        let chequeInfo = new FormData();
        chequeInfo.append('cq_holder_type', cq_holder_type)
        chequeInfo.append('cq_holder_name', cq_holder_name)
        chequeInfo.append('cq_holder_id', cq_holder_id)
        chequeInfo.append('cq_relationship', cq_relationship)
        chequeInfo.append('cheque_count', cheque_count)
        chequeInfo.append('cq_bank_name', cq_bank_name)
        chequeInfo.append('cq_upload', cq_upload)
        chequeInfo.append('cq_upload_edit', cq_upload_edit)
        chequeInfo.append('cus_id', cus_id)
        chequeInfo.append('customer_profile_id', customer_profile_id)
        chequeInfo.append('id', cheque_id)

        $.ajax({
            url: 'api/loan_issue_files/submit_cheque_info.php',
            type: 'post',
            data: chequeInfo,
            contentType: false,
            processData: false,
            cache: false,
            success: function (response) {
                if(response =='1'){
                    swalSuccess('Success', 'Cheque Info Updated Successfully')
                }else if(response =='2'){
                    swalSuccess('Success', 'Cheque Info Added Successfully')
                }else{
                    swalSuccess('Alert', 'Failed')
                }
                getChequeCreationTable();
            }
        });
    });
    ///////////////////////////////////////////////////////////////////Cheque info END ////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////Document info START ////////////////////////////////////////////////////////////////////////////
    $('#doc_holder_name').change(function(){
        let id = $(this).val();
        if(id !=''){
            getRelationship(id, '#doc_relationship')
        }else{
            $('#doc_relationship').val('');
        }
        
    });

    $('#submit_doc_info').click(function(event){
        event.preventDefault();
        let doc_name = $('#doc_name').val();
        let doc_type = $('#doc_type').val();
        let doc_holder_name = $('#doc_holder_name').val();
        let doc_relationship = $('#doc_relationship').val();
        let doc_upload = $('#doc_upload')[0].files[0];
        let doc_upload_edit = $('#doc_upload_edit').val();
        let doc_info_id = $('#doc_info_id').val();
        let cus_id = $('#cus_id').val();
        let customer_profile_id = $('#customer_profile_id').val();
        
        if(doc_name =='' || doc_type =='' || doc_holder_name =='' || doc_relationship =='' || (doc_upload === undefined && doc_upload_edit == '')){
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        }

        let docInfo = new FormData();
            docInfo.append('doc_name', doc_name);
            docInfo.append('doc_type', doc_type);
            docInfo.append('doc_holder_name', doc_holder_name);
            docInfo.append('doc_relationship', doc_relationship);
            docInfo.append('doc_upload', doc_upload);
            docInfo.append('doc_upload_edit', doc_upload_edit);
            docInfo.append('cus_id', cus_id);
            docInfo.append('customer_profile_id', customer_profile_id);
            docInfo.append('id', doc_info_id);

        $.ajax({
            url: 'api/loan_issue_files/submit_document_info.php',
            type: 'post',
            data: docInfo,
            contentType: false,
            processData: false,
            cache: false,
            success: function (response) {
                if(response =='1'){
                    swalSuccess('Success', 'Document Info Updated Successfully')
                }else if(response =='2'){
                    swalSuccess('Success', 'Document Info Added Successfully')
                }else{
                    swalError('Alert', 'Failed')
                }
                getDocCreationTable();
                $('#clear_doc_form').trigger('click');
                $('#doc_info_id').val('');
            }
        });
    });

    $(document).on('click','.docActionBtn', function(){
        let id = $(this).attr('value');
        $.post('api/loan_issue_files/doc_info_data.php', {id}, function(response){
            $('#doc_name').val(response[0].doc_name);
            $('#doc_type').val(response[0].doc_type);
            $('#doc_holder_name').val(response[0].holder_name);
            $('#doc_relationship').val(response[0].relationship);
            $('#doc_upload_edit').val(response[0].upload);
            $('#doc_info_id').val(response[0].id);
        },'json');
    });

    $(document).on('click','.docDeleteBtn', function(){
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this document?', deleteDocInfo, id);
    });
    
    $('#clear_doc_form').click(function(){
        $('#doc_info_id').val('');
    })
    ///////////////////////////////////////////////////////////////////Document info END ////////////////////////////////////////////////////////////////////////////
    
    ///////////////////////////////////////////////////////////////////Mortgage info START ////////////////////////////////////////////////////////////////////////////
    $('#property_holder_name').change(function(){
        let id = $(this).val();
        if(id !=''){
            getRelationship(id, '#mort_relationship')
        }else{
            $('#mort_relationship').val('');
        }
        
    });

    $('#submit_mortgage_info').click(function(event){
        event.preventDefault();
        let property_holder_name = $('#property_holder_name').val();
        let mort_relationship = $('#mort_relationship').val();
        let mort_property_details = $('#mort_property_details').val();
        let mortgage_name = $('#mortgage_name').val();
        let mort_designation = $('#mort_designation').val();
        let mortgage_no = $('#mortgage_no').val();
        let reg_office = $('#reg_office').val();
        let mortgage_value = $('#mortgage_value').val();
        let mortgage_info_id = $('#mortgage_info_id').val();
        let cus_id = $('#cus_id').val();
        let customer_profile_id = $('#customer_profile_id').val();
        let mort_upload = $('#mort_upload')[0].files[0];
        let mort_upload_edit = $('#mort_upload_edit').val();
        
        if(property_holder_name =='' || mort_relationship =='' || mort_property_details =='' || mortgage_name =='' || mort_designation =='' || mortgage_no =='' || reg_office =='' || mortgage_value =='' || (mort_upload === undefined && mort_upload_edit == '')){
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        }

        let docInfo = new FormData();
            docInfo.append('property_holder_name', property_holder_name);
            docInfo.append('mort_relationship', mort_relationship);
            docInfo.append('mort_property_details', mort_property_details);
            docInfo.append('mortgage_name', mortgage_name);
            docInfo.append('mort_designation', mort_designation);
            docInfo.append('mortgage_no', mortgage_no);
            docInfo.append('reg_office', reg_office);
            docInfo.append('mortgage_value', mortgage_value);
            docInfo.append('mort_upload', mort_upload);
            docInfo.append('mort_upload_edit', mort_upload_edit);
            docInfo.append('cus_id', cus_id);
            docInfo.append('customer_profile_id', customer_profile_id);
            docInfo.append('id', mortgage_info_id);

        $.ajax({
            url: 'api/loan_issue_files/submit_mortgage_info.php',
            type: 'post',
            data: docInfo,
            contentType: false,
            processData: false,
            cache: false,
            success: function (response) {
                if(response =='1'){
                    swalSuccess('Success', 'Mortgage Info Updated Successfully')
                }else if(response =='2'){
                    swalSuccess('Success', 'Mortgage Info Added Successfully')
                }else{
                    swalError('Alert', 'Failed')
                }
                getMortCreationTable()
                $('#clear_mortgage_form').trigger('click');
                $('#mortgage_info_id').val('');
            }
        });
    });

    $(document).on('click','.mortActionBtn', function(){
        let id = $(this).attr('value');
        $.post('api/loan_issue_files/mortgage_info_data.php', {id}, function(response){
            $('#property_holder_name').val(response[0].property_holder_name);
            $('#mort_relationship').val(response[0].relationship);
            $('#mort_property_details').val(response[0].property_details);
            $('#mortgage_name').val(response[0].mortgage_name);
            $('#mort_designation').val(response[0].designation);
            $('#mortgage_no').val(response[0].mortgage_number);
            $('#reg_office').val(response[0].reg_office);
            $('#mortgage_value').val(response[0].mortgage_value);
            $('#mort_upload_edit').val(response[0].upload);
            $('#mortgage_info_id').val(response[0].id);
        },'json');
    });

    $(document).on('click','.mortDeleteBtn', function(){
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this Mortgage?', deleteMortgageInfo, id);
    });
    
    $('#clear_mortgage_form').click(function(){
        $('#mortgage_info_id').val('');
    })
    ///////////////////////////////////////////////////////////////////Mortgage info END ////////////////////////////////////////////////////////////////////////////

}); ///////////////////////////////////////////////////////////////// Documentation - Document END ////////////////////////////////////////////////////////////////////

//On Load function 
$(function () {
    getLoanIssueTable();
});

function getLoanIssueTable() {
    $.post('api/loan_issue_files/loan_issue_list.php', function (response) {
        var columnMapping = [
            'sno',
            'cus_id',
            'cus_name',
            'area',
            'linename',
            'branch_name',
            'loan_amount',
            'mobile1',
            'action'
        ];
        appendDataToTable('#loan_issue_table', response, columnMapping);
        setdtable('#loan_issue_table');
        //Dropdown in List Screen
        setDropdownScripts();
    }, 'json');
}

function swapTableAndCreation() {
    if ($('.loanissue_table_content').is(':visible')) {
        $('.loanissue_table_content').hide();
        $('#loan_issue_content').show();
        $('#back_btn').show();

    } else {
        $('.loanissue_table_content').show();
        $('#loan_issue_content').hide();
        $('#back_btn').hide();
        $('#documentation').trigger('click')
    }
}

function getDocNeedTable(cusProfileId) {
    $.post('api/loan_entry/loan_calculation/document_need_list.php', { cusProfileId }, function (response) {
        let loanCategoryColumn = [
            "sno",
            "document_name"
        ]
        appendDataToTable('#doc_need_table', response, loanCategoryColumn);
    }, 'json');
}

function getFamilyMember(optn, selector) {
    let cus_id = $('#cus_id').val();
    $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
        let appendOption = '';
        appendOption += "<option value=''>"+optn+"</option>";
        $.each(response, function (index, val) {
            appendOption += "<option value='" + val.id + "'>" + val.fam_name + "</option>";
        });
        $(selector).empty().append(appendOption);
    }, 'json');
}

function getNameRelationship(id, type){
    $.post('api/loan_issue_files/get_cus_fam_members.php', { id, type }, function (response) {
        if (type == '1') {
            $('#cq_holder_name').val(response[0].cus_name);
            $('#cq_relationship').val('Customer');
        } else {
            $('#cq_holder_name').val(response[0].fam_name);
            $('#cq_holder_name').attr('data-id',response[0].id);
            $('#cq_relationship').val(response[0].fam_relationship);
        }
    }, 'json');
}

function getRelationship(id, selector){
    $.post('api/loan_entry/family_creation_data.php', { id }, function (response) {
        $(selector).val(response[0].fam_relationship);
    }, 'json');
}

function emptyholderFields(){
    $('#cq_holder_name').val('');
    $('#cq_holder_name').attr('data-id','');
    $('#cq_relationship').val('');
}

function getChequeCreationTable(){
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/cheque_info_list.php', { cus_profile_id }, function (response) {
        let loanCategoryColumn = [
            "sno",
            "holder_type",
            "holder_name",
            "relationship",
            "bank_name",
            "cheque_cnt",
            "action"
        ]
        appendDataToTable('#cheque_info_creation_table', response, loanCategoryColumn);
    }, 'json');
}

function getDocCreationTable(){
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/doc_info_list.php', { cus_profile_id }, function (response) {
        let docInfoColumn = [
            "sno",
            "doc_name",
            "doc_type",
            "fam_name",
            "relationship",
            "upload",
            "action"
        ]
        appendDataToTable('#doc_creation_table', response, docInfoColumn);
        setdtable('#doc_creation_table')
    }, 'json');
}

function getDocInfoTable(){
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/doc_info_list.php', { cus_profile_id }, function (response) {
        let docColumn = [
            "sno",
            "doc_name",
            "doc_type",
            "fam_name",
            "relationship",
            "upload"
        ]
        appendDataToTable('#document_info', response, docColumn);
        setdtable('#document_info')
    }, 'json');
}

function deleteDocInfo(id){
    $.post('api/loan_issue_files/delete_doc_info.php', {id}, function(response){
        if(response =='1'){
            swalSuccess('success','Doc Info Deleted Successfully');
            getDocCreationTable();
        }else{
            swalError('Alert', 'Delete Failed')
        }
    },'json');
}

function getMortCreationTable(){
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/mortgage_info_list.php', { cus_profile_id }, function (response) {
        let mortInfoColumn = [
            "sno",
            "fam_name",
            "relationship",
            "property_details",
            "mortgage_name",
            "designation",
            "mortgage_number",
            "reg_office",
            "mortgage_value",
            "upload",
            "action"
        ]
        appendDataToTable('#mortgage_creation_table', response, mortInfoColumn);
        setdtable('#mortgage_creation_table')
    }, 'json');
}

function getMortInfoTable(){
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/mortgage_info_list.php', { cus_profile_id }, function (response) {
        let mortgageColumn = [
            "sno",
            "fam_name",
            "relationship",
            "property_details",
            "mortgage_name",
            "designation",
            "mortgage_number",
            "reg_office",
            "mortgage_value",
            "upload"
        ]
        appendDataToTable('#mortgage_info', response, mortgageColumn);
        setdtable('#mortgage_info')
    }, 'json');
}

function deleteMortgageInfo(id){
    $.post('api/loan_issue_files/delete_mortgage_info.php', {id}, function(response){
        if(response =='1'){
            swalSuccess('success','Mortgage Info Deleted Successfully');
            getMortCreationTable();
        }else{
            swalError('Alert', 'Delete Failed')
        }
    },'json');
}



/////////////////////////////////////////////////////////// Loan Issue START////////////////////////////////////////////////
$(document).ready(function(){

});

$(function(){

});

/////////////////////////////////////////////////////////// Loan Issue END////////////////////////////////////////////////
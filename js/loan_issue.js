$(document).ready(function () {
    $(document).on('click', '.edit-loan-issue', function () {
        let id = $(this).attr('value'); //Customer Profile id From List page.
        $('#customer_profile_id').val(id);
        let cusID = $(this).attr('data-id'); //Cus id From List Page.
        $('#cus_id').val(cusID);
        swapTableAndCreation();
        getDocNeedTable(id);
        getChequeInfoTable();
        getDocInfoTable();
        getMortInfoTable();
        getEndorsementInfoTable();
        getGoldInfoTable();
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
            callLoanCaculationFunctions();
        }
    })

    ///////////////////////////////////////////////////////////////////Cheque info START ////////////////////////////////////////////////////////////////////////////
    $('#cq_holder_type').change(function () {
        let holderType = $(this).val();
        emptyholderFields();
        if (holderType == '1' || holderType == '2') {
            $('.cq_fam_member').hide();
            let cus_profile_id = $('#customer_profile_id').val();
            getNameRelationship(cus_profile_id, holderType);
        } else if(holderType == '3') {
            getFamilyMember('Select Family Member', '#cq_fam_mem');
            $('.cq_fam_member').show();
        } else{
            $('.cq_fam_member').hide();
        }
    });

    $('#cq_fam_mem').change(function () {
        let famMemId = $(this).val();
        if (famMemId != '') {
            getNameRelationship(famMemId, '3');
        }
    });

    $('#cheque_count').keyup(function () {
        $('#cheque_no').empty();
        let cnt = $(this).val();
        if (cnt != '') {
            for (let i = 1; i <= cnt; i++) {
                $('#cheque_no').append("<div class='col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12'><div class='form-group'><input type='number' class='form-control chequeno' name='chequeno[]' id='chequeno'/> </div></div>")
            }
        }
    });

    $('#submit_cheque_info').click(function (event) {
        event.preventDefault();
        let cus_id = $('#cus_id').val();
        let cq_holder_type = $('#cq_holder_type').val();
        let cq_holder_name = $("#cq_holder_name").val();
        let cq_holder_id = $("#cq_holder_name").attr('data-id');
        let cq_relationship = $('#cq_relationship').val();
        let cq_bank_name = $('#cq_bank_name').val();
        let cheque_count = $('#cheque_count').val();
        let cq_upload = $('#cq_upload')[0].files;
        let cq_upload_edit = $('#cq_upload_edit').val();
        let customer_profile_id = $('#customer_profile_id').val();
        let cheque_info_id = $('#cheque_info_id').val();

        let chequeNoArr = []; //for storing cheque no
        let i = 0;
        $('.chequeno').each(function () {//cheque numbers input box
            chequeNoArr[i] = $(this).val();//store each numbers in an array
            i++;
        });

        if (cq_holder_type === '' || cq_holder_name === '' || cq_relationship === '' || cq_bank_name === '' || cheque_count == '') {
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
        chequeInfo.append('cq_upload_edit', cq_upload_edit)
        chequeInfo.append('cheque_no', chequeNoArr)
        chequeInfo.append('cus_id', cus_id)
        chequeInfo.append('customer_profile_id', customer_profile_id)
        chequeInfo.append('id', cheque_info_id)

        for (var a = 0; a < cq_upload.length; a++) {
            chequeInfo.append('cq_upload[]', cq_upload[a])
        }

        $.ajax({
            url: 'api/loan_issue_files/submit_cheque_info.php',
            type: 'post',
            data: chequeInfo,
            contentType: false,
            processData: false,
            cache: false,
            dataType: 'json',
            success: function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Cheque Info Updated Successfully')
                } else if (response == '2') {
                    swalSuccess('Success', 'Cheque Info Added Successfully')
                } else {
                    swalError('Alert', 'Failed')
                }
                getChequeCreationTable();
                $('#clear_cheque_form').trigger('click');
                $('#cheque_info_id').val('');
                $('.cq_fam_member').hide();
            }
        });
    });

    $(document).on('click', '.chequeActionBtn', function () {
        let id = $(this).attr('value');
        $.post('api/loan_issue_files/cheque_info_data.php', { id }, function (response) {
            $('#cq_holder_type').val(response.result[0].holder_type);
            $('#cq_holder_name').val(response.result[0].holder_name);
            $('#cq_holder_name').attr('data-id', response.result[0].holder_id);
            $('#cq_relationship').val(response.result[0].relationship);
            $('#cq_bank_name').val(response.result[0].bank_name);
            $('#cheque_count').val(response.result[0].cheque_cnt);
            $('#cheque_info_id').val(response.result[0].id);
            if (response.result[0].holder_type == '3') {
                getFamilyMember('Select Family Member', '#cq_fam_mem')
                $('.cq_fam_member').show();
                setTimeout(() => {
                    $('#cq_fam_mem').val(response.result[0].holder_id);
                }, 1000);
            }else{
                $('#cq_fam_mem').val('');
                $('.cq_fam_member').hide();
            }

            $('#cheque_no').empty();
            for (let key in response.no) {
                let cheque = response.no[key];
                $('#cheque_no').append("<div class='col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12'><div class='form-group'><input type='number' class='form-control chequeno' name='chequeno[]' id='chequeno' value='" + cheque['cheque_no'] + "'/> </div></div>");
            }

        }, 'json');
    });

    $(document).on('click', '.chequeDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this Cheque?', deleteChequeInfo, id);
    });

    $('#clear_cheque_form').click(function () {
        $('#cheque_no').empty();
        $('#cheque_info_id').val('');
        $('.cq_fam_member').hide();
    });
    ///////////////////////////////////////////////////////////////////Cheque info END ////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////Document info START ////////////////////////////////////////////////////////////////////////////
    $('#doc_holder_name').change(function () {
        let id = $(this).val();
        if (id != '') {
            getRelationship(id, '#doc_relationship')
        } else {
            $('#doc_relationship').val('');
        }

    });

    $('#submit_doc_info').click(function (event) {
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

        if (doc_name == '' || doc_type == '' || doc_holder_name == '' || doc_relationship == '') {
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
                if (response == '1') {
                    swalSuccess('Success', 'Document Info Updated Successfully')
                } else if (response == '2') {
                    swalSuccess('Success', 'Document Info Added Successfully')
                } else {
                    swalError('Alert', 'Failed')
                }
                getDocCreationTable();
                $('#clear_doc_form').trigger('click');
                $('#doc_info_id').val('');
            }
        });
    });

    $(document).on('click', '.docActionBtn', function () {
        let id = $(this).attr('value');
        $.post('api/loan_issue_files/doc_info_data.php', { id }, function (response) {
            $('#doc_name').val(response[0].doc_name);
            $('#doc_type').val(response[0].doc_type);
            $('#doc_holder_name').val(response[0].holder_name);
            $('#doc_relationship').val(response[0].relationship);
            $('#doc_upload_edit').val(response[0].upload);
            $('#doc_info_id').val(response[0].id);
        }, 'json');
    });

    $(document).on('click', '.docDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this document?', deleteDocInfo, id);
    });

    $('#clear_doc_form').click(function () {
        $('#doc_info_id').val('');
    })
    ///////////////////////////////////////////////////////////////////Document info END ////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////Mortgage info START ////////////////////////////////////////////////////////////////////////////
    $('#property_holder_name').change(function () {
        let id = $(this).val();
        if (id != '') {
            getRelationship(id, '#mort_relationship')
        } else {
            $('#mort_relationship').val('');
        }

    });

    $('#submit_mortgage_info').click(function (event) {
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

        if (property_holder_name == '' || mort_relationship == '' || mort_property_details == '' || mortgage_name == '' || mort_designation == '' || mortgage_no == '' || reg_office == '' || mortgage_value == '') {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        }

        let mortgageInfo = new FormData();
        mortgageInfo.append('property_holder_name', property_holder_name);
        mortgageInfo.append('mort_relationship', mort_relationship);
        mortgageInfo.append('mort_property_details', mort_property_details);
        mortgageInfo.append('mortgage_name', mortgage_name);
        mortgageInfo.append('mort_designation', mort_designation);
        mortgageInfo.append('mortgage_no', mortgage_no);
        mortgageInfo.append('reg_office', reg_office);
        mortgageInfo.append('mortgage_value', mortgage_value);
        mortgageInfo.append('mort_upload', mort_upload);
        mortgageInfo.append('mort_upload_edit', mort_upload_edit);
        mortgageInfo.append('cus_id', cus_id);
        mortgageInfo.append('customer_profile_id', customer_profile_id);
        mortgageInfo.append('id', mortgage_info_id);

        $.ajax({
            url: 'api/loan_issue_files/submit_mortgage_info.php',
            type: 'post',
            data: mortgageInfo,
            contentType: false,
            processData: false,
            cache: false,
            success: function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Mortgage Info Updated Successfully')
                } else if (response == '2') {
                    swalSuccess('Success', 'Mortgage Info Added Successfully')
                } else {
                    swalError('Alert', 'Failed')
                }
                getMortCreationTable()
                $('#clear_mortgage_form').trigger('click');
                $('#mortgage_info_id').val('');
            }
        });
    });

    $(document).on('click', '.mortActionBtn', function () {
        let id = $(this).attr('value');
        $.post('api/loan_issue_files/mortgage_info_data.php', { id }, function (response) {
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
        }, 'json');
    });

    $(document).on('click', '.mortDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this Mortgage?', deleteMortgageInfo, id);
    });

    $('#clear_mortgage_form').click(function () {
        $('#mortgage_info_id').val('');
    })
    ///////////////////////////////////////////////////////////////////Mortgage info END ////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////Endorsement info START ////////////////////////////////////////////////////////////////////////////
    $('#owner_name').change(function () {
        let id = $(this).val();
        if (id != '') {
            getRelationship(id, '#owner_relationship')
        } else {
            $('#owner_relationship').val('');
        }
    });

    $('#submit_endorsement').click(function (event) {
        event.preventDefault();
        let owner_name = $('#owner_name').val();
        let owner_relationship = $('#owner_relationship').val();
        let vehicle_details = $('#vehicle_details').val();
        let endorsement_name = $('#endorsement_name').val();
        let key_original = $('#key_original').val();
        let rc_original = $('#rc_original').val();
        let endorsement_upload = $('#endorsement_upload')[0].files[0];
        let endorsement_upload_edit = $('#endorsement_upload_edit').val();
        let endorsement_info_id = $('#endorsement_info_id').val();
        let cus_id = $('#cus_id').val();
        let customer_profile_id = $('#customer_profile_id').val();

        if (owner_name == '' || owner_relationship == '' || vehicle_details == '' || endorsement_name == '' || key_original == '' || rc_original == '') {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        }

        let endorsementInfo = new FormData();
        endorsementInfo.append('owner_name', owner_name);
        endorsementInfo.append('owner_relationship', owner_relationship);
        endorsementInfo.append('vehicle_details', vehicle_details);
        endorsementInfo.append('endorsement_name', endorsement_name);
        endorsementInfo.append('key_original', key_original);
        endorsementInfo.append('rc_original', rc_original);
        endorsementInfo.append('endorsement_upload', endorsement_upload);
        endorsementInfo.append('endorsement_upload_edit', endorsement_upload_edit);
        endorsementInfo.append('cus_id', cus_id);
        endorsementInfo.append('customer_profile_id', customer_profile_id);
        endorsementInfo.append('id', endorsement_info_id);

        $.ajax({
            url: 'api/loan_issue_files/submit_endorsement_info.php',
            type: 'post',
            data: endorsementInfo,
            contentType: false,
            processData: false,
            cache: false,
            success: function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Endorsement Info Updated Successfully')
                } else if (response == '2') {
                    swalSuccess('Success', 'Endorsement Info Added Successfully')
                } else {
                    swalError('Alert', 'Failed')
                }
                getEndorsementCreationTable()
                $('#clear_endorsement_form').trigger('click');
                $('#endorsement_info_id').val('');
            }
        });
    });

    $(document).on('click', '.endorseActionBtn', function () {
        let id = $(this).attr('value');
        $.post('api/loan_issue_files/endorsement_info_data.php', { id }, function (response) {
            $('#owner_name').val(response[0].owner_name);
            $('#owner_relationship').val(response[0].relationship);
            $('#vehicle_details').val(response[0].vehicle_details);
            $('#endorsement_name').val(response[0].endorsement_name);
            $('#key_original').val(response[0].key_original);
            $('#rc_original').val(response[0].rc_original);
            $('#endorsement_upload_edit').val(response[0].upload);
            $('#endorsement_info_id').val(response[0].id);
        }, 'json');
    });

    $(document).on('click', '.endorseDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this Endorsement?', deleteEndorsementInfo, id);
    });

    $('#clear_endorsement_form').click(function () {
        $('#endorsement_info_id').val('');
    });

    ///////////////////////////////////////////////////////////////////Endorsement info END ////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////Gold info START ////////////////////////////////////////////////////////////////////////////
    $('#submit_gold_info').click(function (event) {
        event.preventDefault();
        let goldInfo = {
            'cus_id': $('#cus_id').val(),
            'customer_profile_id': $('#customer_profile_id').val(),
            'gold_type': $('#gold_type').val(),
            'purity': $('#gold_purity').val(),
            'weight': $('#gold_weight').val(),
            'value': $('#gold_value').val(),
            'id': $('#gold_info_id').val(),
        };
        if (goldInfo.gold_type == '' || goldInfo.purity == '' || goldInfo.weight == '' || goldInfo.value == '') {
            swalError('Alert', 'Please fill Mandatory fields');
            return;
        }

        $.post('api/loan_issue_files/submit_gold_info.php', goldInfo, function (response) {
            if (response == '1') {
                swalSuccess('Success', 'Gold Info Updated Successfully')
            } else if (response == '2') {
                swalSuccess('Success', 'Gold Info Added Successfully')
            } else {
                swalError('Alert', 'Failed')
            }
            getGoldCreationTable()
            $('#clear_gold_form').trigger('click');
            $('#gold_info_id').val('');
        });
    });

    $(document).on('click', '.goldActionBtn', function () {
        let id = $(this).attr('value');
        $.post('api/loan_issue_files/gold_info_data.php', { id }, function (response) {
            $('#gold_type').val(response[0].gold_type);
            $('#gold_purity').val(response[0].purity);
            $('#gold_weight').val(response[0].weight);
            $('#gold_value').val(response[0].value);
            $('#gold_info_id').val(response[0].id);
        }, 'json');
    });

    $(document).on('click', '.goldDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this Gold Info?', deleteGoldInfo, id);
    });

    $('#clear_gold_form').click(function () {
        $('#gold_info_id').val('');
    });
    ///////////////////////////////////////////////////////////////////Gold info END ////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////Document Print START ////////////////////////////////////////////////////////////////////////////
    $('#print_doc').click(function () {
        let cus_profile_id = $('#customer_profile_id').val();
        // Open a new window or tab
        var printWindow = window.open('', '_blank');

        // Make sure the popup window is not blocked
        if (printWindow) {
            // Load the content into the popup window
            $.ajax({
                url: 'api/loan_issue_files/print_document.php',
                data: { cus_profile_id },
                cache: false,
                type: "post",
                success: function (html) {
                    // Write the content to the new window
                    printWindow.document.open();
                    printWindow.document.write(html);
                    printWindow.document.close();

                    // Optionally, print the content
                    printWindow.print();
                },
                error: function () {
                    // Handle error
                    printWindow.close();
                    alert('Failed to load print content.');
                }
            });
        } else {
            alert('Popup blocked. Please allow popups for this website.');
        }
    })
    ///////////////////////////////////////////////////////////////////Document Print END ////////////////////////////////////////////////////////////////////////////

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
        $('#documentation').trigger('click');
        refreshIssueInfo();
    }
}

function getDocNeedTable(cusProfileId) {
    $.post('api/loan_entry/loan_calculation/document_need_list.php', { cusProfileId }, function (response) {
        let docColumn = [
            "sno",
            "document_name"
        ]
        appendDataToTable('#doc_need_table', response, docColumn);
    }, 'json');
}

function getFamilyMember(optn, selector) {
    let cus_id = $('#cus_id').val();
    $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
        let appendOption = '';
        appendOption += "<option value=''>" + optn + "</option>";
        $.each(response, function (index, val) {
            appendOption += "<option value='" + val.id + "'>" + val.fam_name + "</option>";
        });
        $(selector).empty().append(appendOption);
    }, 'json');
}

function getNameRelationship(id, type) {
    $.post('api/loan_issue_files/get_cus_fam_members.php', { id, type }, function (response) {
        if (type == '1') {
            $('#cq_holder_name').val(response[0].cus_name);
            $('#cq_relationship').val('Customer');
        } else {
            $('#cq_holder_name').val(response[0].fam_name);
            $('#cq_holder_name').attr('data-id', response[0].id);
            $('#cq_relationship').val(response[0].fam_relationship);
        }
    }, 'json');
}

function getRelationship(id, selector) {
    $.post('api/loan_entry/family_creation_data.php', { id }, function (response) {
        $(selector).val(response[0].fam_relationship);
    }, 'json');
}

function emptyholderFields() {
    $('#cq_fam_mem').val('');
    $('#cq_holder_name').val('');
    $('#cq_holder_name').attr('data-id', '');
    $('#cq_relationship').val('');
}

function getChequeCreationTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/cheque_info_list.php', { cus_profile_id }, function (response) {
        let chequeColumn = [
            "sno",
            "holder_type",
            "holder_name",
            "relationship",
            "bank_name",
            "cheque_cnt",
            "upload",
            "action"
        ]
        appendDataToTable('#cheque_creation_table', response, chequeColumn);
        setdtable('#cheque_creation_table');
    }, 'json');
}

function getChequeInfoTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/cheque_info_list.php', { cus_profile_id }, function (response) {
        let chequeColumn = [
            "sno",
            "holder_type",
            "holder_name",
            "relationship",
            "bank_name",
            "cheque_cnt",
            "upload"
        ]
        appendDataToTable('#cheque_info_table', response, chequeColumn);
        setdtable('#cheque_info_table');
    }, 'json');
}

function deleteChequeInfo(id) {
    $.post('api/loan_issue_files/delete_cheque_info.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Cheque Info Deleted Successfully');
            getChequeCreationTable();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}

function getDocCreationTable() {
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

function refreshChequeModal() {
    $('#clear_cheque_form').trigger('click');
}

function getDocInfoTable() {
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

function deleteDocInfo(id) {
    $.post('api/loan_issue_files/delete_doc_info.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Doc Info Deleted Successfully');
            getDocCreationTable();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}

function refreshDocModal() {
    $('#clear_doc_form').trigger('click');
}

function getMortCreationTable() {
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

function getMortInfoTable() {
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

function deleteMortgageInfo(id) {
    $.post('api/loan_issue_files/delete_mortgage_info.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Mortgage Info Deleted Successfully');
            getMortCreationTable();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}

function refreshMortModal() {
    $('#clear_mortgage_form').trigger('click');
}

function getEndorsementCreationTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/endorsement_info_list.php', { cus_profile_id }, function (response) {
        let endorsementInfoColumn = [
            "sno",
            "fam_name",
            "relationship",
            "vehicle_details",
            "endorsement_name",
            "key_original",
            "rc_original",
            "upload",
            "action"
        ]
        appendDataToTable('#endorsement_creation_table', response, endorsementInfoColumn);
        setdtable('#endorsement_creation_table')
    }, 'json');
}

function getEndorsementInfoTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/endorsement_info_list.php', { cus_profile_id }, function (response) {
        let endorsementColumn = [
            "sno",
            "fam_name",
            "relationship",
            "vehicle_details",
            "endorsement_name",
            "key_original",
            "rc_original",
            "upload"
        ]
        appendDataToTable('#endorsement_info', response, endorsementColumn);
        setdtable('#endorsement_info')
    }, 'json');
}

function deleteEndorsementInfo(id) {
    $.post('api/loan_issue_files/delete_endorsement_info.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Endorsement Info Deleted Successfully');
            getEndorsementCreationTable();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}

function refreshEndorsementModal() {
    $('#clear_endorsement_form').trigger('click');
}

function getGoldCreationTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/gold_info_list.php', { cus_profile_id }, function (response) {
        let goldInfoColumn = [
            "sno",
            "gold_type",
            "purity",
            "weight",
            "value",
            "action"
        ]
        appendDataToTable('#gold_creation_table', response, goldInfoColumn);
        setdtable('#gold_creation_table')
    }, 'json');
}

function getGoldInfoTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/gold_info_list.php', { cus_profile_id }, function (response) {
        let goldColumn = [
            "sno",
            "gold_type",
            "purity",
            "weight",
            "value"
        ]
        appendDataToTable('#gold_info', response, goldColumn);
        setdtable('#gold_info')
    }, 'json');
}

function deleteGoldInfo(id) {
    $.post('api/loan_issue_files/delete_gold_info.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Gold Info Deleted Successfully');
            getGoldCreationTable();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}

function refreshGoldModal() {
    $('#clear_gold_form').trigger('click');
}


/////////////////////////////////////////////////////////// Loan Issue START////////////////////////////////////////////////
$(document).ready(function () {

    {
        // Get today's date
        var today = new Date().toISOString().split('T')[0];
        //Set loan date
        $('#issue_date').val(today);
    }

    $('#due_startdate_calc').change(function () {
        var due_start_from = $('#due_startdate_calc').val(); // get start date to calculate maturity date
        var due_period = parseInt($('#due_period_calc').val()); //get due period to calculate maturity date
        var profit_type = $('#profit_type_calc').val()
        if (profit_type == '0') { //Based on the profit method choose due method from input box
            var due_method = $('#due_method_calc').val()
        } else if (profit_type == '1') {
            var due_method = $('#scheme_due_method_calc').val()
        }

        if (due_period == '' || isNaN(due_period)) {
            swalError('Warning', 'Kindly Fill the Due Period field.');
            $(this).val('');
        } else {
            if (due_method == 'Monthly' || due_method == '1') { // if due method is monthly or 1(for scheme) then calculate maturity by month

                var maturityDate = moment(due_start_from, 'YYYY-MM-DD').add(due_period, 'months').subtract(1, 'month').format('YYYY-MM-DD');//subract one month because by default its showing extra one month
                $('#maturity_date_calc').val(maturityDate);

            } else if (due_method == '2') {//if Due method is weekly then calculate maturity by week

                var due_day = parseInt($('#scheme_day_calc').val());

                var momentStartDate = moment(due_start_from, 'YYYY-MM-DD').startOf('day').isoWeekday(due_day);//Create a moment.js object from the start date and set the day of the week to the due day value

                var weeksToAdd = Math.floor(due_period - 1);//Set the weeks to be added by giving due period. subract 1 because by default it taking extra 1 week

                momentStartDate.add(weeksToAdd, 'weeks'); //Add the calculated number of weeks to the start date.

                if (momentStartDate.isBefore(due_start_from)) {
                    momentStartDate.add(1, 'week'); //If the resulting maturity date is before the start date, add another week.
                }

                var maturityDate = momentStartDate.format('YYYY-MM-DD'); //Get the final maturity date as a formatted string.

                $('#maturity_date_calc').val(maturityDate);

            } else if (due_method == '3') {
                var momentStartDate = moment(due_start_from, 'YYYY-MM-DD').startOf('day');
                var daysToAdd = Math.floor(due_period - 1);
                momentStartDate.add(daysToAdd, 'days');
                var maturityDate = momentStartDate.format('YYYY-MM-DD');
                $('#maturity_date_calc').val(maturityDate);
            }
        }
    });

    // Payment Mode
    $('#payment_mode').change(function () {
        $('#transaction_id').val('');
        $('#chequeno').val('');
        $('#issue_amount').val('');
        var type = $(this).val();
        var netcash = $('#balance_net_cash').val();
        $('#issue_amount').val(netcash);
        $('#issue_amount').attr('readonly', true);

        if (type == '1') {
            $('.transaction').hide();
            $('.checque').hide();
            $('.cash_issue').show();

        } else if (type == '2') {
            $('.transaction').show();
            $('.checque').hide();
            $('.cash_issue').show();

        } else if (type == '3') {
            $('.transaction').hide();
            $('.checque').show();
            $('.cash_issue').show();

        } else {
            $('.transaction').hide();
            $('.checque').hide();
            $('.cash_issue').hide();
        }
    });

    $('#issue_person').change(function () {
        let id = $(this).val();
        if (id != '' && id != 'Customer') {
            getRelationship(id, '#issue_relationship');
        } else if (id == 'Customer') {
            $('#issue_relationship').val('Customer');
        } else {
            $('#issue_relationship').val('');
        }
    });

    $('#submit_loan_issue').click(function (event) {
        event.preventDefault();
        let loanIssue = {
            'cus_id': $('#cus_id').val(),
            'cus_profile_id': $('#customer_profile_id').val(),
            'loan_amnt': $('#loan_amnt_calc').val(),
            'due_startdate': $('#due_startdate_calc').val(),
            'maturity_date': $('#maturity_date_calc').val(),
            'net_cash': $('#balance_net_cash').val(),
            'payment_mode': $('#payment_mode').val(),
            'transaction_id': $('#transaction_id').val(),
            'chequeno': $('#chequeno').val(),
            'issue_amount': $('#issue_amount').val(),
            'issue_date': $('#issue_date').val(),
            'issue_person': $('#issue_person').val(),
            'issue_relationship': $('#issue_relationship').val(),
        }

        if (isFormDataValid(loanIssue)) {
            $.post('api/loan_issue_files/submit_loan_issue.php', loanIssue, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Loan Issued Successfully');
                    swapTableAndCreation();
                    getLoanIssueTable();
                } else {
                    swalError('Warning', 'Loan Issue Failed.');
                }
            });
        } else {
            swalError('Warning', 'Kindly Fill Mandatory Fields.')
        }

    })
}); ///Document END.

$(function () {

});
function callLoanCaculationFunctions() {
    personalInfo();
}

function personalInfo() {
    let id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/loan_issue_data.php', { id }, function (response) {
        $('#cus_id').val(response[0].cus_id);
        $('#cus_name').val(response[0].cus_name);
        $('#cus_data').val(response[0].cus_data);
        $('#mobile1').val(response[0].mobile1);
        $('#cus_area').val(response[0].areaname);
        $('#loan_category_calc').val(response[0].loan_category);
        $('#loan_amnt_calc').val(response[0].loan_amnt);
        $('#principal_amnt_calc').val(response[0].principal_amnt);
        $('#interest_amnt_calc').val(response[0].interest_amnt);
        $('#total_amnt_calc').val(response[0].total_amnt);
        $('#due_amnt_calc').val(response[0].due_amnt);
        $('#doc_charge_calculate').val(response[0].doc_charge_calculate);
        $('#processing_fees_calculate').val(response[0].processing_fees_calculate);
        $('#net_cash_calc').val(response[0].net_cash);
        $('#balance_net_cash').val(response[0].net_cash);
        $('#loan_date_calc').val(response[0].loan_date);
        $('#due_startdate_calc').val(response[0].due_startdate);
        $('#maturity_date_calc').val(response[0].maturity_date);
        $('#due_period_calc').val(response[0].due_period);
        $('#profit_type_calc').val(response[0].profit_type);
        $('#due_method_calc').val(response[0].due_method);
        $('#scheme_due_method_calc').val(response[0].scheme_due_method);
        $('#scheme_day_calc').val(response[0].scheme_day);
        getIssuePerson(response[0].cus_name);
        $('#due_startdate_calc').attr('min', response[0].loan_date);

        let path = "uploads/loan_entry/cus_pic/";
        $('#per_pic').val(response[0].pic);
        var img = $('#imgshow');
        img.attr('src', path + response[0].pic);

    }, 'json');
}

function getIssuePerson(cus_name) {
    let cus_id = $('#cus_id').val();
    $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
        let appendOption = '';
        appendOption += "<option value=''>Select Issue Person</option>";
        appendOption += "<option value='Customer'>" + cus_name + "</option>";
        $.each(response, function (index, val) {
            appendOption += "<option value='" + val.id + "'>" + val.fam_name + "</option>";
        });
        $('#issue_person').empty().append(appendOption);
    }, 'json');
}

function refreshIssueInfo() {
    $('#payment_mode').val('');
    $('#transaction_id').val('');
    $('#chequeno').val('');
    $('#issue_amount').val('');
    $('#issue_person').val('');
    $('#issue_relationship').val('');
    $('.transaction').hide();
    $('.checque').hide();
    $('.cash_issue').hide();
}

// Function to check if all values in an object are not empty
function isFormDataValid(formData) {

    for (let key in formData) {
        if (key != 'payment_mode' && key != 'issue_amount' && key != 'transaction_id' && key != 'chequeno') {
            if (formData[key] == '' || formData[key] == null || formData[key] == undefined) {
                return false;
            }
        }
    }

    if (formData['payment_mode'] == '1') { //Cash
        if (formData['issue_amount'] == '' || formData['issue_amount'] == null || formData['issue_amount'] == undefined) {
            return false;
        }
    } else if (formData['payment_mode'] == '2') { //Bank Transfer
        if (formData['transaction_id'] == '' || formData['transaction_id'] == null || formData['transaction_id'] == undefined ||
            formData['issue_amount'] == '' || formData['issue_amount'] == null || formData['issue_amount'] == undefined) {
            return false;
        }
    } else if (formData['payment_mode'] == '3') { //Bank Transfer
        if (formData['chequeno'] == '' || formData['chequeno'] == null || formData['chequeno'] == undefined ||
            formData['issue_amount'] == '' || formData['issue_amount'] == null || formData['issue_amount'] == undefined) {
            return false;
        }
    } else if (formData['payment_mode'] == '') { //if Empty
        return false;
    }

    return true;
}
/////////////////////////////////////////////////////////// Loan Issue END////////////////////////////////////////////////
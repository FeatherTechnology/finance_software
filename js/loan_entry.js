$(document).ready(function () {
    // Loan Entry Tab Change Radio buttons
    $(document).on('click', '#add_loan, #back_btn', function () {
        swapTableAndCreation();
        getFamilyTable()
        getFamilyInfoTable()
        getPropertyTable()
        getPropertyInfoTable()
        getBankTable()
        getBankInfoTable()
        getKycTable()
        getKycInfoTable()
        getProofTable()
        getPropertyHolder();
        getRelationshipName();
        getFamilyMember();
        getKycRelationshipName();
        fetchProofList();
        getGuarantorName();
        getGrelationshipName()
        getAreaName()
        getAlineName()
        famNameList()
        aadharList()
        mobileList()
        //clear();
    });

    $('input[name=loan_entry_type]').click(function () {
        let loanEntryType = $(this).val();
        if (loanEntryType == 'cus_profile') {
            $('#loan_entry_customer_profile').show(); $('#loan_entry_loan_calculation').hide();

        } else if (loanEntryType == 'loan_calc') {
            $('#loan_entry_customer_profile').hide(); $('#loan_entry_loan_calculation').show();
            callLoanCaculationFunctions();
        }
    })

    $('#cus_id').on('keyup', function () {
        var value = $(this).val();
        value = value.replace(/\D/g, "").split(/(?:([\d]{4}))/g).filter(s => s.length > 0).join(" ");
        $(this).val(value);
    });

    $('#cus_name').on('blur', function () {
        var customerName = $(this).val().trim();
        if (customerName) {
            updateCustomerName(customerName);
        } else {
            removeCustomerName();
        }
    });

    $('#mobile1').on('blur', function () {
        var customerMobile = $(this).val().trim();
        if (customerMobile) {
            addCustomerMobile(customerMobile);
        } else {
            removeCustomerMobile();
        }
    });

    $('#cus_id').on('blur', function () {
        var customerID = $('#cus_id').val().trim().replace(/\s/g, '');
        if (customerID) {
            updateCustomerID(customerID);
        } else {
            removeCustomerID();
        }
    });

    $('#cus_id, #cus_name').on('blur', function () {
        var customerID = $('#cus_id').val().trim().replace(/\s/g, '');
        var customerName = $('#cus_name').val().trim();
        if (customerID && customerName) {
            addPropertyHolder(customerID, customerName);
        } else {
            removeCustomerEntries();
        }
    });

    $('#fam_aadhar').on('keyup', function () {
        var value = $(this).val();
        value = value.replace(/\D/g, "").split(/(?:([\d]{4}))/g).filter(s => s.length > 0).join(" ");
        $(this).val(value);
    });

    $('#pic').change(function () {
        var pic = $('#pic')[0];
        var img = $('#imgshow');
        img.attr('src', URL.createObjectURL(pic.files[0]));
    })

    $('#gu_pic').change(function () {
        var pic = $('#gu_pic')[0];
        var img = $('#imgshows');
        img.attr('src', URL.createObjectURL(pic.files[0]));
    })

    /////family Modal////
    $('#submit_family').click(function () {
        event.preventDefault();
        //Validation

        let fam_name = $('#fam_name').val(); let fam_relationship = $('#fam_relationship').val(); let fam_age = $('#fam_age').val(); let fam_live = $('#fam_live').val(); let fam_occupation = $('#fam_occupation').val(); let fam_aadhar = $('#fam_aadhar').val().replace(/\s/g, ''); let fam_mobile = $('#fam_mobile').val(); let family_id = $('#family_id').val();
        if (fam_name === '' || fam_relationship === '' || fam_live === '' || fam_aadhar === '' || fam_mobile === '') {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        } else {
            $.post('api/loan_entry/submit_family.php', { fam_name, fam_relationship, fam_age, fam_live, fam_occupation, fam_aadhar, fam_mobile, family_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Family Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Family Info Updated Successfully!')
                }

                $('#clear_fam_form').trigger('click')
                $('#family_id').val('')
                $('#add_fam_info_modal').modal('hide');

                // Refresh the family table
                getFamilyTable();
                //getFamilyInfoTable();
                getPropertyHolder();
                getRelationshipName();
                getFamilyMember();
                getKycRelationshipName();
                getGuarantorName();
                mobileList()
                famNameList()
                aadharList()
            });
        }
    });

    $(document).on('click', '.familyActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/loan_entry/family_creation_data.php', { id: id }, function (response) {
            $('#family_id').val(id);
            $('#fam_name').val(response[0].fam_name);
            $('#fam_relationship').val(response[0].fam_relationship);
            $('#fam_age').val(response[0].fam_age);
            $('#fam_live').val(response[0].fam_live);
            $('#fam_occupation').val(response[0].fam_occupation);
            $('#fam_aadhar').val(response[0].fam_aadhar);
            $('#fam_mobile').val(response[0].fam_mobile);
        }, 'json');
    });

    $(document).on('click', '.familyDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Family Details?', getFamilyDelete, id);
        return;
    });

    ////Proerty Modal////
    $('#submit_property').click(function () {
        event.preventDefault();
        //Validation
        let property = $('#property').val(); let property_detail = $('#property_detail').val(); let property_holder = $('#property_holder').val(); let property_id = $('#property_id').val();
        if (property === '' || property_detail === '' || property_holder === '' || prop_relationship === '') {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        } else {
            $.post('api/loan_entry/submit_property.php', { property, property_detail, property_holder, property_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Property Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Property Info Updated Successfully!')
                }
                $('#clear_prop_form').trigger('click')
                $('#property_id').val('')
                $('#add_prop_info_modal').modal('hide');

                getPropertyTable();
                //getPropertyInfoTable();

            });
        }
    });

    $(document).on('click', '.propertyActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/loan_entry/property_creation_data.php', { id: id }, function (response) {
            $('#property_id').val(id);
            $('#property').val(response[0].property);
            $('#property_detail').val(response[0].property_detail);
            $('#property_holder').val(response[0].property_holder);
            $('#prop_relationship').val(response[0].fam_relationship);


        }, 'json');
    });

    $(document).on('click', '.propertyDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Property Details?', getPropertyDelete, id);
        return;
    });

    $('#property_holder').change(function () {
        var propertyHolderId = $(this).val();
        if (propertyHolderId) {
            getRelationshipName(propertyHolderId);
        } else {
            $('#prop_relationship').val('');
        }
    });

    $('#proof_of').change(function () {
        var proofOf = $(this).val();
        if (proofOf == "2") { // Family Member selected
            $('.fam_mem_div').show();
            $('#kyc_relationship').val('');
        } else { // Customer or any other selection
            $('.fam_mem_div').hide();
            $('#kyc_relationship').val('Customer');
        }
    });

    $('#fam_mem').change(function () {
        var familyMemberId = $(this).val();
        if (familyMemberId) {
            getKycRelationshipName(familyMemberId);
        } else {
            $('#kyc_relationship').val('');
        }
    });

    //////Bank Modal/////
    $('#submit_bank').click(function () {
        event.preventDefault();
        //Validation
        let bank_name = $('#bank_name').val(); let branch_name = $('#branch_name').val(); let acc_holder_name = $('#acc_holder_name').val(); let acc_number = $('#acc_number').val(); let ifsc_code = $('#ifsc_code').val(); let bank_id = $('#bank_id').val();
        if (bank_name === '' || branch_name === '' || acc_holder_name === '' || acc_number === '' || ifsc_code === '') {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        } else {
            $.post('api/loan_entry/submit_bank.php', { bank_name, branch_name, acc_holder_name, acc_number, ifsc_code, bank_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Bank Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Bank Info Updated Successfully!')
                }
                $('#clear_bank_form').trigger('click')
                $('#bank_id').val('')
                $('#add_bank_info_modal').modal('hide');
                getBankTable();
                //getBankInfoTable();

            });
        }
    })

    $(document).on('click', '.bankActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/loan_entry/bank_creation_data.php', { id: id }, function (response) {
            $('#bank_id').val(id);
            $('#bank_name').val(response[0].bank_name);
            $('#branch_name').val(response[0].branch_name);
            $('#acc_holder_name').val(response[0].acc_holder_name);
            $('#acc_number').val(response[0].acc_number);
            $('#ifsc_code').val(response[0].ifsc_code);
        }, 'json');
    });

    $(document).on('click', '.bankDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Bank Details?', getBankDelete, id);
        return;
    });

    ////////////Kyc Modal///////

    $('#submit_kyc').click(function () {
        event.preventDefault();
        //Validation
        let upload = $('#upload')[0].files[0]; let kyc_upload = $('#kyc_upload').val();
        let proof_of = $('#proof_of').val(); let fam_mem = $("#fam_mem").val(); let proof = $('#proof').val(); let proof_detail = $('#proof_detail').val(); let kyc_id = $('#kyc_id').val();
        if (proof_of === '' || kyc_relationship === '' || proof === '' || proof_detail === '') {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        } else {
            let kycDetail = new FormData();
            kycDetail.append('proof_of', proof_of)
            if (proof_of !== 'Customer') {
                kycDetail.append('fam_mem', fam_mem);
            }

            kycDetail.append('proof', proof)
            kycDetail.append('proof_detail', proof_detail)
            kycDetail.append('upload', upload)
            kycDetail.append('kyc_upload', kyc_upload)
            kycDetail.append('kyc_id', kyc_id)
            $.ajax({
                url: 'api/loan_entry/submit_kyc.php',
                type: 'post',
                data: kycDetail,
                contentType: false,
                processData: false,
                cache: false,
                success: function (response) {
                    if (response = 'Success') {
                        if (kyc_id == '') {
                            swalSuccess('Success', 'KYC Added Successfully!');
                        } else {
                            swalSuccess('Success', 'KYC Updated Successfully!')
                        }
                    } else {
                        swalError('Error', 'Error in table');
                    }

                    $('#clear_kyc_form').trigger('click')
                    $('#kyc_id').val('')
                    $('#add_kyc_info_modal').modal('hide');
                    getKycTable();
                    //getKycInfoTable();
                }
            });
        }
    });

    $(document).on('click', '.kycActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/loan_entry/kyc_creation_data.php', { id: id }, function (response) {
            if (response && response.length > 0) {
                $('#kyc_id').val(id);
                $('#proof_of').val(response[0].proof_of);

                if (response[0].proof_of == 1) { // Assuming 1 is for customer
                    $('.fam_mem_div').hide();
                    $('#fam_mem').val('');
                } else {
                    getFamilyMember();
                    setTimeout(() => {
                        $("#fam_mem").val(response[0].fam_mem);
                    }, 100);
                    $('.fam_mem_div').show();
                }
                if (response[0].proof_of == 1) {
                    $('#kyc_relationship').val('Customer');
                } else {
                    $('#kyc_relationship').val(response[0].fam_relationship);
                }

                $('#proof').val(response[0].proof);
                $('#proof_detail').val(response[0].proof_detail);
                $("#kyc_upload").val(response[0].upload);
            } else {
                alert('No data found for the selected KYC ID.');
            }
        }, 'json')
    });


    $('#clear_kyc_form').on('click', function () {
        $('.fam_mem_div').hide();
        $('#fam_mem').val('');
    });

    $('.close').on('click', function () {
        $('.fam_mem_div').hide();
        $('#fam_mem').val('');
    });

    $(document).on('click', '.kycDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the KYC Details?', getKycDelete, id);
        return;
    });

    $('#proof_of').on('change', function () {
        if ($(this).val() == "2") {
            $('.fam_mem_div').show();
        } else {
            $('.fam_mem_div').hide();
        }
    });

    //////KyC Proof Modal///////
    $('#submit_proof').click(function () {
        event.preventDefault();
        //Validation
        let addProof_name = $('#addProof_name').val(); let proof_id = $('#proof_id').val();
        if (addProof_name === '') {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        } else {
            $.post('api/loan_entry/submit_proof.php', { addProof_name, proof_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Proof Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Proof Info Updated Successfully!')
                }

                $('#clear_proof_form').trigger('click')
                $('#proof_id').val('')
                $('#add_proof_info_modal').modal('hide');
                getProofTable();
                fetchProofList();
            });
        }
    });

    $(document).on('click', '.proofActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/loan_entry/proof_creation_data.php', { id: id }, function (response) {
            $('#proof_id').val(id);
            $('#addProof_name').val(response[0].addProof_name);
        }, 'json');
    });

    $(document).on('click', '.proofDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Proof Details?', getProofDelete, id);
        return;
    });

    $('#mobile1,#mobile2,#fam_mobile').change(function () {
        checkMobileNo($(this).val(), $(this).attr('id'));
    });

    // Function to format Aadhaar number input
    $('input[data-type="adhaar-number"]').keyup(function () {
        var value = $(this).val();
        value = value.replace(/\D/g, "").split(/(?:([\d]{4}))/g).filter(s => s.length > 0).join(" ");
        $(this).val(value);
    });

    $('#area').change(function () {
        var areaId = $(this).val();
        if (areaId) {
            getAlineName(areaId);
        } else {
            $('#').val('');
        }
    })

    document.getElementById('dob').addEventListener('change', function () {
        var dob = new Date(this.value);
        var today = new Date();
        var age = today.getFullYear() - dob.getFullYear();
        var m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        document.getElementById('age').value = age;
    })

}); /// Document END/////

function addCustomerMobile(mobile) {
    $('#mobile_check .custom-option').remove();
    $('#mobile_check').append('<option class="custom-option" value="' + mobile + '">' + mobile + '</option>');
}

function removeCustomerMobile() {
    $('#mobile_check .custom-option').remove();
}

function updateCustomerID(id) {
    $('#aadhar_check .custom-option').remove();
    $('#aadhar_check').append('<option class="custom-option" value="' + id + '">' + id + '</option>');
}

function removeCustomerID() {
    $('#aadhar_check .custom-option').remove();
}

function updateCustomerName(name) {
    $('#name_check .custom-option').remove();
    $('#name_check').append('<option class="custom-option" value="' + name + '">' + name + '</option>');
}

function removeCustomerName() {
    $('#name_check .custom-option').remove();
}

function addPropertyHolder(id, name) {
    $('#property_holder .custom-option').remove();
    $('#property_holder').append('<option class="custom-option" value="' + id + '">' + name + '</option>');
}

function removeCustomerEntries() {
    $('#property_holder .custom-option').remove();
}

function swapTableAndCreation() {
    if ($('.loan_table_content').is(':visible')) {
        $('.loan_table_content').hide();
        $('#add_loan').hide();
        $('#loan_entry_content').show();
        $('#back_btn').show();

    } else {
        $('.loan_table_content').show();
        $('#add_loan').show();
        $('#loan_entry_content').hide();
        $('#back_btn').hide();
    }
}

$(function () {

});

function getFamilyTable() {
    $.post('api/loan_entry/family_creation_list.php', function (response) {
        var columnMapping = [
            'sno',
            'fam_name',
            'fam_relationship',
            'fam_age',
            'fam_live',
            'fam_occupation',
            'fam_aadhar',
            'fam_mobile',
            'action'
        ];
        appendDataToTable('#family_creation_table', response, columnMapping);
        setdtable('#family_creation_table');

    }, 'json')

}

function getFamilyInfoTable() {
    $.post('api/loan_entry/family_creation_list.php', function (response) {
        var columnMapping = [
            'sno',
            'fam_name',
            'fam_relationship',
            'fam_age',
            'fam_live',
            'fam_occupation',
            'fam_aadhar',
            'fam_mobile',
        ];
        appendDataToTable('#fam_info_table', response, columnMapping);
        setdtable('#fam_info_table');
        $('#family_form input').val('');
        $('#family_form textarea').val('');
        $('#family_form select').each(function () {
            $(this).val($(this).find('option:first').val());
        });

    }, 'json')
}

function getPropertyTable() {
    $.post('api/loan_entry/property_creation_list.php', function (response) {
        var columnMapping = [
            'sno',
            'property',
            'property_detail',
            'property_holder',
            'fam_relationship',
            'action'
        ];
        appendDataToTable('#property_creation_table', response, columnMapping);
        setdtable('#property_creation_table');

    }, 'json')

}

function getPropertyInfoTable() {
    $.post('api/loan_entry/property_creation_list.php', function (response) {
        var columnMapping = [
            'sno',
            'property',
            'property_detail',
            'property_holder',
            'fam_relationship',
        ];
        appendDataToTable('#prop_info', response, columnMapping);
        setdtable('#prop_info');
        $('#property_form input').val('');
        $('#property_form textarea').val('');
        $('#property_form select').each(function () {
            $(this).val($(this).find('option:first').val());
        });

    }, 'json')

}

function getFamilyDelete(id) {
    $.post('api/loan_entry/delete_family_creation.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'Family Info Deleted Successfully!');
            getFamilyTable();
            getFamilyTable();
            //getFamilyInfoTable();
            getPropertyHolder();
            getRelationshipName();
            getFamilyMember();
            getKycRelationshipName();
            getGuarantorName();
            mobileList()
            famNameList()
            aadharList()


        } else if (response == '0') {
            swalError('Access Denied', 'Family Member Already Used');
        } else {
            swalError('Warning', 'Error occur While Delete Family Info.');
        }
    }, 'json');
}

$('#guarantor_name').change(function () {
    var guarantorId = $(this).val();
    if (guarantorId) {
        getGrelationshipName(guarantorId);
    } else {
        $('#relationship').val('');
    }
})

function getGuarantorName() {
    $.post('api/loan_entry/get_guarantor_name.php', function (response) {
        let appendGuarantorOption = '';
        appendGuarantorOption += "<option value='0'>Select Guarantor Name</option>";
        $.each(response, function (index, val) {
            appendGuarantorOption += "<option value='" + val.id + "'>" + val.fam_name + "</option>";
        });
        $('#guarantor_name').empty().append(appendGuarantorOption);
        getFamilyInfoTable();
    }, 'json');
}

function getGrelationshipName(guarantorId) {
    $.ajax({
        url: 'api/loan_entry/getGrelationship.php',
        type: 'POST',
        data: { guarantor_id: guarantorId },
        dataType: 'json',
        cache: false,
        success: function (response) {
            $('#relationship').val(response.relationship);
        },
        error: function (xhr, status, error) {
            console.error('AJAX error: ' + status, error);
            // Optionally handle errors here, such as displaying an error message to the user
        }
    });
}

function getPropertyHolder() {
    $.post('api/loan_entry/get_property_holder.php', function (response) {
        let appendHolderOption = '';
        appendHolderOption += "<option value='0'>Select Property Holder</option>";
        $.each(response, function (index, val) {
            appendHolderOption += "<option value='" + val.id + "'>" + val.fam_name + "</option>";
        });
        $('#property_holder').empty().append(appendHolderOption);
        getFamilyInfoTable();
    }, 'json');
}

function getPropertyDelete(id) {
    $.post('api/loan_entry/delete_property_creation.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'Property Info Deleted Successfully!');
            getPropertyTable();
            getPropertyInfoTable();
        } else {
            swalError('Error', 'Failed to Delete Property: ' + response);
        }
    }, 'json');
}

function getRelationshipName(propertyHolderId) {
    $.ajax({
        url: 'api/loan_entry/getRelationshipName.php',
        type: 'POST',
        data: { property_holder_id: propertyHolderId },
        dataType: 'json',
        cache: false,
        success: function (response) {
            $('#prop_relationship').val(response.prop_relationship);
        },
    });
}

function getBankDelete(id) {
    $.post('api/loan_entry/delete_bank_creation.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'Bank Info Deleted Successfully!');
            getBankTable();
            getBankInfoTable();
        } else {
            swalError('Error', 'Failed to Delete Bank: ' + response);
        }
    }, 'json');
}

function getBankTable() {
    $.post('api/loan_entry/bank_creation_list.php', function (response) {
        var columnMapping = [
            'sno',
            'bank_name',
            'branch_name',
            'acc_holder_name',
            'acc_number',
            'ifsc_code',
            'action'
        ];
        appendDataToTable('#bank_creation_table', response, columnMapping);
        setdtable('#bank_creation_table');
    }, 'json')
}

function getBankInfoTable() {
    $.post('api/loan_entry/bank_creation_list.php', function (response) {
        var columnMapping = [
            'sno',
            'bank_name',
            'branch_name',
            'acc_holder_name',
            'acc_number',
            'ifsc_code',
        ];
        appendDataToTable('#bank_info', response, columnMapping);
        setdtable('#bank_info');
        $('#bank_form input').val('');
        $('#bank_form textarea').val('');
        $('#bank_form select').each(function () {
            $(this).val($(this).find('option:first').val());
        });

    }, 'json')
}

function getKycDelete(id) {
    $.post('api/loan_entry/delete_kyc_creation.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'Kyc Info Deleted Successfully!');
            getKycTable();
            getKycInfoTable();
        } else {
            swalError('Error', 'Failed to Delete Kyc: ' + response);
        }
    }, 'json');
}

function getKycTable() {
    $.post('api/loan_entry/kyc_creation_list.php', function (response) {
        var columnMapping = [
            'sno',
            'proof_of',
            'fam_relationship',
            'proof',
            'proof_detail',
            'upload',
            'action'
        ];
        appendDataToTable('#kyc_creation_table', response, columnMapping);
        setdtable('#kyc_creation_table');
    }, 'json')
}

function getKycInfoTable() {
    $.post('api/loan_entry/kyc_creation_list.php', function (response) {
        var columnMapping = [
            'sno',
            'proof_of',
            'fam_relationship',
            'proof',
            'proof_detail',
            'upload',
        ];
        appendDataToTable('#kyc_info', response, columnMapping);
        setdtable('#kyc_info');
        $('#kyc_form input').val('');
        $('#Kyc_form.fam_mem_div').hide();
        $('#kyc_form textarea').val('');
        $('#kyc_form select').each(function () {
            $(this).val($(this).find('option:first').val());
        });
    }, 'json')
}

function getFamilyMember() {
    $.post('api/loan_entry/get_family_member.php', function (response) {
        let appendHolderOption = '';
        appendHolderOption += "<option value=''>Select Family Member</option>";
        $.each(response, function (index, val) {
            appendHolderOption += "<option value='" + val.id + "'>" + val.fam_name + "</option>";
        });
        $('#fam_mem').empty().append(appendHolderOption);
        getFamilyInfoTable();
    }, 'json');
}

function getKycRelationshipName(familyMemberId) {
    $.ajax({
        url: 'api/loan_entry/getKycRelationshipName.php',
        type: 'POST',
        data: { family_member_id: familyMemberId },
        dataType: 'json',
        cache: false,
        success: function (response) {
            $('#kyc_relationship').val(response.kyc_relationship);
        },
    });
}

function getProofDelete(id) {
    $.post('api/loan_entry/delete_proof_creation.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'proof Info Deleted Successfully!');
            getProofTable();
        } else if (response == '0') {
            swalError('Access Denied', 'proof Info Already Used');
        } else {
            swalError('Warning', 'Error occur While Delete Proof Info.');
        }
    }, 'json')
}

function getProofTable() {
    $.post('api/loan_entry/proof_creation_list.php', function (response) {
        var columnMapping = [
            'sno',
            'addProof_name',
            'action'
        ];
        appendDataToTable('#proof_creation_table', response, columnMapping);
        setdtable('#proof_creation_table');
    }, 'json')
}

function fetchProofList() {
    $.ajax({
        url: 'api/loan_entry/get_proof_list.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            $('#proof').empty().append('<option value="Select Proof">Select proof</option>');
            $.each(response, function (index, proof) {
                $('#proof').append('<option value="' + proof.id + '">' + proof.addProof_name + '</option>');
            });
            $('#proof_form input').val('');
        }
    });
}

/*function clear() {
    // Bind the click event to buttons with the specified class and data attribute
    $('button[data-dismiss="modal"]').click(function (event) {
        event.preventDefault();

        // Clear all input fields
        $('#family_form input').val('');

        // Clear all textarea fields
        $('textarea').val('');

        // Reset all select elements to their first option
        $('select').each(function () {
            $(this).val($(this).find('option:first').val());
        });
    });
}*/

/*function clear() {
    // Bind the click event to buttons with the specified class and data attribute
    $('button[data-dismiss="modal"]').click(function (event) {
        event.preventDefault();

        // Clear all input fields
        $('input').val('');

        // Clear all textarea fields
        $('textarea').val('');
        $('.fam_mem_div').hide();

        // Reset all select elements to their first option
        $('select').each(function () {
            $(this).val($(this).find('option:first').val());
        });
    });
}*/

function getAreaName() {
    $.post('api/loan_entry/get_area.php', function (response) {
        let appendAreaOption = '';
        appendAreaOption += "<option value='0'>Select Area Name</option>";
        $.each(response, function (index, val) {
            appendAreaOption += "<option value='" + val.id + "'>" + val.areaname + "</option>";
        });
        $('#area').empty().append(appendAreaOption);
    }, 'json');
}

function getAlineName(areaId) {
    $.ajax({
        url: 'api/loan_entry/getAlineName.php',
        type: 'POST',
        data: { aline_id: areaId },
        dataType: 'json',
        cache: false,
        success: function (response) {
            $('#line').val(response.line);
        },
    });
}

function famNameList() {
    $.post('api/loan_entry/datacheck_name.php', function (response) {
        let nameCheck = '';
        nameCheck += "<option value='0'>Select  Name</option>";
        $.each(response, function (index, val) {
            nameCheck += "<option value='" + val.fam_name + "'>" + val.fam_name + "</option>";
        });
        $('#name_check').empty().append(nameCheck);

        getFamilyInfoTable();
    }, 'json');
}

function aadharList() {
    $.post('api/loan_entry/datacheck_name.php', function (response) {
        let aadharCheck = '';
        aadharCheck += "<option value='0'>Select Aadhar Number</option>";
        $.each(response, function (index, val) {
            aadharCheck += "<option value='" + val.fam_aadhar + "'>" + val.fam_aadhar + "</option>";
        });
        $('#aadhar_check').empty().append(aadharCheck);

        getFamilyInfoTable();
    }, 'json');
}

function mobileList() {
    $.post('api/loan_entry/datacheck_name.php', function (response) {
        let mobileCheck = '';
        mobileCheck += "<option value='0'>Select Mobile Number</option>";
        $.each(response, function (index, val) {
            mobileCheck += "<option value='" + val.fam_mobile + "'>" + val.fam_mobile + "</option>";
        });
        $('#mobile_check').empty().append(mobileCheck);

        getFamilyInfoTable();
    }, 'json');
}

// Initial population of name_check dropdown

///////////////////////////////////////////////Customer Profile js End//////////////////////////////

//////////////////////////////////////////////////////////////// Loan Calculation START //////////////////////////////////////////////////////////////////////
$(document).ready(function () {

    $('#loan_category_calc').change(function () {
        let profiType = $('#profit_type_calc').val();
        if (profiType == '0') { //Loan Calculation
            getLoanCatDetails($(this).val());
        }
    });

    $('#profit_type_calc').change(function () {
        let profitType = $(this).val();
        //check whether the loan category selected or not. if not alert and return else call function to get loan category details to show in calculation.
        let id = $('#loan_category_calc').val();
        if (id == '') {
            swalError('Alert', 'Kindly select Loan Category');
            $(this).val('');
            return;
        }
        clearCalcSchemeFields(profitType);
        $('#profit_type_calc_scheme').show();
        $('.calc_scheme_title').text((profitType =='0') ? 'Calculation' : 'Scheme');
        if (profitType == '0') {//Loan Calculation
            $('.calc').show();
            $('.scheme').hide();
            $('.scheme_day').hide();
            getLoanCatDetails(id);
        } else if (profitType == '1') { //Scheme
            $('.calc').hide();
            $('.scheme').show();
        } else {
            $('#profit_type_calc_scheme').hide();
        }

        $('#due_startdate_calc').val('');
        $('#maturity_date_calc').val('');
    });

    $('#scheme_due_method_calc').change(function(){
        let schemeDueMethod = $(this).val();
        let loanCatId = $('#loan_category_calc').val();
        $.post('api/common_files/get_due_method_scheme.php', {schemeDueMethod, loanCatId}, function(response){
            let appendSchemeNameOption = '';
            appendSchemeNameOption += '<option value="">Select Scheme Name</option>';
            $.each(response, function (index, val) {
                let selected = '';
                let scheme_edit_it = '';
                if (val.id == scheme_edit_it) {
                    selected = 'selected';
                }
                appendSchemeNameOption += '<option value="' + val.id + '" ' + selected + '>' + val.scheme_name + '</option>';
            });
            $('#scheme_name_calc').empty().append(appendSchemeNameOption);
            clearCalcSchemeFields('1') //to clear fields.
        },'json');

        if(schemeDueMethod =='2'){
            $('.scheme_day').show();
        }else{
            $('.scheme_day').hide();
            $('.scheme_day_calc').val('');
        }

        $('#due_startdate_calc').val('');
        $('#maturity_date_calc').val('');
    });

    $('#scheme_name_calc').change(function(){ //Scheme Name change event
        var scheme_id = $(this).val();
        schemeCalAjax(scheme_id);
        $('#due_startdate_calc').val('');
        $('#maturity_date_calc').val('');
    });

    
    $('#refresh_cal').click(function(){
        $('.int-diff').text('*');$('.due-diff').text('*')
        
        var profit_method = $('#profit_type_calc').val(); // if profit method changes, due type is EMI
        if(profit_method == '0'){
            getLoanAfterInterest();
        }

        var due_type = $('#due_type_calc').val(); //If Changes not found in profit method, calculate loan amt for monthly basis
        if(due_type == 'Interest'){
            getLoanInterest();
        }
        
        var due_method_scheme = $('#scheme_due_method_calc').val();
        if(due_method_scheme == '1'){//Monthly scheme as 1
            getLoanMonthly();
        }else if(due_method_scheme == '2'){//Weekly scheme as 2
            getLoanWeekly();
        }else if(due_method_scheme == '3'){//Daily scheme as 3
            getLoanDaily();
        }

        changeInttoBen();
        
        function changeInttoBen() {
            let dueType = document.getElementById('due_type_calc');
            let intLabel = document.querySelector('label[for="interest_amnt_calc"]');
            if (dueType.value == 'Interest') {
                intLabel.textContent = 'Benefit Amount';
            } else {
                intLabel.textContent = 'Interest Amount';
            }
        }
    });

    {
        // Get today's date
        var today = new Date().toISOString().split('T')[0];
        //Set loan date
        $('#loan_date_calc').val(today);
        //Due start date -- set min date = current date.
        $('#due_startdate_calc').attr('min', today);
    }

    $('#scheme_day_calc').change(function(){
        $('#due_start_from').val('');
        $('#maturity_month').val('');
    })

    $('#due_startdate_calc').change(function(){
        var due_start_from = $('#due_startdate_calc').val(); // get start date to calculate maturity date
        var due_period = parseInt($('#due_period_calc').val()); //get due period to calculate maturity date
        var profit_type = $('#profit_type_calc').val()
        if(profit_type == '0'){ //Based on the profit method choose due method from input box
            var due_method = $('#due_method_calc').val()
        }else if(profit_type == '1'){
            var due_method = $('#scheme_due_method_calc').val()
        }
    
        if(due_method == 'Monthly' || due_method == '1'){ // if due method is monthly or 1(for scheme) then calculate maturity by month
            
            var maturityDate = moment(due_start_from, 'YYYY-MM-DD').add(due_period, 'months').subtract(1, 'month').format('YYYY-MM-DD');//subract one month because by default its showing extra one month
            $('#maturity_date_calc').val(maturityDate);
        
        }else if(due_method == '2'){//if Due method is weekly then calculate maturity by week
            
            var due_day = parseInt($('#scheme_day_calc').val());
            
            var momentStartDate = moment(due_start_from, 'YYYY-MM-DD').startOf('day').isoWeekday(due_day);//Create a moment.js object from the start date and set the day of the week to the due day value
            
            var weeksToAdd = Math.floor(due_period-1);//Set the weeks to be added by giving due period. subract 1 because by default it taking extra 1 week
            
            momentStartDate.add(weeksToAdd, 'weeks'); //Add the calculated number of weeks to the start date.
            
            if (momentStartDate.isBefore(due_start_from)) {
                momentStartDate.add(1, 'week'); //If the resulting maturity date is before the start date, add another week.
            }
            
            var maturityDate = momentStartDate.format('YYYY-MM-DD'); //Get the final maturity date as a formatted string.
            
            $('#maturity_date_calc').val(maturityDate);
        
        }else if(due_method == '3'){
            var momentStartDate = moment(due_start_from, 'YYYY-MM-DD').startOf('day');
            var daysToAdd = Math.floor(due_period-1);
            momentStartDate.add(daysToAdd, 'days');
            var maturityDate = momentStartDate.format('YYYY-MM-DD');
            $('#maturity_date_calc').val(maturityDate);
        }
    });

    $('#referred_calc').change(function(){
        let referred = $(this).val();
        if(referred =='0'){
            $('#agent_id_calc').prop('disabled',false).val('');
            $('#agent_name_calc').val('');
            getAgentID();
        }else{
            $('#agent_id_calc').prop('disabled',true).val('');
            $('#agent_name_calc').prop('readonly',true).val('');
        }
    });

    $('#agent_id_calc').change(function(){
        let id = $(this).val();
        $.post('api/agent_creation/agent_creation_data.php', {id},function (response) {
            if(response.length>0){
                $('#agent_name_calc').val(response[0].agent_name);
            }else{
                $('#agent_name_calc').val('');
            }
        }, 'json');
    });

    $('#submit_doc_need').click(function(){
        let docName = $('#doc_need_calc').val();
        let docExists = false;

        // Check if the document name already exists in the table
        $('#doc_need_table tbody tr').each(function() {
            if ($(this).find('td:eq(1)').text().trim() === docName) {
                docExists = true;
                return false; // Exit the loop
            }
        });

        if(!docExists){
            if(docName != ''){
                $('#doc_need_table').append('<tr><td></td><td>'+docName+'</td><td><span class="icon-trash-2 docDeleteBtn"></span></td></tr>');
                resetDocRowCount();
                setAllDocNameInput();
                $('#doc_need_calc').val('');
            }else{
                swalError('Warning', 'Kindly Fill the Field.')
            }
        }else{
            swalError('Warning', 'Document name already exists.')
        }
    });

    $(document).on('click','.docDeleteBtn', function() {
        $(this).closest('tr').remove();
        resetDocRowCount();
        setAllDocNameInput();
    });

    $('#submit_loan_calculation').click(function(event){
        event.preventDefault();
        $('#refresh_cal').trigger('click'); //For calculate once again if user missed to refresh calculation
        let formData = {
            'loan_id_calc': $('#loan_id_calc').val(),
            'loan_category_calc': $('#loan_category_calc').val(),
            'category_info_calc': $('#category_info_calc').val(),
            'loan_amount_calc': $('#loan_amount_calc').val(),
            'profit_type_calc': $('#profit_type_calc').val(),
            'due_method_calc': $('#due_method_calc').val(),
            'due_type_calc': $('#due_type_calc').val(),
            'profit_method_calc': $('#profit_method_calc').val(),
            'scheme_due_method_calc': $('#scheme_due_method_calc').val(),
            'scheme_day_calc': $('#scheme_day_calc').val(),
            'scheme_name_calc': $('#scheme_name_calc').val(),
            'interest_rate_calc': $('#interest_rate_calc').val(),
            'due_period_calc': $('#due_period_calc').val(),
            'doc_charge_calc': $('#doc_charge_calc').val(),
            'processing_fees_calc': $('#processing_fees_calc').val(),
            'loan_amnt_calc': $('#loan_amnt_calc').val(),
            'principal_amnt_calc': $('#principal_amnt_calc').val(),
            'interest_amnt_calc': $('#interest_amnt_calc').val(),
            'total_amnt_calc': $('#total_amnt_calc').val(),
            'due_amnt_calc': $('#due_amnt_calc').val(),
            'doc_charge_calculate': $('#doc_charge_calculate').val(),
            'processing_fees_calculate': $('#processing_fees_calculate').val(),
            'net_cash_calc': $('#net_cash_calc').val(),
            'loan_date_calc': $('#loan_date_calc').val(),
            'due_startdate_calc': $('#due_startdate_calc').val(),
            'maturity_date_calc': $('#maturity_date_calc').val(),
            'referred_calc': $('#referred_calc').val(),
            'agent_id_calc': $('#agent_id_calc').val(),
            'agent_name_calc': $('#agent_name_calc').val(),
            'all_doc_need': $('#all_doc_need').val(),
            'id': $('#loan_calculation_id').val()
        }

        if(isFormDataValid(formData)){
            $.post('api/loan_entry/loan_calculation/submit_loan_calculation.php', formData, function(response){
                if (response == '1') {
                    swalSuccess('Success', 'Loan Calculation Added Successfully!');
                } else if (response == '2') {
                    swalSuccess('Success', 'Loan Calculation Updated Successfully!')
                } else {
                    swalError('Error', 'Error Occurs!')
                }
            },'json');
        }else{
            swalError('Warning', 'Kindly Fill All Required Fields.')
        }
    });

    $('#clear_loan_calc_form').click(function(event){
        event.preventDefault();
        // clearLoanCalcForm();
    })

}); //Document END.

function callLoanCaculationFunctions() {
    getLoanCategoryName();
    getAutoGenLoanId('');
}

function getAutoGenLoanId(id){
    $.post('api/loan_entry/loan_calculation/get_autoGen_loan_id.php', {id}, function (response) {
        $('#loan_id_calc').val(response);
    }, 'json');
}

function getLoanCategoryName() {
    $.post('api/common_files/get_loan_category_creation.php', function (response) {
        let appendLoanCatOption = '';
        appendLoanCatOption += '<option value="">Select Loan Category</option>';
        $.each(response, function (index, val) {
            let selected = '';
            let loan_cat_edit_it = '';
            if (val.id == loan_cat_edit_it) {
                selected = 'selected';
            }
            appendLoanCatOption += '<option value="' + val.id + '" ' + selected + '>' + val.loan_category + '</option>';
        });
        $('#loan_category_calc').empty().append(appendLoanCatOption);
    }, 'json');
}

function getAgentID() {
    $.post('api/agent_creation/agent_creation_list.php', function (response) {
        let appendAgentIdOption = '';
        appendAgentIdOption += '<option value="">Select Agent ID</option>';
        $.each(response, function (index, val) {
            let selected = '';
            let agent_id_edit_it = '';
            if (val.id == agent_id_edit_it) {
                selected = 'selected';
            }
            appendAgentIdOption += '<option value="' + val.id + '" ' + selected + '>' + val.agent_code + '</option>';
        });
        $('#agent_id_calc').empty().append(appendAgentIdOption);
    }, 'json');
}

function getLoanCatDetails(id) {
    $.post('api/loan_entry/loan_calculation/getLoanCatDetails.php', { id }, function (response) {
        $('#due_method_calc').val(response[0].due_method);

        if(response[0].due_type == 'emi'){
            $('#due_type_calc').val('EMI');
        }else if(response[0].due_type == 'interest'){
            $('#due_type_calc').val('Interest');
        }
        //To set min and maximum 
        $('.min-max-int').text('* ('+response[0].interest_rate_min+'% - '+response[0].interest_rate_max+'%) ');
        $('#interest_rate_calc').attr('onChange',`if( parseFloat($(this).val()) > '`+response[0].interest_rate_max+`' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                            if( parseFloat($(this).val()) < '`+response[0].interest_rate_min+`' && parseFloat($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between rage
        // $('#interest_rate_calc').val(int_rate_upd);
        $('.min-max-due').text('* ('+response[0].due_period_min+' - '+response[0].due_period_max+') ');
        $('#due_period_calc').attr('onChange',`if( parseInt($(this).val()) > '`+response[0].due_period_max+`' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                            if( parseInt($(this).val()) < '`+response[0].due_period_min+`' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between rage
        // $('#due_period_calc').val(due_period_upd);
        
        $('.min-max-doc').text('* ('+response[0].doc_charge_min+'% - '+response[0].doc_charge_max+'%) ');
        $('#doc_charge_calc').attr('onChange',`if( parseFloat($(this).val()) > '`+response[0].doc_charge_max+`' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                            if( parseFloat($(this).val()) < '`+response[0].doc_charge_min+`' && parseFloat($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between rage
        // $('#doc_charge_calc').val(doc_charge_upd);

        $('.min-max-proc').text('* ('+response[0].processing_fee_min+'% - '+response[0].processing_fee_max+'%) ');
        $('#processing_fees_calc').attr('onChange',`if( parseFloat($(this).val()) > '`+response[0].processing_fee_max+`' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                            if( parseFloat($(this).val()) < '`+response[0].processing_fee_min+`' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between rage
        // $('#processing_fees_calc').val(proc_fee_upd);

    }, 'json');
}

function clearCalcSchemeFields(type){
    $('.to_clear').val('');
    $('.min-max-int').text('*');
    $('.min-max-due').text('*');
    $('.min-max-doc').text('*');
    $('.min-max-proc').text('*');
    if(type == '1'){ //Scheme
        $('#interest_rate_calc').prop('readonly',  true);
        $('#due_period_calc').prop('readonly',  true);
    }else{
        $('#interest_rate_calc').prop('readonly',  false);
        $('#due_period_calc').prop('readonly',  false);
    }
}

function schemeCalAjax(id){
    
    if(id != ''){
        $.post('api/loan_category_creation/get_scheme_data.php', { id }, function (response) {
            //To set min and maximum 
            $('#interest_rate_calc').val(response[0].interest_rate_percent);// setting readonly due to fixed interest
            $('#due_period_calc').val(response[0].due_period_percent);// setting readonly due to fixed due period
            
            (response[0].doc_charge_type == 'percent') ? type='%' : type = '₹';//Setting symbols
            $('.min-max-doc').text('* ('+response[0].doc_charge_min +' '+type+' - '+response[0].doc_charge_max+' '+type+') '); //setting min max values in span
            $('#doc_charge_calc').attr('onChange',`if( parseInt($(this).val()) > '`+response[0].doc_charge_max+`' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                                    if( parseInt($(this).val()) < '`+response[0].doc_charge_min+`' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between rage
            // $('#doc_charge_calc').val(doc_charge_upd);
            
            (response[0].processing_fee_type == 'percent') ? type='%' : type = '₹';//Setting symbols
            $('.min-max-proc').text('* ('+response[0].processing_fee_min+' '+type+' - '+response[0].processing_fee_max+' '+type+') ');//setting min max values in span
            $('#processing_fees_calc').attr('onChange',`if( parseInt($(this).val()) > '`+response[0].processing_fee_max+`' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                                if( parseInt($(this).val()) < '`+response[0].processing_fee_min+`' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between rage
            // $('#processing_fees_calc').val(doc_charge_upd);
    
        }, 'json');

    }else{
        clearCalcSchemeFields('1')
    }
}

//To Get Loan Calculation for After Interest
function getLoanAfterInterest(){
    var loan_amt = $('#loan_amount_calc').val();
    var int_rate = $('#interest_rate_calc').val();
    var due_period = $('#due_period_calc').val();
    var doc_charge = $('#doc_charge_calc').val();
    var proc_fee = $('#processing_fees_calc').val();
    
    $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card
    $('#principal_amnt_calc').val(parseInt(loan_amt).toFixed(0)); // principal amt as same as loan amt for after interest

    var interest_rate = (parseInt(loan_amt) * (parseFloat(int_rate)/100) * parseInt(due_period)).toFixed(0); //Calculate interest rate 
    $('#interest_amnt_calc').val(parseInt(interest_rate));

    var tot_amt = parseInt(loan_amt) + parseFloat(interest_rate); //Calculate total amount from principal/loan amt and interest rate
    $('#total_amnt_calc').val(parseInt(tot_amt).toFixed(0));

    var due_amt = parseInt(tot_amt) / parseInt(due_period);//To calculate due amt by dividing total amount and due period given on loan info
    var roundDue = Math.ceil(due_amt / 5) * 5; //to increase Due Amt to nearest multiple of 5
    if (roundDue < due_amt) {
        roundDue += 5;
    }
    $('.due-diff').text('* (Difference: +' + parseInt(roundDue - due_amt) + ')'); //To show the difference amount
    $('#due_amnt_calc').val(parseInt(roundDue).toFixed(0));

    ////////////////////recalculation of total, principal, interest///////////////////
    var new_tot = parseInt(roundDue) * due_period;
    $('#total_amnt_calc').val(new_tot)

    //to get new interest rate using round due amt 
    let new_int = (roundDue * due_period) - loan_amt;
    var roundedInterest = Math.ceil(new_int / 5) * 5;
    if (roundedInterest < new_int) {
        roundedInterest += 5;
    }

    $('.int-diff').text('* (Difference: +' + parseInt(roundedInterest - interest_rate) + ')'); //To show the difference amount from old to new
    $('#interest_amnt_calc').val(parseInt(roundedInterest));
    
    var new_princ = parseInt(new_tot) - parseInt(roundedInterest);
    $('#principal_amnt_calc').val(new_princ);
    
    //////////////////////////////////////////////////////////////////////////////////

    var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge)/100) ; //Get document charge from loan info and multiply with loan amt to get actual doc charge
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
    $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

    var proc_fee = parseInt(loan_amt) * (parseFloat(proc_fee)/100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
    $('#processing_fees_calculate').val(parseInt(roundeprocfee));

    var net_cash = parseInt(loan_amt) - parseFloat(roundeddoccharge) - parseFloat(roundeprocfee) ; //Net cash will be calculated by subracting other charges
    $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
}

//To Get Loan Calculation for Interest due type
function getLoanInterest(){
    var loan_amt = $('#loan_amount_calc').val();
    var int_rate = $('#interest_rate_calc').val();
    var doc_charge = $('#doc_charge_calc').val();
    var proc_fee = $('#processing_fees_calc').val();

    $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card
    $('#principal_amnt_calc').val(parseInt(loan_amt).toFixed(0)); 
    
    $('#total_amnt_calc').val('');
    $('#due_amnt_calc').val('');//Due period will be monthly by default so no need of due amt
    
    var int_amt = (parseInt(loan_amt) * (parseFloat(int_rate)/100)).toFixed(0) ; //Calculate interest rate 

    var roundedInterest = Math.ceil(int_amt / 5) * 5;
    if (roundedInterest < int_amt) {
        roundedInterest += 5;
    }
    $('.int-diff').text('* (Difference: +' + parseInt(roundedInterest - int_amt) + ')'); //To show the difference amount
    $('#interest_amnt_calc').val(parseInt(roundedInterest));

    var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge)/100) ; //Get document charge from loan info and multiply with loan amt to get actual doc charge
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
    $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

    var proc_fee = parseInt(loan_amt) * (parseFloat(proc_fee)/100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
    $('#processing_fees_calculate').val(parseInt(roundeprocfee));

    var net_cash = parseInt(loan_amt) - parseInt(doc_charge) - parseInt(proc_fee) ; //Net cash will be calculated by subracting other charges
    $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
}

//To Get Loan Calculation for Monthly Scheme method
function getLoanMonthly(){ 
    var loan_amt = $('#loan_amount_calc').val();
    var int_rate = $('#interest_rate_calc').val();
    var due_period = $('#due_period_calc').val();
    var doc_charge = $('#doc_charge_calc').val();
    var proc_fee = $('#processing_fees_calc').val();

    $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card
    
    var int_amt = (parseInt(loan_amt) * (parseFloat(int_rate)/100)).toFixed(0) ; //Calculate interest rate 
    // $('#interest_amnt_calc').val(parseInt(int_amt));
    
    var princ_amt = parseInt(loan_amt) - parseInt(int_amt); // Calculate principal amt by subracting interest amt from loan amt
    // $('#principal_amnt_calc').val(princ_amt); 

    var tot_amt = parseInt(princ_amt) + parseFloat(int_amt); //Calculate total amount from principal/loan amt and interest rate
    // $('#total_amnt_calc').val(parseInt(tot_amt).toFixed(0));

    var due_amt = parseInt(tot_amt) / parseInt(due_period);//To calculate due amt by dividing total amount and due period given on loan info
    var roundDue = Math.ceil(due_amt / 5) * 5; //to increase Due Amt to nearest multiple of 5
    if (roundDue < due_amt) {
        roundDue += 5;
    }
    $('.due-diff').text('* (Difference: +' + parseInt(roundDue - due_amt) + ')'); //To show the difference amount
    $('#due_amnt_calc').val(parseInt(roundDue).toFixed(0));

    ////////////////////recalculation of total, principal, interest///////////////////

    var new_tot = parseInt(roundDue) * due_period;
    $('#total_amnt_calc').val(new_tot)

    //to get new interest rate using round due amt 
    let new_int = (roundDue * due_period) - princ_amt;
    
    var roundedInterest = Math.ceil(new_int / 5) * 5;
    if (roundedInterest < new_int) {
        roundedInterest += 5;
    }

    $('.int-diff').text('* (Difference: +' + parseInt(roundedInterest - int_amt) + ')'); //To show the difference amount
    $('#interest_amnt_calc').val(parseInt(roundedInterest));

    var new_princ = parseInt(new_tot) - parseInt(roundedInterest);
    $('#principal_amnt_calc').val(new_princ);

    //////////////////////////////////////////////////////////////////////////////////

    var doc_type = $('.min-max-doc').text(); //Scheme may have document charge in rupees or percentage . so getting symbol from span
    if(doc_type.includes('₹')){
        var doc_charge = parseInt(doc_charge) ; //Get document charge from loan info and directly show the document charge provided because of it is in rupees
    }else if(doc_type.includes('%')){
        var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge)/100) ; //Get document charge from loan info and multiply with loan amt to get actual doc charge
    }
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
    $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

    var proc_type = $('.min-max-proc').text(); //Scheme may have Processing fee in rupees or percentage . so getting symbol from span
    if(proc_type.includes('₹')){
        var proc_fee =parseInt(proc_fee);//Get processing fee from loan info and directly show the Processing Fee provided because of it is in rupees
    }else if(proc_type.includes('%')){
        var proc_fee = parseInt(loan_amt) * (parseInt(proc_fee)/100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
    }
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
    $('#processing_fees_calculate').val(parseInt(roundeprocfee));

    var net_cash = parseInt(princ_amt) - parseInt(doc_charge) - parseInt(proc_fee) ; //Net cash will be calculated by subracting other charges
    $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
}

//To Get Loan Calculation for Weekly Scheme method
function getLoanWeekly(){ 
    var loan_amt = $('#loan_amount_calc').val();
    var int_rate = $('#interest_rate_calc').val();
    var due_period = $('#due_period_calc').val();
    var doc_charge = $('#doc_charge_calc').val();
    var proc_fee = $('#processing_fees_calc').val();

    $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card
    
    var int_amt = (parseInt(loan_amt) * (parseFloat(int_rate)/100)).toFixed(0) ; //Calculate interest rate
    // $('#interest_amnt_calc').val(parseInt(int_amt));

    var princ_amt = parseInt(loan_amt) - parseInt(int_amt); // Calculate principal amt by subracting interest amt from loan amt
    $('#principal_amnt_calc').val(parseInt(princ_amt).toFixed(0)); 

    var tot_amt = parseInt(princ_amt) + parseFloat(int_amt); //Calculate total amount from principal/loan amt and interest rate
    $('#total_amnt_calc').val(parseInt(tot_amt).toFixed(0));

    var due_amt = parseInt(tot_amt) / parseInt(due_period);//To calculate due amt by dividing total amount and due period given on loan info
    var roundDue = Math.ceil(due_amt / 5) * 5; //to increase Due Amt to nearest multiple of 5
    if (roundDue < due_amt) {
        roundDue += 5;
    }
    $('.due-diff').text('* (Difference: +' + parseInt(roundDue - due_amt) + ')'); //To show the difference amount
    $('#due_amnt_calc').val(parseInt(roundDue).toFixed(0));
    
    ////////////////////recalculation of total, principal, interest///////////////////

        var new_tot = parseInt(roundDue) * due_period;
        $('#total_amnt_calc').val(new_tot)
    
        //to get new interest rate using round due amt 
        let new_int = (roundDue * due_period) - princ_amt;
        
        var roundedInterest = Math.ceil(new_int / 5) * 5;
        if (roundedInterest < new_int) {
            roundedInterest += 5;
        }
    
        $('.int-diff').text('* (Difference: +' + parseInt(roundedInterest - int_amt) + ')'); //To show the difference amount
        $('#interest_amnt_calc').val(parseInt(roundedInterest));
    
        var new_princ = parseInt(new_tot) - parseInt(roundedInterest);
        $('#principal_amnt_calc').val(new_princ);

     //////////////////////////////////////////////////////////////////////////////////

    var doc_type = $('.min-max-doc').text(); //Scheme may have document charge in rupees or percentage . so getting symbol from span
    if(doc_type.includes('₹')){ 
        var doc_charge = parseInt(doc_charge) ; //Get document charge from loan info and directly show the document charge provided because of it is in rupees
    }else if(doc_type.includes('%')){
        var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge)/100) ; //Get document charge from loan info and multiply with loan amt to get actual doc charge
    }
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
    $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

    var proc_type = $('.min-max-proc').text();//Scheme may have Processing fee in rupees or percentage . so getting symbol from span
    if(proc_type.includes('₹')){
        var proc_fee =parseInt(proc_fee);//Get processing fee from loan info and directly show the Processing Fee provided because of it is in rupees
    }else if(proc_type.includes('%')){
        var proc_fee = parseInt(loan_amt) * (parseInt(proc_fee)/100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
    }
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
    $('#processing_fees_calculate').val(parseInt(roundeprocfee));

    var net_cash = parseInt(princ_amt) - parseInt(doc_charge) - parseInt(proc_fee) ; //Net cash will be calculated by subracting other charges
    $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
}

//To Get Loan Calculation for Daily Scheme method
function getLoanDaily(){ 
    var loan_amt = $('#loan_amount_calc').val();
    var int_rate = $('#interest_rate_calc').val();
    var due_period = $('#due_period_calc').val();
    var doc_charge = $('#doc_charge_calc').val();
    var proc_fee = $('#processing_fees_calc').val();

    $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card
    
    var int_amt = (parseInt(loan_amt) * (parseFloat(int_rate)/100)).toFixed(0) ; //Calculate interest rate 
    $('#interest_amnt_calc').val(parseInt(int_amt));

    var princ_amt = parseInt(loan_amt) - parseInt(int_amt); // Calculate principal amt by subracting interest amt from loan amt
    $('#principal_amnt_calc').val(parseInt(princ_amt).toFixed(0)); 

    var tot_amt = parseInt(princ_amt) + parseFloat(int_amt); //Calculate total amount from principal/loan amt and interest rate
    $('#total_amnt_calc').val(parseInt(tot_amt).toFixed(0));

    var due_amt = parseInt(tot_amt) / parseInt(due_period);//To calculate due amt by dividing total amount and due period given on loan info
    var roundDue = Math.ceil(due_amt / 5) * 5; //to increase Due Amt to nearest multiple of 5
    if (roundDue < due_amt) {
        roundDue += 5;
    }
    $('.due-diff').text('* (Difference: +' + parseInt(roundDue - due_amt) + ')'); //To show the difference amount
    $('#due_amnt_calc').val(parseInt(roundDue).toFixed(0));

    ////////////////////recalculation of total, principal, interest///////////////////

    var new_tot = parseInt(roundDue) * due_period;
    $('#total_amnt_calc').val(new_tot)

    //to get new interest rate using round due amt 
    let new_int = (roundDue * due_period) - princ_amt;
    
    var roundedInterest = Math.ceil(new_int / 5) * 5;
    if (roundedInterest < new_int) {
        roundedInterest += 5;
    }

    $('.int-diff').text('* (Difference: +' + parseInt(roundedInterest - int_amt) + ')'); //To show the difference amount
    $('#interest_amnt_calc').val(parseInt(roundedInterest));

    var new_princ = parseInt(new_tot) - parseInt(roundedInterest);
    $('#principal_amnt_calc').val(new_princ);

 //////////////////////////////////////////////////////////////////////////////////
    
    var doc_type = $('.min-max-doc').text(); //Scheme may have document charge in rupees or percentage . so getting symbol from span
    if(doc_type.includes('₹')){ 
        var doc_charge = parseInt(doc_charge) ; //Get document charge from loan info and directly show the document charge provided because of it is in rupees
    }else if(doc_type.includes('%')){
        var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge)/100) ; //Get document charge from loan info and multiply with loan amt to get actual doc charge
    }
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
    $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

    var proc_type = $('.min-max-proc').text();//Scheme may have Processing fee in rupees or percentage . so getting symbol from span
    if(proc_type.includes('₹')){
        var proc_fee =parseInt(proc_fee);//Get processing fee from loan info and directly show the Processing Fee provided because of it is in rupees
    }else if(proc_type.includes('%')){
        var proc_fee = parseInt(loan_amt) * (parseInt(proc_fee)/100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
    }
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
    $('#processing_fees_calculate').val(parseInt(roundeprocfee));

    var net_cash = parseInt(princ_amt) - parseInt(doc_charge) - parseInt(proc_fee) ; //Net cash will be calculated by subracting other charges
    $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
}

function resetDocRowCount(){
    // Reset the serial numbers after deletion
    $('#doc_need_table').find('tbody tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

function setAllDocNameInput(){
    let allDocNames = [];
    // Check if the document name already exists in the table
    $('#doc_need_table tbody tr').each(function() {
        allDocNames.push($(this).find('td:eq(1)').text());
        $('#all_doc_need').val(allDocNames.join(', '));
    });
}

// Function to check if all values in an object are not empty
function isFormDataValid(formData) {
    for (let key in formData) {
        if (key != 'id' && key !='category_info_calc' && key !='due_method_calc' && key !='due_type_calc' && key !='profit_method_calc' && key !='scheme_due_method_calc' && key !='scheme_day_calc' && key !='scheme_name_calc' && key !='agent_id_calc' && key !='agent_name_calc') {
            if (formData[key] == '' || formData[key] == null || formData[key] == undefined) {
                return false;
            }
        }
    }

    if(formData['profit_type_calc'] == '0'){ //Calculation
        if (formData['due_method_calc'] == '' || formData['due_method_calc'] == null || formData['due_method_calc'] == undefined ||
            formData['due_type_calc'] == '' || formData['due_type_calc'] == null || formData['due_type_calc'] == undefined ||
            formData['profit_method_calc'] == '' || formData['profit_method_calc'] == null || formData['profit_method_calc'] == undefined){
            return false;
        }
    }else if(formData['profit_type_calc'] == '1'){ //Scheme
        if (formData['scheme_due_method_calc'] == '' || formData['scheme_due_method_calc'] == null || formData['scheme_due_method_calc'] == undefined ||
            formData['scheme_name_calc'] == '' || formData['scheme_name_calc'] == null || formData['scheme_name_calc'] == undefined){
            return false;
        }
        if(formData['scheme_due_method_calc'] =='2'){//weekly
            if(formData['scheme_day_calc'] == '' || formData['scheme_day_calc'] == null || formData['scheme_day_calc'] == undefined){
                return false;
            } 
        }
    }

    if(formData['referred_calc'] == '0'){ //Referred
        if (formData['agent_id_calc'] == '' || formData['agent_id_calc'] == null || formData['agent_id_calc'] == undefined ||
            formData['agent_name_calc'] == '' || formData['agent_name_calc'] == null || formData['agent_name_calc'] == undefined){
            return false;
        }
    }
    
    return true;
}

// function clearLoanCalcForm() {
//     $('#loan_entry_loan_calculation: input').each(function () {
//         var id = $(this).attr('id');
//         if (id !== 'loan_id_calc' && id !== 'loan_date_calc') {
//             $(this).val('');
//         }
//     });
//     $('textarea').val('');

//     $('select').each(function () {
//         $(this).val($(this).find('option:first').val());
//     });

//     $('#doc_need_table').empty();
//     setAllDocNameInput();
// }
//////////////////////////////////////////////////////////////// Loan Calculation END //////////////////////////////////////////////////////////////////////
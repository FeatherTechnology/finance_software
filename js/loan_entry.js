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
        clear();

    });
    $('input[name=loan_entry_type]').click(function () {
        let loanEntryType = $(this).val();
        if (loanEntryType == 'cus_profile') {
            $('#loan_entry_customer_profile').show(); $('#loan_entry_loan_calculation').hide();

        } else if (loanEntryType == 'loan_calc') {
            $('#loan_entry_customer_profile').hide(); $('#loan_entry_loan_calculation').show();
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
        }else {
            removeCustomerName();
        }
    });
    function updateCustomerName(name) {
        $('#name_check .custom-option').remove();
        $('#name_check').append('<option class="custom-option" value="' + name + '">' + name + '</option>');
    }
    function removeCustomerName() {
        $('#name_check .custom-option').remove();
    }
    
    $('#mobile1').on('blur', function () {
        var customerMobile = $(this).val().trim();
        if (customerMobile) {
            addCustomerMobile(customerMobile);
        }else {
            removeCustomerMobile();
        }
    }); 
   
    function addCustomerMobile(mobile) {
        $('#mobile_check .custom-option').remove();
        $('#mobile_check').append('<option class="custom-option" value="' + mobile + '">' + mobile + '</option>');
    } 
    
    function removeCustomerMobile() {
        $('#mobile_check .custom-option').remove();
    }
    ``
    $('#cus_id').on('blur', function () {    
        var customerID = $('#cus_id').val().trim().replace(/\s/g, '');
        if (customerID) {
            updateCustomerID(customerID);
        }else {
            removeCustomerID();
        }
    });
   
    function updateCustomerID(id) {
        $('#aadhar_check .custom-option').remove();
        $('#aadhar_check').append('<option class="custom-option" value="' + id + '">' + id + '</option>');
    }
    function removeCustomerID() {
        $('#aadhar_check .custom-option').remove();
    }
     
    $('#cus_id, #cus_name').on('blur', function () {
        var customerID = $('#cus_id').val().trim().replace(/\s/g, '');
        var customerName = $('#cus_name').val().trim();
        if (customerID && customerName) {
            addPropertyHolder(customerID, customerName);
        } else {
            removeCustomerEntries();
        }
    });

    function addPropertyHolder(id, name) {
        $('#property_holder .custom-option').remove();
        $('#property_holder').append('<option class="custom-option" value="' + id + '">' + name + '</option>');
    }
    function removeCustomerEntries() {
        $('#property_holder .custom-option').remove();
    }

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

});
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
// Function to format Aadhaar number input
$('input[data-type="adhaar-number"]').keyup(function () {
    var value = $(this).val();
    value = value.replace(/\D/g, "").split(/(?:([\d]{4}))/g).filter(s => s.length > 0).join(" ");
    $(this).val(value);
});



function clear() {
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
}

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
$('#area').change(function () {
    var areaId = $(this).val();
    if (areaId) {
        getAlineName(areaId);
    } else {
        $('#').val('');
    }
})
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
            nameCheck += "<option value='" + val.fam_name+ "'>" + val.fam_name + "</option>";
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
            aadharCheck += "<option value='" + val.fam_aadhar+ "'>" + val.fam_aadhar + "</option>";
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
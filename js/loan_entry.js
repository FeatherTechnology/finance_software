$(document).ready(function () {
    //Move Loan Entry 
    $(document).on('click','.move-loan-entry', function(){
        let cus_sts_id = $(this).attr('value');
        $.post('api/common_files/move_to_approval.php', {cus_sts_id}, function(response){
            if(response == '0'){
                swalSuccess('Success', 'Moved to Approval');
                getLoanEntryTable();
            }else{
                swalError('Alert', 'Failed to Move to Approval');
            }
        },'json');
    });
    // Loan Entry Tab Change Radio buttons
    $(document).on('click', '#add_loan, #back_btn', function () {
        swapTableAndCreation();
    });

    $('#add_loan').click(function () {
        let cus_data = $('#cus_data').val();
        getFamilyInfoTable()
        getPropertyInfoTable();
        getBankInfoTable()
        getKycInfoTable()
        getAreaName()
        $('.personal_info_disble').attr("disabled", false);
        if (cus_data == 'Existing') {
            $('#cus_status').show();
            $('#data_checking_div').show();
        } else {
            $('#cus_status').hide();
            $('#data_checking_div').hide();
            $('#data_checking_table_div').hide();
        }
    });

    $('#back_btn').click(function () {
        getLoanEntryTable();
        clearLoanCalcForm();//To clear Loan Calculation.
        clearCusProfileForm('1');//To Clear Customer Profile
    });

    $(document).on('click', '.edit-loan-entry', function () {
        let id = $(this).attr('value');
        $('#customer_profile_id').val(id);
        let loanCalcId = $(this).attr('data-id');
        swapTableAndCreation();
        editCustmerProfile(id)
        loanCalculationEdit(loanCalcId);
        $('#loan_calculation_id').val(loanCalcId);
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

    // Function to format Aadhaar number input
    $('input[data-type="adhaar-number"]').keyup(function () {
        var value = $(this).val();
        value = value.replace(/\D/g, "").split(/(?:([\d]{4}))/g).filter(s => s.length > 0).join(" ");
        $(this).val(value);
    });

    $('input[data-type="adhaar-number"]').change(function () {
        let len = $(this).val().length;
        if (len < 14) {
            $(this).val('');
            swalError('Warning', 'Kindly Enter Valid Aadhaar Number');
        }
    });

    $('#cus_name').on('blur', function () {
        var customerName = $(this).val().trim();
        if (customerName) {
            updateCustomerName(customerName);
        } else {
            removeCustomerName();
        }
    });

    $('#cus_id').on('blur', function () {
        let customerID = $('#cus_id').val().trim().replace(/\s/g, '');
        let cus_name = $('#cus_name').val();
        let mobileno = $('#mobile1').val();
        if (customerID) {
            dataCheckList(customerID, cus_name, mobileno)
        } else {
            removeCustomerID();
        }
    });

    $('#mobile1').on('blur', function () {
        let cus_id = $('#cus_id').val().trim().replace(/\s/g, '');
        let cus_name = $('#cus_name').val();
        let customerMobile = $(this).val().trim();
        if (customerMobile) {
            dataCheckList(cus_id, cus_name, customerMobile)
        } else {
            removeCustomerMobile();
        }
    });

    $('#cus_id, #cus_name').on('blur', function () {
        let customerID = $('#cus_id').val().trim().replace(/\s/g, '');
        let customerName = $('#cus_name').val().trim();
        if (customerID && customerName) {
            addPropertyHolder(customerID, customerName);
        } else {
            removeCustomerEntries();
        }
    });

    $('#pic').change(function () {
        let pic = $('#pic')[0];
        let img = $('#imgshow');
        img.attr('src', URL.createObjectURL(pic.files[0]));
    })

    $('#gu_pic').change(function () {
        let pic = $('#gu_pic')[0];
        let img = $('#gur_imgshow');
        img.attr('src', URL.createObjectURL(pic.files[0]));
    })

    /////family Modal////
    $('#submit_family').click(function (event) {
        event.preventDefault();
        // Validation
        let cus_profile_id = $('#customer_profile_id').val();
        let cus_id = $('#cus_id').val().replace(/\s/g, ''); // Remove spaces from cus_id
        let fam_name = $('#fam_name').val();
        let fam_relationship = $('#fam_relationship').val();
        let fam_age = $('#fam_age').val();
        let fam_live = $('#fam_live').val();
        let fam_occupation = $('#fam_occupation').val();
        let fam_aadhar = $('#fam_aadhar').val().replace(/\s/g, '');
        let fam_mobile = $('#fam_mobile').val();
        let family_id = $('#family_id').val();

        if (cus_profile_id == '') {
            swalError('Warning', 'Kindly Fill the Personal Info');
            return false;
        }
        if (fam_name === '' || fam_relationship === '' || fam_live == '' || fam_aadhar === '' || fam_mobile === '') {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        } else {
            $.post('api/loan_entry/submit_family_info.php', { cus_id, fam_name, fam_relationship, fam_age, fam_live, fam_occupation, fam_aadhar, fam_mobile, family_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Family Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Family Info Updated Successfully!');
                }
                // Refresh the family table
                getFamilyTable();
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
        let cus_profile_id = $('#customer_profile_id').val();
        let cus_id = $('#cus_id').val().replace(/\s/g, '');
        let property = $('#property').val(); let property_detail = $('#property_detail').val(); let property_holder = $('#property_holder').val(); let property_id = $('#property_id').val();
        if (cus_profile_id == '') {
            swalError('Warning', 'Kindly Fill the Personal Info');
            return false;
        }
        if (property === '' || property_detail === '' || property_holder === '' || prop_relationship === '') {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        } else {
            $.post('api/loan_entry/submit_property.php', { cus_id, property, property_detail, property_holder, property_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Property Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Property Info Updated Successfully!')
                }
                getPropertyTable();
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
            getFamilyMember();
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
        let cus_profile_id = $('#customer_profile_id').val();
        let cus_id = $('#cus_id').val().replace(/\s/g, '');
        let bank_name = $('#bank_name').val(); let branch_name = $('#branch_name').val(); let acc_holder_name = $('#acc_holder_name').val(); let acc_number = $('#acc_number').val(); let ifsc_code = $('#ifsc_code').val(); let bank_id = $('#bank_id').val();
        if (cus_profile_id == '') {
            swalError('Warning', 'Kindly Fill the Personal Info');
            return false;
        }
        if (bank_name === '' || branch_name === '' || acc_holder_name === '' || acc_number === '' || ifsc_code === '') {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        } else {
            $.post('api/loan_entry/submit_bank.php', { cus_id, bank_name, branch_name, acc_holder_name, acc_number, ifsc_code, bank_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Bank Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Bank Info Updated Successfully!')
                }
                getBankTable();
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
        let cus_profile_id = $('#customer_profile_id').val();
        let cus_id = $('#cus_id').val().replace(/\s/g, '');
        let upload = $('#upload')[0].files[0]; let kyc_upload = $('#kyc_upload').val();
        let proof_of = $('#proof_of').val(); let fam_mem = $("#fam_mem").val(); let proof = $('#proof').val(); let proof_detail = $('#proof_detail').val(); let kyc_id = $('#kyc_id').val();
        if (cus_profile_id == '') {
            swalError('Warning', 'Kindly Fill the Personal Info');
            return false;
        }
        if (proof_of === '' || kyc_relationship === '' || proof === '' || proof_detail === '') {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        } else {
            let kycDetail = new FormData();
            kycDetail.append('proof_of', proof_of)
            if (proof_of !== 'Customer') {
                kycDetail.append('fam_mem', fam_mem);
            }
            kycDetail.append('cus_id', cus_id)
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
                    getKycTable();
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

    $('.kycmodal_close').on('click', function () {
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

    $('#area').change(function () {
        var areaId = $(this).val();
        if (areaId) {
            getAlineName(areaId);
        }
    });

    $('#dob').on('change', function () {
        var dob = new Date($(this).val());
        var today = new Date();
        var age = today.getFullYear() - dob.getFullYear();
        var m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        $('#age').val(age);
    });

    $('#guarantor_name').change(function () {
        var guarantorId = $(this).val();
        if (guarantorId) {
            getGrelationshipName(guarantorId);
        } else {
            $('#relationship').val('');
        }
    })

    $('#submit_customer_profile').click(function (event) {
        event.preventDefault();
        // Validate form fields
        let famInfoRowCount = $('#fam_info_table').DataTable().rows().count();
        let kycInfoRowCount = $('#kyc_info').DataTable().rows().count();
        let pic = $('#pic')[0].files[0];
        let per_pic = $('#per_pic').val();
        let gu_pic = $('#gu_pic')[0].files[0];
        let gur_pic = $('#gur_pic').val();
        let cus_id = $('#cus_id').val().replace(/\s/g, '');
        let cus_name = $("#cus_name").val();
        let gender = $('#gender').val();
        let dob = $('#dob').val();
        let age = $('#age').val();
        let mobile1 = $('#mobile1').val();
        let mobile2 = $('#mobile2').val();
        let guarantor_name = $('#guarantor_name').val();
        let cus_data = $('#cus_data').val();
        let cus_status = $('#cus_status').val();
        let res_type = $('#res_type').val();
        let res_detail = $('#res_detail').val();
        let res_address = $('#res_address').val();
        let native_address = $('#native_address').val();
        let occupation = $('#occupation').val();
        let occ_detail = $('#occ_detail').val();
        let occ_income = $('#occ_income').val();
        let occ_address = $('#occ_address').val();
        let area_confirm = $('#area_confirm').val();
        let area = $('#area').val();
        let line = $('#line').attr('data-id');
        let cus_limit = $('#cus_limit').val();
        let about_cus = $('#about_cus').val();
        let customer_profile_id = $('#customer_profile_id').val();
        if (customer_profile_id === '') {
            swalError('Warning', 'Please Fill out personal Info!');
            return false;
        }
        if ((area_confirm == '1') && (res_type == '' || res_detail == '' || res_address == '' || native_address == '')) {
            swalError('Warning', 'Please Fill out Residential Info!');
            return false;

        } else if ((area_confirm == '2') && (occupation == '' || occ_detail == '' || occ_income == '' || occ_address == '')) {
            swalError('Warning', 'Please Fill out Occupation Info!');
            return false;

        }

        if (cus_name === '' || gender === '' || mobile1 === '' || (pic === undefined && per_pic == '') || guarantor_name === '' || (gu_pic === undefined && gur_pic == '') || area_confirm === '' || area === '' || line === '' || cus_limit === '' || famInfoRowCount === 0 || kycInfoRowCount === 0) {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        }

        // Prepare form data using FormData object
        let entryDetail = new FormData();
        entryDetail.append('cus_id', cus_id);
        entryDetail.append('cus_name', cus_name);
        entryDetail.append('gender', gender);
        entryDetail.append('dob', dob);
        entryDetail.append('age', age);
        entryDetail.append('mobile1', mobile1);
        entryDetail.append('mobile2', mobile2);
        entryDetail.append('pic', pic);
        entryDetail.append('per_pic', per_pic);
        entryDetail.append('guarantor_name', guarantor_name);
        entryDetail.append('gu_pic', gu_pic);
        entryDetail.append('gur_pic', gur_pic);
        entryDetail.append('cus_data', cus_data);
        entryDetail.append('cus_status', cus_status);
        entryDetail.append('res_type', res_type);
        entryDetail.append('res_detail', res_detail);
        entryDetail.append('res_address', res_address);
        entryDetail.append('native_address', native_address);
        entryDetail.append('occupation', occupation);
        entryDetail.append('occ_detail', occ_detail);
        entryDetail.append('occ_income', occ_income);
        entryDetail.append('occ_address', occ_address);
        entryDetail.append('area_confirm', area_confirm);
        entryDetail.append('area', area);
        entryDetail.append('line', line);
        entryDetail.append('cus_limit', cus_limit);
        entryDetail.append('about_cus', about_cus);
        entryDetail.append('customer_profile_id', customer_profile_id)

        // AJAX call to submit data
        $.ajax({
            url: 'api/loan_entry/submit_cus_profile.php',
            type: 'POST',
            data: entryDetail,
            contentType: false,
            processData: false,
            cache: false,
            dataType: 'json',
            success: function (response) {
                // Handle success response
                if (response.status == 0) {
                    swalSuccess('Success', 'Loan Entry Updated Successfully!');
                }
                $('#customer_profile_id').val(response.last_id);
            },
            error: function () {
                swalError('Error', 'Error occurred while processing your request.');
            }
        });
    });

    $('#submit_personal_info').click(function () {
        event.preventDefault();
        // Validate form fields
        let pic = $('#pic')[0].files[0];
        let per_pic = $('#per_pic').val();
        let cus_id = $('#cus_id').val().replace(/\s/g, '');
        let cus_name = $("#cus_name").val();
        let gender = $('#gender').val();
        let dob = $('#dob').val();
        let age = $('#age').val();
        let mobile1 = $('#mobile1').val();
        let mobile2 = $('#mobile2').val();
        let customer_profile_id = $('#customer_profile_id').val();
        if (cus_id === '' || cus_name === '' || gender === '' || mobile1 === '' || (pic === undefined && per_pic == '')) {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        }
        let personalDetail = new FormData();
        personalDetail.append('cus_id', cus_id);
        personalDetail.append('cus_name', cus_name);
        personalDetail.append('gender', gender);
        personalDetail.append('dob', dob);
        personalDetail.append('age', age);
        personalDetail.append('mobile1', mobile1);
        personalDetail.append('mobile2', mobile2);
        personalDetail.append('pic', pic);
        personalDetail.append('per_pic', per_pic);
        personalDetail.append('customer_profile_id', customer_profile_id)
        $.ajax({
            url: 'api/loan_entry/submit_personal_info.php',
            type: 'POST',
            data: personalDetail,
            contentType: false,
            processData: false,
            cache: false,
            dataType: 'json',
            success: function (response) {
                // Handle success response
                if (response.status == 0) {
                    swalSuccess('Success', 'Personal Info Updated Successfully!');
                } else if (response.status == 1) {
                    swalSuccess('Success', 'Personal Info Added Successfully!');
                } else {

                }
                $('#customer_profile_id').val(response.last_id);
                $('#per_pic').val(response.pic);
                $('#cus_data').val(response.cus_data);
                if (response.cus_data == 'Existing') {
                    $('#cus_status').show();
                    $('#data_checking_div').show();
                }
                $('#cus_status').val(response.cus_status);
                $('.personal_info_disble').attr("disabled", true);
                $('#submit_personal_info').attr("disabled", true);
            },
        });
    })

    // $('#area_confirm').change(function () {
    //     let cus_id = $('#cus_id').val().replace(/\s/g, ''); 
    //     let area_confirm = $(this).val(); 

    //     $.post('api/loan_entry/area_confirm.php', { cus_id, area_confirm }, function (response) {
    //         if(!response){
    //             if (response.error) {
    //                 // Handle error
    //                 swalError('Error', response.error);
    //             } else {
    //                 if (area_confirm == '1') { // Resident
    //                     $('#res_type').val(response.res_type);
    //                     $('#res_detail').val(response.res_detail);
    //                     $('#res_address').val(response.res_address);
    //                     $('#native_address').val(response.native_address);
    //                 } else if (area_confirm == '2') { // Occupation
    //                     $('#occupation').val(response.occupation);
    //                     $('#occ_detail').val(response.occ_detail);
    //                     $('#occ_income').val(response.occ_income);
    //                     $('#occ_address').val(response.occ_address);
    //                 }
    //             }
    //         }
    //     }, 'json')
    // });

    $('#name_check, #aadhar_check, #mobile_check').on('input', function () {
        var name = $('#name_check').val().trim();
        var aadhar = $('#aadhar_check').val().trim();
        var mobile = $('#mobile_check').val().trim();
        let cus_profile_id = $('#customer_profile_id').val();

        // Check which field triggered the event
        if ($(this).attr('id') === 'name_check') {
            // Clear aadhar_check and mobile_check if searching by name
            $('#aadhar_check').val('');
            $('#mobile_check').val('');
            aadhar = ''; // Reset aadhar variable
            mobile = ''; // Reset mobile variable
        } else if ($(this).attr('id') === 'aadhar_check') {
            // Clear aadhar_check and mobile_check if searching by name
            $('#name_check').val('');
            $('#mobile_check').val('');
            name = ''; // Reset aadhar variable
            mobile = ''; //{
        } else if ($(this).attr('id') === 'mobile_check') {
            // Clear aadhar_check and mobile_check if searching by name
            $('#aadhar_check').val('');
            $('#name_check').val('');
            name = ''; // Reset aadhar variable
            aadhar = ''; //{
        }

        // Fetch data for both customer and family tables
        $('#data_checking_table_div').show();
        fetchCustomerData(name, aadhar, mobile, cus_profile_id);
    });

    $('#clear_loan').click(function () {
        event.preventDefault();
        clearCusProfileForm('2');
    });

    $('#proof_modal_btn').click(function () {
        if ($('#add_kyc_info_modal').is(':visible')) {
            $('#add_kyc_info_modal').hide();
        }
    });

    $('.kyc_proof_close').click(function () {
        if ($('#add_kyc_info_modal').is(':hidden')) {
            $('#add_kyc_info_modal').show();
        }
    });

}); ///////////////////////////////////////////////////////////////// Customer Profile - Document END ////////////////////////////////////////////////////////////////////

//On Load function 
$(function () {
    getLoanEntryTable();
});

function getLoanEntryTable() {
    $.post('api/loan_entry/loan_entry_list.php', function (response) {
        var columnMapping = [
            'sno',
            'cus_id',
            'cus_name',
            'loan_id',
            'loan_category',
            'loan_amount',
            'area',
            'linename',
            'branch_name',
            'mobile1',
            'action'
        ];
        appendDataToTable('#loan_entry_table', response, columnMapping);
        setdtable('#loan_entry_table');
        //Dropdown in List Screen
        setDropdownScripts();
    }, 'json');
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
        $('#customer_profile').trigger('click')
    }
}

function clearCusProfileForm(type) {
    // Clear input fields except those with IDs 'loan_id_calc' and 'loan_date_calc'
    $('#loan_entry_customer_profile').find('input').each(function () {
        let id = $(this).attr('id');
        if (type == '1') {
            cusid = '';
            $('.personal_info_disble').val('');
            $('#submit_personal_info').attr('disabled', false);
        } else if (type == '2') {
            cusid = 'customer_profile_id';
        }

        if (id !== cusid && id != 'cus_id' && id != 'cus_name' && id != 'dob' && id != 'mobile1' && id != 'mobile2' && id != 'pic' && id != 'age' && id != 'per_pic') {
            $(this).val('');
        }
    });

    // Clear all textarea fields within the specific form
    $('#loan_entry_customer_profile').find('textarea').val('');

    //clear all upload inputs within the form.
    $('#loan_entry_customer_profile').find('input[type="file"]').val('');

    // Reset all select fields within the specific form
    $('#loan_entry_customer_profile').find('select').each(function () {
        let selectid = $(this).attr('id');
        if (selectid != 'gender') {
            $(this).val($(this).find('option:first').val());
        }
    });

    //Reset all  images within the form
    $('#imgshow').attr('src', 'img/avatar.png');
    $('#gur_imgshow').attr('src', 'img/avatar.png');
}

function fetchCustomerData(name, cusid, mobile, cus_profile_id) {
    $.post('api/loan_entry/search_customer.php', { name, cusid, mobile, cus_profile_id }, function (response) {
        // Process customer data
        var customerMapping = ['index', 'cus_id', 'cus_name', 'mobiles'];
        var customerData = response.customers.map(function (customer, index) {
            let mobiles = customer.mobile1;
            if (customer.mobile2) {
                mobiles += `, ${customer.mobile2}`;
            }
            return {
                index: index + 1,
                cus_id: customer.cus_id,
                cus_name: customer.cus_name,
                mobiles: mobiles
            };
        });
        appendDataToTable('#cus_info', customerData, customerMapping);

        // Process family data
        var familyMapping = ['index', 'cus_id', 'fam_name', 'fam_relationship', 'under_customer_name', 'under_customer_id'];
        var familyData = response.family.map(function (member, index) {
            return {
                index: index + 1,
                cus_id: member.cus_id,
                fam_name: member.fam_name,
                fam_relationship: member.fam_relationship,
                under_customer_name: member.under_customer_name,
                under_customer_id: member.under_customer_id
            };
        });
        appendDataToTable('#family_info', familyData, familyMapping);

    }, 'json');
}

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

function getFamilyInfoTable() {
    let cus_id = $('#cus_id').val().replace(/\s/g, '');
    $.post('api/loan_entry/family_creation_list.php', { cus_id }, function (response) {
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

function getFamilyTable() {
    let cus_id = $('#cus_id').val().replace(/\s/g, '');
    $.post('api/loan_entry/family_creation_list.php', { cus_id: cus_id }, function (response) {
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
        $('#family_form input').val('');
        $('#fam_relationship').val('');
        $('#fam_live').val('0');
    }, 'json')
}

function getFamilyDelete(id) {
    let cus_id = $('#cus_id').val().replace(/\s/g, '');
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_entry/delete_family_creation.php', { id, cus_id, cus_profile_id }, function (response) {
        if (response == '0') {
            swalError('Warning', 'Have to maintain atleast one Family Info');
        } else if (response == '1') {
            swalSuccess('Success', 'Family Info Deleted Successfully!');
            getFamilyTable();
        } else if (response == '2') {
            swalError('Access Denied', 'Family Member Already Used');
        } else {
            swalError('Warning', 'Error occur While Delete Family Info.');
        }
    }, 'json');
}

function getGuarantorName() {
    let cus_id = $('#cus_id').val().replace(/\s/g, '');
    $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
        let appendGuarantorOption = '';
        appendGuarantorOption += "<option value='0'>Select Guarantor Name</option>";
        $.each(response, function (index, val) {
            let selected = '';
            let editGId = $('#guarantor_name_edit').val();
            if (val.id == editGId) {
                selected = 'selected';
            }
            appendGuarantorOption += "<option value='" + val.id + "' " + selected + ">" + val.fam_name + "</option>";
        });
        $('#guarantor_name').empty().append(appendGuarantorOption);
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

function getPropertyTable() {
    let cus_id = $('#cus_id').val().replace(/\s/g, '');
    $.post('api/loan_entry/property_creation_list.php', { cus_id }, function (response) {
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
        $('#property_form input').val('');
        $('#property_holder').val('');
        $('#property_detail').val('');
    }, 'json')
}

function getPropertyInfoTable() {
    let cus_id = $('#cus_id').val().replace(/\s/g, '');
    $.post('api/loan_entry/property_creation_list.php', { cus_id }, function (response) {
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

function getPropertyHolder() {
    let cus_id = $('#cus_id').val().replace(/\s/g, '');
    $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
        let appendHolderOption = '';
        appendHolderOption += "<option value=''>Select Property Holder</option>";
        $.each(response, function (index, val) {
            appendHolderOption += "<option value='" + val.id + "'>" + val.fam_name + "</option>";
        });
        $('#property_holder').empty().append(appendHolderOption);
    }, 'json');
}

function getPropertyDelete(id) {
    $.post('api/loan_entry/delete_property_creation.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'Property Info Deleted Successfully!');
            getPropertyTable();
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
        } else {
            swalError('Error', 'Failed to Delete Bank: ' + response);
        }
    }, 'json');
}

function getBankTable() {
    let cus_id = $('#cus_id').val().replace(/\s/g, '');
    $.post('api/loan_entry/bank_creation_list.php', { cus_id }, function (response) {
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
        $('#bank_form input').val('');
    }, 'json')
}

function getBankInfoTable() {
    let cus_id = $('#cus_id').val().replace(/\s/g, '');
    $.post('api/loan_entry/bank_creation_list.php', { cus_id }, function (response) {
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
    let cus_id = $('#cus_id').val().replace(/\s/g, '');
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_entry/delete_kyc_creation.php', { id, cus_id, cus_profile_id }, function (response) {
        if (response == '0') {
            swalError('Warning', 'Have to maintain atleast one Kyc Info');
        } else if (response == '1') {
            swalSuccess('Success', 'Kyc Info Deleted Successfully!');
            getKycTable();
        } else {
            swalError('Error', 'Failed to Delete Kyc');
        }
    }, 'json');
}

function getKycTable() {
    let cus_id = $('#cus_id').val().replace(/\s/g, '');
    $.post('api/loan_entry/kyc_creation_list.php', { cus_id }, function (response) {
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
        $('#kyc_form input').val('');
        $('#Kyc_form.fam_mem_div').hide();
        $('#kyc_form select').each(function () {
            $(this).val($(this).find('option:first').val());
        });
    }, 'json')
}

function getKycInfoTable() {
    let cus_id = $('#cus_id').val().replace(/\s/g, '');
    $.post('api/loan_entry/kyc_creation_list.php', { cus_id }, function (response) {
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
    let cus_id = $('#cus_id').val().replace(/\s/g, '');
    $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
        let appendHolderOption = '';
        appendHolderOption += "<option value=''>Select Family Member</option>";
        $.each(response, function (index, val) {
            appendHolderOption += "<option value='" + val.id + "'>" + val.fam_name + "</option>";
        });
        $('#fam_mem').empty().append(appendHolderOption);
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

function getAreaName() {
    $.post('api/loan_entry/get_area.php', function (response) {
        let appendAreaOption = '';
        appendAreaOption += "<option value='0'>Select Area Name</option>";
        $.each(response, function (index, val) {
            let selected = '';
            let editArea = $('#area_edit').val();
            if (val.id == editArea) {
                selected = 'selected';
            }
            appendAreaOption += "<option value='" + val.id + "' " + selected + ">" + val.areaname + "</option>";
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
            if (response != '') {
                $('#line').val(response[0].linename);
                $('#line').attr('data-id', response[0].line_id);
            } else {
                $('#line').val('');
                $('#line').attr('data-id', '');
            }
        },
    });
}

function dataCheckList(cus_id, cus_name, cus_mble_no) {
    $.post('api/loan_entry/datacheck_name.php', { cus_id }, function (response) {
        //Name
        $('#name_check').empty();
        $('#name_check').append("<option value=''>Select Name</option>");
        $('#name_check').append('<option value="' + cus_name + '">' + cus_name + '</option>');
        $.each(response, function (index, val) {
            $('#name_check').append("<option value='" + val.fam_name + "'>" + val.fam_name + "</option>");
        });

        //Adhar no
        $('#aadhar_check').empty();
        $('#aadhar_check').append("<option value=''>Select Aadhar Number</option>");
        $('#aadhar_check').append('<option value="' + cus_id + '">' + cus_id + '</option>');
        $.each(response, function (index, val) {
            $('#aadhar_check').append("<option value='" + val.fam_aadhar + "'>" + val.fam_aadhar + "</option>");
        });

        //Mobile no 
        $('#mobile_check').empty();
        $('#mobile_check').append("<option value=''>Select Mobile Number</option>");
        $('#mobile_check').append('<option value="' + cus_mble_no + '">' + cus_mble_no + '</option>');
        $.each(response, function (index, val) {
            $('#mobile_check').append("<option value='" + val.fam_mobile + "'>" + val.fam_mobile + "</option>");
        });

    }, 'json');
}

function editCustmerProfile(id) {
    $.post('api/loan_entry/customer_profile_data.php', { id: id }, function (response) {
        $('#customer_profile_id').val(id);
        $('#area_edit').val(response[0].area);
        $('#cus_id').val(response[0].cus_id);
        $('#cus_name').val(response[0].cus_name);
        $('#gender').val(response[0].gender);
        $('#dob').val(response[0].dob);
        $('#age').val(response[0].age);
        $('#mobile2').val(response[0].mobile2);
        $('#mobile1').val(response[0].mobile1);
        $('#guarantor_name_edit').val(response[0].guarantor_name);
        $('#cus_data').val(response[0].cus_data);
        $('#cus_status').val(response[0].cus_status);
        $('#res_type').val(response[0].res_type);
        $('#res_detail').val(response[0].res_detail);
        $('#res_address').val(response[0].res_address);
        $('#native_address').val(response[0].native_address);
        $('#occupation').val(response[0].occupation);
        $('#occ_address').val(response[0].occ_address);
        $('#occ_detail').val(response[0].occ_detail);
        $('#occ_income').val(response[0].occ_income);
        $('#area_confirm').val(response[0].area_confirm);
        $('#line').val(response[0].line);
        $('#cus_limit').val(response[0].cus_limit);
        $('#about_cus').val(response[0].about_cus);
        dataCheckList(response[0].cus_id, response[0].cus_name, response[0].mobile1)
        getGuarantorName()
        getAreaName()
        setTimeout(() => {
            getFamilyInfoTable()
            getPropertyInfoTable()
            getBankInfoTable()
            getKycInfoTable()
            $('#area').trigger('change');
            $('#guarantor_name').trigger('change');
        }, 1000);

        if (response[0].cus_data == 'Existing') {
            $('#cus_status').show();
            $('#data_checking_div').show();
        }
        let path = "uploads/loan_entry/cus_pic/";
        $('#per_pic').val(response[0].pic);
        var img = $('#imgshow');
        img.attr('src', path + response[0].pic);
        let paths = "uploads/loan_entry/gu_pic/";
        $('#gur_pic').val(response[0].gu_pic);
        var img = $('#gur_imgshow');
        img.attr('src', paths + response[0].gu_pic);
        $('.personal_info_disble').attr("disabled", true);
        $('#submit_personal_info').attr('disabled', true);
    }, 'json');
}

///////////////////////////////////////////////Customer Profile js End//////////////////////////////

//////////////////////////////////////////////////////////////// Loan Calculation START //////////////////////////////////////////////////////////////////////
$(document).ready(function () {

    $('#loan_category_calc').change(function () {
        if ($(this).val() != '') {
            $('#loan_amount_calc').val('')
            getLoanCatDetails($(this).val());
            $('#profit_type_calc').val('').trigger('change');
            $('#loan_category_calc2').val($(this).val())
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
        $('.calc_scheme_title').text((profitType == '0') ? 'Calculation' : 'Scheme');
        if (profitType == '0') {//Loan Calculation
            $('.calc').show();
            $('.scheme').hide();
            $('.scheme_day').hide();
            getLoanCatDetails(id);
            $('#scheme_due_method_calc').val('')
        } else if (profitType == '1') { //Scheme
            $('#scheme_due_method_calc').val('').trigger('change');
            $('.calc').hide();
            $('.scheme').show();
            $('#due_type_calc').val('');
        } else {
            $('#profit_type_calc_scheme').hide();
        }

        $('#due_startdate_calc').val('');
        $('#maturity_date_calc').val('');
        $('.int-diff').text('*'); $('.due-diff').text('*'); $('.doc-diff').text('*'); $('.proc-diff').text('*'); $('.refresh_loan_calc').val('');
    });

    $('#scheme_due_method_calc').change(function () {
        let schemeDueMethod = $(this).val();
        let loanCatId = $('#loan_category_calc').val();
        dueMethodScheme(schemeDueMethod, loanCatId);
        $('#due_startdate_calc').val('');
        $('#maturity_date_calc').val('');
    });

    $('#scheme_name_calc').change(function () { //Scheme Name change event
        let scheme_id = $(this).val();
        schemeCalAjax(scheme_id);
        $('#due_startdate_calc').val('');
        $('#maturity_date_calc').val('');
    });

    $('#refresh_cal').click(function () {
        $('.int-diff').text('*'); $('.due-diff').text('*'); $('.doc-diff').text('*'); $('.proc-diff').text('*'); $('.refresh_loan_calc').val('');
        let loan_amt = $('#loan_amount_calc').val(); let int_rate = $('#interest_rate_calc').val(); let due_period = $('#due_period_calc').val(); let doc_charge = $('#doc_charge_calc').val(); let proc_fee = $('#processing_fees_calc').val();

        if (loan_amt != '' && int_rate != '' && due_period != '' && doc_charge != '' && proc_fee != '') {
            let due_type = $('#due_type_calc').val(); //If Changes not found in profit method, calculate loan amt for monthly basis
            if (due_type == 'Interest') {
                getLoanInterest(loan_amt, int_rate, doc_charge, proc_fee);

            } else if (due_type == 'EMI') {
                getLoanAfterInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee);
            }

            let due_method_scheme = $('#scheme_due_method_calc').val();
            if (due_method_scheme == '1') {//Monthly scheme as 1
                getLoanMonthly(loan_amt, int_rate, due_period, doc_charge, proc_fee);

            } else if (due_method_scheme == '2') {//Weekly scheme as 2
                getLoanWeekly(loan_amt, int_rate, due_period, doc_charge, proc_fee);

            } else if (due_method_scheme == '3') {//Daily scheme as 3
                getLoanDaily(loan_amt, int_rate, due_period, doc_charge, proc_fee);

            }
            changeInttoBen();
        } else {
            swalError('Warning', 'Kindly Fill the Calculation fields.')
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

    $('#scheme_day_calc').change(function () {
        $('#due_start_from').val('');
        $('#maturity_month').val('');
    })

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

    $('#referred_calc').change(function () {
        let referred = $('#referred_calc').val();
        if (referred == '0') {
            $('#agent_id_calc').prop('disabled', false).val('');
            $('#agent_name_calc').val('');
            getAgentID();
        } else {
            $('#agent_id_calc').prop('disabled', true).val('');
            $('#agent_name_calc').prop('readonly', true).val('');
        }
    });

    $('#agent_id_calc').change(function () {
        let id = $(this).val();
        $.post('api/agent_creation/agent_creation_data.php', { id }, function (response) {
            if (response.length > 0) {
                $('#agent_name_calc').val(response[0].agent_name);
            } else {
                $('#agent_name_calc').val('');
            }
        }, 'json');
    });

    $('#submit_doc_need').click(function () {
        event.preventDefault();
        let docName = $('#doc_need_calc').val();
        let cusProfileId = $('#customer_profile_id').val();
        if (docName != '') {
            $.post('api/loan_entry/loan_calculation/submit_document_need.php', { docName, cusProfileId }, function (response) {
                // if (response == '1') {
                //     swalError('Warning', 'Document Need Already Exists!');
                // } else if (response == '2') {
                //     swalSuccess('Success', 'Document Need Added Successfully!');
                // }

                getDocNeedTable(cusProfileId);
            }, 'json');

            $('#doc_need_calc').val('');
        } else {
            swalError('Warning', 'Kindly Fill the Document Need!')
        }
    });

    $(document).on('click', '.docNeedDeleteBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/loan_entry/loan_calculation/delete_doc_need.php', { id }, function (response) {
            if (response == '0') {
                // swalSuccess('Success', 'Document Need Deleted Successfully.');
                let cus_profile_id = $('#customer_profile_id').val();
                getDocNeedTable(cus_profile_id);
            } else {
                // swalError('Error', 'Document Need Delete Failed.');
            }
        }, 'json');
    });

    $('#submit_loan_calculation').click(function (event) {
        event.preventDefault();
        let customerProfileId = $('#customer_profile_id').val();
        if (customerProfileId != '') {
            $('#refresh_cal').trigger('click'); //For calculate once again if user missed to refresh calculation
            let formData = {
                'customer_profile_id': customerProfileId,
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
                'id': $('#loan_calculation_id').val()
            }

            if (isFormDataValid(formData)) {
                $.post('api/loan_entry/loan_calculation/submit_loan_calculation.php', formData, function (response) {
                    if (response.status == '1') {
                        swalSuccess('Success', 'Loan Calculation Added Successfully!');
                    } else if (response.status == '2') {
                        swalSuccess('Success', 'Loan Calculation Updated Successfully!')
                    } else {
                        swalError('Error', 'Error Occurs!')
                    }

                    $('#loan_calculation_id').val(response.last_id);
                }, 'json');
            } else {
                swalError('Warning', 'Kindly Fill All Required Fields.')
            }
        } else {
            swalError('Submit Customer Profile', 'Before Loan Calculation')
        }
    });

    $('#clear_loan_calc_form').click(function (event) {
        event.preventDefault();
        clearLoanCalcForm();
    })

}); //Document END.

function callLoanCaculationFunctions() {
    getLoanCategoryName();
    let loan_calc_id = $('#loan_calculation_id').val();
    getAutoGenLoanId(loan_calc_id);
    let cus_profile_id = $('#customer_profile_id').val();
    getDocNeedTable(cus_profile_id);
}

function getAutoGenLoanId(id) {
    $.post('api/loan_entry/loan_calculation/get_autoGen_loan_id.php', { id }, function (response) {
        $('#loan_id_calc').val(response);
    }, 'json');
}

function getLoanCategoryName() {
    $.post('api/common_files/get_loan_category_creation.php', function (response) {
        let appendLoanCatOption = '';
        appendLoanCatOption += '<option value="">Select Loan Category</option>';
        $.each(response, function (index, val) {
            let selected = '';
            let loan_category_calc2 = $('#loan_category_calc2').val();
            if (val.id == loan_category_calc2) {
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

        if (response[0].due_type == 'emi') {
            $('#due_type_calc').val('EMI');
        } else if (response[0].due_type == 'interest') {
            $('#due_type_calc').val('Interest');
        }
        let cus_limit = parseInt($('#cus_limit').val());
        let loan_limit = parseInt(response[0].loan_limit);
        let min_loan_limit;

        if (isNaN(cus_limit) || isNaN(loan_limit)) {
            min_loan_limit = 0; // Both values are NaN
        } else {
            min_loan_limit = (cus_limit < loan_limit) ? cus_limit : loan_limit; // Both values are valid numbers
        }
        $('#loan_amount_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + min_loan_limit + `' ){ alert("Enter Lesser than '${min_loan_limit}'"); $(this).val(""); }`); //To check value between range

        var int_rate_upd = ($('#int_rate_upd').val()) ? $('#int_rate_upd').val() : '';
        var due_period_upd = ($('#due_period_upd').val()) ? $('#due_period_upd').val() : '';
        var doc_charge_upd = ($('#doc_charge_upd').val()) ? $('#doc_charge_upd').val() : '';
        var proc_fee_upd = ($('#proc_fees_upd').val()) ? $('#proc_fees_upd').val() : '';
        //To set min and maximum 
        $('.min-max-int').text('* (' + response[0].interest_rate_min + '% - ' + response[0].interest_rate_max + '%) ');
        $('#interest_rate_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + response[0].interest_rate_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                            if( parseFloat($(this).val()) < '`+ response[0].interest_rate_min + `' && parseFloat($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
        $('#interest_rate_calc').val(int_rate_upd);
        $('.min-max-due').text('* (' + response[0].due_period_min + ' - ' + response[0].due_period_max + ') ');
        $('#due_period_calc').attr('onChange', `if( parseInt($(this).val()) > '` + response[0].due_period_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                            if( parseInt($(this).val()) < '`+ response[0].due_period_min + `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
        $('#due_period_calc').val(due_period_upd);

        $('.min-max-doc').text('* (' + response[0].doc_charge_min + '% - ' + response[0].doc_charge_max + '%) ');
        $('#doc_charge_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + response[0].doc_charge_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                            if( parseFloat($(this).val()) < '`+ response[0].doc_charge_min + `' && parseFloat($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
        $('#doc_charge_calc').val(doc_charge_upd);

        $('.min-max-proc').text('* (' + response[0].processing_fee_min + '% - ' + response[0].processing_fee_max + '%) ');
        $('#processing_fees_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + response[0].processing_fee_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                            if( parseFloat($(this).val()) < '`+ response[0].processing_fee_min + `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
        $('#processing_fees_calc').val(proc_fee_upd);

    }, 'json');
}

function clearCalcSchemeFields(type) {
    $('.to_clear').val('');
    $('.min-max-int').text('*');
    $('.min-max-due').text('*');
    $('.min-max-doc').text('*');
    $('.min-max-proc').text('*');
    if (type == '1') { //Scheme
        $('#interest_rate_calc').prop('readonly', true);
        $('#due_period_calc').prop('readonly', true);
    } else {
        $('#interest_rate_calc').prop('readonly', false);
        $('#due_period_calc').prop('readonly', false);
    }
}

function dueMethodScheme(schemeDueMethod, loanCatId) {
    $.post('api/common_files/get_due_method_scheme.php', { schemeDueMethod, loanCatId }, function (response) {
        clearCalcSchemeFields('1') //to clear fields.
        let appendSchemeNameOption = '';
        appendSchemeNameOption += '<option value="">Select Scheme Name</option>';
        $.each(response, function (index, val) {
            let selected = '';
            let scheme_edit_it = $('#scheme_name_edit').val();
            if (val.id == scheme_edit_it) {
                selected = 'selected';
            }
            appendSchemeNameOption += '<option value="' + val.id + '" ' + selected + '>' + val.scheme_name + '</option>';
        });
        $('#scheme_name_calc').empty().append(appendSchemeNameOption);
    }, 'json');

    if (schemeDueMethod == '2') {
        $('.scheme_day').show();
    } else {
        $('.scheme_day').hide();
        $('.scheme_day_calc').val('');
    }
}

function schemeCalAjax(id) {

    if (id != '') {
        let doc_charge_upd = ($('#doc_charge_upd').val()) ? $('#doc_charge_upd').val() : '';
        let proc_fee_upd = ($('#proc_fees_upd').val()) ? $('#proc_fees_upd').val() : '';

        $.post('api/loan_category_creation/get_scheme_data.php', { id }, function (response) {
            //To set min and maximum 
            $('#interest_rate_calc').val(response[0].interest_rate_percent);// setting readonly due to fixed interest
            $('#due_period_calc').val(response[0].due_period_percent);// setting readonly due to fixed due period

            (response[0].doc_charge_type == 'percent') ? type = '%' : type = '₹';//Setting symbols
            $('.min-max-doc').text('* (' + response[0].doc_charge_min + ' ' + type + ' - ' + response[0].doc_charge_max + ' ' + type + ') '); //setting min max values in span
            $('#doc_charge_calc').attr('onChange', `if( parseInt($(this).val()) > '` + response[0].doc_charge_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                                    if( parseInt($(this).val()) < '`+ response[0].doc_charge_min + `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
            $('#doc_charge_calc').val(doc_charge_upd);

            (response[0].processing_fee_type == 'percent') ? type = '%' : type = '₹';//Setting symbols
            $('.min-max-proc').text('* (' + response[0].processing_fee_min + ' ' + type + ' - ' + response[0].processing_fee_max + ' ' + type + ') ');//setting min max values in span
            $('#processing_fees_calc').attr('onChange', `if( parseInt($(this).val()) > '` + response[0].processing_fee_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                                if( parseInt($(this).val()) < '`+ response[0].processing_fee_min + `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
            $('#processing_fees_calc').val(proc_fee_upd);

        }, 'json');

    } else {
        clearCalcSchemeFields('1')
    }
}

//To Get Loan Calculation for After Interest
function getLoanAfterInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee) {

    $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card
    $('#principal_amnt_calc').val(parseInt(loan_amt).toFixed(0)); // principal amt as same as loan amt for after interest

    var interest_rate = (parseInt(loan_amt) * (parseFloat(int_rate) / 100) * parseInt(due_period)).toFixed(0); //Calculate interest rate 
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

    var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100); //Get document charge from loan info and multiply with loan amt to get actual doc charge
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
    $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

    var proc_fee = parseInt(loan_amt) * (parseFloat(proc_fee) / 100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
    $('#processing_fees_calculate').val(parseInt(roundeprocfee));

    var net_cash = parseInt(loan_amt) - parseFloat(roundeddoccharge) - parseFloat(roundeprocfee); //Net cash will be calculated by subracting other charges
    $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
}

//To Get Loan Calculation for Interest due type
function getLoanInterest(loan_amt, int_rate, doc_charge, proc_fee) {

    $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card
    $('#principal_amnt_calc').val(parseInt(loan_amt).toFixed(0));

    $('#total_amnt_calc').val('');
    $('#due_amnt_calc').val('');//Due period will be monthly by default so no need of due amt

    var int_amt = (parseInt(loan_amt) * (parseFloat(int_rate) / 100)).toFixed(0); //Calculate interest rate 

    var roundedInterest = Math.ceil(int_amt / 5) * 5;
    if (roundedInterest < int_amt) {
        roundedInterest += 5;
    }
    $('.int-diff').text('* (Difference: +' + parseInt(roundedInterest - int_amt) + ')'); //To show the difference amount
    $('#interest_amnt_calc').val(parseInt(roundedInterest));

    var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100); //Get document charge from loan info and multiply with loan amt to get actual doc charge
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
    $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

    var proc_fee = parseInt(loan_amt) * (parseFloat(proc_fee) / 100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
    $('#processing_fees_calculate').val(parseInt(roundeprocfee));

    var net_cash = parseInt(loan_amt) - parseInt(doc_charge) - parseInt(proc_fee); //Net cash will be calculated by subracting other charges
    $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
}

//To Get Loan Calculation for Monthly Scheme method
function getLoanMonthly(loan_amt, int_rate, due_period, doc_charge, proc_fee) {

    $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card

    var int_amt = (parseInt(loan_amt) * (parseFloat(int_rate) / 100)).toFixed(0); //Calculate interest rate 
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
    if (doc_type.includes('₹')) {
        var doc_charge = parseInt(doc_charge); //Get document charge from loan info and directly show the document charge provided because of it is in rupees
    } else if (doc_type.includes('%')) {
        var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100); //Get document charge from loan info and multiply with loan amt to get actual doc charge
    }
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
    $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

    var proc_type = $('.min-max-proc').text(); //Scheme may have Processing fee in rupees or percentage . so getting symbol from span
    if (proc_type.includes('₹')) {
        var proc_fee = parseInt(proc_fee);//Get processing fee from loan info and directly show the Processing Fee provided because of it is in rupees
    } else if (proc_type.includes('%')) {
        var proc_fee = parseInt(loan_amt) * (parseInt(proc_fee) / 100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
    }
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
    $('#processing_fees_calculate').val(parseInt(roundeprocfee));

    var net_cash = parseInt(princ_amt) - parseInt(doc_charge) - parseInt(proc_fee); //Net cash will be calculated by subracting other charges
    $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
}

//To Get Loan Calculation for Weekly Scheme method
function getLoanWeekly(loan_amt, int_rate, due_period, doc_charge, proc_fee) {

    $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card

    var int_amt = (parseInt(loan_amt) * (parseFloat(int_rate) / 100)).toFixed(0); //Calculate interest rate
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
    if (doc_type.includes('₹')) {
        var doc_charge = parseInt(doc_charge); //Get document charge from loan info and directly show the document charge provided because of it is in rupees
    } else if (doc_type.includes('%')) {
        var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100); //Get document charge from loan info and multiply with loan amt to get actual doc charge
    }
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
    $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

    var proc_type = $('.min-max-proc').text();//Scheme may have Processing fee in rupees or percentage . so getting symbol from span
    if (proc_type.includes('₹')) {
        var proc_fee = parseInt(proc_fee);//Get processing fee from loan info and directly show the Processing Fee provided because of it is in rupees
    } else if (proc_type.includes('%')) {
        var proc_fee = parseInt(loan_amt) * (parseInt(proc_fee) / 100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
    }
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
    $('#processing_fees_calculate').val(parseInt(roundeprocfee));

    var net_cash = parseInt(princ_amt) - parseInt(doc_charge) - parseInt(proc_fee); //Net cash will be calculated by subracting other charges
    $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
}

//To Get Loan Calculation for Daily Scheme method
function getLoanDaily(loan_amt, int_rate, due_period, doc_charge, proc_fee) {

    $('#loan_amnt_calc').val(parseInt(loan_amt).toFixed(0)); //get loan amt from loan info card

    var int_amt = (parseInt(loan_amt) * (parseFloat(int_rate) / 100)).toFixed(0); //Calculate interest rate 
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
    if (doc_type.includes('₹')) {
        var doc_charge = parseInt(doc_charge); //Get document charge from loan info and directly show the document charge provided because of it is in rupees
    } else if (doc_type.includes('%')) {
        var doc_charge = parseInt(loan_amt) * (parseFloat(doc_charge) / 100); //Get document charge from loan info and multiply with loan amt to get actual doc charge
    }
    var roundeddoccharge = Math.ceil(doc_charge / 5) * 5; //to increase document charge to nearest multiple of 5
    if (roundeddoccharge < doc_charge) {
        roundeddoccharge += 5;
    }
    $('.doc-diff').text('* (Difference: +' + parseInt(roundeddoccharge - doc_charge) + ')'); //To show the difference amount from old to new
    $('#doc_charge_calculate').val(parseInt(roundeddoccharge));

    var proc_type = $('.min-max-proc').text();//Scheme may have Processing fee in rupees or percentage . so getting symbol from span
    if (proc_type.includes('₹')) {
        var proc_fee = parseInt(proc_fee);//Get processing fee from loan info and directly show the Processing Fee provided because of it is in rupees
    } else if (proc_type.includes('%')) {
        var proc_fee = parseInt(loan_amt) * (parseInt(proc_fee) / 100);//Get processing fee from loan info and multiply with loan amt to get actual proc fee
    }
    var roundeprocfee = Math.ceil(proc_fee / 5) * 5; //to increase Processing fee to nearest multiple of 5
    if (roundeprocfee < proc_fee) {
        roundeprocfee += 5;
    }
    $('.proc-diff').text('* (Difference: +' + parseInt(roundeprocfee - proc_fee) + ')'); //To show the difference amount from old to new
    $('#processing_fees_calculate').val(parseInt(roundeprocfee));

    var net_cash = parseInt(princ_amt) - parseInt(doc_charge) - parseInt(proc_fee); //Net cash will be calculated by subracting other charges
    $('#net_cash_calc').val(parseInt(net_cash).toFixed(0));
}

// Function to check if all values in an object are not empty
function isFormDataValid(formData) {
    for (let key in formData) {
        if (key != 'id' && key != 'category_info_calc' && key != 'due_method_calc' && key != 'due_type_calc' && key != 'profit_method_calc' && key != 'scheme_due_method_calc' && key != 'scheme_day_calc' && key != 'scheme_name_calc' && key != 'agent_id_calc' && key != 'agent_name_calc' && key != 'customer_profile_id') {
            if (formData[key] == '' || formData[key] == null || formData[key] == undefined) {
                return false;
            }
        }
    }

    if (formData['profit_type_calc'] == '0') { //Calculation
        if (formData['due_method_calc'] == '' || formData['due_method_calc'] == null || formData['due_method_calc'] == undefined ||
            formData['due_type_calc'] == '' || formData['due_type_calc'] == null || formData['due_type_calc'] == undefined ||
            formData['profit_method_calc'] == '' || formData['profit_method_calc'] == null || formData['profit_method_calc'] == undefined) {
            return false;
        }
    } else if (formData['profit_type_calc'] == '1') { //Scheme
        if (formData['scheme_due_method_calc'] == '' || formData['scheme_due_method_calc'] == null || formData['scheme_due_method_calc'] == undefined ||
            formData['scheme_name_calc'] == '' || formData['scheme_name_calc'] == null || formData['scheme_name_calc'] == undefined) {
            return false;
        }
        if (formData['scheme_due_method_calc'] == '2') {//weekly
            if (formData['scheme_day_calc'] == '' || formData['scheme_day_calc'] == null || formData['scheme_day_calc'] == undefined) {
                return false;
            }
        }
    }

    if (formData['referred_calc'] == '0') { //Referred
        if (formData['agent_id_calc'] == '' || formData['agent_id_calc'] == null || formData['agent_id_calc'] == undefined ||
            formData['agent_name_calc'] == '' || formData['agent_name_calc'] == null || formData['agent_name_calc'] == undefined) {
            return false;
        }
    }

    return true;
}

function changeInttoBen() {
    let dueType = document.getElementById('due_type_calc');
    let intLabel = document.querySelector('label[for="interest_amnt_calc"]');
    if (dueType.value == 'Interest') {
        intLabel.textContent = 'Benefit Amount';
    } else {
        intLabel.textContent = 'Interest Amount';
    }
}

function clearLoanCalcForm() {
    // Clear input fields except those with IDs 'loan_id_calc' and 'loan_date_calc'
    $('#loan_entry_loan_calculation').find('input').each(function () {
        var id = $(this).attr('id');
        if (id !== 'loan_id_calc' && id !== 'loan_date_calc' && id != 'profit_method_calc' && id != 'refresh_cal' && id != 'submit_doc_need') {
            $(this).val('');
        }
    });

    $('.min-max-int').text('*'); $('.min-max-due').text('*'); $('.min-max-doc').text('*'); $('.min-max-proc').text('*');
    $('.int-diff').text('*'); $('.due-diff').text('*'); $('.doc-diff').text('*'); $('.proc-diff').text('*');

    // Clear all textarea fields within the specific form
    $('#loan_entry_loan_calculation').find('textarea').val('');

    // Reset all select fields within the specific form
    $('#loan_entry_loan_calculation').find('select').each(function () {
        $(this).val($(this).find('option:first').val());
    });
}

function getDocNeedTable(cusProfileId) {
    $.post('api/loan_entry/loan_calculation/document_need_list.php', { cusProfileId }, function (response) {
        let loanCategoryColumn = [
            "sno",
            "document_name",
            "action"
        ]
        appendDataToTable('#doc_need_table', response, loanCategoryColumn);
    }, 'json');
}

function loanCalculationEdit(id) {
    $.post('api/loan_entry/loan_calculation/loan_calculation_data.php', { id }, function (response) {
        $('#loan_id_calc').val(response[0].loan_id);
        $('#loan_category_calc').val(response[0].loan_category);
        $('#loan_category_calc2').val(response[0].loan_category);
        $('#category_info_calc').val(response[0].category_info);
        $('#loan_amount_calc').val(response[0].loan_amount);
        $('#profit_type_calc').val(response[0].profit_type);
        $('#due_method_calc').val(response[0].due_method);
        $('#due_type_calc').val(response[0].due_type);
        $('#profit_method_calc').val(response[0].profit_method);
        $('#scheme_due_method_calc').val(response[0].scheme_due_method);
        $('#scheme_day_calc').val(response[0].scheme_day);
        $('#scheme_name_edit').val(response[0].scheme_name);
        $('#int_rate_upd').val(response[0].interest_rate);
        $('#due_period_upd').val(response[0].due_period);
        $('#doc_charge_upd').val(response[0].doc_charge);
        $('#proc_fees_upd').val(response[0].processing_fees);
        $('#loan_amnt_calc').val(response[0].loan_amnt);
        $('#principal_amnt_calc').val(response[0].principal_amnt);
        $('#interest_amnt_calc').val(response[0].interest_amnt);
        $('#total_amnt_calc').val(response[0].total_amnt);
        $('#due_amnt_calc').val(response[0].due_amnt);
        $('#doc_charge_calculate').val(response[0].doc_charge_calculate);
        $('#processing_fees_calculate').val(response[0].processing_fees_calculate);
        $('#net_cash_calc').val(response[0].net_cash);
        $('#loan_date_calc').val(response[0].loan_date);
        $('#due_startdate_calc').val(response[0].due_startdate);
        $('#maturity_date_calc').val(response[0].maturity_date);
        $('#referred_calc').val(response[0].referred);
        $('#referred_calc').trigger('change');

        $('#profit_type_calc_scheme').show();
        if (response[0].profit_type == '0') {//Loan Calculation
            $('.calc').show();
            $('.scheme').hide();
            $('.scheme_day').hide();
            getLoanCatDetails(response[0].loan_category);
        } else if (response[0].profit_type == '1') { //Scheme
            dueMethodScheme(response[0].scheme_due_method, response[0].loan_category)
            $('.calc').hide();
            $('.scheme').show();
            schemeCalAjax(response[0].scheme_name)
        }


        setTimeout(() => {
            $('#agent_id_calc').val(response[0].agent_id);
            $('#agent_name_calc').val(response[0].agent_name);
            $('#refresh_cal').trigger('click');
        }, 1000);
    }, 'json');
}
//////////////////////////////////////////////////////////////// Loan Calculation END //////////////////////////////////////////////////////////////////////
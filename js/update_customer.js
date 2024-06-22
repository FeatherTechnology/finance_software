$(document).ready(function () {
    //Move Loan Entry  
    // Loan Entry Tab Change Radio buttons
    $(document).on('click', '#add_loan, #back_btn', function () {
        swapTableAndCreation();
    });


    $('#back_btn').click(function () {
        getcusUpdateTable();
        clearLoanCalcForm();//To clear Loan Calculation.
        clearCusProfileForm('1');//To Clear Customer Profile
    });

    $(document).on('click', '.edit-cus-update', function () {
        let id = $(this).attr('value');
        $('#customer_profile_id').val(id);
        
        swapTableAndCreation();
        editCustmerProfile(id)
    });

    $('input[name=update_type]').click(function () {
        let updateType = $(this).val();
        if (updateType  == 'cus_profile') {
            $('#cus_update_customer_profile').show(); $('#update_documentation').hide();

        } else if (updateType  == 'loan_doc') {
            $('#cus_update_customer_profile').hide(); $('#update_documentation').show();
            
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
    getcusUpdateTable();
});

function getcusUpdateTable() {
    $.post('api/update_customer_files/update_customer_list.php', function (response) {
        var columnMapping = [
            'sno',
            'cus_id',
            'cus_name',
            'area',
            'linename',
            'branch_name',
            'mobile1',
            'action'
        ];
        appendDataToTable('#cus_update_table', response, columnMapping);
        setdtable('#cus_update_table');
        //Dropdown in List Screen
        setDropdownScripts();
    }, 'json');
}

function swapTableAndCreation() {
    if ($('.update_table_content').is(':visible')) {
        $('.update_table_content').hide();
        $('#add_loan').hide();
        $('#update_content').show();
        $('#back_btn').show();

    } else {
        $('.update_table_content').show();
        $('#add_loan').show();
        $('#update_content').hide();
        $('#back_btn').hide();
        $('#customer_profile').trigger('click')
    }
}

function clearCusProfileForm(type) {
    // Clear input fields except those with IDs 'loan_id_calc' and 'loan_date_calc'
    $('#cus_update_customer_profile').find('input').each(function () {
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
    $('#cus_update_customer_profile').find('textarea').val('');

    //clear all upload inputs within the form.
    $('#cus_update_customer_profile').find('input[type="file"]').val('');

    // Reset all select fields within the specific form
    $('#cus_update_customer_profile').find('select').each(function () {
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


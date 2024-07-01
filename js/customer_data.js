$(document).ready(function () {
    $('.new_table_content').show();
    $('.existing_table_content').hide();
    // Event handler for radio buttons
    $('input[name=customer_data]').click(function () {
        let customerDataType = $(this).val();
        if (customerDataType == 'new_list') {
            $('.new_table_content').show();
            $('.existing_table_content').hide();
        } else if (customerDataType == 'existing_list') {
            $('.new_table_content').hide();
            $('.existing_table_content').show();
        }
    });
    $('#submit_new').click(function () {
        event.preventDefault();
        //Validation
        let cus_name = $('#cus_name').val(); let area = $('#area').val(); let mobile = $('#mobile').val(); let loan_cat = $('#loan_cat').val(); let loan_amount = $('#loan_amount').val(); let new_promotion_id = $('#new_Promotion_id').val();
        if (cus_name === '' || area === '' || mobile === '' || loan_cat === '' || loan_amount === '') {
            swalError('Warning', 'Please Fill out Mandatory fields!');
            return false;
        }
        $.post('api/customer_data_files/get_existing_mobiles.php', { mobile: mobile }, function (response) {
            if (response.exists) {
                // Show an alert with the customer status if the mobile number already exists
                let statusMsg = "";
                if (response.status == 1) {
                    statusMsg = "Customer Profile Insert";
                }
                else if (response.status == 2) {
                    statusMsg = "Loan Calculation Insert";
                }
                else if (response.status == 3) {
                    statusMsg = "Moved To Approval";
                }
                else if (response.status == 4) {
                    statusMsg = "Approved";
                }
                else if (response.status == 5) {
                    statusMsg = "Cancel in Approval";
                }
                else if (response.status == 6) {
                    statusMsg = "Revoke in Approval";
                }
                else if (response.status == 7) {
                    statusMsg = "Loan Issue";
                }
                else if (response.status == 8) {
                    statusMsg = "Closed";
                }
                else if (response.status == 9) {
                    statusMsg = "Closed";
                }
                else if (response.status == 10) {
                    statusMsg = "NOC";
                }
                else if (response.status == 11) {
                    statusMsg = "NOC";
                }
                swalError('Warning', 'Mobile number already exists. Customer status: ' + statusMsg);
                return false;
            } else {
                // Proceed with form submission
                $.post('api/customer_data_files/submit_new.php', { cus_name, area, mobile, loan_cat, loan_amount, new_promotion_id }, function (response) {
                    if (response == '1') {
                        swalSuccess('Success', 'Customer Data Added Successfully!');
                       $('#new_form input').val('');
                    }
                });
            }
        }, 'json');
    })



    $('#mobile').change(function () {
        checkMobileNo($(this).val(), $(this).attr('id'));
    });
    $(document).on('click', '.newPromoDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Customer Details?', getNewPromoDelete, id);
        return;
    });
    // Reset form fields when modal is hidden
$('#add_new_list_modal').on('hidden.bs.modal', function () {
    $('#new_form').trigger('reset'); // Reset the form with id 'new_form'
});

// Reset form fields when modal backdrop is clicked
$('#add_new_list_modal').on('click', function (e) {
    if ($(e.target).hasClass('modal')) {
        $('#new_form').trigger('reset'); // Reset the form with id 'new_form'
    }
});

    /*$(document).on('click', '.existingNeedBtn', function () {
        let cus_id = $(this).val();// Get the customer ID from the checkbox value
        swalConfirm('Move', 'Do you want to Move the Customer Profile?', getConfirm, cus_id);
        return;
    });*/
})


$(function () {
    getNewPromotionTable()
    let cus_id =$("#cus_id").val();
    getExistingPromotionTable(cus_id)

});
function getNewPromotionTable() {
    $.post('api/customer_data_files/get_new_promotion.php', function (response) {
        var columnMapping = [
            'sno',
            'cus_name',
            'area',
            'mobile',
            'loan_cat',
            'loan_amount',
            'action'
        ];
        appendDataToTable('#new_list_table', response, columnMapping);
        setdtable('#new_list_table');
        $('#new_form input').val('');
    }, 'json')
}
function getNewPromoDelete(id) {
    $.post('api/customer_data_files/delete_new_promotion.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'Customer Data Deleted Successfully!');
            getNewPromotionTable()
        } else {
            swalError('Error', 'Failed to Delete Customer Data: ' + response);
        }
    }, 'json');
}
function getExistingPromotionTable(cus_id) {

    $.post('api/customer_data_files/get_existing_promotion.php',{cus_id} ,function (response) {
        var columnMapping = [
            'id',
            'cus_id',
            'cus_name',
            'area',
            'mobile1',
            'linename',
            'branch_name',
            'c_sts',
            'c_substs',
            'action'
        ];
        appendDataToTable('#existing_list_table', response, columnMapping);
        setdtable('#existing_list_table');
    }, 'json')
}
/*function getConfirm(cus_id) {
    localStorage.setItem('currentPage', 'loan_entry');
    window.location.href = 'home.php';
}*/
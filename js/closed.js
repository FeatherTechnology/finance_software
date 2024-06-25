$(document).ready(function () {

    $(document).on('click', '.closed-details', function (event) {
        event.preventDefault();
        $('#closed_list').hide();
        $('#closed_main_container,.back_to_closed_list').show();
        let cus_id = $(this).attr('value');
        $.post('api/common_files/personal_info.php', { cus_id }, function (response) {
            if (response.length > 0) {
                $('#cus_id').val(response[0].cus_id);
                $('#cus_name').val(response[0].cus_name);
                $('#area').val(response[0].area);
                $('#branch_name').val(response[0].branch_name);
                $('#line').val(response[0].linename);
                $('#mobile1').val(response[0].mobile1);

                let path = "uploads/loan_entry/cus_pic/";
                $('#per_pic').val(response[0].pic);
                var img = $('#imgshow');
                img.attr('src', path + response[0].pic);
            }
        }, 'json');

     getClosedLoanList(cus_id);

    })

    $('#back_to_closed_list').click(function (event) {
        event.preventDefault();
        $('#closed_main_container,.back_to_closed_list').hide();
        $('#closed_list').show();
    })

    $('#submit_closed_remark').click(function (event) {
        event.preventDefault();
        if (validate()) {
            let cus_profile_id=$('#cus_profile_id').val();
            let sub_status = $('#sub_status').val();
            let remark = $('#remark').val();
            $.post('api/closed_files/closed_submit.php', {sub_status,remark,cus_profile_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Closed Info Updated Successfully!');
                    $('#closed_remark_form input').val('');
                    $('#closed_remark_form select').val('');
                    $('#closed_remark_form textarea').val('');
                    $('#closed_remark_model').modal('hide');
                    let cus_id = $('#cus_id').val();
                    getClosedLoanList(cus_id);
                } else{
                    swalError('Error','Failed to Closed');
                }           
            },'json');
        }else{
            swalError('Warning', 'Kindly Fill Mandatory Fields');
        }
    });
    
    // setDropdownScripts();
    $(document).on('click', '.due-chart', function() {
       
        $('#due_chart_model').modal('show');
    });
    $(document).on('click', '.penalty-chart', function() {
        $('#penalty_model').modal('show');
    });

    $(document).on('click', '.fine-chart', function(e) {
        $('#fine_model').modal('show');
    });
    $(document).on('click', '.closed-view', function() {
        let id = $(this).attr('value');
        $('#cus_profile_id').val(id)
        $('#closed_remark_model').modal('show');
    });

});
$(function () {
    getClosedListTable();
});

function getClosedListTable() {
    $.post('api/closed_files/close_list_table.php', function (response) {
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
        appendDataToTable('#closed_list_table', response, columnMapping);
        setdtable('#closed_list_table');
        //Dropdown in List Screen
        //setDropdownScripts();
    }, 'json');
}
function validate() {
    let response = true;
    let sub_status = $('#sub_status').val(); let cus_profile_id = $('#cus_profile_id').val();
    if (sub_status == '' || cus_profile_id == '') {
        response = false;
    }
    return response;
}
function getClosedLoanList(cus_id) {
    $.post('api/common_files/closed_loan_list.php', { cus_id }, function (response) {
        var columnMapping = [
            'sno',
            'loan_id',
            'loan_category',
            'loan_date',
            'closed_date',
            'loan_amount',
            'status',
            'sub_status',
            'charts',
            'action'
        ];
        appendDataToTable('#close_loan_table', response, columnMapping);
        setdtable('#close_loan_table');
        //Dropdown in List Screen
        setDropdownScripts();
    }, 'json');
}
function closeChartsModal() {
    $('#due_chart_model').modal('hide');
    $('#penalty_model').modal('hide');
    $('#fine_model').modal('hide');
    $('#closed_remark_model').modal('hide');
}
$(document).ready(function () {

    $('#cus_id').on('keyup', function () {
        var value = $(this).val();
        value = value.replace(/\D/g, "").split(/(?:([\d]{4}))/g).filter(s => s.length > 0).join(" ");
        $(this).val(value);
    });

    $('#cus_mobile').change(function () {
        checkMobileNo($(this).val(), $(this).attr('id'));
    });

    $(document).on('click', '.view_customer', function (event) {
        event.preventDefault();
        $('#customer_status').show();
        $('#custome_list, #search_form').hide();
        let cus_id = $('#cus_id').val().replace(/\s/g, '');
        getLoanTable(cus_id);
    })
    $(document).on('click', '.noc-summary', function (event) {
        event.preventDefault();
        $('#noc_summary').show();
        $('#customer_status, #custome_list, #search_form').hide();
    })

    $('#back_to_search').click(function (event) {
        event.preventDefault();
        $('#customer_status').hide();
        $('#custome_list, #search_form').show();
    })
    $('#back_to_cus_status').click(function (event) {
        event.preventDefault();
        $('#noc_summary').hide();
        $('#customer_status').show();
    })
    $(document).on('click', '.due-chart', function () {

        $('#due_chart_model').modal('show');
    });

    $(document).on('click', '.penalty-chart', function () {
       
        $('#penalty_model').modal('show');
    });

    $(document).on('click', '.fine-chart', function () {
       
        $('#fine_model').modal('show');
    });

   
    $(document).on('click', '.closed-remark', function () {
        $('#closed_remark_model').modal('show');
        
        let id = $(this).attr('value');
        $('#cus_profile_id').val(id)
        $.post('api/search_files/remark_info.php', { id }, function (response) {
            if (response.length > 0) {
                $('#sub_status').val(response[0].sub_status);
                $('#remark').val(response[0].remark);
            }
        }, 'json');

    });

    $('#submit_search').click(function (event) {
        event.preventDefault();
        let cus_id = $('#cus_id').val().replace(/\s/g, '');
        let cus_name = $('#cus_name').val();
        let area = $('#area').val();
        let mobile = $('#cus_mobile').val();
    
        if (validate()) {
            $.ajax({
                url: 'api/search_files/search_customer.php',
                type: 'POST',
                data: { cus_id, cus_name, area, mobile },
                success: function (data) {
                    $('#custome_list').show();
                  
                }
               
            });
            getSearchTable()

        } else {
            $('#custome_list').hide();
        }
    });
    
  
});
$('#closed_remark_model').on('hidden.bs.modal', function() {
    $('#closed_remark_form')[0].reset();
});

function validate() {
    let response = true;
    let cus_id = $('#cus_id').val(); let cus_name = $('#cus_name').val(); let area = $('#area').val(); let mobile = $('#cus_mobile').val();

    if (cus_id == '' && cus_name == '' && area == '' && mobile == '') {
        response = false;
        event.preventDefault();
        swalError('Error', 'Please fill any one field to search!')
    }

    return response;
}
// Function to fetch and display customer list
function getSearchTable() {
    $.post('api/search_files/search_customer.php', {
        cus_id: $('#cus_id').val().replace(/\s/g, ''),
        cus_name: $('#cus_name').val(),
        area: $('#area').val(),
        mobile: $('#cus_mobile').val()
    }, function (response) {
        // Assuming response is in JSON format and contains customer data
        if (response && response.length > 0) {
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
            appendDataToTable('#search_table', response, columnMapping);
            setdtable('#search_table');
            setDropdownScripts();
        } 
    }, 'json')
    
}
function getLoanTable(cus_id) {
    $.post('api/search_files/search_loan.php',{cus_id}, function (response) {
        var columnMapping = [
            'sno',
            'loan_date',
            'loan_id',
            'loan_category',
            'loan_amount',
            'status',
            'sub_status',
            'info',
            'charts'
        ];
        appendDataToTable('#status_table', response, columnMapping);
        setdtable('#status_table');
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

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

    $('#submit_search').click(function (event) {
        event.preventDefault();
        let cus_id = $('#cus_id').val(); let cus_name = $('#cus_name').val(); let area = $('#cus_area').val(); let mobile = $('#cus_mobile').val();
        if (validate()) {
            //ajax function
        }
    })
    setDropdownScripts();
});

function validate() {
    let response = true;
    let cus_id = $('#cus_id').val(); let cus_name = $('#cus_name').val(); let area = $('#cus_area').val(); let mobile = $('#cus_mobile').val();

    if (cus_id == '' && cus_name == '' && area == '' && mobile == '') {
        response = false;
        event.preventDefault();
        swalError('Error', 'Please fill any one field to search!')
    }

    return response;
}
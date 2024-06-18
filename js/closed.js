$(document).ready(function () {

    $(document).on('click', '.closed-details', function (event) {
        event.preventDefault();
        $('#closed_list').hide();
        $('#closed_main_container,.back_to_closed_list').show();
    })

    $('#back_to_closed_list').click(function (event) {
        event.preventDefault();
        $('#closed_main_container,.back_to_closed_list').hide();
        $('#closed_list').show();
    })

    $('#submit_closed_remark').click(function (event) {
        event.preventDefault();
        if (validate()) {
            //form submit
        }
    })
    setDropdownScripts();
});

function validate() {
    let response = true;
    let sub_status = $('#sub_status').val(); let remark = $('#remark').val();
    if (sub_status == '' || remark == '') {
        response = false;
    }
    return response;
}
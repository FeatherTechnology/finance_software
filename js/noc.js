$(document).ready(function () {
    $(document).on('click', '.noc-details', function (event) {
        event.preventDefault();
        $('#noc_list').hide();
        $('#noc_main_container,.back_to_noc_list').show();
    })
    $('#back_to_noc_list').click(function (event) {
        event.preventDefault();
        $('#noc_main_container,.back_to_noc_list').hide();
        $('#noc_list').show();
    })
    $(document).on('click', '.noc-summary', function (event) {
        event.preventDefault();
        $('#noc_summary,.back_to_loan_list').show();
        $('#loan_list, #personal_info,.back_to_noc_list').hide();
    })
    $('#back_to_loan_list').click(function (event) {
        event.preventDefault();
        $('#noc_summary, .back_to_loan_list').hide();
        $('#loan_list, #personal_info,.back_to_noc_list').show();
    })
    $('#submit_noc').click(function (event) {
        event.preventDefault();
        if (validate()) {
            //form submit
        }
    })


    setDropdownScripts();
    setCurrentDate('#date_of_noc');
});


function validate() {
    let response = true;

    return response;
}
$(document).ready(function () {

    //Closed Report Table
    $('#interest_ledger_view_report_btn').click(function () {
        let toDate = $('#to_date').val();
        let ledgerType = $('#ledger_type').val();
        if (toDate == '' || ledgerType == '') {
            swalError('Warning', 'Kindly fill the Date and Type.');
            return false;
        }

        let url;
        if (ledgerType == '1') {
            url = 'api/interest_report_files/get_interest_ledger_view_daily_report.php';

        } else if (ledgerType == '2') {
            url = 'api/interest_report_files/get_interest_ledger_view_monthly_report.php';

        }
        $.ajax({
            type: "POST",
            url: url,
            data: { toDate: toDate },
            success: function (data) {
                $('.reportDiv').empty().html(data);
            }
        });
    });
});

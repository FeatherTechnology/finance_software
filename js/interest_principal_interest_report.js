$(document).ready(function () {
    $('#reset_btn').click(function () {
        PrincipalInterestReportTable();
    })
});

function PrincipalInterestReportTable() {
    $('#principal_interest_table').DataTable().destroy();
    getUserAccess(function (downloadAccess) {
        let buttons = [];

        // Add Excel button if download access is 1
        if (downloadAccess === 1) {
            buttons.push({
                extend: 'excel',
                title: "Interest - Principal Interest Report List"
            });
        }

        // Add column visibility button
        buttons.push({
            extend: 'colvis',
            collectionLayout: 'fixed four-column',
        });

        $('#principal_interest_table').DataTable({
            "order": [
                [0, "desc"]
            ],
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': 'api/interest_report_files/get_interest_principal_interest_report.php',
                'data': function (data) {
                    var search = $('input[type=search]').val();
                    data.search = search;
                    data.from_date = $('#from_date').val();
                }
            },
            dom: 'lBfrtip',
            buttons: buttons,  // Use the dynamically constructed buttons array
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api();

                // Remove formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                // Array of column indices to sum
                var columnsToSum = [13, 14, 15, 16, 17];

                // Loop through each column index
                columnsToSum.forEach(function (colIndex) {
                    // Total over all pages for the current column
                    var total = api
                        .column(colIndex)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer for the current column
                    $(api.column(colIndex).footer()).html(`<b>` + total.toLocaleString() + `</b>`);
                });
            }
        });
    });
}

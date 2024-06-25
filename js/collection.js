$(document).ready(function(){
    $(document).on('click','.collection-details',function(){
        let cusId = $(this).attr('value');
        getPersonalInfo(cusId);
        OnLoadFunctions(cusId)
        $('#collection_list').hide();
        $('#back_to_coll_list').show();
        $('#coll_main_container').show();
    });

    $('#back_to_coll_list').click(function(){
        swapTableAndCreation();
    });

    $('#collection_mode').change(function(){
        var collection_mode = $(this).val();
        //Clear All Value initially
        $('#trans_id').val('')
        $('#trans_date').val('')
        $('#cheque_no').val('')
        if(collection_mode == '2'){ //Cheque
            $('.cheque').show();
            $('.transaction').show();
            
        }else if(collection_mode >= '3' && collection_mode <= '5'){ // ECS / IMPS/NEFT/RTGS / UPI Transaction
            $('.cheque').hide();
            $('.transaction').show();

        }else if(collection_mode == '1'){ //Cash
            $('.cheque').hide();
            $('.transaction').hide();

        }else{//If nothing chosen
            $('.cheque').hide();
            $('.transaction').hide();
        }
    });

    $(document).on('click','.pay-due', function(){
        let cp_id = $(this).attr('value');
        $('.colls-cntnr').hide();
        $('#back_to_coll_list').hide();
        $('.coll_details').show();
        $('#back_to_loan_list').show();
        
        //To get the loan category ID to store when collection form submitted
        $.ajax({
            url:'api/collection_files/collection_details.php',
            data: {"cp_id":cp_id},
            dataType:'json',
            type:'post',
            cache: false,
            success:function(response){
                var loan_category_id = response['loan_category'];
                $('#loan_category_id').val(loan_category_id)
            }
        })
        var status = $(this).closest('#loan_list_table tbody tr').find('td:nth-child(7)').text()
        var sub_status = $(this).closest('#loan_list_table tbody tr').find('td:nth-child(8)').text()
        
        $('#cp_id').val(cp_id)
        $('#status').val(status)
        $('#sub_status').val(sub_status)
        
        //To get Collection Code
        $.ajax({
            url:'api/collection_files/collection_code.php',
            data:{},
            dataType: 'json',
            type: 'post',
            cache: false,
            success: function(response){
                $('#collection_id').val(response)
            }
        });

        //To get Cheque List
        // $.ajax({
        //     url:'collectionFile/getChequeList.php',
        //     data:{'cp_id':cp_id},
        //     dataType: 'json',
        //     type: 'post',
        //     cache: false,
        //     success: function(response){
        //         $('#cheque_no').empty()
        //         $('#cheque_no').append('<option value="">Select Cheque No</option>');
        //         for(var i=0;i<response.length;i++){
        //             $('#cheque_no').append('<option value="'+response[i]['cheque_no_id']+'">'+response[i]['cheque_no']+'</option>');
        //         }
        //         $('#cheque_no').change(function(){
        //             var cheque_no = $(this).val();
        //             if(cheque_no != ''){
        //                 for(var i=0;i<response.length;i++){
        //                     if(cheque_no == response[i]['cheque_no_id']){
        //                         var holder_name = response[i]['cheque_holder_name'];
        //                     }
        //                 }
        //                 $('.chequeSpan').text('* ' + holder_name);
        //             }else{
        //                 $('.chequeSpan').text("*");
        //             }
        //         })
        //     }
        // });
        
        //in this file, details gonna fetch by request ID, Not by customer ID (Because we need loan details from particular request ID)
        $.ajax({
            url: 'api/collection_files/collection_loan_details.php',
            data: {'cp_id':cp_id},
            dataType:'json',
            type:'post',
            cache: false,
            success: function(response){
                //Display all value to readonly fields
                $('#tot_amt').val(response['total_amt'])
                $('#paid_amt').val(response['total_paid'])
                $('#bal_amt').val(response['balance'])
                $('#due_amt').val(response['due_amt'])
                $('#pending_amt').val(response['pending'])
                $('#pend_amt').val(response['pending'])
                $('#payable_amt').val(response['payable'])
                $('#payableAmount').val(response['payable'])
                $('#penalty').val(response['penalty'])
                $('#coll_charge').val(response['coll_charge']);

                if(response['loan_type'] == "interest"  ){
                    $('.till-date-int').show();
                    $('#till_date_int').val(response['till_date_int'].toFixed(0))
                    $('#tot_amt').prev().prev().text('Principal Amount')
                    $('#due_amt').prev().prev().text('Interest Amount')

                    $('.emiLoanDiv').hide()
                    $('.intLoanDiv').show()

                    //Show all in span class
                    $('.totspan').text('*')
                    $('.paidspan').text('*')
                    $('.balspan').text('*')
                    $('.pendingspan').text('*')
                    $('.payablespan').text('*')

                }else{
                    $('.till-date-int').hide();
                    $('#till_date_int').val('')
                    $('#tot_amt').prev().prev().text('Total Amount')
                    $('#due_amt').prev().prev().text('Due Amount')

                    $('.emiLoanDiv').show()
                    $('.intLoanDiv').hide()
                    
                    //to get how many due are pending till now
                    var totspan = (response['total_amt'] / response['due_amt']).toFixed(1);
                    var paidspan =(response['total_paid'] / response['due_amt']).toFixed(1);
                    var balspan =(response['balance'] / response['due_amt']).toFixed(1);
                    var pendingspan =(response['pending'] / response['due_amt']).toFixed(1);
                    var payablespan =(response['payable'] / response['due_amt']).toFixed(1);
                    
                    //Show all in span class
                    $('.totspan').text('* (No of Due : '+totspan+')')
                    $('.paidspan').text('* (No of Due : '+paidspan+')')
                    $('.balspan').text('* (No of Due : '+balspan+')')
                    $('.pendingspan').text('* (No of Due : '+pendingspan+')')
                    $('.payablespan').text('* (No of Due : '+payablespan+')')
                }
                
                //To set limitations for input fields
                $('#due_amt_track').on('blur', function() {
                    if (parseInt($(this).val()) > response['balance']) {
                        alert("Enter a Lesser Value");
                        $(this).val("");
                        $('#total_paid_track').val("");
                    }
                    $('#pre_close_waiver').trigger('blur');//this will check whether preclosure amount crosses limit
                });

                $('#princ_amt_track').on('blur', function() {
                    if (parseInt($(this).val()) > response['balance']) {
                        alert("Enter a Lesser Value");
                        $(this).val("");
                        $('#total_paid_track').val("");
                    }
                    $('#pre_close_waiver').trigger('blur');//this will check whether preclosure amount crosses limit
                });

                $('#int_amt_track').on('blur', function() {
                    if (parseInt($(this).val()) > response['payable']) {
                        alert("Enter a Lesser Value");
                        $(this).val("");
                        $('#total_paid_track').val("");
                    }
                });
                
                $('#penalty_track').on('blur', function() {
                    if (parseInt($(this).val()) > response['penalty']) {
                        alert("Enter a Lesser Value");
                        $(this).val("");
                        $('#total_paid_track').val("");
                    }
                });
                
                $('#coll_charge_track').on('blur', function() {
                    if (parseInt($(this).val()) > response['coll_charge']) {
                        alert("Enter a Lesser Value");
                        $(this).val("");
                        $('#total_paid_track').val("");
                    }
                });
                
                //To set Limitation that should not cross its limit with considering track values and previous readonly values
                $('#pre_close_waiver').on('blur', function() {
                    if(response['loan_type'] == "emi" ){
                        var due_track = $('#due_amt_track').val();
                        if (parseFloat($(this).val()) > response['balance'] - due_track) {
                            alert("Enter a Lesser Value");
                            $(this).val("");
                            $('#total_waiver').val("");
                        }
                    }else if(response['loan_type'] == 'interest'){
                        var princ_track = $('#princ_amt_track').val();
                        if (parseFloat($(this).val()) > response['balance'] - princ_track) {
                            alert("Enter a Lesser Value");
                            $(this).val("");
                            $('#total_waiver').val("");
                        }
                    }
                });
                
                $('#penalty_waiver').on('blur', function() {
                    var penalty_track = $('#penalty_track').val();
                    if (parseFloat($(this).val()) > response['penalty'] - penalty_track) {
                        alert("Enter a Lesser Value");
                        $(this).val("");
                        $('#total_waiver').val("");
                    }
                });
                
                $('#coll_charge_waiver').on('blur', function() {
                    var coll_charge_track = $('#coll_charge_track').val();
                    if (parseFloat($(this).val()) > response['coll_charge'] - coll_charge_track) {
                        alert("Enter a Lesser Value");
                        $(this).val("");
                        $('#total_waiver').val("");
                    }
                });

            }//success END.
        })

    });
    
    $(document).on('click','#back_to_loan_list', function(){
        $('.colls-cntnr').show();
        $('#back_to_coll_list').show();
        $('.coll_details').hide();
        $('#back_to_loan_list').hide();
    });

    $(document).on('click','.due-chart', function(){
        var cp_id = $(this).attr('value');
        dueChartList(cp_id,cus_id); // To show Due Chart List.
        setTimeout(()=>{
            $('.print_due_coll').click(function(){
                var id = $(this).attr('value');
                Swal.fire({
                    title: 'Print',
                    text: 'Do you want to print this collection?',
                    imageUrl: 'img/printer.png',
                    imageWidth: 300,
                    imageHeight: 210,
                    imageAlt: 'Custom image',
                    showCancelButton: true,
                    confirmButtonColor: '#009688',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url:'collectionFile/print_collection.php',
                            data:{'coll_id':id},
                            type:'post',
                            cache:false,
                            success:function(html){
                                $('#printcollection').html(html)
                                // Get the content of the div element
                                var content = $("#printcollection").html();
                            }
                        })
                    }
                })
            })
        },1000)
    });
    
    $(document).on('click','.penalty-chart', function(){
        var cp_id = $(this).attr('value');
        $.ajax({
            //to insert penalty by on click
            url: 'collectionFile/getLoanDetails.php',
                data: {'cp_id':cp_id,'cus_id':cus_id},
                dataType:'json',
                type:'post',
                cache: false,
                success: function(response){
                    penaltyChartList(cp_id,cus_id); //To show Penalty List.
                }
        })
    });
    
    $(document).on('click','.coll-charge-chart', function(){
        var cp_id = $(this).attr('value');
        collectionChargeChartList(cp_id) //To Show Fine Chart List
    });

    $(document).on('click','.coll-charge', function(){
        var cp_id = $(this).attr('value');
        resetcollCharges(cp_id);  //Fine
    }); 

});
/////////////////////////////////////////////////////////////////////////   Document END /////////////////////////////////////////////////////////////////////////
$(function(){
    getCollectionListTable();
});

function getCollectionListTable(){
    $.post('api/collection_files/collection_list.php', function (response) {
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
        appendDataToTable('#collection_list_table', response, columnMapping);
        setdtable('#collection_list_table');
    }, 'json');
}

function swapTableAndCreation() {
    if ($('#collection_list').is(':visible')) {
        $('#collection_list').hide();
        $('#coll_main_container').show();
        $('#back_to_coll_list').show();

    } else {
        $('#collection_list').show();
        $('#coll_main_container').hide();
        $('#back_to_coll_list').hide();
        getCollectionListTable();
    }
}

function getPersonalInfo(cusId){
    $.post('api/common_files/personal_info.php', { cus_id: cusId }, function(response){
        if(response.length > 0){
            $('#cus_id').val(response[0].cus_id);
            $('#cus_name').val(response[0].cus_name);
            $('#cus_area').val(response[0].area);
            $('#cus_branch').val(response[0].branch_name);
            $('#cus_line').val(response[0].linename);
            $('#cus_mobile').val(response[0].mobile1);
    
            let path = "uploads/loan_entry/cus_pic/";
            var img = $('#cus_image');
            img.attr('src', path + response[0].pic);
        }
    },'json');
}

function OnLoadFunctions(cus_id){
    //To get loan sub Status
    var pending_arr = [];
    var od_arr = [];
    var due_nil_arr = [];
    var balAmnt = [];
    $.ajax({
        url: 'api/collection_files/resetCustomerStatus.php',
        data: {'cus_id':cus_id},
        dataType:'json',
        type:'post',
        cache: false,
        success: function(response){
            if(response.length != 0){

                for(var i=0;i< response['pending_customer'].length;i++){
                    pending_arr[i] = response['pending_customer'][i]
                    od_arr[i] = response['od_customer'][i]
                    due_nil_arr[i] = response['due_nil_customer'][i]
                    balAmnt[i] = response['balAmnt'][i]
                }
                var pending_sts = pending_arr.join(',');
                $('#pending_sts').val(pending_sts);
                var od_sts = od_arr.join(',');
                $('#od_sts').val(od_sts);
                var due_nil_sts = due_nil_arr.join(',');
                $('#due_nil_sts').val(due_nil_sts);
                balAmnt = balAmnt.join(',');
            }
        }
    }).then(function(){
            showOverlay();//loader start
            var pending_sts = $('#pending_sts').val()
            var od_sts = $('#od_sts').val()
            var due_nil_sts = $('#due_nil_sts').val()
            var bal_amt = balAmnt;
            $.ajax({
                //in this file, details gonna fetch by customer ID, Not by req id (Because we need all loans from customer)
                url: 'api/collection_files/collection_loan_list.php',
                data: {'cus_id':cus_id,'pending_sts':pending_sts,'od_sts':od_sts,'due_nil_sts':due_nil_sts,'bal_amt':bal_amt},
                type:'post',
                dataType: 'json',
                cache: false,
                success: function(response){
                    $('.overlay').remove();
                    var columnMapping = [
                        'sno',
                        'loan_id',
                        'loan_category',
                        'issue_date',
                        'loan_amount',
                        'bal_amount',
                        'status',
                        'sub_status',
                        'charts',
                        'action'
                    ];
                    appendDataToTable('#loan_list_table', response, columnMapping);
                    setdtable('#loan_list_table');
                    //Dropdown in List Screen
                    setDropdownScripts();
                }
            });
            hideOverlay();//loader stop
        }); 
}//Auto Load function END
$(document).ready(function () {
    $('#branch_id').change(function () {
        let branch_id = $(this).val();
        console.log(branch_id)
        $.post('api/dashboard_files/get_branch_lines.php',{branch_id}, function(response){
            $('#line_id').val(response);
            setTimeout(() => {
                getLoanEntryCounts();
                getApprovalCounts();
                getLoanIssueCounts();
                getCollectionCounts();
                getClosedCounts();
            }, 500);
        },'json');
    });

    $('#loan_entry_title').click(function(){
        $('#loan_entry_body').slideToggle();
        setTimeout(() => {
            if($('#loan_entry_body').is(':visible')){
                getLoanEntryCounts();
            }
        }, 500);
    });

    $('#approval_title').click(function(){
        $('#approval_body').slideToggle();
        setTimeout(() => {
            if($('#approval_body').is(':visible')){
                getApprovalCounts();
            }
        }, 500);
    });

    $('#loan_issue_title').click(function(){
        $('#loan_issue_body').slideToggle();
        setTimeout(() => {
            if($('#loan_issue_body').is(':visible')){
                getLoanIssueCounts();
            }
        }, 500);
    });

    $('#collection_title').click(function(){
        $('#collection_body').slideToggle();
        setTimeout(() => {
            if($('#collection_body').is(':visible')){
                getCollectionCounts();
            }
        }, 500);
    });

    $('#closed_title').click(function(){
        $('#closed_body').slideToggle();
        setTimeout(() => {
            if($('#closed_body').is(':visible')){
                getClosedCounts();
            }
        }, 500);
    });

});

$(function () {
    checkUserScreenAccess();
});

function checkUserScreenAccess(){
    $.post('api/common_files/check_user_screen_access.php',{},function(response){
        
        let screens = response[0].screens.split(','); // Split the comma-separated string into an array
        
        if(screens.includes('9')){
            $('.loan-entry-card').show();
        }
        if(screens.includes('10')){
            $('.approval-card').show();
        }
        if(screens.includes('11')){
            $('.loan-issue-card').show();
        }
        if(screens.includes('12')){
            $('.collection-card').show();
        }
        if(screens.includes('13')){
            $('.closed-card').show();
        }
    },'json').then(function(){
        getBranchList();
    });
}


function getBranchList() {
    $.post('api/common_files/get_branch_list.php', function (response) {
        let appendBranchOption = '';
        appendBranchOption += '<option value="">Select Branch</option>';
        appendBranchOption += '<option value="0">All Branch</option>';
        $.each(response, function (index, val) {
            appendBranchOption += '<option value="' + val.id + '">' + val.branch_name + '</option>';
        });
        $('#branch_id').empty().append(appendBranchOption);

    }, 'json');
}

function getLoanEntryCounts() {
    let lineId = $('#line_id').val();
    $.post('api/dashboard_files/loan_entry_details.php', {lineId}, function(response) {
        $('#tot_entry').text(response['total_loan_entry'])
        $('#tot_issued').text(response['total_loan_issued'])
        $('#tot_bal').text(response['total_loan_balance'])
        $('#today_entry').text(response['today_loan_entry'])
        $('#today_issued').text(response['today_loan_issued'])
        $('#today_bal').text(response['today_loan_balance'])
    }, 'json');
}

function getApprovalCounts() {
    let lineId = $('#line_id').val();
    $.post('api/dashboard_files/approval_details.php', {lineId}, function(response) {
        $('#tot_approval').text(response['total_approved'])
        $('#tot_approval_issued').text(response['total_loan_issued'])
        $('#tot_approval_bal').text(response['total_approve_balance'])
        $('#today_approval').text(response['today_approved'])
        $('#today_approval_issued').text(response['today_loan_issued'])
        $('#today_approval_bal').text(response['today_approve_balance'])
    }, 'json');
}

function getLoanIssueCounts() {
    let lineId = $('#line_id').val();
    $.post('api/dashboard_files/loan_issue_details.php', {lineId}, function(response) {
        $('#tot_loan_issue').text(response['total_loan_issue'])
        $('#tot_issue_issued').text(response['total_loan_issued'])
        $('#tot_issue_bal').text(response['total_loan_balance'])
        $('#today_loan_issue').text(response['today_loan_issue'])
        $('#today_issue_issued').text(response['today_loan_issued'])
        $('#today_issue_bal').text(response['today_loan_balance'])
    }, 'json');
}

function getCollectionCounts() {
    let lineId = $('#line_id').val();
    $.post('api/dashboard_files/collection_details.php', {lineId}, function(response) {
        $('#tot_paid').text(response['total_paid'])
        $('#tot_penalty').text(response['total_penalty'])
        $('#tot_fine').text(response['total_fine'])
        $('#today_paid').text(response['today_paid'])
        $('#today_penalty').text(response['today_penalty'])
        $('#today_fine').text(response['today_fine'])
    }, 'json');
}

function getClosedCounts() {
    let lineId = $('#line_id').val();
    $.post('api/dashboard_files/closed_details.php', {lineId}, function(response) {
        $('#tot_closed').text(response['total_in_closed'])
        $('#tot_consider').text(response['total_consider'])
        $('#tot_rejected').text(response['total_rejected'])
        $('#today_closed').text(response['today_in_closed'])
        $('#today_consider').text(response['today_consider'])
        $('#today_rejected').text(response['today_rejected'])
    }, 'json');
}

<!-- Loan Entry List Start -->
<div class="card loanissue_table_content">
    <div class="card-body">
        <div class="col-12">
            <table id="loan_issue_table" class="table custom-table">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Customer ID</th>
                        <th>Customer Name</th>
                        <th>Area</th>
                        <th>Line</th>
                        <th>Branch</th>
                        <th>Loan Amount</th>
                        <th>Mobile</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<!--Loan Entry List End-->
<div id="loan_issue_content" style="display:none;">
    <div class="text-right">
        <button type="button" class="btn btn-primary" id="back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
        <br><br>
    </div>
    <div class="radio-container">
        <div class="selector">
            <div class="selector-item">
                <input type="radio" id="documentation" name="loan_issue_type" class="selector-item_radio" value="loandoc" checked>
                <label for="documentation" class="selector-item_label">Documentation</label>
            </div>
            <div class="selector-item">
                <input type="radio" id="loan_issue" name="loan_issue_type" class="selector-item_radio" value="loanissue">
                <label for="loan_issue" class="selector-item_label">Loan Issue</label>
            </div>
        </div>
    </div>
    <br>
    <form id="documentation_form" name="documentation_form">
        <input type="hidden" id="customer_profile_id">
        <div class="text-right">
            <button type="button" class="btn btn-primary" id="print_doc"><span class="icon-print"></span>&nbsp; Print </button>
            <br><br>
        </div>
        <div class="row gutters">
            <div class="col-12">
                <!--- -------------------------------------- Document Need START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Document Need</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table id="doc_need_table" class="table custom-table">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Document Name</th>
                                        </tr>
                                    </thead>
                                    <tbody> </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <!--- -------------------------------------- Document Need END ------------------------------- -->

                <!--- -------------------------------------- Cheque Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Cheque Info
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_cheque_info_modal" style="padding: 5px 35px; float: right;" tabindex='9' onclick="getChequeCreationTable();"><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="cheque_info_table" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Holder Type</th>
                                                <th>Holder Name</th>
                                                <th>Relationship</th>
                                                <th>Bank Name</th>
                                                <th>Cheque Count</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Cheque Info END ------------------------------- -->

                <!--- -------------------------------------- Document Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Document Info
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_doc_info_modal" onclick="getFamilyMember('Select Holder Name', '#doc_holder_name'); getDocCreationTable();" style="padding: 5px 35px; float: right;" tabindex='29'><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="document_info" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Document Name</th>
                                                <th>Document Type</th>
                                                <th>Holder Name</th>
                                                <th>Relationship</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Document Info END ------------------------------- -->

                <!--- -------------------------------------- Mortgage Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Mortgage Info
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_mortgage_info_modal" onclick="getFamilyMember('Select Property Holder Name', '#property_holder_name');getMortCreationTable()" style="padding: 5px 35px; float: right;" tabindex='30'><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="mortgage_info" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.No</th>
                                                <th>Property Holder Name</th>
                                                <th>Relationship</th>
                                                <th>Property Detail</th>
                                                <th>Mortgage Name</th>
                                                <th>Designation</th>
                                                <th>Mortgage Number</th>
                                                <th>Reg Office</th>
                                                <th>Mortgage Value</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Mortgage Info END ------------------------------- -->

                <!--- -------------------------------------- Endorsement Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Endorsement Info
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_endorsement_info_modal" onclick="getFamilyMember('Select Proof Of', '#owner_name');getEndorsementCreationTable();" style="padding: 5px 35px; float: right;" tabindex='31'><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="endorsement_info" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Owner Name</th>
                                                <th>Relationship</th>
                                                <th>Vehicle Details</th>
                                                <th>Endorsement Name</th>
                                                <th>Key Original</th>
                                                <th>RC Original</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Endorsement Info END ------------------------------- -->

                <!--- -------------------------------------- Gold Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Gold Info
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_gold_info_modal" style="padding: 5px 35px; float: right;" tabindex='31' onclick="getGoldCreationTable()"><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="gold_info" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Gold Type</th>
                                                <th>Purity</th>
                                                <th>Weight</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Gold Info END ------------------------------- -->

            </div>
        </div>
    </form>

    <!-- -------------------------------------- Loan Calculation START ------------------------------ -->
    <form id="loan_issue_form" name="loan_issue_form" style="display: none;">
        <input type="hidden" id="due_period_calc">
        <input type="hidden" id="profit_type_calc">
        <input type="hidden" id="due_method_calc">
        <input type="hidden" id="scheme_due_method_calc">
        <input type="hidden" id="scheme_day_calc">
        <div class="row gutters">
            <div class="col-12">

                <!--- -------------------------------------- Personal Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Personal Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="cus_id"> Customer ID</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="cus_id" name="cus_id" tabindex="1" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="cus_name"> Customer Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="cus_name" name="cus_name" tabindex="2" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="cus_data"> Customer Data</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="cus_data" name="cus_data" tabindex="3" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="mobile1"> Mobile Number </label><span class="text-danger">*</span>
                                            <input type="number" class="form-control " id="mobile1" name="mobile1" tabindex="4" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="cus_area"> Area </label><span class="text-danger">*</span>
                                            <input type="text" class="form-control " id="cus_area" name="cus_area" tabindex="5" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="pic"> Photo</label><span class="text-danger">*</span><br>
                                            <img id='imgshow' class="img_show" src='img\avatar.png' />
                                            <input type="hidden" id="per_pic">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Personal Info END ------------------------------- -->

                <!--- -------------------------------------- Loan Calculate START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Loan Calculation</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_category_calc"> Loan Category</label><span class="text-danger">*</span>
                                    <input class="form-control" id="loan_category_calc" name="loan_category_calc" tabindex="6" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_amnt_calc">Loan Amount</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control refresh_loan_calc" id="loan_amnt_calc" name="loan_amnt_calc" tabindex="7" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="principal_amnt_calc">Principal Amount</label><span class="text-danger princ-diff">*</span>
                                    <input type="number" class="form-control refresh_loan_calc" id="principal_amnt_calc" name="principal_amnt_calc" tabindex="8" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="interest_amnt_calc">Interest Amount</label><span class="text-danger int-diff">*</span>
                                    <input type="number" class="form-control refresh_loan_calc" id="interest_amnt_calc" name="interest_amnt_calc" tabindex="9" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="total_amnt_calc">Total Amount</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control refresh_loan_calc" id="total_amnt_calc" name="total_amnt_calc" tabindex="10" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="due_amnt_calc">Due Amount</label><span class="text-danger due-diff">*</span>
                                    <input type="number" class="form-control refresh_loan_calc" id="due_amnt_calc" name="due_amnt_calc" tabindex="11" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_charge_calculate">Document Charges</label><span class="text-danger doc-diff">*</span>
                                    <input type="number" class="form-control refresh_loan_calc" id="doc_charge_calculate" name="doc_charge_calculate" tabindex="12" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="processing_fees_calculate">Processing Fees</label><span class="text-danger proc-diff">*</span>
                                    <input type="number" class="form-control refresh_loan_calc" id="processing_fees_calculate" name="processing_fees_calculate" tabindex="13" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="net_cash_calc">Net Cash</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control refresh_loan_calc" id="net_cash_calc" name="net_cash_calc" tabindex="14" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Loan Calculate END ------------------------------- -->

                <!--- -------------------------------------- Collection Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Collection Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_date_calc">Loan date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="loan_date_calc" name="loan_date_calc" tabindex="15" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="due_startdate_calc">Due Start Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="due_startdate_calc" name="due_startdate_calc" tabindex="16">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="maturity_date_calc">Maturity Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="maturity_date_calc" name="maturity_date_calc" tabindex="17" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Collection Info END ------------------------------- -->

                <!--- -------------------------------------- Issue Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Issue Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="balance_net_cash">Balance Net Cash</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" id="balance_net_cash" name="balance_net_cash" tabindex="18" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="payment_mode">Payment Mode</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="payment_mode" name="payment_mode" tabindex="19">
                                        <option value=""> Select Payment Mode</option>
                                        <option value="1"> Cash </option>
                                        <option value="2"> Bank Transfer </option>
                                        <option value="3"> Cheque </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 transaction" style="display:none">  
                                <div class="form-group">
                                    <label for="transaction_id">Transaction ID</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" id="transaction_id" name="transaction_id" tabindex="20">
                                </div>
                            </div>            

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 checque" style="display:none"> 
                                <div class="form-group">
                                    <label for="chequeno">Cheque number</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" id="chequeno" name="chequeno" tabindex="21">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 cash_issue" style="display:none"> 
                                <div class="form-group">
                                    <label for="issue_amount">Issue Amount</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" id="issue_amount" name="issue_amount" tabindex="22">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="issue_date">Issue date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="issue_date" name="issue_date" tabindex="23" readonly>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="issue_person"> Issue Person </label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="issue_person" name="issue_person" tabindex="24">
                                        <option value=""> Select Issue Person </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="issue_relationship">Relationship</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="issue_relationship" id="issue_relationship" tabindex="25" placeholder="Relationship" readonly>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Issue Info END ------------------------------- -->

                <div class="col-12 mt-3 text-right">
                    <button name="submit_loan_issue" id="submit_loan_issue" class="btn btn-primary" tabindex="30"><span class="icon-check"></span>&nbsp;Submit</button>
                </div>
            </div>
        </div>
    </form>
    <!-- -------------------------------------- Loan Calculation END ------------------------------ -->
</div> <!-- Loan entry Content END - Customer profile & Loan Calculation -->


<!-- ------------------------------------------------------------ Cheque Info Modal START ------------------------------------------------------------- -->
<div class="modal fade" id="add_cheque_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Cheque Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" tabindex="1" onclick="getChequeInfoTable(); refreshChequeModal();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="cheque_info_form">
                        <input type="hidden" name="cheque_info_id" id='cheque_info_id'>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cq_holder_type">Holder Type</label><span class="text-danger">*</span>
                                    <select class="form-control" name="cq_holder_type" id="cq_holder_type" tabindex="2">
                                        <option value="">Select Holder Type</option>
                                        <option value="1">Customer</option>
                                        <option value="2">Guarantor</option>
                                        <option value="3">Family Member</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 cq_fam_member" style="display:none">
                                <div class="form-group">
                                    <label for="cq_fam_mem"> Family Member </label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="cq_fam_mem" name="cq_fam_mem" tabindex="3">
                                        <option value=""> Select Family Member </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cq_holder_name">Holder Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="cq_holder_name" name="cq_holder_name" tabindex="4" placeholder="Holder Name" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cq_relationship">Relationship</label>
                                    <input type="text" class="form-control" name="cq_relationship" id="cq_relationship" tabindex="5" placeholder="Relationship" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cq_bank_name">Bank Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="cq_bank_name" name="cq_bank_name" tabindex="6" placeholder="Enter Bank Name">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cheque_count">Cheque Count</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="cheque_count" id="cheque_count" tabindex="7" placeholder="Enter Cheque Count">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cq_upload">Upload</label><span class="text-danger">*</span>
                                    <input type="file" class="form-control" name="cq_upload" id="cq_upload" tabindex="8">
                                    <input type="hidden" id="cq_upload_edit">
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="" style="visibility:hidden"></label><br>
                                    <button name="submit_cheque_info" id="submit_cheque_info" class="btn btn-primary" tabindex="9"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_cheque_form" class="btn btn-outline-secondary" tabindex="10">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="cheque_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.NO</th>
                                    <th>Holder Type</th>
                                    <th>Holder Name</th>
                                    <th>Relationship</th>
                                    <th>Bank Name</th>
                                    <th>Cheque Count</th>
                                    <th>Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="11" onclick="getChequeInfoTable();refreshChequeModal();">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ------------------------------------------------------------ Cheque Info Modal END ------------------------------------------------------------- -->

<!-- ------------------------------------------------------------ Document Info Modal START --------------------------------------------------------------- -->
<div class="modal fade" id="add_doc_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Document Info</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="getDocInfoTable();refreshDocModal();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="doc_info_form">
                        <input type="hidden" name="doc_info_id" id='doc_info_id'>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_name">Document Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="doc_name" id="doc_name" tabindex="1" placeholder="Enter Document Name">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_type">Document Type</label><span class="text-danger">*</span>
                                    <select class="form-control" name="doc_type" id="doc_type" tabindex="2">
                                        <option value="">Select Document Type</option>
                                        <option value="1">Original</option>
                                        <option value="2">Xerox</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_holder_name">Holder Name</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="doc_holder_name" name="doc_holder_name" tabindex="3">
                                        <option value="">Select Holder Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_relationship">Relationship</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="doc_relationship" id="doc_relationship" tabindex="4" placeholder="Relationship" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_upload">Upload</label><span class="text-danger">*</span>
                                    <input type="file" class="form-control" name="doc_upload" id="doc_upload" tabindex="5">
                                    <input type="hidden" name="doc_upload_edit" id="doc_upload_edit">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button name="submit_doc_info" id="submit_doc_info" class="btn btn-primary" tabindex="6" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_doc_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="7">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="row">
                    <div class="col-12">
                        <table id="doc_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.No.</th>
                                    <th>Document Name</th>
                                    <th>Document Type</th>
                                    <th>Holder Name</th>
                                    <th>Relationship</th>
                                    <th>Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="getDocInfoTable();refreshDocModal()" tabindex="8">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ------------------------------------------------------------ Document Info Modal END --------------------------------------------------------------- -->

<!-- ------------------------------------------------------------ Mortgage Info Modal START --------------------------------------------------------------- -->
<div class="modal fade" id="add_mortgage_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Mortgage Info</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="getMortInfoTable();refreshMortModal();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="mortgage_form">
                        <input type="hidden" name="mortgage_info_id" id='mortgage_info_id'>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="property_holder_name">Property Holder Name</label><span class="text-danger">*</span>
                                    <select class="form-control" name="property_holder_name" id="property_holder_name" tabindex="1">
                                        <option value="">Select Property Holder Name </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mort_relationship">Relationship</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="mort_relationship" id="mort_relationship" tabindex="2" placeholder="Relationship" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mort_property_details">Property Details</label><span class="text-danger">*</span>
                                    <textarea class="form-control" name="mort_property_details" id="mort_property_details" tabindex="3"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mortgage_name">Mortgage Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="mortgage_name" id="mortgage_name" tabindex="4" placeholder="Enter Mortgage Name">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mort_designation">Designation</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="mort_designation" id="mort_designation" tabindex="5" placeholder="Enter Designation">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mortgage_no">Mortgage Number</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="mortgage_no" id="mortgage_no" tabindex="6" placeholder="Mortgage Number">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="reg_office">Reg Office</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="reg_office" id="reg_office" tabindex="7" placeholder="Reg Office">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mortgage_value">Mortgage Value</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="mortgage_value" id="mortgage_value" tabindex="8" placeholder="Mortgage value">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mort_upload">Upload</label><span class="text-danger">*</span>
                                    <input type="file" class="form-control" name="mort_upload" id="mort_upload" tabindex="9">
                                    <input type="hidden" name="mort_upload_edit" id="mort_upload_edit">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button name="submit_mortgage_info" id="submit_mortgage_info" class="btn btn-primary" tabindex="10" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_mortgage_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="11">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="mortgage_creation_table" class="table-responsive custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.No.</th>
                                    <th>Property Holder Name</th>
                                    <th>Relationship</th>
                                    <th>Property Details</th>
                                    <th>Mortgage Name</th>
                                    <th>Designation</th>
                                    <th>Mortgage Number</th>
                                    <th>Reg Office</th>
                                    <th>Mortgage Value</th>
                                    <th>Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="12" onclick="getMortInfoTable();refreshMortModal();">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ------------------------------------------------------------ Mortgage Info Modal END --------------------------------------------------------------- -->

<!-- ------------------------------------------------------------ Endorsement Info Modal START --------------------------------------------------------------- -->
<div class="modal fade" id="add_endorsement_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Endorsement Info</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="getEndorsementInfoTable();refreshEndorsementModal();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="endorsement_form">
                        <input type="hidden" name="endorsement_info_id" id='endorsement_info_id'>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="owner_name">Owner</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="owner_name" name="owner_name" tabindex="2">
                                        <option value="">Select Proof Of</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="owner_relationship">Relationship</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="owner_relationship" id="owner_relationship" tabindex="3" placeholder="Relationship" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="vehicle_details">Vehicle Details</label><span class="text-danger">*</span>
                                    <textarea class="form-control" id="vehicle_details" name="vehicle_details" tabindex="4"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="endorsement_name">Endorsement Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="endorsement_name" id="endorsement_name" tabindex="5" placeholder="Enter Endorsement Name">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="key_original">Key Original</label><span class="text-danger">*</span>
                                    <select class="form-control" name="key_original" id="key_original" tabindex="6">
                                        <option value="">Select Key Original</option>
                                        <option value="YES">YES</option>
                                        <option value="NO">NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="rc_original">RC Original</label><span class="text-danger">*</span>
                                    <select class="form-control" name="rc_original" id="rc_original" tabindex="7">
                                        <option value="">Select RC Original</option>
                                        <option value="YES">YES</option>
                                        <option value="NO">NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="endorsement_upload"> Upload</label><span class="text-danger">*</span>
                                    <input type="file" class="form-control" id="endorsement_upload" name="endorsement_upload" tabindex="8">
                                    <input type="hidden" id="endorsement_upload_edit">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button name="submit_endorsement" id="submit_endorsement" class="btn btn-primary" tabindex="9" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_endorsement_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="10">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="endorsement_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.No.</th>
                                    <th>Owner Name</th>
                                    <th>Relationship</th>
                                    <th>Vehicle Details</th>
                                    <th>Endorsement Name</th>
                                    <th>Key Original</th>
                                    <th>RC Original</th>
                                    <th>Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="11" onclick="getEndorsementInfoTable();refreshEndorsementModal();">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ------------------------------------------------------------ Endorsement Info Modal END --------------------------------------------------------------- -->

<!-- ------------------------------------------------------------ Gold Info Modal END --------------------------------------------------------------- -->
<div class="modal fade" id="add_gold_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Gold</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" onclick="getGoldInfoTable();refreshGoldModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="gold_form">
                        <input type="hidden" name="gold_info_id" id='gold_info_id'>
                        <div class="row">
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="gold_type">Gold Type</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="gold_type" id="gold_type" tabindex="1" placeholder="Enter Gold Type">
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="gold_purity">Purity</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="gold_purity" id="gold_purity" tabindex="2" placeholder="Enter Purity">
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="gold_weight">Weight</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="gold_weight" id="gold_weight" tabindex="3" placeholder="Enter Weight">
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="gold_value">Value</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="gold_value" id="gold_value" tabindex="4" placeholder="Enter Value">
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <button name="submit_gold_info" id="submit_gold_info" class="btn btn-primary" tabindex="5" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_gold_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="6">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table id="gold_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.NO</th>
                                    <th>Gold Type</th>
                                    <th>Purity</th>
                                    <th>Weight</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="7" onclick="getGoldInfoTable();refreshGoldModal();">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ------------------------------------------------------------ Gold Info Modal END --------------------------------------------------------------- -->
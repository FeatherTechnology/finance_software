<div class="row gutters">
    <div class="col-12">
        <div class="card" id="collection_list">
            <div class="card-header">
                <h5 class="card-title">Collection List</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table id="collection_list_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="50">S.No.</th>
                                    <th>Customer ID</th>
                                    <th>Customer Name</th>
                                    <th>Area</th>
                                    <th>Line</th>
                                    <th>Branch</th>
                                    <th>Mobile No</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-3 text-right" style="margin-bottom:20px">
            <button class="btn btn-primary" id="back_to_coll_list" style="display: none;"><span class="icon-arrow-left"></span> Back</button>
            <button class="btn btn-primary" id="back_to_loan_list" style="display: none;"><span class="icon-cancel"></span> cancel</button>
        </div>

        <div id="coll_main_container" style="display:none">
            <!-- Row start -->
            <div class="row gutters colls-cntnr">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Personal Info</h5>
                        </div>
                        <input type="hidden" name="pending_sts" id="pending_sts" value="" />
                        <input type="hidden" name="od_sts" id="od_sts" value="" />
                        <input type="hidden" name="due_nil_sts" id="due_nil_sts" value="" />

                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <div class="row">                                        
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_id">Customer ID</label>
                                                <input type="text" class="form-control" id="cus_id" name="cus_id" tabindex="1" disabled>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_name">Customer Name</label>
                                                <input type="text" class="form-control" id="cus_name" name="cus_name" pattern="[a-zA-Z\s]+" tabindex="2" disabled>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_area">Area</label>
                                                <input type="text" class="form-control" id="cus_area" name="cus_area" tabindex="3" disabled>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_branch">Branch</label>
                                                <input type="text" class="form-control" id="cus_branch" name="cus_branch" tabindex="4" disabled>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_line">Line</label>
                                                <input type="text" class="form-control" id="cus_line" name="cus_line" tabindex="5" disabled>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_mobile"> Mobile No</label>
                                                <input type="number" class="form-control" id="cus_mobile" name="cus_mobile" tabindex="6" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="pic"> Photo</label><br>
                                                <img id='cus_image' class="img_show" src='img\avatar.png' />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Loan List</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <table id="loan_list_table" class=" table custom-table">
                                        <thead>
                                            <th width="50">S.No.</th>
                                            <th>Loan ID</th>
                                            <th>Loan Category</th>
                                            <th>Loan Date</th>
                                            <th>Loan Amount</th>
                                            <th>Balance Amount</th>
                                            <th>Status</th>
                                            <th>Sub Status</th>
                                            <th>Charts</th>
                                            <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /////////////////////////////////////////////////// Collection Info  START ///////////////////////////////////////// -->
            <div class="card coll_details" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title">Collection Info</h5>
                </div>

                <input type="hidden" name="loan_category_id" id="loan_category_id" >
                <input type="hidden" name="cp_id" id="cp_id" >
                <input type="hidden" name="status" id="status" >
                <input type="hidden" name="sub_status" id="sub_status" >

                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Total Amount</label>&nbsp;<span class="text-danger totspan">*</span>
                                        <input type="text" class="form-control" readonly id="tot_amt" name="tot_amt" value='' tabindex='7'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Paid Amount</label>&nbsp;<span class="text-danger paidspan">*</span>
                                        <input type="text" class="form-control" readonly id="paid_amt" name="paid_amt" value='' tabindex='8'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Balance Amount</label>&nbsp;<span class="text-danger balspan">*</span>
                                        <input type="text" class="form-control" readonly id="bal_amt" name="bal_amt" value='' tabindex='9'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Due Amount</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control" readonly id="due_amt" name="due_amt" value='' tabindex='10'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Pending Amount</label>&nbsp;<span class="text-danger pendingspan">*</span>
                                        <input type="text" class="form-control" readonly id="pending_amt" name="pending_amt" value='' tabindex='11'>
                                        <input type="hidden" class="form-control" readonly id="pend_amt" name="pend_amt">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Payable Amount</label>&nbsp;<span class="text-danger payablespan">*</span>
                                        <input type="text" class="form-control" readonly id="payable_amt" name="payable_amt" value='' tabindex='12'>
                                        <input type="hidden" class="form-control" readonly id="payableAmount" name="payableAmount" >
                                    </div>
                                </div>
                                
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 till-date-int">
                                    <div class="form-group">
                                        <label for="disabledInput">Till Date Interest</label>&nbsp;<span class="text-danger ">*</span>
                                        <input type="text" class="form-control" readonly id="till_date_int" name="till_date_int" value='' tabindex='13'>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Penalty</label>&nbsp;<span class="text-danger ">*</span>
                                        <input type="text" class="form-control" readonly id="penalty" name="penalty" value='' tabindex='14'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Fine</label>&nbsp;<span class="text-danger ">*</span>
                                        <input type="text" class="form-control" readonly id="coll_charge" name="coll_charge" value='' tabindex='15'>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /////////////////////////////////////////////////// Collection Info END ///////////////////////////////////////// -->
            
            <!-- /////////////////////////////////////////////////// Collection Track START ///////////////////////////////////////// -->
            <div class="card coll_details" style="display: none;">
                <div class="card-header">
                    <div class="card-title">Collection Track</div>
                </div>
                <div class="card-body">
                    <div class="row ">
                        <!--Fields -->
                        <div class="col-md-12 ">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 emiLoanDiv">
                                    <div class="form-group">
                                        <label for="disabledInput">Due Amount</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="due_amt_track" name="due_amt_track" value='' placeholder='Enter Due Amount' tabindex='16'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 intLoanDiv" style="display: none;">
                                    <div class="form-group">
                                        <label for="disabledInput">Principal Amount</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="princ_amt_track" name="princ_amt_track" value='' placeholder='Enter Principal Amount' tabindex='17'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 intLoanDiv" style="display: none;">
                                    <div class="form-group">
                                        <label for="disabledInput">Interest Amount</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="int_amt_track" name="int_amt_track" value='' placeholder='Enter Interest Amount' tabindex='18'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Penalty</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="penalty_track" name="penalty_track" value='' placeholder='Enter Penalty Amount' tabindex='19'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Fine</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="coll_charge_track" name="coll_charge_track" value='' placeholder='Enter Fine' tabindex='20'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Total Paid</label>
                                        <input type="text" readonly class="form-control" id="total_paid_track" name="total_paid_track" value='' tabindex='21'>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Waiver Access if the user have collection access. -->
                            <div class="row collection_access">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Pre Closure</label>
                                        <input type="text" class="form-control" id="pre_close_waiver" name="pre_close_waiver" value='' placeholder='Enter Pre Closure Amount' tabindex='22'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Penalty Waiver</label>
                                        <input type="text" class="form-control" id="penalty_waiver" name="penalty_waiver" value='' placeholder='Enter Penalty Waiver' tabindex='23'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Fine Waiver</label>
                                        <input type="text" class="form-control" id="coll_charge_waiver" name="coll_charge_waiver" value='' placeholder='Enter Fine Waiver' tabindex='24'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Total Waiver</label>
                                        <input type="text" readonly class="form-control" id="total_waiver" name="total_waiver" value='' tabindex='25'>
                                    </div>
                                </div>
                            </div>

                            <div class="row">    
                                <div class="col-12"><hr></div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Collection Date</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" readonly class="form-control" id="collection_date" name="collection_date" tabindex='26'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Collection ID</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" readonly class="form-control" id="collection_id" name="collection_id" value='' tabindex='27'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Collection Mode</label>&nbsp;<span class="text-danger">*</span>
                                        <select class='form-control' id='collection_mode' name='collection_mode' tabindex='28'>
                                            <option value=''>Select Collection Mode</option>
                                            <option value='1'>Cash</option>
                                            <option value='2'>Cheque</option>
                                            <option value='3'>ECS</option>
                                            <option value='4'>IMPS/NEFT/RTGS</option>
                                            <option value='5'>UPI Transaction</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 cheque transaction" style="display:none">
                                    <div class="form-group">
                                        <label for="disabledInput">Bank Name</label>&nbsp;<span class="text-danger">*</span>
                                        <select class='form-control' id='bank_id' name='bank_id' tabindex='29'>
                                            <option value=''>Select Bank Name</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 cheque" style="display:none">
                                    <div class="form-group">
                                        <label for="disabledInput">Cheque No</label>&nbsp;<span class="text-danger chequeSpan">*</span>
                                        <select class='form-control' id='cheque_no' name='cheque_no' tabindex='30'>
                                            <option value=''>Select Cheque No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 transaction" style="display:none">
                                    <div class="form-group">
                                        <label for="disabledInput">Transaction ID</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="trans_id" name="trans_id" value='' placeholder="Enter Transaction ID" tabindex='31'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 transaction" style="display:none">
                                    <div class="form-group">
                                        <label for="disabledInput">Transaction Date</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="date" class="form-control" id="trans_date" name="trans_date" value='' tabindex='32'>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- /////////////////////////////////////////////////// Collection Track END ///////////////////////////////////////// -->
            
            <!-- Submit Button Start -->
            <div class="col-md-12 coll_details" style="display: none;">
                <div class="text-right">
                    <button type="submit" name="submit_collection" id="submit_collection" class="btn btn-primary" value="Submit" tabindex='40'><span class="icon-check"></span>&nbsp;Submit</button>
                    <!-- <button type="reset" class="btn btn-outline-secondary" tabindex="20">Clear</button> -->
                </div>
            </div>
            <!-- Submit Button End -->

        </div>

    </div>
</div>
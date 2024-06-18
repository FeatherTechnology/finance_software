<div class="row gutters">
    <div class="col-12">
        <br>
        <div class="radio-container" style="height:auto">
            <div class="selector">
                <div class="selector-item">
                    <input type="radio" id="new_promo" name="promo_type" class="selector-item_radio" value="new_promo" checked>
                    <label for="new_promo" class="selector-item_label">New Promotion</label>
                </div>
                <div class="selector-item">
                    <input type="radio" id="existing_promo" name="promo_type" class="selector-item_radio" value="existing_promo">
                    <label for="existing_promo" class="selector-item_label">Existing</label>
                </div>
            </div>
        </div>
        <br>

        <div class="card" id="new_promo_list">
            <div class="card-header">
                <h5 class="card-title">New Promotion List&nbsp;<button type="button" data-toggle="modal" data-target="#add_promo_model" style="float:right" class="btn btn-primary"><span class="icon-add"></span>&nbsp;Add</button></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table id="new_promo_table" class=" table custom-table">
                            <thead>
                                <tr>
                                    <th width="50">S.No.</th>
                                    <th>Customer Name</th>
                                    <th>Area</th>
                                    <th>Mobile No</th>
                                    <th>Loan Category</th>
                                    <th>Loan Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" id="existing_promo_list" style="display:none">
            <div class="card-header">
                <h5 class="card-title">Existing List</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table id="existing_table" class=" table custom-table">
                            <thead>
                                <tr>
                                    <th width="50">S.No.</th>
                                    <th>Customer ID</th>
                                    <th>Customer Name</th>
                                    <th>Area</th>
                                    <th>Mobile No</th>
                                    <th>Line</th>
                                    <th>Branch</th>
                                    <th>Status</th>
                                    <th>Sub Status</th>
                                    <th>Need</th>
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

<!-- /////////////////////////////////////////////////////////////////// Add New Promotion Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="add_promo_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add New Promotion</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="add_promo_form" method="post">
                        <div class="row">
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="cus_name">Customer Name</label>&nbsp;<span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="cus_name" name="cus_name" placeholder="Enter Customer Name" tabindex="2">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="cus_area">Area</label>&nbsp;<span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="cus_area" name="cus_area" placeholder="Enter Area" tabindex="3">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="cus_mobile">Mobile</label>&nbsp;<span class="text-danger">*</span>
                                    <input type="number" class="form-control" id="cus_mobile" name="cus_mobile" placeholder="Enter Mobile Number" tabindex="4" onKeyPress="if(this.value.length==10) return false;">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="loan_cat">Loan Category</label>&nbsp;<span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="loan_cat" name="loan_cat" placeholder="Enter Loan Category" tabindex="5">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="loan_amt">Loan Amount</label>&nbsp;<span class="text-danger">*</span>
                                    <input type="number" class="form-control" id="loan_amt" name="loan_amt" placeholder="Enter Loan Category" tabindex="5">
                                </div>
                            </div>
                            <div class="col-4 mt-3">
                                <button name="submit_new_promo" id="submit_new_promo" class="btn btn-primary" tabindex="5"><span class="icon-check"></span>&nbsp;Submit</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Add New Promotion Modal END ////////////////////////////////////////////////////////////////////// -->
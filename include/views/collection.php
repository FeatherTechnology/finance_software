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
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><button class="collection-details btn btn-primary">Collect</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-3 text-right back_to_coll_list" style="margin-bottom:20px">
            <button class="btn btn-primary back_to_coll_list" id="back_to_coll_list" style="display: none;"><span class="icon-arrow-left"></span> Back</button>
        </div>

        <div id="coll_main_container" style="display:none">
            <!-- Row start -->
            <div class="row gutters" id="personal_info">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Personal Info</h5>
                        </div>
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
            </div>
            <div class="card" id="loan_list">
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
                                    <th>Closed Date</th>
                                    <th>Loan Amount</th>
                                    <th>Status</th>
                                    <th>Sub Status</th>
                                    <th>Charts</th>
                                    <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <div class='dropdown'>
                                                <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
                                                <div class='dropdown-content'>
                                                    <a href='#' data-toggle="modal" data-target="#due_chart_model" title='Edit details'>Due Chart</a>
                                                    <a href='#' data-toggle="modal" data-target="#penalty_model" title='Edit details'>Penalty Chart</a>
                                                    <a href='#' data-toggle="modal" data-target="#fine_model" title='Edit details'>Fine Chart</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class='dropdown'>
                                                <button class='btn btn-outline-secondary'><i class='fa'>&#xf107;</i></button>
                                                <div class='dropdown-content'>
                                                    <a href='#' title='Pay Due'>Pay Due</a>
                                                    <a href='#' data-toggle="modal" data-target="#add_fine_model">Fine</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" id="coll_info_card" style="display: none;"> 
                <div class="card-header">
                    <h5 class="card-title">Collection Info</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="row">

                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label for="princ_amt">Principal Amount</label>
                                        <input type="text" class="form-control" id="princ_amt" name="princ_amt" tabindex="1" readonly>
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

    </div>
</div>
<div class="row gutters">
    <div class="col-12">
        <!----------------------------- CARD START  SEARCH FORM ------------------------------>
        <div>
            <form id="search_form" name="search_form" method="post" enctype="multipart/form-data">
                <!-- Row start -->
                <div class="row gutters">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Search Customer</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Fields -->
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="cus_id">Customer ID</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="cus_id" name="cus_id" placeholder="Enter Customer ID" tabindex="1" maxlength="14">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="cus_name">Customer Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="cus_name" name="cus_name" placeholder="Enter Customer Name" tabindex="2">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="area">Area</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="area" name="area" placeholder="Enter Area" tabindex="3">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="cus_mobile">Mobile Number</label><span class="text-danger">*</span>
                                            <input type="number" class="form-control" id="cus_mobile" name="cus_mobile" placeholder="Enter Mobile Number" tabindex="4" onKeyPress="if(this.value.length==10) return false;">
                                        </div>
                                    </div>
                                    <div class="col-12 mt-3 text-right">
                                        <button name="submit_search" id="submit_search" class="btn btn-primary" tabindex="5"><span class="icon-check"></span>&nbsp;Search</button>
                                        <button type="reset" class="btn btn-outline-secondary" tabindex="6">Clear</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!----------------------------- CARD END SEARCH FORM------------------------------>
            <div class="card" id="custome_list" style="display:none">
                <div class="card-header">
                    <h5 class="card-title">Customer List</h5>
                </div>
                <div class="card-body">
                    <div class="col-12">
                        <table id="search_table" class="table custom-table">
                            <thead>
                                <th width="20">S No.</th>
                                <th>Customer ID</th>
                                <th>Customer Name</th>
                                <th>Area</th>
                                <th>Branch</th>
                                <th>Line</th>
                                <th>Mobile Number</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                <!-- <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><button class="view_customer btn btn-primary">View</button></td>
                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mt-3" id="customer_status" style="display:none">
                <div class="card-header">
                    <h5 class="card-title">Customer Status&nbsp;<button type="button" id="back_to_search" style="float:right" class="btn btn-primary">Back</button></h5>
                </div>
                <div class="card-body">
                    <div class="col-12">
                        <table id="status_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th>S No.</th>
                                    <th>Date</th>
                                    <th>Loan ID</th>
                                    <th>Loan Category</th>
                                    <th>Loan Amount</th>
                                    <th colspan="2">Loan Status</th>
                                    <th colspan="2">Details</th>
                                </tr>
                                <tr>
                                    <th colspan="5"></th>
                                    <th>Status</th>
                                    <th>Sub Status</th>
                                    <th>Info</th>
                                    <th>Charts</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- <tr>
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
                                                <a href='#' title='Edit details'>Customer Profile</a>
                                                <a href='#' title='Edit details'>Loan Calculation</a>
                                                <a href='#' title='Edit details'>Documentation</a>
                                                <a href='#' data-toggle="modal" data-target="#closed_remark_model" title='Edit details'>Remark View</a>
                                                <a href='#' class="noc-summary" title='Edit details'>NOC Summary</a>
                                            </div>
                                        </div>
                                    </td>
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
                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row gutters" id="noc_summary" style="display:none">
                <div class="col-12">
                    <div class="card" style="box-shadow: none;background-color: transparent;">
                        <div class="card-header">
                            <h5 class="card-title">NOC Summary&nbsp;<button type="button" id="back_to_cus_status" style="float:right" class="btn btn-primary ">Back</button></h5>
                        </div>
                        <div class="card-body">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Cheque List</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <table class="table custom-table">
                                            <thead>
                                                <th>S No.</th>
                                                <th>Holder Type</th>
                                                <th>Holder Name</th>
                                                <th>Relationship</th>
                                                <th>Bank Name</th>
                                                <th>Cheque No.</th>
                                                <th>Date of NOC</th>
                                                <th>Handover Person</th>
                                                <th>Relationship</th>
                                                <th>Checklist</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Mortgage List</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <table class="table custom-table">
                                            <thead>
                                                <th>S No.</th>
                                                <th>Details</th>
                                                <th>Date of NOC</th>
                                                <th>Handover Person</th>
                                                <th>Relationship</th>
                                                <th>Checklist</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Endorsement List</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <table class="table custom-table">
                                            <thead>
                                                <th>S No.</th>
                                                <th>Details</th>
                                                <th>Date of NOC</th>
                                                <th>Handover Person</th>
                                                <th>Relationship</th>
                                                <th>Checklist</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Other Document List</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <table class="table custom-table">
                                            <thead>
                                                <th>S No.</th>
                                                <th>Document Name</th>
                                                <th>Document Type</th>
                                                <th>Document Holder</th>
                                                <th>Document</th>
                                                <th>Date of NOC</th>
                                                <th>Handover Person</th>
                                                <th>Relationship</th>
                                                <th>Checklist</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
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

<!-- /////////////////////////////////////////////////////////////////// Closed Remark Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="closed_remark_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Closed Remark</h5>
                <button type="button" class="close" data-dismiss="modal" onclick="closeChartsModal()" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="closed_remark_form" method="post">
                    <input type="hidden" id="cus_profile_id">
                        <div class="row">
                            <div class="col-sm-2 col-md-2 col-lg-2"></div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="sub_status">Sub Status</label>
                                    <input class="form-control" name="sub_status" id="sub_status" tabindex="2" disabled>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="remark">Remark</label>
                                    <textarea class="form-control" name="remark" id="remark" tabindex="3" disabled></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Closed Remark Modal END ////////////////////////////////////////////////////////////////////// -->

<!-- /////////////////////////////////////////////////////////////////// Due Chart Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade bd-example-modal-lg" id="due_chart_model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document" style="max-width: 70% !important">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Due Chart</h5>
                <button type="button" class="close" data-dismiss="modal" onclick="closeChartsModal()" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <table class="table custom-table">
                        <thead>
                            <th>Due No.</th>
                            <th>Due Month</th>
                            <th>Month</th>
                            <th>Due Amount</th>
                            <th>Pending</th>
                            <th>Payable</th>
                            <th>Collection Date</th>
                            <th>Collection Amount</th>
                            <th>Balance Amount</th>
                            <th>Pre Closure</th>
                            <th>Role</th>
                            <th>User ID</th>
                            <th>Collection Method</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Due Chart Modal END ////////////////////////////////////////////////////////////////////// -->
<!-- /////////////////////////////////////////////////////////////////// Penalty Chart Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="penalty_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Penalty Chart</h5>
                <button type="button" class="close" data-dismiss="modal" onclick="closeChartsModal()" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <table class="table custom-table">
                            <thead>
                                <th>S No.</th>
                                <th>Penalty Date</th>
                                <th>Penalty</th>
                                <th>Paid Date</th>
                                <th>Paid Amount</th>
                                <th>Balance Amount</th>
                                <th>Waiver Amount</th>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Penalty Chart Modal END ////////////////////////////////////////////////////////////////////// -->
<!-- /////////////////////////////////////////////////////////////////// Fine Chart Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="fine_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Fine Chart</h5>
                <button type="button" class="close" data-dismiss="modal" onclick="closeChartsModal()" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <table class="table custom-table">
                            <thead>
                                <th>S No.</th>
                                <th>Date</th>
                                <th>Fine</th>
                                <th>Purpose</th>
                                <th>Paid Date</th>
                                <th>Paid Amount</th>
                                <th>Balance Amount</th>
                                <th>Waiver Amount</th>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Fine Chart Modal END ////////////////////////////////////////////////////////////////////// -->
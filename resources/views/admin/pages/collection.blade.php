<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    @include('head.head')

</head>

<body>
    <section id="container">
        <!-- **********************************************************************************************************************************************************
        TOP BAR CONTENT & NOTIFICATIONS
        *********************************************************************************************************************************************************** -->
        @include('admin.sidebar.sidebar')
        <!--sidebar end-->
        <!-- **********************************************************************************************************************************************************
        MAIN CONTENT
        *********************************************************************************************************************************************************** -->
        <!--main content start-->
        <section class="container-fluid mt-5" id="main-content">
            <section class="wrapper">
                <!-- BASIC FORM VALIDATION -->
                <div class="row mt">
                    <div class="col-lg-8 col-md-8">
                        @if(Session::has('message'))
                        <div class="showback">
                            <h3>Alert!</h3>
                            <p class="alert alert-success">
                                {{ Session::get('message') }}
                                {{ Session::forget('message') }}
                            </p>
                        </div>
                        @endif
                        @if(Session::has('error'))
                        <div class="showback">
                            <h3>Alert!</h3>
                            <p class="alert alert-danger">
                                {{ Session::get('error') }}
                                {{ Session::forget('error') }}
                            </p>
                        </div>
                        @endif
                        <div class="form-panel">
                            <form role="form" class="form-horizontal style-form" onsubmit="return myconfirmation()"
                                action="{{url('submit_collection')}}" method="post" oncopy="return false"
                                oncut="return false" onpaste="return false">
                                @csrf
                                <div class="centered">
                                    <h3>Collection</h3>
                                </div>
                                <br>

                                <div class="form-group">
                                    <label class="col-lg-2 col-md-2 col-lg-offset-2 col-md-offset-2 control-label">Cust
                                        Id</label>
                                    <div class="col-lg-4 col-md-4">
                                        <input type="number" placeholder="Enter The Customer Id" id="customer_id"
                                            name="customer_id" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-8 col-md-8 col-lg-offset-2 col-md-offset-2 control-label">
                                        <h4>Customer Info</h4>
                                        <div id="cust_info">

                                        </div>

                                    </label>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-2 col-md-2 col-lg-offset-2 col-md-offset-2 control-label">Select</label>
                                    <div class="radio col-lg-4 col-md-4">
                                        <label>
                                            <input type="radio" name="collection" id="optionsRadios1" value="1">
                                            Daily Collection
                                        </label><br> <br>
                                        <label>
                                            <input type="radio" name="collection" id="optionsRadios2" value="2">
                                            Loan Collection
                                        </label> <br> <br>
                                        <label>
                                            <input type="radio" name="collection" id="optionsRadios3" value="3">
                                            Pending Collection
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-2 col-md-2 col-lg-offset-2 col-md-offset-2 control-label">Amount</label>
                                    <div class="col-lg-4 col-md-4">
                                        <input type="text" placeholder="Amount" name="amount" id="amount"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-2 col-md-2 col-lg-offset-2 col-md-offset-2 control-label">Details</label>
                                    <div class="col-lg-4 col-md-4">
                                        <input type="text" placeholder="Details" name="details" id="details"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                        class="col-lg-2 col-md-2 col-lg-offset-2 col-md-offset-2 control-label">Pin</label>
                                    <div class="col-lg-4 col-md-4">
                                        <input type="password" placeholder="Pin" name="con_pin" id="con_pin"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-6 col-md-6 col-lg-offset-2 col-md-offset-2">
                                        <button class="btn btn-theme" type="submit">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /form-panel -->
                    </div>
                    <!-- /col-lg-12 -->
                </div>
                <!-- /row -->
                <!-- /row -->
            </section>
            <!-- /wrapper -->
        </section>
        <!-- /MAIN CONTENT -->
        <!--main content end-->
        <!--footer start-->
        @include('footer.footer')
        <!--footer end-->
    </section>
    <script>
    $("#customer_id").change(function() {
        var id = $(this).val();
        var url = window.location.origin + '/get_customer_data/' + id;
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            success: function(response) {

                var len = 0;
                $('#cust_info').empty();

                if (response['cuss_data'] != null) {
                    len = response['cuss_data'].length;

                }

                var acc_no = response['cuss_data'][0].acc_no;
                var full_name = response['cuss_data'][0].full_name;
                var shop_name = response['cuss_data'][0].shop_name;
                if (shop_name != null) {
                    $("#cust_info").append("<b>Account Number:</b> " + acc_no +
                        "<br><b>Shop Name:</b> " +
                        shop_name + "<br><b>Customer Name:</b> " +
                        full_name);
                } else {
                    $("#cust_info").append("<b>Account Number:</b> " + acc_no +
                        "<br><b>Customer Name:</b> " +
                        full_name);
                }
                console.log(tr_str);
            },
            error(data) {
                // console.log(data);
            }
        });
    });

    $("#optionsRadios3").click(function() {
        var customer_id = document.getElementById("customer_id").value;
        var url = window.location.origin + '/get_pending_loan/' + customer_id;
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            success: function(response) {

                $('#amount').empty();

                var pending_amount = response['loan_data'][0].pending;
                document.getElementById("amount").value = pending_amount;
            },
            error(data) {
                alert("error");
                // console.log(data);
            }
        });
    });

    function myconfirmation() {
        var customer_id = document.getElementById("customer_id").value;

        if (document.getElementById('optionsRadios1').checked) {

        } else if (document.getElementById('optionsRadios2').checked) {

        } else if (document.getElementById('optionsRadios3').checked) {

        } else {
            alert('Select Collection Option First');
            return false;
        }

        if (customer_id == '') {
            alert('Enter Customer Id.');
            document.getElementById("customer_id").style.borderColor = "red";
            document.getElementById("customer_id").focus();
            return false;

        } else {
            var r = confirm("Are You Sure.");
            if (r == true) {
                return true;
            } else {
                return false;
            }
        }

    }
    </script>
    <script src="{{asset('lib/common-scripts.js')}}"></script>
    <script src="lib/form-validation-script.js"></script>

</body>


</html>

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
        <!--header start-->
        @include('admin.sidebar.sidebar')
        <!--sidebar end-->
        <!-- **********************************************************************************************************************************************************
        MAIN CONTENT
        *********************************************************************************************************************************************************** -->
        <!--main content start-->
        <section class="container-fluid" id="main-content">
            <section class="wrapper site-min-height">
                <div class="row mt">
                    <div class="col-lg-12">
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
                        <div class="row content-panel">
                            <!-- /col-md-4 -->
                            @if(!empty($customer_data))
                            @foreach ($customer_data as $cus_data)
                            <div class="col-md-4 centered">
                                @if(empty(($cus_data->shop_name)))
                                <h4><b>Account Type : </b>Persnol</h4>
                                @else
                                <h4><b>Account Type : </b>Bussiness</h4>
                                @endif

                                <div class="profile-pic">
                                    <a href="{{asset('profile/'.$cus_data->profile)}}"><img
                                            src="{{asset('profile/'.$cus_data->profile)}}" class="img-circle"></a>
                                    <!-- <p>
                                        <button class="btn btn-theme02"><i class="fa fa-pencil"></i> Edit
                                            Profile</button>
                                    </p> -->
                                </div>
                            </div>
                            <!-- /col-md-4 -->
                            <div class="col-md-8 profile-text">
                                @if(empty(($cus_data->shop_name)))
                                <h3><b>Cust Name : </b>{{$cus_data->full_name}}</h3>
                                @else
                                <h3><b>Name : </b>{{$cus_data->shop_name}}</h3>
                                <h4><b>Cust Name : </b>{{$cus_data->full_name}}</h4>
                                @endif
                                <h5>Cust Id : {{$cus_data->id}}</h5>
                                <h5>Account No : {{$cus_data->acc_no}}</h5>
                                <h5>PAN No : {{$cus_data->pan}}</h5>
                                <h5>Addhar No : {{$cus_data->aadhaar}}</h5>
                                <h5>Account Open Date : {{$cus_data->acc_open_date}}</h5>
                                <h5>Account Expire Date : {{$cus_data->acc_expire_date}}</h5>
                            </div>
                            @endforeach
                            @endif
                        </div>
                        <!-- /row -->
                    </div>
                    <!-- /col-lg-12 -->
                    <div class="col-lg-12 mt">
                        <div class="row content-panel">
                            <div class="panel-body">
                                <div class="tab-content">
                                    <div id="overview" class="tab-pane active">
                                        <div class="row">
                                            <!-- /col-md-6 -->
                                            <div class="col-md-12 detailed">
                                                <h4>Bachat Status</h4>
                                                @if(!empty($customer_data))
                                                @foreach ($customer_data as $cus_data)
                                                <div class="row centered mt mb">
                                                    <div class="col-sm-6">
                                                        <h1><i class="fa fa-money"></i></h1>
                                                        <h3>{{$cus_data->balance}}</h3>
                                                        <h4>Balance</h4>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <h1><i class="fa fa-money"></i></h1>
                                                        <h3>{{$cus_data->per_month_bachat}}</h3>
                                                        <h4>Monthly Bachat</h4>
                                                    </div>
                                                </div>
                                                @endforeach
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <div class="detailed">

                                                </div>
                                            </div>
                                            <!-- /col-md-6 -->
                                        </div>
                                        @if(!empty($current_month_bachat_data))
                                        <div>
                                            <h2>Current Month Status</h2>
                                            <div style="overflow-x:auto;">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>credited Amt</th>
                                                            <th>Pending Amt</th>
                                                            <th>Start Date</th>
                                                            <th>End Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach ($current_month_bachat_data as $curr_data)
                                                        <tr>
                                                            <td>{{$curr_data->credited}}</td>
                                                            <td>{{$curr_data->pending}}</td>
                                                            <td>{{$curr_data->start_date}}</td>
                                                            <td>{{$curr_data->end_date}}</td>
                                                        </tr>
                                                        @endforeach

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                        <br>
                                        @if(!empty($previous_month_bachat_data))
                                        <div>
                                            <h2>Pending Collectiom Month's</h2>
                                            <div style="overflow-x:auto;">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>credited Amt</th>
                                                            <th>Pending Amt</th>
                                                            <th>Penalty Amt</th>
                                                            <th>Start Date</th>
                                                            <th>End Date</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($previous_month_bachat_data as $prev_data)
                                                        <tr>
                                                            <td>{{$prev_data->credited}}</td>
                                                            <td>{{$prev_data->pending}}</td>
                                                            <td>{{$prev_data->penalty}}</td>
                                                            <td>{{$prev_data->start_date}}</td>
                                                            <td>{{$prev_data->end_date}}</td>
                                                            @if(($prev_data->is_calculate) == 0)
                                                            <td><a href="{{url('/calculate_penalty/'.$prev_data->id)}}"
                                                                    class="btn btn-theme">
                                                                    <span>Calculate Penalty</span>
                                                                </a> </td>
                                                            @else
                                                            <td><a href="{{url('/collect_penalty/'.$prev_data->id)}}"
                                                                    class="btn btn-theme">
                                                                    <span>Collect Penalty</span>
                                                                </a> </td>
                                                            @endif
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                        <!-- /OVERVIEW -->
                                    </div>
                                    @if(!empty($customer_data))
                                    @foreach ($customer_data as $cus_data)
                                    @if(($cus_data->is_active)== 1)
                                    <div class="row">
                                        <!-- /col-md-6 -->
                                        <div class="col-md-12 detailed">
                                            <div class="row centered">
                                                <div class="col-md-2">
                                                    <p>
                                                        <a href="{{url('/statement/'.$cus_data->id)}}"
                                                            class="btn btn-theme">
                                                            <span>Bachat Statement</span>
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-md-2">
                                                    <p>
                                                        <a href="{{url('/monthly_statement/'.$cus_data->id)}}"
                                                            class="btn btn-theme">
                                                            <span>Monthly Status</span>
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /col-md-6 -->
                                    </div>
                                    @endif
                                    @endforeach
                                    @endif

                                    <!-- /tab-pane -->
                                </div>
                                <!-- /tab-content -->
                            </div>
                            <!-- /panel-body -->
                        </div>
                        <!-- /col-lg-12 -->
                    </div>
                    <!-- /col-lg-12 -->
                    <!-- /col-lg-12 -->
                    <div class="col-lg-12 mt">
                        <div class="row content-panel">
                            <div class="panel-body">
                                <div class="tab-content">
                                    <div id="overview" class="tab-pane active">
                                        <div class="row">
                                            <!-- /col-md-6 -->
                                            <div class="col-md-12 detailed">
                                                <h4>Loan Status</h4>
                                                @if(!empty($current_loan_data))
                                                @foreach ($current_loan_data as $curr_loan_data)

                                                <div class="row centered mt mb">
                                                    <div class="col-sm-3">
                                                        <h1><i class="fa fa-money"></i></h1>
                                                        <h3>{{$curr_loan_data->amount}}</h3>
                                                        <h4>Loan Amount</h4>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <h1><i class="fa fa-money"></i></h1>
                                                        <h3>{{$curr_loan_data->shares_amount}}
                                                        </h3>
                                                        <h4>Shares Amount</h4>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <h1><i class="fa fa-money"></i></h1>
                                                        <h3>{{$curr_loan_data->amount - $curr_loan_data->pending_loan}}
                                                        </h3>
                                                        <h4>Recovered Loans</h4>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <h1><i class="fa fa-money"></i></h1>
                                                        <h3>{{$curr_loan_data->pending_loan}}</h3>
                                                        <h4>Pending Loan</h4>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <h1><i class="fa fa-money"></i></h1>
                                                        <h3>{{$curr_loan_data->interest}}</h3>
                                                        <h4>Interest</h4>
                                                    </div>
                                                </div>
                                                <div class="row mt mb">
                                                    <div class="col-sm-2">
                                                        <h5>Loan Period</h5>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <h5>Start Date : {{$curr_loan_data->start_date}}</h5>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <h5>End Date : {{$curr_loan_data->end_date}}</h5>
                                                    </div>
                                                </div>
                                                @endforeach
                                                @else
                                                <div class="row centered mt mb">
                                                    <div class="col-sm-12">
                                                        <h4>No active loan</h4>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <!-- /col-md-6 -->
                                        </div>
                                        <br>
                                        <!-- /OVERVIEW -->
                                    </div>

                                    <div class="row">
                                        <!-- /col-md-6 -->
                                        <div class="col-md-12 detailed">
                                            <div class="row centered">
                                                @if(empty($current_loan_data))
                                                @foreach ($customer_data as $cus_data)
                                                @if(($cus_data->is_active)== 1)
                                                <div class="col-md-2">
                                                    <p>
                                                        <a href="{{url('/give_a_loan/'.$cus_data->id)}}"
                                                            class="btn btn-theme">
                                                            <span>Give A Loan</span>
                                                        </a>
                                                    </p>
                                                </div>
                                                @endif
                                                @endforeach
                                                @else
                                                @foreach ($current_loan_data as $loan_data)

                                                <div class="col-md-2">
                                                    <p>
                                                        <a href="{{url('/loan_statement/'.$loan_data->loan_no.'/'.$cus_data->id)}}"
                                                            class="btn btn-theme">
                                                            <span>Loan Statement</span>
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-md-2">
                                                    <p>
                                                        <a href="{{url('/monthly_loan_statement/'.$loan_data->loan_no.'/'.$cus_data->id)}}"
                                                            class="btn btn-theme">
                                                            <span>Monthly Status</span>
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-md-2">
                                                    <p>
                                                        <a href="{{url('/calculate_interest/'.$loan_data->id)}}"
                                                            class="btn btn-theme">
                                                            <span>Calculate Interest</span>
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-md-2">
                                                    <p>
                                                        <a href="{{url('/collect_all_loan/'.$cus_data->id)}}"
                                                            class="btn btn-theme">
                                                            <span>Collect All Loan</span>
                                                        </a>
                                                    </p>
                                                </div>
                                                @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <!-- /col-md-6 -->
                                    </div>
                                    @if(!empty($previous_loan_data))
                                    <div>
                                        <h2>Previous Loan Status</h2>
                                        <div style="overflow-x:auto;">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Sr. No</th>
                                                        <th>Amount</th>
                                                        <th>interest</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($previous_loan_data as $prev_loan_data)
                                                    <tr>
                                                        <td>{{$prev_loan_data->loan_no}}</td>
                                                        <td>{{$prev_loan_data->amount}}</td>
                                                        <td>{{$prev_loan_data->interest}}</td>
                                                        <td>{{$prev_loan_data->start_date}}</td>
                                                        <td>{{$prev_loan_data->end_date}}</td>
                                                        <td>
                                                            <select name="select_action" class="form-control col-sm-6"
                                                                id="select_action"
                                                                onchange="location = this.options[this.selectedIndex].value;">
                                                                <option selected disabled>Select Action</option>
                                                                <option
                                                                    value="{{url('/loan_statement/'.$prev_loan_data->loan_no.'/'.$cus_data->id)}}">
                                                                    Loan Statement
                                                                </option>
                                                                <option
                                                                    value="{{url('/monthly_loan_statement/'.$prev_loan_data->loan_no.'/'.$cus_data->id)}}">
                                                                    Monthly Status
                                                                </option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif
                                    <br>
                                    <!-- /tab-pane -->
                                </div>
                                <!-- /tab-content -->
                            </div>
                            <!-- /panel-body -->
                        </div>
                        <!-- /col-lg-12 -->
                    </div>
                    <!-- /col-lg-12 -->
                    <div class="col-lg-12 mt">
                        <div class="row content-panel">
                            <div class="panel-body">
                                <div class="tab-content">
                                    <div id="overview" class="tab-pane active">
                                        <div class="row">
                                            <!-- /col-md-6 -->
                                            <div class="col-md-12 detailed">
                                                <div class="row centered">

                                                    @foreach ($customer_data as $cus_data)
                                                    @if(($cus_data->is_active)== 1)
                                                    <div class="col-md-2">
                                                        <p>
                                                            <a href="{{url('/edit_profile/'.$cus_data->id)}}"
                                                                class="btn btn-theme">
                                                                <span>Edit Profile</span>
                                                            </a>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-2">

                                                        @if(($cus_data->status)== 0)
                                                        <p>
                                                            <a href="{{url('/block/'.$cus_data->id)}}"
                                                                class="btn btn-theme04">
                                                                <span>Block</span>
                                                            </a>
                                                        </p>
                                                        @elseif(($cus_data->status)== 1)
                                                        <p>
                                                            <a href="{{url('/block/'.$cus_data->id)}}"
                                                                class="btn btn-theme">
                                                                <span>UnBlock</span>
                                                            </a>
                                                        </p>
                                                        @endif
                                                    </div>
                                                    @endif
                                                    <div class="col-md-2">
                                                        @if(($cus_data->is_account_ready_to_reuse)== 0)
                                                        <p id="close_account">
                                                            <a class="btn btn-theme04">
                                                                <span>Close Account</span>
                                                            </a>
                                                        </p>
                                                        @elseif(($cus_data->is_account_ready_to_reuse)== 1)
                                                        <p>
                                                            <a href="{{url('/reuse_account/'.$cus_data->acc_no)}}"
                                                                class="btn btn-theme">
                                                                <span>Reuse Account</span>
                                                            </a>
                                                        </p>
                                                        @endif
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <!-- /col-md-6 -->
                                        </div>
                                        <!-- /OVERVIEW -->
                                    </div>
                                    <!-- /tab-pane -->
                                </div>
                                <!-- /tab-content -->
                            </div>
                            <!-- /panel-body -->
                        </div>
                        <!-- /col-lg-12 -->
                    </div>
                    <!-- /row -->
                    <div class="col-lg-12 mt" id="confirmation">
                        <div class="row content-panel">
                            <div class="panel-body">
                                <div class="tab-content">
                                    <div id="overview" class="tab-pane active">
                                        <div class="row">
                                            <form role="form" class="form-horizontal style-form"
                                                onsubmit="return myconfirmation()"
                                                action="{{url('close_account/'.$cus_data->id)}}" method="post"
                                                oncopy="return false" oncut="return false" onpaste="return false"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="centered">
                                                    <h3>Confirmation</h3>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-3">

                                                    </div>
                                                    <div class="col-lg-4 col-md-6" style="text-align: center;">
                                                        <div class="form-group">
                                                            <label class="col-lg-4 col-md-4 control-label">Enter
                                                                Pin</label>
                                                            <div class="col-lg-8 col-md-8">
                                                                <input type="password" name="pin" id="pin"
                                                                    class="form-control" required>
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-theme04" type="submit">Close
                                                            Account</button>
                                                    </div>
                                                </div>
                                            </form>
                                            <!-- /col-md-6 -->
                                        </div>
                                        <!-- /OVERVIEW -->
                                    </div>
                                    <!-- /tab-pane -->
                                </div>
                                <!-- /tab-content -->
                            </div>
                            <!-- /panel-body -->
                        </div>
                        <!-- /col-lg-12 -->
                    </div>
                </div>
                <!-- /container -->
            </section>
            <!-- /wrapper -->
        </section>
        <!-- /MAIN CONTENT -->
        <!--main content end-->

        <!--footer start-->
        @include('footer.footer')
        <script src="{{asset('lib/common-scripts.js')}}"></script>
        <script>
        $(document).ready(function() {
            $("#confirmation").hide();
            $("#close_account").click(function() {
                $("#close_account").hide();
                $("#confirmation").show();
            });
        });

        // function myconfirmation() {
        //     var r = confirm("Are You Sure.");
        //     if (r == true) {
        //         return true;
        //     } else {
        //         return false;
        //     }
        // }
        </script>
        <!--footer end-->
    </section>
    <!-- js placed at the end of the document so the pages load faster -->
</body>





</html>
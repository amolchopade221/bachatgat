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
        @include('customer.sidebar.sidebar')
        <!--sidebar end-->
        <!-- **********************************************************************************************************************************************************
        MAIN CONTENT
        *********************************************************************************************************************************************************** -->
        <!--main content start-->
        <section class="container-fluid" id="main-content">
            <section class="wrapper">
                <h3>Monthly Bachat Statement</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="content-panel">
                            <Div class="centered">
                                <h3>Jagtap Bachatgat</h3>
                                @if(!empty($customer_data))
                                @foreach ($customer_data as $cus_data)
                                @if(!empty(($cus_data->full_name)))
                                <h4><b>Cust Name : </b>{{$cus_data->full_name}}</h4>
                                @else
                                <h4><b>Name : </b>{{$cus_data->shop_name}}</h4>
                                <h4><b>Cust Name : </b>{{$cus_data->full_name}}</h4>
                                @endif
                                <h5>Cust Id : {{$cus_data->id}}</h5>
                                <h5>Account No : {{$cus_data->acc_no}}</h5>
                                <br>
                                <h4>DETAILS OF MONTHLY STATEMENT</h4>
                                @endforeach
                                @endif
                            </Div>
                            <br>
                            <br>

                            <!-- <hr> -->
                            <div style="overflow-x:auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Monthly Pending</th>
                                            <th>Amount Of Loan Paid Off</th>
                                            <th>Pending Loan</th>
                                            <th>Interest</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($monthly_loan_statement))
                                        @foreach ($monthly_loan_statement as $data)
                                        <tr>
                                            <td>{{++$sr}}</td>
                                            <td>{{$data->monthly_pending_loan}}</td>
                                            <td>{{$data->amount_of_loan_paid_off}}</td>
                                            <td>{{$data->pending_loan}}</td>
                                            <td>{{$data->interest}}</td>
                                            <td>{{$data->start_date}}</td>
                                            <td>{{$data->end_date}}</td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </section>
        <!-- /MAIN CONTENT -->
        <!--main content end-->
        <!--footer start-->
        @include('footer.footer')
        <!--footer end-->
    </section>

    <script src="{{asset('lib/common-scripts.js')}}"></script>
    <!--script for this page-->

</body>

</html>
@extends('admin.layouts.app')

@push('styles')
@endpush

@section('content')
    
    <style>
        a{color:#333333;}
        .input-group-text {
            align-items: left;
            padding: 0px;
            font-size: 13px;
            font-weight: 100;
            text-align: left;
        }
        #calendar {
            max-width: 100%;
            background: #fff;
            padding: 15px;
        }
        .notFound{
            height: 373px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .notFound > p {
            text-align: center;
            font-size: 20px;
            color: grey;
        }
    </style>

    @php
        $role_id = admin()->user()->role_id;
    @endphp

    <div class="row g-2">
        <div class="col-md-3">
            <h2>Dashboard</h2>
        </div>

        <div class="col-md-9">
            <div class="row g-1">
                <div class="col-lg-4 d-flex flex-row"> 
                    <button class="prev-month btn btn-outline-secondary px-1" style="height: 2.4rem;"><i class="fa fa-angle-left" aria-hidden='true'></i></button>

                    <input type="text" class="form-control date-filter btn btn-outline-secondary px-0" name="bid-date-filter" id="bid-date-filter"
                    value="" onchange="datefilter(event);" />

                    <button class='next-month btn btn-outline-secondary px-1' style="height: 2.4rem;"><i class='fa fa-angle-right' aria-hidden='true'></i></button>
                </div>

                <div class="col-md-8">
                    <div class="row g-1">
                        @if ($role_id != \App\Models\Role::STAFF)
                            @if ($role_id == \App\Models\Role::MANAGER)
                                <div class="col-md-5">
                                    <select onchange="staffilter(event);" class="form-select staff-filter " name="staff" id="">
                                        <option selected="" value="">All Staff</option>
                                        @forelse ($staffs_dd as $staff)
                                            <option value="{{ $staff['id'] }}">{{ $staff['name'] }}</option>
                                        @empty
                                            <option selected="" disabled="" value="">Staffs Not Available</option>
                                        @endforelse
                                    </select>
                                </div> 
                            @else
                                <div class="col-md-6">
                                    <select class="form-select manager-filter" name="manager" id="">
                                        <option selected="" value="">All Managers</option>
                                        @forelse ($managers_dd as $manager)
                                            <option value="{{ $manager['id'] }}">{{ $manager['name'] }}</option>
                                        @empty
                                            <option selected="" disabled="" value="">Managers Not Available</option>
                                        @endforelse
                                    </select>
                                </div>
                                
                                <div class="col-md-5">
                                    <select onchange="staffilter(event);" class="form-select staff-filter " name="staff" id="">
                                        <option selected="" value="">All Staffs</option>
                                        @forelse ($staffs_dd as $staff)
                                            <option value="{{ $staff['id'] }}" >{{ $staff['name'] }}</option>
                                        @empty
                                            <option selected="" disabled="" value="">Staffs Not Available</option>
                                        @endforelse
                                    </select>
                                </div> 
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="load-table-data" style="height:100%;">
            @include('admin.dashboard-content',['staff_bid_list'=>$staff_bid_list,'staff_lead_list'=>$staff_lead_list,'record'=>$record,'awarded_per_month'=>$awarded_per_month,'month_dates'=>$month_dates,'graph_filter'=>$graph_filter,'total_hot_lead' => $total_hot_lead])
        </div>
        
        @if($role_id == \App\Models\Role::MANAGER)
            <div class="card">
                <div class="card-body radius-2">
                    <div class="card-header py-3 ">
                        <div class="row g-3 justify-content-center align-items-center">
                            <div class="col-lg-2">
                                <select onchange="loadTargetData(event);" class="form-select portal-filter" name="portal" id="">
                                    <option selected="" value="">All Portal</option>
                                    @forelse ($portals as $portal)
                                        <option value="{{$portal->slug}}">{{$portal->name}}</option>
                                    @empty
                                        <option selected value="">Portal Not Available</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <select onchange="loadTargetData(event);" class="form-select staff-filter" name="staff-filter" id="target-staff-filter">
                                    <option selected="" value="">All Staffs</option>
                                    @forelse ($staffs_dd as $staff)
                                        <option value="{{ $staff['id'] }}" >{{ $staff['name'] }}</option>
                                    @empty
                                        <option selected="" disabled="" value="">Staffs Not Available </option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="row col-lg-6 g-2 justify-content-start align-items-center w-25 px-0"> 
                                <div class="col-md-2 text-end mt-2 px-0">
                                    <button class="target-prev-month btn btn-outline-secondary px-1 w-50" style="height: 2.4rem;"><i class="fa fa-angle-left" aria-hidden='true'></i></button>
                                </div>
                                <div class="col-md-8 mt-2 ">
                                    <input type="text" class="form-control target-date-filter btn btn-outline-secondary px-0 my-0" name="target-date-filter" id="target-date-filter" value="" onchange="loadTargetData(event);" />
                                </div>
                                <div class="col-md-2 text-start mt-2 px-0">
                                    <button class='target-next-month btn btn-outline-secondary px-1' style="height: 2.4rem;"><i class='fa fa-angle-right' aria-hidden='true'></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="card-body">
                        <div class="d-flex flex-column justify-content-center align-items-center">
                            <div class="col-md-2">
                                <label for="target" class="form-label">Target<span class="text-danger"></span></label>
                                <div class="input-group md-3">
                                    <span class="input-group-text inputGroup-sizing-lg w-25 text-center"><p class="w-100 text-center my-0">$</p></span>
                                    <input type="number" class="form-control" id="target" value="{{$target}}" aria-label="Enter Target" readonly>
                                </div>
                            </div>
            
                            {{-- <div class="col-md-2">
                                @php
                                    $archieved_percentage = (float)(($archieved / 50000) * 100);
                                @endphp
                                <label class="form-label">Achieved<span class="text-danger"></span></label>
                                <div class="progress" style="height: 38px;">
                                    <div class="progress-bar" style="color:black;" role="progressbar" style="width: {{$archieved_percentage}}%;" aria-valuenow="{{$archieved_percentage}}" aria-valuemin="{{$archieved_percentage}}" aria-valuemax="100">$&nbsp;{{$archieved}}</div>
                                </div>
                            </div> --}}

                            <div class="col-md-2">
                                @php
                                    $archieved_percentage = (float)(($archieved / 50000) * 100);
                                @endphp
                                <label class="form-label">Achieved<span class="text-danger"></span></label>
                                <div class="progress" style="height: 38px; position: relative;">
                                    <div 
                                        class="progress-bar" 
                                        style="width: {{$archieved_percentage}}%; background-color: #007bff;" 
                                        role="progressbar" 
                                        aria-valuenow="{{$archieved_percentage}}" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                    </div>
                                    <span class="achieved_value"
                                        style="position: absolute; 
                                                top: 50%; 
                                                left: 50%; 
                                                transform: translate(-50%, -50%); 
                                                color: black; 
                                                font-weight: bold;">
                                        $&nbsp;{{$archieved}}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label for="received" class="form-label">Received<span class="text-danger"></span></label>
                                <div class="input-group md-3">
                                    <span class="input-group-text inputGroup-sizing-lg w-25"><p class="w-100 text-center my-0">$</p></span>
                                    <input type="number" class="form-control" id="received" value="{{$received}}" aria-label="Enter Target" readonly>
                                </div>
                            </div>
            
                            <div class="col-md-2">
                                <label for="short_fall" class="form-label">Short Fall<span class="text-danger"></span></label>
                                <div class="input-group md-3">
                                    <span class="input-group-text inputGroup-sizing-lg w-25"><p class="w-100 text-center my-0">$</p></span>
                                    <input type="number" class="form-control" id="short_fall" value="{{$short_fall}}" aria-label="Enter Target" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
@endsection

@push('scripts')
    <script>
        var isInitialTargetDataLoad = true;
        var isInitialDateLoad = true;

        $( document ).ready(function() {
            renderCalendar();
            renderGraph();
            renderPieChart();
        });

        $(function() {
            $('#bid-date-filter').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
            });

            $('.prev-month').on('click', function () {
                let start = $('#bid-date-filter').data('daterangepicker').startDate;
                let end = $('#bid-date-filter').data('daterangepicker').endDate;
                
                $('#bid-date-filter').daterangepicker({
                    startDate: start.subtract(1, 'month').startOf('month'),
                    endDate: end.subtract(1, 'month').endOf('month'),
                });
            });

            $('.next-month').on('click', function () {
                let start = $('#bid-date-filter').data('daterangepicker').startDate;
                let end = $('#bid-date-filter').data('daterangepicker').endDate;
                
                $('#bid-date-filter').daterangepicker({
                    startDate: start.add(1, 'month').startOf('month'),
                    endDate: end.add(1, 'month').endOf('month'),
                });
            });

        });

        function staffilter(event) {
            loadData();
        }
        
        function datefilter(event) {
            if (isInitialDateLoad) {
                isInitialDateLoad = false;
                return;
            }
            loadData();
        }

        function loadData(){
            let imageUrl = 'https://i.gifer.com/origin/b4/b4d657e7ef262b88eb5f7ac021edda87_w200.gif';
            let staff_filter = $('.staff-filter').val() == undefined ? '' : $('.staff-filter').val() ;
            let manager_filter = $('.manager-filter').val() == undefined ? '' :  $('.manager-filter').val();
            let dateRange = $('#bid-date-filter').val() ;

            let perPage = $('#filter-per-page').val();
            let page = $('#filter-page').val();
            let search = $('#filter-search-data').val();
            let dataF = {
                'perPage': perPage,
                'page': page,
                'method': 'ajax',
                'dateRange':dateRange,'manager_filter':manager_filter,'staff_filter':staff_filter
            };

            $.ajax({
                url : "{{route('admin.dashboard')}}",
                type: 'get',
                data: dataF,
                dataType: 'json',
                beforeSend: function(req) {
                    $('.load-table-data').html(`
                        <div class="text-center mt-5 mb-5">
                        <img src="${imageUrl}" height="50" />
                        </div>
                    `);
                },
                success: function(response){
                    $('.load-table-data').html(response.html);
                    renderCalendar();
                    renderGraph();
                    renderPieChart();
                    $('a[rel*=facebox]').facebox();
                }
            });
        }

        $('.manager-filter').on('change',function(event){
            $.ajax({
                url : "{{route('admin.staff.staff-list')}}",
                type: 'get',
                data : {id : event.target.value},
                dataType : 'json',
                success:function(response){
                    let options = "<option value=''>All Staffs</option>";
                    $('.staff-filter').empty();
                    if(response.detail.length > 0){
                        response.detail.forEach(function(value,key,array){
                            options = options + `<option value="${value.id}">${value.name}</option>`;
                        });
                    }
                    else{
                        options = options + `<option selected value="">Staffs not available</option>`;
                    }
                    $('.staff-filter').html(options);
                }
            });
            loadData();
        });

        $(function() {
            $('#target-date-filter').daterangepicker({
                startDate: moment().quarter(moment().quarter()).startOf('quarter'),
                endDate: moment().quarter(moment().quarter()).endOf('quarter'),
            });

            $('.target-prev-month').on('click', function () {
                let start = $('#target-date-filter').data('daterangepicker').startDate;
                let end = $('#target-date-filter').data('daterangepicker').endDate;
                
                $('#target-date-filter').daterangepicker({
                    startDate: start.subtract(3, 'month').startOf('month'),
                    endDate: end.subtract(3, 'month').endOf('month'),
                });
            });

            $('.target-next-month').on('click', function () {
                let start = $('#target-date-filter').data('daterangepicker').startDate;
                let end = $('#target-date-filter').data('daterangepicker').endDate;
                
                $('#target-date-filter').daterangepicker({
                    startDate: start.add(3, 'month').startOf('month'),
                    endDate: end.add(3, 'month').endOf('month'),
                });
            });
        });

        function loadTargetData(event) {
            if (isInitialTargetDataLoad) {
                isInitialTargetDataLoad = false;
                return;
            }

            let dateRange = '';
            let portalFilter = '';
            let staff_filter = '';
            let manager_filter = '';

            portalFilter = $('.portal-filter').val();
            staff_filter = $('#target-staff-filter').val() == undefined ? '' : $('#target-staff-filter').val();
            manager_filter = $('#target-manager-filter').val() == undefined ? '' : $('#target-manager-filter').val();
            dateRange = $('#target-date-filter').val();

            extraSearchItem = "portalFilter=" + portalFilter + '&' + 'staff_filter=' + staff_filter + '&' +'manager_filter='+ manager_filter + '&' + 'dateRange=' + dateRange;

            let url = "{{ route('admin.dashboard.refresh-target-data') }}"+ "?" + extraSearchItem;

            $.ajax({
                url:url,
                type:"get",
                // data:{action:'refreshTargetData'},
                success:function(response){
                    let archieved_percentage = parseFloat((response.archieved / 50000) * 100);
                    $('.achieved_value').html(`$ ${response.archieved}`);
                    $('.progress-bar').attr('aria-valuenow',archieved_percentage);
                    $('.progress-bar').css('width',`${archieved_percentage}%`);
                    $('#received').val(response.received);
                    $('#short_fall').val(response.short_fall);
                }
            });
        }

    </script>
@endpush
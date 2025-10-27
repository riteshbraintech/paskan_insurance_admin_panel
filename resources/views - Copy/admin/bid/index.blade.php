@extends('admin.layouts.app')
@section('content')
    @php
        $converted_bids = request()->query('converted_bids') ;
        $bidsFilter = request()->query('bidsFilter') ;
        $staff_id = request()->query('staffFilter');
        $manager_id = request()->query('managerFilter');
        $date_filter = request()->query('dateRange');
    @endphp

    @push('styles')
    @endpush
    <style>
        .input-group-text {
            align-items: left;
            padding: 0px;
            font-size: 13px;
            font-weight: 100;
            text-align: left;
        }
        .inactive {
            color: red; 
        }          
    </style>

    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">All Bids</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{route('admin.bid.list')}}">Bid</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Bid</li>
                </ol>
            </nav>
        </div>

        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('admin.bid.create') }}">
                    <a href="{{ route('admin.bid.create') }}" class="btn btn-primary">+ Add Bid</a>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-header py-3 px-1">
                <div class="row g-1">
                    @include('admin.elements.search')
                    
                    <div class="col-lg-2">
                        <select onchange="portalfilter(event);" class="form-select portal-filter portal" name="portal" id="portal">
                            <option selected="" value="">All Portal</option>
                            @forelse ($portals as $portal)
                                <option value="{{$portal->slug}}">{{$portal->name}}</option>
                            @empty
                                <option selected value="">Portal Not Available</option>
                            @endforelse
                        </select>
                    </div>
                    
                    <div class="col-lg-3 d-flex flex-row">
                        <button class="prev-month btn btn-outline-secondary px-1" style="height: 2.4rem;"><i class="fa fa-angle-left" aria-hidden='true'></i></button>

                        <input type="text" class="form-control btn btn-outline-secondary px-0" name="bid-date-filter" id="bid-date-filter"
                        value="" onchange="datefilter(event);" />
                        
                        <button class='next-month btn btn-outline-secondary px-1' style="height: 2.4rem;"><i class='fa fa-angle-right' aria-hidden='true'></i></button>
                    </div>

                    <input type="checkbox" id="disable_btn" name="disable_btn" onclick="disable(event);" class=" d-flex form-check-input fs-4" checked/>
                    
                    @if(admin()->user()->role_id == \App\Models\Role::MANAGER)
                        <div class="mx-1 col-lg-3">
                            <select onchange="staffilter(event);" class="form-select staff-filter" name="staff-filter" id="">
                                <option selected="" value="">All Staffs</option>
                                @forelse ($staffs_dd as $staff)
                                    @if ($staff['status'] == 'active' || ($staff['status'] == 'inactive' && $staff['check'] == 1))
                                        <option value="{{ $staff['id'] }}" class="{{ $staff['status'] == 'inactive' ? 'inactive' : '' }}">
                                            {{ $staff['name'] }}{{ $staff['status'] == 'inactive' && $staff['check'] == 1 ? ' -> Inactive' : '' }}
                                        </option>
                                    @endif
                                @empty
                                    <option selected="" disabled="" value="">Staffs Not Available</option>
                                @endforelse
                            </select>
                        </div>
                    @endif

                    @if(in_array(admin()->user()->role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN]))
                        <div class="mx-1 col-lg-3">
                            <select onchange="managerfilter(event);" class="form-select manager-filter" name="manager-filter" id="">
                                <option selected="" value="">All Managers</option>
                                @forelse ($managers as $manager)
                                    <option value="{{ $manager->id }}" >{{ $manager->name }}</option>
                                @empty
                                    <option selected="" disabled="" value="">Managers Not Available</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="col-lg-2">
                            <select onchange="staffilter(event);" class="form-select staff-filter" name="staff-filter" id="">
                                <option selected="" value="">All Staffs</option>
                                @forelse ($staffs_dd as $staff)
                                    @if ($staff['status'] == 'active' || ($staff['status'] == 'inactive' && $staff['check'] == 1))
                                        <option value="{{ $staff['id'] }}" class="{{ $staff['status'] == 'inactive' ? 'inactive' : '' }}">
                                            {{ $staff['name'] }}{{ $staff['status'] == 'inactive' && $staff['check'] == 1 ? '    (Inactive)' : '' }}
                                        </option>
                                    @endif
                                @empty
                                <option selected="" disabled="" value="">Staffs Not Available</option>
                                @endforelse
                            </select>
                        </div>
                    @endif

                    <div class="mx-1 col-lg-3">
                        <select onchange="bidfilter(event);" class="form-select bids-filter bids" name="bids" id="">
                            <option {{request()->has('bidsFilter') ? 'selected' : ''}} value="">All Bids </option>
                            <option value="1">Converted Bids</option>
                            <option value="0" {{request()->has('bidsFilter') ? '' : 'selected'}}>Non Converted Bids</option>
                        </select>
                    </div>

                    @include('admin.elements.perPage', ['datas' => $bids])
                </div>
            </div>

            <div class="card-body p-1">
                <div class="load-table-data">
                    @include('admin.bid.table')
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        var isInitialLoad = true;

        function disable(event){
            $date = $('#bid-date-filter').val();
            if (event.target.checked) {
                $('#bid-date-filter').prop('disabled', false); // If checked disable item 
                $('.prev-month').prop('disabled', false); // If checked disable item 
                $('.next-month').prop('disabled', false); // If checked disable item 
                $('#disable_btn').attr("checked",false);  
            } else {
                // $('#next_followup').val(null); // If checked disable item 
                $('#bid-date-filter').prop('disabled', true); // If checked enable item 
                $('.prev-month').prop('disabled', true); // If checked enable item 
                $('.next-month').prop('disabled', true); // If checked enable item 
                $('#disable_btn').attr("checked",true);                                    
            }
            loadData();  
        }; 

        function staffilter(event) {
            loadData();
        }

        function datefilter(event) {
            if (isInitialLoad) {
                isInitialLoad = false;
                return;
            }
            loadData();
        }

        function bidfilter(event) {
            loadData();
        }

        function portalfilter(event) {
            loadData();
        }

        function managerfilter(event) {
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
        }

        function loadData(){
            let dateRange = '';
            let portalFilter = '';
            let bidsFilter = '';
            let staffFilter = '';
            let managerFilter = '';
            let disabledBtn = '';

            portalFilter = $('.portal-filter').val();
            disabledBtn = $('#disable_btn').is(":checked");
            bidsFilter = $('.bids-filter').val();
            dateRange = $('#bid-date-filter').val();
            staffFilter = $('.staff-filter').val() == undefined ? '' : $('.staff-filter').val() ;
            managerFilter = $('.manager-filter').val() == undefined ? '' :  $('.manager-filter').val();

            if(disabledBtn === true){
                extraSearchItem =  "portalFilter="+portalFilter+"&"+"bidsFilter="+bidsFilter+'&'+'dateRange=' + dateRange+'&'+"staffFilter=" + staffFilter+'&'+"managerFilter=" + managerFilter + "&"+"disabledBtn="+disabledBtn;
            }else{
                extraSearchItem =  "portalFilter="+portalFilter+"&"+"bidsFilter="+bidsFilter+'&'+'dateRange=' + ""+'&'+"staffFilter=" + staffFilter+'&'+"managerFilter=" + managerFilter + "&"+"disabledBtn="+disabledBtn;
            }
            ajaxTableData();
        }

        // $('.manager-filter').on('change',function(event){    
        // });

        // var converted_bids = {{ Illuminate\Support\Js::from($converted_bids) }};
        // if(converted_bids != '' & converted_bids != undefined){
        //     document.getElementsByClassName('bids-filter')[0].value = converted_bids ;
        //     loadData("");
        // }

        var staff_id = {{ Illuminate\Support\Js::from($staff_id) }};
        if(staff_id != '' & staff_id != undefined){
            $('.staff-filter').val(staff_id);
        }

        var manager_id = {{ Illuminate\Support\Js::from($manager_id) }};
        if(manager_id != '' & manager_id != undefined){
            $('.manager-filter').val(manager_id);
        }

    </script>
    
    @if ($date_filter)

        @php
            $expDate =  explode("-", $date_filter);
        @endphp

        <script>
            $(function() {
                $('#bid-date-filter').daterangepicker({
                    startDate: {{ Illuminate\Support\Js::from($expDate[0]) }},
                    endDate: {{ Illuminate\Support\Js::from($expDate[1]) }},
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
        </script>
    @else
        <script>
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
        </script>
    @endif
@endpush

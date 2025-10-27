@extends('admin.layouts.app')
@push('styles')
@endpush
@section('content')

    @php
        $type = request()->query('status');
        $date_filter = request()->query('dateRange');
        $staff_id = is_null(request()->query('staffFilter')) ? '' : request()->query('staffFilter');
        $manager_id = is_null(request()->query('managerFilter')) ? '' : request()->query('managerFilter');
        $dateFilterEnabled = request()->query('disabledBtn');
        $role_id = admin()->user()->role_id;
    @endphp

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
        <div class="breadcrumb-title pe-3">All Leads</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.lead.list') }}">Lead</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Lead</li>
                </ol>
            </nav>
        </div>

        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('admin.lead.create') }}">
                    <a href="{{ route('admin.lead.create') }}" class="btn btn-primary">+ Add Lead</a>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-header py-3 px-0">
                <div class="row g-1">
                    @include('admin.elements.search')
                    
                    @if ($role_id == \App\Models\Role::MANAGER)
                        <div class="col-lg-2">
                            <select onchange="staffilter(event);" class="form-select staff-filter" name="staff-filter" id="">
                                <option selected="" value="">All Staffs</option>
                                @forelse ($staffs_dd as $staff)

                                    @if ($staff['status'] == 'active' || ($staff['status'] == 'inactive' && $staff['check'] == 1))
                                        <option value="{{ $staff['id'] }}" {{$staff_id == $staff['id'] ? 'selected' : ''}} class="{{ $staff['status'] == 'inactive' ? 'inactive' : '' }}">
                                            {{ $staff['name'] }}{{ $staff['status'] == 'inactive' && $staff['check'] == 1 ? '    (Inactive)' : '' }}
                                        </option>
                                    @endif
                                @empty
                                    <option selected="" disabled="" value="">Staffs Not Available</option>
                                @endforelse
                            </select>
                        </div>
                    @endif

                    @if (in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN]))
                        <div class="col-lg-3">
                            <select onchange="managerfilter(event);" class="form-select manager-filter" name="manager-filter" id="">
                                <option selected="" value="">All Managers</option>
                                @forelse ($managers as $manager)
                                    <option value="{{ $manager->id }}" {{$manager_id == $manager->id ? 'selected' : ''}}>{{ $manager->name }}</option>
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
                                        <option value="{{ $staff['id'] }}" {{$staff_id == $staff['id'] ? 'selected' : ''}} class="{{ $staff['status'] == 'inactive' ? 'inactive' : '' }}">
                                            {{ $staff['name'] }}{{ $staff['status'] == 'inactive' && $staff['check'] == 1 ? '    (Inactive)' : '' }}
                                        </option>
                                    @endif

                                @empty
                                    <option selected="" disabled="" value="">Staffs Not Available</option>
                                @endforelse
                            </select>
                        </div>
                    @endif

                    <div class="col-lg-3 d-flex flex-row">
                        <button class="prev-month btn btn-outline-secondary px-1" style="height: 2.4rem;">
                            <i class="fa fa-angle-left" aria-hidden='true' style="margin-top: -7px !important;"></i>
                        </button>

                        <input type="text" class="form-control date-range btn btn-outline-secondary px-0" name="lead-date-filter" id="lead-date-filter" value="" onchange="datefilter(event);" />

                        <button class='next-month btn btn-outline-secondary px-1' style="height: 2.4rem;">
                            <i class='fa fa-angle-right' aria-hidden='true' style="margin-top: -7px !important;"></i>
                        </button>
                    </div>

                    <input type="checkbox" id="disable_btn" name="disable_btn" onclick="disable(event);" class=" d-flex form-check-input fs-4" {{isset($dateFilterEnabled) ? ($dateFilterEnabled == true ? 'checked' : '') : 'checked'}}/>

                    <div class="col-lg-2">
                        <select onchange="portalfilter(event);" class="form-select portal-filter" name="portal" id="">
                            <option selected="" value="">All Portal</option>
                            @forelse ($portals as $portal)
                                <option value="{{$portal->slug}}">{{$portal->name}}</option>
                            @empty
                                <option selected value="">Portal Not Available</option>
                            @endforelse
                        </select>
                    </div>
                    
                    @include('admin.elements.perPage', ['datas' => $leads])

                    {{-- export csv --}}
                    @if (in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN])) 
                        <div class="col-lg-2">
                            <input type="button" class="form-control export-csv  fs-6 " name="export-csv" id="export-csv" value="Export CSV" onclick="exportCSV(event);" />
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-body p-1 mt-2">
                <button type="button" class="btn btn-outline-success px-6 radius-30 status-filter {{is_null($type) ? 'active' : ''}}" data-status="" id="filterbtn-show_all" onclick="statusfilter(event,'show_all','filterbtn-show_all');">
                    Show All
                </button>

                <button type="button" class="btn btn-outline-success px-6 radius-30 status-filter" data-status="is_invited" id="filterbtn-is_invited" onclick="invitedFilter(event)">Is Invited</button>

                @foreach (statusList() as $stKey => $status)
                    
                    <button type="button" class="btn btn-outline-success px-6 radius-30 status-filter {{$type == $stKey ? 'active' : ''}}" data-status="{{ $stKey }}" id="filterbtn-{{ $stKey }}" onclick="statusfilter(event, `{{ $stKey }}`,'filterbtn-{{ $stKey }}')">
                        {{ ucfirst($status) }}
                    </button>

                @endforeach

                <div class="load-table-data">
                    @include('admin.lead.table')
                </div>
            </div>
        </div>
    </div>

    {{-- status modal --}}
    <!-- Button trigger modal -->
    <button type="button" style="visibility: hidden;" id="statusModalOpenButton" class="btn btn-primary"
        data-bs-toggle="modal" data-bs-target="#statusModal">
        Launch demo modal
    </button>

    <!-- General Modal -->
    <div class="modal fade" id="generalModal" tabindex="-1" aria-labelledby="generalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generalModalLabel">Update Lead Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.lead.status-update') }}" method="POST">
                        @csrf
                        <input type="hidden" class="leadIdInput" name="leadIdInput" value="" />
                        <input type="hidden" class="leadIdStatus" name="leadIdStatus" value="" />
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Remark</label>
                            <textarea name="remark" class="form-control" placeholder="Enter remark"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal with Reason Dropdown -->
    <div class="modal fade" id="modalWithReasonDropdown" tabindex="-1" aria-labelledby="modalWithReasonDropdownLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalWithReasonDropdownLabel">Update Lead Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.lead.status-update') }}" method="POST">
                        @csrf
                        <input type="hidden" class="leadIdInput" name="leadIdInput" value="" />
                        <input type="hidden" class="leadIdStatus" name="leadIdStatus" value="" />
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Remark</label>
                            <textarea name="remark" class="form-control" placeholder="Enter remark"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="reasonDropdown" class="form-label">Reason</label>
                            <select name="reason" id="reasonDropdown" class="form-control">
                               
                                <option value="reason1">Reason 1</option>
                                <option value="reason2">Reason 2</option>
                               
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Remove region popup --}}
    <div id="clone-confirm-popup" class="d-none">
        @include('admin.lead.clone-confirm-popup')
    </div>
@endsection

@push('scripts')
    
    <script>
        var isInitialLoad = true;

        $(document).ready(function(){
            disabledBtn = $('#disable_btn').is(":checked");
            if(disabledBtn === false){
                $('#lead-date-filter').prop('disabled', true); // If checked enable item 
                $('.prev-month').prop('disabled', true); // If checked enable item 
                $('.next-month').prop('disabled', true);
            }else{
                $('#lead-date-filter').prop('disabled', false); // If checked disable item 
                $('.prev-month').prop('disabled', false); // If checked disable item 
                $('.next-month').prop('disabled', false); //
            }
        });

        function disable(event){
            if (event.target.checked) {
                $('#lead-date-filter').prop('disabled', false); // If checked disable item 
                $('.prev-month').prop('disabled', false); // If checked disable item 
                $('.next-month').prop('disabled', false); // If checked disable item 
                $('#disable_btn').attr("checked",false);  
                loadData();   
            } else {
                // $('#next_followup').val(null); // If checked disable item 
                $('#lead-date-filter').prop('disabled', true); // If checked enable item 
                $('.prev-month').prop('disabled', true); // If checked enable item 
                $('.next-month').prop('disabled', true); // If checked enable item 
                $('#disable_btn').attr("checked",true);   
                loadData();                                 
            }
        }; 

        function exportCSV(event) {
            window.location.href = "{{route('admin.lead.export-csv')}}";
        }

        function staffilter(event) {
            loadData(event, '', '');
        }

        function datefilter(event) {
            if (isInitialLoad) {
                isInitialLoad = false;
                return;
            }
            loadData(event, '', '');
        }

        function statusfilter(event, status = '', id = '') {
            loadData(event, status, id);
        }

        function portalfilter(event) {
            loadData(event, '', '');
        }

        function invitedFilter(event) {
            loadData(event, 'is_invited', 'filterbtn-is_invited');
        }

        function managerfilter(event) {
            $.ajax({
                url: "{{ route('admin.staff.staff-list') }}",
                type: 'get',
                data: {
                    id: event.target.value
                },
                dataType: 'json',
                success: function(response) {
                    let options = "<option value=''>All Staffs</option>";
                    $('.staff-filter').empty();
                    if (response.detail.length > 0) {
                        response.detail.forEach(function(value, key, array) {
                            options = options + `<option value="${value.id}">${value.name}</option>`;
                        });
                    } else {
                        options = options + `<option selected value="">Staffs not available</option>`;
                    }
                    $('.staff-filter').html(options);
                    loadData();
                }
            });
        }

        function loadData(event = "", status = '', id = '') {
            let statusId = '';
            let dateRange = '';
            let portalFilter = '';
            let staffFilter = '';
            let managerFilter = '';
            let disabledBtn = '';
            let isInvited = '';

            if (id != '') {
                $('.active').removeClass('active');
                $("#" + id).addClass('active');
            }

            disabledBtn = $('#disable_btn').is(":checked");
            statusId = $('.status-filter.active').attr('data-status');
            portalFilter = $('.portal-filter').val();
            staffFilter = $('.staff-filter').val() == undefined ? '' : $('.staff-filter').val();
            dateRange = $('#lead-date-filter').val();
            managerFilter = $('.manager-filter').val() == undefined ? '' : $('.manager-filter').val();

            if (status === 'is_invited') {
                isInvited = '1';
                statusId = '';
            }

            if(disabledBtn === true){
                extraSearchItem = "status=" + statusId + '&' + "portalFilter=" + portalFilter + '&' + 'dateRange=' + dateRange +
                '&' + "staffFilter=" + staffFilter + '&' + "managerFilter=" + managerFilter +  "&"+"disabledBtn="+disabledBtn;
            }else{
                extraSearchItem = "status=" + statusId + '&' + "portalFilter=" + portalFilter + '&' + 'dateRange=' +"" +
                '&' + "staffFilter=" + staffFilter + '&' + "managerFilter=" + managerFilter +  '&'+"disabledBtn="+disabledBtn+"&"+ "is_invited=" + isInvited;
            }

            if (status === 'is_invited') {
                extraSearchItem += "&is_invited=1";
            }
            
            ajaxTableData();
        }

        // $('.manager-filter').on('change', function(event) {
        // });

        var reasonModalStatuses = ['dead_lead', 'fake_lead', 'cancelled'];

        $(document).on('change', '.status-change', function() {
            let leadIdInput = $(this).attr('data-leadid');
            let leadIdStatus = $(this).val().toLowerCase();
            $(' .leadIdInput').val(leadIdInput);
            $('.leadIdStatus').val(leadIdStatus);
        
            
            if (reasonModalStatuses.includes(leadIdStatus)) {
                
                $('#modalWithReasonDropdown').modal('show');
            } else {
                
                $('#generalModal').modal('show');
            }
        }); 

        // var Itype = {{ Illuminate\Support\Js::from($type) }};
        // if (Itype != '' & Itype != undefined) {
        //     loadData(event = "", Itype, 'filterbtn-' + Itype);
        //     $('#filterbtn-show_all').removeClass('active');
        //     $(`#filterbtn-${Itype}`).addClass('active');
        // }

        // var staff_id = {{ Illuminate\Support\Js::from($staff_id) }};
        // if (staff_id != '' & staff_id != undefined) {
        //     $('.staff-filter').val(staff_id);
        // }

        // var manager_id = {{ Illuminate\Support\Js::from($manager_id) }};
        // if (manager_id != '' & manager_id != undefined) {
        //     $('.manager-filter').val(manager_id);
        // }
    </script>

    @if ($date_filter)

        @php
            $expDate =  explode("-", $date_filter);
        @endphp

        <script>
            $(function() {
                $('#lead-date-filter').daterangepicker({
                    startDate: {{ Illuminate\Support\Js::from($expDate[0]) }},
                    endDate: {{ Illuminate\Support\Js::from($expDate[1]) }},
                });

                $('.prev-month').on('click', function () {
                    let start = $('#lead-date-filter').data('daterangepicker').startDate;
                    let end = $('#lead-date-filter').data('daterangepicker').endDate;
                    
                    $('#lead-date-filter').daterangepicker({
                        startDate: start.subtract(1, 'month').startOf('month'),
                        endDate: end.subtract(1, 'month').endOf('month'),
                    });
                });

                $('.next-month').on('click', function () {
                    let start = $('#lead-date-filter').data('daterangepicker').startDate;
                    let end = $('#lead-date-filter').data('daterangepicker').endDate;
                    
                    $('#lead-date-filter').daterangepicker({
                        startDate: start.add(1, 'month').startOf('month'),
                        endDate: end.add(1, 'month').endOf('month'),
                    });
                });

            });
        </script>
    @else
        <script>
            $(function() {
                $('#lead-date-filter').daterangepicker({
                    startDate: moment().startOf('month'),
                    endDate: moment().endOf('month'),
                });

                $('.prev-month').on('click', function () {
                    let start = $('#lead-date-filter').data('daterangepicker').startDate;
                    let end = $('#lead-date-filter').data('daterangepicker').endDate;
                    
                    $('#lead-date-filter').daterangepicker({
                        startDate: start.subtract(1, 'month').startOf('month'),
                        endDate: end.subtract(1, 'month').endOf('month'),
                    });
                });

                $('.next-month').on('click', function () {
                    let start = $('#lead-date-filter').data('daterangepicker').startDate;
                    let end = $('#lead-date-filter').data('daterangepicker').endDate;
                    
                    $('#lead-date-filter').daterangepicker({
                        startDate: start.add(1, 'month').startOf('month'),
                        endDate: end.add(1, 'month').endOf('month'),
                    });
                });

            });
        </script>
    @endif
@endpush
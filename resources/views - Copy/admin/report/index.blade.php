@extends('admin.layouts.app')
@section('content')

    @php
        // $type = request()->query('type');
        // $user_id = request()->query('user_id');
        // $date_filter = request()->query('date');
        // $staff_id = request()->query('staff_id');
        // $manager_id = request()->query('manager_id');
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
    </style>

    @include('admin.components.FlashMessage')

    @php
        $role_id = admin()->user()->role_id;
    @endphp

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Leads Report</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.report.list') }}">Report</a></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-header py-3">
                <div class="row g-2">
                    @include('admin.elements.search')
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
                    
                    @if ($role_id == \App\Models\Role::MANAGER)
                        <div class="col-lg-2">
                            <select onchange="staffilter(event);" class="form-select staff-filter" name="staff-filter" id="">
                                <option selected="" value="">All Staffs</option>
                                @forelse ($staffs_dd as $staff)
                                    <option value="{{ $staff['id'] }}" > {{ $staff['name'] }} </option>
                                @empty
                                    <option selected="" disabled="" value="">Staffs Not Available</option>
                                @endforelse
                            </select>
                        </div>
                    @endif

                    @if (in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN]))
                        <div class="col-lg-2">
                            <select onchange="managerfilter(event);" class="form-select manager-filter" name="manager-filter" id="">
                                <option selected="" value="">All Managers</option>
                                @forelse ($managers as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                @empty
                                    <option selected="" disabled="" value="">Managers Not Available</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="col-lg-2">
                            <select onchange="staffilter(event);" class="form-select staff-filter" name="staff-filter" id="">
                                <option selected="" value="">All Staffs</option>
                                @forelse ($staffs_dd as $staff)
                                    <option value="{{ $staff['id'] }}" >{{ $staff['name'] }}</option>
                                @empty
                                    <option selected="" disabled="" value="">Staffs Not Available</option>
                                @endforelse
                            </select>
                        </div>

                    @endif

                    <div class="col-lg-4 d-flex flex-row">

                        <button class="prev-month btn btn-outline-secondary" style="height: 2.4rem;"><i class="fa fa-angle-left" aria-hidden='true'></i></button>

                        <input type="text" class="form-control date-range btn btn-outline-secondary" name="lead-date-filter" id="lead-date-filter" value="" onchange="datefilter(event);" />

                        <button class='next-month btn btn-outline-secondary' style="height: 2.4rem;"><i class='fa fa-angle-right' aria-hidden='true'></i></button>

                    </div>

                    <div class="col-lg-2">
                        <input type="button" class="form-control export-excel" name="export-excel" id="export-excel" value="Export Excel" onclick="exportExcel(event);" />
                    </div>
                    {{-- @include('admin.elements.perPage', ['datas' => $leads]) --}}
                </div>
            </div>

            <div class="card-body">
                <div class="load-table-data">
                    @include('admin.report.table')
                </div>
            </div>
        </div>
    </div>

    <!-- Button trigger modal -->
    <button type="button" style="visibility: hidden;" id="statusModalOpenButton" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal">Launch demo modal</button>

    <!-- Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Add Remark</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.lead.status-update') }}" method="POST">
                        @csrf
                        <input type="hidden" id="leadIdInput" name="leadIdInput" value="" />
                        <input type="hidden" id="leadIdStatus" name="leadIdStatus" value="" />
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

@endsection

@push('scripts')
    <script>
        var isInitialLoad = true;
        $(function() {
            $('#lead-date-filter').daterangepicker({
                startDate: moment().quarter(moment().quarter()).startOf('quarter'),
                endDate: moment().quarter(moment().quarter()).endOf('quarter'),
            });

            $('.prev-month').on('click', function () {
                let start = $('#lead-date-filter').data('daterangepicker').startDate;
                let end = $('#lead-date-filter').data('daterangepicker').endDate;
                
                $('#lead-date-filter').daterangepicker({
                    startDate: start.subtract(3, 'month').startOf('month'),
                    endDate: end.subtract(3, 'month').endOf('month'),
                });
            });

            $('.next-month').on('click', function () {
                let start = $('#lead-date-filter').data('daterangepicker').startDate;
                let end = $('#lead-date-filter').data('daterangepicker').endDate;
                
                $('#lead-date-filter').daterangepicker({
                    startDate: start.add(3, 'month').startOf('month'),
                    endDate: end.add(3, 'month').endOf('month'),
                });
            });
        });

        function exportExcel(event) {
            let dateRange = '';
            let portalFilter = '';
            let staffFilter = '';
            let managerFilter = '';
            // let user = {{ Illuminate\Support\Js::from($user) }};         php variable into js
            
            let totalSale = $(".total-sale").text();
            portalFilter = $('.portal-filter').val();
            staffFilter = $('.staff-filter').val() == undefined ? '' : $('.staff-filter').val();
            dateRange = $('#lead-date-filter').val();
            managerFilter = $('.manager-filter').val() == undefined ? '' : $('.manager-filter').val();
            
            let extraSearchItem = "portalFilter=" + portalFilter + '&' + 'dateRange=' + dateRange + '&' + "staffFilter=" + staffFilter + '&' + "managerFilter=" + managerFilter + '&' + "totalSale=" + totalSale ;

            let url = "{{route('admin.report.export-incentive-excel')}}" + "?" + extraSearchItem;
            window.location.href = url;
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

        function portalfilter(event) {
            loadData(event, '', '');
        }

        function managerfilter(event) {
            $.ajax({
                url: "{{ route('admin.staff.staff-list') }}",
                type: 'get',
                data: {id: event.target.value},
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
            let dateRange = '';
            let portalFilter = '';
            let staffFilter = '';
            let managerFilter = '';

            portalFilter = $('.portal-filter').val();
            staffFilter = $('.staff-filter').val() == undefined ? '' : $('.staff-filter').val();
            dateRange = $('#lead-date-filter').val();
            managerFilter = $('.manager-filter').val() == undefined ? '' : $('.manager-filter').val();

            extraSearchItem = "portalFilter=" + portalFilter + '&' + 'dateRange=' + dateRange + '&' + "staffFilter=" + staffFilter + '&' + "managerFilter=" + managerFilter ;
            ajaxTableData();
        }

        $(document).on('change', '.status-change', function() {
            let leadIdInput = $(this).attr('data-leadid');
            let leadIdStatus = $(this).val();
            $('#leadIdInput').val(leadIdInput);
            $('#leadIdStatus').val(leadIdStatus);
            $('#statusModalOpenButton').click();
        });

        $(document).on('change','.project_type',function(){
            let project_type = $(this).val();
            let id = $(this).attr('data-id');
            $.ajax({
                type:"post",
                url:"{{ route('admin.report.update.project_type') }}",
                data:{
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id:id,
                    project_type:project_type
                },
                success:function(response){
                    if(response.success){
                        alert(response.message);
                    }
                }
            });
        });

    </script>    
@endpush
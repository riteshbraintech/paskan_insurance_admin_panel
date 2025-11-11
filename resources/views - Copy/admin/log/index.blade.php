@extends('admin.layouts.app')
@section('content')

    @include('admin.components.FlashMessage')

    @push('styles')
    @endpush
    
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">All Logs</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{route('admin.log.list')}}">All Logs</a></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-header py-3">
                <div class="row g-2">
                    @include('admin.elements.search')
                    <div class="col-lg-3">
                        <select onchange="extrafilter()" class="form-select lead-filter" name="lead" id="">
                            <option value="">All Leads</option>
                            @if ($leads)
                                @forelse ($leads as $lead)
                                    <option {{(isset($_GET['leadFilter'])) ? ($lead->id == $_GET['leadFilter'] ? 'selected' : '') : ""}} value="{{$lead->id}}" >{{$lead->lead_id}}</option>
                                @empty
                                    <option selected disabled value="">No Logs Available</option>
                                @endforelse    
                            @endif
                        </select>
                    </div>
                    
                    {{-- date range picker --}}
                    <div class="col-lg-4 d-flex flex-row">
                        <button class="prev-month btn btn-outline-secondary" style="height: 2.4rem;"><i class="fa fa-angle-left" aria-hidden='true'></i></button>

                        <input type="text" class="form-control date-range btn btn-outline-secondary" name="logs-date-filter" id="logs-date-filter" value="" onchange="extrafilter(event);" />
                        
                        <button class='next-month btn btn-outline-secondary' style="height: 2.4rem;"><i class='fa fa-angle-right' aria-hidden='true'></i></button>
                    </div>

                    @include('admin.elements.perPage', ['datas' => $logs])
                </div>
            </div>

            <div class="card-body">
                <div class="load-table-data">
                    @include('admin.log.table')
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        var isInitialLoad = true;
        
        function extrafilter(){
            if (isInitialLoad) {
                isInitialLoad = false;
                return;
            }
            let dateRange = '';
            let leadFilter = '';
            
            leadFilter = $('.lead-filter').val();
            dateRange = $('#logs-date-filter').val();
            
            extraSearchItem =  'leadFilter='+leadFilter+'&'+'dateRange='+dateRange;
            ajaxTableData();
        }

        $(function() {
            $('#logs-date-filter').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
            });

            $('.prev-month').on('click', function () {
                let start = $('#logs-date-filter').data('daterangepicker').startDate;
                let end = $('#logs-date-filter').data('daterangepicker').endDate;
                
                $('#logs-date-filter').daterangepicker({
                    startDate: start.subtract(1, 'month').startOf('month'),
                    endDate: end.subtract(1, 'month').endOf('month'),
                });
            });

            $('.next-month').on('click', function () {
                let start = $('#logs-date-filter').data('daterangepicker').startDate;
                let end = $('#logs-date-filter').data('daterangepicker').endDate;
                
                $('#logs-date-filter').daterangepicker({
                    startDate: start.add(1, 'month').startOf('month'),
                    endDate: end.add(1, 'month').endOf('month'),
                });
            });

        });
    </script>
@endpush

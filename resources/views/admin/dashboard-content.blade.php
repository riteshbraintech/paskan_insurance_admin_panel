@php
    $bid_count = $bid->count();
    $lead_count = $lead->whereNotIn('status', ['fake_lead','cancelled'])->count();
    $invited_count = $lead->whereNotIn('status', ['fake_lead','cancelled'])->where('is_invited', 1)->count();
    $invited_open_count = $lead->where('status', 'open')->where('is_invited', 1)->count();
    $not_invited_count = $lead->whereNotIn('status', ['fake_lead','cancelled'])->where('is_invited', 0)->count();
    
    $lead_percentage = 0;
    if($bid_count && $lead_count){
        $lead_percentage = ((int)($lead_count- $invited_open_count) / (int)$bid_count)*100;
        $lead_percentage > 100 ? 100 : $lead_percentage;
    }
@endphp

<style>
    .table-wrapper {
    max-height: 370px;   /* control height */
    overflow-y: auto;    /* enable vertical scroll */
}

.table thead th {
    position: sticky;
    top: 0;
    background: #fff;    /* keep background so rows don’t bleed under */
    z-index: 2;          /* keep above rows */
}
</style>

<div class="row">
    <div class="col">
        <div class="card overflow-hidden radius-10" style="height:142px;">
            <div class="card-body">
                <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                    <div class="w-100">
                        <a href="javascript:void(0);" onclick="redirect('bid');" class="hover" style="cursor: default;">
                            <p style="cursor: pointer;">Total Bid</p>
                            <h4 class="" id="example"><span style="cursor: pointer;" class="total_bids" onmouseover="showHiddenDiv(event)" onmouseout="hideHiddenDiv(event)">{{ $bid_count }}</span></h4>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body" style="display:none;top:-7.5rem;padding:5px;width:12rem;height:auto;position:absolute;left:3rem;background-color:rgb(250, 246, 246);border-radius:5px;z-index:999;" id="hiddenDivBid">
                    <div class="">
                        @forelse ($staff_bid_list as $key => $val)
                            <p style="margin-bottom:2px;font-weight:500">{{ $key." - ".$val['bid'] }}</p>
                        @empty
                            <p>Data Not Found !!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>     
    </div>
   
    <div class="col">
        <div class="card overflow-hidden radius-10" style="height:142px;">
            <div class="card-body">
                <a href="javascript:void(0);" onclick="redirect('');" style="cursor: default;">
                    <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                        <div class="w-100">
                            <p style="cursor: pointer;">Total Lead</p>

                            <h4 class=" text-{{$lead_percentage < 8 ? 'danger' :( ($lead_percentage >= 8 && $lead_percentage <= 12) ? 'warning' : 'success')}}" ><span style="cursor: pointer;" class="total_leads" onmouseover="showHiddenDiv(event)" onmouseout="hideHiddenDiv(event)">{{ $lead_count - $invited_open_count }}</span> ({{round($lead_percentage, 2)}}%)</h4>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body" style="display:none;top:-7.5rem;padding:5px;width:13rem;height:auto;position:absolute;left:3rem;background-color:rgb(250, 246, 246);border-radius:5px;z-index:999;" id="hiddenDivLead">
                    <div class="">

                        <p style="margin-bottom: 2px;font-weight:500">Invited: {{ $invited_count }}</p>
                        <p style="margin-bottom: 2px;font-weight:500">Lead: {{ $not_invited_count }}</p>
                        
                        @forelse ($staff_lead_list as $key => $val)
                            <p style="margin-bottom: 2px;font-weight:500" class="text-{{$val['percentage'] < 8 ? 'danger' :( ($val['percentage'] >= 8 && $val['percentage'] <= 12) ? 'warning' : 'success')}}">{{ $key." - ".$val['lead_count']." (".$val['percentage']."%)" }}</p>  
                        @empty
                            <p>Data Not Found !!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card overflow-hidden radius-10" style="height:142px;">
            <div class="card-body">
                <a href="javascript:void(0);">
                    <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                        <div class="w-100">
                            <p>Hot Lead</p>
                            <div class="d-flex flex-row">
                                <a href="javascript:void(0);" onclick="redirect('hot_lead');"><h4 class="">{{ $hot_lead->count() }}</h4></a>
                                <p href="javascript:void(0);" class="d-flex mx-2 fs-4"><strong>/</strong></p>
                                <a href="javascript:void(0);" onclick="redirect('hot_lead','off');"><h4 class="">{{ $total_hot_lead }}</h4></a>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card overflow-hidden radius-10" style="height:142px;">
            <div class="card-body">
                <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                    <div class="w-100">
                        
                        @php
                            $hourlyCount = $fixedCount = $totalAwarded = $bidBudgetSum = 0;
                            $hourlyBidBudget = [];
                            foreach ($awardeds as $awarded) {
                                $totalAwarded++;
                                if ($awarded->project_type == 'hourly') {
                                    $hourlyCount++;
                                    array_push($hourlyBidBudget, $awarded->bid_budget);
                                } else {
                                    $fixedCount++;
                                    $bidBudgetSum += $awarded->bid_budget;
                                }
                            }
                        @endphp

                        <a href="javascript:void(0);" onclick="redirect('awarded');">
                            <p class="">Awarded</p>
                            <h4 class="">{{ $totalAwarded }}</h4>
                            <p class="" style="font-size: 12px; margin-bottom:0px;">Fixed<strong>({{ $fixedCount }})</strong></p>
                            <p class="" style="font-size: 12px; margin-bottom:0px;">Hourly<strong>({{ $hourlyCount }})</strong>
                                {{-- <strong>({{ implode(',', $hourlyBidBudget) }})</strong> --}}
                            </p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card overflow-hidden radius-10" style="height:142px;">
            <div class="card-body">
                <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                    <div class="w-100">
                        @php
                            $totalConnects = 0;
                            $temp = 0.0;
                            foreach ($bid as $val){
                                $totalConnects = $totalConnects + $val->connects_needed;
                            }
                            if(admin()->user()->role_id == "manager" && $totalConnects > 0){
                                $temp = ($totalConnects / 300) * 45;
                            }
                        @endphp
                        <p>Total Connects</p>
                        <h4 class="">{{ $totalConnects }} {{admin()->user()->role_id == "manager" ? " ( $ ".$temp." )" : ''}}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card radius-10">
            @include('admin.calender')
        </div>
    </div>

    <div class="col-md-7">
        <div class="card radius-10" style="height: 95%;padding: 20px;">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="mb-2">Today/Missed Follow Up Leads</h5>

                    @if (!blank($todayFollowUpLeads))
                        <div class="table-wrapper">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Lead Id</th>
                                        <th scope="col" class="text-center">FollowUp Date</th>
                                        <th scope="col" class="text-center">Portal</th>
                                        @if (admin()->user()->role_id != \App\Models\Role::STAFF)
                                            <th scope="col" class="text-center">Staff Name</th>
                                        @endif
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($todayFollowUpLeads as $item)
                                        @php
                                            $showDanger = false;
                                            if (strtotime($item->next_followup) < strtotime(date('Y-m-d'))) {
                                                $showDanger = true;
                                            }
                                        @endphp
                                        <tr class="{{ $showDanger ? 'table-danger' : '' }}">
                                            <td>
                                                <a href="{{ route('admin.lead.view', ['id' => $item->id]) }}" rel="facebox" class="text-primary" title="{{isset($item->client->client_name) ? ($item->job_title." (".$item->client->client_name.")") : $item->job_title}}">{{ $item->lead_id }}</a>
                                            </td>
                                            
                                            <td class="text-center">{{ date('d M Y', strtotime($item->next_followup)) }}</td>

                                            <td class="text-center">
                                                <a href="{{ $item->job_link }}" target="_BLANK" class="text-capitalize"> {{ $item->portal }} </a>
                                            </td>
                                            
                                            @if (admin()->user()->role_id != \App\Models\Role::STAFF)
                                                <td class="text-center">{{ $item->admin->name ?? '-' }}</td>
                                            @endif

                                            <td class="text-center">
                                                <a href="{{ route('admin.lead.view', ['id' => $item->id]) }}" rel="facebox" class="text-success" title="View">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">No records available !</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="notFound">
                            <p>Not Available !</p>
                        </div>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
    
    {{-- // line graph --}}
    <div class="col-md-12">
        <div class="card radius-10 graph-container">
            @include('admin.graph', ['record' => $record,'month_dates' => $month_dates,'awarded_per_month' => $awarded_per_month,'graph_filter' => $graph_filter])
        </div>
    </div>

</div>

{{-- pie chart --}}
<div class="row">

    <div class="col-md-12">
        <div class="card radius-10">
            @include('admin.piechart',['staff_bid_list' => $staff_bid_list])
        </div>
    </div>

</div>

@push('scripts')

    <script>

        function showHiddenDiv(event){
            if(event.target.classList.contains('total_bids')){
                document.getElementById('hiddenDivBid').style.display = 'block';
            }else if(event.target.classList.contains('total_leads')){
                document.getElementById('hiddenDivLead').style.display = 'block';
            }
        }
        
        function hideHiddenDiv(event){
           if(event.target.classList.contains('total_bids')){
                document.getElementById('hiddenDivBid').style.display = 'none';
           }else if(event.target.classList.contains('total_leads')){
                document.getElementById('hiddenDivLead').style.display = 'none';
           }
        }

        function redirect(paramType = "", dateFilterEnabled = true) {
            let bidsFilter = "";
            let staff_id = "";
            let manager_id = "";
            staff_id = $('.staff-filter').val() != undefined ? $('.staff-filter').val() : '';
            manager_id = $('.manager-filter').val() != undefined ? $('.manager-filter').val() : '';
            date = $('.date-filter').val();

            if (paramType === 'bid') {
                window.location.href = "{{ route('admin.bid.list') }}?staffFilter=" + staff_id + '&' + "dateRange=" + date + '&' + "managerFilter=" + manager_id + '&' + "bidsFilter="+bidsFilter;
            } else {
                window.location.href = "{{ route('admin.lead.list') }}?status=" + paramType + '&' + "staffFilter=" + staff_id + '&' + "managerFilter=" + manager_id + '&' + "dateRange=" + date + '&' + "disabledBtn=" + dateFilterEnabled;
            }
        }
    
    </script>
@endpush

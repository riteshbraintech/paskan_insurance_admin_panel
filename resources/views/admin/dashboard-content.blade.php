@php

    $cmspage_count = $cmspages->count();
    $category_count = $categories->count();
    $user_count = $users->count();
    $banner_count = $banner->count();

    $bid_count = $bid->count();
    $lead_count = $lead->whereNotIn('status', ['fake_lead', 'cancelled'])->count();
    $invited_count = $lead
        ->whereNotIn('status', ['fake_lead', 'cancelled'])
        ->where('is_invited', 1)
        ->count();
    $invited_open_count = $lead->where('status', 'open')->where('is_invited', 1)->count();
    $not_invited_count = $lead
        ->whereNotIn('status', ['fake_lead', 'cancelled'])
        ->where('is_invited', 0)
        ->count();

    $lead_percentage = 0;
    if ($bid_count && $lead_count) {
        $lead_percentage = ((int) ($lead_count - $invited_open_count) / (int) $bid_count) * 100;
        $lead_percentage > 100 ? 100 : $lead_percentage;
    }
@endphp

{{-- <style>
    .user-wrapper {
        font-family: "Segoe UI", Arial, sans-serif;
        width: 70%;
    }

    .user-title {
        color: #000000;
        text-align: center;
        padding: 15px 20px;
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        border-radius: 8px 8px 0 0;
    }

    .user-table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 0 0 8px 8px;
        overflow: hidden;
        box-shadow: 0px 3px 12px rgba(0, 0, 0, 0.15);
    }

    .user-table thead {
        background-color: #0a0c35;
        color: white;
        font-size: 13px;
        text-transform: uppercase;
    }

    .user-table th,
    .user-table td {
        padding: 12px;
        text-align: left;
    }

    .user-table tbody tr {
        border-bottom: 1px solid #e6e6e6;
        transition: background 0.3s ease;
    }

    .user-table tbody tr:hover {
        background-color: #f5f5f5;
    }
</style> --}}

<div class="row">
    <div class="col">
        <div class="card overflow-hidden radius-10" style="height:142px;">
            <div class="card-body">
                <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                    <div class="w-100">
                        <a href="javascript:void(0);" onclick="redirect('cmspage');" class="hover"
                            style="cursor: default;">
                            <p style="cursor: pointer;">Total CMSPage</p>
                            <h4 class="" id="example"><span style="cursor: pointer;" class="total_bids"
                                    onmouseover="showHiddenDiv(event)"
                                    onmouseout="hideHiddenDiv(event)">{{ $cmspage_count }}</span></h4>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body"
                    style="display:none;top:-7.5rem;padding:5px;width:12rem;height:auto;position:absolute;left:3rem;background-color:rgb(250, 246, 246);border-radius:5px;z-index:999;"
                    id="hiddenDivBid">
                    <div class="">
                        @forelse ($staff_bid_list as $key => $val)
                            <p style="margin-bottom:2px;font-weight:500">{{ $key . ' - ' . $val['bid'] }}</p>
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
                <a href="javascript:void(0);" onclick="redirect('category');" style="cursor: default;">
                    <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                        <div class="w-100">
                            <p style="cursor: pointer;">Total Category</p>

                            {{-- <h4
                                class=" text-{{ $lead_percentage < 8 ? 'danger' : ($lead_percentage >= 8 && $lead_percentage <= 12 ? 'warning' : 'success') }}">
                                <span style="cursor: pointer;" class="total_leads" onmouseover="showHiddenDiv(event)"
                                    onmouseout="hideHiddenDiv(event)">{{ $lead_count - $invited_open_count }}</span>
                                ({{ round($lead_percentage, 2) }}%)</h4> --}}
                            <h4 class="" id="example"><span style="cursor: pointer;" class="total_bids"
                                    onmouseover="showHiddenDiv(event)"
                                    onmouseout="hideHiddenDiv(event)">{{ $category_count }}</span></h4>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body"
                    style="display:none;top:-7.5rem;padding:5px;width:13rem;height:auto;position:absolute;left:3rem;background-color:rgb(250, 246, 246);border-radius:5px;z-index:999;"
                    id="hiddenDivLead">
                    <div class="">

                        <p style="margin-bottom: 2px;font-weight:500">Invited: {{ $invited_count }}</p>
                        <p style="margin-bottom: 2px;font-weight:500">Lead: {{ $not_invited_count }}</p>

                        @forelse ($staff_lead_list as $key => $val)
                            <p style="margin-bottom: 2px;font-weight:500"
                                class="text-{{ $val['percentage'] < 8 ? 'danger' : ($val['percentage'] >= 8 && $val['percentage'] <= 12 ? 'warning' : 'success') }}">
                                {{ $key . ' - ' . $val['lead_count'] . ' (' . $val['percentage'] . '%)' }}</p>
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
                <a href="javascript:void(0);" onclick="redirect('user');" style="cursor: default;">
                    <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                        <div class="w-100">
                            <p style="cursor: pointer;">Total User</p>

                            {{-- <h4
                                class=" text-{{ $lead_percentage < 8 ? 'danger' : ($lead_percentage >= 8 && $lead_percentage <= 12 ? 'warning' : 'success') }}">
                                <span style="cursor: pointer;" class="total_leads" onmouseover="showHiddenDiv(event)"
                                    onmouseout="hideHiddenDiv(event)">{{ $lead_count - $invited_open_count }}</span>
                                ({{ round($lead_percentage, 2) }}%)</h4> --}}
                            <h4 class="" id="example"><span style="cursor: pointer;" class="total_bids"
                                    onmouseover="showHiddenDiv(event)"
                                    onmouseout="hideHiddenDiv(event)">{{ $user_count }}</span></h4>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body"
                    style="display:none;top:-7.5rem;padding:5px;width:13rem;height:auto;position:absolute;left:3rem;background-color:rgb(250, 246, 246);border-radius:5px;z-index:999;"
                    id="hiddenDivLead">
                    <div class="">

                        <p style="margin-bottom: 2px;font-weight:500">Invited: {{ $invited_count }}</p>
                        <p style="margin-bottom: 2px;font-weight:500">Lead: {{ $not_invited_count }}</p>

                        @forelse ($staff_lead_list as $key => $val)
                            <p style="margin-bottom: 2px;font-weight:500"
                                class="text-{{ $val['percentage'] < 8 ? 'danger' : ($val['percentage'] >= 8 && $val['percentage'] <= 12 ? 'warning' : 'success') }}">
                                {{ $key . ' - ' . $val['lead_count'] . ' (' . $val['percentage'] . '%)' }}</p>
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
                <a href="javascript:void(0);" onclick="redirect('banner');" style="cursor: default;">
                    <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                        <div class="w-100">
                            <p style="cursor: pointer;">Total Banner</p>

                            {{-- <h4
                                class=" text-{{ $lead_percentage < 8 ? 'danger' : ($lead_percentage >= 8 && $lead_percentage <= 12 ? 'warning' : 'success') }}">
                                <span style="cursor: pointer;" class="total_leads" onmouseover="showHiddenDiv(event)"
                                    onmouseout="hideHiddenDiv(event)">{{ $lead_count - $invited_open_count }}</span>
                                ({{ round($lead_percentage, 2) }}%)</h4> --}}
                            <h4 class="" id="example"><span style="cursor: pointer;" class="total_bids"
                                    onmouseover="showHiddenDiv(event)"
                                    onmouseout="hideHiddenDiv(event)">{{ $banner_count }}</span></h4>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body"
                    style="display:none;top:-7.5rem;padding:5px;width:13rem;height:auto;position:absolute;left:3rem;background-color:rgb(250, 246, 246);border-radius:5px;z-index:999;"
                    id="hiddenDivLead">
                    <div class="">

                        <p style="margin-bottom: 2px;font-weight:500">Invited: {{ $invited_count }}</p>
                        <p style="margin-bottom: 2px;font-weight:500">Lead: {{ $not_invited_count }}</p>

                        @forelse ($staff_lead_list as $key => $val)
                            <p style="margin-bottom: 2px;font-weight:500"
                                class="text-{{ $val['percentage'] < 8 ? 'danger' : ($val['percentage'] >= 8 && $val['percentage'] <= 12 ? 'warning' : 'success') }}">
                                {{ $key . ' - ' . $val['lead_count'] . ' (' . $val['percentage'] . '%)' }}</p>
                        @empty
                            <p>Data Not Found !!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="width: 100%;">
        <div style="display: flex; gap: 20px; align-items: flex-start;">

            <!-- Left Table -->
            <div style="width: 50%;">
                <h4 class="user-title" style="text-align: center">Latest Users</h4>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Registered Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($latest_contacts as $index => $contact)
                            <tr>
                                <td>{{ $contact->fullname }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Right Table: Latest Enquiry -->
            <div style="width: 50%;">
                <h4 class="user-title" style="text-align: center">Latest Enquiry</h4>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Fields</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentEnquiries as $enquiry)
                            <tr>
                                <td>{{ $enquiry->user->name ?? '' }}</td>
                                <td>{{ $enquiry->category->title ?? '' }}</td>
                                <td>{{ $enquiry->status }}</td>
                                <td>
                                    {{-- <button class="btn btn-sm " data-bs-toggle="modal"
                                        data-bs-target="#detailsModal{{ $enquiry->id }}">
                                        <i class="fa fa-eye"></i>
                                    </button> --}}
                                    <button class="btn btn-sm p-0" style="border:none; background:none;"
                                        data-bs-toggle="modal" data-bs-target="#detailsModal{{ $enquiry->id }}">
                                        <i class="fa fa-eye text-primary" style="font-size:16px;"></i>
                                    </button>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- All Modals Placed Outside Table -->
                @foreach ($recentEnquiries as $enquiry)
                    <div class="modal fade" id="detailsModal{{ $enquiry->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Enquiry Details - {{ $enquiry->user->name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <p><strong>Category:</strong> {{ $enquiry->category->title ?? '' }}</p>
                                    <p><strong>Status:</strong> {{ $enquiry->status }}</p>
                                    <p><strong>Time:</strong> {{ $enquiry->enquery_time }}</p>
                                    <hr>

                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Field Name</th>
                                                <th>Field Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($enquiry->fillups->isEmpty())
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted">
                                                        No Data Found
                                                    </td>
                                                </tr>
                                            @else
                                                @foreach ($enquiry->fillups as $fillup)
                                                    <tr>
                                                        <td>{{ $fillup->form_field_name }}</td>
                                                        <td>{{ $fillup->form_field_value }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>




    {{-- <div class="col">
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
                            <p class="" style="font-size: 12px; margin-bottom:0px;">
                                Fixed<strong>({{ $fixedCount }})</strong></p>
                            <p class="" style="font-size: 12px; margin-bottom:0px;">
                                Hourly<strong>({{ $hourlyCount }})</strong>
                                <strong>({{ implode(',', $hourlyBidBudget) }})</strong>
                            </p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- <div class="col">
        <div class="card overflow-hidden radius-10" style="height:142px;">
            <div class="card-body">
                <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                    <div class="w-100">
                        @php
                            $totalConnects = 0;
                            $temp = 0.0;
                            foreach ($bid as $val) {
                                $totalConnects = $totalConnects + $val->connects_needed;
                            }
                            if (admin()->user()->role_id == 'manager' && $totalConnects > 0) {
                                $temp = ($totalConnects / 300) * 45;
                            }
                        @endphp
                        <p>Total Connects</p>
                        <h4 class="">{{ $totalConnects }}
                            {{ admin()->user()->role_id == 'manager' ? " ( $ " . $temp . ' )' : '' }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>

{{-- <div class="row">
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
                                                <a href="{{ route('admin.lead.view', ['id' => $item->id]) }}"
                                                    rel="facebox" class="text-primary"
                                                    title="{{ isset($item->client->client_name) ? $item->job_title . ' (' . $item->client->client_name . ')' : $item->job_title }}">{{ $item->lead_id }}</a>
                                            </td>

                                            <td class="text-center">
                                                {{ date('d M Y', strtotime($item->next_followup)) }}</td>

                                            <td class="text-center">
                                                <a href="{{ $item->job_link }}" target="_BLANK"
                                                    class="text-capitalize"> {{ $item->portal }} </a>
                                            </td>

                                            @if (admin()->user()->role_id != \App\Models\Role::STAFF)
                                                <td class="text-center">{{ $item->admin->name ?? '-' }}</td>
                                            @endif

                                            <td class="text-center">
                                                <a href="{{ route('admin.lead.view', ['id' => $item->id]) }}"
                                                    rel="facebox" class="text-success" title="View">
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

    <div class="col-md-12">
        <div class="card radius-10 graph-container">
            @include('admin.graph', [
                'record' => $record,
                'month_dates' => $month_dates,
                'awarded_per_month' => $awarded_per_month,
                'graph_filter' => $graph_filter,
            ])
        </div>
    </div>

</div> --}}

{{-- pie chart --}}
{{-- <div class="row">
    <div class="col-md-12">
        <div class="card radius-10">
            @include('admin.piechart', ['staff_bid_list' => $staff_bid_list])
        </div>
    </div>
</div> --}}

@push('scripts')
    <script>
        // function showHiddenDiv(event) {
        //     if (event.target.classList.contains('total_bids')) {
        //         document.getElementById('hiddenDivBid').style.display = 'block';
        //     } else if (event.target.classList.contains('total_leads')) {
        //         document.getElementById('hiddenDivLead').style.display = 'block';
        //     }
        // }

        // function hideHiddenDiv(event) {
        //     if (event.target.classList.contains('total_bids')) {
        //         document.getElementById('hiddenDivBid').style.display = 'none';
        //     } else if (event.target.classList.contains('total_leads')) {
        //         document.getElementById('hiddenDivLead').style.display = 'none';
        //     }
        // }

        function redirect(paramType = "", dateFilterEnabled = true) {
            let bidsFilter = "";
            let staff_id = "";
            let manager_id = "";
            staff_id = $('.staff-filter').val() != undefined ? $('.staff-filter').val() : '';
            manager_id = $('.manager-filter').val() != undefined ? $('.manager-filter').val() : '';
            date = $('.date-filter').val();

            if (paramType === 'cmspage') {
                window.location.href = "{{ route('admin.cmspage.index') }}?staffFilter=" + staff_id + '&' + "dateRange=" +
                    date + '&' + "managerFilter=" + manager_id + '&' + "bidsFilter=" + bidsFilter;
            } else if (paramType === 'category') {
                window.location.href = "{{ route('admin.categories.index') }}?status=" + paramType + '&' + "staffFilter=" +
                    staff_id + '&' + "managerFilter=" + manager_id + '&' + "dateRange=" + date + '&' + "disabledBtn=" +
                    dateFilterEnabled;
            } else if (paramType === 'user') {
                window.location.href = "{{ route('admin.user.index') }}"
            } else if (paramType === 'banner') {
                window.location.href = "{{ route('admin.banner.index') }}"
            }
        }
    </script>
@endpush

@php
    // $i = $bids->firstItem();
@endphp

<div class="row d-flex mt-2">
    <div class="col-md-2"><strong>Total Records:&nbsp;</strong>{{$total_record}}</div>
    <div class="col-md-3"><strong class="ml-2">Total Connects:&nbsp;</strong>{{$connects_sum}}</div>
</div>

<div class="table-responsive mt-3">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                {{-- <td >#</td> --}}
                <td class="no-wrap text-center">@sortablelink('bid_date','Bid Date')</td>
                <td class="no-wrap text-center">@sortablelink('created_at','Bid Created')</td>
                <td class="no-wrap text-center">User Name</td>
                <td class="no-wrap text-center">Job Title</td>
                <td class="no-wrap text-center">@sortablelink('portal','Portal')</td>
                <td class="no-wrap text-center">@sortablelink('project_type', 'Type')</td>
                <td class="no-wrap text-center">@sortablelink('bid_quote','Bid Quote')</td>
                <td class="no-wrap text-center">@sortablelink('connects_needed','Connects')</td>
                <td class="no-wrap text-center">Lead ID</td>
                <td class="no-wrap text-center">Status</td>
                <td class="no-wrap text-center" width="100px">Action</td>
            </tr>
        </thead>
        <tbody>
            @forelse ($bids as $key => $item)
                
                @php $client_name = is_null($item->client) ? '' : ($item->client->client_name ?? '') @endphp

                <tr class="{{($item->is_test == 1 && admin()->user()->role_id == \App\Models\Role::SUPERADMIN) ? 'table-warning' : ''}}">
                    {{-- <td>{{ $i }}</td> --}}
                    <td class="no-wrap text-center" title="{{$item->client->client_name ?? 'Client not Available'}}">{{ date('d M Y',strtotime($item->bid_date)) }}</td>
                    <td class="no-wrap text-center">{{ date('d M Y',strtotime($item->created_at)) }}</td>
                    <td class="no-wrap text-center">{{ $item->user_name }}</td>
                    <td class="text-center">{{ $item->job_title }}{{$client_name ? " (".$client_name.")" : ''}}</td>
                    <td class="no-wrap text-center"><a href="{{ $item->job_link }}" target="_blank" class="text-success" title="Visit Job">{{ $item->portal }}</a></td>
                    <td class="no-wrap text-center">{{ $item->project_type }}</td>
                    <td class="no-wrap text-center">{{ $item->bid_quote }}</td>
                    <td class="no-wrap text-center">{{ $item->connects_needed }}</td>
                    
                    <td class="no-wrap text-center">
                        <a href="{{ route('admin.lead.view', ['id' => $item->lead->id ?? '']) }}" class="text-success" title="{{$item->client->client_name ?? 'Client Not Available'}}" rel="facebox">{{ $item->lead->lead_id ?? '' }}</a>
                    </td>
                    <td class="no-wrap text-center"> 
                        <span class="badge badge-{{ $item->status == 'active' ? 'success' : 'danger' }} status-{{$item->id}}" data-value="{{$item->status}}" style="cursor:default;">{{ ucfirst($item->status) }}</span>
                    </td>
                    <td class="no-wrap text-center">
                        @if(!$item->is_lead_converted)
                            <div class="table-actions d-flex align-items-center justify-content-center gap-3">
                                <a href="{{ route('admin.bid.edit', ['id' => $item->id]) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="fa-solid fa-pen-to-square text-warning fs-5"></i></a>

                                @if (in_array(admin()->user()->role_id,[\App\Models\Role::SUPERADMIN,\App\Models\Role::ADMIN,\App\Models\Role::MANAGER]))
                                    <a href="{{ route('admin.bid.delete', ['id' => $item->id]) }}" class="text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i
                                    class="fa-solid fa-trash text-danger fs-5"></i></a>
                                @endif

                                @if ($item->status == "active")
                                    <a href="{{ route('admin.lead.create', ['id' => $item->id]) }}" class="text-success"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Convert to Lead"><i class="fa-solid fa-circle-arrow-right fs-5"></i></a>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
                @php
                    // $i++;
                @endphp
            @empty
                <tr>
                    <td colspan="11" class="text-center">
                        No Record Found !
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
{{-- pagination with filter --}}
@include('admin.elements.filter-with-pagi', ['data' => $bids])

@php
    // $i = $leads->firstItem();
@endphp

<div class="row d-flex mt-2">
    <div class="col-md-2"><strong>Total Records:&nbsp;</strong>{{$total_record}}</div>
    <div class="col-md-2"><strong class="ml-2">Total Connects:&nbsp;</strong>{{$connects_sum}}</div>
</div>

<div class="table-responsive mt-3">
    <table class="table align-middle" style="background-color:#8e8e8" id="datatable">
        <thead class="table-secondary">
            <tr>
                {{-- <td class="no-wrap text-center">#</td> --}}
                @if ( !in_array(admin()->user()->role_id, [\App\Models\Role::STAFF]))
                    <td class="no-wrap">@sortablelink('lead_id','Lead ID')</td>
                @endif
                <td class="no-wrap">@sortablelink('bid_date','Bid Date')</td>
                <td class="no-wrap">@sortablelink('next_followup','Followup')</td>
                <td class="no-wrap">@sortablelink('created_at','Lead Created')</td>
                <td class="no-wrap">User Name</td>
                <td class="text-wrap">Job Title</td>
                <td class="no-wrap">Is Invited</td>
                <td class="no-wrap">@sortablelink('portal','Portal')</td>
                <td class="no-wrap">@sortablelink('project_type','Type')</td>
                <td class="no-wrap">Status</td>
                <td class="no-wrap" width="100px">Action</td>
            </tr>
        </thead>
        <tbody>
            @forelse ($leads as $key => $item)
                
                @php $client_name = is_null($item->client) ? '' : ($item->client->client_name ?? '') @endphp

                <tr class="{{($item->is_test == 1 && admin()->user()->role_id == 'superadmin') ? 'table-warning' : ''}}">
                    {{-- <td class="no-wrap">{{ $i }}</td> --}}
                    @if ( in_array(admin()->user()->role_id, [\App\Models\Role::MANAGER]))
                        <td class="no-wrap text-success" style="cursor:pointer;" title="{{$item->client->client_name ?? 'Client Not Available'}}">
                            @if ($item->lead_id!=NULL)
                                <a href="{{ route('admin.lead.viewOnly', ['id' => $item->id]) }}" rel="facebox" class="text-success" title="View"> {{ $item->lead_id }}</a>
                            @endif
                        </td>
                    @endif

                    @if (in_array(admin()->user()->role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN]))
                        <td class="no-wrap text-success" style="cursor:pointer;" title="{{$item->client->client_name ?? 'Client Not Available'}}">{{ $item->lead_id }}</td>
                    @endif

                    <td class="no-wrap">
                        @if ($item->bid_id != NULL)
                            <a href="{{ route('admin.bid.view', ['id' => $item->bid_id]) }}" rel="facebox" class="text-success" title="View">{{ date('d M Y', strtotime($item->bid_date)) }}</a>
                        @else
                            {{ date('d M Y', strtotime($item->bid_date)) }}   
                        @endif
                    </td>

                    <td class="no-wrap">{{ date('d M Y', strtotime($item->next_followup)) }}</td>
                    <td class="no-wrap">{{ date('d M Y', strtotime($item->created_at)) }}</td>
                    <td class="no-wrap">{{ $item->user_name }}</td>
                    <td class="text-wrap" style="width:15%;">{{ $item->job_title }}{{$client_name ? " (".$client_name.")" : ''}}</td>
                    <td>{{ $item->is_invited == 1 ? 'Yes' : 'No' }}</td>

                    <td class="no-wrap">
                        <a href="{{ $item->job_link }}" target="_blank" class="text-success" title="Visit Job">{{ $item->portal }}</a>
                    </td>
                    
                    <td class="no-wrap">{{ $item->project_type }}</td>
                    
                    <td class="no-wrap pt-3" >
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <select id="statusDropdown" name="" class="input-group-text status-change col-md-12" data-leadid="{{ $item->id }}">
                                    @foreach (statusList() as $stKey => $status)
                                        <option value="{{ $stKey }}" {{ $stKey == $item->status ? 'selected' : '' }}>
                                            {{ ucfirst($status) == "Follow UP" ? ucfirst($status)." (".$item->followup_count.")"."" : ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </td>

                    <td class="no-wrap">
                        <div class="table-actions d-flex align-items-center gap-1">

                            <a href="{{ route('admin.lead.edit', ['id' => $item->id]) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit">
                                <i class="fa-solid fa-pen-to-square text-warning fs-5"></i>
                            </a> 

                            &nbsp;&nbsp;

                            @if (in_array(admin()->user()->role_id,[\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN]))
                                <a href="{{ route('admin.lead.view', ['id' => $item->id]) }}" rel="facebox" class="text-success" title="View">
                                    <i class="fa-solid fa-eye fs-5"></i>
                                </a>

                                &nbsp;&nbsp;
                            @endif
    
                                
                            @if (admin()->user()->role_id == \App\Models\Role::MANAGER)
                                <a href="{{ route('admin.lead.client-edit', ['id' => $item->client_id]) }}" rel="facebox" class="text-danger" title="Client">
                                    <i class="fa-solid fa-user fs-5"></i>
                                </a>&nbsp;&nbsp;
    
                                <a  data-id="{{$item->id}}" 
                                    id="global_delete"
                                    href="javascript:void(0)" 
                                    data-route="{{ route('admin.lead.delete', ['id' => $item->id]) }}" 
                                    class="text-danger" 
                                    data-message="delete this lead" 
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="bottom" 
                                    title="Delete Lead">
                                    <i class="fa-solid fa-trash text-danger fs-5"></i>
                                </a>&nbsp;&nbsp;

                                {{-- lead clone --}}
                                @if($item->project_type == "hourly")
                                    <a href="javascript:void(0)" data-id="{{$item->id}}" class="text-success" id="clone_lead" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Clone this lead">
                                        <i class="fa-solid fa-clone fs-5"></i>
                                    </a>&nbsp;&nbsp;
                                @endif

                            @endif
    
                            <a href="{{ route('admin.lead.log', ['id' => $item->id]) }}" class="text-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Logs" rel="facebox">
                                <i class="fa-solid fa-clock-rotate-left fs-5"></i>
                            </a>&nbsp;&nbsp;

                            {{-- @if (in_array(admin()->user()->role_id,["admin","superadmin"]))
                                    <a href="{{ route('admin.lead.edit', ['id' => $item->id]) }}" class="text-warning"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="fa-solid fa-pen-to-square text-warning"></i></a>
                                    &nbsp;&nbsp;
                                @endif

                                <a href="{{ route('admin.lead.delete', ['id' => $item->id]) }}" class="text-danger"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i
                                        class="bi bi-trash-fill"></i></a>
                                @if (admin()->user()->role_id == "manager")
                                    <a href="{{ route('admin.lead.client-edit', ['id' => $item->client_id]) }}" rel="facebox" class="text-danger" title="Client"><i class="fa-solid fa-user"></i></a>
                                    &nbsp;&nbsp;
                                @endif

                                <a href="{{ route('admin.lead.view', ['id' => $item->id]) }}" rel="facebox"
                                    class="text-success" title="View"><i class="fa-solid fa-eye"></i></a>
                                &nbsp;&nbsp;
                                
                                <a href="{{ route('admin.lead.log', ['id' => $item->id]) }}" class="text-dark"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Logs" rel="facebox"><i
                                        class="fa-solid fa-clock-rotate-left"></i></a> --}}
                        </div>
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

@include('admin.elements.filter-with-pagi', ['data' => $leads])
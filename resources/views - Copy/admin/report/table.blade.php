@php
    // $i = $leads->firstItem();
    $total_client_budget = 0;
    $total_sale = 0;
    $total_Received=0;
    $role_id = admin()->user()->role_id;
@endphp

<style>
    .project_type{margin:0;padding:3px;}
</style>
<div class="table-responsive mt-3">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                {{-- <td class="no-wrap text-center">#</td> --}}
                <td class="no-wrap ">@sortablelink('lead_id','Lead ID')</td>
                <td class="no-wrap ">@sortablelink('bid_date','Bid Date')</td>
                <td class="no-wrap ">User Name</td>
                <td class="text-wrap">Job Title</td>
                <td class="no-wrap ">@sortablelink('portal','Portal')</td>
                <td class="no-wrap ">@sortablelink('project_type','Type')</td>
                <td class="no-wrap  ">@sortablelink('client_budget','Client Budget')</td>
                <td class="no-wrap ">Received Amount</td>
                <td class="no-wrap">Sale</td>
                {{-- <td class="no-wrap">Status</td> --}}
                <td class="no-wrap" width="100px">Action</td>
            </tr>
        </thead>
        <tbody>
            @forelse ($leads as $key => $item)
                @php $client_name = is_null($item->client) ? '' : ($item->client->client_name ?? '') @endphp
                <tr class="{{($item->is_test == 1 && in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN,\App\Models\Role::MANAGER])) ? 'table-warning' : ''}}">
                    {{-- <td class="no-wrap">{{ $i }}</td> --}}
                    
                    <td class="no-wrap">{{ $item->lead_id }}</td>
                    
                    <td class="no-wrap">
                        @if ($item->bid_id != NULL)
                            <a href="{{ route('admin.bid.view', ['id' => $item->bid_id]) }}" rel="facebox" class="text-success" title="View">{{ date('d M Y', strtotime($item->bid_date)) }}</a>
                        @else
                            {{ date('d M Y', strtotime($item->bid_date)) }}   
                        @endif
                    </td>
                    
                    <td class="no-wrap">{{$item->user_name}}</td>
                    
                    <td class="text-wrap" style="width:15%;">{{ $item->job_title }}{{$client_name ? " (".$client_name.")" : ''}}</td>

                    <td class="no-wrap">
                        <a href="{{ $item->job_link }}" target="_blank" class="text-success" title="Visit Job">{{ $item->portal }}</a>
                    </td>

                    <td class="no-wrap">
                        @if(in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN,\App\Models\Role::MANAGER]))
                            <select name="project_type" class="input-group-text project_type col-md-10" data-id="{{$item->id}}">
                                <option value="fixed" {{ $item->project_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                <option value="hourly" {{ $item->project_type == 'hourly' ? 'selected' : '' }}>Hourly</option>
                            </select>
                        @else
                            {{ $item->project_type }}
                        @endif
                    </td>
                    
                    <td class="no-wrap">

                        <div class="d-flex budget_container-{{$item->id}}" >
                            <input type="text" value="{{ $item->client_budget }}" disabled="disabled" class="w-50 {{'budget-'.$item->id}}" onkeyup="enableSaveBtn({{$item->id}})">
                            
                            <button class="" id="{{'savebtn-'.$item->id}}" data-project-type="{{$item->project_type}}" style="display:none;" onclick="saveBudget({{$item->id}})">Save</button>
                        </div>
                    
                    </td>
                    
                    <td class="no-wrap">
                        @php
                            $sum = 0;
                            
                            foreach($item->budgets as $budget){
                                $sum = $sum + $budget['budget'];
                            }
                            
                            $total_Received = $total_Received + $sum;
                            echo $sum;
                        @endphp
                    </td>

                    <td class="no-wrap" id="sale-{{$item->id}}">
                        @if ($item->project_type == "fixed")
                            @php
                                echo $item->client_budget;
                                $total_sale = $total_sale + $item->client_budget;
                            @endphp
                        @elseif($item->project_type == "hourly")
                            @php
                                echo $sum;
                                $total_sale = $total_sale + $sum;
                            @endphp
                        @endif
                    </td>

                    <td class="no-wrap ">
                        <div class="table-actions d-flex align-items-center justify-content-center gap-2">
                            <a href="javascript:void(0)" onclick="editBudget({{$item->id}})" class="text-danger" title="Edit"><i class="fa-solid fa-pen-to-square text-warning fs-5"></i></a>
                         
                            <a href="{{ route('admin.lead.view', ['id' => $item->id]) }}" rel="facebox" class="text-success" title="View"><i class="fa-solid fa-eye fs-5"></i></a>

                            <a href="{{ route('admin.report.add-budget', ['id' => $item->id]) }}" rel="facebox" class="text-pending" title="Add Budget"><i class="fa-solid fa-comments-dollar fs-5"></i></a>
                          
                        </div>
                    </td>
                </tr>
                @if ((sizeof($leads) - 1) == $key)
                    <tr class="no-wrap">
                        <td class="no-wrap "></td>
                        <td class="no-wrap "></td>
                        <td class="no-wrap "></td>
                        <td class="no-wrap "></td>
                        <td class="no-wrap "></td>
                       
                        <td class="no-wrap "></td>
                       
                   
                        <td class="no-wrap"><strong>Total</strong></td>
                        <td class="no-wrap total-received">{{ $total_Received}}</td>
                        <td class="no-wrap total-sale">{{$total_sale}}</td>
                        <td class="no-wrap "></td>
                    </tr>
                @endif
                @php
                    // $i++;
                @endphp
            @empty
                <tr>
                    <td colspan="10" class="text-center">
                        No Record Found !
                    </td>
                </tr>
            @endforelse

        </tbody>
    </table>
</div>

<div class="table-responsive mt-3">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                <td></td>
                <td></td>
                <td class="no-wrap ">User Name</td>
                <td class="text-wrap">Total Received</td>
                <td class="text-wrap">Total Sale</td>
            </tr>
        </thead>
        <tbody>
            @forelse ($user as $key => $item)
                <tr class="{{($item['is_test'] == 1 && $role_id == \App\Models\Role::SUPERADMIN) ? 'table-warning' : ''}}">
                    <td class="no-wrap"></td>
                    <td class="no-wrap"></td>
                 
                    <td class="no-wrap">
                       {{$item['user']}}
                    </td>
                    <td class="no-wrap">{{$item['rec']}}</td>
                    <td class="no-wrap ">{{$item['incentive']}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">
                        No Record Found !!
                    </td>
                </tr>
            @endforelse
            
        </tbody>
    </table>
</div>

<script>
    function enableSaveBtn(id){
        $("#savebtn-"+id).attr('style','display:block');
    }

    function editBudget(id){
        let condition = $(".budget-"+id).attr('disabled');
        if(condition == "disabled"){
            $(".budget-"+id).attr('disabled',false);
        }else{
            $(".budget-"+id).attr('disabled',true);
            $("#savebtn-"+id).attr('style','display:none');
        }
    }

    function saveBudget(id){
        let project_type = $('#savebtn-'+id).attr('data-project-type');
        let amount = $(".budget-"+id).val();
        $.ajax({
            url:"{{route('admin.report.update-client_budget')}}",
            type:'post',
            data:{
                "_token": "{{ csrf_token() }}",
                leadId:id,
                budget:amount
            },
            dataType:"json",
            success:function(response){
                if (response.status == true) {
                    if(project_type == "fixed"){
                        $('#sale-'+id).html(amount);
                    }
                    $(".budget-"+id).attr('disabled',true);
                    $("#savebtn-"+id).attr('style','display:none');
                    $(".budget_container-"+id).siblings('div').html("");
                }
                if (response.status == false) {
                    var errors = response['error'];
                    if(errors['budget']){
                        $(".budget_container-"+id).siblings('div').html(errors['budget']);
                    }
                    else{
                        $("#savebtn-"+id).siblings('div').html("");
                    }
                }
            }
        });
    }
</script>

{{-- @include('admin.elements.filter-with-pagi', ['data' => $leads]) --}}

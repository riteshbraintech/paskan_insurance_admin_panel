@php
    $status = ["fake_lead","awarded","dead_lead"];
    $editableRoles = [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN,\App\Models\Role::MANAGER];
@endphp

<style scoped>
    #facebox {
        top: 50px !important;
        left: 25%;
        width: 55%;
        height: 90vh;
        position: fixed !important;
    }

    .close img{
        height: 12px;
        width: 12px;
    }
    #facebox .close {
        position: absolute;
        top: 15px;
        right: 13px;
        padding: 2px;
        background: #fff;
    }
    #facebox .popup {
        height: 100%;
        overflow-y: auto;
        overflow-x: hidden;
    }

    #facebox .content {
        width: 100%;
        height: 100%;
        border-radius: 0;
    }

    .box-list{
        padding: 7px;
        margin-bottom: 15px;
    }
    .remarlist {
        border-bottom: 1px solid #ebebeb;
        margin-bottom: 5px;
    }

    .remarlist>p {
        margin-bottom: 0px;
    }

    .remarlist>p:last-child {
        font-size: 11px;
        color: black;
    }
    
</style>

<div class="row">
    <div class="col-xl-12">
        <div class="p-4 border rounded" style="margin-bottom:10px;">
            <form action="{{ route('admin.lead.view-form-update', ['id' => $lead_data->id]) }}" method="post" enctype="multipart/form-data" class="row g-3 needs-validation" onsubmit="enableFields();" >
                    
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <label for="next_followup" class="form-label">Next Followup<span class="text-danger">*</span></label>
                        
                        <input type="checkbox" id="disable_btn" name="disable_btn" onclick="disable(event);" {{in_array($lead_data->status,$status) ? "hidden" : ""}} />
                        
                        <input type="date" name="next_followup" class="form-control" id="next_followup" value="{{ $lead_data->next_followup }}" placeholder="" min="<?php echo date('Y-m-d'); ?>" disabled>

                        @if ($errors->has('next_followup'))
                            <div class="text-danger">{{ $errors->first('next_followup') }}</div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label for="bid_quote" class="form-label">Bid Quote<span class="text-danger">*</span></label>
                        <input type="text" name="bid_quote" class="form-control" id="bid_quote" value="{{ $lead_data->bid_quote }}" placeholder="Enter your initial bid" {{(in_array($lead_data->status,$status)) ? "readOnly" : ""}}>
                        
                        @if ($errors->has('bid_quote'))
                            <div class="text-danger">{{ $errors->first('bid_quote') }}</div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label">Status<span class="text-danger">*</span></label>
                        <select class="form-select status" name="status" id="status" {{(in_array($lead_data->status,$status)) ? 'disabled="disabled"' : ""}}>
                            <option selected disabled value="">Choose...</option>
                            @foreach (statusList() as $stKey => $value)
                                <option value="{{ $stKey }}" {{ $stKey == $lead_data->status ? 'selected' : '' }}> {{ ucfirst($value) }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('status'))
                            <div class="text-danger">{{ $errors->first('status') }}</div>
                        @endif
                    </div>

                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <label for="project_type" class="form-label">Project Type<span class="text-danger">*</span></label>
                        <select class="form-select project_type" name="project_type" id="project_type" {{in_array($lead_data->status,$status) ? "disabled='disabled'" : ""}}>
                            <option selected disabled value="">Choose...</option>
                            <option value="hourly" {{ $lead_data->project_type == 'hourly' ? 'selected' : '' }}>Hourly</option>
                            <option value="fixed" {{ $lead_data->project_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                        </select>

                        @if ($errors->has('project_type'))
                            <div class="text-danger">{{ $errors->first('project_type') }}</div>
                        @endif
                    </div>

                    <div class="col-md-8">
                        <label for="attachments" class="form-label">Attachment</label>
                        <input type="file" name="attachments[]" class="form-control attachments" id="attachments" value="{{ old('attachments') }}" multiple {{in_array($lead_data->status,$status) ? "disabled='disabled'" : ""}}>

                        @if ($errors->has('attachments'))
                            <div class="text-danger">{{ $errors->first('attachments') }}</div>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-9">
                        <label for="remark" class="form-label"><strong>Remarks :</strong></label>
                        <textarea class="form-control" name="remark" id="remark" placeholder=""> </textarea>
                    </div>

                    <div class="col-md-3 mt-5">
                        <button type="submit" class="btn btn-success px-5 radius-30">Update</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="border rounded" style="margin-bottom:10px;">
            <div class="card" style="margin-bottom:0;">
                <div class="card-header">
                    <strong>Lead Budget History</strong> 
                </div>
                <div class="card-body" style="padding: 0.1rem 1rem;">
                    <div class="row">
                        <div class="col-md-12 box-list">
                            <div class="table-responsive mt-3">
                                <table class="table align-middle" style="background-color:#8e8e8">
                                    <thead class="table-secondary">
                                        <tr>
                                            <td class="no-wrap ">Added By</td>
                                            <td class="no-wrap ">Date</td>
                                            <td class="no-wrap ">Budget</td>
                                            <td class="no-wrap ">Action</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($lead_data->budgets as $budget)
                                            <tr class="" id="{{'budget-'.$budget->id}}">
                                                <td class="no-wrap">{{ $budget->admin->name }}</td>
                                                <td class="no-wrap">{{ date('d M Y', strtotime($budget->added_at)) }}</td>
                                                <td class="no-wrap">
                                                    <div class="d-flex budget_container-{{$budget->id}}">
                                                        <input type="text" value="{{ $budget->budget }}" disabled="disabled" class="w-50 {{'budget-'.$budget->id}}" onkeyup="enableSaveBtn({{$budget->id}})">
                                                        
                                                        <button class="" id="{{'savebtn-'.$budget->id}}" style="display:none;" onclick="saveBudget({{$budget->id}})">Save</button>
                                                    </div>
                                                    <div class="text-danger"></div>
                                                </td>

                                                <td class="no-wrap ">
                                                    <div class="table-actions d-flex align-items-center gap-0 fs-7 ">
                                                        
                                                        <a href="javascript::void(0)" onclick="editBudget({{$budget->id}})" class="text-danger" title="Edit"><i class="fa-solid fa-pen-to-square text-warning"></i></a>
                                                        &nbsp;&nbsp;
                                                        
                                                        <a href="javascript::void(0)" class="text-danger delete-budget" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" onclick="deleteBudget({{$budget->id}})"><i class="fa-solid fa-trash text-danger "></i></a>

                                                        &nbsp;&nbsp;
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    No Record Found !
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class=" border rounded" style="margin-bottom:10px;">
            <div class="card" style="margin-bottom:0;">
                <div class="card-header">
                <strong>Lead Remarks</strong> 
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 box-list">
                            @forelse ($lead_data->remarks as $remark)
                                @if (!empty($remark['remark']))
                                    <div class="remarlist">
                                        <p>
                                            <strong>Remark :</strong>{{ $remark['remark'] }}
                                        </p>
                                        <p>Created By {{ $remark->admin->name ?? '' }} on
                                            {{ date('d M Y h:i:s',strtotime($remark['created_at'])) }} </p>
                                    </div>
                                @endif
                            @empty
                                No Record Found !
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class=" border rounded" style="margin-bottom:10px;">
            <div class="card" style="margin-bottom:0;">
                <div class="card-header">
                    <strong>Lead Attachment</strong> 
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            @forelse ($lead_data->attachments as $attachment)
                                <div class="remarlist">
                                    <p>
                                        <strong>Created By :</strong>{{ $attachment->admin->name ?? '' }}
                                    </p>
                                    <p>
                                        <strong>Date :</strong>{{ date('d M Y h:i:s',strtotime($attachment['created_at'])) }}
                                    </p>
                                    <p>
                                        <strong>Attachment :</strong>
                                        {{ $attachment['attachment'] }}
                                        <a target="_BLANK" href="{{ $attachment['attachment_url'] }}">View</a>
                                    </p>
                                </div>
                            @empty 
                                No Record Found !   
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class=" border rounded" style="margin-bottom:10px;">
            <div class="card" style="margin-bottom:0;">
                <div class="card-header">
                    <strong>Lead</strong> 
                    @if (in_array(admin()->user()->role_id, $editableRoles))
                        <a href="javascript:void(0)" onclick="editJob({{$lead_data->id}})" class="text-danger" title="Edit" style="float: right;">
                            <i class="fa-solid fa-pen-to-square text-warning"></i>
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 ">
                            @if(!blank($lead_data))
                                
                                <div class="row">
                                    @if (in_array(admin()->user()->role_id, $editableRoles))
                                        <div class="remarlist col-md-12">
                                            <strong>Job Title:</strong>
                                            
                                            <input type="text" name="job_title" class="w-50 job-title-{{ $lead_data->id }}" id="job_title" value="{{ $lead_data->job_title }}" disabled onkeyup="enableJobSaveBtn({{ $lead_data->id }})">
                                            
                                            <span class="text-danger error_job_title error_jobs"></span>
                                        </div>
                                    
                                    @else
                                        <div class="remarlist col-md-12">
                                            <strong>Job Title:</strong>
                                            {{ $lead_data->job_title }}
                                        </div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="remarlist col-md-12"><strong>Job Description :</strong> {{ $lead_data->description }}</div>
                                </div>

                                <div class="row">
                                    <div class="remarlist col-md-4"><strong>Bid Date :</strong>{{ date('d M Y',strtotime($lead_data->bid_date)) }}</div>

                                    <div class="remarlist col-md-4"><strong>Username :</strong>  {{ $lead_data->user_name }}</div>
                                    
                                    <div class="remarlist col-md-4"><strong>Next Followup :</strong>{{ date('d M Y',strtotime($lead_data->next_followup)) }}</div>
                                </div>

                                <div class="row">
                                    <div class="remarlist col-md-4"><strong>Status :</strong> {{ $lead_data->status }}</div>
                                    <div class="remarlist col-md-4"><strong>Connects Needed :</strong> {{ $lead_data->connects_needed }}</div>

                                    @if (in_array(admin()->user()->role_id, $editableRoles))
                                        <div class="remarlist col-md-4">
                                            <strong>Job Link :</strong>

                                            <input type="text" name="job_link" class="job-link-{{ $lead_data->id }}" id="job_link" value="{{  $lead_data->job_link}}" disabled onkeyup="enableJobSaveBtn({{ $lead_data->id }})" style="width:70%;"><br>

                                            <span class="text-danger error_job_link error_jobs"></span>
                                        </div>
                                    @else
                                        <div class="remarlist col-md-4">
                                            <strong>Job Link :</strong>
                                            <a href="{{ $lead_data->job_link }}" target="_blank" class="text-success" title="Visit Job">{{ $lead_data->job_link }}</a>
                                        </div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="remarlist col-md-4"><strong>Project Type :</strong> {{ $lead_data->project_type }}</div>
                                    
                                    <div class="remarlist col-md-4">
                                        <strong>Portal :</strong>
                                        <a href="{{ $lead_data->job_link }}" target="_blank" class="text-success" title="Visit Job">{{ $lead_data->portal }}</a>
                                    </div>

                                    <div class="remarlist col-md-4"><strong>Bid Quote :</strong> {{ $lead_data->bid_quote }}</div>
                                </div>

                                <div class="row">
                                    <div class="remarlist col-md-4"><strong>Client Budget :</strong> {{ $lead_data->client_budget }}</div>
                                    <div class="remarlist col-md-4"><strong>Profile :</strong> {{ $lead_data->profile }}</div>
                                    <div class="remarlist col-md-4"><strong>Technology :</strong> {{ $lead_data->technology }}</div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 ">
                                        <button type="button" id="job_save" style="display:none;" onclick="saveJob({{$lead_data->id}})" class="save-job-{{$lead_data->id}} btn btn-success px-5 radius-30">Save</button>
                                    </div>
                                </div>
                            @else
                                No Record Found !
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class=" border rounded" style="margin-bottom:10px;">
            <div class="card" style="margin-bottom:0;">
                <div class="card-header">
                    <strong>Lead Client Information</strong>  
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 ">
                            @if(!blank($lead_data->client))
                                <div class="row">
                                    <div class="remarlist col-md-4"><strong>Client Name :</strong>{{ $lead_data->client->client_name }}</div>
                                    <div class="remarlist col-md-4"><strong>Mobile :</strong>{{ $lead_data->client->mobile }}</div>
                                    <div class="remarlist col-md-4"><strong>Email :</strong>{{ $lead_data->client->email }}</div>
                                </div>
                                <div class="row">
                                    <div class="remarlist col-md-4"><strong>Skype :</strong>{{ $lead_data->client->skype }}</div>
                                    <div class="remarlist col-md-4"><strong>LinkedIn :</strong>{{ $lead_data->client->linkedin }}</div>
                                    <div class="remarlist col-md-4"><strong>Other :</strong>{{ $lead_data->client->other}}</div>
                                </div>  
                                
                                <div class="row">
                                    <div class="remarlist col-md-12"><strong>Location :</strong>{{ $lead_data->client->location}}</div>
                                </div>
                            @else
                                No Record Found !!
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function enableFields(){
        document.getElementsByClassName('status')[0].disabled = false;
        document.getElementsByClassName('project_type')[0].disabled = false;
    }

    function disable(event){
        $data = $('#next_followup').val();
        if (event.target.checked) {
            $('#next_followup').prop('disabled', false); // If checked enable item 
            $('#disable_btn').attr("checked",false);      
        } else {
            // $('#next_followup').val(null); // If checked disable item 
            $('#next_followup').prop('disabled', true); // If checked disable item 
            $('#disable_btn').attr("checked",true);                                    
        }
    }

    function deleteBudget(id){
        let budgetId = id;
        $.ajax({
            url:"{{ route('admin.report.delete-budget') }}",
            type:'get',
            data:{id:budgetId},
            dataType:'json',
            success:function(response){
                if (response.status == true) {
                    $("#budget-"+id).remove();
                }
            }
        });
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

    function enableSaveBtn(id){
        $("#savebtn-"+id).attr('style','display:block');
    }

    function saveBudget(id){
        let amount = $(".budget-"+id).val();
        $.ajax({
            url:"{{route('admin.report.update-budget')}}",
            type:'post',
            data:{
                "_token": "{{ csrf_token() }}",
                budgetId:id,
                budget:amount
            },
            dataType:"json",
            success:function(response){
                if (response.status == true) {
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

    function editJob(id) {
    
        let titleCondition = $(".job-title-" + id).attr('disabled');
        let linkCondition = $(".job-link-" + id).attr('disabled');

        if (titleCondition == "disabled" && linkCondition == "disabled") {
            $(".job-title-" + id).attr('disabled', false);
            $(".job-link-" + id).attr('disabled', false);
            
        } else {
            $(".job-title-" + id).attr('disabled', true);
            $(".job-link-" + id).attr('disabled', true);
            $(".save-job-" + id).attr('style', 'display:none');
        }
    }

    function enableJobSaveBtn(id) {
        $(".save-job-" + id).attr('style', 'display:block');
    }


    function saveJob(id) {
        $('.error_jobs').text('');
        let title = $(".job-title-" + id).val();
        let link = $(".job-link-" + id).val();
        $.ajax({
            url: "{{ route('admin.lead.update-job') }}",
            type: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                id: id,
                job_title: title,
                job_link: link
            },
            dataType: "json",
            success: function(response) {
                
                if (response.status == true) {
                    $(".job-title-" + id).attr('disabled', true);
                    $(".job-link-" + id).attr('disabled', true);
                    $(".save-job-" + id).hide();
                    loadData();
                }else{
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            $('.error_' + key).text(value);
                        });
                    }
                    if (response.job_link) {
                        $('.error_job_link').text(response.job_link);
                    }
                }
            
            }
        });
    }
    
</script>


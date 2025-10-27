<style scoped>
    #facebox {
        top: 56px !important;
        left: 25%;
        width: 55%;
        height: 90vh !important;
    }
    #facebox .popup {
        height: 100%;
        overflow-y: scroll;
        overflow-x: hidden;
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
    .card-body{
        height: 100%;
    }
</style>

<div class="p-4 border rounded">
    <div class="card">
        <div class="card-header">
            <strong>Lead Information</strong> 
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 ">
                    @if(!blank($lead))
                        @php $client_name = is_null($lead->client) ? '' : ($lead->client->client_name ?? '') @endphp
                        <div class="row">
                            <div class="remarlist col-md-6">
                                <strong>Bid Date :</strong>{{ date('d M Y',strtotime($lead->bid_date)) }}
                            </div>
                            <div class="remarlist col-md-6">
                                <strong>Is Invite : {{$lead->is_invited =='1' ? 'Yes' : 'No'}}</strong>
                            </div>
                        </div>
                            
                        <div class="row">
                            <div class="remarlist col-md-6">
                                <strong>Project Type :</strong> {{ $lead->project_type }}
                            </div>
                            <div class="remarlist col-md-6">
                                <strong>Portal :</strong><a href="{{ $lead->job_link }}" target="_blank" class="text-success" title="Visit Job">{{ $lead->portal }}</a>
                            </div>    
                        </div>

                        <div class="row">
                            <div class="remarlist col-md-6"><strong>Username :</strong>  {{ $lead->user_name }}</div>
                            <div class="remarlist col-md-6"><strong>Technology :</strong> {{ $lead->technology }}</div>
                        </div>

                        <div class="row">
                            <div class="remarlist col-md-6"><strong>Profile :</strong> {{ $lead->profile }}</div>
                            <div class="remarlist col-md-6"><strong>Connects Needed :</strong> {{ $lead->connects_needed }}</div>
                        </div>
                        
                        <div class="row">
                            <div class="remarlist col-md-6">
                                <strong>Client Budget : {{$lead->currency}}</strong> {{" "}}{{ $lead->client_budget }}
                            </div>
                            <div class="remarlist col-md-6"><strong>Bid Quote :</strong> {{ $lead->bid_quote }}</div>
                        </div> 

                        <div class="row">
                            <div class="remarlist col-md-6"><strong>Next Followup :</strong> {{ $lead->next_followup }}</div>
                            <div class="remarlist col-md-6"><strong>Bid Status :</strong> {{ $lead->status }}</div>
                        </div> 

                        <div class="row">
                            <div class="card-header">
                                <strong>Lead Remarks</strong>
                            </div>
                            <div class="col-md-12 box-list">
                                @if (!empty($lead->remarks))
                                    @forelse ($lead->remarks as $remark)
                                        @if (!empty($remark['remark']))
                                            <div class="remarlist">
                                                <p><strong>Remark :</strong>{{$remark['remark']}}</p>
                                                <p>Created By {{ $remark->admin->name ?? '' }} on {{ $remark['created_at'] }}</p>
                                            </div>
                                        @endif
                                    @empty
                                        No Record Found !
                                    @endforelse
                                @endif
                            </div>
                        </div> 

                        <div class="row">
                            <div class="remarlist col-md-12"><strong>Job Title :</strong> {{ $lead->job_title }}{{$client_name ? " (".$client_name.")" : ''}}</div>
                            <div class="remarlist col-md-12"><strong>Job Description :</strong> {{ $lead->description }}</div>
                            <div class="remarlist col-md-12">
                                <strong>Job Link :</strong>
                                <a href="{{ $lead->job_link }}" target="_blank" class="text-success" title="Visit Job">{{ $lead->job_link }}</a>
                            </div>
                        </div>
                            
                        <div class="row">
                            <div class="card-header">
                                <strong>Lead Attachment</strong> 
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        @forelse ($lead->attachments as $attachment)
                                            <div class="remarlist">
                                                <p><strong>Created By:</strong> {{ $attachment->admin->name ?? '' }}</p>
                                                <p>
                                                    <strong>Date:</strong> {{ date('d M Y h:i:s', strtotime($attachment->created_at)) }}
                                                </p>
                                                <p>
                                                    <strong>Attachment:</strong>{{ $attachment->attachment }}
                                                    <a target="_blank" href="{{ $attachment->attachment_url }}">View</a>
                                                </p>
                                            </div>
                                        @empty 
                                            <div class="remarlist">
                                                No Record Found!
                                            </div>  
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="card-header">
                                <strong>Lead Client information</strong> 
                                </div>
                        
                            <div class="col-md-12 box-list">
                                @if (!empty($lead->client))
                                    @if (!empty($lead->client->client_name))
                                        <div class="remarlist col-md-12">
                                            <strong>Client Name :</strong> {{ $lead->client->client_name }}
                                        </div>
                                    @endif

                                    @if (!empty($lead->client->mobile))
                                        <div class="remarlist col-md-12">
                                            <strong>Mobile :</strong> {{ $lead->client->mobile }}
                                        </div>
                                    @endif

                                    @if (!empty($lead->client->email))
                                        <div class="remarlist col-md-12">
                                            <strong>Email :</strong> {{ $lead->client->email }}
                                        </div>
                                    @endif

                                    @if (!empty($lead->client->skype))
                                        <div class="remarlist col-md-12">
                                            <strong>Skype :</strong> {{ $lead->client->skype }}
                                        </div>
                                    @endif

                                    @if (!empty($lead->client->linkedin))
                                        <div class="remarlist col-md-12">
                                            <strong>Linkedin :</strong> {{ $lead->client->linkedin }}
                                        </div>
                                    @endif
                                    
                                    @if (!empty($lead->client->location))
                                        <div class="remarlist col-md-12">
                                            <strong>Location :</strong> {{ $lead->client->location }}
                                        </div>
                                    @endif

                                    @if (!empty($lead->client->other))
                                        <div class="remarlist col-md-12">
                                            <strong>Other :</strong> {{ $lead->client->other }}
                                        </div>
                                    @endif
                                    
                                @else
                                    <div class="remarlist col-md-12">
                                        No Record Found!
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="card-header">
                                <strong>Lead Budget History</strong> 
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 box-list">
                                        <div class="table-responsive mt-3">
                                            <table class="table align-middle" style="background-color:#8e8e8">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th scope="col" class="no-wrap">Added By</th>
                                                        <th scope="col" class="no-wrap">Date</th>
                                                        <th scope="col" class="no-wrap">Budget</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($lead->budgets as $budget)
                                                        <tr id="{{'budget-'.$budget->id}}">
                                                            <td class="no-wrap">{{ $budget->admin->name }}</td>
                                                            <td class="no-wrap">{{ date('d M Y', strtotime($budget->added_at)) }}</td>
                                                            <td class="no-wrap">
                                                                <div class="d-flex budget_container-{{$budget->id}}">
                                                                    <input type="text" value="{{ $budget->budget }}" disabled="disabled" class="w-50 {{'budget-'.$budget->id}}" onkeyup="enableSaveBtn({{$budget->id}})">
                                                                </div>
                                                                <div class="text-danger"></div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center">
                                                                No Record Found!
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
                    @endif 
                </div>
            </div>
        </div>
    </div>
</div>
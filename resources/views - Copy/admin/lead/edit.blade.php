@extends('admin.layouts.app')

@push('style')
    <style>
        span.select2.select2-container.select2-container--classic{width: 100% !important;}
    </style>
@endpush

@section('content')

    @php
        $status = ["fake_lead","cancelled"];
    @endphp

    <style>
        .box-list{
            border: 1px solid #ededed;
            padding: 7px;
            margin-bottom: 15px;
        }
        .remarlist {
            border-bottom: 1px solid #ebebeb;
            margin-bottom: 5px;
        }

        .remarlist>p {margin-bottom: 0px;}

        .remarlist>p:last-child {
            font-size: 11px;
            color: grey;
        }
       
    </style>

    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Edit Bid</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.lead.list') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.lead.list') }}">Lead</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Lead</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <form action="{{ route('admin.lead.update', ['id' => $lead->id]) }}" method="post" enctype="multipart/form-data" class="row g-3 needs-validation" onsubmit="enableFields();">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="border rounded" style="margin-bottom:15px;">
                            <div class="card" style="margin-bottom:0;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card-header">
                                            <strong>Lead</strong>
                                            <a href="{{ route('admin.lead.log', ['id' => $lead->id]) }}" rel="facebox" class="text-success" title="Logs View" style="float:right;"><i class="fa-solid fa-eye"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="job_title" class="form-label">Job Title<span class="text-danger">*</span></label>
                                            <input type="text" name="job_title" class="form-control" id="job_title" value="{{ $lead->job_title }}" placeholder="Enter Job Title" {{in_array($lead->status,$status) ? "readOnly" : ""}}>
                                            
                                            @if ($errors->has('job_title'))
                                                <div class="text-danger">{{ $errors->first('job_title') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="bid_date" class="form-label">Bid Date</label>
                                            <input type="date" name="bid_date" class="form-control" id="bid_date" value="{{ $lead->bid_date }}" placeholder="" readonly>
                                            
                                            @if ($errors->has('bid_date'))
                                                <div class="text-danger">{{ $errors->first('bid_date') }}</div>
                                            @endif
                                        </div>
                                       
                                        <div class="col-md-4">
                                            <label for="next_followup" class="form-label">Next Followup<span class="text-danger">*</span></label>
                                            
                                            <input type="checkbox" id="disable_btn" name="disable_btn" onclick="disable(event);" {{in_array($lead->status,$status) ? "hidden" : ""}}/>
                                            
                                            <input type="date" name="next_followup" class="form-control" id="next_followup" value="{{ $lead->next_followup }}" placeholder="" min="<?php echo date('Y-m-d'); ?>" disabled>

                                            @if ($errors->has('next_followup'))
                                                <div class="text-danger">{{ $errors->first('next_followup') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="job_link" class="form-label">Job Link<span class="text-danger">*</span></label>
                                            <input type="text" name="job_link" class="form-control" id="job_link" value="{{ $lead->job_link }}" placeholder="Enter Job Link" {{in_array($lead->status,$status) ? "readOnly" : ""}}>
                                            
                                            @if ($errors->has('job_link'))
                                                <div class="text-danger">{{ $errors->first('job_link') }}</div>
                                            @endif
                                            @if (session('job_link'))
                                                <div class="text-danger">{{ session('job_link') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="project_type" class="form-label">Project Type<span class="text-danger">*</span></label>
                                            <select class="form-select project_type" name="project_type" id="project_type" {{in_array($lead->status,$status) ? "disabled='disabled'" : ""}}>
                                                <option selected disabled value="">Choose...</option>
                                                <option value="hourly" {{ $lead->project_type == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                                <option value="fixed" {{ $lead->project_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                            </select>

                                            @if ($errors->has('project_type'))
                                                <div class="text-danger">{{ $errors->first('project_type') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="portal" class="form-label">Portal</label>
                                            <select class="form-select portal other" data-type="portal" name="portal" id="portal" {{(in_array($lead->status,$status)) ? 'disabled="disabled"' : ""}}>
                                                
                                                <option selected disabled value="">Choose...</option>
                                                
                                                @forelse ($portals as $portal)
                                                    <option value="{{$portal->slug}}" {{ $lead->portal == $portal->slug ? 'selected' : '' }}>{{$portal->name}}</option>
                                                @empty
                                                    <option selected value="">No Portals Available</option>
                                                @endforelse

                                                <option value="other">Other</option>
                                            </select>

                                            @if ($errors->has('portal'))
                                                <div class="text-danger">{{ $errors->first('portal') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="bid_quote" class="form-label">Bid Quote<span class="text-danger">*</span></label>
                                            <input type="text" name="bid_quote" class="form-control" id="bid_quote" value="{{ $lead->bid_quote }}" placeholder="Enter your initial bid" {{(in_array($lead->status,$status)) ? "readOnly" : ""}}>
                                            
                                            @if ($errors->has('bid_quote'))
                                                <div class="text-danger">{{ $errors->first('bid_quote') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="client_budget" class="form-label">Client Budget<span class="text-danger">*</span></label>
                                            <div class="input-group mb-3">
                                                <select class="input-group-text" name="currency" id="">
                                                    <option value="USD" {{ $lead->currency == 'USD' ? 'selected' : '' }}>USD</option>
                                                    <option value="INR" {{ $lead->currency == 'INR' ? 'selected' : '' }}>INR</option>
                                                </select>

                                                <input type="text" name="client_budget" class="form-control" id="" value="{{ $lead->client_budget }}"
                                                placeholder="Enter your budget" {{( in_array($lead->status,$status)) ? 'readOnly' : ""}}>
                                            </div>

                                            @if ($errors->has('client_budget'))
                                                <div class="text-danger">{{ $errors->first('client_budget') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="profile" class="form-label">Profile<span class="text-danger">*</span></label>
            
                                            <select class="form-select profile other" name="profile" id="profile" data-type="profile" {{in_array($lead->status,$status) ? "disabled='disabled'" : ""}}>

                                                <option selected disabled value="">Choose...</option>
                                                @forelse ( profiles() as $key => $val)
                                                    <option value="{{$key}}" {{ isset($lead->profile) ? ($lead->profile == $key ? 'selected' : "") : (old('profile') == $key ? 'selected' : '' )}}>{{$val}}</option>    
                                                @empty 
                                                @endforelse

                                            </select>
                                            
                                            @if ($errors->has('profile'))
                                                <div class="text-danger">{{ $errors->first('profile') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="technology" class="form-label">Project Technology</label>
                                            <select class="form-select technology other" data-type="technology" name="technology" id="technology" {{in_array($lead->status,$status) ? "disabled='disabled'" : ""}}>
                                                <option selected disabled value="">Choose...</option>
                                                @forelse ($technologies as $technology)
                                                    <option value="{{$technology->slug}}" {{ $lead->technology == $technology->slug ? 'selected' : '' }}>{{$technology->name}}</option>
                                                @empty
                                                    <option selected value="">No Technology Available</option>
                                                @endforelse

                                                <option value="other">Other</option>
                                            </select>

                                            @if ($errors->has('technology'))
                                                <div class="text-danger">{{ $errors->first('technology') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="connects_needed" class="form-label">Connects Needed<span class="text-danger">*</span></label>
                                            <input type="number" name="connects_needed" class="form-control" id="connects_needed" value="{{ $lead->connects_needed }}" placeholder="" {{(in_array($lead->status,$status)) ? 'readOnly' : ""}}>
                                            
                                            @if ($errors->has('connects_needed'))
                                                <div class="text-danger">{{ $errors->first('connects_needed') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="status" class="form-label">Status<span class="text-danger">*</span></label>
                                            <select class="form-select status" name="status" id="status" {{(in_array($lead->status,$status)) ? 'disabled="disabled"' : ""}}>
                                                <option selected disabled value="">Choose...</option>
                                                @foreach (statusList() as $stKey => $value)
                                                    <option value="{{ $stKey }}" {{ $stKey == $lead->status ? 'selected' : '' }}>
                                                        {{ ucfirst($value) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('status'))
                                                <div class="text-danger">{{ $errors->first('status') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-12 mt-3">
                                            <label for="description" class="form-label">Job Description</label>
                                            <textarea name="description" id="description" class="form-control" cols="30" rows="2">{{$lead->description}}</textarea>
                                           
                                            @if ($errors->has('description'))
                                                <div class="text-danger">{{ $errors->first('description') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded" style="margin-bottom:15px;">
                            <div class="card" style="margin-bottom:0;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card-header">
                                            <strong>Lead Remarks</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 box-list" style="border:0;">
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

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="remark" class="form-label"><strong>Remarks:</strong></label>
                                                <textarea class="form-control" name="remark" id="remark" placeholder="Required example textarea"> </textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded" style="margin-bottom:15px;">
                            <div class="card" style="margin-bottom:0;">
                                <div class="card-header">
                                    <strong>Lead Client Information</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="client_name" class="form-label">Client name<span class="text-danger">*</span></label>
                                            <input type="text" name="client_name" class="form-control" id="client_name" value="{{ isset($lead->client->client_name) ? $lead->client->client_name : '' }}" placeholder="Enter Full Name">
                                            
                                            @if ($errors->has('client_name'))
                                                <div class="text-danger">{{ $errors->first('client_name') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="mobile" class="form-label">Mobile</label>
                                            <input type="number" name="mobile" class="form-control" id="mobile" value="{{ isset($lead->client->mobile) ?  $lead->client->mobile : ''}}">
                                            
                                            @if ($errors->has('mobile'))
                                                <div class="text-danger">{{ $errors->first('mobile') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" id="email" value="{{isset($lead->client->email) ? $lead->client->email : '' }}" >
                                            
                                            @if ($errors->has('email'))
                                                <div class="text-danger">{{ $errors->first('email') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="skype" class="form-label">Skype</label>
                                            <input type="text" name="skype" class="form-control" id="skype" value="{{isset($lead->client->skype) ? $lead->client->skype : ''  }}" >
                                            
                                            @if ($errors->has('skype'))
                                                <div class="text-danger">{{ $errors->first('skype') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="linkedin" class="form-label">Linkedin</label>
                                            <input type="text" name="linkedin" class="form-control" id="linkedin" value="{{ isset($lead->client->linkedin) ? $lead->client->linkedin : ''   }}">
                                            
                                            @if ($errors->has('linkedin'))
                                                <div class="text-danger">{{ $errors->first('linkedin') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="other" class="form-label">Other</label>
                                            <input type="text" name="other" class="form-control" id="other" value="{{ isset($lead->client->other) ? $lead->client->other : ''  }}">
                                            
                                            @if ($errors->has('other'))
                                                <div class="text-danger">{{ $errors->first('other') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-12">
                                            <label for="location" class="form-label">Location</label>
                                            <textarea name="location" id="location" class="form-control" cols="30" rows="2">{{ isset($lead->client->location) ? $lead->client->location : '' }}</textarea>
                                            
                                            @if ($errors->has('location'))
                                                <div class="text-danger">{{ $errors->first('location') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded" style="margin-bottom:15px;">
                            <div class="card" style="margin-bottom:0;">
                                <div class="card-header">
                                    <strong>Lead Attachment</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 box-list" style="border:0;">
                                            @if (!empty($lead->attachments))
                                                @forelse ($lead->attachments as $attachment)
                                                    <div class="remarlist">
                                                        <p>
                                                            <strong>Created By :</strong>{{ $attachment->admin->name ?? '' }}
                                                        </p>
                                                        <p>
                                                            <strong>Date :</strong>{{ $attachment['created_at'] }}
                                                        </p>
                                                        <p>
                                                            <strong>Attachment :</strong>{{ $attachment['attachment'] }}
                                                            <a target="_BLANK" href="{{ $attachment['attachment_url'] }}">View</a>
                                                        </p>
                                                    </div>
                                                @empty
                                                    No Record Found !
                                                @endforelse
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="attachments" class="form-label">Attachment</label>
                                            <input type="file" name="attachments[]" class="form-control attachments" id="attachments" value="{{ old('attachments') }}" multiple {{in_array($lead->status,$status) ? "disabled='disabled'" : ""}}>

                                            @if ($errors->has('attachments'))
                                                <div class="text-danger">{{ $errors->first('attachments') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12" style="margin-top:15px;">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="submit" class="btn btn-success px-5 radius-30">Update</button>
                                </div>

                                <div class="col">
                                    <a href="{{ route('admin.lead.list') }}" class="btn btn-outline-success px-5 radius-30">Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // $(document).ready(function() {
        //     $('.client-dropdown').select2();
        // });

        // $('.client-dropdown').on('change',function(event){
        //     $.ajax({
        //         url : "{{route('admin.client.detail')}}",
        //         type: 'get',
        //         data : {id : event.target.value},
        //         dataType : 'json',
        //         success:function(response){
        //             if(response.status == true){
        //                 $('#mobile').val(response.detail[0].mobile);
        //                $('#email').val(response.detail[0].email);
        //                $('#skype').val(response.detail[0].skype);
        //                $('#linkedin').val(response.detail[0].linkedin);
        //                $('#other').val(response.detail[0].other);
        //             }
        //             if(response.status == false){
        //                 $('#mobile').val("");
        //                $('#email').val("");
        //                $('#skype').val("");
        //                $('#linkedin').val("");
        //                $('#other').val("");
        //             }
        //         }
        //     });
        // });

        function enableFields(){
            document.getElementsByClassName('portal')[0].disabled = false;
            document.getElementsByClassName('project_type')[0].disabled = false;
            document.getElementsByClassName('technology')[0].disabled = false;
            document.getElementsByClassName('status')[0].disabled = false;
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
        }; 

        $(".other").on('change', function(event) {
            event.preventDefault();
            
            let type = event.target.getAttribute('data-type');
            if(event.target.value == "other"){
                $(`.${type}`).val('');
                let url = "";
                if(type == "portal"){
                    url = "{{ route('admin.portal.create') }}";
                }else{
                    url = "{{ route('admin.technology.create') }}";
                }
                jQuery.facebox({ ajax: url });
            }
        });
        
    </script>
@endpush
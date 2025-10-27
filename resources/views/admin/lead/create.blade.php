@extends('admin.layouts.app')
@push('style')
    <style>
        span.select2.select2-container.select2-container--classic {width: 100% !important;}
    </style>
@endpush

@section('content')

    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Create Lead</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.lead.list') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.lead.list') }}">Lead</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Lead</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form action="{{ route('admin.lead.store') }}" method="post" class="row g-3 needs-validation" enctype="multipart/form-data" >
                            @csrf

                            <div class="card">
                                
                                <div class="card-header">
                                    Lead Information
                                </div>

                                <div class="card-body">

                                    <div class="row">
                                        <input type="hidden" name="bidId" value="{{ $bid->id ?? '' }}" />
                                        <input type="hidden" name="bid_date" value="{{ isset($bid->bid_date) ? $bid->bid_date : date('Y-m-d') }}">
                                        <input type="hidden" name="username" value="{{ isset($bid->user_name) ? $bid->user_name : admin()->user()->name }}">
                                        
                                        {{-- <div class="col-md-4">
                                            <label for="validationServer01" class="form-label">Username</label>
                                            <input type="text" name="username" class="form-control" id=""
                                            value="{{ isset($bid->user_name) ? $bid->user_name : admin()->user()->name }}"
                                            placeholder="" readonly>
                                            <div class="valid-feedback">Looks good!</div>
                                            @if ($errors->has('username'))
                                            <div class="text-danger">{{ $errors->first('username') }}</div>
                                            @endif
                                        </div>  --}}

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="job_title" class="form-label">Job Title<span class="text-danger">*</span></label>
                                                <input type="text" name="job_title" class="form-control" id="job_title" value="{{ isset($bid->job_title) ? $bid->job_title : old('job_title') }}" placeholder="Enter Job Title">
                                                
                                                @if ($errors->has('job_title'))
                                                    <div class="text-danger">{{ $errors->first('job_title') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                            
                                        <div class="col-md-4">
                                            <label for="bid_date" class="form-label">Bid Date<span class="text-danger">*</span></label>
                                            <input type="date" name="bid_date" class="form-control" id="bid_date" value="{{ isset($bid->bid_date) ? $bid->bid_date : old('bid_date') }}" {{isset($bid->bid_date) ? 'readonly' : ''}}>
                                            
                                            @if ($errors->has('bid_date'))
                                                <div class="text-danger">{{ $errors->first('bid_date') }}</div>
                                            @endif
                                        </div>
                                        

                                        <div class="col-md-4">
                                            <label for="next_followup" class="form-label">Next Followup<span class="text-danger">*</span></label>
                                            <input type="date" name="next_followup" class="form-control" id="next_followup" value="{{ old('next_followup') }}" min="<?php echo date('Y-m-d'); ?>">
                                            
                                            @if ($errors->has('next_followup'))
                                                <div class="text-danger">{{ $errors->first('next_followup') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="job_link" class="form-label">Job Link<span class="text-danger">*</span></label>
                                            <input type="text" name="job_link" class="form-control" id="job_link" value="{{ isset($bid->job_link) ? $bid->job_link : old('job_link') }}" placeholder="Enter Job Link">
                                            
                                            @if ($errors->has('job_link'))
                                                <div class="text-danger">{{ $errors->first('job_link') }}</div>
                                            @endif

                                            @if (session('job_link'))
                                                <div class="text-danger">{{ session('job_link') }}</div>
                                            @endif

                                        </div>

                                        <div class="col-md-4">
                                            <label for="project_type" class="form-label">Project Type<span class="text-danger">*</span></label>
                                            <select class="form-select" name="project_type" id="project_type">
                                                <option selected disabled value="">Choose...</option>
                                                <option value="hourly" {{ isset($bid->project_type) ? ($bid->project_type == 'hourly' ? 'selected' : '') : (old('project_type') == 'hourly' ? 'selected' : '') }}>Hourly
                                                </option>
                                                
                                                <option value="fixed" {{ isset($bid->project_type) ? ($bid->project_type == 'fixed' ? 'selected' : '') : (old('project_type') == 'fixed' ? 'selected' : '') }}>Fixed
                                                </option>
                                            </select>
                                            
                                            @if ($errors->has('project_type'))
                                                <div class="text-danger">{{ $errors->first('project_type') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="portal" class="form-label">Portal</label>
                                            <select class="form-select portal other" name="portal" id="portal" data-type="portal">
                                                <option selected disabled value="">Choose...</option>
                                                @forelse ($portals as $portal)
                                                    <option value="{{$portal->slug}}" {{ isset($bid->portal) ? ($bid->portal == $portal->slug ? 'selected' : '') :  (old('portal') == $portal->slug ? 'selected' : '') }}>{{$portal->name}}
                                                    </option>
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
                                            <input type="text" name="bid_quote" class="form-control" id="bid_quote" value="{{ isset($bid->bid_quote) ? $bid->bid_quote : old('bid_quote') }}" placeholder="Enter your initial bid">
                                            
                                            @if ($errors->has('bid_quote'))
                                                <div class="text-danger">{{ $errors->first('bid_quote') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="client_budget" class="form-label">Client Budget<span class="text-danger">*</span></label>
                                            <div class="input-group mb-3">
                                                <select class="input-group-text" name="currency" id="">
                                                    <option value="USD" selected>USD</option>
                                                    <option value="INR">INR</option>
                                                </select>

                                                <input type="text" name="client_budget" class="form-control" id="client_budget" value="{{ isset($bid->client_budget) ? $bid->client_budget : old('client_budget') }}" placeholder="Enter your budget" {{isset($bid->id) ? 'readOnly' : ""}}>
                                            </div>
                                            
                                            @if ($errors->has('client_budget'))
                                                <div class="text-danger">{{ $errors->first('client_budget') }}</div>
                                            @endif
                                        </div>

                                        {{-- <div class="col-md-4">
                                            <label for="validationServer01" class="form-label">Profile<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="profile" class="form-control" id=""
                                                value="{{ isset($bid->profile) ? $bid->profile : (old('profile') ? old('profile') : 'BT') }}"
                                                placeholder="">
                                            <div class="valid-feedback">Looks good!</div>
                                            @if ($errors->has('profile'))
                                                <div class="text-danger">{{ $errors->first('profile') }}</div>
                                            @endif
                                        </div> --}}

                                        <div class="col-md-4">
                                            <label for="profile" class="form-label">Profile<span class="text-danger">*</span></label>
            
                                            <select class="form-select profile other" name="profile" id="profile" data-type="profile">
                                                <option selected disabled value="">Choose...</option>
                                                @forelse ( profiles() as $key => $val)
                                                    <option value="{{$key}}" {{ isset($bid->profile) ? ($bid->profile == $key ? 'selected' : "") : (old('profile') == $key ? 'selected' : '' )}}>
                                                    {{$val}}</option>    
                                                @empty 
                                                @endforelse
                                            </select>
                                            
                                            @if ($errors->has('profile'))
                                                <div class="text-danger">{{ $errors->first('profile') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="technology" class="form-label">Project Technology</label>
                                            <select class="form-select technology other" name="technology" id="technology" data-type="technology">
                                                <option selected disabled value="">Choose...</option>
                                                @forelse ($technologies as $technology)
                                                    <option value="{{$technology->slug}}" {{ isset($bid->technology) ? ($bid->technology == $technology->slug ? 'selected' : '') : (old('technology') == $technology->slug ? 'selected' : '') }}>
                                                        {{$technology->name}}
                                                    </option>
                                                @empty
                                                    <option selected value="">No Technology Available</option>
                                                @endforelse
                                                <option value="other">Other</option>
                                            </select>

                                            @if ($errors->has('technology'))
                                                <div class="text-danger">{{ $errors->first('technology') }}</div>
                                            @endif
                                        </div>

                                        @if (empty($bid))
                                            <div class="col-md-4">
                                                <label for="is_invited" class="form-label">Is Invite</label><br>

                                                <!-- Hidden input to ensure a value is always sent -->
                                                <input type="hidden" name="is_invited" value="0">
                                        
                                                <input type="checkbox" class="form-check-input" name="is_invited" id="is_invited" value="1"
                                                {{ old('is_invited') ? (old('is_invited') == 1 ? 'checked' : '') : (isset($bid->is_invited) && $bid->is_invited == 1 ? 'checked' : '') }}>
                                                
                                                <label for="is_invited" class="form-label" style="font-size: 1rem">Invite</label>
                                            </div>
                                        @else
                                            <!-- Hidden input to ensure "is_invited" is always set to 0 when not shown -->
                                            <input type="hidden" name="is_invited" value="0">
                                        @endif
                                        
                                        {{-- <div class="col-md-4">
                                            <label for="validationDefault04" class="form-label">Status<span class="text-danger">*</span></label>
                                            <select class="form-select status other" name="status" id="validationDefault04" data-type="status">
                                                <option selected disabled value="">Choose...</option>
                                                
                                                @foreach ($statusList as $stKey => $value)
                                                <option value="{{ $stKey }}"
                                                    {{ isset($bid->status) ? ($bid->status == $stKey ? 'selected' : '') : (old('status') == $stKey ? 'selected' : ($loop->first ? 'selected' : '')) }}>
                                                    {{ ucfirst($value) }}
                                                </option>
                                            @endforeach     
                                            </select>
                                            @if ($errors->has('status'))
                                                <div class="text-danger">{{ $errors->first('status') }}</div>
                                            @endif
                                        </div> --}}

                                        <div class="col-md-4">
                                            <label for="status" class="form-label">Status<span class="text-danger">*</span></label>
                                            <select class="form-select status other" name="status" id="status" data-type="status" disabled>
                                                <option selected value="open">Open</option>
                                                
                                                @foreach (statusList() as $stKey => $value)
                                                    @if ($stKey != 'open')
                                                        <option value="{{ $stKey }}">{{ ucfirst($value) }}</option>
                                                    @endif
                                                @endforeach     
                                            </select>
                                            <input type="hidden" name="status" value="open">
                                            
                                            @if ($errors->has('status'))
                                                <div class="text-danger">{{ $errors->first('status') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="connects_needed" class="form-label">Connects Needed<span class="text-danger">*</span></label>
                                            <input type="number" name="connects_needed" class="form-control" id="connects_needed" value="{{ isset($bid->connects_needed) ? $bid->connects_needed : old('connects_needed') }}" placeholder="" {{isset($bid->id) ? 'readOnly' : ""}}>
                                            
                                            @if ($errors->has('connects_needed'))
                                                <div class="text-danger">{{ $errors->first('connects_needed') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-12">
                                            <label for="description" class="form-label">Job Description</label>
                                            <textarea name="description" id="description" class="form-control" cols="30" rows="2">{{ isset($bid->description) ? $bid->description : old('description') }}</textarea>
                                            
                                            @if ($errors->has('description'))
                                                <div class="text-danger">{{ $errors->first('description') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="remark" class="form-label">Remarks</label>
                                                <textarea class="form-control" name="remark" id="remark" placeholder="Required example textarea"> {{ old('remark') }}</textarea>
                                            </div>
                                            
                                            @if ($errors->has('remark'))
                                                <div class="text-danger">{{ $errors->first('remark') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    Lead Client Information
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- <div class="col-md-4">
                                            
                                            <label for="validationServer01" class="form-label">Client Name<span
                                                    class="text-danger">*</span></label>
                                            @if(empty(old('client_id')) && empty(old('client_name')) )
                                                <input type="hidden" name="event_type" value="dropdown" class="event_type">

                                                <div class="client-dropdown-div">
                                                    <select name="client_id" class="form-control client-dropdown" id="client_id" class="client_id" {{isset($client_data) && blank($client_data) ? "" : "disabled='disabled'"}}>
                                                        <option value="">Select Client</option>
                                                        @foreach ($clients_list as $client)
                                                            <option
                                                                {{ isset($client_data) && blank($client_data) ? ((old('client_id') == $client['id']) ? 'selected' : "") : (($client_data['id'] == $client['id']) ? 'selected' : "") }}
                                                                value="{{ $client['id'] }}">
                                                                {{ $client['client_name']  }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                           
                                                <div class="client-name-div" style="display:none">
                                                    <input type="text" name="client_name" class="form-control client_name" id="client_name"
                                                    value="{{ old('client_name') }}" placeholder=""  />
                                                </div>
                                            @else
                                                @if (old('client_name') != NULL )
                                                    <input type="hidden" name="event_type" value="input" class="event_type">
                                                @else
                                                    <input type="hidden" name="event_type" value="dropdown" class="event_type">
                                                @endif

                                                <div class="client-dropdown-div" style="display:{{ old('event_type') == 'dropdown' ? 'block' : 'none' }}">
                                                    <select name="client_id" class="form-control client-dropdown" id="client_id" {{isset($client_data) && blank($client_data) ? "" : "disabled='disabled'"}}>
                                                        <option value="">Select Client</option>
                                                        @foreach ($clients_list as $client)
                                                            <option
                                                            {{ isset($client_data) && blank($client_data) ? ((old('client_id') == $client['id']) ? 'selected' : "") : (($client_data['id'] == $client['id']) ? 'selected' : "") }}
                                                            value="{{ $client['id'] }}">
                                                                {{ $client['client_name']  }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            
                                                <div class="client-name-div" style="display:{{ old('event_type') == 'input' ? 'block' : 'none' }}">
                                                    <input type="text" name="client_name" class="form-control client_name" id="client_name"
                                                    value="{{ old('client_name') }}" placeholder=""  />
                                                </div>
                                                 
                                            @endif
                                            
                                            @if ($errors->has('client_id'))
                                                    <div class="text-danger">{{ $errors->first('client_id') }}</div>
                                            @elseif($errors->has('client_name'))
                                                    <div class="text-danger">{{ $errors->first('client_name') }}</div>
                                            @endif 

                                            @if(blank($client_data))
                                                <div>
                                                    @if (empty(old('client_id')) && empty(old('client_name')) )
                                                        <a href="javascript:void(0);" id="add-client-link" onclick="addClient();">+add new client</a>
                                                    @else
                                                    <a href="javascript:void(0);" id="add-client-link" onclick="addClient();">
                                                        {{old('event_type') == 'input' ? 'view clients list' : '+add new client' }}
                                                    </a>
                                                    @endif
                                                </div>
                                            @endif

                                        </div> --}}

                                        <div class="col-md-4">
                                            <label for="client_name" class="form-label">Client Name<span class="text-danger">*</span></label>
                                            <input type="text" name="client_name" class="form-control" id="client_name" value="{{blank($client_data) ? old('client_name') : (empty($client_data->client_name) ? old('client_name') : $client_data->client_name) }}" >
                                            
                                            @if ($errors->has('client_name'))
                                                <div class="text-danger">{{ $errors->first('client_name') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="mobile" class="form-label">Mobile</label>
                                            <input type="number" name="mobile" class="form-control" id="mobile" value="{{blank($client_data) ? old('mobile') : (empty($client_data->mobile) ? old('mobile') : $client_data->mobile) }}">
                                            
                                            @if ($errors->has('mobile'))
                                                <div class="text-danger">{{ $errors->first('mobile') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" id="email" value="{{blank($client_data) ? old('email') : (empty($client_data->email) ? old('email') : $client_data->email) }}" placeholder="">
                                            
                                            @if ($errors->has('email'))
                                                <div class="text-danger">{{ $errors->first('email') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="skype" class="form-label">Skype</label>
                                            <input type="text" name="skype" class="form-control" id="skype" value="{{blank($client_data) ? old('skype') : (empty($client_data->skype) ? old('skype') : $client_data->skype) }}" placeholder="Enter skype">
                                            
                                            @if ($errors->has('skype'))
                                                <div class="text-danger">{{ $errors->first('skype') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="linkedin" class="form-label">Linkedin</label>
                                            <input type="text" name="linkedin" class="form-control" id="linkedin" value="{{blank($client_data) ? old('linkedin') : (empty($client_data->linkedin) ? old('linkedin') : $client_data->linkedin) }}">

                                            @if ($errors->has('linkedin'))
                                                <div class="text-danger">{{ $errors->first('linkedin') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="other" class="form-label">Other</label>
                                            <input type="text" name="other" class="form-control" id="other" value="{{blank($client_data) ? old('other') : (empty($client_data->other) ? old('other') : $client_data->other) }}" placeholder="">
                                            
                                            @if ($errors->has('other'))
                                                <div class="text-danger">{{ $errors->first('other') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-12">
                                            <label for="location" class="form-label">Location</label>
                                            <textarea name="location" id="location" class="form-control" cols="30" rows="2">{{blank($client_data) ? old('location') : (empty($client_data->location) ? old('location') : $client_data->location) }}</textarea>
                                           
                                            @if ($errors->has('location'))
                                                <div class="text-danger">{{ $errors->first('location') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    Lead Attachment
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="attachments" class="form-label">Attachment</label>
                                            <input type="file" name="attachments[]" class="form-control" id="attachments" value="{{ old('attachments') }}" multiple>

                                            @if ($errors->has('attachments'))
                                                <div class="text-danger">{{ $errors->first('attachments') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="row row-cols-auto g-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-success px-5 radius-30">{{ blank($btn) ? 'Create' : $btn}}</button>
                                    </div>

                                    <div class="col">
                                        <a href="{{ route('admin.lead.list') }}" class="btn btn-outline-success px-5 radius-30">Back</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // $(document).ready(function() {
        //     $('.client-dropdown').select2();
        // });

        // $(".client-dropdown").on('change', function(event) {
        //     // let id = event.target.value;
        //     // element = $(this);
        //     $.ajax({
        //         url: '{{ route('admin.client.detail') }}',
        //         type: 'get',
        //         data: {
        //             id: event.target.value
        //         },
        //         dataType: 'json',
        //         success: function(response) {
        //             console.log(response.detail);
        //             if (response.status == true) {
        //                 $('#mobile').val(response.detail[0].mobile);
        //                 $('#email').val(response.detail[0].email);
        //                 $('#skype').val(response.detail[0].skype);
        //                 $('#linkedin').val(response.detail[0].linkedin);
        //                 $('#other').val(response.detail[0].other);
        //                 $('#location').val(response.detail[0].location);
        //             }
        //             if (response.status == false) {
        //                 $('#mobile').val("");
        //                 $('#email').val("");
        //                 $('#skype').val("");
        //                 $('#linkedin').val("");
        //                 $('#other').val("");
        //                 $('#location').val("");
        //             }
        //         }

        //     });
        // });

        // function addClient() {
        //     if($('.event_type').val() == "dropdown"){
        //         $('.client-dropdown-div').attr('style','display: none');
        //         $(".client-dropdown").select2('destroy');
        //         $('.client-dropdown').val('');
        //         $('.client-name-div').attr('style','');
        //         $('.event_type').val("input");
        //         $('#add-client-link').html("view clients list");
        //         $('#mobile').val("");
        //         $('#email').val("");
        //         $('#skype').val("");
        //         $('#linkedin').val("");
        //         $('#other').val("");
        //     }
        //     else{
        //         $('.client-name-div').attr('style','display: none');
        //         $('.event_type').val("dropdown");
        //         $('.client-dropdown-div').attr('style', '');
        //         $(".client-dropdown").select2();
        //         $('#add-client-link').html("+add new client");
        //         $('#mobile').val("");
        //         $('#email').val("");
        //         $('#skype').val("");
        //         $('#linkedin').val("");
        //         $('#other').val("");
        //     }
        // }

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

        // function enableFields(){
        //     document.getElementById('client_id').disabled = false;
        // }
        // function desableEnable() {
        //     if ($("#checkboxId").is(':checked')) {
        //         document.getElementById("TfLroad").disabled = true;

        //     } else {
        //         document.getElementById("TfLroad").disabled = false;

        //     }
        // }
        // desableEnable();
    </script>
@endpush
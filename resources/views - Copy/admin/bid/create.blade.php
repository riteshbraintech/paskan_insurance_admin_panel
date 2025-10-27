@extends('admin.layouts.app')
@section('content')

    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Create Bid</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.bid.list') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{route('admin.bid.list')}}">Bid</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Bid</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form action="{{ route('admin.bid.store') }}" method="post" class="row g-3 needs-validation" enctype="multipart/form-data">
                            @csrf
                            {{-- <div class="col-md-4">
                                <label for="validationServer01" class="form-label">Bid Date</label>
                                <input type="date" name="bid_date" class="form-control" id=""
                                    value="" placeholder="" readonly>
                                <div class="valid-feedback">Looks good!</div>
                                @if ($errors->has('bid_date'))
                                    <div class="text-danger">{{ $errors->first('bid_date') }}</div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="validationCustom03" class="form-label">Username</label>
                                <input type="text" name="username" value="{{admin()->user()->name}}" class="form-control"
                                    id="validationCustom03" readonly>
                                @if ($errors->has('username'))
                                    <div class="text-danger">{{ $errors->first('username') }}</div>
                                @endif
                            </div> --}}

                            <div class="col-md-12">
                                <label for="job_title" class="form-label">Job Title<span class="text-danger">*</span></label>
                                <input type="text" name="job_title" class="form-control" id="job_title" value="{{ old('job_title') }}" placeholder="Enter Job Title">
                                
                                @if ($errors->has('job_title'))
                                    <div class="text-danger">{{ $errors->first('job_title') }}</div>
                                @endif
                            </div>
                            
                            <div class="col-md-4">
                                <label for="job_link" class="form-label">Job Link<span class="text-danger">*</span></label>
                                <input type="text" name="job_link" class="form-control" id="job_link" value="{{ old('job_link') }}" placeholder="Enter Job Link">
                                
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
                                    <option value="hourly" {{ old('project_type') == 'hourly' ? 'selected' : '' }}> Hourly </option>
                                    <option value="fixed" {{ old('project_type') == 'fixed' ? 'selected' : '' }}>Fixed </option>
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
                                        <option value="{{$portal->slug}}" {{ old('portal') == $portal->slug ? 'selected' : '' }}>{{$portal->name}} </option>
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
                                <label for="technology" class="form-label">Project Technology</label>
                                <select class="form-select technology other" name="technology" id="technology" data-type="technology">
                                    <option selected disabled value="">Choose...</option>
                                    @forelse ($technologies as $technology)
                                        <option value="{{$technology->slug}}" {{ old('technology') == $technology->slug ? 'selected' : '' }}>{{$technology->name}}
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

                            <div class="col-md-4">
                                <label for="bid_quote" class="form-label">Bid Quote<span class="text-danger">*</span></label>
                                <input type="text" name="bid_quote" class="form-control" id="bid_quote" value="{{ old('bid_quote') }}" placeholder="Enter your initial bid">
                                
                                @if ($errors->has('bid_quote'))
                                    <div class="text-danger">{{ $errors->first('bid_quote') }}</div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="client_budget" class="form-label">Client Budget<span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    {{-- <span >$</span> --}}
                                    <select class="input-group-text" name="currency" id="">
                                        <option value="USD" selected>USD</option>
                                        <option value="INR">INR</option>
                                    </select>
                                    <input type="text" name="client_budget" class="form-control" id="client_budget" value="{{ old('client_budget') }}" placeholder="Enter your budget">
                                </div>
                                
                                @if ($errors->has('client_budget'))
                                    <div class="text-danger">{{ $errors->first('client_budget') }}</div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="profile" class="form-label">Profile<span class="text-danger">*</span></label>
                                <input type="text" name="profile" class="form-control" id="profile" value="{{ old('profile') ? old('profile') : 'BT' }}" placeholder="">
                                
                                @if ($errors->has('profile'))
                                    <div class="text-danger">{{ $errors->first('profile') }}</div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="connects_needed" class="form-label">Connects Needed<span class="text-danger">*</span></label>
                                <input type="number" name="connects_needed" class="form-control" id="connects_needed" value="{{old('connects_needed')}}" placeholder="">
                                
                                @if ($errors->has('connects_needed'))
                                    <div class="text-danger">{{ $errors->first('connects_needed') }}</div>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">Job Description</label>
                                <textarea name="description" id="description" class="form-control" cols="30" rows="2">{{old('description')}}</textarea>
                               
                                @if ($errors->has('description'))
                                    <div class="text-danger">{{ $errors->first('description') }}</div>
                                @endif
                            </div>

                            {{-- <div class="col-md-4">
                                <label for="validationDefault04" class="form-label">Status<span class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="validationDefault04">
                                    <option selected disabled value="">Choose...</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                                @if ($errors->has('status'))
                                    <div class="text-danger">{{ $errors->first('status') }}</div>
                                @endif
                            </div> --}}

                            {{-- <div class="col-md-6">
                                <label for="validationServer01" class="form-label">Is lead converted</label>
                                <input type="number" name="is_lead_converted" class="form-control" id=""
                                    value="" placeholder="">
                                <div class="valid-feedback">Looks good!</div>
                                @if ($errors->has('is_lead_converted'))
                                    <div class="text-danger">{{ $errors->first('is_lead_converted') }}</div>
                                @endif
                            </div> --}}

                            {{-- <div class="col-md-6">
                                <label for="validationCustom03" class="form-label">Contact Number</label>
                                <div class="d-flex align-items-center contact_number">
                                    <select class="form-control" name="dial_code">
                                        <option value="91">+91</option>
                                        <option value="1">+1</option>
                                        <option value="86">+86</option>
                                    </select>

                                    <input type="tel" name="phone" class="form-control" id="validationCustom03"
                                        value="{{ old('phone') }}" max="12">
                                </div>
                                <div class="invalid-feedback">Please provide your contact number.</div>
                                @if ($errors->has('phone'))
                                    <div class="text-danger">{{ $errors->first('phone') }}</div>
                                @endif
                            </div> --}}
                            {{-- <div class="col-md-6">
                                <label for="validationCustom05" class="form-label">Profile Image</label>
                                <input type="file" name="image" class="form-control" id="validationDefault01"
                                    value="{{ old('image') }}">
                                @if ($errors->has('image'))
                                    <div class="text-danger">{{ $errors->first('image') }}</div>
                                @endif

                            </div> --}}

                            <div class="card">
                                <div class="card-header">Client Information</div>
                                <div class="card-body">
                                    <div class="row">
                                        {{-- <div class="col-md-4">
                                            <label for="validationServer01" class="form-label">Client Name<span
                                                    class="text-danger">*</span></label>
                                            @if(empty(old('client_id')) && empty(old('client_name')))
                                                <input type="hidden" name="event_type" value="dropdown" class="event_type">

                                                <div class="client-dropdown-div">
                                                    <select name="client_id" class="form-control client-dropdown" id="client_id">
                                                        <option value="">Select Client</option>
                                                        @foreach ($clients_list as $client)
                                                            <option
                                                                {{ old('client_id') == $client['id'] ? 'selected' : '' }}
                                                                value="{{ $client['id'] }}">
                                                                {{ $client['client_name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                           
                                                <div class="client-name-div" style="display:none">
                                                    <input type="text" name="client_name" class="form-control client_name" id="client_name"
                                                    value="{{ old('client_name') }}" placeholder=""  />
                                                </div>
                                                
                                            
                                            @else
                                                @if (old('client_name') != NULL)
                                                    <input type="hidden" name="event_type" value="input" class="event_type">
                                                @else
                                                    <input type="hidden" name="event_type" value="dropdown" class="event_type">
                                                @endif

                                                <div class="client-dropdown-div" style="display:{{ old('event_type') == 'dropdown' ? 'block' : 'none' }}">
                                                    <select name="client_id" class="form-control client-dropdown" id="client_id" >
                                                        <option value="">Select Client</option>
                                                        @foreach ($clients_list as $client)
                                                            <option
                                                                {{ old('client_id') == $client['id'] ? 'selected' : '' }}
                                                                value="{{ $client['id'] }}">
                                                                {{ $client['client_name'] }}
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

                                            <div>
                                                @if (empty(old('client_id')) && empty(old('client_name')) )
                                                    <a href="javascript:void(0);" id="add-client-link" onclick="addClient();">+add new client</a>
                                                @else
                                                <a href="javascript:void(0);" id="add-client-link" onclick="addClient();">
                                                    {{old('event_type') == 'input' ? 'view clients list' : '+add new client' }}
                                                </a>
                                                @endif
                                            </div>

                                        </div> --}}

                                        <div class="col-md-4">
                                            <label for="client_name" class="form-label">Client Name </label>
                                            <input type="text" name="client_name" class="form-control" id="client_name" value="{{ old('client_name') }}" placeholder="">
                                            
                                            @if ($errors->has('client_name'))
                                                <div class="text-danger">{{ $errors->first('client_name') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="mobile" class="form-label">Mobile</label>
                                            <input type="number" name="mobile" class="form-control" id="mobile" value="{{ old('mobile') }}" placeholder="">
                                            
                                            @if ($errors->has('mobile'))
                                                <div class="text-danger">{{ $errors->first('mobile') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="">
                                            
                                            @if ($errors->has('email'))
                                                <div class="text-danger">{{ $errors->first('email') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="skype" class="form-label">Skype</label>
                                            <input type="text" name="skype" class="form-control" id="skype" value="{{ old('skype') }}" placeholder="">
                                            
                                            @if ($errors->has('skype'))
                                                <div class="text-danger">{{ $errors->first('skype') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="linkedin" class="form-label">Linkedin</label>
                                            <input type="text" name="linkedin" class="form-control" id="linkedin" value="{{ old('linkedin') }}" placeholder="">
                                            
                                            @if ($errors->has('linkedin'))
                                                <div class="text-danger">{{ $errors->first('linkedin') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <label for="other" class="form-label">Other</label>
                                            <input type="text" name="other" class="form-control" id="other" value="{{ old('other') }}" placeholder="">
                                            
                                            @if ($errors->has('other'))
                                                <div class="text-danger">{{ $errors->first('other') }}</div>
                                            @endif
                                        </div>

                                        <div class="col-md-12">
                                            <label for="location" class="form-label">Location</label>
                                            <textarea name="location" id="location" class="form-control" cols="30" rows="2">{{old('location')}}</textarea>
                                                                                     
                                            @if ($errors->has('location'))
                                                <div class="text-danger">{{ $errors->first('location') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="row row-cols-auto g-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-success px-5 radius-30">Create</button>
                                    </div>

                                    <div class="col">
                                        <a href="{{route('admin.bid.list')}}" class="btn btn-outline-success px-5 radius-30">Back</a>
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
        //         url: "{{ route('admin.client.detail') }}",
        //         type: 'get',
        //         data: {
        //             id: event.target.value
        //         },
        //         dataType: 'json',
        //         success: function(response) {
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

        $(document).on('change','.other', function(event) {
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

        // function desableEnable() {
        //     if($("#checkboxId").is(':checked')){
        //         document.getElementById("TfLroad").disabled = true;

        //     } else {
        //         document.getElementById("TfLroad").disabled = false;

        //     }
        // }
        // desableEnable();

    </script>
@endpush

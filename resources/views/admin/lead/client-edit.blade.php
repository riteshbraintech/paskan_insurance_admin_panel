    <style>
        #facebox {
            top: 78.3px;
            left: 25%;
            width: 50%;
        }

        #facebox .content {
            width: 100%;
        }
    </style>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form action="{{ route('admin.client.update', ['id' => $client->id]) }}" method="post"
                            enctype="multipart/form-data" class="row g-3 needs-validation">
                            @csrf
                            <div class="col-md-4">
                                <label for="client_name" class="form-label">Full name<span class="text-danger">*</span></label>
                                <input type="text" name="client_name" class="form-control" id="client_name" value="{{ $client->client_name }}" required placeholder="Enter Full Name">
                                
                                @if ($errors->has('client_name'))
                                    <div class="text-danger">{{ $errors->first('client_name') }}</div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" value="{{ $client->email }}" class="form-control" id="email" >
                                
                                @if ($errors->has('email'))
                                    <div class="text-danger">{{ $errors->first('email') }}</div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="number" name="mobile" value="{{ $client->mobile }}" class="form-control" id="mobile" >
                                
                                @if ($errors->has('mobile'))
                                    <div class="text-danger">{{ $errors->first('mobile') }}</div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="skype" class="form-label">Skype</label>
                                <input type="text" name="skype" value="{{ $client->skype }}" class="form-control" id="skype" >
                                
                                @if ($errors->has('skype'))
                                    <div class="text-danger">{{ $errors->first('skype') }}</div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="linkedin" class="form-label">LinkedIn</label>
                                <input type="text" name="linkedin" value="{{ $client->linkedin }}" class="form-control" id="linkedin" >
                                
                                @if ($errors->has('linkedin'))
                                    <div class="text-danger">{{ $errors->first('linkedin') }}</div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="other" class="form-label">Other</label>
                                <input type="text" name="other" value="{{ $client->other }}" class="form-control" id="other" >
                                
                                @if ($errors->has('other'))
                                    <div class="text-danger">{{ $errors->first('other') }}</div>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <label for="location" class="form-label">Location</label>
                                <textarea name="location" id="location" class="form-control" cols="30" rows="2">{{$client->location}}</textarea>
                               
                                @if ($errors->has('location'))
                                    <div class="text-danger">{{ $errors->first('location') }}</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <div class="row row-cols-auto g-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-success px-5 radius-30">Update</button>
                                    </div>

                                    {{-- <div class="col">
                                        <a href="#" class="btn btn-outline-success px-5 radius-30">Close</a>
                                    </div> --}}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
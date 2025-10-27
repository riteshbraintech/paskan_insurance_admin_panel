@extends('admin.layouts.app')
@section('content')

    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Edit Client</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.client.list') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{route('admin.client.list')}}">Client Account</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Client</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form action="{{ route('admin.client.update', ['id' => $client->id]) }}" method="post" enctype="multipart/form-data" class="row g-3 needs-validation">
                            @csrf
                            <div class="col-md-6">
                                <label for="client_name" class="form-label">Full name<span class="text-danger">*</span></label>
                                <input type="text" name="client_name" class="form-control" id="client_name" value="{{ $client->client_name }}" required placeholder="Enter Full Name" >
                                @if ($errors->has('client_name'))
                                    <div class="text-danger">{{ $errors->first('client_name') }}</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" value="{{ $client->email }}" class="form-control" id="email" >
                                @if ($errors->has('email'))
                                    <div class="text-danger">{{ $errors->first('email') }}</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="number" name="mobile" value="{{ $client->mobile }}" class="form-control" id="mobile" >
                                @if ($errors->has('mobile'))
                                    <div class="text-danger">{{ $errors->first('mobile') }}</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="skype" class="form-label">Skype</label>
                                <input type="text" name="skype" value="{{ $client->skype }}" class="form-control" id="skype" >
                                @if ($errors->has('skype'))
                                    <div class="text-danger">{{ $errors->first('skype') }}</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="linkedin" class="form-label">LinkedIn</label>
                                <input type="text" name="linkedin" value="{{ $client->linkedin }}" class="form-control" id="linkedin" >
                                @if ($errors->has('linkedin'))
                                    <div class="text-danger">{{ $errors->first('linkedin') }}</div>
                                @endif
                            </div>

                            <div class="col-md-6">
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
                                    <div class="col">
                                        <a href="{{route('admin.client.list')}}" class="btn btn-outline-success px-5 radius-30">Back</a>
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
    </script>
@endpush
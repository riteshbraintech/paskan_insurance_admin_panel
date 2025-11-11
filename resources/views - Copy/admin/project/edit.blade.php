@extends('admin.layouts.app')
@section('content')

    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Edit Project</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.project.index') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{route('admin.project.index')}}">Project</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Project</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form action="{{ route('admin.project.update', ['id' => $project->id]) }}" method="post" enctype="multipart/form-data" class="row g-3 needs-validation">
                            @csrf

                            <div class="col-md-7">
                                <label for="app_name" class="form-label">App Name<span class="text-danger">*</span></label>
                                <input type="text" name="app_name" value="{{old('app_name',$project->app_name)}}" class="form-control" id="app_name">
                                @if ($errors->has('app_name'))
                                    <div class="text-danger">{{ $errors->first('app_name') }}</div>
                                @endif
                            </div>

                            <div class="col-lg-3">
                                <label for="" class="form-label">Select Technology<span class="text-danger">*</span></label>
                                
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="technology[]" id="technology" value="Android(Java)" {{in_array("Android(Java)",old('technology',$project->technology)) ? 'checked' : ''}}>Android(Java)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="technology[]" id="technology" value="iOS(Swift)" {{in_array("iOS(Swift)",old('technology',$project->technology)) ? 'checked' : ''}}>iOS(Swift)
                                    </label>
                                </div>
                                @if ($errors->has('technology'))
                                    <div class="text-danger">{{ $errors->first('technology') }}</div>
                                @endif
                            </div>

                            <div class="col-md-2">
                                <label for="status" class="form-label">Status<span class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="status">
                                    <option selected disabled value="">Choose</option>
                                        <option value="live" {{ old('status',$project->application_status) == 'live' ? 'selected' : '' }}>Live</option>
                                        <option value="dev" {{ old('status',$project->application_status) == "dev" ? 'selected' : '' }}>Dev</option>
                                </select>
                                @if ($errors->has('status'))
                                    <div class="text-danger">{{ $errors->first('status') }}</div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="play_store_url" class="form-label">Playstore URL<span class="text-danger">*</span></label>
                                <input type="text" name="play_store_url" class="form-control" id="play_store_url" value="{{ old('play_store_url',$project->play_store_url) }}" placeholder="">
                                @if ($errors->has('play_store_url'))
                                    <div class="text-danger">{{ $errors->first('play_store_url') }}</div>
                                @endif
                            </div>
                            
                            <div class="col-md-4">
                                <label for="app_store_url" class="form-label">App Store URL<span class="text-danger">*</span></label>
                                <input type="text" name="app_store_url" class="form-control" id="app_store_url" value="{{ old('app_store_url',$project->app_store_url) }}" placeholder="">
                                @if ($errors->has('app_store_url'))
                                    <div class="text-danger">{{ $errors->first('app_store_url') }}</div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="website_url" class="form-label">Web Site URL<span class="text-danger">*</span></label>
                                <input type="text" name="website_url" class="form-control" id="website_url" value="{{ old('website_url',$project->website_url) }}" placeholder="">
                                @if ($errors->has('website_url'))
                                    <div class="text-danger">{{ $errors->first('website_url') }}</div>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" cols="30" rows="2">{{old('description',$project->description)}}</textarea>
                                @if ($errors->has('description'))
                                    <div class="text-danger">{{ $errors->first('description') }}</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <div class="row row-cols-auto g-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-success px-5 radius-30">Update</button>
                                    </div>

                                    <div class="col">
                                        <a href="{{route('admin.project.index')}}" class="btn btn-outline-success px-5 radius-30">Back</a>
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
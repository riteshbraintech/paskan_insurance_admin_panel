@extends('admin.layouts.app')
@section('content')

    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Create CMS Page</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.cmspage.index') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{route('admin.cmspage.index')}}">CMS Pages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create CMS Page</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form action="{{ route('admin.cmspage.store') }}" method="post" class="row g-3 needs-validation" enctype="multipart/form-data">
                            @csrf

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @foreach (langueses() as $langCode => $language)
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Title ({{$language}}) <span class="text-danger">*</span></label>
                                    <input type="text" name="trans[{{$langCode}}][title]" class="form-control" id="title" value="{{ old('trans.'.$langCode.'.title') }}" placeholder=" {{ __('Enter Title in') .' '. $language }}">
                                    @if ($errors->has('trans.'.$langCode.'.title'))
                                        <div class="text-danger">{{ $errors->first('trans.'.$langCode.'.title') }}</div>
                                    @endif
                                </div>                                
                            @endforeach

                            @foreach (langueses() as $langCode => $language)
                                <div class="col-md-12">
                                    <label for="content" class="form-label">Content ({{$language}}) </label>
                                    <textarea name="trans[{{$langCode}}][content]" id="trans_{{ $langCode }}_content" class="form-control" cols="30" rows="2">{{old('trans.'.$langCode.'.content')}}</textarea>
                                    @if ($errors->has('trans.'.$langCode.'.content'))
                                        <div class="text-danger">{{ $errors->first('trans.'.$langCode.'.content') }}</div>
                                    @endif
                                </div>
                            @endforeach

                            
                            @foreach (langueses() as $langCode => $language)
                                <div class="col-md-4">
                                    <label for="meta_title" class="form-label">{{ __('Meta Title')}} ({{$language}})</label>
                                    <input type="text" name="trans[{{$langCode}}][meta_title]]" class="form-control" id="meta_title" value="{{ old('trans.'.$langCode.'.meta_title') }}" placeholder=" {{ __('Enter meta title in') .' '. $language }}">
                                    @if ($errors->has('trans.'.$langCode.'.meta_title'))
                                        <div class="text-danger">{{ $errors->first('trans.'.$langCode.'.meta_title') }}</div>
                                    @endif
                                </div>    
                                <div class="col-md-4">
                                    <label for="meta_keywords" class="form-label">{{ __('Meta keywords')}} ({{$language}})</label>
                                    <input type="text" name="trans[{{$langCode}}][meta_keywords]]" class="form-control" id="meta_keywords" value="{{ old('trans.'.$langCode.'.meta_keywords') }}" placeholder=" {{ __('Enter meta keywords in') .' '. $language }}">
                                    @if ($errors->has('trans.'.$langCode.'.meta_keywords'))
                                        <div class="text-danger">{{ $errors->first('trans.'.$langCode.'.meta_keywords') }}</div>
                                    @endif
                                </div>    
                                <div class="col-md-4">
                                    <label for="meta_description" class="form-label">{{ __('Meta Description')}} ({{$language}})</label>
                                    <input type="text" name="trans[{{$langCode}}][meta_description]]" class="form-control" id="meta_description" value="{{ old('trans.'.$langCode.'.meta_description') }}" placeholder=" {{ __('Enter meta description in') .' '. $language }}">
                                    @if ($errors->has('trans.'.$langCode.'.meta_description'))
                                        <div class="text-danger">{{ $errors->first('trans.'.$langCode.'.meta_description') }}</div>
                                    @endif
                                </div>                                
                            @endforeach


                            <div class="col-12">
                                <div class="row row-cols-auto g-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-success px-5 radius-30">Create</button>
                                    </div>

                                    <div class="col">
                                        <a href="{{route('admin.cmspage.index')}}" class="btn btn-outline-success px-5 radius-30">Back</a>
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
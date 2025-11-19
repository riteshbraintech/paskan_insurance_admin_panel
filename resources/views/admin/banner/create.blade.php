@extends('admin.layouts.app')
@section('content')
    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Create Banner</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.banner.index') }}"><i
                                class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a
                            href="{{ route('admin.banner.index') }}">Banner Pages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Banner</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form action="{{ route('admin.banner.store') }}" method="post" class="row g-3 needs-validation"
                            enctype="multipart/form-data">
                            @csrf

                            @foreach (langueses() as $langCode => $language)
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Title ({{ $language }}) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="trans[{{ $langCode }}][title]" class="form-control"
                                        id="title" value="{{ old('trans.' . $langCode . '.title') }}"
                                        placeholder=" {{ __('Enter Title in') . ' ' . $language }}">
                                    @if ($errors->has('trans.' . $langCode . '.title'))
                                        <div class="text-danger">{{ $errors->first('trans.' . $langCode . '.title') }}</div>
                                    @endif
                                </div>
                            @endforeach

                            @foreach (langueses() as $langCode => $language)
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Sub Title ({{ $language }}) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="trans[{{ $langCode }}][sub_title]" class="form-control"
                                        id="sub_title" value="{{ old('trans.' . $langCode . '.sub_title') }}"
                                        placeholder=" {{ __('Enter Sub Title in') . ' ' . $language }}">
                                    @if ($errors->has('trans.' . $langCode . '.sub_title'))
                                        <div class="text-danger">{{ $errors->first('trans.' . $langCode . '.sub_title') }}</div>
                                    @endif
                                </div>
                            @endforeach

                            @foreach (langueses() as $langCode => $language)
                                <div class="col-md-12">
                                    <label for="description" class="form-label">Description ({{ $language }}) </label>
                                    <textarea name="trans[{{ $langCode }}][description]" id="trans_{{ $langCode }}_description"
                                        class="form-control" cols="30" rows="5">{{ old('trans.' . $langCode . '.description') }}</textarea>
                                    @if ($errors->has('trans.' . $langCode . '.description'))
                                        <div class="text-danger">{{ $errors->first('trans.' . $langCode . '.description') }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <div class="col-md-12">
                                <label for="image" class="form-label mt-4">image</label>
                                <input type="file" name="image" class="form-control" id="image">
                            </div>


                            <div class="col-12">
                                <div class="row row-cols-auto g-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-success px-5 radius-30">Create</button>
                                    </div>

                                    <div class="col">
                                        <a href="{{ route('admin.categories.index') }}"
                                            class="btn btn-outline-success px-5 radius-30">Back</a>
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

{{-- @push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @foreach (langueses() as $langCode => $language)
                ClassicEditor
                    .create(document.querySelector('#trans_{{ $langCode }}_description'))
                    .then(editor => {
                        editor.ui.view.editable.element.style.minHeight = '200px';
                    })
                    .catch(error => console.error(error));
            @endforeach
        });
    </script>
@endpush --}}

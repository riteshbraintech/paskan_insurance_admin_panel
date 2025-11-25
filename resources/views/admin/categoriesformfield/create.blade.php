@extends('admin.layouts.app')
@section('content')
    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Create Category Form Field</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.categoryformfield.index') }}"><i
                                class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a
                            href="{{ route('admin.categoryformfield.index') }}">Category Form Field Pages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Category Form Field</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form action="{{ route('admin.categoryformfield.store') }}" method="POST"
                            class="row g-3 needs-validation" enctype="multipart/form-data">
                            @csrf
                            {{-- Category Dropdown --}}
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Select Category <span
                                        class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-control">
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', request()->get('catId')) == $category->id ? 'selected' : '' }}>
                                            {{ $category->translation->title ?? 'Unnamed' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Main Parent Dropdown --}}
                            <div class="col-md-6">
                                <label for="parent_field_id" class="form-label">Select Parent Form Question <span
                                        class="text-danger">*</span></label>
                                <select name="parent_field_id" id="parent_field_id" class="form-control">
                                    <option value="">-- Select Parent Form Question --</option>
                                    @foreach ($parentQuestion as $que)
                                        <option value="{{ $que->id }}"
                                            {{ old('parent_field_id') == $que->id ? 'selected' : '' }}>
                                            {{ $que->translation->label ?? 'Unnamed' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_field_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Multilingual fields --}}

                            @foreach (langueses() as $langCode => $language)
                                <div class="col-md-6">
                                    <label for="label_{{ $langCode }}" class="form-label">Label ({{ $language }})
                                        <span class="text-danger">*</span></label>
                                    <input type="text" name="trans[{{ $langCode }}][label]" class="form-control"
                                        id="label_{{ $langCode }}" value="{{ old('trans.' . $langCode . '.label') }}"
                                        placeholder="Enter label in {{ $language }}">
                                    @if ($errors->has('trans.' . $langCode . '.label'))
                                        <div class="text-danger">{{ $errors->first('trans.' . $langCode . '.label') }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            @foreach (langueses() as $langCode => $language)
                                <div class="col-md-6">
                                    <label for="place_holder_{{ $langCode }}" class="form-label">Place Holder
                                        ({{ $language }})
                                        <span class="text-danger">*</span></label>
                                    <input type="text" name="trans[{{ $langCode }}][place_holder]"
                                        class="form-control" id="place_holder_{{ $langCode }}"
                                        value="{{ old('trans.' . $langCode . '.place_holder') }}"
                                        placeholder="Enter place_holder in {{ $language }}">
                                    @if ($errors->has('trans.' . $langCode . '.place_holder'))
                                        <div class="text-danger">
                                            {{ $errors->first('trans.' . $langCode . '.place_holder') }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach


                            {{-- Field info --}}
                            <div class="col-md-4">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name"
                                    value="{{ old('name') }}" placeholder="Enter unique name">
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            @php
                                $oldTypes = old('type', []);
                            @endphp

                            <div class="col-md-4">
                                <label for="type" class="form-label">
                                    Field Type <span class="text-danger">*</span>
                                </label>
                                @php
                                    $fieldArry = ['text', 'number', 'select','checkbox','radio','textarea'];
                                @endphp
                                <select name="type" id="type" class="form-select">
                                    <option value="">-- Select Type --</option>
                                    @foreach ($fieldArry  as $fieldname)
                                        <option value="{{$fieldname}}" {{ old('type') == $fieldname ? 'selected' : '' }}>
                                            {{ ucfirst($fieldname)}}
                                        </option>
                                    @endforeach
                                </select>

                                @error('type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="is_required" class="form-label">
                                    Is Required ? <span class="text-danger">*</span>
                                </label>
                                @php
                                    $fieldArry = [0=>'No', 1=>'Yes'];
                                @endphp
                                <select name="is_required" id="is_required" class="form-control">
                                    <option value="">-- Select Type --</option>
                                    @foreach ($fieldArry  as $key => $fieldname)
                                        <option value="{{$key}}" {{ old('is_required') == $key ? 'selected' : '' }}>
                                            {{ ucfirst($fieldname)}}
                                        </option>
                                    @endforeach
                                </select>
                                @error('is_required')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Buttons --}}
                            <div class="col-12">
                                <div class="row row-cols-auto g-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-success px-5 radius-30">Create</button>
                                    </div>

                                    <div class="col">
                                        <a href="{{ route('admin.categoryformfield.index') }}"
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

@push('scripts')
    <script>
        $(document).ready(function() {
            // Function to toggle visibility based on selected type
            function toggleOptions() {
                const types = $('#type').val();
                if (types && (types.includes('select') || types.includes('radio') || types.includes('checkbox'))) {
                    $('.options-wrapper').show();
                    $('#imageOptionsWrapper').show();
                } else {
                    $('.options-wrapper').hide();
                    $('#imageOptionsWrapper').hide();
                }
            }

            toggleOptions();

            $('#type').on('change', toggleOptions);

            // Image preview for selected files
            // Image preview with delete button
            let selectedFiles = [];

            $('#optionImagesInput').on('change', function(e) {
                const files = Array.from(e.target.files);

                files.forEach(file => {
                    selectedFiles.push(file);

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const imgWrapper = $('<div>').css({
                            'position': 'relative',
                            'width': '80px',
                            'height': '80px'
                        });

                        const img = $('<img>').attr('src', event.target.result)
                            .css({
                                'width': '100%',
                                'height': '100%',
                                'object-fit': 'cover',
                                'border': '1px solid #ccc',
                                'border-radius': '5px'
                            });

                        const removeBtn = $('<span>')
                            .html('&times;')
                            .css({
                                'position': 'absolute',
                                'top': '-5px',
                                'right': '-5px',
                                'background': '#ff000000',
                                'color': 'black',
                                'border-radius': '50%',
                                'padding': '2px 6px',
                                'cursor': 'pointer',
                                'font-weight': 'bold'
                            })
                            .on('click', function() {
                                const index = imgWrapper.index();
                                selectedFiles.splice(index, 1);
                                imgWrapper.remove();
                                updateFileInput();
                            });

                        imgWrapper.append(img).append(removeBtn);
                        $('#imagePreview').append(imgWrapper);
                    };
                    reader.readAsDataURL(file);
                });

                updateFileInput();
            });

            // Update the file input so it reflects selectedFiles array
            function updateFileInput() {
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                document.getElementById('optionImagesInput').files = dataTransfer.files;
            }


            // handle on changes and reload all the data
            $('#category_id').on('change', function () {
                let value = $(this).val();

                const params = new URLSearchParams(window.location.search);

                // Add/update parameters
                params.set("catId", value); 
                // Build updated URL
                const newUrl = window.location.pathname + '?' + params.toString();

                // Redirect to new URL
                window.location.href = newUrl;
            });


        });
    </script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#type').select2({
                placeholder: "Select field types",
                width: '100%'
            });
        });
    </script>
@endpush

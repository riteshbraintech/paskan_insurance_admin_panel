@extends('admin.layouts.app')

@section('content')
    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Edit Category Form Field</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.categoryformfield.index') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.categoryformfield.index') }}">Category Form Fields</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">

                        <form action="{{ route('admin.categoryformfield.update', $record->id) }}" method="POST"
                            enctype="multipart/form-data" class="row g-3">

                            @csrf

                            {{-- ==================== LABEL (MULTI-LANG) ==================== --}}
                            @foreach (langueses() as $lang => $language)
                                <input type="hidden" name="trans[{{ $lang }}][id]"
                                    value="{{ $translations[$lang]['id'] ?? '' }}">

                                <div class="col-md-6">
                                    <label class="form-label">Label ({{ $language }}) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="trans[{{ $lang }}][label]"
                                        value="{{ old("trans.$lang.label", $translations[$lang]['label'] ?? '') }}">
                                    
                                        @if ($errors->has('trans.' . $lang . '.label'))
                                        <div class="text-danger">{{ $errors->first('trans.' . $lang . '.label') }}
                                        </div>
                                    @endif

                                </div>
                            @endforeach


                            {{-- ==================== PLACEHOLDER (MULTI-LANG) ==================== --}}
                            @foreach (langueses() as $lang => $language)
                                <div class="col-md-6">
                                    <label class="form-label">Placeholder ({{ $language }})</label>
                                    <input type="text" class="form-control"
                                        name="trans[{{ $lang }}][place_holder]"
                                        value="{{ old("trans.$lang.place_holder", $translations[$lang]['place_holder'] ?? '') }}">
                                </div>
                            @endforeach

                            {{-- ==================== Short Description (MULTI-LANG) ==================== --}}
                            @foreach (langueses() as $lang => $language)
                                <div class="col-md-6">
                                    <label class="form-label">Short Desription ({{ $language }})</label>
                                    <input type="text" class="form-control"
                                        name="trans[{{ $lang }}][short_description]"
                                        value="{{ old("trans.$lang.short_description", $translations[$lang]['short_description'] ?? '') }}">
                                </div>
                            @endforeach


                            {{-- ==================== FIELD NAME ==================== --}}
                            <div class="col-md-4">
                                <label class="form-label">Field Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ old('name', $record->name) }}">
                                
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror

                            </div>


                            {{-- ==================== FIELD TYPE ==================== --}}
                            <div class="col-md-4">
                                <label class="form-label">Field Type <span class="text-danger">*</span></label>
                                @php
                                    $fieldArry = ['text', 'number', 'select','checkbox','radio','textarea'];
                                @endphp
                                <select name="type" id="type" class="form-select">
                                    <option value="">-- Select Type --</option>
                                    @foreach ($fieldArry  as $fieldname)
                                        <option value="{{$fieldname}}" {{ old('type', $record->type) == $fieldname ? 'selected' : '' }}>
                                            {{ ucfirst($fieldname)}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Main Parent Dropdown --}}
                            <div class="col-md-4">
                                <label for="parent_field_id" class="form-label">Select Parent Form Question <span
                                        class="text-danger">*</span></label>
                                <select name="parent_field_id" id="parent_field_id" class="form-control">
                                    <option value="">-- Select Parent Form Question --</option>
                                    @foreach ($parentQuestion as $que)
                                        <option value="{{ $que->id }}"
                                            {{ old('parent_field_id', $record->parent_field_id) == $que->id ? 'selected' : '' }}>
                                            {{ $que->translation->label ?? 'Unnamed' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_field_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-12 mt-2">
                                <button class="btn btn-success px-5">Update</button>
                                <a href="{{ route('admin.categoryformfield.index') }}"
                                    class="btn btn-secondary px-5">Back</a>
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

            function toggleFields() {
                const type = $('#type').val();

                $('#textOptionsWrapper').hide();
                $('#imageOptionsWrapper').hide();

                if (['select', 'radio', 'checkbox'].includes(type)) {
                    $('#textOptionsWrapper').show();
                    $('#imageOptionsWrapper').show();
                }
            }

            $('#type').on('change', toggleFields);
            toggleFields();


            // -------------------- New Image Preview --------------------
            let selectedFiles = [];

            $('#optionImagesInput').on('change', function(e) {
                const files = Array.from(e.target.files);

                files.forEach(file => {
                    selectedFiles.push(file);

                    const reader = new FileReader();
                    reader.onload = (event) => {
                        const wrapper = $(`
                    <div class="position-relative image-box">
                        <img src="${event.target.result}"
                             class="img-thumbnail"
                             style="width:80px;height:80px;object-fit:cover;">
                        <button type="button" class="remove-new-image btn btn-danger btn-sm"
                                style="position:absolute; top:-8px; right:-8px; border-radius:50%; padding:2px 6px;">
                            &times;
                        </button>
                    </div>
                `);

                        wrapper.find('.remove-new-image').click(function() {
                            const index = wrapper.index();
                            selectedFiles.splice(index, 1);
                            updateFileInput();
                            wrapper.remove();
                        });

                        $('#imagePreview').append(wrapper);
                    };
                    reader.readAsDataURL(file);
                });

                updateFileInput();
            });

            function updateFileInput() {
                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));
                document.getElementById('optionImagesInput').files = dt.files;
            }


            // -------------------- Delete Existing Image --------------------
            $(document).on('click', '.remove-existing-image', function() {
                const imgPath = $(this).data('img');

                // Add hidden input for backend to delete
                $('<input>', {
                    type: 'hidden',
                    name: 'remove_images[]',
                    value: imgPath
                }).appendTo('form');

                $(this).closest('.image-box').remove();
            });


        });
    </script>
@endpush

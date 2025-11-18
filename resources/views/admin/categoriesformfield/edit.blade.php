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


                            {{-- ==================== FIELD NAME ==================== --}}
                            <div class="col-md-4">
                                <label class="form-label">Field Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name"
                                    value="{{ old('name', $record->name) }}">
                            </div>


                            {{-- ==================== FIELD TYPE ==================== --}}
                            <div class="col-md-4">
                                <label class="form-label">Field Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control">
                                    <option value="text" {{ $record->type == 'text' ? 'selected' : '' }}>Text</option>
                                    <option value="number" {{ $record->type == 'number' ? 'selected' : '' }}>Number
                                    </option>
                                    <option value="select" {{ $record->type == 'select' ? 'selected' : '' }}>Select
                                    </option>
                                    <option value="checkbox" {{ $record->type == 'checkbox' ? 'selected' : '' }}>Checkbox
                                    </option>
                                    <option value="radio" {{ $record->type == 'radio' ? 'selected' : '' }}>Radio</option>
                                    <option value="textarea" {{ $record->type == 'textarea' ? 'selected' : '' }}>Textarea
                                    </option>
                                </select>
                            </div>


                            {{-- ==================== OPTIONS (MULTI-LANG) ==================== --}}
                            <div id="textOptionsWrapper" class="col-md-12" style="display:none;">
                                @foreach (langueses() as $lang => $language)
                                    <div class="mb-3">
                                        <label class="form-label">Options ({{ $language }}) </label>
                                        <textarea name="trans[{{ $lang }}][options]" class="form-control">{{ old("trans.$lang.options", $translations[$lang]['options'] ?? '') }}</textarea>
                                    </div>
                                @endforeach
                            </div>


                            {{-- ==================== IMAGE OPTIONS ==================== --}}
                            <div id="imageOptionsWrapper" class="col-md-12" style="display:none;">
                                <label class="form-label">Upload Option Images</label>
                                <input type="file" name="option_images[]" multiple class="form-control mb-2"
                                    id="optionImagesInput">

                                <small class="text-muted">
                                    The number of images must match number of options (primary language).
                                </small>

                                {{-- NEW PREVIEWS --}}
                                <div id="imagePreview" class="d-flex flex-wrap gap-2 mt-2"></div>


                                {{-- EXISTING IMAGES --}}
                                @if (!empty($record->images))
                                    @php
                                        $images = json_decode($record->images, true);
                                    @endphp

                                    <div class="d-flex flex-wrap gap-2 mt-2" id="existingImages">
                                        @foreach ($images as $img)
                                            <div class="position-relative image-box">
                                                <img src="{{ asset('public/' . $img) }}" class="img-thumbnail"
                                                    style="width:80px;height:80px;object-fit:cover;">

                                                <button type="button" class="remove-existing-image btn btn-danger btn-sm"
                                                    data-img="{{ $img }}"
                                                    style="position:absolute; top:-8px; right:-8px; border-radius:50%; padding:2px 6px;">
                                                    &times;
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

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

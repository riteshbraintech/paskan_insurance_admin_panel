@extends('admin.layouts.app')

@section('content')
    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Edit Category Form Field</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.categoryformfield.index') }}"><i
                                class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('admin.categoryformfield.index') }}">Category form
                            Field Pages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Category Form Field</li>
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
                            class="row g-3 needs-validation" enctype="multipart/form-data">
                            @csrf
                            {{-- @method('PUT') --}}

                            {{-- Category --}}
                            {{-- <div class="col-md-12">
                                <label class="form-label">Select Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                        @php
                                            $lang = app()->getLocale() ?? 'en';
                                            $translation =
                                                $category->translations->where('lang_code', $lang)->first() ??
                                                $category->translations->first();
                                            $categoryName = $translation->title ?? ($category->title ?? 'Unnamed');
                                        @endphp
                                        <option value="{{ $category->id }}"
                                            {{ $record->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $categoryName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}

                            {{-- Multilingual Title --}}
                            @foreach (langueses() as $langCode => $language)
                                @php
                                    $labelValue = old("trans.$langCode.label", $translations[$langCode]['label'] ?? '');
                                    $idValue = $translations[$langCode]['id'] ?? '';
                                @endphp

                                <input type="hidden" name="trans[{{ $langCode }}][id]" class="form-control"
                                    value="{{ $idValue }}" />

                                <div class="col-md-6">
                                    <label class="form-label">Label ({{ $language }}) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="trans[{{ $langCode }}][label]" class="form-control"
                                        value="{{ $labelValue }}" placeholder="Enter Label in {{ $language }}">
                                    @error("trans.$langCode.label")
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach

                            @foreach (langueses() as $langCode => $language)
                                @php
                                    $placeholderValue = old(
                                        "trans.$langCode.place_holder",
                                        $translations[$langCode]['place_holder'] ?? '',
                                    );
                                @endphp
                                <div class="col-md-12">
                                    <label class="form-label">Place Holder ({{ $language }})</label>
                                    <textarea name="trans[{{ $langCode }}][place_holder]" id="trans_{{ $langCode }}_place_holder"
                                        class="form-control" rows="2">{{ $placeholderValue }}</textarea>
                                    @error("trans.$langCode.place_holder")
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach

                            {{-- Single-language fields --}}
                            <div class="col-md-4">
                                <label class="form-label">Field Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $record->name) }}">
                            </div>

                            @php
                                // Decode stored JSON field type
                                $storedTypes = json_decode($record->type, true) ?? [];
                                $oldTypes = old('type', $storedTypes);
                            @endphp

                            <div class="col-md-4">
                                <label for="type" class="form-label">
                                    Field Type <span class="text-danger">*</span>
                                </label>

                                <select name="type[]" id="type" class="form-select" multiple>
                                    <option value="text" {{ in_array('text', $oldTypes) ? 'selected' : '' }}>Text
                                    </option>
                                    <option value="number" {{ in_array('number', $oldTypes) ? 'selected' : '' }}>Number
                                    </option>
                                    <option value="select" {{ in_array('select', $oldTypes) ? 'selected' : '' }}>Select
                                    </option>
                                    <option value="checkbox" {{ in_array('checkbox', $oldTypes) ? 'selected' : '' }}>
                                        Checkbox</option>
                                    <option value="radio" {{ in_array('radio', $oldTypes) ? 'selected' : '' }}>Radio
                                    </option>
                                    <option value="textarea" {{ in_array('textarea', $oldTypes) ? 'selected' : '' }}>
                                        Textarea</option>
                                </select>

                                @error('type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- <div class="col-md-4">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control"
                                    value="{{ old('sort_order', $record->sort_order ?? 0) }}">
                            </div> --}}


                            {{-- Multilingual Options (for select/radio/checkbox) --}}
                            @foreach (langueses() as $langCode => $language)
                                @php
                                    $optionsValue = old(
                                        "trans.$langCode.options",
                                        $translations[$langCode]['options'] ?? '',
                                    );
                                @endphp
                                <div class="col-md-12 options-group">
                                    <label class="form-label">Options ({{ $language }})</label>
                                    <textarea name="trans[{{ $langCode }}][options]" id="trans_{{ $langCode }}_options" class="form-control"
                                        rows="2">{{ $optionsValue }}</textarea>
                                    @error("trans.$langCode.options")
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach




                            <div class="col-12">
                                <div class="row row-cols-auto g-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-success px-5 radius-30">Update</button>
                                    </div>
                                    <div class="col">
                                        <a href="{{ route('admin.categoryformfield.index') }}"
                                            class="btn btn-outline-secondary px-5 radius-30">Back</a>
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
    {{-- <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @foreach (langueses() as $langCode => $language)
                ClassicEditor
                    .create(document.querySelector('#trans_{{ $langCode }}_description'))
                    .catch(error => console.error(error));
            @endforeach
        });
    </script> --}}

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <script>
            $(document).ready(function() {
                $('#type').select2({
                    placeholder: "Select field types",
                    width: '100%'
                });

                function toggleOptions() {
                    const types = $('#type').val() || [];
                    if (types.includes('select') || types.includes('radio') || types.includes('checkbox')) {
                        $('.options-group').show();
                    } else {
                        $('.options-group').hide();
                    }
                }

                toggleOptions();
                $('#type').on('change', toggleOptions);
            });
        </script>
    @endpush

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
                            <div class="col-md-12">
                                <label for="category_id" class="form-label">Select Category <span
                                        class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-control">
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                        @php
                                            // Detect current language (or set default)
                                            $lang = app()->getLocale() ?? 'en';

                                            // Try to fetch translation for the current language
                                            $translation = $category->translations->where('lang_code', $lang)->first();

                                            // If not found, fall back to the first translation available
                                            if (!$translation) {
                                                $translation = $category->translations->first();
                                            }
                                            $categoryName = $translation->title ?? ($category->title ?? 'Unnamed');
                                        @endphp

                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $categoryName }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
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

                            {{-- Multiple Select Checkbox --}}
                            {{-- <div class="col-md-12 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_multiple" id="is_multiple"
                                        value="1" {{ old('is_multiple') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_multiple">
                                        Multiple Select
                                    </label>
                                </div>
                            </div> --}}

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

                                {{-- Dropdown-style multiselect --}}
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
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" id="sort_order"
                                    value="{{ old('sort_order', 0) }}">
                            </div> --}}

                            {{-- Options per language (for select/radio/checkbox) --}}
                            <div class="options-wrapper">
                                @foreach (langueses() as $langCode => $language)
                                    <div class="col-md-12 mb-3">
                                        <label for="options_{{ $langCode }}" class="form-label">
                                            Options ({{ $language }})
                                        </label>
                                        <textarea name="trans[{{ $langCode }}][options]" id="options_{{ $langCode }}" class="form-control"
                                            rows="2" placeholder='["Option1","Option2"]'>{{ old('trans.' . $langCode . '.options') }}</textarea>

                                        @if ($errors->has('trans.' . $langCode . '.options'))
                                            <div class="text-danger">
                                                {{ $errors->first('trans.' . $langCode . '.options') }}</div>
                                        @endif
                                    </div>
                                @endforeach
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
                } else {
                    $('.options-wrapper').hide();
                }
            }

            toggleOptions();

            $('#type').on('change', toggleOptions);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('type');
            const display = document.getElementById('selectedTypes');

            function updateSelected() {
                const selected = Array.from(select.selectedOptions).map(opt => opt.text);
                display.value = selected.join(', ') || 'No types selected';
            }

            // Update on change
            select.addEventListener('change', updateSelected);

            // Initialize on page load
            updateSelected();
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

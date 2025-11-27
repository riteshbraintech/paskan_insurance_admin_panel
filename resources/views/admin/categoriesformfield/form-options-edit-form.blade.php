<form action="{{ route('admin.categoryformfield.store') }}" method="POST"   class="row g-3 needs-validation" enctype="multipart/form-data">
    @csrf
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


    {{-- Field info --}}
    <div class="col-md-4">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" id="name"
            value="{{ old('name') }}" placeholder="Enter unique name">
        @error('name')
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
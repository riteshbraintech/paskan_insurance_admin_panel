@extends('admin.layouts.app')

@section('content')
    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Update Form Field Options</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.categoryformfield.index') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.categoryformfield.index') }}">Category Form Fields</a>
                    </li>
                    <li class="breadcrumb-item active">Update Form Field Options</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">

                        <form action="{{ route('admin.categoryformfieldoptions.optionstore') }}" method="POST"
                            enctype="multipart/form-data" class="row g-3">

                            @csrf

                            {{-- <input type="hidden" id="field_id" name="field_id" value="{{ $parent_field_id }}"> --}}
                            <input type="hidden" id="field_id" value="{{ $mainForm->id }}">

                            <div class="col-md-12 d-flex justify-content-between">
                                <p class="">
                                    <b>Question:</b> {{ $mainForm->translation->label }}
                                </p>
                                @if ($mainForm->parent)
                                    <p>
                                        <b> >>> Parent Question:</b> {{ $mainForm->parent->translation->label }}
                                    </p>
                                @endif
                            </div>


                            <button id="addRowBtn" type="button" class="btn btn-primary mb-2">Add Option</button>

                            <table class="table table-bordered" id="optionsTable">
                                <thead>
                                    <tr>
                                        @foreach (langueses() as $langCode => $language)
                                            <th class="no-wrap">Label ({{ $language }}) </th>
                                        @endforeach
                                        <th>Image</th>
                                        <th>Value</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <!-- Existing rows loaded from DB -->
                                    {{-- @foreach ($options as $option)
                                    <tr data-id="{{ $option->id }}">
                                        <td>{{ $option->label_en }}</td>
                                        <td>{{ $option->label_hi }}</td>
                                        <td>
                                            @if ($option->image)
                                                <img src="{{ asset('uploads/'.$option->image) }}" style="width:40px;height:40px;">
                                            @endif
                                        </td>
                                        <td>{{ $option->value }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning editRow">Edit</button>
                                            <button class="btn btn-sm btn-danger deleteRow">Delete</button>
                                        </td>
                                    </tr>
                                    @endforeach --}}
                                </tbody>
                            </table>

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

            // Add New Row
            $("#addRowBtn").click(function() {
                let newRow = `
        <tr class="editing">
            <td><input type="text" class="form-control label_en"></td>
            <td><input type="text" class="form-control label_hi"></td>
            <td><input type="file" class="form-control image" multiple></td>
            <td><input type="text" class="form-control value"></td>
            <td>
                <button class="btn btn-sm btn-success saveRow">Save</button>
                <button class="btn btn-sm btn-secondary cancelRow">Cancel</button>
            </td>
        </tr>
        `;
                $("#optionsTable tbody").append(newRow);
            });

            // Cancel
            $(document).on("click", ".cancelRow", function() {
                $(this).closest("tr").remove();
            });

            // Save New Row
            $(document).on("click", ".saveRow", function(e) {
                e.preventDefault(); // 🔥 Stop form submit

                let row = $(this).closest("tr");

                let formData = new FormData();
                formData.append("label_en", row.find(".label_en").val());
                formData.append("label_hi", row.find(".label_hi").val());
                formData.append("value", row.find(".value").val());
                formData.append("field_id", $("#field_id").val()); // 🔥 REQUIRED
                formData.append("_token", $("meta[name='csrf-token']").attr("content"));

                // Multiple Images
                let images = row.find(".image")[0].files;
                for (let i = 0; i < images.length; i++) {
                    formData.append("image[]", images[i]);
                }

                // Proper FormData Logging
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ', pair[1]);
                }

                $.ajax({
                    url: "{{ route('admin.categoryformfieldoptions.optionstore') }}",
                    type: "POST",
                    data: formData, // Correct
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        alert("Saved!");
                        location.reload();
                    }
                });

            });

        });
    </script>
@endpush

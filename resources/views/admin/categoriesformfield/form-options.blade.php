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

                        <form action="{{ route('admin.categoryformfield.update', $record->id) }}" method="POST"
                            enctype="multipart/form-data" class="row g-3">

                            @csrf

                            <div class="col-md-12 d-flex justify-content-between">
                                <p class="">
                                    <b>Question:</b>  {{ $mainForm->translation->label }}
                                </p>
                                @if($mainForm->parent)
                                    <p>
                                        <b> >>> Parent Question:</b> {{ $mainForm->parent->translation->label }} 
                                    </p>
                                @endif
                            </div>


                            <button id="addRowBtn" type="button" class="btn btn-primary mb-2">Add Option</button>

                            <table class="table table-bordered" id="optionsTable">
                                <thead>
                                    <tr>
                                        <th>Label (EN)</th>
                                        <th>Label (HI)</th>
                                        <th>Image</th>
                                        <th>Value</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <!-- Existing rows loaded from DB -->
                                    {{-- @foreach($options as $option)
                                    <tr data-id="{{ $option->id }}">
                                        <td>{{ $option->label_en }}</td>
                                        <td>{{ $option->label_hi }}</td>
                                        <td>
                                            @if($option->image)
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
        $(document).ready(function(){

    // Add New Row
    $("#addRowBtn").click(function(){
        let newRow = `
            <tr class="editing">
                <td><input type="text" class="form-control label_en"></td>
                <td><input type="text" class="form-control label_hi"></td>
                <td><input type="file" class="form-control image"></td>
                <td><input type="text" class="form-control value"></td>
                <td>
                    <button class="btn btn-sm btn-success saveRow">Save</button>
                    <button class="btn btn-sm btn-secondary cancelRow">Cancel</button>
                </td>
            </tr>
        `;
        $("#optionsTable tbody").append(newRow);
    });


    // Cancel new row
    $(document).on("click", ".cancelRow", function(){
        $(this).closest("tr").remove();
    });


    // Save New Row
    $(document).on("click", ".saveRow", function(){
        let row = $(this).closest("tr");

        let formData = new FormData();
        formData.append("label_en", row.find(".label_en").val());
        formData.append("label_hi", row.find(".label_hi").val());
        formData.append("value", row.find(".value").val());
        formData.append("image", row.find(".image")[0].files[0]);
        formData.append("_token", $("meta[name='csrf-token']").attr("content"));

        $.ajax({
            url: "/options/store",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                alert("Saved!");
                location.reload();
            }
        });
    });


    // Edit Row
    $(document).on("click", ".editRow", function(){
        let tr = $(this).closest("tr");
        let id = tr.data("id");

        let label_en = tr.children().eq(0).text();
        let label_hi = tr.children().eq(1).text();
        let value    = tr.children().eq(3).text();

        tr.html(`
            <td><input type="text" class="form-control label_en" value="${label_en}"></td>
            <td><input type="text" class="form-control label_hi" value="${label_hi}"></td>
            <td>
                <input type="file" class="form-control image">
            </td>
            <td><input type="text" class="form-control value" value="${value}"></td>
            <td>
                <button class="btn btn-sm btn-success updateRow" data-id="${id}">Update</button>
                <button class="btn btn-sm btn-secondary cancelEdit">Cancel</button>
            </td>
        `);
    });


    // Cancel Edit Row (reload page)
    $(document).on("click", ".cancelEdit", function(){
        location.reload();
    });


    // Update row
    $(document).on("click", ".updateRow", function(){
        let row = $(this).closest("tr");
        let id = $(this).data("id");

        let formData = new FormData();
        formData.append("id", id);
        formData.append("label_en", row.find(".label_en").val());
        formData.append("label_hi", row.find(".label_hi").val());
        formData.append("value", row.find(".value").val());
        formData.append("image", row.find(".image")[0].files[0]);
        formData.append("_token", $("meta[name='csrf-token']").attr("content"));

        $.ajax({
            url: "/options/update/" + id,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                alert("Updated!");
                location.reload();
            }
        });
    });


    // Delete Row
    $(document).on("click", ".deleteRow", function(){
        if (!confirm("Are you sure?")) return;

        let id = $(this).closest("tr").data("id");

        $.ajax({
            url: "/options/delete/" + id,
            type: "POST",
            data: {
                _token: $("meta[name='csrf-token']").attr("content")
            },
            success: function(){
                alert("Deleted!");
                location.reload();
            }
        });
    });

});


    </script>
@endpush

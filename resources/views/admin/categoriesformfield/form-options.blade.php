@extends('admin.layouts.app')

@section('content')
    @include('admin.components.FlashMessage')

    @php
        $parentOptions = $mainForm->parent->options ?? [];
    @endphp

    <div id="flashMessageContainer"></div>


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

                        <form id="optionForm" action="#" method="POST" enctype="multipart/form-data" class="row g-3">

                            @csrf

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
                                <div class="col-md-2">
                                    <select id="ParentOptionFilter" name="parent_option_id" class="form-select">
                                        <option value="">-- Filter by --</option>
                                        @foreach ($parentOptions as $parentOption)
                                            <option value="{{ $parentOption->id }}">
                                                {{ $parentOption->translation->label ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button id="addRowBtn" type="button" class="btn btn-primary mb-2">Add Option</button>
                            </div>



                            <table class="table table-bordered" id="optionsTable">
                                <thead>
                                    <tr>
                                        @foreach (langueses() as $langCode => $language)
                                            <th class="no-wrap">Label ({{ $language }}) </th>
                                        @endforeach
                                        @foreach (langueses() as $langCode => $language)
                                            <th>Image ({{ $language }})</th>
                                        @endforeach
                                        <th>Value</th>
                                        @if ($mainForm->parent)
                                            <th>Options</th>
                                        @endif
                                        <th width="150" style="text-align: center;">Actions</th>
                                    </tr>
                                </thead>

                                <tbody id="optionTable">
                                    <!-- Existing rows loaded from DB -->
                                    @foreach ($mainForm->options as $option)
                                        @include('admin.categoriesformfield.form-line-options', [
                                            'option' => $option,
                                            'mainForm' => $mainForm,
                                        ])
                                    @endforeach
                                </tbody>
                            </table>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- // make data ready for next option edit --}}

    <template id="newRowTemplate">
        <tr class="editing">

            @foreach (langueses() as $langCode => $language)
                <td>
                    <input type="text" class="form-control" name="trans[{{ $langCode }}][label]"
                        placeholder="Enter label in {{ $language }}">
                </td>
            @endforeach

            @foreach (langueses() as $langCode => $language)
                <td>
                    <input type="file" name="trans[{{ $langCode }}][images]" style="width: 60%;">
                </td>
            @endforeach

            <td>
                <input type="text" class="form-control value" name="value">
            </td>

            @if ($mainForm->parent)
                <td>
                    <select name="parent_option_id" class="form-select" multiple>
                        @foreach ($parentOptions as $parentOption)
                            <option value="{{ $parentOption->id }}">
                                {{ $parentOption->translation->label ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </td>
            @endif

            <td>
                <div class="text-center">
                    <button class="btn btn-sm btn-success saveRow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-floppy" viewBox="0 0 16 16">
                            <path d="M11 2H9v3h2z" />
                            <path
                                d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                        </svg>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger cancelRow">X</button>
                </div>
            </td>

        </tr>
    </template>


@endsection


@push('scripts')
    <script>
        $(document).ready(function() {

            // Add New Row
            $("#addRowBtn").click(function() {
                let template = document.getElementById("newRowTemplate");
                let clone = template.content.cloneNode(true);
                $("#optionsTable tbody").append(clone);
                initselect2();
            });

            // Cancel
            $(document).on("click", ".cancelRow", function() {
                $(this).closest("tr").remove();
            });

            // Save New Row
            $(document).on("click", ".saveRow", function(e) {
                e.preventDefault();

                let row = $(this).closest("tr");

                let formData = new FormData();

                // Loop through all inputs, selects, textareas
                row.find("input, select, textarea").each(function() {
                    let input = $(this);
                    let name = input.attr("name");

                    if (!name) return; // skip inputs without name

                    // Handle file inputs
                    // if (input.attr("type") === "file") {
                    //     let files = input[0].files;
                    //     for (let i = 0; i < files.length; i++) {
                    //         formData.append(name, files[i]);
                    //     }
                    // } 
                    if (input.attr("type") === "file") {
                        let files = input[0].files;
                        if (files.length > 0) {
                            formData.append(name, files[0]);
                        } else {
                            formData.append(name, "");
                        }
                    } else {
                        // Normal value
                        formData.append(name, input.val());
                    }
                });

                // Add extra values if needed
                formData.append("_token", $("meta[name='csrf-token']").attr("content"));
                formData.append("field_id", $("#field_id").val());

                // Log all FormData
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ": ", pair[1]);
                }


                $.ajax({
                    url: "{{ route('admin.categoryformfieldoptions.optionstore') }}",
                    type: "POST",
                    data: formData, // Correct
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.success) {
                            row.replaceWith(res.html);
                            initselect2();
                            showFlashMessage('success', res.message);
                        } else {
                            showFlashMessage('success', res.message);
                            return;
                        }
                        // location.reload();
                    },
                    error: function(err) {
                        // alert("Error saving option.");
                        showFlashMessage('danger', err.responseText);
                        console.error(err);
                    }
                });

            });

            $(document).on("click", ".editRow", function() {
                let row = $(this).closest("tr");
                let optionId = row.data("id");

                let formData = new FormData();
                console.log(formData);


                // Collect only row-specific inputs
                row.find("input, select, textarea").each(function() {
                    let input = $(this);
                    let name = input.attr("name");
                    if (!name) return;

                    if (input.attr("type") === "file") {
                        let files = input[0].files;
                        for (let i = 0; i < files.length; i++) {
                            formData.append(name, files[i]);
                        }
                    } else {
                        formData.append(name, input.val());
                    }
                });

                formData.append("_token", $("meta[name='csrf-token']").attr("content"));
                formData.append("field_id", $("#field_id").val());

                $.ajax({
                    url: "{{ route('admin.categoryformfieldoptions.optionupdate', ':id') }}"
                        .replace(':id', optionId),
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(response) {
                        console.log(response);
                        row.replaceWith(response.html);
                        showFlashMessage('success', 'Updated successfully');
                        initselect2();
                        // alert("Updated successfully");
                        // location.reload();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        showFlashMessage('danger', xhr.responseText);
                        // alert("Update failed");
                    }
                });
            });


            $(document).on("click", ".deleteRow", function() {

                let row = $(this).closest("tr");
                let optionId = row.data("id");

                if (!confirm("Are you sure you want to delete this option?")) return;

                $.ajax({
                    url: "{{ route('admin.categoryformfieldoptions.optiondelete', ':id') }}"
                        .replace(':id', optionId),
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.success) {
                            row.remove();
                            alert("Deleted successfully");
                            // location.reload();
                        } else {
                            alert("Failed to delete");
                        }
                    },
                    error: function(err) {
                        console.error(err);
                        alert("Server error");
                    }
                });

            });
        });


        function initselect2() {
            $('.form-select').select2({
                width: '100%'
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            // Base URL for Facebox assets
            // var baseUrl = "{{ asset('public/admin/facebox') }}";

            // Initialize Facebox
            // function initializeFacebox() {
            //     $('a[rel*=facebox]').facebox({
            //         loadingImage: baseUrl + '/loading.gif',
            //         closeImage: baseUrl + '/closelabel.png'
            //     });
            // }

            // initializeFacebox();
            // $(document).on('ajaxComplete', initializeFacebox);

            // AJAX filter
            $('#ParentOptionFilter').on('change', function() {
                filterTable();
            });

            function filterTable(page = 1) {
            let parent_option_id = $('#ParentOptionFilter').val();
            let search = $('#search-input').val();
            let form_id = "{{ $mainForm->id }}"; // send current form id

            $.ajax({
                url: "{{ route('admin.parentoptions.filter') }}",
                type: "GET",
                data: {
                    parent_option_id,
                    search,
                    page,
                    form_id
                },
                success: function (response) {
                    $("#optionTable").html(response.html);
                    initselect2();
                }
            });
        }


        });
    </script>
@endpush

@extends('admin.layouts.app')

@section('content')
    @include('admin.components.FlashMessage')

    @php
        $parentOptions = $mainForm->parent->options ?? [];
    @endphp
    
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

                        <form action="#" method="POST"  enctype="multipart/form-data" class="row g-3">

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

                                <tbody>
                                    <!-- Existing rows loaded from DB -->
                                    @foreach ($mainForm->options as $option)
                                    @php
                                        $optionsDeatil = collect($option->translations()->get())->keyBy('lang_code')->map(function($item){
                                            return $item;
                                        })->toArray();

                                    @endphp
                                    <tr data-id="{{ $option->id }}">
                                        
                                        @foreach (langueses() as $langCode => $language)
                                            <td>
                                                <input type="text" class="form-control" name="trans[{{ $langCode }}][label]" 
                                                    placeholder="Enter label in {{ $language }}" value="{{ $optionsDeatil[$langCode]['label'] ?? '' }}">
                                            </td>
                                        @endforeach

                                        @foreach (langueses() as $langCode => $language)
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <input type="file" name="trans[{{ $langCode }}][images]" style="width: 60%;">
                                                    @php
                                                        $imageUrl = $optionsDeatil[$langCode]['image_url'] ?? '';
                                                    @endphp
                                                    @if(!empty($imageUrl))
                                                        <img src="{{ $imageUrl }}" alt="Option Image" width="50">
                                                    @endif
                                                
                                                </div>
                                            </td>
                                        @endforeach

                                        <td>
                                            <input type="text" class="form-control value" name="value" value="{{ $option->value }}">
                                        </td>

                                        @if ($mainForm->parent)
                                        <td>
                                            <select name="parent_option_id"  id="parent_option_id">
                                                @foreach($parentOptions as $parentOption)
                                                    <option value="{{ $parentOption->id }}" {{ $parentOption->id == $option->parent_option_id ? 'selected':'' }}>
                                                        {{ $parentOption->translation->label ?? 'N/A' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        @endif
                                        

                                        <td>
                                            <div class="text-center">
                                                <button type="button" class="btn btn-sm btn-warning editRow">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                                    </svg>
                                                </button>
                                                <button class="btn btn-sm btn-danger deleteRow">X</button>
                                            </div>
                                        </td>
                                    </tr>
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
                <select name="parent_option_id"  id="parent_option_id">
                    @foreach($parentOptions as $parentOption)
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy" viewBox="0 0 16 16">
                            <path d="M11 2H9v3h2z"/>
                            <path d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z"/>
                        </svg>
                    </button>
                    <button class="btn btn-sm btn-danger removeRow">X</button>
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

                // Loop through all inputs, selects, textareas
                row.find("input, select, textarea").each(function () {
                    let input = $(this);
                    let name = input.attr("name");

                    if (!name) return; // skip inputs without name

                    // Handle file inputs
                    if (input.attr("type") === "file") {
                        let files = input[0].files;
                        for (let i = 0; i < files.length; i++) {
                            formData.append(name, files[i]);
                        }
                    } 
                    else {
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
                        if(res.success){
                            alert(res.message);
                        }else{
                            alert(res.message);
                            return ;
                        }
                        // location.reload();
                    },error: function(err) {
                        alert("Error saving option.");
                        console.error(err);
                    }
                });

            });

        });
    </script>
@endpush

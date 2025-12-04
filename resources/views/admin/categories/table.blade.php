<style>
    #sortable tr.ui-state-highlight {
        background: #f9f9f9;
        border: 2px dashed #999;
        height: 50px;
    }

    /* Style for the row that is being dragged */
    #sortable tr.ui-sortable-helper {
        background-color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    /* General row styling */
    #sortable tr {
        cursor: move;
        transition: background-color 0.2s ease;
    }

    #sortable tr:hover {
        background-color: #f1f1f1;
    }
</style>

@php
    $i = $records->firstItem();
@endphp
<div class="table-responsive mt-3">
    <table class="table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                <th class="no-wrap">@sortablelink('sort_order', 'Sl No.')</th>
                <th class="no-wrap">Image</th>
                @foreach (langueses() as $langCode => $language)
                    <th class="no-wrap">Title ({{ $language }}) </th>
                @endforeach
                <th class="no-wrap">Link To Api</th>
                <th class="no-wrap"> @sortablelink('is_active', 'Status')</th>
                <th class="no-wrap text-center" width="100px">Action</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @forelse ($records as $key => $item)

                <tr data-id="{{ $item->id }}" class="sortable-row">
                    <td>{{ $item->sort_order }}</td>
                    <td>
                        <img src="{{ $item->image_url }}" alt="Category Image"
                            style="object-fit: contain;border-radius: 6px;border: 1px solid #ddd;background-color: #f8f9fa;max-width: 35px;height: 35px;padding: 4px;">
                    </td>

                    {{-- // display title for each language --}}
                    @foreach (langueses() as $langCode => $language)
                        <td>{{ $item->translations->where('lang_code', $langCode)->first()->title ?? 'N/A' }}</td>
                    @endforeach

                    <td>
                        <span
                            class="badge badge-{{ $item->is_link == 1 ? 'success' : 'danger' }} linkapistatus-{{ $item->id }}"
                            onclick="change_link_to_api_Status(event,{{ $item->id }})">{{ ucfirst($item->is_link == 1 ? 'Yes' : 'No') }}
                        </span>
                    </td>

                    <td>
                        <span
                            class="badge badge-{{ $item->is_active == 1 ? 'success' : 'danger' }} status-{{ $item->id }}"
                            onclick="changeStatus(event,{{ $item->id }})">{{ ucfirst($item->is_active == 1 ? 'Active' : 'Inactive') }}
                        </span>
                    </td>

                    <td>
                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                            <a href="{{ route('admin.categories.view', ['id' => $item->id]) }}"
                                class="text-success facebox" rel="facebox" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="View"><i class="fa-solid fa-eye fs-5"></i></a>

                            <a href="{{ route('admin.categories.edit', ['id' => $item->id]) }}" class="text-warning"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i
                                    class="fa-solid fa-pen-to-square text-warning fs-5"></i></a>

                            <a href="javascript:void(0);"
                                onclick="deleteItem('{{ route('admin.categories.delete', ['id' => $item->id]) }}')"
                                class="text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                title="Delete"><i class="fa-solid fa-trash fs-5"></i></a>
                        </div>
                    </td>
                </tr>
                @php
                    $i++;
                @endphp
            @empty
                <tr>
                    <td colspan="10" class="text-center">
                        No Record Found !
                    </td>
                </tr>
            @endforelse

        </tbody>
    </table>
</div>
<script></script>
{{-- pagination with filter --}}
@include('admin.elements.filter-with-pagi', ['data' => $records])


@push('scripts')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <script>
        jQuery(function($) {
            $("#sortable").sortable({
                placeholder: "ui-state-highlight",
                cursor: "move",
                update: function(event, ui) {
                    let order = [];
                    $('#sortable tr').each(function(index) {
                        order.push({
                            id: $(this).data('id'),
                            position: index + 1
                        });
                    });

                    // console.log('Sending new order:', order);

                    $.ajax({
                        url: "{{ route('admin.categories.reorder') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            order: order
                        },
                        success: function(response) {
                            console.log('Order updated:', response.message);
                            $("#sortable").load(location.href + " #sortable > *");
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr.responseText);
                            alert('Something went wrong while sorting.');
                        }
                    });
                }
            });
        });
    </script>
@endpush

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
    <table class="load-table-data table align-middle" style="background-color:#8e8e8">
        <thead class="table-secondary">
            <tr>
                <th class="no-wrap">@sortablelink('sort_order', 'Sl No.')</th>
                <th>Category</th>
                <th>Field Label</th>
                <th>HTML Type</th>
                <th>Av. Options</th>
                <th class="no-wrap">Filtered</th>
                <th class="no-wrap">Required</th>
                <th class="no-wrap" width="100px">Action</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @forelse ($records as $index => $item)
                <tr data-id="{{ $item->id }}" class="sortable-row">
                    {{-- <td class="sort-index">{{ $i }}</td> --}}
                    <td>
                        {{ $item->sort_order }}
                    </td>

                    {{-- Category name --}}
                    <td>{{ $item->category->translation->title ?? 'N/A' }}</td>

                    <td>{{ $item->translation?->label ?? 'N/A' }}</td>
                    <td>{{ ucfirst($item->type ?? 'N/A') }}</td>

                    <td>
                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                            {{-- Show 2 buttons ONLY if type is select or checkbox --}}
                            @if (in_array($item->type, ['select', 'checkbox','radio']))
                                <a href="{{ route('admin.categoryformfield.view', ['id' => $item->id]) }}"
                                    class="text-success facebox" rel="facebox" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="View">
                                    <i class="fa-solid fa-eye fs-5"></i>
                                </a>

                                <a href="{{ route('admin.categoryformfield.viewOptions', ['id' => $item->id]) }}"
                                    class="text-warning" data-bs-toggle="tooltip" title="Edit Options">
                                    <i class="fa-solid fa-pen-to-square fs-5"></i>
                                </a>
                            @endif
                        </div>
                    </td>

                    <td>
                        <span
                            class="badge badge-{{ $item->is_filtered ? 'success' : 'danger' }} filterstatus-{{ $item->id }}"
                            onclick="filterchangeStatus(event, {{ $item->id }})">
                            {{ $item->is_filtered ? 'Yes' : 'No' }}
                        </span>
                    </td>

                    <td>
                        <span
                            class="badge badge-{{ $item->is_required ? 'success' : 'danger' }} status-{{ $item->id }}"
                            onclick="changeStatus(event, {{ $item->id }})">
                            {{ $item->is_required ? 'Yes' : 'No' }}
                        </span>
                    </td>

                    <td>
                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                            <a href="{{ route('admin.categoryformfield.view', ['id' => $item->id]) }}"
                                class="text-success facebox" rel="facebox" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="View">
                                <i class="fa-solid fa-eye fs-5"></i>
                            </a>

                            <a href="{{ route('admin.categoryformfield.edit', ['id' => $item->id]) }}"
                                class="text-warning" data-bs-toggle="tooltip" title="Edit">
                                <i class="fa-solid fa-pen-to-square fs-5"></i>
                            </a>

                            <a href="javascript:void(0);"
                                onclick="deleteItem('{{ route('admin.categoryformfield.delete', ['id' => $item->id]) }}')"
                                class="text-danger" data-bs-toggle="tooltip" title="Delete">
                                <i class="fa-solid fa-trash fs-5"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @php $i++; @endphp
            @empty
                <tr>
                    <td colspan="10" class="text-center">No Record Found!</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@include('admin.elements.filter-with-pagi', ['data' => $records])

{{-- @push('scripts')
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

                    $.ajax({
                        url: "{{ route('admin.categoryformfield.reorder') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            order: order
                        },
                        success: function(response) {
                            console.log('Order updated:', response.message);
                            location.reload();
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
@endpush --}}
